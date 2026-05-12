<?php

namespace App\Services;

use App\Item;
use App\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Record a stock movement
     * 
     * @param array $data [item_model, type, direction, quantity, warehouse_id, unit_cost, reference, area, created_by, is_value_adjustment, parent_item_model]
     * @return int|null Transaction ID
     */
    public function recordMovement(array $data)
    {
        $model = $data['item_model'];
        $parentModel = $data['parent_item_model'] ?? $model; // For services, parent is the Carpet
        $type = $data['type'];
        $direction = $data['direction'];
        $quantity = $data['quantity'];
        $warehouseId = $data['warehouse_id'] ?? 1;
        $unitCost = $data['unit_cost'] ?? 0;
        $area = $data['area'] ?? 0;
        $isValueAdjustment = $data['is_value_adjustment'] ?? false;

        // 1. Idempotency Check: Prevent duplicate processing for same source and type
        // Use the actual record (e.g. App\CarpetWash) as the reference to allow multiple services on one carpet
        $exists = DB::table('inventory_transactions')
            ->where('reference_type', get_class($model))
            ->where('reference_id', $model->getKey())
            ->where('type', $type)
            ->exists();
        
        if ($exists) {
            return null; // Already processed
        }

        // 2. Get the Item mapping for the PARENT item (the one that holds the value)
        $item = $this->getOrRegisterItem($parentModel);

        // 3. Calculate WAC / Specific Cost
        // We update cost on IN movements OR on Value Adjustments
        if ($direction === 'IN' || $isValueAdjustment) {
            $this->updateWAC($item, $quantity, $unitCost, $isValueAdjustment);
        }

        // 4. Create Inventory Transaction record
        $transaction = DB::table('inventory_transactions')->insertGetId([
            'item_id' => $item->id,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'direction' => $direction,
            'quantity' => $quantity,
            'area' => $area,
            'unit_cost' => $unitCost,
            'total_cost' => ($isValueAdjustment || $quantity == 0 ? $unitCost : ($quantity * $unitCost)),
            'is_value_adjustment' => $isValueAdjustment,
            'reference_type' => get_class($model),
            'reference_id' => $model->getKey(),
            'status' => 1, // Approved
            'created_by' => $data['created_by'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Update Legacy Tables for Backward Compatibility
        $this->updateLegacyStock($parentModel, $direction, $quantity);

        return $transaction;
    }

    /**
     * Get or create the Item mapping for a legacy model
     */
    protected function getOrRegisterItem($model)
    {
        $type = get_class($model);
        $refId = $model->getKey();

        $item = DB::table('items')
            ->where('type', $type)
            ->where('ref_id', $refId)
            ->first();

        if (!$item) {
            $id = DB::table('items')->insertGetId([
                'type' => $type,
                'ref_id' => $refId,
                'current_cost' => $model->total_price ?? $model->price_per_kilo ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $item = DB::table('items')->find($id);
        }

        return $item;
    }

    /**
     * Update Weighted Average Cost
     * Logic: Cost can exist without stock, but WAC cannot be recalculated without quantity.
     */
    protected function updateWAC($item, $newQty, $newCost, $isValueAdjustment = false)
    {
        DB::transaction(function() use ($item, $newQty, $newCost, $isValueAdjustment) {
            // 0. Lock the item record for the duration of this calculation
            $lockedItem = DB::table('items')->where('id', $item->id)->lockForUpdate()->first();
            
            // 1. Get current total quantity across all warehouses
            $currentQty = DB::table('inventory_transactions')
                ->where('item_id', $item->id)
                ->where('status', 1)
                ->selectRaw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as balance")
                ->value('balance') ?? 0;

            $currentWAC = $lockedItem->current_cost;
            $totalCurrentValue = $currentQty * $currentWAC;
            
            if ($isValueAdjustment || $newQty == 0) {
                // Value Adjustment (e.g. freight, washing)
                $totalNewValue = $newCost; // Added cost
                $totalQty = $currentQty;   // Quantity stays same
            } else {
                // Normal Purchase
                $totalNewValue = $newQty * $newCost;
                $totalQty = $currentQty + $newQty;
            }
            
            // 2. Recalculate WAC ONLY if there is quantity
            if ($totalQty > 0) {
                $newWAC = ($totalCurrentValue + $totalNewValue) / $totalQty;
                
                DB::table('items')->where('id', $item->id)->update([
                    'current_cost' => $newWAC,
                    'updated_at' => now()
                ]);
            } else {
                // If Qty is 0, we still update current_cost for items that have a base price
                if ($currentQty == 0 && $newQty > 0) {
                    DB::table('items')->where('id', $item->id)->update([
                        'current_cost' => $newCost,
                        'updated_at' => now()
                    ]);
                }
            }
        });
    }

    /**
     * Update legacy tables to keep them in sync
     */
    protected function updateLegacyStock($model, $direction, $quantity)
    {
        $materialCategoryId = null;
        $materialTypeId = null;

        if ($model instanceof \App\PurchaseMaterial) {
            $materialCategoryId = $model->material_category;
            $materialTypeId = $model->material_type;
        } elseif ($model instanceof \App\MaterialSale) {
            $materialCategoryId = $model->category_id;
            $materialTypeId = $model->type_id;
        }

        if ($materialCategoryId && $materialTypeId) {
            $stock = \App\MaterialStock::firstOrCreate([
                'material_category' => $materialCategoryId,
                'material_type' => $materialTypeId
            ], [
                'quantity' => 0,
                'price_per_kilo' => 0,
                'in_words' => ''
            ]);

            if ($direction === 'IN') {
                $stock->increment('quantity', $quantity);
            } else if ($direction === 'OUT') {
                $stock->decrement('quantity', $quantity);
            }
        }
    }

    /**
     * Reverse all movements for a specific source
     */
    public function reverseMovement($model, $reason = null)
    {
        return DB::transaction(function () use ($model) {
            $transactions = DB::table('inventory_transactions')
                ->where('reference_type', get_class($model))
                ->where('reference_id', $model->getKey())
                ->where('status', 1)
                ->get();

            foreach ($transactions as $tx) {
                // Insert a reversing entry
                DB::table('inventory_transactions')->insert([
                    'item_id' => $tx->item_id,
                    'warehouse_id' => $tx->warehouse_id,
                    'type' => 'REVERSAL',
                    'direction' => ($tx->direction === 'IN' ? 'OUT' : 'IN'),
                    'quantity' => $tx->quantity,
                    'area' => $tx->area,
                    'unit_cost' => $tx->unit_cost,
                    'total_cost' => $tx->total_cost,
                    'is_value_adjustment' => $tx->is_value_adjustment,
                    'reference_type' => get_class($model),
                    'reference_id' => $model->getKey(),
                    'status' => 1,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Mark original as reversed
                DB::table('inventory_transactions')->where('id', $tx->id)->update(['status' => 0]);
            }
        });
    }
}
