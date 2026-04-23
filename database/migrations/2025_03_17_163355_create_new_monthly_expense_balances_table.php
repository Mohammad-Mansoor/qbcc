<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewMonthlyExpenseBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_monthly_expense_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount');
            $table->tinyInteger('currency');
            $table->LongText('description');
            $table->date('date');
            $table->string('category');
            $table->string('dollar_rate');
            $table->string('user_role');
            $table->unsignedInteger('month_id');
            $table->foreign('month_id')->references('me_id')->on('new_monthly_expenses');
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
        Schema::dropIfExists('new_monthly_expense_blanaces');
    }
}
