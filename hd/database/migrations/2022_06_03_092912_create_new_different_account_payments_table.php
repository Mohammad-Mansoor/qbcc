<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewDifferentAccountPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_different_account_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('amount');
            $table->tinyInteger('currency');
            $table->string('description');
            $table->date('date');
            $table->unsignedBigInteger('account_id');
            $table->string('type');
            $table->string('insert_credit')->nullable();
            $table->tinyInteger('status');
            $table->foreign('account_id')->references('id')->on('new_different_accounts');
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
        Schema::dropIfExists('new_different_account_payments');
    }
}
