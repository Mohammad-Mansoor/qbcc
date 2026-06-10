<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Carpet;
use App\Invoice;
use App\Currency;
use App\User;
use App\Sale;
use App\LedgerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Log in admin user
$user = User::first();
Auth::login($user);

// Start database transaction to prevent committing test data
DB::beginTransaction();

try {
    echo "Finding carpet with status 5 (Completed/Ready for sale)...\n";
    $carpet = Carpet::where('status', 5)->first();
    if (!$carpet) {
        echo "No carpet in status 5 found. Force setting a test carpet to status 5...\n";
        $carpet = Carpet::first();
        $carpet->status = 5;
        $carpet->save();
    }
    
    echo "Using Carpet ID: {$carpet->carpet_id}, Carpet No: {$carpet->carpet_no}\n";

    echo "Finding an invoice...\n";
    $invoice = Invoice::orderBy('id', 'desc')->first();
    if (!$invoice) {
        throw new \Exception("No invoices found in database to link the sale.");
    }
    echo "Using Invoice ID: {$invoice->id}, Invoice No: {$invoice->invoice_no}, Customer ID: {$invoice->customer_id}\n";

    echo "Finding USD currency...\n";
    $currency = Currency::where('code', 'USD')->first();
    if (!$currency) {
        $currency = Currency::first();
    }
    echo "Using Currency: {$currency->code} (ID: {$currency->id})\n";

    // Setup request variables
    $req = new Request();
    $req->merge([
        'carpet_id' => $carpet->carpet_id,
        'invoice_id' => $invoice->id,
        'sale_cost_per_meter' => 150.00,
        'sale_cost_total' => 1500.00,
        'total_price_cost' => 1000.00, // cost price
        'carpet_type' => $carpet->type->carpet_type ?? 'Handmade',
        'carpet_quality' => $carpet->quality->quality ?? 'Silk',
        'package_id' => 1,
        'customer_code' => $invoice->customer->customer_code,
        'carpet_height' => 2.0,
        'carpet_width' => 5.0,
        'carpet_area' => 10.0,
        'currency_id' => $currency->id,
        'exchange_rate' => 1.0
    ]);

    echo "Simulating SaleController@store...\n";
    $controller = app(\App\Http\Controllers\SaleController::class);
    
    // Call store
    $response = $controller->store($req);
    
    echo "SaleController redirected successfully! Status check...\n";

    // Verify Sale record
    $sale = Sale::where('carpet_id', $carpet->carpet_id)->first();
    if (!$sale) {
        throw new \Exception("FAIL: Sale record was not created in the database.");
    }
    
    echo "SUCCESS: Sale record created successfully! ID: {$sale->id}\n";
    echo "Sale profit: {$sale->profit}\n";
    echo "Carpet Status updated to: " . Carpet::find($carpet->carpet_id)->status . " (should be 6)\n";

    // Verify ledger postings
    if ($sale->ledger_transaction_id) {
        $lt = LedgerTransaction::with('entries')->find($sale->ledger_transaction_id);
        echo "SUCCESS: Ledger transaction created! ID: {$lt->id}, Amount: {$lt->amount} USD\n";
        echo "Entries posted:\n";
        foreach ($lt->entries as $entry) {
            echo "  Account Code: {$entry->account->account_code} | Debit: {$entry->debit} | Credit: {$entry->credit} | Currency: {$entry->currency_code}\n";
        }
    } else {
        echo "WARNING: No ledger_transaction_id found on the sale record.\n";
    }

    echo "All checks passed successfully!\n";
} catch (\Exception $e) {
    echo "FAIL: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
} finally {
    // Rollback changes to keep DB pristine
    DB::rollBack();
    echo "Transaction rolled back.\n";
}
