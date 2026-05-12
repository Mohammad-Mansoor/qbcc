<?php

use Illuminate\Database\Seeder;
use App\MappingRule;
use App\ChartOfAccount;

class ERPMappingRulesSeeder extends Seeder
{
    public function run()
    {
        $mappings = [
            [
                'mapping_key' => 'WASHING_CREDIT',
                'transaction_type' => 'washing',
                'condition' => 'credit',
                'debit_code' => '1410', // WIP
                'credit_code' => '2100', // A/P
                'template' => 'هزینه شست قالین (Washing Cost Capitalization)'
            ],
            [
                'mapping_key' => 'FINISHING_CREDIT',
                'transaction_type' => 'finishing',
                'condition' => 'credit',
                'debit_code' => '1410', // WIP
                'credit_code' => '2100', // A/P
                'template' => 'هزینه تیاری قالین (Finishing Cost Capitalization)'
            ],
            [
                'mapping_key' => 'kachaee_repair_cost',
                'transaction_type' => 'kachaee',
                'condition' => 'credit',
                'debit_code' => '1410', // WIP
                'credit_code' => '2100', // A/P
                'template' => 'هزینه کچایی قالین (Kachaee Repair Cost Capitalization)'
            ],
            [
                'mapping_key' => 'PYMT_OUT',
                'transaction_type' => 'payment',
                'condition' => 'گرفت',
                'debit_code' => '2100', // A/P
                'credit_code' => '1000', // Cash
                'template' => 'پرداخت پول به تیم/فروشنده (Outbound Payment)'
            ],
            [
                'mapping_key' => 'PYMT_IN',
                'transaction_type' => 'payment',
                'condition' => 'رسید',
                'debit_code' => '1000', // Cash
                'credit_code' => '1200', // A/R
                'template' => 'رسید پول از مشتری/نماینده (Inbound Payment)'
            ],
            [
                'mapping_key' => 'washing_transfer',
                'transaction_type' => 'inventory',
                'condition' => 'transfer',
                'debit_code' => '1410', // WIP
                'credit_code' => '1410', // WIP (Location change)
                'template' => 'انتقال قالین به شستشو (Transfer to Washing)'
            ],
        ];

        foreach ($mappings as $map) {
            $debit = ChartOfAccount::where('account_code', $map['debit_code'])->first();
            $credit = ChartOfAccount::where('account_code', $map['credit_code'])->first();

            if ($debit && $credit) {
                MappingRule::updateOrCreate(
                    ['transaction_type' => $map['transaction_type'], 'condition' => $map['condition']],
                    [
                        'mapping_key' => $map['mapping_key'],
                        'debit_account_id' => $debit->id,
                        'credit_account_id' => $credit->id,
                        'description_template' => $map['template']
                    ]
                );
            }
        }
    }
}
