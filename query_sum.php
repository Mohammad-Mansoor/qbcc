<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sum = DB::table('inventory_transactions')
    ->where('item_id', 1)
    ->where('status', 1)
    ->where('direction', 'IN')
    ->sum('base_unit_cost');
echo "Sum of base_unit_cost for IN transactions (status=1): $sum\n";

$item = DB::table('items')->find(1);
echo "current_cost: {$item->current_cost}\n";
