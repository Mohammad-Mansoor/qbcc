<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Currency;

$currencies = Currency::all();
foreach ($currencies as $curr) {
    echo "ID: {$curr->id}, Code: {$curr->code}, Name: {$curr->name}, Rate: {$curr->exchange_rate}\n";
}
