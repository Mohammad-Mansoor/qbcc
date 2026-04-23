<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeCashBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_cash_books', function (Blueprint $table) {
            $table->increments('id');
            $table->double('balance');
            $table->unsignedInteger('debit_id')->nullable();
            $table->string('user_role')->nullable();
            $table->foreign('debit_id')->references('id')->on('office_debits');
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
        Schema::dropIfExists('office_cash_books');
    }
}
