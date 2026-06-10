<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Customer;
use App\Currency;
use App\CustomerPayment;
use App\LedgerTransaction;
use App\LedgerEntry;
use Illuminate\Support\Facades\DB;

class CustomerPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCustomerPaymentCreationAndLedgerIntegration()
    {
        // 1. Authenticate as SP user
        $user = User::where('role', 'SP')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'last_name' => 'SP',
                'role' => 'SP',
                'email' => 'test_sp@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 2. Fetch or create a Customer
        $customer = Customer::first();
        if (!$customer) {
            $customer = Customer::create([
                'customer_code' => 'CUST-T01',
                'name' => 'Test Customer',
                'company_name' => 'Test Corp',
                'phone' => '123456789',
                'type' => 'مشتری قالین',
            ]);
        }

        // 3. Fetch or create a Currency
        $currency = Currency::where('code', 'AFN')->first();
        if (!$currency) {
            $currency = Currency::create([
                'code' => 'AFN',
                'name' => 'Afghan Afghani',
                'symbol' => '؋',
                'exchange_rate' => 0.0125, // 1 AFN = 0.0125 USD (80 AFN = 1 USD)
            ]);
        }

        // 4. Send POST request to store a payment with custom exchange rate
        // We will pass exchange_rate = 0.013 (different from default 0.0125)
        $customRate = 0.01300000;
        $amount = 10000.00; // 10,000 AFN

        $response = $this->actingAs($user)->post('/dashboard/customer-payments', [
            'customer_id' => $customer->id,
            'amount' => $amount,
            'currency_id' => $currency->id,
            'type' => 'رسید',
            'invoice_number' => 'نقد',
            'date' => date('Y-m-d'),
            'description' => 'Test Forensic Payment with Custom Rate',
            'exchange_rate' => $customRate,
        ]);

        // Assert redirect (success)
        $response->assertStatus(302);

        // 5. Assert database records were created with forensic exchange rates
        $payment = CustomerPayment::where('description', 'Test Forensic Payment with Custom Rate')->first();
        $this->assertNotNull($payment);
        $this->assertEquals($currency->id, $payment->currency_id);
        $this->assertEquals('AFN', $payment->currency_code);
        $this->assertEquals($customRate, $payment->exchange_rate);
        $this->assertEquals($amount, $payment->original_amount);

        // Base amount: 10,000 * 0.013 = 130.00 USD
        $expectedBaseAmount = bcmul((string) $amount, (string) $customRate, 4);
        $this->assertEquals($expectedBaseAmount, $payment->base_amount);

        // 6. Assert Ledger entries were correctly created with custom rate
        $this->assertNotNull($payment->ledger_transaction_id);
        $transaction = LedgerTransaction::with('entries')->find($payment->ledger_transaction_id);
        $this->assertNotNull($transaction);

        $this->assertEquals(2, $transaction->entries->count());

        foreach ($transaction->entries as $entry) {
            $this->assertEquals('AFN', $entry->currency_code);
            $this->assertEquals($customRate, $entry->exchange_rate);

            // Expected base_currency_amount: 130.0000
            // Since AccountingService rounds bcmul to 4 decimal places:
            $expectedBaseEntryAmt = round((float) bcmul((string) $amount, (string) $customRate, 12), 4);
            $this->assertEquals($expectedBaseEntryAmt, $entry->base_currency_amount);
        }
    }
}
