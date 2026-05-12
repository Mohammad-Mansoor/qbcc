<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transactions = \App\LedgerTransaction::where('source_type', 'NewMonthlyExpenseBalance')->get();
foreach ($transactions as $t) {
    echo "ID: {$t->id}, Date: {$t->date}, Desc: {$t->description}, Source: {$t->source_type}, SourceID: {$t->source_id}, Status: {$t->status}\n";
}
