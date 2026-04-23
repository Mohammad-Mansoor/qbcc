<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('sale_date');
            $table->double('sale_cost_per_meter');
            $table->double('sale_cost_total');
            $table->double('profit');
            $table->string('description');
            $table->string('quality');
            $table->string('type');
            $table->unsignedBigInteger('carpet_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('carpet_id')->references('carpet_id')->on('carpets')->onUpdate('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onUpdate('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade');
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
        Schema::dropIfExists('sales');
    }
}
