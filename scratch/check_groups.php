<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\ChartOfAccount;

echo "Distinct Report Groups:\n";
$groups = ChartOfAccount::distinct()->pluck('report_group')->toArray();
print_r($groups);
