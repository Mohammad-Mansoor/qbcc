<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_order_details', function (Blueprint $table) {
            $table->bigIncrements('cod_id');
            $table->string('quality')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('area')->nullable();
            $table->string('warp')->nullable();
            $table->string('weft')->nullable();
            $table->string('wash_type')->nullable();
            $table->string('pile_height')->nullable();
            $table->string('weaver_code')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('photo')->nullable();
            $table->string('current_status')->nullable();
            $table->unsignedBigInteger('customer_order_id');
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
        Schema::dropIfExists('customer_order_details');
    }
}
