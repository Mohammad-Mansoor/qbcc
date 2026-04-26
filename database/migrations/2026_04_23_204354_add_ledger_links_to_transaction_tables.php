<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLedgerLinksToTransactionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'sales',
            'customer_payments',
            'agent_payments',
            'washing_payments',
            'seller_payments',
            'finishing_team_payments',
            'employee_payments',
            'office_credits',
            'office_debits',
            'invoices',
            'purchase_materials',
            'kachaee_payments',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('ledger_transaction_id')->nullable()->after('id');
                    $table->foreign('ledger_transaction_id')->references('id')->on('ledger_transactions')->onDelete('set null');
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
            'sales',
            'customer_payments',
            'agent_payments',
            'washing_payments',
            'seller_payments',
            'finishing_team_payments',
            'employee_payments',
            'office_credits',
            'office_debits',
            'invoices',
            'purchase_materials',
            'kachaee_payments',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['ledger_transaction_id']);
                    $table->dropColumn('ledger_transaction_id');
                });
            }
        }
    }
}
