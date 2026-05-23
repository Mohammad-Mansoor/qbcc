<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['different_account_payments', 'different_account_totals', 'agent_payments', 'new_different_account_payments'];
foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        echo "Columns of $t:\n";
        print_r(Schema::getColumnListing($t));
    } else {
        echo "Table $t does not exist.\n";
    }
}
