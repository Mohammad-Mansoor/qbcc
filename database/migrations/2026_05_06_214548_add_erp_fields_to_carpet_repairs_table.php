<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddErpFieldsToCarpetRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpet_repairs', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('AFN')->after('af_total_price');
            $table->decimal('exchange_rate', 15, 6)->nullable()->after('currency_code');
            $table->decimal('base_currency_amount', 15, 2)->nullable()->after('exchange_rate')->comment('Value in USD');
            $table->unsignedBigInteger('inventory_transaction_id')->nullable()->after('base_currency_amount');
            
            $table->foreign('inventory_transaction_id')
                  ->references('id')
                  ->on('inventory_transactions')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpet_repairs', function (Blueprint $table) {
            $table->dropForeign(['inventory_transaction_id']);
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_currency_amount', 'inventory_transaction_id']);
        });
    }
}
