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
        $parentModel = $data['parent_item_model'] ?? $model; 
        $type = $data['type'];
        $direction = $data['direction'];
        $quantity = $data['quantity'];
        $warehouseId = $data['warehouse_id'] ?? 1;
        $unitCost = $data['unit_cost'] ?? 0;
        $area = $data['area'] ?? 0;
        $isValueAdjustment = $data['is_value_adjustment'] ?? false;

        // NEW: Currency Normalization
        $currencyCode = $data['currency_code'] ?? \App\Currency::getBase()->code;
        $exchangeRate = $data['exchange_rate'] ?? null;
        
        if ($exchangeRate === null) {
            $curr = \App\Currency::where('code', $currencyCode)->first();
            $exchangeRate = $curr ? $curr->exchange_rate : 1.0;
        }

        // Calculate Base Unit Cost (Normalized)
        $baseUnitCost = bcmul((string)$unitCost, (string)$exchangeRate, 12);
        $baseUnitCost = round((float)$baseUnitCost, 4);

        // Enforce Warehouse Subtype Segregation
        $warehouse = DB::table('warehouses')->where('id', $warehouseId)->first();
        if ($warehouse && isset($warehouse->subtype)) {
            $resolvedType = get_class($parentModel);
            $materialTypeId = null;

            if ($parentModel instanceof \App\PurchaseMaterial) {
                $resolvedType = 'App\MaterialType';
                $materialTypeId = $parentModel->material_type;
            } elseif ($parentModel instanceof \App\MaterialSale) {
                $resolvedType = 'App\MaterialType';
                $materialTypeId = $parentModel->type_id;
            } elseif ($parentModel instanceof \App\CarpetMaterial) {
                $resolvedType = 'App\MaterialType';
                $materialTypeId = $parentModel->type_id;
            } elseif ($parentModel instanceof \App\MaterialAccountPayment) {
                $resolvedType = 'App\MaterialType';
                $materialTypeId = $parentModel->type_id;
            } elseif ($resolvedType === 'App\MaterialType') {
                $materialTypeId = $parentModel->material_type_id;
            }

            if ($warehouse->subtype === 'carpet') {
                if ($resolvedType !== 'App\Carpet') {
                    throw new Exception("این گدام مخصوص نگهداری قالین می‌باشد و شما نمی‌توانید مواد در آن ذخیره کنید.");
                }
            } else {
                if ($resolvedType !== 'App\MaterialType') {
                    throw new Exception("این گدام مخصوص نگهداری مواد خام می‌باشد و شما نمی‌توانید قالین در آن ذخیره کنید.");
                }
                if ($materialTypeId) {
                    $matType = DB::table('material_types')->where('material_type_id', $materialTypeId)->first();
                    if ($matType && $matType->subtype !== $warehouse->subtype) {
                        $whSubtypeName = $warehouse->subtype === 'yarn' ? 'تار' : 'رنگ';
                        $matSubtypeName = $matType->subtype === 'yarn' ? 'تار' : 'رنگ';
                        throw new Exception("این گدام مخصوص نگهداری {$whSubtypeName} می‌باشد، اما شما قصد ذخیره {$matSubtypeName} را دارید.");
                    }
                }
            }
        }

        // 1. Get/Register Item
        $item = $this->getOrRegisterItem($parentModel);

        // 2. Idempotency Check
        $exists = DB::table('inventory_transactions')
            ->where('reference_type', get_class($model))
            ->where('reference_id', $model->getKey())
            ->where('item_id', $item->id)
            ->where('type', $type)
            ->where('direction', $direction)
            ->where('status', 1)
            ->exists();
        
        if ($exists) {
            return null;
        }

        // 3. Calculate WAC
        if ($direction === 'IN' || $isValueAdjustment) {
            $this->updateWAC($item, $quantity, $baseUnitCost, $isValueAdjustment);
        }

        // Resolve category_id
        $categoryId = $data['category_id'] ?? null;
        if (!$categoryId) {
            if ($parentModel instanceof \App\PurchaseMaterial) {
                $categoryId = $parentModel->material_category;
            } elseif ($parentModel instanceof \App\MaterialSale) {
                $categoryId = $parentModel->category_id;
            } elseif ($parentModel instanceof \App\CarpetMaterial) {
                $categoryId = $parentModel->category_id;
            }
        }

        // 4. Create Transaction
        $transaction = DB::table('inventory_transactions')->insertGetId([
            'item_id' => $item->id,
            'warehouse_id' => $warehouseId,
            'category_id' => $categoryId,
            'type' => $type,
            'direction' => $direction,
            'quantity' => $quantity,
            'area' => $area,
            'unit_cost' => $unitCost,
            'currency_code' => $currencyCode,
            'exchange_rate' => $exchangeRate,
            'base_unit_cost' => $baseUnitCost,
            'total_cost' => ($isValueAdjustment || $quantity == 0 ? $unitCost : bcmul((string)$quantity, (string)$unitCost, 4)),
            'is_value_adjustment' => $isValueAdjustment,
            'reference_type' => get_class($model),
            'reference_id' => $model->getKey(),
            'status' => 1,
            'created_by' => $data['created_by'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Sync Legacy
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

        // Check if this is a raw material model to consolidate under App\MaterialType
        $isRawMaterial = false;
        $materialTypeId = null;

        if ($model instanceof \App\PurchaseMaterial) {
            $isRawMaterial = true;
            $materialTypeId = $model->material_type;
        } elseif ($model instanceof \App\MaterialSale) {
            $isRawMaterial = true;
            $materialTypeId = $model->type_id;
        } elseif ($model instanceof \App\CarpetMaterial) {
            $isRawMaterial = true;
            $materialTypeId = $model->type_id;
        } elseif ($model instanceof \App\MaterialAccountPayment) {
            $isRawMaterial = true;
            $materialTypeId = $model->type_id;
        }

        if ($isRawMaterial && $materialTypeId) {
            $type = 'App\MaterialType';
            $refId = $materialTypeId;
        }

        $item = DB::table('items')
            ->where('type', $type)
            ->where('ref_id', $refId)
            ->first();

        if (!$item) {
            // Initial Cost Normalization (Legacy models usually store prices in their own context)
            $initialCost = $model->total_price ?? $model->price_per_kilo ?? 0;
            if ($model instanceof \App\CarpetMaterial) {
                $initialCost = $model->price ?? 0;
            }
            
            $id = DB::table('items')->insertGetId([
                'type' => $type,
                'ref_id' => $refId,
                'current_cost' => $initialCost,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $item = DB::table('items')->find($id);
        }

        return $item;
    }

    /**
     * Update Weighted Average Cost using BCMath for forensic precision
     */
    protected function updateWAC($item, $newQty, $newNormalizedCost, $isValueAdjustment = false)
    {
        DB::transaction(function() use ($item, $newQty, $newNormalizedCost, $isValueAdjustment) {
            $lockedItem = DB::table('items')->where('id', $item->id)->lockForUpdate()->first();
            
            // 1. Get current balance
            $currentQty = DB::table('inventory_transactions')
                ->where('item_id', $item->id)
                ->where('status', 1)
                ->selectRaw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as balance")
                ->value('balance') ?? 0;

            $currentWAC = (string)($lockedItem->current_cost ?? 0);
            
            // Calculate Total Current Value (BCMath)
            $totalCurrentValue = bcmul((string)$currentQty, $currentWAC, 12);
            
            if ($isValueAdjustment || $newQty == 0) {
                // Adjustment: Add total value directly
                $totalNewValue = (string)$newNormalizedCost; 
                $totalQty = (string)$currentQty;
            } else {
                // Normal Purchase: Qty * Unit Cost
                $totalNewValue = bcmul((string)$newQty, (string)$newNormalizedCost, 12);
                $totalQty = bcadd((string)$currentQty, (string)$newQty, 12);
            }
            
            // 2. Recalculate WAC
            if ((float)$totalQty > 0) {
                $totalCombinedValue = bcadd($totalCurrentValue, $totalNewValue, 12);
                $newWAC = bcdiv($totalCombinedValue, $totalQty, 8);
                
                DB::table('items')->where('id', $item->id)->update([
                    'current_cost' => round((float)$newWAC, 4),
                    'updated_at' => now()
                ]);
            } else {
                if ((float)$currentQty == 0 && (float)$newQty > 0) {
                    DB::table('items')->where('id', $item->id)->update([
                        'current_cost' => round((float)$newNormalizedCost, 4),
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
        } elseif ($model instanceof \App\CarpetMaterial) {
            $materialCategoryId = $model->category_id;
            $materialTypeId = $model->type_id;
        } elseif ($model instanceof \App\MaterialAccountPayment) {
            $materialTypeId = $model->type_id;
            // Lookup category ID from purchase_materials or material_stocks
            $materialCategoryId = DB::table('purchase_materials')
                ->where('material_type', $materialTypeId)
                ->value('material_category');
            if (!$materialCategoryId) {
                $materialCategoryId = DB::table('material_stocks')
                    ->where('material_type', $materialTypeId)
                    ->value('material_category');
            }
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
    public function reverseMovement($model, $reason = null, $transactionType = null)
    {
        return DB::transaction(function () use ($model, $transactionType) {
            $query = DB::table('inventory_transactions')
                ->where('reference_type', get_class($model))
                ->where('reference_id', $model->getKey())
                ->where('status', 1);

            if ($transactionType) {
                $query->where('type', $transactionType);
            }

            $transactions = $query->get();

            foreach ($transactions as $tx) {
                // Insert a reversing entry
                DB::table('inventory_transactions')->insert([
                    'item_id' => $tx->item_id,
                    'warehouse_id' => $tx->warehouse_id,
                    'category_id' => $tx->category_id,
                    'type' => 'REVERSAL',
                    'direction' => ($tx->direction === 'IN' ? 'OUT' : 'IN'),
                    'quantity' => $tx->quantity,
                    'area' => $tx->area,
                    'unit_cost' => $tx->unit_cost,
                    'total_cost' => $tx->total_cost,
                    'is_value_adjustment' => $tx->is_value_adjustment,
                    'reference_type' => get_class($model),
                    'reference_id' => $model->getKey(),
                    'status' => 0,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update legacy stock (reverse direction)
                $revDirection = ($tx->direction === 'IN' ? 'OUT' : 'IN');
                $this->updateLegacyStock($model, $revDirection, $tx->quantity);

                // Mark original as inactive so it does not count in stock balance and bypasses idempotency checks
                DB::table('inventory_transactions')->where('id', $tx->id)->update(['status' => 0]);
            }
        });
    }
}
