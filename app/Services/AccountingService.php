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
                'party_type' => ($debitAcc->account_type == 'Asset' || $debitAcc->account_type == 'Liability') ? ($params['party_type'] ?? null) : null,
                'party_id' => ($debitAcc->account_type == 'Asset' || $debitAcc->account_type == 'Liability') ? ($params['party_id'] ?? null) : null,
            ],
            [
                'account_id' => $creditAccountId,
                'debit' => 0,
                'credit' => $params['amount'],
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

        // 2. Validate Debit == Credit
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($data['entries'] as $entry) {
            $totalDebit += $entry['debit'] ?? 0;
            $totalCredit += $entry['credit'] ?? 0;
        }

        if (abs($totalDebit - $totalCredit) > 0.001) {
            throw new Exception("Transaction is unbalanced. Total Debit ($totalDebit) must equal Total Credit ($totalCredit).");
        }

        return DB::transaction(function () use ($data) {
            // 2.5 System-Level Idempotency Check (Distributed Consistency)
            if (isset($data['source_id']) && isset($data['source_type'])) {
                $query = LedgerTransaction::where('source_id', $data['source_id'])
                    ->where('source_type', $data['source_type'])
                    ->where('status', 'posted');

                // If a mapping_key is provided, we check for duplicate of THAT specific event
                if (isset($data['mapping_key'])) {
                    $query->where('mapping_key', $data['mapping_key']);
                }

                $exists = $query->lockForUpdate()->exists();
                
                if ($exists) {
                    \Log::warning("Duplicate event blocked: {$data['source_type']} #{$data['source_id']} [{$data['mapping_key']}]");
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
                'mapping_key' => $data['mapping_key'] ?? null, // Save the intent
                'journal_type' => $data['journal_type'] ?? 'journal',
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            // 4. Create Entries
            foreach ($data['entries'] as $entryData) {
                $exchangeRate = $entryData['exchange_rate'] ?? 1;
                $debit = $entryData['debit'] ?? 0;
                $credit = $entryData['credit'] ?? 0;
                $currencyCode = $entryData['currency_code'] ?? 'USD';
                
                // Calculate base currency amount (Base is USD as per plan)
                $amount = ($debit > 0 ? $debit : $credit);
                $originalAmount = $entryData['original_amount'] ?? $amount;
                $baseAmount = $amount * $exchangeRate;

                LedgerEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $entryData['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'currency_code' => $currencyCode,
                    'original_amount' => $originalAmount,
                    'exchange_rate' => $exchangeRate,
                    'base_currency_amount' => $baseAmount,
                    'party_type' => $entryData['party_type'] ?? null,
                    'party_id' => $entryData['party_id'] ?? null,
                    'cost_center_id' => $entryData['cost_center_id'] ?? null,
                ]);
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
                    'party_type' => $entry->party_type,
                    'party_id' => $entry->party_id,
                    'cost_center_id' => $entry->cost_center_id,
                ]);
            }

            // 3. Link reversal to original (Implicitly linked via reversed_transaction_id on the reversal record)
            // Note: We skip updating the original description to satisfy immutability rules in LedgerTransaction model.
            // $original->update(['description' => $original->description . " (REVERSED BY #$reversal->id)"]);

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
        $query = LedgerTransaction::where('source_id', $sourceId)
            ->where('status', 'posted');

        if ($sourceType) {
            $query->where('source_type', $sourceType);
        }

        $transactions = $query->get();

        foreach ($transactions as $tx) {
            $this->reverseTransaction($tx->id, $reason);
        }
    }
}
