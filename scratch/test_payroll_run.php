<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Auth::loginUsingId(1);

$controller = app(\App\Http\Controllers\PayrollController::class);
$request = new \Illuminate\Http\Request();
$request->merge([
    'month_year' => '07-2026',
    'run_date' => '2026-07-13',
    'override_debit_account_id' => 22,
    'override_credit_account_id' => 14,
    'employees' => [
        1 => [
            'include' => 'on',
            'base_salary' => 1000,
            'bonus' => 0,
            'deductions' => 0,
            'currency_id' => 1,
            'exchange_rate' => 1
        ]
    ]
]);

$response = $controller->store($request);
print_r($response);
