<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddWipAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add WIP account if it doesn't exist
        $exists = DB::table('chart_of_accounts')->where('account_code', '1410')->exists();
        if (!$exists) {
            DB::table('chart_of_accounts')->insert([
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
        
        // Ensure Finished Goods is 1420
        $exists = DB::table('chart_of_accounts')->where('account_code', '1420')->exists();
        if (!$exists) {
            DB::table('chart_of_accounts')->insert([
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('chart_of_accounts')->whereIn('account_code', ['1410', '1420'])->delete();
    }
}
