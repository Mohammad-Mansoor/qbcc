<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddWipMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Dynamically resolve account IDs by code to support fresh migrations
        $wipId = DB::table('chart_of_accounts')->where('account_code', '1410')->value('id');
        if (!$wipId) {
            $wipId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '1410',
                'account_name' => 'Work In Progress (WIP)',
                'account_type' => 'Asset',
                'report_group' => 'Current Asset',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $inventoryId = DB::table('chart_of_accounts')->where('account_code', '1400')->value('id');
        if (!$inventoryId) {
            $inventoryId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '1400',
                'account_name' => 'Inventory',
                'account_type' => 'Asset',
                'report_group' => 'Current Asset',
                'currency' => 'USD',
                'is_cash_account' => 0,
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

        $fgId = DB::table('chart_of_accounts')->where('account_code', '1420')->value('id');
        if (!$fgId) {
            $fgId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '1420',
                'account_name' => 'Finished Goods',
                'account_type' => 'Asset',
                'report_group' => 'Current Asset',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 1. DR WIP / CR Inventory - Production Start
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'production_start', 'condition' => 'transfer'],
            [
                'debit_account_id' => $wipId,
                'credit_account_id' => $inventoryId,
                'description_template' => 'Production Start: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. DR WIP / CR Payable - Production Service (Washing/Finishing)
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'production_service', 'condition' => 'credit'],
            [
                'debit_account_id' => $wipId,
                'credit_account_id' => $apId,
                'description_template' => 'Service Cost: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. DR Finished Goods / CR WIP - Production Completion
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'production_completion', 'condition' => 'transfer'],
            [
                'debit_account_id' => $fgId,
                'credit_account_id' => $wipId,
                'description_template' => 'Production Completion: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Update washing and finishing to use WIP if desired, 
        // but user specifically asked for these 3 flows.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('mapping_rules')->whereIn('transaction_type', ['production_start', 'production_service', 'production_completion'])->delete();
    }
}
