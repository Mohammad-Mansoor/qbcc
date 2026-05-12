<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMultiCurrencyToDifferentAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Update payments table
        Schema::table('different_account_payments', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('amount');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000)->after('currency_code');
            $table->decimal('base_amount', 15, 2)->nullable()->after('exchange_rate');
        });

        // Convert varchar amounts to decimal safely
        DB::statement("UPDATE different_account_payments SET amount = REPLACE(amount, ',', '')");
        DB::statement("ALTER TABLE different_account_payments MODIFY amount DECIMAL(15,2) NOT NULL DEFAULT 0.00");
        DB::statement("UPDATE different_account_payments SET base_amount = amount");

        // 2. Update totals table
        // First, wipe the existing corrupt totals so we can rebuild them cleanly
        DB::table('different_account_totals')->truncate();

        Schema::table('different_account_totals', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('account_id');
        });

        // Convert varchar to decimal
        DB::statement("ALTER TABLE different_account_totals MODIFY total DECIMAL(15,2) NOT NULL DEFAULT 0.00");
        DB::statement("ALTER TABLE different_account_totals MODIFY paid DECIMAL(15,2) NOT NULL DEFAULT 0.00");
        DB::statement("ALTER TABLE different_account_totals MODIFY remaining DECIMAL(15,2) NOT NULL DEFAULT 0.00");

        Schema::table('different_account_totals', function (Blueprint $table) {
            $table->unique(['account_id', 'currency_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('different_account_totals', function (Blueprint $table) {
            $table->dropUnique(['account_id', 'currency_code']);
            $table->dropColumn('currency_code');
        });

        Schema::table('different_account_payments', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_amount']);
        });

        // Reverting type changes is risky, so we generally leave them as DECIMAL.
    }
}
