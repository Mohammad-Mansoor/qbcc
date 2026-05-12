<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rules = \App\MappingRule::all();
foreach ($rules as $r) {
    $debit = \App\ChartOfAccount::find($r->debit_account_id);
    $credit = \App\ChartOfAccount::find($r->credit_account_id);
    echo "Key: {$r->mapping_key}, Name: {$r->transaction_name}\n";
    echo "  Debit: [{$debit->account_code}] {$debit->account_name}\n";
    echo "  Credit: [{$credit->account_code}] {$credit->account_name}\n\n";
}
