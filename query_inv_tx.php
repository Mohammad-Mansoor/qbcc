<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$txs = DB::table('inventory_transactions')->where('item_id', 1)->orderBy('id', 'asc')->get()->toArray();
print_r($txs);
