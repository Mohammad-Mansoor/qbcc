<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HardenEmployeeSalariesForensicFx extends Migration
{
    public function up()
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            // Note: original column is "contact_number" (typo) — we add "contract_number" separately
            if (!Schema::hasColumn('employee_salaries', 'contract_number')) {
                $table->string('contract_number')->nullable()->after('id');
            }

            // Forensic FX columns
            if (!Schema::hasColumn('employee_salaries', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('salary');
            }
            if (!Schema::hasColumn('employee_salaries', 'currency_code')) {
                $table->string('currency_code', 10)->nullable()->after('currency_id');
            }
            if (!Schema::hasColumn('employee_salaries', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->nullable()->default(1)->after('currency_code');
            }
            if (!Schema::hasColumn('employee_salaries', 'salary_usd')) {
                $table->decimal('salary_usd', 18, 4)->nullable()->after('exchange_rate');
            }
            if (!Schema::hasColumn('employee_salaries', 'salary_currency')) {
                $table->decimal('salary_currency', 18, 4)->nullable()->after('salary_usd');
            }
        });

        // Add FK separately to avoid issues
        try {
            Schema::table('employee_salaries', function (Blueprint $table) {
                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // FK may already exist — skip
        }

        // Backfill existing rows: use the base currency (USD)
        $baseCurrency = DB::table('currencies')->where('is_base_currency', 1)->first();
        if ($baseCurrency) {
            DB::table('employee_salaries')
                ->whereNull('currency_id')
                ->update([
                    'currency_id'     => $baseCurrency->id,
                    'currency_code'   => $baseCurrency->code,
                    'exchange_rate'   => 1.0,
                    'salary_usd'      => DB::raw('salary'),
                    'salary_currency' => DB::raw('salary'),
                ]);
        }

        // Also sync contact_number → contract_number if it was stored in wrong column
        DB::statement("UPDATE employee_salaries SET contract_number = contact_number WHERE contract_number IS NULL AND contact_number IS NOT NULL");
    }

    public function down()
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            try { $table->dropForeign(['currency_id']); } catch (\Exception $e) {}
            $cols = ['contract_number', 'currency_id', 'currency_code', 'exchange_rate', 'salary_usd', 'salary_currency'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('employee_salaries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
