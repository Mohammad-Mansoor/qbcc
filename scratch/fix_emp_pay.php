<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payments = \App\EmployeePayment::all();
$fixed = 0;
foreach($payments as $payment) {
    if($payment->currency_code !== 'USD' && $payment->currency_code !== null) {
        $amount = $payment->original_amount ?? $payment->amount;
        $entries = \App\LedgerEntry::join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.source_type', 'App\EmployeePayment')
            ->where('ledger_transactions.source_id', $payment->id)
            ->where('ledger_entries.currency_code', 'USD')
            ->update([
                'ledger_entries.currency_code' => $payment->currency_code,
                'ledger_entries.exchange_rate' => $payment->exchange_rate,
                'ledger_entries.original_amount' => $amount,
                'ledger_entries.debit' => \DB::raw("CASE WHEN ledger_entries.debit > 0 THEN {$amount} ELSE 0 END"),
                'ledger_entries.credit' => \DB::raw("CASE WHEN ledger_entries.credit > 0 THEN {$amount} ELSE 0 END")
            ]);
        if($entries) {
            $fixed++;
        }
    }
}
echo "Fixed $fixed payments\n";
