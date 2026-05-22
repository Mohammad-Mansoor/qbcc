<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HardenLaborPaymentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('washing_payments', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('type');
            $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_symbol');
            $table->decimal('original_amount', 18, 4)->nullable()->after('exchange_rate');
            $table->decimal('base_amount', 18, 4)->nullable()->after('original_amount');
        });

        Schema::table('finishing_team_payments', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('type');
            $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_symbol');
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
        Schema::table('washing_payments', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'exchange_rate', 'original_amount', 'base_amount']);
        });

        Schema::table('finishing_team_payments', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'exchange_rate', 'original_amount', 'base_amount', 'override_debit_account_id', 'override_credit_account_id']);
        });
    }
}
