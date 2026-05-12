<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\InventoryTransactionManager;
use App\Carpet;
use Illuminate\Support\Facades\DB;

try {
    $manager = app(InventoryTransactionManager::class);
    $carpet = Carpet::find(3);

    echo "Testing Process Purchase for Carpet ID 3...\n";
    $result = $manager->processPurchase($carpet, [
        'quantity' => 1,
        'unit_cost' => 500,
        'total_amount' => 500,
        'transaction_type' => 'material_purchase',
        'date' => date('Y-m-d'),
        'reference' => 'TEST-001',
        'description' => 'Test ERP Purchase',
    ]);

    echo "Success! Result:\n";
    print_r($result);

    // Verify DB
    $invTx = DB::table('inventory_transactions')->where('id', $result['inventory_transaction_id'])->first();
    echo "\nInventory Transaction:\n";
    print_r($invTx);

    $ledgerTx = $result['accounting_transaction'];
    echo "\nLedger Transaction ID: " . $ledgerTx->id . "\n";
    $entries = DB::table('ledger_entries')->where('transaction_id', $ledgerTx->id)->get();
    echo "Ledger Entries:\n";
    print_r($entries->toArray());

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
