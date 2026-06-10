<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Carpet;
use App\CarpetWash;
use App\Http\Controllers\CarpetWashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Print warehouse stock helper
function printWarehouseStock($title, $carpetWhId = null) {
    echo "--- {$title} ---\n";
    $whIds = [1, 63, 64];
    if ($carpetWhId && !in_array($carpetWhId, $whIds)) {
        $whIds[] = $carpetWhId;
    }
    foreach ($whIds as $whId) {
        $metrics = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->where('inventory_transactions.warehouse_id', $whId)
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\Carpet')
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END), 0) as qty")
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.area ELSE -inventory_transactions.area END), 0) as area")
            ->first();
        echo "  Warehouse ID {$whId}: Qty: {$metrics->qty}, Area: {$metrics->area} m2\n";
    }
}

try {
    DB::transaction(function() {
        $carpet = Carpet::findOrFail(66);
        $wash = CarpetWash::where('carpetId', 66)->firstOrFail();
        
        echo "Initial State: Carpet #{$carpet->carpet_no}, Status: {$carpet->status}, Warehouse: {$carpet->warehouse_id}\n";
        printWarehouseStock("Initial Warehouse Stock", $carpet->warehouse_id);

        // 1. Simulate Complete Wash
        // Complete wash updates carpet status to 13 and records wash cost
        $controller = app(CarpetWashController::class);
        
        // We will fake authentication
        $admin = \App\User::where('role', 'CO')->first() ?? \App\User::first();
        auth()->login($admin);
        
        $selectionService = new \App\Services\AccountSelectionService();
        $debitAccountId = $selectionService->getValidAccounts('WASHING_CREDIT', 'debit')->first()->id ?? 1;
        $creditAccountId = $selectionService->getValidAccounts('WASHING_CREDIT', 'credit')->first()->id ?? 1;

        $request = Request::create('/dashboard/carpet-wash/store', 'POST', [
            'wash_id' => $wash->id,
            'carpetId' => 66,
            'price' => 10,
            'height' => 4,
            'width' => 3,
            'area' => 11.5,
            'total_price' => 120,
            'af_total_price' => 12000,
            'date' => '2026-05-27',
            'description' => 'Tested washing',
            'currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'account_id' => $debitAccountId ?? 1,
            'override_credit_account_id' => $creditAccountId ?? 1,
            'warehouse_id' => $carpet->warehouse_id ?? 1,
            'wash_number' => 'W-100',
            'wash_number_sh' => 'SH-100',
        ]);

        echo "\n=== SIMULATING WASHING COMPLETION ===\n";
        $response = $controller->store($request, $wash);
        
        $carpet->refresh();
        echo "After Wash Completion: Status: {$carpet->status}, Warehouse: {$carpet->warehouse_id}\n";
        printWarehouseStock("Warehouse Stock after Wash", $carpet->warehouse_id);

        // 2. Simulate Send to Finishing (Tayaari) to Warehouse 63
        $finishingId = \App\FinishingTeam::first()->id ?? 1;
        $requestFinish = Request::create('/dashboard/carpet-wash/sent-to-finish/' . $carpet->carpet_id, 'POST', [
            'warehouse_id' => 63,
            'finishing_id' => $finishingId,
        ]);

        echo "\n=== SIMULATING SEND TO FINISHING (TAYAARI) TO WH 63 ===\n";
        $controller->sent_to_finishing_center($requestFinish, $carpet);

        $carpet->refresh();
        echo "After Sent to Finishing: Status: {$carpet->status}, Warehouse: {$carpet->warehouse_id}\n";
        printWarehouseStock("Warehouse Stock after Finishing Transfer", $carpet->warehouse_id);

        // Let's print active transactions for this carpet
        $txs = DB::table('inventory_transactions')
            ->where(function($query) use ($carpet, $wash) {
                $query->where(function($q) use ($carpet) {
                    $q->where('reference_type', 'App\Carpet')->where('reference_id', $carpet->carpet_id);
                })->orWhere(function($q) use ($wash) {
                    $q->where('reference_type', 'App\CarpetWash')->where('reference_id', $wash->id);
                });
            })
            ->where('status', 1)
            ->get();
        echo "\nActive Inventory Transactions for Carpet #{$carpet->carpet_no}:\n";
        foreach ($txs as $tx) {
            echo "  - ID: {$tx->id}, Wh: {$tx->warehouse_id}, Type: {$tx->type}, Dir: {$tx->direction}, Qty: {$tx->quantity}, Area: {$tx->area}, ValueAdj: {$tx->is_value_adjustment}\n";
        }

        // Rollback so we don't pollute the DB during this simulation
        throw new Exception("ROLLING BACK TEST");
    });
} catch (Exception $e) {
    echo "\nSimulation finished with exception: " . $e->getMessage() . "\n";
}
