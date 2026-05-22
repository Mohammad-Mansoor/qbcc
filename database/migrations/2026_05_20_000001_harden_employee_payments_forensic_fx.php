<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HardenEmployeePaymentsForensicFx extends Migration
{
    public function up()
    {
        Schema::table('employee_payments', function (Blueprint $table) {
            // Rename legacy contract_no → contract_number if needed (safe: only if contract_number doesn't exist)
            if (!Schema::hasColumn('employee_payments', 'contract_number')) {
                $table->string('contract_number')->nullable()->after('id');
            }

            // Forensic FX columns
            if (!Schema::hasColumn('employee_payments', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('dollar_rate');
            }
            if (!Schema::hasColumn('employee_payments', 'currency_code')) {
                $table->string('currency_code', 10)->nullable()->after('currency_id');
            }
            if (!Schema::hasColumn('employee_payments', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');
            }
            if (!Schema::hasColumn('employee_payments', 'original_amount')) {
                $table->decimal('original_amount', 18, 4)->nullable()->after('exchange_rate');
            }
            if (!Schema::hasColumn('employee_payments', 'base_currency_amount')) {
                $table->decimal('base_currency_amount', 18, 4)->nullable()->after('original_amount');
            }
        });

        // Add FK for currency_id (separate call to avoid issues if column already existed)
        try {
            Schema::table('employee_payments', function (Blueprint $table) {
                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // FK may already exist — skip silently
        }

        // Backfill existing rows: set exchange_rate from dollar_rate string, compute base amounts
        DB::statement("
            UPDATE employee_payments
            SET
                currency_code = CASE WHEN amount > 0 THEN 'USD' ELSE 'AFN' END,
                exchange_rate = CASE
                    WHEN dollar_rate REGEXP '^[0-9]+(\.[0-9]+)?$' THEN CAST(dollar_rate AS DECIMAL(18,8))
                    ELSE 1.0
                END,
                original_amount = CASE WHEN amount > 0 THEN amount ELSE amount_af END,
                base_currency_amount = CASE
                    WHEN amount > 0 THEN amount
                    ELSE CASE
                        WHEN dollar_rate REGEXP '^[0-9]+(\.[0-9]+)?$' AND CAST(dollar_rate AS DECIMAL(18,8)) > 0
                            THEN amount_af / CAST(dollar_rate AS DECIMAL(18,8))
                        ELSE amount_af
                    END
                END
            WHERE currency_code IS NULL
        ");

        // Seed the PAYROLL_PAYMENT mapping rule if not already present
        $exists = DB::table('mapping_rules')->where('mapping_key', 'PAYROLL_PAYMENT')->exists();
        if (!$exists) {
            DB::table('mapping_rules')->insert([
                [
                    'transaction_type'    => 'employee_payment',
                    'condition'           => 'گرفت',
                    'mapping_key'         => 'PAYROLL_PAYMENT',
                    'debit_account_id'    => 3,  // Accounts Payable / Payroll Liability
                    'credit_account_id'   => 1,  // Cash / Bank
                    'description_template'=> 'معاش کارمند: {reference}',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ],
            ]);
        }
    }

    public function down()
    {
        Schema::table('employee_payments', function (Blueprint $table) {
            try { $table->dropForeign(['currency_id']); } catch (\Exception $e) {}
            $cols = ['currency_id', 'currency_code', 'exchange_rate', 'original_amount', 'base_currency_amount'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('employee_payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        DB::table('mapping_rules')->where('mapping_key', 'PAYROLL_PAYMENT')->delete();
    }
}
