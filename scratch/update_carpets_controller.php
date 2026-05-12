<?php
$file = '/home/anonymous/projects/public_html/app/Http/Controllers/CarpetsController.php';
$content = file_get_contents($file);

// 1. Update pass_parcha
$oldPassParcha = '/public function pass_parcha\(Request \$request\)\{\s+([\s\S]+?)\s+return response\(\)->json\(\[\'status\' => \'success\'\]\);\s+\}/';
$newPassParcha = 'public function pass_parcha(Request $request){
        $carpet = Carpet::find($request->carpet_id);
        if (!$carpet) return response()->json([\'status\' => \'error\', \'message\' => \'Carpet not found\']);

        $carpet->status = 1;
        $carpet->update();

        try {
            $this->inventoryManager->processProductionCompletion($carpet, [
                \'quantity\' => 1,
                \'amount\' => $carpet->total_price,
                \'warehouse_id\' => $carpet->warehouse_id ?? 1,
                \'date\' => now()->format(\'Y-m-d\'),
                \'reference\' => $carpet->parcha_number,
                \'description\' => "Production Completion: #" . $carpet->parcha_number,
                \'override_debit_account_id\' => $carpet->override_inventory_account_id,
            ]);
        } catch (\Exception $e) {
            \Log::error("ERP Sync failed: " . $e->getMessage());
        }

        return response()->json([\'status\' => \'success\']);
    }';
$content = preg_replace($oldPassParcha, $newPassParcha, $content);

// 2. Update PostBuyCarpet (add account override)
$content = str_replace(
    "'description' => \"Direct Purchase of Carpet #\" . \$carpet->carpet_no,",
    "'description' => \"Direct Purchase of Carpet #\" . \$carpet->carpet_no,\n            'override_debit_account_id' => \$request->override_inventory_account_id,",
    $content
);

// 3. Add syncAccounting helper before the last closing brace
$helper = '
    /**
     * Reverses and re-posts transactions if warehouse or account changed on a posted carpet
     */
    private function syncAccounting($carpet, $oldWarehouseId, $oldAccountId)
    {
        if ($carpet->status == 1) {
            if ($carpet->warehouse_id != $oldWarehouseId || $carpet->override_inventory_account_id != $oldAccountId) {
                try {
                    $this->inventoryManager->reverseTransactions($carpet, \'Correction: Warehouse/Account change\');
                    if ($carpet->contract_type == \'buy\') {
                        $this->inventoryManager->processPurchase($carpet, [
                            \'quantity\' => 1, \'unit_cost\' => $carpet->total_price,
                            \'warehouse_id\' => $carpet->warehouse_id, \'date\' => $carpet->date ?? now()->format(\'Y-m-d\'),
                            \'total_amount\' => $carpet->total_price, \'party_type\' => \'App\Agents\',
                            \'party_id\' => $carpet->agent_id, \'reference\' => $carpet->carpet_no,
                            \'override_debit_account_id\' => $carpet->override_inventory_account_id,
                        ]);
                    } else {
                        $this->inventoryManager->processProductionCompletion($carpet, [
                            \'quantity\' => 1, \'amount\' => $carpet->total_price,
                            \'warehouse_id\' => $carpet->warehouse_id, \'date\' => now()->format(\'Y-m-d\'),
                            \'reference\' => $carpet->parcha_number,
                            \'override_debit_account_id\' => $carpet->override_inventory_account_id,
                        ]);
                    }
                } catch (\Exception $e) { \Log::error("Reversal sync failed: " . $e->getMessage()); }
            }
        }
    }
';

$content = rtrim($content);
$content = substr($content, 0, strrpos($content, '}')) . $helper . "\n}";

file_put_contents($file, $content);
echo "Updated successfully\n";
