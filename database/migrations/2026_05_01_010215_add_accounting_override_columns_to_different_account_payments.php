<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingOverrideColumnsToDifferentAccountPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('different_account_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('override_debit_account_id')->nullable();
            $table->unsignedBigInteger('override_credit_account_id')->nullable();
            $table->unsignedBigInteger('ledger_transaction_id')->nullable();

            $table->foreign('override_debit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('override_credit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('ledger_transaction_id')->references('id')->on('ledger_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('different_account_payments', function (Blueprint $table) {
            $table->dropForeign(['override_debit_account_id']);
            $table->dropForeign(['override_credit_account_id']);
            $table->dropForeign(['ledger_transaction_id']);
            $table->dropColumn(['override_debit_account_id', 'override_credit_account_id', 'ledger_transaction_id']);
        });
    }
}
