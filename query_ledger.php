<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = DB::table('ledger_entries')
    ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
    ->where('ledger_transactions.reference', 'PAY-07-2026')
    ->select('ledger_entries.*')
    ->get();
print_r($entries);
