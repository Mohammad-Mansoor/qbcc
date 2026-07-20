<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sale = \App\Sale::with('carpet')->orderBy('id', 'desc')->first();
if ($sale) {
    echo "Sale attributes:\n";
    print_r($sale->getAttributes());
    echo "\nCarpet attributes:\n";
    print_r($sale->carpet->getAttributes());
}
