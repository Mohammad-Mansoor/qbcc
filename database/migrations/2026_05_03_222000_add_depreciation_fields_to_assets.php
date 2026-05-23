<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepreciationFieldsToAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ajnas_account_details', function (Blueprint $table) {
            $table->decimal('accumulated_depreciation', 15, 2)->default(0)->after('estimated_salvage_value');
            $table->date('last_depreciation_date')->nullable()->after('accumulated_depreciation');
        });
        
        // Dynamically resolve account IDs by code to support fresh migrations
        $expenseId = DB::table('chart_of_accounts')->where('account_code', '6000')->value('id');
        if (!$expenseId) {
            $expenseId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '6000',
                'account_name' => 'Operational Expenses',
                'account_type' => 'Expense',
                'report_group' => 'Operating Expense',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $fixedAssetId = DB::table('chart_of_accounts')->where('account_code', '1500')->value('id');
        if (!$fixedAssetId) {
            $fixedAssetId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '1500',
                'account_name' => 'Fixed Assets',
                'account_type' => 'Asset',
                'report_group' => 'Fixed Asset',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure mapping exists
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'asset', 'condition' => 'depreciation'],
            [
                'mapping_key' => 'ASSET_DEPRECIATION',
                'debit_account_id' => $expenseId, // Operational Expenses
                'credit_account_id' => $fixedAssetId, // Fixed Assets (Contrally reduce the asset value)
                'description_template' => 'استهلاک ماهوار جایداد (Monthly Depreciation): {reference}'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ajnas_account_details', function (Blueprint $table) {
            $table->dropColumn(['accumulated_depreciation', 'last_depreciation_date']);
        });
    }
}
