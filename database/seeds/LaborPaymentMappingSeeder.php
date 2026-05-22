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
        $rules = [
            [
                'transaction_type' => 'washing_payment',
                'condition' => 'گرفت',
                'mapping_key' => 'PYMT_OUT',
                'debit_account_id' => 3, // Accounts Payable
                'credit_account_id' => 1, // Cash
                'description_template' => 'پرداخت پول به تیم شست (Washing Team Payment)',
            ],
            [
                'transaction_type' => 'washing_payment',
                'condition' => 'رسید',
                'mapping_key' => 'PYMT_IN',
                'debit_account_id' => 1, // Cash
                'credit_account_id' => 3, // Accounts Payable
                'description_template' => 'رسید پول از تیم شست (Washing Team Receipt)',
            ],
            [
                'transaction_type' => 'finishing_payment',
                'condition' => 'گرفت',
                'mapping_key' => 'PYMT_OUT',
                'debit_account_id' => 3, // Accounts Payable
                'credit_account_id' => 1, // Cash
                'description_template' => 'پرداخت پول به تیم تیاری (Finishing Team Payment)',
            ],
            [
                'transaction_type' => 'finishing_payment',
                'condition' => 'رسید',
                'mapping_key' => 'PYMT_IN',
                'debit_account_id' => 1, // Cash
                'credit_account_id' => 3, // Accounts Payable
                'description_template' => 'رسید پول از تیم تیاری (Finishing Team Receipt)',
            ],
            [
                'transaction_type' => 'kachaee_payment',
                'condition' => 'گرفت',
                'mapping_key' => 'PYMT_OUT',
                'debit_account_id' => 3, // Accounts Payable
                'credit_account_id' => 1, // Cash
                'description_template' => 'پرداخت پول به تیم کچایی (Kachaee Team Payment)',
            ],
            [
                'transaction_type' => 'kachaee_payment',
                'condition' => 'رسید',
                'mapping_key' => 'PYMT_IN',
                'debit_account_id' => 1, // Cash
                'credit_account_id' => 3, // Accounts Payable
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
