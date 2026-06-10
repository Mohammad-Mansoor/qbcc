<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverrideAccountsToNewMonthlyExpenseBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_monthly_expense_balances', function (Blueprint $table) {
            $table->unsignedBigInteger('override_debit_account_id')->nullable()->after('month_id');
            $table->unsignedBigInteger('override_credit_account_id')->nullable()->after('override_debit_account_id');
            
            $table->foreign('override_debit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('override_credit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_monthly_expense_balances', function (Blueprint $table) {
            $table->dropForeign(['override_debit_account_id']);
            $table->dropForeign(['override_credit_account_id']);
            $table->dropColumn(['override_debit_account_id', 'override_credit_account_id']);
        });
    }
}
