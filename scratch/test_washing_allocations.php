<?php

use App\WashingPayment;
use App\WashingPaymentAllocation;
use App\ProductionBatch;
use App\WashingTeam;
use App\User;
use App\CarpetWash;
use App\Carpet;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;
use App\Http\Controllers\WashingPaymentController;

// Ensure we are running under bootstrap environment using absolute paths
require_once '/home/anonymous/projects/public_html/vendor/autoload.php';
$app = require_once '/home/anonymous/projects/public_html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Washing Advance Allocation Validation...\n";

try {
    DB::transaction(function() {
        // 1. Get or create washing team
        $team = WashingTeam::first();
        if (!$team) {
            $team = WashingTeam::create([
                'name' => 'Test Washing Team',
                'address' => 'Test Address',
                'contact_no' => '123456789',
            ]);
        }

        echo "Washing Team ID: {$team->id}\n";

        // 2. Create Advance Payment of $1,000 USD
        $payment = WashingPayment::create([
            'team_id' => $team->id,
            'date' => date('Y-m-d'),
            'type' => 'گرفت', // Payment sent (advance out)
            'description' => 'Test Washing Advance Payment of $1000',
            'wash_number' => 'General',
            'currency_code' => 'USD',
            'original_amount' => 1000.00,
            'exchange_rate' => 1.0000,
            'dollar_rate' => 1.0000,    // Legacy required field
            'base_amount' => 1000.00,
            'amount' => 1000.00,       // Legacy field
            'amount_af' => 0.00,       // Legacy field
            'is_advance' => 1,
            'payment_status' => 'unallocated',
            'remaining_unallocated_amount' => 1000.00,
            'status' => 1, // Approved
        ]);

        echo "Created Washing Advance Payment ID: {$payment->id}\n";

        // Post to accounting using container resolved controller
        $controller = app(WashingPaymentController::class);
        $reflect = new \ReflectionClass($controller);
        $method = $reflect->getMethod('postPaymentToAccounting');
        $method->setAccessible(true);
        $method->invoke($controller, $payment);
        echo "Posted payment to accounting.\n";

        // Verify ledger transaction exists for payment
        $ledgerTxPayment = DB::table('ledger_transactions')->where('reference', 'WSH-PAY-' . $payment->id)->first();
        if ($ledgerTxPayment) {
            echo " Ledger transaction found for payment: {$ledgerTxPayment->id}, Status: {$ledgerTxPayment->status}\n";
            $entries = DB::table('ledger_entries')->where('transaction_id', $ledgerTxPayment->id)->get();
            foreach ($entries as $entry) {
                echo "   Account ID: {$entry->account_id}, Debit: {$entry->debit}, Credit: {$entry->credit}\n";
            }
        } else {
            echo " [WARNING] No ledger transaction found for payment reference WSH-PAY-{$payment->id}\n";
        }

        // 3. Create a Production Batch
        $batch = ProductionBatch::create([
            'reference_number' => 'BATCH-TEST-999',
            'type' => 'wash',
            'status' => 'open',
        ]);

        // Create carpet and CarpetWash to give batch an outstanding cost of $1,000 USD
        $carpet = Carpet::first() ?: Carpet::create([
            'carpet_no' => 'CRPT-WSH-999',
            'width' => 2.00,
            'height' => 5.00,
            'area' => 10.00,
            'total_price' => 100.00,
            'status' => 0,
        ]);

        $wash = CarpetWash::create([
            'team_id' => $team->id,
            'carpetId' => $carpet->carpet_id,
            'date' => date('Y-m-d'),
            'wash_number' => 'BATCH-TEST-999',
            'wash_number_sh' => 'BATCH-TEST-999',
            'description' => 'Test wash',
            'price' => '100',
            'total_price' => '1000',
            'af_total_price' => '0',
            'exchange_rate' => 1.0000,
            'base_currency_amount' => 1000.00,
            'currency_code' => 'USD',
        ]);

        echo "Created Production Batch ID: {$batch->id}, Reference: BATCH-TEST-999, Total Cost: 1000.00\n";

        // 4. Allocate $400 USD from Advance to Batch
        echo "Allocating $400 USD to Batch...\n";
        $request = new \Illuminate\Http\Request([
            'washing_payment_id' => $payment->id,
            'allocatable_id' => $batch->id,
            'allocatable_type' => 'App\ProductionBatch',
            'amount' => 400.00,
        ]);

        // Login a user for Auth dependency in activity logging
        $user = User::first() ?: User::create([
            'name' => 'Test User',
            'last_name' => 'Test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'SP',
        ]);
        Auth::login($user);

        $response = $controller->allocateAdvance($request);
        $resData = json_decode($response->getContent(), true);

        echo "Allocation Response: " . print_r($resData, true) . "\n";

        // Reload models
        $payment->refresh();

        echo "After Allocation:\n";
        echo "  Payment Status: {$payment->payment_status}\n";
        echo "  Remaining Unallocated: {$payment->remaining_unallocated_amount}\n";

        // Check allocation records
        $allocation = WashingPaymentAllocation::where('washing_payment_id', $payment->id)->first();
        if ($allocation) {
            echo "  Allocation Record ID: {$allocation->id}, Amount: {$allocation->allocated_amount}\n";
            
            // Verify ledger transaction exists for allocation
            $ledgerTxSettle = DB::table('ledger_transactions')
                ->where('source_type', 'App\WashingPaymentAllocation')
                ->where('source_id', $allocation->id)
                ->first();
            if ($ledgerTxSettle) {
                echo "   Ledger transaction found for settlement: {$ledgerTxSettle->id}, Status: {$ledgerTxSettle->status}\n";
                $entries = DB::table('ledger_entries')->where('transaction_id', $ledgerTxSettle->id)->get();
                foreach ($entries as $entry) {
                    echo "     Account ID: {$entry->account_id}, Debit: {$entry->debit}, Credit: {$entry->credit}\n";
                }
            } else {
                echo "   [WARNING] No ledger transaction found for settlement source: App\WashingPaymentAllocation ID: {$allocation->id}\n";
            }

            // 5. Remove the allocation (Reversal)
            echo "Removing Allocation ID: {$allocation->id}...\n";
            $delResponse = $controller->removeAllocation($allocation->id);
            $delData = json_decode($delResponse->getContent(), true);
            echo "Removal Response: " . print_r($delData, true) . "\n";

            // Reload models
            $payment->refresh();

            echo "After Removal:\n";
            echo "  Payment Status: {$payment->payment_status}\n";
            echo "  Remaining Unallocated: {$payment->remaining_unallocated_amount}\n";

            // Verify ledger transaction for settlement is deleted/reversed
            $ledgerTxSettlePostDel = DB::table('ledger_transactions')->where('id', $ledgerTxSettle->id)->first();
            echo "  Original Settlement Transaction Status Post-Removal: {$ledgerTxSettlePostDel->status}\n";

            $reversalTx = DB::table('ledger_transactions')->where('reversed_transaction_id', $ledgerTxSettle->id)->first();
            if ($reversalTx) {
                echo "  Reversal Transaction Found: ID: {$reversalTx->id}, Status: {$reversalTx->status}, Reference: {$reversalTx->reference}\n";
            } else {
                echo "  [WARNING] No reversal transaction found!\n";
            }
        } else {
            echo "   [ERROR] Allocation record was not created.\n";
        }

        // Rollback so we don't pollute the database
        throw new \Exception("Rollback after test completion.");
    });
} catch (\Exception $e) {
    if ($e->getMessage() === "Rollback after test completion.") {
        echo "Success! Validation completed without errors and changes were safely rolled back.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}
