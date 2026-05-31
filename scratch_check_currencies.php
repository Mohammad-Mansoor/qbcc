<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$currencies = DB::table('currencies')->get();
foreach ($currencies as $c) {
    echo "ID: {$c->id}, Code: {$c->code}, Name: {$c->name}, Rate: {$c->exchange_rate}, Is Base: {$c->is_base_currency}\n";
}
