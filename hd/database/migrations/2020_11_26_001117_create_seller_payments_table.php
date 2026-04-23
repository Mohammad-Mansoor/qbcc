<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount');
            $table->double('amount_af');
            $table->LongText('description');
            $table->date('date');
            $table->string('type');
            $table->string('dollar_rate');
            $table->unsignedInteger('seller_id');
            $table->string('purchase_number');
            $table->foreign('seller_id')->references('id')->on('string_sellers')->onUpdate('cascade');
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
        Schema::dropIfExists('seller_payments');
    }
}
