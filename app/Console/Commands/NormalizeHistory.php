<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Currency;
use App\LedgerEntry;

class NormalizeHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:normalize-history {--dry-run : Display changes without saving} {--force : Force recalculation even if base_amount exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forensic normalization of historical financial data to USD base amounts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting ERP Forensic Normalization...");
        
        $currencies = Currency::all()->keyBy('code');
        $baseCurrency = Currency::where('is_base_currency', 1)->first();
        
        if (!$baseCurrency) {
            $this->error("Base currency (USD) not found. Please set a base currency first.");
            return;
        }

        $this->normalizeGeneralLedger($currencies, $baseCurrency);
        $this->normalizeSubLedgers($currencies, $baseCurrency);

        $this->info("Normalization completed successfully.");
    }

    private function normalizeGeneralLedger($currencies, $baseCurrency)
    {
        $this->info("Normalizing General Ledger (ledger_entries)...");
        
        $query = LedgerEntry::query();
        
        if (!$this->option('force')) {
            $query->where(function($q) {
                $q->where('base_currency_amount', 0)
                  ->orWhere('base_debit', 0)
                  ->where('debit', '>', 0)
                  ->orWhere('base_credit', 0)
                  ->where('credit', '>', 0);
            });
        }

        $entries = $query->get();
        $bar = $this->output->createProgressBar($entries->count());

        foreach ($entries as $entry) {
            $rate = $entry->exchange_rate;
            
            // If rate is missing or 1.0 but currency is NOT base, we might need a fallback
            if (($rate <= 0 || $rate == 1.0) && $entry->currency_code != $baseCurrency->code) {
                $fallback = $currencies->get($entry->currency_code);
                $rate = $fallback ? $fallback->exchange_rate : 1.0;
            }

            // If entry has no currency_code, assume Base
            if (empty($entry->currency_code)) {
                $entry->currency_code = $baseCurrency->code;
                $rate = 1.0;
            }

            // Normalization Logic (BCMath)
            $originalAmount = ($entry->debit > 0) ? $entry->debit : $entry->credit;
            
            // If original_amount was already set, use it
            if ($entry->original_amount > 0) {
                $originalAmount = $entry->original_amount;
            }

            $baseAmount = bcmul((string)$originalAmount, (string)$rate, 4);
            $baseDebit = ($entry->debit > 0) ? $baseAmount : 0;
            $baseCredit = ($entry->credit > 0) ? $baseAmount : 0;

            if (!$this->option('dry-run')) {
                DB::table('ledger_entries')->where('id', $entry->id)->update([
                    'exchange_rate' => $rate,
                    'original_amount' => $originalAmount,
                    'base_currency_amount' => $baseAmount,
                    'base_debit' => $baseDebit,
                    'base_credit' => $baseCredit,
                    'currency_code' => $entry->currency_code
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line("");
    }

    private function normalizeSubLedgers($currencies, $baseCurrency)
    {
        $tables = [
            'purchase_materials' => ['date_col' => 'purchase_date', 'base_col' => 'base_currency_amount'],
            'material_sales' => ['date_col' => 'date', 'base_col' => 'base_currency_amount'],
            'washing_payments' => ['date_col' => 'date', 'base_col' => 'base_amount'],
            'finishing_team_payments' => ['date_col' => 'date', 'base_col' => 'base_amount'],
            'agent_payments' => ['date_col' => 'date', 'base_col' => 'base_amount'],
            'kachaee_payments' => ['date_col' => 'date', 'base_col' => 'base_amount'],
            'seller_payments' => ['date_col' => 'date', 'base_col' => 'base_amount'],
            'office_debits' => ['date_col' => 'date', 'base_col' => 'base_amount']
        ];

        foreach ($tables as $table => $config) {
            $this->info("Normalizing Sub-Ledger: $table...");
            $dateCol = $config['date_col'];
            $baseCol = $config['base_col'];

            $items = DB::table($table);
            if (!$this->option('force')) {
                $items->where($baseCol, 0);
            }
            
            $records = $items->get();
            $bar = $this->output->createProgressBar($records->count());

            foreach ($records as $record) {
                $currencyCode = $record->currency_code ?? null;
                $rate = $record->exchange_rate ?? 0;

                // Special handling for legacy office_debits (Expenses)
                if ($table == 'office_debits' && empty($currencyCode)) {
                    if ($record->amount_af > 0 && $record->amount == 0) {
                        $currencyCode = 'AFN';
                    } elseif ($record->amount > 0 && $record->amount_af == 0) {
                        $currencyCode = 'USD';
                    } else {
                        $currencyCode = $baseCurrency->code;
                    }
                }

                if (empty($currencyCode)) {
                    $currencyCode = $baseCurrency->code;
                }

                if ($rate <= 0 && $currencyCode != $baseCurrency->code) {
                    $fallback = $currencies->get($currencyCode);
                    $rate = $fallback ? $fallback->exchange_rate : 1.0;
                } elseif ($currencyCode == $baseCurrency->code) {
                    $rate = 1.0;
                }

                // If original_amount is missing, try 'total' or 'amount'
                $original = $record->original_amount ?? 0;
                if ($original <= 0) {
                    // For office_debits, we prefer the non-zero legacy column
                    if ($table == 'office_debits') {
                        $original = ($record->amount_af > 0) ? $record->amount_af : $record->amount;
                    } else {
                        $original = $record->total ?? $record->amount ?? 0;
                    }
                }

                $baseAmount = bcmul((string)$original, (string)$rate, 4);

                if (!$this->option('dry-run')) {
                    DB::table($table)->where('id', $record->id)->update([
                        'exchange_rate' => $rate,
                        'currency_code' => $currencyCode,
                        'original_amount' => $original,
                        $baseCol => $baseAmount
                    ]);
                }
                $bar->advance();
            }
            $bar->finish();
            $this->line("");
        }
    }
}
