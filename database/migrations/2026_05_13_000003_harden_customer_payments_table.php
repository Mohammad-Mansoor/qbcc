<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HardenCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            // Forensic Multi-Currency Columns
            $table->string('currency_code', 3)->nullable()->after('type');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');
            $table->decimal('original_amount', 19, 4)->nullable()->after('exchange_rate');
            $table->decimal('base_amount', 19, 4)->nullable()->after('original_amount');
            
            // Note: We keep dollar_rate, amount, and amount_af for legacy compatibility 
            // until the Great Normalization script is run.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'original_amount', 'base_amount']);
        });
    }
}
