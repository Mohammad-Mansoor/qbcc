<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Customer;
use App\StringSeller;
use App\WashingTeam;
use App\FinishingTeam;
use App\Agents;
use App\Invoice;
use App\Sale;
use App\Carpet;
use App\CarpetWash;
use App\FinishingWork;
use App\PurchaseMaterial;
use App\ChartOfAccount;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateTestScenario extends Command
{
    protected $signature = 'finance:test-scenario';
    protected $description = 'Seed the database with a complete business scenario for testing';

    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
    }

    public function handle()
    {
        $this->info("🚀 Starting Business Test Scenario Seeding...");

        try {
            DB::transaction(function () {
                $this->seedDependencies();
                $this->setupAccounts();
                $this->setupMappingRules();
                $parties = $this->createParties();
                $this->injectCapital();
                $this->simulatePurchase($parties['seller']);
                $this->simulateProduction($parties['washing_team'], $parties['finishing_team'], $parties['agent']);
                $this->simulateSales($parties['customer']);
                $this->simulatePaymentsAndExpenses($parties);
            });
            $this->info("✅ Business scenario seeded successfully!");
        } catch (\Exception $e) {
            $this->error("❌ Error seeding scenario: " . $e->getMessage());
        }
    }

    private function seedDependencies()
    {
        $this->info("Seeding dependencies...");
        DB::table('provinces')->updateOrInsert(['province_id' => 1], ['province' => 'Kabul']);
        DB::table('material_types')->updateOrInsert(['material_type_id' => 1], ['material_type' => 'Wool', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('material_categories')->updateOrInsert(['material_category_id' => 1], ['material_category' => 'Grade A', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('carpet_types')->updateOrInsert(['carpet_type_id' => 1], ['carpet_type' => 'Handmade Silk', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('finishing_team_categories')->updateOrInsert(['id' => 1], ['category' => 'Qaitan', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function setupAccounts()
    {
        $this->info("Setting up COA...");
        $accounts = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset', 'balance' => 'debit'],
            ['code' => '1300', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'balance' => 'debit'],
            ['code' => '2100', 'name' => 'Accounts Payable', 'type' => 'Liability', 'balance' => 'credit'],
            ['code' => '3000', 'name' => 'Owner Equity', 'type' => 'Equity', 'balance' => 'credit'],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Revenue', 'balance' => 'credit'],
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Expense', 'balance' => 'debit'],
            ['code' => '6000', 'name' => 'Operational Expenses', 'type' => 'Expense', 'balance' => 'debit'],
        ];
        foreach ($accounts as $acc) {
            ChartOfAccount::updateOrCreate(
                ['account_code' => $acc['code']], 
                [
                    'account_name' => $acc['name'], 
                    'account_type' => $acc['type'], 
                    'normal_balance' => $acc['balance'],
                    'status' => 1
                ]
            );
        }
    }

    private function setupMappingRules()
    {
        $this->info("Setting up mapping rules...");
        $acc = ChartOfAccount::all()->pluck('id', 'account_code')->toArray();
        $rules = [
            ['type' => 'material_purchase', 'cond' => 'credit', 'db' => '5000', 'cr' => '2100'],
            ['type' => 'washing', 'cond' => 'credit', 'db' => '5000', 'cr' => '2100'],
            ['type' => 'finishing', 'cond' => 'credit', 'db' => '5000', 'cr' => '2100'],
            ['type' => 'sale', 'cond' => 'credit', 'db' => '1300', 'cr' => '4000'],
            ['type' => 'customer_payment', 'cond' => 'رسید', 'db' => '1000', 'cr' => '1300'],
            ['type' => 'washing_payment', 'cond' => 'credit', 'db' => '2100', 'cr' => '1000'],
            ['type' => 'expense', 'cond' => 'کرایه و برق', 'db' => '6000', 'cr' => '1000'],
        ];
        foreach ($rules as $r) {
            DB::table('mapping_rules')->updateOrInsert(['transaction_type' => $r['type'], 'condition' => $r['cond']], ['debit_account_id' => $acc[$r['db']], 'credit_account_id' => $acc[$r['cr']], 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function createParties()
    {
        $this->info("Creating parties...");
        $customer = Customer::updateOrCreate(['customer_code' => 'C-TEST-001'], ['name' => 'Ahmad Khan', 'type' => 'Local', 'company_name' => 'Khan Carpets Ltd', 'company_address' => 'Kabul', 'phone' => '0700123456', 'email' => 'ahmad@test.com']);
        $seller = StringSeller::updateOrCreate(['name' => 'Al-Madina Wool Co.'], ['phone' => '0788112233', 'address' => 'Kabul, Afghanistan']);
        $wTeam = WashingTeam::updateOrCreate(['name' => 'Kabul Pro Washers'], ['last_name' => 'Team', 'contact_no' => '0799009900', 'address' => 'Kabul Industrial Area']);
        $fTeam = FinishingTeam::updateOrCreate(['name' => 'Herat Silk Finishers'], []);
        
        $user = User::firstOrCreate(['email' => 'agent@test.com'], ['name' => 'Test Agent', 'last_name' => 'Admin', 'role' => 'admin', 'password' => bcrypt('password')]);
        $agent = Agents::updateOrCreate(['account_no' => 'AG-TEST-001'], [
            'agent_father_name' => 'Father', 'agent_address' => 'Kabul', 'account_type' => 'Standard', 'contract_type' => 'Commission', 'contract_date' => now(), 'image' => 'default.png', 'user_id' => $user->id, 'province_id' => 1
        ]);

        return ['customer' => $customer, 'seller' => $seller, 'washing_team' => $wTeam, 'finishing_team' => $fTeam, 'agent' => $agent];
    }

    private function injectCapital()
    {
        $this->info("Injecting capital...");
        $cashAcc = ChartOfAccount::where('account_code', '1000')->first();
        $equityAcc = ChartOfAccount::where('account_code', '3000')->first();
        $this->accountingService->postTransaction(['date' => date('Y-m-d'), 'reference' => 'CAP-001', 'description' => 'Initial Capital Investment', 'journal_type' => 'journal', 'entries' => [['account_id' => $cashAcc->id, 'debit' => 5000000, 'credit' => 0], ['account_id' => $equityAcc->id, 'debit' => 0, 'credit' => 5000000]]]);
    }

    private function simulatePurchase($seller)
    {
        $this->info("Simulating purchase...");
        $purchase = PurchaseMaterial::create(['seller_id' => $seller->id, 'material_type' => 1, 'material_category' => 1, 'quantity' => 1000, 'price_per_kilo' => 200, 'total' => 200000, 'total_af' => 200000, 'purchase_date' => date('Y-m-d'), 'purchase_number' => 'PO-TEST-001', 'in_words' => 'Two Hundred Thousand']);
        $this->accountingService->postAutoTransaction('material_purchase', 'credit', ['date' => $purchase->purchase_date, 'amount' => $purchase->total_af, 'reference' => $purchase->purchase_number, 'description' => "Purchase from " . $seller->name, 'source_id' => $purchase->id, 'party_type' => 'App\StringSeller', 'party_id' => $seller->id]);
    }

    private function simulateProduction($wTeam, $fTeam, $agent)
    {
        $this->info("Simulating production...");
        $carpet = Carpet::create(['carpet_no' => 'CAR-TEST-' . rand(100, 999), 'width' => '3', 'height' => '4', 'area' => '12', 'status' => 5, 'agent_id' => $agent->agent_id, 'type_id' => 1]);
        $wash = CarpetWash::create(['carpetId' => $carpet->carpet_id, 'team_id' => $wTeam->id, 'wash_number' => 'WASH-TEST-' . rand(100, 999), 'area' => 12, 'price' => 100, 'af_total_price' => 1200, 'date' => date('Y-m-d')]);
        $this->accountingService->postAutoTransaction('washing', 'credit', ['date' => $wash->date, 'amount' => $wash->af_total_price, 'reference' => $wash->wash_number, 'description' => "Washing cost", 'source_id' => $wash->id, 'party_type' => 'App\WashingTeam', 'party_id' => $wTeam->id]);
        $finish = FinishingWork::create(['carpetId' => $carpet->carpet_id, 'team_id' => $fTeam->id, 'finish_number' => 'FIN-TEST-' . rand(100, 999), 'category_id' => 1, 'price' => 2000, 'price_af' => 2000, 'date' => date('Y-m-d'), 'description' => 'Test finish']);
        $this->accountingService->postAutoTransaction('finishing', 'credit', ['date' => $finish->date, 'amount' => $finish->price_af, 'reference' => $finish->finish_number, 'description' => "Finishing cost", 'source_id' => $finish->id, 'party_type' => 'App\FinishingTeam', 'party_id' => $fTeam->id]);
    }

    private function simulateSales($customer)
    {
        $this->info("Simulating sale...");
        $carpet = Carpet::orderBy('carpet_id', 'desc')->first();
        
        $invoice = Invoice::create([
            'invoice_no' => 'INV-TEST-001',
            'invoice_date' => date('Y-m-d'),
            'customer_id' => $customer->id,
            'invoice_description' => 'Test Invoice'
        ]);

        $sale = Sale::create(['customer_id' => $customer->id, 'carpet_id' => $carpet->carpet_id, 'sale_cost_total' => 50000, 'sale_cost_per_meter' => 50000/12, 'sale_date' => date('Y-m-d'), 'description' => 'Sale', 'quality' => 'Super', 'type' => 'Silk', 'profit' => 10000, 'invoice_id' => $invoice->id]);
        $this->accountingService->postAutoTransaction('sale', 'credit', ['date' => $sale->sale_date, 'amount' => $sale->sale_cost_total, 'reference' => 'SALE-TEST-' . $sale->id, 'description' => "Sale to " . $customer->name, 'source_id' => $sale->id, 'party_type' => 'App\Customer', 'party_id' => $customer->id]);
    }

    private function simulatePaymentsAndExpenses($parties)
    {
        $this->info("Simulating payments...");
        $this->accountingService->postAutoTransaction('customer_payment', 'رسید', ['date' => date('Y-m-d'), 'amount' => 25000, 'reference' => 'PAY-CUST-001', 'description' => "Payment from customer", 'party_type' => 'App\Customer', 'party_id' => $parties['customer']->id]);
        $this->accountingService->postAutoTransaction('washing_payment', 'credit', ['date' => date('Y-m-d'), 'amount' => 1200, 'reference' => 'PAY-WASH-001', 'description' => "Payment to washing team", 'party_type' => 'App\WashingTeam', 'party_id' => $parties['washing_team']->id]);
        $this->accountingService->postAutoTransaction('expense', 'کرایه و برق', ['date' => date('Y-m-d'), 'amount' => 5000, 'reference' => 'EXP-ELEC-001', 'description' => "Electricity Bill"]);
    }
}
