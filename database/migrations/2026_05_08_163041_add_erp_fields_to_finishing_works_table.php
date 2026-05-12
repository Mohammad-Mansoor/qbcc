<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddErpFieldsToFinishingWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_works', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('price_af');
            $table->decimal('exchange_rate', 15, 4)->default(1.0000)->after('currency_code');
            $table->decimal('base_currency_amount', 15, 2)->default(0.00)->after('exchange_rate');
            $table->bigInteger('inventory_transaction_id')->unsigned()->nullable()->after('base_currency_amount');
            $table->tinyInteger('status')->default(0)->after('category_id'); // 0: Pending, 1: Approved
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finishing_works', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_currency_amount', 'inventory_transaction_id', 'status']);
        });
    }
}
