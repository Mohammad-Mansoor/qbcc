<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedKachaeeMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create WIP Warehouse
        $warehouseId = DB::table('warehouses')->insertGetId([
            'name' => 'گدام کچایی (WIP - Kachaee)',
            'location' => 'Virtual',
            'type' => 'WIP',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        $apId = DB::table('chart_of_accounts')->where('account_code', '2100')->value('id');
        if (!$apId) {
            $apId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '2100',
                'account_name' => 'Accounts Payable',
                'account_type' => 'Liability',
                'report_group' => 'Current Liability',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'credit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert Mapping Rules
        DB::table('mapping_rules')->insert([
            [
                'transaction_type' => 'kachaee_repair_cost',
                'condition' => 'کچایی',
                'mapping_key' => 'KCH_REP_COST',
                'debit_account_id' => $cashId, // Default, user can change
                'credit_account_id' => $apId, // Accounts Payable Kachaee
                'warehouse_id' => null,
                'description_template' => 'مصرف کچایی (Kachaee Expense)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transaction_type' => 'kachaee_transfer',
                'condition' => 'ارسال به کچایی',
                'mapping_key' => 'KCH_TRANS_OUT',
                'debit_account_id' => $cashId,
                'credit_account_id' => $cashId,
                'warehouse_id' => $warehouseId,
                'description_template' => 'انتقال به گدام کچایی',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('mapping_rules')->whereIn('transaction_type', ['kachaee_repair_cost', 'kachaee_transfer'])->delete();
        DB::table('warehouses')->where('name', 'گدام کچایی (WIP - Kachaee)')->delete();
    }
}
