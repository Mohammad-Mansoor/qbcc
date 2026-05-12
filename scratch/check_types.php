<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\ChartOfAccount;

echo "Distinct Account Types:\n";
$types = ChartOfAccount::distinct()->pluck('account_type')->toArray();
print_r($types);
