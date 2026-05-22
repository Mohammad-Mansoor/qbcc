<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyColumnsToOfficeCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('office_credits', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->after('status');
            $table->string('currency_code')->nullable()->after('currency_id');
            $table->decimal('exchange_rate', 15, 6)->nullable()->after('currency_code');
            $table->decimal('original_amount', 15, 2)->nullable()->after('exchange_rate');
            $table->decimal('base_amount', 15, 2)->nullable()->after('original_amount');

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
        Schema::table('office_credits', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'currency_code', 'exchange_rate', 'original_amount', 'base_amount']);
        });
    }
}
