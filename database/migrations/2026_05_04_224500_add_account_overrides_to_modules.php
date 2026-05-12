<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountOverridesToModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'customer_payments',
            'seller_payments',
            'expenses',
            'material_purchases',
            'material_sales',
            'washing_payments',
            'salary_payments',
            'fixed_assets'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'override_debit_account_id')) {
                        $table->unsignedBigInteger('override_debit_account_id')->nullable();
                    }
                    if (!Schema::hasColumn($table->getTable(), 'override_credit_account_id')) {
                        $table->unsignedBigInteger('override_credit_account_id')->nullable();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'customer_payments',
            'seller_payments',
            'expenses',
            'material_purchases',
            'material_sales',
            'washing_payments',
            'salary_payments',
            'fixed_assets'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn(['override_debit_account_id', 'override_credit_account_id']);
                });
            }
        }
    }
}
