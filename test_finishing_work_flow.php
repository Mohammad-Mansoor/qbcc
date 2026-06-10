<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Carpet;
use App\CarpetWash;
use App\FinishingWork;
use App\FinishingTeam;
use App\FinishingTeamCategory;
use App\Warehouse;
use App\Http\Controllers\FinishingWorkController;
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
        // Setup mock environment
        $admin = \App\User::where('role', 'SP')->first() ?? \App\User::first();
        auth()->login($admin);

        // Find or create dummy warehouses
        $sourceWh = Warehouse::first() ?? Warehouse::create(['name' => 'Source Wh', 'subtype' => 'carpet', 'is_active' => true]);
        $targetWh = Warehouse::where('id', '!=', $sourceWh->id)->first() ?? Warehouse::create(['name' => 'Target Wh', 'subtype' => 'carpet', 'is_active' => true]);
        
        // Find or create dummy carpet
        $carpet = Carpet::where('status', 4)->first();
        if (!$carpet) {
            $carpet = Carpet::create([
                'carpet_no' => 'MOCK-101',
                'height' => 4.00,
                'width' => 3.00,
                'area' => 12.00,
                'status' => 4, // Sent to Finishing
                'warehouse_id' => $sourceWh->id,
                'total_price' => 100.0,
                'total_price_af' => 7000.0,
            ]);
        }
        
        // Create matching item record if it doesn't exist
        $item = DB::table('items')->where('type', 'App\Carpet')->where('ref_id', $carpet->carpet_id)->first();
        if (!$item) {
            DB::table('items')->insert([
                'type' => 'App\Carpet',
                'ref_id' => $carpet->carpet_id,
                'current_cost' => 100.0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Mock an initial IN movement to warehouse
        DB::table('inventory_transactions')->insert([
            'item_id' => DB::table('items')->where('type', 'App\Carpet')->where('ref_id', $carpet->carpet_id)->value('id'),
            'warehouse_id' => $carpet->warehouse_id,
            'direction' => 'IN',
            'quantity' => 1,
            'area' => $carpet->area,
            'unit_cost' => 100.0,
            'currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'type' => 'Initial',
            'status' => 1,
            'created_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first() ?? $carpet;
        $team = FinishingTeam::first() ?? FinishingTeam::create(['name' => 'Finishing Team A']);
        
        // Allowed accounts lookup
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('FINISHING_CREDIT', 'credit');
        
        $debitAccId = $allowedDebitAccounts->first()->id ?? null;
        $creditAccId = $allowedCreditAccounts->first()->id ?? null;
        
        if (!$debitAccId || !$creditAccId) {
            throw new Exception("Debit or Credit accounts not set up in Chart of Accounts.");
        }

        echo "Initial State: Carpet #{$carpet->carpet_no}, Status: {$carpet->status}, Warehouse: {$carpet->warehouse_id}\n";
        printWarehouseStock("Initial Warehouse Stock", $carpet->warehouse_id);

        $controller = app(FinishingWorkController::class);

        // 1. Simulate STORE finishing work
        $request = Request::create('/dashboard/finishing-center/store', 'POST', [
            'carpetId' => $carpet->carpet_id,
            'finished' => 1,
            'warehouse_id' => $targetWh->id,
            'currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'override_debit_account_id' => $debitAccId,
            'override_credit_account_id' => $creditAccId,
            'qaitan_checkbox' => 'on',
            'team_id_qaitan' => $team->id,
            'price_af_qaitan' => 5.0, // Unit price
            'date_qaitan' => '2026-06-06',
            'finish_number_qaitan' => 'TA-101',
        ]);

        echo "\n=== SIMULATING FINISHING WORK STORE (Qaitan, 5.0 USD/m2) ===\n";
        $controller->store($request);

        $carpet->refresh();
        $finish = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 1)->firstOrFail();
        
        echo "Stored FinishingWork ID: {$finish->id}\n";
        echo "  - Price USD (calculated): {$finish->price}\n";
        echo "  - Selected Debit Account: {$finish->debit_account_id}\n";
        echo "  - Selected Credit Account: {$finish->credit_account_id}\n";
        echo "  - Target Warehouse saved on FinishingWork: {$finish->warehouse_id}\n";
        echo "  - Carpet Status after completion: {$carpet->status}\n";
        echo "  - Carpet Warehouse after completion: {$carpet->warehouse_id}\n";

        // Expected price: area * unit price
        $expectedPrice = $newCarpet->area * 5.0;
        if (abs($finish->price - $expectedPrice) > 0.001) {
            throw new Exception("FAIL: Price was double calculated or incorrect! Expected {$expectedPrice}, got {$finish->price}");
        }
        echo "SUCCESS: Price calculated correctly!\n";

        printWarehouseStock("Warehouse Stock after Store", $carpet->warehouse_id);

        // Verify completion movements exist in inventory_transactions linked to $finish
        $movements = DB::table('inventory_transactions')
            ->where('reference_type', 'App\FinishingWork')
            ->where('reference_id', $finish->id)
            ->where('type', 'Finishing Completion Transfer')
            ->get();
            
        echo "Found " . count($movements) . " completion movements linked to FinishingWork.\n";
        if (count($movements) !== 2) {
            throw new Exception("FAIL: Expected 2 completion movements, found " . count($movements));
        }
        echo "SUCCESS: Completion movements recorded and linked correctly!\n";

        // 2. Simulate EDIT/UPDATE finishing work
        // Let's edit Qaitan price to 10 USD/m2, change accounts, and change target warehouse
        $otherDebitAccId = $allowedDebitAccounts->count() > 1 ? $allowedDebitAccounts->get(1)->id : $debitAccId;
        $otherCreditAccId = $allowedCreditAccounts->count() > 1 ? $allowedCreditAccounts->get(1)->id : $creditAccId;
        
        $requestUpdate = Request::create("/dashboard/finishing-center/{$finish->id}", 'POST', [
            'carpetId' => $carpet->carpet_id,
            'finished' => 1,
            'warehouse_id' => $sourceWh->id, // transfer back to source warehouse
            'price_af' => 10.0, // New unit price
            'finish_number' => 'TA-101-ED',
            'team_id' => $team->id,
            'category_id' => 1,
            'date' => '2026-06-06',
            'override_debit_account_id' => $otherDebitAccId,
            'override_credit_account_id' => $otherCreditAccId,
            'description' => 'Updated finishing',
        ]);

        echo "\n=== SIMULATING FINISHING WORK UPDATE (Qaitan updated to 10.0 USD/m2) ===\n";
        $controller->update($requestUpdate, $finish);

        $finish->refresh();
        $carpet->refresh();

        echo "Updated FinishingWork ID: {$finish->id}\n";
        echo "  - Price USD (calculated): {$finish->price}\n";
        echo "  - Selected Debit Account: {$finish->debit_account_id}\n";
        echo "  - Selected Credit Account: {$finish->credit_account_id}\n";
        echo "  - Target Warehouse saved on FinishingWork: {$finish->warehouse_id}\n";
        echo "  - Carpet Status after update: {$carpet->status}\n";
        echo "  - Carpet Warehouse after update: {$carpet->warehouse_id}\n";

        // Expected price: area * unit price
        $expectedUpdatedPrice = $newCarpet->area * 10.0;
        if (abs($finish->price - $expectedUpdatedPrice) > 0.001) {
            throw new Exception("FAIL: Updated price was incorrect! Expected {$expectedUpdatedPrice}, got {$finish->price}");
        }
        echo "SUCCESS: Updated price calculated correctly!\n";

        // Verify completion movements updated
        $updatedMovements = DB::table('inventory_transactions')
            ->where('reference_type', 'App\FinishingWork')
            ->where('reference_id', $finish->id)
            ->where('type', 'Finishing Completion Transfer')
            ->where('status', 1)
            ->get();
            
        echo "Found " . count($updatedMovements) . " active completion movements after update.\n";
        if (count($updatedMovements) !== 2) {
            throw new Exception("FAIL: Expected 2 active completion movements after update, found " . count($updatedMovements));
        }
        
        // Verify target warehouse of completion movement is sourceWh->id (since we passed sourceWh as target warehouse in update)
        $inMovement = DB::table('inventory_transactions')
            ->where('reference_type', 'App\FinishingWork')
            ->where('reference_id', $finish->id)
            ->where('type', 'Finishing Completion Transfer')
            ->where('direction', 'IN')
            ->where('status', 1)
            ->first();
            
        if ($inMovement->warehouse_id != $sourceWh->id) {
            throw new Exception("FAIL: Expected target warehouse to be {$sourceWh->id}, got {$inMovement->warehouse_id}");
        }
        echo "SUCCESS: Completion warehouse transfer updated correctly in database!\n";

        // 3. Simulate DELETE finishing work
        echo "\n=== SIMULATING FINISHING WORK DELETION ===\n";
        $controller->destroy($finish->id);

        $carpet->refresh();
        echo "Carpet Status after deletion: {$carpet->status}\n";
        echo "Carpet Warehouse after deletion: {$carpet->warehouse_id}\n";

        if ($carpet->status != 4) {
            throw new Exception("FAIL: Expected carpet status to revert to 4, got {$carpet->status}");
        }
        echo "SUCCESS: Carpet status reverted to 4 successfully!\n";

        // Verify completion movements are reversed (status = 0 or deleted)
        $activeMovements = DB::table('inventory_transactions')
            ->where('reference_type', 'App\FinishingWork')
            ->where('reference_id', $finish->id)
            ->where('status', 1)
            ->get();
            
        echo "Found " . count($activeMovements) . " active movements after deletion.\n";
        if (count($activeMovements) > 0) {
            throw new Exception("FAIL: Completion movements were not reversed on deletion!");
        }
        echo "SUCCESS: Completion movements reversed successfully on deletion!\n";

        // Rollback so we don't pollute the DB during this simulation
        throw new Exception("ROLLING BACK TEST (SUCCESSFUL)");
    });
} catch (Exception $e) {
    echo "\nSimulation finished: " . $e->getMessage() . "\n";
}
