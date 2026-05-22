<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HardenCustomerPaymentsCurrencyId extends Migration
{
    public function up()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_payments', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('ledger_transaction_id');
            }
        });

        // Add foreign key constraint
        try {
            Schema::table('customer_payments', function (Blueprint $table) {
                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }

        // Backfill currency_id based on currency_code mapping
        $currencies = DB::table('currencies')->get();
        foreach ($currencies as $currency) {
            DB::table('customer_payments')
                ->where('currency_code', $currency->code)
                ->whereNull('currency_id')
                ->update(['currency_id' => $currency->id]);
        }

        // For any remaining nulls (e.g., legacy payments before currency was set)
        // Check base currency or default to USD/AFN
        $baseCurrency = DB::table('currencies')->where('is_base_currency', 1)->first() 
            ?? DB::table('currencies')->where('code', 'USD')->first()
            ?? DB::table('currencies')->first();

        if ($baseCurrency) {
            DB::table('customer_payments')
                ->whereNull('currency_id')
                ->update([
                    'currency_id' => $baseCurrency->id,
                    'currency_code' => $baseCurrency->code,
                    'exchange_rate' => $baseCurrency->exchange_rate ?? 1.0,
                ]);
        }
    }

    public function down()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            try {
                $table->dropForeign(['currency_id']);
            } catch (\Exception $e) {}
            
            if (Schema::hasColumn('customer_payments', 'currency_id')) {
                $table->dropColumn('currency_id');
            }
        });
    }
}
