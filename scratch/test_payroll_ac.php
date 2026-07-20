<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rule = \App\MappingRule::where('mapping_key', 'PAYROLL_ACCRUAL')->first();
print_r($rule->toArray());
