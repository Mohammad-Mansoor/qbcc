<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToCustomerOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_order_details', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('total_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('customer_order_details', 'currency_id')) {
                $table->dropColumn('currency_id');
            }
        });
    }
}
