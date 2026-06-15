<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// We need to fetch carpets and group them by warehouse.
$transactions = \DB::table('carpets')
    ->join('carpets', 'carpets.id', '=', 'carpets.item_id')
    ->leftJoin('warehouses', 'warehouses.id', '=', 'carpets.warehouse_id')
    ->where('carpets.status', 1)
    ->select(
        'carpets.id as item_id',
        'carpets.type as item_model',
        'carpets.ref_id',
        'warehouses.id as warehouse_id',
        'warehouses.name as warehouse_name',
        \DB::raw("SUM(CASE WHEN carpets.direction = 'IN' AND carpets.is_value_adjustment = 0 THEN carpets.quantity WHEN carpets.direction = 'OUT' THEN -carpets.quantity ELSE 0 END) as qty"),
        \DB::raw("SUM(CASE WHEN carpets.direction = 'IN' AND carpets.is_value_adjustment = 0 THEN carpets.area WHEN carpets.direction = 'OUT' THEN -carpets.area ELSE 0 END) as area"),
        \DB::raw("SUM(CASE WHEN carpets.direction = 'IN' THEN carpets.total_cost WHEN carpets.direction = 'OUT' THEN -carpets.total_cost ELSE 0 END) as total_value")
    )
    ->groupBy('carpets.id', 'carpets.type', 'carpets.ref_id', 'warehouses.id', 'warehouses.name')
    ->havingRaw("qty > 0 OR area > 0")
    ->get();

print_r(count($transactions));
