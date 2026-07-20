<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sale = \App\Sale::orderBy('id', 'desc')->first();
if ($sale) {
    print_r($sale->toArray());
    echo "\nLedger Entries for Sale:\n";
    $entries = DB::table('ledger_entries')->where('source_type', 'Sale')->where('source_id', $sale->id)->get();
    print_r($entries->toArray());
}
