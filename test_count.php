<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$itemsCount = \DB::table('items')->count();
$txCount = \DB::table('inventory_transactions')->count();
$carpetCount = \DB::table('carpets')->where('status', 5)->count();
$pmCount = \DB::table('purchase_materials')->count();

echo json_encode(['items' => $itemsCount, 'tx' => $txCount, 'carpets' => $carpetCount, 'pm' => $pmCount]);
