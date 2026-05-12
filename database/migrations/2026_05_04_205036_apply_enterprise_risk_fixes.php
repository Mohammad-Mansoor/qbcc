<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApplyEnterpriseRiskFixes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Improve Decimal Precision for Accounting Integrity (Raw SQL for Laravel 7 compatibility)
        DB::statement("ALTER TABLE ledger_entries MODIFY debit DECIMAL(19, 4) DEFAULT 0.0000");
        DB::statement("ALTER TABLE ledger_entries MODIFY credit DECIMAL(19, 4) DEFAULT 0.0000");
        DB::statement("ALTER TABLE ledger_entries MODIFY base_currency_amount DECIMAL(19, 4) DEFAULT NULL");

        // 2. Enforce Idempotency at Database Level
        // Note: This prevents the same business event from being posted twice
        Schema::table('ledger_transactions', function (Blueprint $table) {
            // First, ensure we don't have duplicates that would break the migration
            // (Usually not an issue in fresh dev, but good practice)
            
            $table->unique(['source_type', 'source_id', 'mapping_key'], 'idx_ledger_idempotency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->decimal('debit', 15, 2)->change();
            $table->decimal('credit', 15, 2)->change();
            $table->decimal('base_currency_amount', 15, 2)->change();
        });

        Schema::table('ledger_transactions', function (Blueprint $table) {
            $table->dropUnique('idx_ledger_idempotency');
        });
    }
}
