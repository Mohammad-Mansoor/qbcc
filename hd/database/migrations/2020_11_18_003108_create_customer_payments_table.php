<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('amount');
            $table->double('amount_af');
            $table->LongText('description');
            $table->date('date');
            $table->string('type');
            $table->string('dollar_rate');
            $table->unsignedBigInteger('customer_id');
            $table->string('invoice_number');
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
        Schema::dropIfExists('customer_payments');
    }
}
