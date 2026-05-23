<?php

namespace App\Services;

use App\LedgerTransaction;
use App\LedgerEntry;
use App\ChartOfAccount;
use App\FiscalPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\MappingRule;

class AccountingService
{
    /**
     * Check if a date is financially locked due to period closing
     */
    public function isDateLocked($date)
    {
        $lockDate = \DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value');
        if (!$lockDate) return false;

        return Carbon::parse($date)->lte(Carbon::parse($lockDate));
    }

    /**
     * Throws exception if date is locked
     */
    public function failIfLocked($date)
    {
        if ($this->isDateLocked($date)) {
            $lockDate = \DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value');
            throw new Exception("این تاریخ ($date) قفل شده است. دوره مالی تا تاریخ $lockDate بسته شده و قابل تغییر نیست.");
        }
    }

    public function postAutoTransaction($type, $key, array $params)
    {
        // Lookup by mapping_key (Preferred) or condition (Fallback for legacy)
        $rule = MappingRule::where('transaction_type', $type)
            ->where(function($q) use ($key) {
                $q->where('mapping_key', $key)
                  ->orWhere('condition', $key);
            })
            ->first();

        if (!$rule) {
            throw new Exception("No mapping rule found for transaction type '$type' with key '$key'. Please configure Slugs in Mapping Rules.");
        }

        // --- ACCOUNT OVERRIDE LOGIC ---
        $selectionService = new AccountSelectionService();
        
        $debitAccountId = $params['override_debit_account_id'] ?? $rule->debit_account_id;
        $creditAccountId = $params['override_credit_account_id'] ?? $rule->credit_account_id;

        // Validate Overrides
        if (isset($params['override_debit_account_id'])) {
            $selectionService->validate($rule->mapping_key, $debitAccountId, 'debit');
        }
        if (isset($params['override_credit_account_id'])) {
            $selectionService->validate($rule->mapping_key, $creditAccountId, 'credit');
        }

        $debitAcc = ChartOfAccount::find($debitAccountId);
        $creditAcc = ChartOfAccount::find($creditAccountId);

        $entries = [
            [
                'account_id' => $debitAccountId,
                'debit' => $params['amount'],
                'credit' => 0,
                'currency_code' => $params['currency_code'] ?? 'USD',
                'exchange_rate' => $params['exchange_rate'] ?? null,
                'party_type' => ($debitAcc->account_type == 'Asset' || $debitAcc->account_type == 'Liability') ? ($params['party_type'] ?? null) : null,
                'party_id' => ($debitAcc->account_type == 'Asset' || $debitAcc->account_type == 'Liability') ? ($params['party_id'] ?? null) : null,
            ],
            [
                'account_id' => $creditAccountId,
                'debit' => 0,
                'credit' => $params['amount'],
                'currency_code' => $params['currency_code'] ?? 'USD',
                'exchange_rate' => $params['exchange_rate'] ?? null,
                'party_type' => ($creditAcc->account_type == 'Asset' || $creditAcc->account_type == 'Liability') ? ($params['party_type'] ?? null) : null,
                'party_id' => ($creditAcc->account_type == 'Asset' || $creditAcc->account_type == 'Liability') ? ($params['party_id'] ?? null) : null,
            ]
        ];

        // Map transaction type to valid DB enum: sales, purchase, payment, receipt, journal
        $journalTypeMap = [
            'sale' => 'sales',
            'material_purchase' => 'purchase',
            'washing' => 'purchase',
            'finishing' => 'purchase',
            'customer_payment' => 'receipt',
            'washing_payment' => 'payment',
            'finishing_payment' => 'payment',
            'expense' => 'payment',
            'agent_payment' => 'payment',
            'payroll' => 'payment',
        ];

        $journalType = $journalTypeMap[$type] ?? 'journal';

        return $this->postTransaction([
            'date' => $params['date'],
            'reference' => $params['reference'] ?? null,
            'description' => $params['description'] ?? $rule->description_template,
            'source_type' => $params['source_type'] ?? ucfirst($type),
            'source_id' => $params['source_id'] ?? null,
            'mapping_key' => $rule->mapping_key, // Capture the intent
            'journal_type' => $journalType,
            'entries' => $entries
        ]);
    }

    /**
     * Post a new double-entry transaction
     * 
     * @param array $data [date, reference, description, source_type, source_id, journal_type, entries]
     * @return LedgerTransaction
     * @throws Exception
     */
    public function postTransaction(array $data)
    {
        // 1. Validate Fiscal Period & Lock Date
        $this->failIfLocked($data['date']);
        
        if (!$this->isPeriodOpen($data['date'])) {
            throw new Exception("The selected date falls within a closed fiscal period.");
        }

        return DB::transaction(function () use ($data) {
            // 2. System-Level Idempotency Check
            if (isset($data['source_id']) && isset($data['source_type'])) {
                $query = LedgerTransaction::where('source_id', $data['source_id'])
                    ->where('source_type', $data['source_type'])
                    ->where('status', 'posted')
                    ->whereNotExists(function($q) {
                        $q->select(DB::raw(1))
                          ->from('ledger_transactions as reversals')
                          ->whereRaw('reversals.reversed_transaction_id = ledger_transactions.id');
                    });

                if (isset($data['mapping_key'])) {
                    $query->where('mapping_key', $data['mapping_key']);
                }


                $exists = $query->lockForUpdate()->exists();
                if ($exists) {
                    $mKey = $data['mapping_key'] ?? 'N/A';
                    \Log::warning("Duplicate event blocked: {$data['source_type']} #{$data['source_id']} [$mKey]");
                    return $query->first();
                }
            }

            // 3. Create Transaction Header
            $transaction = LedgerTransaction::create([
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'mapping_key' => $data['mapping_key'] ?? null,
                'journal_type' => $data['journal_type'] ?? 'journal',
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            $totalBaseDebit = '0';
            $totalBaseCredit = '0';
            $transactionCurrency = null;
            $totalOriginalDebit = '0';
            $totalOriginalCredit = '0';

            // 4. Create Entries with Forensic Precision
            foreach ($data['entries'] as $entryData) {
                // Mandate Currency Code
                $currencyCode = $entryData['currency_code'] ?? null;
                if (!$currencyCode) {
                    throw new Exception("Currency code is mandatory for ledger entries. Transaction: " . ($data['reference'] ?? 'unnamed'));
                }

                // Track transaction currency (to verify single-currency balancing if applicable)
                if (!$transactionCurrency) $transactionCurrency = $currencyCode;

                // Lookup Exchange Rate if not provided
                $exchangeRate = $entryData['exchange_rate'] ?? null;
                if ($exchangeRate === null) {
                    $currency = \App\Currency::where('code', $currencyCode)->first();
                    if (!$currency) throw new Exception("Currency '$currencyCode' not found in system.");
                    $exchangeRate = $currency->exchange_rate;
                }

                $debit = $entryData['debit'] ?? 0;
                $credit = $entryData['credit'] ?? 0;
                
                // Use BCMath for normalization to prevent floating point drift
                $amount = ($debit > 0 ? $debit : $credit);
                $baseAmount = bcmul((string)$amount, (string)$exchangeRate, 12);
                $baseAmount = round((float)$baseAmount, 4);

                // Track totals for final balance verification
                if ($debit > 0) {
                    $totalOriginalDebit = bcadd($totalOriginalDebit, (string)$debit, 4);
                    $totalBaseDebit = bcadd($totalBaseDebit, (string)$baseAmount, 4);
                } else {
                    $totalOriginalCredit = bcadd($totalOriginalCredit, (string)$credit, 4);
                    $totalBaseCredit = bcadd($totalBaseCredit, (string)$baseAmount, 4);
                }

                LedgerEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $entryData['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'currency_code' => $currencyCode,
                    'original_amount' => $entryData['original_amount'] ?? $amount,
                    'exchange_rate' => $exchangeRate,
                    'base_currency_amount' => $baseAmount,
                    'base_debit' => ($debit > 0 ? $baseAmount : 0),
                    'base_credit' => ($credit > 0 ? $baseAmount : 0),
                    'party_type' => $entryData['party_type'] ?? null,
                    'party_id' => $entryData['party_id'] ?? null,
                    'cost_center_id' => $entryData['cost_center_id'] ?? null,
                ]);
            }

            // 5. FINAL DUAL-LEVEL BALANCE VERIFICATION
            // Level A: Transaction Currency Balance (Only if it's a single-currency tx)
            // Note: We skip this for mixed-currency swaps as they balance in Base Currency.
            
            // Level B: Base Currency Balance (Mandatory for ALL transactions)
            if (abs((float)bcsub($totalBaseDebit, $totalBaseCredit, 4)) > 0.0001) {
                throw new Exception("Transaction is unbalanced in Base Currency (USD). Debit: $totalBaseDebit, Credit: $totalBaseCredit. Difference: " . bcsub($totalBaseDebit, $totalBaseCredit, 4));
            }

            return $transaction;
        });
    }

    /**
     * Reverse an existing transaction
     * 
     * @param int $transactionId
     * @param string|null $reason
     * @return LedgerTransaction
     * @throws Exception
     */
    public function reverseTransaction($transactionId, $reason = null)
    {
        $original = LedgerTransaction::with('entries')->findOrFail($transactionId);

        // Period Locking Check
        // Note: Reversals are usually posted for TODAY, but we must check if the original 
        // is in a locked period to prevent tampering.
        $this->failIfLocked($original->date);

        return DB::transaction(function () use ($original, $reason) {
            // 1. Create Reversal Transaction (Append-only)
            // Dated for TODAY to preserve historical reports of the original period
            $reversal = LedgerTransaction::create([
                'date' => Carbon::now()->format('Y-m-d'), 
                'reference' => 'REV-' . $original->id,
                'description' => "REVERSAL: " . ($reason ?? "Correction") . " | (Original Ref: " . $original->reference . ")",
                'source_type' => $original->source_type,
                'source_id' => $original->source_id,
                'mapping_key' => $original->mapping_key ? 'REV-' . $original->mapping_key : null,
                'journal_type' => $original->journal_type,
                'status' => 'posted',
                'posted_at' => now(),
                'reversed_transaction_id' => $original->id,
            ]);

            // 2. Create Flipped Entries
            foreach ($original->entries as $entry) {
                LedgerEntry::create([
                    'transaction_id' => $reversal->id,
                    'account_id' => $entry->account_id,
                    'debit' => $entry->credit, 
                    'credit' => $entry->debit, 
                    'currency_code' => $entry->currency_code,
                    'original_amount' => $entry->original_amount,
                    'exchange_rate' => $entry->exchange_rate,
                    'base_currency_amount' => $entry->base_currency_amount,
                    'base_debit' => $entry->base_credit,
                    'base_credit' => $entry->base_debit,
                    'party_type' => $entry->party_type,
                    'party_id' => $entry->party_id,
                    'cost_center_id' => $entry->cost_center_id,
                ]);
            }

            // 3. Link reversal to original (Implicitly linked via reversed_transaction_id on the reversal record)
            // We update the original status to 'reversed' and append '-REV' to mapping_key to release unique key constraints.
            DB::table('ledger_transactions')
                ->where('id', $original->id)
                ->update([
                    'status' => 'reversed',
                    'mapping_key' => $original->mapping_key ? $original->mapping_key . '-REV' : null
                ]);

            return $reversal;
        });
    }

    /**
     * Check if a date falls within an open fiscal period
     * 
     * @param string $date
     * @return bool
     */
    public function isPeriodOpen($date)
    {
        $date = Carbon::parse($date);
        
        $period = FiscalPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        // If no period is defined, we assume it's open (or we can enforce creating periods)
        if (!$period) {
            return true; 
        }

        return !$period->is_closed;
    }
    
    /**
     * Find and reverse a transaction by its source
     * 
     * @param int $sourceId
     * @param string|null $reason
     * @return void
     */
    public function reverseTransactionBySource($sourceId, $reason = null, $sourceType = null)
    {
        if ($sourceType === null) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $callerClass = $backtrace[1]['class'] ?? '';
            
            $mappings = [
                'App\Http\Controllers\DifferentAccountPaymentController' => 'DifferentAccountPayment',
                'App\Http\Controllers\NewDifferentAccountPaymentController' => 'DifferentAccountPayment',
                'App\Http\Controllers\CustomerPaymentController' => 'Customer_payment',
                'App\Http\Controllers\OfficeDebitController' => 'Office_debit',
                'App\Http\Controllers\OfficeCreditController' => 'Office_credit',
                'App\Http\Controllers\AgentPaymentController' => 'Agent_payment',
                'App\Http\Controllers\MonthlyExpenseController' => 'Expense',
                'App\Http\Controllers\NewMonthlyExpenseBalanceController' => 'NewMonthlyExpenseBalance',
                'App\Http\Controllers\SellerPaymentController' => 'Payment',
                'App\Http\Controllers\EmployeePaymentController' => 'App\EmployeePayment',
                'App\Http\Controllers\AjnasAccountDetailsController' => 'Ajnas_account',
                'App\Http\Controllers\WashingPaymentController' => 'Washing_payment',
                'App\Http\Controllers\FinishingTeamPaymentController' => 'Finishing_payment',
            ];
            
            if (isset($mappings[$callerClass])) {
                $sourceType = $mappings[$callerClass];
            } else {
                \Log::warning("reverseTransactionBySource called without sourceType from caller: {$callerClass}");
            }
        }

        $query = LedgerTransaction::where('source_id', $sourceId)
            ->where('status', 'posted')
            ->whereNull('reversed_transaction_id')
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                  ->from('ledger_transactions as reversals')
                  ->whereRaw('reversals.reversed_transaction_id = ledger_transactions.id');
            });

        if ($sourceType) {
            $query->where('source_type', $sourceType);
        }

        $transactions = $query->get();

        foreach ($transactions as $tx) {
            $this->reverseTransaction($tx->id, $reason);
        }
    }
}
