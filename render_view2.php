<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $sale = \App\Sale::first();
    $sales = \App\Sale::paginate(10);
    $invoices = \App\Invoice::all();
    $packing_list = \App\PakingList::all();
    $carpets = \App\Carpet::all();
    echo view('sales.sales-list', compact('sale', 'sales', 'invoices', 'packing_list', 'carpets'))->render() ? "SUCCESS" : "FAIL";
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n";
    echo $e->getFile() . " on line " . $e->getLine() . "\n";
}
