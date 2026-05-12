<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Carpet;
use App\MaterialStock;

class InventoryVerify extends Command
{
    protected $signature = 'inventory:verify';
    protected $description = 'Verify integrity between legacy tables and new inventory transactions';

    public function handle()
    {
        $this->info("Verifying Carpets...");
        $carpetCount = Carpet::count();
        $carpetTxCount = DB::table('inventory_transactions')
            ->where('reference_type', 'App\Carpet')
            ->where('type', 'OPENING')
            ->count();

        if ($carpetCount === $carpetTxCount) {
            $this->info("✅ Carpet Integrity: OK ($carpetCount records matched)");
        } else {
            $this->error("❌ Carpet Integrity: FAIL ($carpetCount legacy vs $carpetTxCount transactions)");
        }

        $this->info("\nVerifying Materials...");
        $materials = MaterialStock::all();
        $fail = false;

        foreach ($materials as $material) {
            $txQty = DB::table('inventory_transactions')
                ->where('reference_type', 'App\MaterialStock')
                ->where('reference_id', $material->id)
                ->sum('quantity');

            if (abs($txQty - $material->quantity) > 0.001) {
                $this->error("❌ Material ID {$material->id} mismatch: Legacy Qty {$material->quantity} vs Tx Qty $txQty");
                $fail = true;
            }
        }

        if (!$fail) {
            $this->info("✅ Material Integrity: OK");
        }

        return 0;
    }
}
