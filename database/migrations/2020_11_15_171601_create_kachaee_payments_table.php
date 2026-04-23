<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKachaeePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kachaee_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('amount');
            $table->double('amount_af');
            $table->LongText('description');
            $table->date('date');
            $table->string('type');
            $table->string('dollar_rate');
            $table->unsignedBigInteger('team_id');
            $table->string('kachaee_number');
            $table->foreign('team_id')->references('id')->on('kachaees')->onUpdate('cascade');
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
        Schema::dropIfExists('kachaee_payments');
    }
}
