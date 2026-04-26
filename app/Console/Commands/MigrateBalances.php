<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\CustomerPayment;
use App\Sale;
use App\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class MigrateBalances extends Command
{
    protected $signature = 'finance:migrate-balances';
    protected $description = 'Migrate historical balances into the new accounting engine';

    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
    }

    public function handle()
    {
        $this->info("Starting balance migration...");

        $arAccount = ChartOfAccount::where('account_code', '1300')->first();
        $openingBalanceEquity = ChartOfAccount::where('account_code', '3900')->first();

        if (!$arAccount || !$openingBalanceEquity) {
            $this->error("Required accounts (1300 or 3900) not found in Chart of Accounts.");
            return;
        }

        // Migrate Customer Balances
        $customers = Customer::all();
        foreach ($customers as $customer) {
            // Calculate old balance: (Total Sales) - (Total Received) + (Total Withdrawals)
            $totalSales = Sale::where('customer_id', $customer->id)->sum('sale_cost_total');
            $totalReceived = CustomerPayment::where('customer_id', $customer->id)->where('type', 'رسید')->where('status', 1)->sum('amount');
            $totalWithdrawals = CustomerPayment::where('customer_id', $customer->id)->where('type', 'گرفت')->where('status', 1)->sum('amount');
            
            $balance = $totalSales - $totalReceived + $totalWithdrawals;

            if ($balance != 0) {
                $this->info("Migrating balance for Customer: {$customer->name} | Balance: {$balance}");
                
                try {
                    $this->accountingService->postTransaction([
                        'date' => date('Y-m-d'),
                        'reference' => 'OPENING-CUST-' . $customer->id,
                        'description' => "Opening Balance Migration for Customer: " . $customer->name,
                        'source_type' => 'Customer',
                        'source_id' => $customer->id,
                        'journal_type' => 'journal',
                        'entries' => [
                            ['account_id' => $arAccount->id, 'debit' => $balance > 0 ? abs($balance) : 0, 'credit' => $balance < 0 ? abs($balance) : 0, 'party_type' => 'App\Customer', 'party_id' => $customer->id],
                            ['account_id' => $openingBalanceEquity->id, 'debit' => $balance < 0 ? abs($balance) : 0, 'credit' => $balance > 0 ? abs($balance) : 0],
                        ]
                    ]);
                } catch (\Exception $e) {
                    $this->error("Failed to migrate balance for Customer {$customer->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Migration completed.");
    }
}
