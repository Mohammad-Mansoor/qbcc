<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\CustomerPayment;
use App\Sale;
use App\StringSeller;
use App\PurchaseMaterial;
use App\SellerPayment;
use App\WashingTeam;
use App\CarpetWash;
use App\WashingPayment;
use App\FinishingTeam;
use App\FinishingWork;
use App\FinishingTeamPayment;
use App\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class MigrateBalances extends Command
{
    protected $signature = 'finance:migrate-balances';
    protected $description = 'Migrate historical balances for Customers, Sellers, and Teams into the new accounting engine';

    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
    }

    public function handle()
    {
        $this->info("🚀 Starting Comprehensive Balance Migration...");

        // Offset account (Opening Balance Equity)
        $equityAccount = ChartOfAccount::where('account_code', '3900')->first();
        if (!$equityAccount) {
            $this->error("Opening Balance Equity (3900) not found.");
            return;
        }

        DB::transaction(function () use ($equityAccount) {
            
            // 1. Customers (Accounts Receivable)
            $this->migrateCustomers($equityAccount);

            // 2. Sellers (Accounts Payable)
            $this->migrateSellers($equityAccount);

            // 3. Washing Teams (Accrued Expenses)
            $this->migrateWashingTeams($equityAccount);

            // 4. Finishing Teams (Accrued Expenses)
            $this->migrateFinishingTeams($equityAccount);

        });

        $this->info("✅ Migration completed successfully!");
    }

    private function migrateCustomers($equityAccount)
    {
        $this->info("--- Migrating Customers ---");
        $arAccount = ChartOfAccount::where('account_code', '1300')->first();
        if (!$arAccount) return;

        $customers = Customer::all();
        foreach ($customers as $customer) {
            $totalSales = Sale::where('customer_id', $customer->id)->sum('sale_cost_total');
            $totalReceived = CustomerPayment::where('customer_id', $customer->id)->where('type', 'رسید')->where('status', 1)->sum('amount');
            $totalWithdrawals = CustomerPayment::where('customer_id', $customer->id)->where('type', 'گرفت')->where('status', 1)->sum('amount');
            
            $balance = $totalSales - $totalReceived + $totalWithdrawals;

            if ($balance != 0) {
                $this->postOpening('Customer', $customer->id, 'App\Customer', $customer->name, $arAccount->id, $balance, $equityAccount->id);
            }
        }
    }

    private function migrateSellers($equityAccount)
    {
        $this->info("--- Migrating Sellers ---");
        $apAccount = ChartOfAccount::where('account_code', '2100')->first();
        if (!$apAccount) return;

        $sellers = StringSeller::all();
        foreach ($sellers as $seller) {
            // Note: Using total_af for local currency ledger
            $totalPurchases = PurchaseMaterial::where('seller_id', $seller->id)->where('status', 1)->sum('total_af');
            $totalPayments = SellerPayment::where('seller_id', $seller->id)->where('status', 1)->sum('amount');
            
            $balance = $totalPurchases - $totalPayments;

            if ($balance != 0) {
                // Liability: Positive balance means we owe them (Credit)
                $this->postOpening('Seller', $seller->id, 'App\StringSeller', $seller->name, $apAccount->id, -$balance, $equityAccount->id);
            }
        }
    }

    private function migrateWashingTeams($equityAccount)
    {
        $this->info("--- Migrating Washing Teams ---");
        $apAccount = ChartOfAccount::where('account_code', '2100')->first();
        if (!$apAccount) return;

        $teams = WashingTeam::all();
        foreach ($teams as $team) {
            $totalWork = CarpetWash::where('team_id', $team->id)->sum('af_total_price');
            $totalPayments = WashingPayment::where('team_id', $team->id)->where('status', 1)->sum('amount');
            
            $balance = $totalWork - $totalPayments;

            if ($balance != 0) {
                $this->postOpening('WashingTeam', $team->id, 'App\WashingTeam', $team->name, $apAccount->id, -$balance, $equityAccount->id);
            }
        }
    }

    private function migrateFinishingTeams($equityAccount)
    {
        $this->info("--- Migrating Finishing Teams ---");
        $apAccount = ChartOfAccount::where('account_code', '2100')->first();
        if (!$apAccount) return;

        $teams = FinishingTeam::all();
        foreach ($teams as $team) {
            $totalWork = FinishingWork::where('team_id', $team->id)->where('status', 1)->sum('price_af');
            $totalPayments = FinishingTeamPayment::where('team_id', $team->id)->where('status', 1)->sum('amount');
            
            $balance = $totalWork - $totalPayments;

            if ($balance != 0) {
                $this->postOpening('FinishingTeam', $team->id, 'App\FinishingTeam', $team->name, $apAccount->id, -$balance, $equityAccount->id);
            }
        }
    }

    private function postOpening($type, $id, $model, $name, $mainAccountId, $balance, $equityAccountId)
    {
        $this->info("Seeding {$type}: {$name} | Balance: " . number_format($balance, 2));
        
        $debit = $balance > 0 ? abs($balance) : 0;
        $credit = $balance < 0 ? abs($balance) : 0;

        try {
            $this->accountingService->postTransaction([
                'date' => date('Y-m-d'),
                'reference' => "OPENING-{$type}-{$id}",
                'description' => "Opening Balance Migration for {$type}: {$name}",
                'source_type' => $type,
                'source_id' => $id,
                'journal_type' => 'journal',
                'entries' => [
                    ['account_id' => $mainAccountId, 'debit' => $debit, 'credit' => $credit, 'party_type' => $model, 'party_id' => $id],
                    ['account_id' => $equityAccountId, 'debit' => $credit, 'credit' => $debit],
                ]
            ]);
        } catch (\Exception $e) {
            $this->error("Failed to migrate {$type} {$id}: " . $e->getMessage());
        }
    }
}
