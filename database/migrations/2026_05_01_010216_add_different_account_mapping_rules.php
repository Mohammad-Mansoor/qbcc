<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\MappingRule;
use App\ChartOfAccount;

class AddDifferentAccountMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $debit = ChartOfAccount::where('account_code', '1000')->first();
        $credit = ChartOfAccount::where('account_code', '2100')->first();

        if ($debit && $credit) {
            MappingRule::updateOrCreate(
                ['transaction_type' => 'different_account', 'condition' => 'رسید'],
                [
                    'mapping_key' => 'DIFF_IN',
                    'debit_account_id' => $debit->id,
                    'credit_account_id' => $credit->id,
                    'description_template' => 'رسید متفرقه (Miscellaneous Receipt)'
                ]
            );

            MappingRule::updateOrCreate(
                ['transaction_type' => 'different_account', 'condition' => 'گرفت'],
                [
                    'mapping_key' => 'DIFF_OUT',
                    'debit_account_id' => $credit->id,
                    'credit_account_id' => $debit->id,
                    'description_template' => 'پرداخت متفرقه (Miscellaneous Payment)'
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        MappingRule::whereIn('mapping_key', ['DIFF_IN', 'DIFF_OUT'])->update(['mapping_key' => null]);
    }
}
