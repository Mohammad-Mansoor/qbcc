<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// We need to fetch items and group them by warehouse.
$transactions = \DB::table('inventory_transactions')
    ->join('items', 'items.id', '=', 'inventory_transactions.item_id')
    ->leftJoin('warehouses', 'warehouses.id', '=', 'inventory_transactions.warehouse_id')
    ->where('inventory_transactions.status', 1)
    ->select(
        'items.id as item_id',
        'items.type as item_model',
        'items.ref_id',
        'warehouses.id as warehouse_id',
        'warehouses.name as warehouse_name',
        \DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.quantity WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.quantity ELSE 0 END) as qty"),
        \DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.area WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.area ELSE 0 END) as area"),
        \DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' THEN inventory_transactions.total_cost WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.total_cost ELSE 0 END) as total_value")
    )
    ->groupBy('items.id', 'items.type', 'items.ref_id', 'warehouses.id', 'warehouses.name')
    ->havingRaw("qty > 0 OR area > 0")
    ->get();

print_r(count($transactions));
