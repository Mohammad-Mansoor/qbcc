<?php

namespace App\Console\Commands;

use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DepreciateAssets extends Command
{
    protected $signature = 'assets:depreciate';
    protected $description = 'Calculate and post monthly depreciation for fixed assets';

    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
    }

    public function handle()
    {
        $today = Carbon::today();
        $assets = DB::table('ajnas_account_details')
            ->where('estimated_useful_life', '>', 0)
            ->where(function($query) use ($today) {
                $query->whereNull('last_depreciation_date')
                      ->orWhere('last_depreciation_date', '<', $today->startOfMonth()->format('Y-m-d'));
            })
            ->get();

        foreach ($assets as $asset) {
            $this->processDepreciation($asset);
        }

        $this->info('Asset depreciation processed successfully.');
    }

    private function processDepreciation($asset)
    {
        DB::transaction(function () use ($asset) {
            // Straight-line method: (Cost - Salvage) / Months of life
            $totalDepreciable = $asset->acquisition_cost - $asset->estimated_salvage_value;
            $monthlyDepreciation = $totalDepreciable / ($asset->estimated_useful_life * 12);
            
            if ($asset->accumulated_depreciation + $monthlyDepreciation > $totalDepreciable) {
                $monthlyDepreciation = $totalDepreciable - $asset->accumulated_depreciation;
            }

            if ($monthlyDepreciation <= 0) return;

            // Update Asset record
            DB::table('ajnas_account_details')->where('aad_id', $asset->aad_id)->update([
                'accumulated_depreciation' => $asset->accumulated_depreciation + $monthlyDepreciation,
                'last_depreciation_date' => Carbon::today()->format('Y-m-d'),
            ]);

            // Post to Accounting
            $this->accountingService->postAutoTransaction('asset', 'ASSET_DEPRECIATION', [
                'date' => Carbon::today()->format('Y-m-d'),
                'amount' => $monthlyDepreciation,
                'reference' => 'DEPR-' . $asset->aad_id . '-' . date('mY'),
                'description' => 'Monthly Depreciation for Asset #' . $asset->asset_number . ' (' . $asset->asset_name . ')',
                'source_type' => 'AjnasAccountDetail',
                'source_id' => $asset->aad_id,
            ]);
        });
    }
}
