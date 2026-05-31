<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Agents;
use App\Carpet;
use App\CarpetType;
use App\Quality;
use App\Warehouse;
use App\Currency;
use App\ChartOfAccount;
use App\LedgerTransaction;
use App\LedgerEntry;
use App\MappingRule;

class AgentCarpetOverrideTest extends TestCase
{
    use DatabaseTransactions;

    public function testAgentCarpetCreationAndEditPopulationWithAccountOverrides()
    {
        // 1. Authenticate as SP user
        $user = User::where('role', 'SP')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'last_name' => 'SP',
                'role' => 'SP',
                'email' => 'test_sp_agent@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 2. Setup master data
        $agent = Agents::first();
        if (!$agent) {
            $agentUser = User::create([
                'name' => 'Test Agent User',
                'role' => 'Agent',
                'email' => 'agent@example.com',
                'password' => bcrypt('password'),
            ]);
            $agent = Agents::create([
                'agent_id' => 9999,
                'user_id' => $agentUser->id,
                'account_no' => 'AG-9999',
                'contract_type' => 'carpet seller', // Will trigger processPurchase
            ]);
        }

        $type = CarpetType::first() ?: CarpetType::create(['carpet_type' => 'Test Type']);
        $quality = Quality::first() ?: Quality::create(['quality' => 'Test Quality']);
        $warehouse = Warehouse::first() ?: Warehouse::create(['name' => 'Test Warehouse']);
        
        $currency = Currency::where('code', 'USD')->first();
        if (!$currency) {
            $currency = Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0,
            ]);
        }

        // 3. Find custom debit/credit override accounts
        $selectionService = new \App\Services\AccountSelectionService();
        $debitAccounts = $selectionService->getValidAccounts('MATERIAL_PURCHASE_CREDIT', 'debit');
        $creditAccounts = $selectionService->getValidAccounts('MATERIAL_PURCHASE_CREDIT', 'credit');

        $this->assertNotEmpty($debitAccounts, "Debit accounts list should not be empty");
        $this->assertNotEmpty($creditAccounts, "Credit accounts list should not be empty");

        // Pick non-default accounts if possible
        $rule = MappingRule::where('transaction_type', 'material_purchase')->first();
        $defaultDebitId = $rule ? $rule->debit_account_id : null;
        $defaultCreditId = $rule ? $rule->credit_account_id : null;

        $selectedDebit = $debitAccounts->first(function($acc) use ($defaultDebitId) {
            return $acc->id != $defaultDebitId;
        }) ?? $debitAccounts->first();

        $selectedCredit = $creditAccounts->first(function($acc) use ($defaultCreditId) {
            return $acc->id != $defaultCreditId;
        }) ?? $creditAccounts->first();

        $this->assertNotNull($selectedDebit);
        $this->assertNotNull($selectedCredit);

        // 4. Send POST request to store carpet with overrides
        $parchaNo = 'TST-' . time();
        $response = $this->actingAs($user)->post('/dashboard/contract-carpet', [
            'parcha_number' => $parchaNo,
            'carpet_no' => $parchaNo,
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
            'price_input' => 15.5,
            'price' => 15.5,
            'total_price' => 93.0,
            'total_price_af' => 93.0,
            'date' => date('Y-m-d'),
            'status' => 0,
            'override_inventory_account_id' => $selectedDebit->id,
            'override_credit_account_id' => $selectedCredit->id,
        ]);

        $response->assertStatus(302);

        // 5. Assert carpet was created with override accounts
        $carpet = Carpet::where('parcha_number', $parchaNo)->first();
        $this->assertNotNull($carpet);
        $this->assertEquals($selectedDebit->id, $carpet->override_inventory_account_id);
        $this->assertEquals($selectedCredit->id, $carpet->override_credit_account_id);
        $this->assertEquals(15.5, $carpet->original_price);
        $this->assertEquals($currency->id, $carpet->currency_id);

        // 6. Hit the edit route for this carpet and verify field population
        $editResponse = $this->actingAs($user)->get("/dashboard/agent-carpet/{$carpet->carpet_id}/edit");
        $editResponse->assertStatus(200);

        // Assert values are populated in input fields
        $editResponse->assertSee('value="' . $parchaNo . '"');
        $editResponse->assertSee('value="15.5"');
        $editResponse->assertSee('value="' . $selectedDebit->id . '" selected');
        $editResponse->assertSee('value="' . $selectedCredit->id . '" selected');
        $editResponse->assertSee('data-code="USD" data-rate="1.00000000" selected');
    }
}
