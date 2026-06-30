<?php

use Illuminate\Database\Seeder;
use App\MappingRule;
use App\ChartOfAccount;

class MappingRulesSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            // --- Sales ---
            [
                'transaction_type' => 'sale',
                'condition' => 'credit',
                'debit_code' => '1300', // Accounts Receivable
                'credit_code' => '4000', // Sales Revenue
                'template' => 'فروش قالین (Carpet Sale)'
            ],
            
            // --- Customer Payments ---
            [
                'transaction_type' => 'customer_payment',
                'condition' => 'رسید', 
                'debit_code' => '1000', // Cash
                'credit_code' => '1300', // Accounts Receivable
                'template' => 'رسید پول از مشتری (Customer Payment)'
            ],
            [
                'transaction_type' => 'customer_payment',
                'condition' => 'گرفت',
                'debit_code' => '1300', // Accounts Receivable
                'credit_code' => '1000', // Cash
                'template' => 'استرداد پول به مشتری (Refund/Withdrawal to Customer)'
            ],

            // --- Agent Payments ---
            [
                'transaction_type' => 'agent_payment',
                'condition' => 'رسید', 
                'debit_code' => '1000', // Cash
                'credit_code' => '1300', // Accounts Receivable
                'template' => 'رسید پول از نماینده (Agent Receipt)'
            ],
            [
                'transaction_type' => 'agent_payment',
                'condition' => 'گرفت',
                'debit_code' => '1300', // Accounts Receivable
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت پول به نماینده (Agent Payment)'
            ],

            // --- Material Purchases ---
            [
                'transaction_type' => 'material_purchase',
                'condition' => 'credit',
                'debit_code' => '5000', // COGS
                'credit_code' => '2100', // Accounts Payable
                'template' => 'خریداری مواد (Material Purchase)'
            ],

            // --- Production Costs ---
            [
                'transaction_type' => 'washing',
                'condition' => 'credit',
                'debit_code' => '5000', // COGS
                'credit_code' => '2100', // Accounts Payable
                'template' => 'هزینه شست (Washing Cost)'
            ],
            [
                'transaction_type' => 'finishing',
                'condition' => 'credit',
                'debit_code' => '5000', // COGS
                'credit_code' => '2100', // Accounts Payable
                'template' => 'هزینه تیاری (Finishing Cost)'
            ],

            // --- Supplier/Team Payments ---
            [
                'transaction_type' => 'seller_payment',
                'condition' => 'گرفت',
                'debit_code' => '2100', // Accounts Payable
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت پول به فروشنده (Seller Payment)'
            ],
            [
                'transaction_type' => 'washing_payment',
                'condition' => 'گرفت',
                'debit_code' => '2100', // Accounts Payable
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت پول به تیم شست (Washing Team Payment)'
            ],
            [
                'transaction_type' => 'finishing_payment',
                'condition' => 'گرفت',
                'debit_code' => '2100', // Accounts Payable
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت پول به تیم تیاری (Finishing Team Payment)'
            ],
            [
                'transaction_type' => 'kachaee_payment',
                'condition' => 'گرفت',
                'debit_code' => '2100', // Accounts Payable
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت پول به بخش کچایی (Kachaee Payment)'
            ],
            [
                'transaction_type' => 'kachaee_payment',
                'condition' => 'رسید',
                'debit_code' => '1000', // Cash
                'credit_code' => '2100', // Accounts Payable
                'template' => 'رسید پول از بخش کچایی (Kachaee Receipt)'
            ],

            // --- Expenses (Operational) ---
            [
                'transaction_type' => 'expense',
                'condition' => 'خوراکه',
                'debit_code' => '6000', // Operational Expenses
                'credit_code' => '1000', // Cash
                'template' => 'هزینه خوراکه (Food Expense)'
            ],
            [
                'transaction_type' => 'expense',
                'condition' => 'کرایه و برق',
                'debit_code' => '6000', 
                'credit_code' => '1000', 
                'template' => 'هزینه کرایه و برق (Rent/Electricity Expense)'
            ],
            [
                'transaction_type' => 'expense',
                'condition' => 'ترانسپورت',
                'debit_code' => '6000', 
                'credit_code' => '1000', 
                'template' => 'هزینه ترانسپورت (Transport Expense)'
            ],
            [
                'transaction_type' => 'expense',
                'condition' => 'متفرقه',
                'debit_code' => '6000', 
                'credit_code' => '1000', 
                'template' => 'هزینه های متفرقه (Misc Expense)'
            ],

            // --- Employee Salaries ---
            [
                'transaction_type' => 'employee_payment',
                'condition' => 'گرفت',
                'debit_code' => '6000', // Salary Expense (Direct hit)
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت معاش کارمند (Employee Salary Payment)'
            ],
            [
                'transaction_type' => 'employee_payment',
                'condition' => 'رسید',
                'debit_code' => '1000', // Cash
                'credit_code' => '6000', // Reverse Salary Expense
                'template' => 'رسید پول از کارمند (Refund from Employee)'
            ],

            // --- Office Cash (Credit/Debit) ---
            [
                'transaction_type' => 'office_credit',
                'condition' => 'deposit',
                'debit_code' => '1000', // Cash
                'credit_code' => '3000', // Equity (assuming direct investment)
                'template' => 'تزریق سرمایه به دخل (Capital/Cash Injection)'
            ],
            [
                'transaction_type' => 'office_debit',
                'condition' => 'withdrawal',
                'debit_code' => '6000', // Generic Expense
                'credit_code' => '1000', // Cash
                'template' => 'برداشت پول از دخل (Cash Withdrawal)'
            ],

            // --- Fixed Assets (Ajnas) ---
            [
                'transaction_type' => 'ajnas_account',
                'condition' => 'purchase',
                'debit_code' => '1500', // Fixed Assets
                'credit_code' => '1000', // Cash
                'template' => 'خریداری اجناس و جایداد (Fixed Asset Purchase)'
            ],

            // --- Different Accounts (Miscellaneous) ---
            [
                'transaction_type' => 'different_account',
                'condition' => 'رسید',
                'debit_code' => '1000', // Cash
                'credit_code' => '2100', // Accounts Payable
                'template' => 'رسید متفرقه (Miscellaneous Receipt)'
            ],
            [
                'transaction_type' => 'different_account',
                'condition' => 'گرفت',
                'debit_code' => '2100', // Accounts Payable
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت متفرقه (Miscellaneous Payment)'
            ],
        ];

        $resolveAccount = function($code) {
            $exact = ChartOfAccount::where('account_code', $code)->first();
            if ($exact) return $exact;

            // Mappings from old COA schema to new COA schema
            $fallbacks = [
                '1000' => '1100', // Cash
                '4000' => '4100', // Sales Revenue
                '5000' => '5100', // COGS
                '6000' => '6500', // Other/Operational Expenses
                '3000' => '3100', // Capital
            ];

            if (isset($fallbacks[$code])) {
                $fallbackAcc = ChartOfAccount::where('account_code', $fallbacks[$code])->first();
                if ($fallbackAcc) return $fallbackAcc;
            }

            // Fallback for expense categories starting with 6
            if (strpos($code, '6') === 0) {
                $expense = ChartOfAccount::where('account_type', 'Expense')->orderBy('account_code')->first();
                if ($expense) return $expense;
            }

            return null;
        };

        foreach ($rules as $rule) {
            $debit = $resolveAccount($rule['debit_code']);
            $credit = $resolveAccount($rule['credit_code']);

            if ($debit && $credit) {
                $slug = '';
                if ($rule['transaction_type'] === 'different_account') {
                    $slug = ($rule['condition'] === 'رسید') ? 'DIFF_IN' : 'DIFF_OUT';
                } else {
                    switch ($rule['condition']) {
                        case 'خوراکه': $slug = 'EXP_FOOD'; break;
                        case 'ترانسپورت': $slug = 'EXP_TRANS'; break;
                        case 'متفرقه': $slug = 'EXP_MISC'; break;
                        case 'گرفت': $slug = 'PYMT_OUT'; break;
                        case 'رسید': $slug = 'PYMT_IN'; break;
                        case 'deposit': $slug = 'CASH_IN'; break;
                        case 'withdrawal': $slug = 'CASH_OUT'; break;
                        case 'purchase': $slug = 'ASSET_PURCH'; break;
                        default: $slug = strtoupper(str_replace(' ', '_', $rule['transaction_type'] . '_' . $rule['condition']));
                    }
                }

                MappingRule::updateOrCreate(
                    ['transaction_type' => $rule['transaction_type'], 'condition' => $rule['condition']],
                    [
                        'mapping_key' => $slug,
                        'debit_account_id' => $debit->id,
                        'credit_account_id' => $credit->id,
                        'description_template' => $rule['template']
                    ]
                );
            }
        }

        // Dynamically locate accounts for SALES_REVENUE without hardcoding IDs or codes
        $receivableAccount = ChartOfAccount::where('account_type', 'Asset')
            ->where('account_name', 'like', '%Receivable%')
            ->first();
        $salesRevenueAccount = ChartOfAccount::where('account_type', 'Revenue')
            ->where('account_name', 'like', '%Sales%')
            ->first();

        if ($receivableAccount && $salesRevenueAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'sale', 'mapping_key' => 'SALES_REVENUE'],
                [
                    'condition' => 'revenue',
                    'debit_account_id' => $receivableAccount->id,
                    'credit_account_id' => $salesRevenueAccount->id,
                    'description_template' => 'فروش قالین - درآمد (Sales Revenue): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'material_sale', 'mapping_key' => 'MATERIAL_REVENUE'],
                [
                    'condition' => 'credit',
                    'debit_account_id' => $receivableAccount->id,
                    'credit_account_id' => $salesRevenueAccount->id,
                    'description_template' => 'فروش مواد - درآمد (Material Sales Revenue): {reference}'
                ]
            );
        }

        // Dynamically locate accounts for SALES_COGS without hardcoding IDs or codes
        $cogsAccount = ChartOfAccount::where('account_type', 'Expense')
            ->where('report_group', 'COGS')
            ->first();
        $inventoryAccount = ChartOfAccount::where('account_type', 'Asset')
            ->where('report_group', 'Current Asset')
            ->where('account_name', 'like', '%Inventory%')
            ->first();

        if ($cogsAccount && $inventoryAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'sale', 'mapping_key' => 'SALES_COGS'],
                [
                    'condition' => 'cogs',
                    'debit_account_id' => $cogsAccount->id,
                    'credit_account_id' => $inventoryAccount->id,
                    'description_template' => 'قیمت تمام شده فروش (COGS): {reference}'
                ]
            );
        }

        // Dynamically locate accounts for Vendor Receipt (seller_payment / رسید)
        $cashAccount = ChartOfAccount::where('account_type', 'Asset')
            ->where('is_cash_account', true)
            ->first();
        $payableAccount = ChartOfAccount::where('account_type', 'Liability')
            ->where(function($q) {
                $q->where('account_name', 'like', '%Accounts Payable%')
                  ->orWhere('account_name', 'like', '%Payable%');
            })
            ->first();

        if ($cashAccount && $payableAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'seller_payment', 'condition' => 'رسید'],
                [
                    'mapping_key' => 'PYMT_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $payableAccount->id,
                    'description_template' => 'رسید پول از فروشنده مواد (Vendor Receipt)'
                ]
            );
        }

        // Dynamically locate/ensure accounts for Vendor Payment (seller_payment / گرفت)
        if ($payableAccount && $cashAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'seller_payment', 'condition' => 'گرفت'],
                [
                    'mapping_key' => 'PYMT_OUT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'پرداخت پول به فروشنده (Seller Payment)'
                ]
            );
        }

        // --- DYNAMIC PRODUCTION COMPLETION & ISSUE RULES ---
        $finishedGoods = ChartOfAccount::where('account_name', 'like', '%Finished Goods%')->first();
        $wip = ChartOfAccount::where('account_name', 'like', '%WIP%')
            ->orWhere('account_name', 'like', '%Work In Progress%')
            ->first();
        $inventory = ChartOfAccount::where('account_name', 'like', '%Inventory%')
            ->where('account_name', 'not like', '%Finished%')
            ->where('account_name', 'not like', '%new%')
            ->first();

        if ($finishedGoods && $wip) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'production_completion', 'condition' => 'transfer'],
                [
                    'mapping_key' => 'PRODUCTION_COMPLETION_TRANSFER',
                    'debit_account_id' => $finishedGoods->id,
                    'credit_account_id' => $wip->id,
                    'description_template' => 'Production Completion: {reference}'
                ]
            );
        }

        if ($wip && $inventory) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'production_start', 'condition' => 'transfer'],
                [
                    'mapping_key' => 'PRODUCTION_START_TRANSFER',
                    'debit_account_id' => $wip->id,
                    'credit_account_id' => $inventory->id,
                    'description_template' => 'Production Issue (Start): {reference}'
                ]
            );
        }

        if ($wip && $payableAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'production_service', 'condition' => 'credit'],
                [
                    'mapping_key' => 'PRODUCTION_SERVICE_CREDIT',
                    'debit_account_id' => $wip->id,
                    'credit_account_id' => $payableAccount->id,
                    'description_template' => 'Production Service cost capitalization: {reference}'
                ]
            );
        }

        // --- DYNAMIC PAYROLL & EMPLOYEE PAYMENT RULES ---
        $salaryExpense = ChartOfAccount::where('account_name', 'like', '%Salary Expense%')->first();
        if (!$salaryExpense) {
            $salaryExpense = ChartOfAccount::create([
                'account_code' => '6200',
                'account_name' => 'Salary Expense',
                'account_type' => 'Expense',
                'report_group' => 'Operating Expense',
                'normal_balance' => 'debit',
                'is_cash_account' => false
            ]);
        }

        $salaryPayable = ChartOfAccount::where('account_name', 'like', '%Salary Payable%')->first();
        if (!$salaryPayable) {
            $salaryPayable = ChartOfAccount::create([
                'account_code' => '2300',
                'account_name' => 'Salary Payable',
                'account_type' => 'Liability',
                'report_group' => 'Current Liability',
                'normal_balance' => 'credit',
                'is_cash_account' => false
            ]);
        }

        if ($salaryExpense && $salaryPayable) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'payroll', 'condition' => 'accrual'],
                [
                    'mapping_key' => 'PAYROLL_ACCRUAL',
                    'debit_account_id' => $salaryExpense->id,
                    'credit_account_id' => $salaryPayable->id,
                    'description_template' => 'Salary Accrual for {reference}'
                ]
            );
        }

        if ($salaryPayable && $cashAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'employee_payment', 'condition' => 'payment'],
                [
                    'mapping_key' => 'PAYROLL_PAYMENT',
                    'debit_account_id' => $salaryPayable->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'Salary Payment to Employee: {reference}'
                ]
            );
        }

        // --- DYNAMIC MATERIAL PAYMENTS ---
        if ($cashAccount && $payableAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'material_payment_in', 'condition' => 'رسید'],
                [
                    'mapping_key' => 'MATERIAL_RECEIPT',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $payableAccount->id,
                    'description_template' => 'رسید از بابت مواد (Material Receipt): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'material_payment_out', 'condition' => 'گرفت'],
                [
                    'mapping_key' => 'MATERIAL_PAYMENT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'پرداخت از بابت مواد (Material Payment): {reference}'
                ]
            );
        }

        // --- DYNAMIC WASHING & FINISHING PAYMENT RECEIPTS ---
        if ($cashAccount && $payableAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'washing_payment', 'condition' => 'رسید'],
                [
                    'mapping_key' => 'PYMT_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $payableAccount->id,
                    'description_template' => 'رسید پول از تیم شست (Washing Team Receipt)'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'finishing_payment', 'condition' => 'رسید'],
                [
                    'mapping_key' => 'PYMT_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $payableAccount->id,
                    'description_template' => 'رسید پول از تیم تیاری (Finishing Team Receipt)'
                ]
            );
        }

        // --- DYNAMIC KACHAEE REPAIR TRANSFER ---
        if ($wip) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'kachaee_transfer', 'condition' => 'ارسال به کچایی'],
                [
                    'mapping_key' => 'KCH_TRANS_OUT',
                    'debit_account_id' => $wip->id,
                    'credit_account_id' => $wip->id,
                    'description_template' => 'ارسال به کچایی (Transfer to Kachaee): {reference}'
                ]
            );
        }

        // --- DYNAMIC FIXED ASSET DEPRECIATION ---
        $depreciationExpense = ChartOfAccount::where('account_name', 'like', '%Depreciation Expense%')->first() 
            ?: ChartOfAccount::where('account_name', 'like', '%Operational Expenses%')->first();
        $accumulatedDepreciation = ChartOfAccount::where('account_name', 'like', '%Accumulated Depreciation%')->first();

        if ($depreciationExpense && $accumulatedDepreciation) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'asset', 'condition' => 'depreciation'],
                [
                    'mapping_key' => 'ASSET_DEPRECIATION',
                    'debit_account_id' => $depreciationExpense->id,
                    'credit_account_id' => $accumulatedDepreciation->id,
                    'description_template' => 'Asset Depreciation: {reference}'
                ]
            );
        }

        // --- DYNAMIC OPERATIONAL EXPENSE CATEGORIES ---
        $operationalExpense = ChartOfAccount::where('account_name', 'like', '%Operational Expenses%')->first();
        if ($operationalExpense && $cashAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'expense', 'condition' => 'ترمیمات و تیل'],
                [
                    'mapping_key' => 'EXP_FUEL',
                    'debit_account_id' => $operationalExpense->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'هزینه ترمیمات و تیل (Fuel/Repair Expense): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'expense', 'condition' => 'اجوره'],
                [
                    'mapping_key' => 'EXP_WAGES',
                    'debit_account_id' => $operationalExpense->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'هزینه اجوره (Wages Expense): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'expense', 'condition' => 'متفرقه دفتر'],
                [
                    'mapping_key' => 'EXP_MISC_OFFICE',
                    'debit_account_id' => $operationalExpense->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'هزینه های متفرقه دفتر (Office Misc Expense): {reference}'
                ]
            );
        }

        // --- DYNAMIC AGENT ADVANCE & SETTLEMENT RULES ---
        $advancesAccount = ChartOfAccount::where('account_code', '1350')->first();
        if ($advancesAccount && $cashAccount && $payableAccount) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'agent_payment', 'condition' => 'AGENT_ADVANCE_OUT'],
                [
                    'mapping_key' => 'AGENT_ADVANCE_OUT',
                    'debit_account_id' => $advancesAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'ثبت پیش‌پرداخت به نماینده (Agent Advance Payment): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'agent_payment', 'condition' => 'AGENT_ADVANCE_IN'],
                [
                    'mapping_key' => 'AGENT_ADVANCE_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'استرداد پیش‌پرداخت از نماینده (Agent Advance Refund): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'seller_payment', 'condition' => 'VENDOR_ADVANCE_OUT'],
                [
                    'mapping_key' => 'VENDOR_ADVANCE_OUT',
                    'debit_account_id' => $advancesAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'ثبت پیش‌پرداخت به فروشنده (Vendor Advance Payment): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'seller_payment', 'condition' => 'VENDOR_ADVANCE_IN'],
                [
                    'mapping_key' => 'VENDOR_ADVANCE_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'استرداد پیش‌پرداخت از فروشنده (Vendor Advance Refund): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'agent_advance_settlement', 'condition' => 'ADVANCE_SETTLEMENT'],
                [
                    'mapping_key' => 'ADVANCE_SETTLEMENT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'تصفیه بل خرید از پیش‌پرداخت (Advance Bill Settlement): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'vendor_advance_settlement', 'condition' => 'ADVANCE_SETTLEMENT'],
                [
                    'mapping_key' => 'VENDOR_ADVANCE_SETTLEMENT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'تصفیه بل مواد از پیش‌پرداخت (Vendor Advance Settlement): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'kachaee_payment', 'condition' => 'KACHAEE_ADVANCE_OUT'],
                [
                    'mapping_key' => 'KACHAEE_ADVANCE_OUT',
                    'debit_account_id' => $advancesAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'ثبت پیش‌پرداخت به تیم کچایی (Kachaee Advance Payment): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'kachaee_payment', 'condition' => 'KACHAEE_ADVANCE_IN'],
                [
                    'mapping_key' => 'KACHAEE_ADVANCE_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'استرداد پیش‌پرداخت از تیم کچایی (Kachaee Advance Refund): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'kachaee_advance_settlement', 'condition' => 'ADVANCE_SETTLEMENT'],
                [
                    'mapping_key' => 'KACHAEE_ADVANCE_SETTLEMENT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'تصفیه حساب کچایی از پیش‌پرداخت (Kachaee Advance Settlement): {reference}'
                ]
            );

            // --- DYNAMIC WASHING ADVANCE & SETTLEMENT RULES ---
            MappingRule::updateOrCreate(
                ['transaction_type' => 'washing_payment', 'condition' => 'WASH_ADVANCE_OUT'],
                [
                    'mapping_key' => 'WASH_ADVANCE_OUT',
                    'debit_account_id' => $advancesAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'ثبت پیش‌پرداخت به تیم شست‌وشو (Washing Advance Payment): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'washing_payment', 'condition' => 'WASH_ADVANCE_IN'],
                [
                    'mapping_key' => 'WASH_ADVANCE_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'استرداد پیش‌پرداخت از تیم شست‌وشو (Washing Advance Refund): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'washing_advance_settlement', 'condition' => 'ADVANCE_SETTLEMENT'],
                [
                    'mapping_key' => 'WASH_ADVANCE_SETTLEMENT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'تصفیه حساب شست‌وشو از پیش‌پرداخت (Washing Advance Settlement): {reference}'
                ]
            );

            // --- DYNAMIC FINISHING ADVANCE & SETTLEMENT RULES ---
            MappingRule::updateOrCreate(
                ['transaction_type' => 'finishing_payment', 'condition' => 'FINISH_ADVANCE_OUT'],
                [
                    'mapping_key' => 'FINISH_ADVANCE_OUT',
                    'debit_account_id' => $advancesAccount->id,
                    'credit_account_id' => $cashAccount->id,
                    'description_template' => 'ثبت پیش‌پرداخت به تیم آماده‌سازی (Finishing Advance Payment): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'finishing_payment', 'condition' => 'FINISH_ADVANCE_IN'],
                [
                    'mapping_key' => 'FINISH_ADVANCE_IN',
                    'debit_account_id' => $cashAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'استرداد پیش‌پرداخت از تیم آماده‌سازی (Finishing Advance Refund): {reference}'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'finishing_advance_settlement', 'condition' => 'ADVANCE_SETTLEMENT'],
                [
                    'mapping_key' => 'FINISH_ADVANCE_SETTLEMENT',
                    'debit_account_id' => $payableAccount->id,
                    'credit_account_id' => $advancesAccount->id,
                    'description_template' => 'تصفیه حساب آماده‌سازی از پیش‌پرداخت (Finishing Advance Settlement): {reference}'
                ]
            );
        }
    }
}

