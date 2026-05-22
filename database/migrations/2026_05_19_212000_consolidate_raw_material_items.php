<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ConsolidateRawMaterialItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function() {
            // Helper function to get or create App\MaterialType item
            $getOrCreateMaterialItem = function($materialTypeId, $initialCost = 0) {
                $item = DB::table('items')
                    ->where('type', 'App\MaterialType')
                    ->where('ref_id', $materialTypeId)
                    ->first();
                if (!$item) {
                    $id = DB::table('items')->insertGetId([
                        'type' => 'App\MaterialType',
                        'ref_id' => $materialTypeId,
                        'current_cost' => $initialCost,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $item = DB::table('items')->find($id);
                }
                return $item;
            };

            // 1. Migrate App\PurchaseMaterial items
            $purchaseItems = DB::table('items')->where('type', 'App\PurchaseMaterial')->get();
            foreach ($purchaseItems as $oldItem) {
                $purchase = DB::table('purchase_materials')->where('id', $oldItem->ref_id)->first();
                if ($purchase) {
                    $newItem = $getOrCreateMaterialItem($purchase->material_type, $oldItem->current_cost);
                    DB::table('inventory_transactions')
                        ->where('item_id', $oldItem->id)
                        ->update(['item_id' => $newItem->id]);
                }
            }

            // 2. Migrate App\MaterialSale items
            $saleItems = DB::table('items')->where('type', 'App\MaterialSale')->get();
            foreach ($saleItems as $oldItem) {
                $sale = DB::table('material_sales')->where('id', $oldItem->ref_id)->first();
                if ($sale) {
                    $newItem = $getOrCreateMaterialItem($sale->type_id);
                    DB::table('inventory_transactions')
                        ->where('item_id', $oldItem->id)
                        ->update(['item_id' => $newItem->id]);
                }
            }

            // 3. Migrate App\CarpetMaterial items
            $carpetMatItems = DB::table('items')->where('type', 'App\CarpetMaterial')->get();
            foreach ($carpetMatItems as $oldItem) {
                $carpetMat = DB::table('carpet_materials')->where('id', $oldItem->ref_id)->first();
                if ($carpetMat) {
                    $newItem = $getOrCreateMaterialItem($carpetMat->type_id);
                    DB::table('inventory_transactions')
                        ->where('item_id', $oldItem->id)
                        ->update(['item_id' => $newItem->id]);
                }
            }

            // 4. Delete old items
            DB::table('items')
                ->whereIn('type', ['App\PurchaseMaterial', 'App\MaterialSale', 'App\CarpetMaterial'])
                ->delete();

            // 5. Recalculate WAC for App\MaterialType items
            $newMaterialItems = DB::table('items')->where('type', 'App\MaterialType')->get();
            foreach ($newMaterialItems as $item) {
                // Get all active IN transactions ordered by ID
                $txs = DB::table('inventory_transactions')
                    ->where('item_id', $item->id)
                    ->where('status', 1)
                    ->orderBy('id', 'ASC')
                    ->get();

                $currentQty = 0;
                $currentWac = 0.0;

                foreach ($txs as $tx) {
                    if ($tx->direction === 'IN' || $tx->is_value_adjustment) {
                        $newQty = $tx->quantity;
                        $newNormalizedCost = $tx->base_unit_cost;
                        
                        $totalCurrentValue = bcmul((string)$currentQty, (string)$currentWac, 12);
                        if ($tx->is_value_adjustment || $newQty == 0) {
                            $totalNewValue = (string)$newNormalizedCost;
                            $totalQty = (string)$currentQty;
                        } else {
                            $totalNewValue = bcmul((string)$newQty, (string)$newNormalizedCost, 12);
                            $totalQty = bcadd((string)$currentQty, (string)$newQty, 12);
                        }

                        if ((float)$totalQty > 0) {
                            $totalCombinedValue = bcadd($totalCurrentValue, $totalNewValue, 12);
                            $currentWac = (float)bcdiv($totalCombinedValue, $totalQty, 8);
                            $currentQty = (float)$totalQty;
                        } else {
                            if ((float)$currentQty == 0 && (float)$newQty > 0) {
                                $currentWac = (float)$newNormalizedCost;
                                $currentQty = (float)$newQty;
                            }
                        }
                    } else if ($tx->direction === 'OUT') {
                        $currentQty = max(0.0, $currentQty - $tx->quantity);
                    }
                }

                DB::table('items')->where('id', $item->id)->update([
                    'current_cost' => round($currentWac, 4),
                    'updated_at' => now()
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Consolidation is one-way
    }
}
