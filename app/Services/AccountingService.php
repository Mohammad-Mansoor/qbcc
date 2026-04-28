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
     * Post a transaction automatically based on a mapping rule
     * 
     * @param string $type e.g. 'sale'
     * @param string $condition e.g. 'credit'
     * @param array $params [date, amount, party_type, party_id, reference, description]
     */
    public function postAutoTransaction($type, $condition, array $params)
    {
        $rule = MappingRule::where('transaction_type', $type)
            ->where('condition', $condition)
            ->first();

        if (!$rule) {
            throw new Exception("No mapping rule found for transaction type '$type' and condition '$condition'. Please configure it in Settings.");
        }

        // Determine which side (Debit or Credit) should have the party information attached.
        // Usually, we attach the party to the Accounts Receivable (Asset) or Accounts Payable (Liability) side.
        $debitAcc = ChartOfAccount::find($rule->debit_account_id);
        $creditAcc = ChartOfAccount::find($rule->credit_account_id);

        $entries = [
            [
                'account_id' => $rule->debit_account_id,
                'debit' => $params['amount'],
                'credit' => 0,
                'party_type' => ($debitAcc->account_code == '1300' || $debitAcc->account_code == '2100') ? ($params['party_type'] ?? null) : null,
                'party_id' => ($debitAcc->account_code == '1300' || $debitAcc->account_code == '2100') ? ($params['party_id'] ?? null) : null,
            ],
            [
                'account_id' => $rule->credit_account_id,
                'debit' => 0,
                'credit' => $params['amount'],
                'party_type' => ($creditAcc->account_code == '1300' || $creditAcc->account_code == '2100') ? ($params['party_type'] ?? null) : null,
                'party_id' => ($creditAcc->account_code == '1300' || $creditAcc->account_code == '2100') ? ($params['party_id'] ?? null) : null,
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
        // 1. Validate Fiscal Period
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
            // 3. Create Transaction Header
            $transaction = LedgerTransaction::create([
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'journal_type' => $data['journal_type'] ?? 'journal',
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            // 4. Create Entries
            foreach ($data['entries'] as $entryData) {
                $exchangeRate = $entryData['exchange_rate'] ?? 1;
                $debit = $entryData['debit'] ?? 0;
                $credit = $entryData['credit'] ?? 0;
                
                // Calculate base currency amount (assuming base is AFN or USD depending on system config)
                // For now, let's just store the calculation
                $baseAmount = ($debit > 0 ? $debit : $credit) * $exchangeRate;

                LedgerEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $entryData['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
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

        if ($original->status === 'reversed') {
            throw new Exception("This transaction has already been reversed.");
        }

        return DB::transaction(function () use ($original, $reason) {
            // 1. Create Reversal Transaction
            $reversal = LedgerTransaction::create([
                'date' => now(),
                'reference' => 'REVERSAL-' . $original->id,
                'description' => ($reason ? $reason . " | " : "") . "Reversal of Transaction #" . $original->id . " (" . $original->reference . ")",
                'source_type' => $original->source_type,
                'source_id' => $original->source_id,
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
                    'debit' => $entry->credit, // Flip Credit to Debit
                    'credit' => $entry->debit, // Flip Debit to Credit
                    'exchange_rate' => $entry->exchange_rate,
                    'base_currency_amount' => $entry->base_currency_amount,
                    'party_type' => $entry->party_type,
                    'party_id' => $entry->party_id,
                    'cost_center_id' => $entry->cost_center_id,
                ]);
            }

            // 3. Mark Original as Reversed
            $original->update(['status' => 'reversed']);

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
    public function reverseTransactionBySource($sourceId, $reason = null)
    {
        $transactions = LedgerTransaction::where('source_id', $sourceId)
            ->where('status', 'posted')
            ->get();

        foreach ($transactions as $tx) {
            $this->reverseTransaction($tx->id, $reason);
        }
    }
}
