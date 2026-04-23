<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeDebitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_debits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->double('amount');
            $table->double('amount_af')->nullable();
            $table->LongText('description');
            $table->date('date');
            $table->unsignedInteger('employee_id')->nullable();
            $table->string('expense_type');
            $table->string('expense_for_where');
            $table->string('user_role')->nullable();
            $table->unsignedInteger('credit_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('office_employees');
            $table->foreign('credit_id')->references('id')->on('office_credits');
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
        Schema::dropIfExists('office_debits');
    }
}
