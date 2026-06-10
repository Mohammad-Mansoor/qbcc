<?php

use Illuminate\Database\Seeder;
use App\MappingRule;

class LaborPaymentMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cash = \App\ChartOfAccount::where('is_cash_account', true)->first();
        $ap = \App\ChartOfAccount::where('account_type', 'Liability')->where('account_name', 'like', '%Payable%')->first();

        if (!$cash || !$ap) {
            return;
        }

        $rules = [
            [
                'transaction_type' => 'washing_payment',
                'condition' => 'گرفت',
                'mapping_key' => 'PYMT_OUT',
                'debit_account_id' => $ap->id,
                'credit_account_id' => $cash->id,
                'description_template' => 'پرداخت پول به تیم شست (Washing Team Payment)',
            ],
            [
                'transaction_type' => 'washing_payment',
                'condition' => 'رسید',
                'mapping_key' => 'PYMT_IN',
                'debit_account_id' => $cash->id,
                'credit_account_id' => $ap->id,
                'description_template' => 'رسید پول از تیم شست (Washing Team Receipt)',
            ],
            [
                'transaction_type' => 'finishing_payment',
                'condition' => 'گرفت',
                'mapping_key' => 'PYMT_OUT',
                'debit_account_id' => $ap->id,
                'credit_account_id' => $cash->id,
                'description_template' => 'پرداخت پول به تیم تیاری (Finishing Team Payment)',
            ],
            [
                'transaction_type' => 'finishing_payment',
                'condition' => 'رسید',
                'mapping_key' => 'PYMT_IN',
                'debit_account_id' => $cash->id,
                'credit_account_id' => $ap->id,
                'description_template' => 'رسید پول از تیم تیاری (Finishing Team Receipt)',
            ],
            [
                'transaction_type' => 'kachaee_payment',
                'condition' => 'گرفت',
                'mapping_key' => 'PYMT_OUT',
                'debit_account_id' => $ap->id,
                'credit_account_id' => $cash->id,
                'description_template' => 'پرداخت پول به تیم کچایی (Kachaee Team Payment)',
            ],
            [
                'transaction_type' => 'kachaee_payment',
                'condition' => 'رسید',
                'mapping_key' => 'PYMT_IN',
                'debit_account_id' => $cash->id,
                'credit_account_id' => $ap->id,
                'description_template' => 'رسید پول از تیم کچایی (Kachaee Team Receipt)',
            ],
        ];

        foreach ($rules as $data) {
            MappingRule::updateOrCreate(
                ['transaction_type' => $data['transaction_type'], 'condition' => $data['condition']],
                $data
            );
        }
    }
}
