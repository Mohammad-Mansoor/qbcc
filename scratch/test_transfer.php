<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create another yarn warehouse
$destId = \App\Warehouse::insertGetId([
    'name' => 'Yarn WH 2',
    'type' => 'Storage',
    'subtype' => 'yarn',
    'is_active' => 1
]);

$sourceId = 3;

// Create transfer
$transfer = \App\WarehouseTransfer::create([
    'transfer_number' => 'TRF-TEST-002',
    'transfer_date' => now()->format('Y-m-d'),
    'item_type' => 'yarn',
    'source_warehouse_id' => $sourceId,
    'destination_warehouse_id' => $destId,
    'quantity' => 10,
    'description' => 'Test',
    'created_by' => 1,
    'status' => 'posted'
]);

$model = \App\MaterialType::first();
$itemsData = [
    [
        'model' => $model,
        'quantity' => 10
    ]
];

$txManager = app(\App\Services\InventoryTransactionManager::class);
$txManager->processWarehouseTransfer($transfer, $itemsData);

echo "Transfer created successfully\n";
