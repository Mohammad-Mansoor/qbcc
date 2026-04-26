<?php

use Illuminate\Database\Seeder;
use App\MappingRule;
use App\ChartOfAccount;

class MappingRulesSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            [
                'transaction_type' => 'sale',
                'condition' => 'cash',
                'debit_code' => '1100', // Cash
                'credit_code' => '4100', // Sales Revenue
                'template' => 'فروش نقدی (Cash Sale)'
            ],
            [
                'transaction_type' => 'sale',
                'condition' => 'credit',
                'debit_code' => '1300', // AR
                'credit_code' => '4100', // Sales Revenue
                'template' => 'فروش نسیه (Credit Sale)'
            ],
            [
                'transaction_type' => 'customer_payment',
                'condition' => 'receipt', // 'رسید'
                'debit_code' => '1100', // Cash
                'credit_code' => '1300', // AR
                'template' => 'دریافت وجه از مشتری (Customer Receipt)'
            ],
            [
                'transaction_type' => 'customer_payment',
                'condition' => 'withdrawal', // 'گرفت'
                'debit_code' => '1300', // AR
                'credit_code' => '1100', // Cash
                'template' => 'پرداخت وجه به مشتری (Customer Withdrawal)'
            ],
        ];

        foreach ($rules as $rule) {
            $debit = ChartOfAccount::where('account_code', $rule['debit_code'])->first();
            $credit = ChartOfAccount::where('account_code', $rule['credit_code'])->first();

            if ($debit && $credit) {
                MappingRule::updateOrCreate(
                    ['transaction_type' => $rule['transaction_type'], 'condition' => $rule['condition']],
                    [
                        'debit_account_id' => $debit->id,
                        'credit_account_id' => $credit->id,
                        'description_template' => $rule['template']
                    ]
                );
            }
        }
    }
}
