<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\ChartOfAccount;

$accounts = ChartOfAccount::all();
foreach ($accounts as $acc) {
    echo "ID: {$acc->id}, Code: {$acc->account_code}, Name: {$acc->account_name}, Type: {$acc->account_type}, Cash: {$acc->is_cash_account}\n";
}
