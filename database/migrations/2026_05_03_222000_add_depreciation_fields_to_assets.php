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
        
        // Ensure mapping exists
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'asset', 'condition' => 'depreciation'],
            [
                'mapping_key' => 'ASSET_DEPRECIATION',
                'debit_account_id' => 7, // Operational Expenses
                'credit_account_id' => 13, // Fixed Assets (Contrally reduce the asset value)
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
