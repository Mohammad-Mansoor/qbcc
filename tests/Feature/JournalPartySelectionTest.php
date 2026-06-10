<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Customer;
use App\Agents;
use App\ChartOfAccount;
use App\LedgerTransaction;
use App\LedgerEntry;
use App\Currency;

class JournalPartySelectionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testPartySearchApiEndpoint()
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

        // 2. Create Customer for search validation if none exists
        $customer = Customer::first();
        if (!$customer) {
            Customer::create([
                'customer_code' => 'CUST-T01',
                'name' => 'Test Customer',
                'company_name' => 'Test Corp',
                'company_address' => 'Test Address',
                'phone' => '123456789',
                'type' => 'مشتری قالین',
            ]);
        }

        // Test API for Customers
        $response = $this->actingAs($user)->get('/dashboard/accounting/journals/api/parties?type=App\Customer');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        if (count($data) > 0) {
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('text', $data[0]);
        }

        // Test API for Agents
        $response = $this->actingAs($user)->get('/dashboard/accounting/journals/api/parties?type=App\Agents');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function testJournalVoucherWithPartyIntegration()
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

        // 2. Setup minimum 2 distinct accounts
        $accounts = ChartOfAccount::limit(2)->get();
        if ($accounts->count() < 2) {
            $accounts = collect([
                ChartOfAccount::create([
                    'account_code' => '1000-TEST',
                    'account_name' => 'Test Asset Account',
                    'account_type' => 'Asset',
                ]),
                ChartOfAccount::create([
                    'account_code' => '5000-TEST',
                    'account_name' => 'Test Expense Account',
                    'account_type' => 'Expense',
                ])
            ]);
        }

        $customer = Customer::first();
        if (!$customer) {
            $customer = Customer::create([
                'customer_code' => 'CUST-T01',
                'name' => 'Test Customer',
                'company_name' => 'Test Corp',
                'company_address' => 'Test Address',
                'phone' => '123456789',
                'type' => 'مشتری قالین',
            ]);
        }

        // Ensure USD currency exists
        $usd = Currency::where('code', 'USD')->first();
        if (!$usd) {
            Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0,
            ]);
        }

        // 3. Post a journal entry with customer party attached at the header level
        $payload = [
            'date' => date('Y-m-d'),
            'reference' => 'JV-UNIT-TEST',
            'journal_type' => 'journal',
            'party_type' => 'App\Customer',
            'party_id' => $customer->id,
            'description' => 'Unit testing polymorphic party selection',
            'entries' => [
                [
                    'account_id' => $accounts[0]->id,
                    'debit' => 125.50,
                    'credit' => 0.00,
                    'currency_code' => 'USD',
                ],
                [
                    'account_id' => $accounts[1]->id,
                    'debit' => 0.00,
                    'credit' => 125.50,
                    'currency_code' => 'USD',
                ]
            ]
        ];

        $response = $this->actingAs($user)->post('/dashboard/accounting/journals', $payload);
        $response->assertStatus(302); // Redirect back to index on success

        // 4. Assert database transaction & entries
        $transaction = LedgerTransaction::where('reference', 'JV-UNIT-TEST')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('posted', $transaction->status);

        $entries = LedgerEntry::where('transaction_id', $transaction->id)->get();
        $this->assertEquals(2, $entries->count());

        // Assert that all entry lines correctly inherit the header-level party
        foreach ($entries as $entry) {
            $this->assertEquals('App\Customer', $entry->party_type);
            $this->assertEquals($customer->id, $entry->party_id);
        }
    }

    public function testJournalVoucherWithoutParty()
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

        // 2. Setup minimum 2 distinct accounts
        $accounts = ChartOfAccount::limit(2)->get();
        if ($accounts->count() < 2) {
            $accounts = collect([
                ChartOfAccount::create([
                    'account_code' => '1000-TEST',
                    'account_name' => 'Test Asset Account',
                    'account_type' => 'Asset',
                ]),
                ChartOfAccount::create([
                    'account_code' => '5000-TEST',
                    'account_name' => 'Test Expense Account',
                    'account_type' => 'Expense',
                ])
            ]);
        }

        // Ensure USD currency exists
        $usd = Currency::where('code', 'USD')->first();
        if (!$usd) {
            Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0,
            ]);
        }

        // 3. Post a journal entry with NO party attached
        $payload = [
            'date' => date('Y-m-d'),
            'reference' => 'JV-NOPARTY-TEST',
            'journal_type' => 'journal',
            'party_type' => null,
            'party_id' => null,
            'description' => 'Should succeed without party details',
            'entries' => [
                [
                    'account_id' => $accounts[0]->id,
                    'debit' => 100.00,
                    'credit' => 0.00,
                    'currency_code' => 'USD',
                ],
                [
                    'account_id' => $accounts[1]->id,
                    'debit' => 0.00,
                    'credit' => 100.00,
                    'currency_code' => 'USD',
                ]
            ]
        ];

        $response = $this->actingAs($user)->post('/dashboard/accounting/journals', $payload);
        $response->assertStatus(302); // Redirect on success

        // Assert entries are saved without party type and ID
        $transaction = LedgerTransaction::where('reference', 'JV-NOPARTY-TEST')->first();
        $this->assertNotNull($transaction);

        $entries = LedgerEntry::where('transaction_id', $transaction->id)->get();
        $this->assertEquals(2, $entries->count());

        foreach ($entries as $entry) {
            $this->assertNull($entry->party_type);
            $this->assertNull($entry->party_id);
        }
    }

    public function testJournalVoucherFilteringByParty()
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

        // 2. Setup minimum 2 distinct accounts
        $accounts = ChartOfAccount::limit(2)->get();
        if ($accounts->count() < 2) {
            $accounts = collect([
                ChartOfAccount::create([
                    'account_code' => '1000-TEST',
                    'account_name' => 'Test Asset Account',
                    'account_type' => 'Asset',
                ]),
                ChartOfAccount::create([
                    'account_code' => '5000-TEST',
                    'account_name' => 'Test Expense Account',
                    'account_type' => 'Expense',
                ])
            ]);
        }

        $customer = Customer::first();
        if (!$customer) {
            $customer = Customer::create([
                'customer_code' => 'CUST-T01',
                'name' => 'Test Customer',
                'company_name' => 'Test Corp',
                'company_address' => 'Test Address',
                'phone' => '123456789',
                'type' => 'مشتری قالین',
            ]);
        }

        // Ensure USD currency exists
        $usd = Currency::where('code', 'USD')->first();
        if (!$usd) {
            Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0,
            ]);
        }

        // Post a transaction with the customer
        $payload = [
            'date' => date('Y-m-d'),
            'reference' => 'JV-FILTER-TEST',
            'journal_type' => 'journal',
            'party_type' => 'App\Customer',
            'party_id' => $customer->id,
            'description' => 'Filtering unit test transaction',
            'entries' => [
                [
                    'account_id' => $accounts[0]->id,
                    'debit' => 500.00,
                    'credit' => 0.00,
                    'currency_code' => 'USD',
                ],
                [
                    'account_id' => $accounts[1]->id,
                    'debit' => 0.00,
                    'credit' => 500.00,
                    'currency_code' => 'USD',
                ]
            ]
        ];

        $response = $this->actingAs($user)->post('/dashboard/accounting/journals', $payload);
        $response->assertStatus(302);

        // Fetch journals page with correct customer filters
        $response = $this->actingAs($user)->get('/dashboard/accounting/journals?party_type=App%5CCustomer&party_id=' . $customer->id);
        $response->assertStatus(200);
        $response->assertSee('JV-FILTER-TEST');

        // Fetch journals page with different party filters
        $response = $this->actingAs($user)->get('/dashboard/accounting/journals?party_type=App%5CAgents&party_id=9999');
        $response->assertStatus(200);
        $response->assertDontSee('JV-FILTER-TEST');
    }
}
