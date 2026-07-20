<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payment = \App\EmployeePayment::where('currency_code', '!=', 'USD')->latest()->first();
print_r($payment ? $payment->toArray() : "No non-USD payments");

if ($payment) {
    $entries = \App\LedgerEntry::where('party_type', 'App\OfficeEmployee')
        ->where('party_id', $payment->employee_id)
        ->get();
    print_r($entries->toArray());
}

