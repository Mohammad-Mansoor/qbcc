<?php

namespace App\Services\Accounting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PdfStatementFilter
{
    /**
     * Filters out reversal pairs and advance payment allocations from the PDF statement.
     * This hides them entirely while preserving the correct net closing balance.
     *
     * @param Collection $entries
     * @return Collection
     */
    public function collapse(Collection $entries)
    {
        if ($entries->isEmpty()) {
            return collect();
        }

        // Extract unique transaction IDs
        $transactionIds = $entries->pluck('transaction_id')->unique()->filter()->toArray();

        // Find reversal relationships
        $reversals = [];
        if (!empty($transactionIds)) {
            $reversals = DB::table('ledger_transactions')
                ->whereIn('id', $transactionIds)
                ->whereNotNull('reversed_transaction_id')
                ->pluck('reversed_transaction_id', 'id')
                ->toArray();
        }

        // Reverse Map to identify original transactions that got reversed
        $originalToReversal = array_flip($reversals);

        $filtered = [];

        foreach ($entries as $entry) {
            $txId = $entry->transaction_id;
            
            $isPartOfReversalPair = isset($reversals[$txId]) || isset($originalToReversal[$txId]);
            $isAdvanceAllocation = (stripos($entry->source_type ?? '', 'advance_settlement') !== false || stripos($entry->source_type ?? '', 'allocation') !== false);
            
            // If the transaction is NOT a reversal pair and NOT an advance allocation, keep it.
            if (!$isPartOfReversalPair && !$isAdvanceAllocation) {
                $filtered[] = $entry;
            }
        }

        // Return the filtered collection
        return collect($filtered);
    }
}
