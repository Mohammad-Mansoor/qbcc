<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AccountingService;
use App\Currency;
use App\ChartOfAccount;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "--- STEP 1: SETTING UP TEST RATES ---\n";
    // Force a known state
    Currency::where('code', 'USD')->update(['exchange_rate' => 1.0, 'is_base_currency' => true]);
    Currency::where('code', 'AFN')->update(['exchange_rate' => 0.01587302, 'is_base_currency' => false]); // 1/63
    
    $usdRate = Currency::where('code', 'USD')->first()->exchange_rate;
    $afnRate = Currency::where('code', 'AFN')->first()->exchange_rate;
    
    echo "Base: USD (Rate: $usdRate)\n";
    echo "AFN Rate: $afnRate (Expected approx 0.0158)\n\n";

    echo "--- STEP 2: ATTEMPTING MULTI-CURRENCY TRANSACTION ---\n";
    $service = new AccountingService();
    
    // Scenario: Customer pays 6300 AFN. We record it in USD Base.
    // 6300 AFN * 0.01587302 = 100.00 USD
    
    $debitAccount = ChartOfAccount::where('account_type', 'Asset')->first();
    $creditAccount = ChartOfAccount::where('account_type', 'Revenue')->first();

    if (!$debitAccount || !$creditAccount) {
        throw new Exception("Test accounts not found. Please ensure Chart of Accounts is seeded.");
    }

    $tx = $service->postTransaction([
        'date' => date('Y-m-d'),
        'description' => 'Multi-currency Hardening Test',
        'journal_type' => 'receipt',
        'entries' => [
            [
                'account_id' => $debitAccount->id,
                'debit' => 6300,
                'currency_code' => 'AFN',
                'exchange_rate' => $afnRate
            ],
            [
                'account_id' => $creditAccount->id,
                'credit' => 100, // This is exactly 6300 * 0.015873
                'currency_code' => 'USD',
                'exchange_rate' => 1.0
            ]
        ]
    ]);

    echo "SUCCESS: Transaction posted with ID: " . $tx->id . "\n";
    
    $entries = DB::table('ledger_entries')->where('transaction_id', $tx->id)->get();
    foreach ($entries as $e) {
        echo "Entry: {$e->currency_code} {$e->original_amount} -> Base: {$e->base_currency_amount} USD\n";
    }

    echo "\n--- STEP 3: TESTING UNBALANCED TRANSACTION (Should Fail) ---\n";
    try {
        $service->postTransaction([
            'date' => date('Y-m-d'),
            'description' => 'Should Fail Test',
            'entries' => [
                [
                    'account_id' => $debitAccount->id,
                    'debit' => 6300,
                    'currency_code' => 'AFN',
                    'exchange_rate' => $afnRate
                ],
                [
                    'account_id' => $creditAccount->id,
                    'credit' => 101, // 1 USD error
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0
                ]
            ]
        ]);
        echo "ERROR: Unbalanced transaction was accepted! (FAIL)\n";
    } catch (Exception $e) {
        echo "CATCH: " . $e->getMessage() . " (PASS)\n";
    }

    DB::rollBack();
    echo "\n--- TEST COMPLETE (Changes Rolled Back) ---\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "GLOBAL ERROR: " . $e->getMessage() . "\n";
}
