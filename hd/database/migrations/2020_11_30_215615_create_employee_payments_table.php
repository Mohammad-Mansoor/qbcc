<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('contract_no');
            $table->double('amount');
            $table->double('amount_af');
            $table->LongText('description');
            $table->date('date');
            $table->string('type');
            $table->string('dollar_rate');
            $table->unsignedInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('office_employees')->onUpdate('cascade');
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
        Schema::dropIfExists('employee_payments');
    }
}
