<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverrideAccountsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('override_debit_account_id')->nullable()->after('currency_id');
            $table->unsignedBigInteger('override_credit_account_id')->nullable()->after('override_debit_account_id');
            $table->unsignedBigInteger('override_cogs_debit_id')->nullable()->after('override_credit_account_id');
            $table->unsignedBigInteger('override_cogs_credit_id')->nullable()->after('override_cogs_debit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'override_debit_account_id',
                'override_credit_account_id',
                'override_cogs_debit_id',
                'override_cogs_credit_id'
            ]);
        });
    }
}
