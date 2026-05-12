<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FinalizeAccountingIntegrity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Unique index already created in failed attempt, skipping.

        // 2. Add Accumulated Depreciation Account
        $accDepId = DB::table('chart_of_accounts')->insertGetId([
            'account_code' => '1550',
            'account_name' => 'Accumulated Depreciation',
            'account_type' => 'Asset', 
            'currency' => 'USD',
            'is_cash_account' => 0,
            'normal_balance' => 'credit',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Update Depreciation Mapping to use the correct contra-account
        DB::table('mapping_rules')->where('mapping_key', 'ASSET_DEPRECIATION')->update([
            'credit_account_id' => $accDepId,
            'description_template' => 'ثبت استهلاک (Depreciation): {reference}'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('chart_of_accounts')->where('account_code', '1550')->delete();
    }
}
