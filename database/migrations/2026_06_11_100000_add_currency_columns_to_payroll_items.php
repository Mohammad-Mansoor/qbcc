<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyColumnsToPayrollItems extends Migration
{
    public function up()
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_items', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('net_salary');
            }
            if (!Schema::hasColumn('payroll_items', 'currency_code')) {
                $table->string('currency_code', 10)->nullable()->after('currency_id');
            }
            if (!Schema::hasColumn('payroll_items', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->nullable()->default(1)->after('currency_code');
            }
            if (!Schema::hasColumn('payroll_items', 'net_salary_usd')) {
                $table->decimal('net_salary_usd', 15, 4)->nullable()->after('exchange_rate');
            }
            if (!Schema::hasColumn('payroll_items', 'base_salary_usd')) {
                $table->decimal('base_salary_usd', 15, 4)->nullable()->after('net_salary_usd');
            }
            if (!Schema::hasColumn('payroll_items', 'payroll_run_id') === false) {
                // FK already exists — skip
            }
        });

        // Backfill existing rows: assume USD for legacy data
        \DB::statement("
            UPDATE payroll_items
            SET currency_code = 'USD',
                exchange_rate = 1.0,
                net_salary_usd = net_salary,
                base_salary_usd = base_salary
            WHERE currency_code IS NULL
        ");
    }

    public function down()
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $cols = ['currency_id', 'currency_code', 'exchange_rate', 'net_salary_usd', 'base_salary_usd'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payroll_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
