<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->increments('co_id');
            $table->string('customer_order');
            $table->string('quality');
            $table->string('height');
            $table->string('width');
            $table->string('area');
            $table->string('warp');
            $table->string('weft');
            $table->string('wash_type');
            $table->string('pile_height');
            $table->string('weaver_code');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('current_status');
            $table->string('photo');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('c_id')->on('customer_account_orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_orders');
    }
}
