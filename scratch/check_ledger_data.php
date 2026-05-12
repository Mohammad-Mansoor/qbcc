<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$sourceId = 4;
$sourceType = 'App\CustomerOrderDetails';

echo "Checking transactions for Source ID: $sourceId, Type: $sourceType\n";

$txs = DB::table('ledger_transactions')
    ->where('source_id', $sourceId)
    ->where('source_type', $sourceType)
    ->get();

if ($txs->isEmpty()) {
    echo "No transactions found in ledger_transactions.\n";
    
    echo "Checking all transactions to see what source_types exist:\n";
    $types = DB::table('ledger_transactions')->select('source_type')->distinct()->get();
    foreach($types as $t) {
        echo " - " . ($t->source_type ?: 'NULL') . "\n";
    }
} else {
    echo "Found " . $txs->count() . " transactions:\n";
    foreach($txs as $tx) {
        echo "ID: {$tx->id}, Date: {$tx->date}, Ref: {$tx->reference}, Type: {$tx->source_type}\n";
        
        $entries = DB::table('ledger_entries')->where('transaction_id', $tx->id)->get();
        foreach($entries as $e) {
            echo "  - Account: {$e->account_id}, Debit: {$e->debit}, Credit: {$e->credit}\n";
        }
    }
}
