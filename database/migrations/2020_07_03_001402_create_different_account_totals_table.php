<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDifferentAccountTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('different_account_totals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('total');
            $table->string('paid');
            $table->string('remaining');
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('different_accounts');
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
        Schema::dropIfExists('different_account_totals');
    }
}
