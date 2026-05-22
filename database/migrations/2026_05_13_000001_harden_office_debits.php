<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HardenOfficeDebits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('office_debits', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->after('amount_af');
            $table->string('currency_code', 3)->nullable()->after('currency_id');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');
            $table->decimal('original_amount', 18, 4)->nullable()->after('exchange_rate');
            $table->decimal('base_amount', 18, 4)->nullable()->after('original_amount');
            $table->unsignedBigInteger('override_debit_account_id')->nullable()->after('base_amount');
            $table->unsignedBigInteger('override_credit_account_id')->nullable()->after('override_debit_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('office_debits', function (Blueprint $table) {
            $table->dropColumn([
                'currency_id', 'currency_code', 'exchange_rate', 
                'original_amount', 'base_amount',
                'override_debit_account_id', 'override_credit_account_id'
            ]);
        });
    }
}
