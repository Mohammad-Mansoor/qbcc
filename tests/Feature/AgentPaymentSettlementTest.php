<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Agents;
use App\PurchaseInvoice;
use App\Invoice;
use App\AgentPayment;
use App\AgentPaymentAllocation;
use App\Currency;

class AgentPaymentSettlementTest extends TestCase
{
    use DatabaseTransactions;

    public function testStorePaymentWithPolymorphicAllocation()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        // 1. Authenticate as SP user
        $user = User::where('role', 'SP')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'last_name' => 'SP',
                'role' => 'SP',
                'email' => 'test_sp_agent_payment@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 2. Setup agent
        $agent = Agents::first();
        if (!$agent) {
            $agentUser = User::create([
                'name' => 'Test Agent Settlement',
                'last_name' => 'Agent',
                'role' => 'Agent',
                'email' => 'agent_settlement@example.com',
                'password' => bcrypt('password'),
            ]);
            $province = \DB::table('provinces')->first();
            $provinceId = $province ? $province->id : 1;
            
            $agent = Agents::create([
                'agent_id' => 9991,
                'user_id' => $agentUser->id,
                'agent_father_name' => 'Father',
                'agent_address' => 'Test Address',
                'account_type' => 'personal',
                'contract_type' => 'carpet seller',
                'contract_date' => date('Y-m-d'),
                'image' => 'dummy.png',
                'account_no' => 'AG-9991',
                'province_id' => $provinceId,
            ]);
        }

        $currency = Currency::where('code', 'USD')->first() ?: Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0,
        ]);

        // 3. Create a mock PurchaseInvoice (bill)
        $bill = PurchaseInvoice::create([
            'agent_id' => $agent->agent_id,
            'invoice_number' => 'BILL-TEST-001',
            'date' => date('Y-m-d'),
            'status' => 'open',
            'payment_status' => 'unpaid',
        ]);

        // Create a carpet linked to this bill
        $type = \App\CarpetType::first() ?: \App\CarpetType::create(['carpet_type' => 'Test Type']);
        $quality = \App\Quality::first() ?: \App\Quality::create(['quality' => 'Test Quality']);
        $warehouse = \App\Warehouse::first() ?: \App\Warehouse::create(['name' => 'Test Warehouse']);

        $carpet = \App\Carpet::create([
            'parcha_number' => 'CARPET-TEST-001',
            'carpet_no' => 'CARPET-TEST-001',
            'agent_id' => $agent->agent_id,
            'type_id' => $type->carpet_type_id,
            'quality_id' => $quality->id,
            'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id,
            'currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'width' => 2.0,
            'height' => 3.0,
            'area' => 6.0,
            'price' => 50.0,
            'original_price' => 50.0,
            'total_price' => 300.0,
            'total_price_af' => 300.0,
            'date' => date('Y-m-d'),
            'status' => 0,
            'purchase_invoice_id' => $bill->id,
        ]);

        // Refresh bill to load carpets and calculate total_amount attribute
        $bill->refresh();
        $this->assertEquals(300.0, floatval($bill->total_amount));
        $this->assertEquals(300.0, floatval($bill->remaining_balance));

        // 4. Pay $200 (partial payment)
        $response = $this->actingAs($user)->post('/dashboard/agent-payments', [
            'agent_id' => $agent->agent_id,
            'amount' => 200.0,
            'currency_id' => $currency->id,
            'exchange_rate' => 1.0,
            'type' => 'گرفت', // Payment Sent (reduces outstanding liability)
            'check_number' => 'نقد',
            'date' => date('Y-m-d'),
            'description' => 'Partial payment to bill',
            'allocatable_id' => $bill->id,
            'allocatable_type' => 'App\PurchaseInvoice',
        ]);

        $response->assertStatus(302);
        
        $bill->refresh();
        $this->assertEquals(200.0, floatval($bill->paid_amount));
        $this->assertEquals(100.0, floatval($bill->remaining_balance));
        $this->assertEquals('partially_paid', $bill->payment_status);

        // Check allocation record
        $allocation = AgentPaymentAllocation::where('allocatable_id', $bill->id)
            ->where('allocatable_type', 'App\PurchaseInvoice')
            ->first();
        
        $this->assertNotNull($allocation);
        $this->assertEquals(200.0, floatval($allocation->allocated_amount));

        // 5. Try to pay more than remaining ($150) -> should fail validation (overpayment)
        $failResponse = $this->actingAs($user)->post('/dashboard/agent-payments', [
            'agent_id' => $agent->agent_id,
            'amount' => 150.0,
            'currency_id' => $currency->id,
            'exchange_rate' => 1.0,
            'type' => 'گرفت',
            'check_number' => 'نقد',
            'date' => date('Y-m-d'),
            'description' => 'Overpaying payment',
            'allocatable_id' => $bill->id,
            'allocatable_type' => 'App\PurchaseInvoice',
        ]);

        $failResponse->assertSessionHasErrors('amount');
        
        // Assert bill payment status is still partially paid and paid_amount is 200
        $bill->refresh();
        $this->assertEquals(200.0, floatval($bill->paid_amount));

        // 6. Pay exactly remaining ($100) -> should succeed and set status to paid
        $successResponse = $this->actingAs($user)->post('/dashboard/agent-payments', [
            'agent_id' => $agent->agent_id,
            'amount' => 100.0,
            'currency_id' => $currency->id,
            'exchange_rate' => 1.0,
            'type' => 'گرفت',
            'check_number' => 'نقد',
            'date' => date('Y-m-d'),
            'description' => 'Final payment to bill',
            'allocatable_id' => $bill->id,
            'allocatable_type' => 'App\PurchaseInvoice',
        ]);

        $successResponse->assertStatus(302);
        $bill->refresh();
        $this->assertEquals(300.0, floatval($bill->paid_amount));
        $this->assertEquals(0.0, floatval($bill->remaining_balance));
        $this->assertEquals('paid', $bill->payment_status);

        // 7. Delete the first payment ($200) -> remaining balance should increase to $200 and status back to partially_paid
        $firstPayment = AgentPayment::where('description', 'Partial payment to bill')->first();
        $this->assertNotNull($firstPayment);

        $deleteResponse = $this->actingAs($user)->delete("/dashboard/agent-payments/{$firstPayment->id}");
        $deleteResponse->assertStatus(200); // Ajax response returns JSON success status

        $bill->refresh();
        $this->assertEquals(100.0, floatval($bill->paid_amount));
        $this->assertEquals(200.0, floatval($bill->remaining_balance));
        $this->assertEquals('partially_paid', $bill->payment_status);
    }
}
