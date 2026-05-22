<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyColumnsToAjnasAccountDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ajnas_account_details', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->after('acquisition_cost');
            $table->string('currency_code', 10)->nullable()->after('currency_id');
            $table->decimal('exchange_rate', 15, 6)->nullable()->after('currency_code');
            $table->decimal('original_amount', 15, 2)->nullable()->after('exchange_rate');

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ajnas_account_details', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'currency_code', 'exchange_rate', 'original_amount']);
        });
    }
}
