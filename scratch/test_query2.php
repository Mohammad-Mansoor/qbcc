<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 3;
$warehouse = \App\Warehouse::find($id);
$items = \DB::table('inventory_transactions')
    ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
    ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
    ->leftJoin('material_categories', 'inventory_transactions.category_id', '=', 'material_categories.material_category_id')
    ->where('inventory_transactions.warehouse_id', $id)
    ->where('inventory_transactions.status', 1)
    ->where('items.type', 'App\MaterialType')
    ->where('material_types.subtype', $warehouse->subtype)
    ->select(
        'material_types.material_type_id',
        'material_types.material_type',
        \DB::raw("MAX(material_categories.material_category) as material_category"),
        \DB::raw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as available_qty"),
        \DB::raw("MAX(items.current_cost) as current_cost")
    )
    ->groupBy('material_types.material_type_id', 'material_types.material_type')
    ->havingRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) > 0")
    ->get();

print_r($items->toArray());
