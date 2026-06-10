<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$acct = app(\App\Services\AccountingService::class);
try {
    // We try to find the rule
    $rule = \App\MappingRule::where('transaction_type', 'expense')
        ->where(function($q) {
            $q->where('mapping_key', 'CASH_OUT')
              ->orWhere('condition', 'CASH_OUT');
        })
        ->first();
    if ($rule) {
        echo "Found rule: ID {$rule->id}, key: {$rule->mapping_key}\n";
    } else {
        echo "NO RULE FOUND for expense / CASH_OUT!\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
