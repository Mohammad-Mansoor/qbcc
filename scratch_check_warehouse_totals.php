<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$txs = DB::table('inventory_transactions')
    ->where('reference_type', 'App\Carpet')
    ->where('reference_id', 54)
    ->get();

print_r($txs);

$carpet = DB::table('carpets')->where('carpet_id', 54)->first();
print_r($carpet);

$item = DB::table('items')->where('type', 'App\Carpet')->where('ref_id', 54)->first();
print_r($item);
