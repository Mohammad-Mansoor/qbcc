<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['customers', 'carpets', 'customer_payments', 'ledger_entries'];
foreach($tables as $table) {
    echo "\nTable: $table\n";
    print_r(Schema::getColumnListing($table));
}
