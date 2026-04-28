<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SafetySchemaRepair extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Repair customer_payments
        if (Schema::hasTable('customer_payments')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_payments', 'status')) {
                    $table->integer('status')->default(1)->after('amount_af');
                }
                if (!Schema::hasColumn('customer_payments', 'ledger_transaction_id')) {
                    $table->unsignedBigInteger('ledger_transaction_id')->nullable()->after('status');
                }
            });
        }

        // 2. Repair invoices
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'invoice_description')) {
                    $table->text('invoice_description')->nullable()->after('customer_id');
                }
            });
        }

        // 3. Add ledger_transaction_id to other transaction tables if missing
        $tables = [
            'sales', 'agent_payments', 'washing_payments', 
            'seller_payments', 'finishing_team_payments', 
            'employee_payments', 'office_credits', 'office_debits'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'ledger_transaction_id')) {
                        $table->unsignedBigInteger('ledger_transaction_id')->nullable();
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
        // No down needed for safety repair
    }
}
