<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedWashingMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Dynamically resolve account IDs by code to support fresh migrations
        $cashId = DB::table('chart_of_accounts')->where('account_code', '1000')->value('id');
        if (!$cashId) {
            $cashId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '1000',
                'account_name' => 'Cash',
                'account_type' => 'Asset',
                'report_group' => 'Current Asset',
                'currency' => 'USD',
                'is_cash_account' => 1,
                'normal_balance' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add washing_transfer for sending carpet to wash
        \Illuminate\Support\Facades\DB::table('mapping_rules')->insert([
            'transaction_type' => 'washing_transfer',
            'condition' => 'transfer',
            'debit_account_id' => $cashId, // WIP - Washing (To be configured)
            'credit_account_id' => $cashId, // Main Inventory (To be configured)
            'description_template' => 'انتقال به گدام شست (Washing Transfer)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::table('mapping_rules')->where('transaction_type', 'washing_transfer')->delete();
    }
}
