<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Carpet;
use App\MaterialStock;

class InventoryBackfill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:backfill {--dry-run : Only show what would be done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create opening inventory transactions for existing carpets and materials';

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
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info("DRY RUN: No changes will be made.");
        }

        $this->info("Starting backfill for Carpets...");
        $this->backfillCarpets($dryRun);

        $this->info("\nStarting backfill for Material Stocks...");
        $this->backfillMaterials($dryRun);

        $this->info("\nBackfill completed successfully.");
        return 0;
    }

    protected function backfillCarpets($dryRun)
    {
        $carpets = Carpet::all();
        $count = 0;
        $skipped = 0;

        foreach ($carpets as $carpet) {
            // 1. Find or create item mapping
            $cost = $carpet->total_price ?? 0;
            $item = $this->getOrCreateItem('App\Carpet', $carpet->carpet_id, $cost, $dryRun);

            if (!$item) {
                $skipped++;
                continue;
            }

            // 2. Check if opening transaction exists
            $exists = DB::table('inventory_transactions')
                ->where('item_id', $item->id)
                ->where('type', 'OPENING')
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            if (!$dryRun) {
                DB::table('inventory_transactions')->insert([
                    'item_id' => $item->id,
                    'warehouse_id' => 1, // Main Store
                    'type' => 'OPENING',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'area' => $carpet->area ?? 0,
                    'unit_cost' => $cost,
                    'total_cost' => $cost,
                    'reference_type' => 'App\Carpet',
                    'reference_id' => $carpet->carpet_id,
                    'status' => 1, // Approved
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $count++;
        }

        $this->info("Carpets: $count processed, $skipped skipped (already exist).");
    }

    protected function backfillMaterials($dryRun)
    {
        $stocks = MaterialStock::all();
        $count = 0;
        $skipped = 0;

        foreach ($stocks as $stock) {
            $totalCost = $stock->quantity * $stock->price_per_kilo;
            
            // 1. Find or create item mapping
            $item = $this->getOrCreateItem('App\MaterialStock', $stock->id, $stock->price_per_kilo, $dryRun);

            if (!$item) {
                $skipped++;
                continue;
            }

            // 2. Check if opening transaction exists
            $exists = DB::table('inventory_transactions')
                ->where('item_id', $item->id)
                ->where('type', 'OPENING')
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            if (!$dryRun) {
                DB::table('inventory_transactions')->insert([
                    'item_id' => $item->id,
                    'warehouse_id' => 1, // Main Store
                    'type' => 'OPENING',
                    'direction' => 'IN',
                    'quantity' => $stock->quantity,
                    'unit_cost' => $stock->price_per_kilo,
                    'total_cost' => $totalCost,
                    'reference_type' => 'App\MaterialStock',
                    'reference_id' => $stock->id,
                    'status' => 1, // Approved
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $count++;
        }

        $this->info("Materials: $count processed, $skipped skipped (already exist).");
    }

    protected function getOrCreateItem($type, $refId, $cost, $dryRun)
    {
        $item = DB::table('items')
            ->where('type', $type)
            ->where('ref_id', $refId)
            ->first();

        if ($item) {
            return $item;
        }

        if ($dryRun) {
            return (object)['id' => 0]; // Fake ID for dry run
        }

        $id = DB::table('items')->insertGetId([
            'type' => $type,
            'ref_id' => $refId,
            'current_cost' => $cost,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('items')->find($id);
    }
}
