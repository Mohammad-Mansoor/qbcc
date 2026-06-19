<?php

use Illuminate\Database\Seeder;
use App\ChartOfAccount;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            // ASSETS (1000)
            ['account_code' => '1100', 'account_name' => 'Cash', 'account_type' => 'Asset', 'report_group' => 'Current Asset', 'normal_balance' => 'debit', 'is_cash_account' => true],
            ['account_code' => '1200', 'account_name' => 'Bank', 'account_type' => 'Asset', 'report_group' => 'Current Asset', 'normal_balance' => 'debit', 'is_cash_account' => true],
            ['account_code' => '1300', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset', 'report_group' => 'Current Asset', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '1350', 'account_name' => 'Advances to Suppliers', 'account_type' => 'Asset', 'report_group' => 'Current Asset', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '1400', 'account_name' => 'Inventory', 'account_type' => 'Asset', 'report_group' => 'Current Asset', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '1500', 'account_name' => 'Fixed Assets', 'account_type' => 'Asset', 'report_group' => 'Fixed Asset', 'normal_balance' => 'debit', 'is_cash_account' => false],

            // LIABILITIES (2000)
            ['account_code' => '2100', 'account_name' => 'Accounts Payable', 'account_type' => 'Liability', 'report_group' => 'Current Liability', 'normal_balance' => 'credit', 'is_cash_account' => false],
            ['account_code' => '2200', 'account_name' => 'Accrued Expenses', 'account_type' => 'Liability', 'report_group' => 'Current Liability', 'normal_balance' => 'credit', 'is_cash_account' => false],
            ['account_code' => '2300', 'account_name' => 'Salary Payable', 'account_type' => 'Liability', 'report_group' => 'Current Liability', 'normal_balance' => 'credit', 'is_cash_account' => false],

            // EQUITY (3000)
            ['account_code' => '3100', 'account_name' => 'Capital', 'account_type' => 'Equity', 'report_group' => 'Equity', 'normal_balance' => 'credit', 'is_cash_account' => false],
            ['account_code' => '3200', 'account_name' => 'Retained Earnings', 'account_type' => 'Equity', 'report_group' => 'Equity', 'normal_balance' => 'credit', 'is_cash_account' => false],
            ['account_code' => '3900', 'account_name' => 'Opening Balance Equity', 'account_type' => 'Equity', 'report_group' => 'Equity', 'normal_balance' => 'credit', 'is_cash_account' => false],

            // REVENUE (4000)
            ['account_code' => '4100', 'account_name' => 'Sales Revenue', 'account_type' => 'Revenue', 'report_group' => 'Revenue', 'normal_balance' => 'credit', 'is_cash_account' => false],
            ['account_code' => '4200', 'account_name' => 'Service Revenue', 'account_type' => 'Revenue', 'report_group' => 'Revenue', 'normal_balance' => 'credit', 'is_cash_account' => false],

            // COST OF GOODS SOLD (5000)
            ['account_code' => '5100', 'account_name' => 'Cost of Goods Sold', 'account_type' => 'Expense', 'report_group' => 'COGS', 'normal_balance' => 'debit', 'is_cash_account' => false],

            // EXPENSES (6000)
            ['account_code' => '6100', 'account_name' => 'Rent Expense', 'account_type' => 'Expense', 'report_group' => 'Operating Expense', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '6200', 'account_name' => 'Salary Expense', 'account_type' => 'Expense', 'report_group' => 'Operating Expense', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '6300', 'account_name' => 'Utilities', 'account_type' => 'Expense', 'report_group' => 'Operating Expense', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '6400', 'account_name' => 'Transportation', 'account_type' => 'Expense', 'report_group' => 'Operating Expense', 'normal_balance' => 'debit', 'is_cash_account' => false],
            ['account_code' => '6500', 'account_name' => 'Other Expenses', 'account_type' => 'Expense', 'report_group' => 'Operating Expense', 'normal_balance' => 'debit', 'is_cash_account' => false],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(
                ['account_code' => $account['account_code']],
                $account
            );
        }
    }
}
