<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Carpet;
use App\CarpetRepair;
use App\CarpetWash;
use App\FinishingWork;
use Illuminate\Support\Facades\DB;

class SyncCarpetCogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carpet:sync-cogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retroactively recalculate and synchronize the static total_price columns on carpets based on production stages.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Carpet COGS synchronization...');
        
        $carpets = Carpet::all();
        $updatedCount = 0;

        foreach ($carpets as $carpet) {
            $base_usd = $carpet->carpet_price_us ?? 0;
            $base_afn = $carpet->carpet_price_af ?? 0;

            // 1. Add Kachaee
            $repairs = CarpetRepair::where('carpetId', $carpet->carpet_id)->get();
            foreach ($repairs as $repair) {
                $base_usd += $repair->base_currency_amount ?? 0;
                $base_afn += $repair->af_total_price ?? 0;
            }

            // 2. Add Wash
            $washes = CarpetWash::where('carpetId', $carpet->carpet_id)->get();
            foreach ($washes as $wash) {
                $base_usd += $wash->base_currency_amount ?? 0;
                $base_afn += $wash->af_total_price ?? 0;
            }

            // 3. Add Finishing (Tayaari)
            $finishings = FinishingWork::where('carpetId', $carpet->carpet_id)->where('status', 1)->get();
            foreach ($finishings as $finishing) {
                $base_usd += $finishing->price ?? 0; // price holds the USD base amount
                $base_afn += $finishing->price_af ?? 0;
            }

            // Check if there is a discrepancy
            if (abs($carpet->total_price - $base_usd) > 0.01 || abs($carpet->total_price_af - $base_afn) > 0.01) {
                $carpet->total_price = $base_usd;
                $carpet->total_price_af = $base_afn;
                
                // Silent update without triggering events if needed, but standard update is fine
                $carpet->save();
                $updatedCount++;
            }
        }

        $this->info("Synchronization complete. Updated {$updatedCount} carpets with misaligned COGS.");
        return 0;
    }
}
