<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rules = \App\MappingRule::all();
foreach ($rules as $r) {
    echo "ID: {$r->id} | Type: {$r->transaction_type} | Key: {$r->mapping_key} | Cond: {$r->condition} | Debit Acc: {$r->debit_account_id} | Credit Acc: {$r->credit_account_id}\n";
}
