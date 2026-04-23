<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_credits', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount');
            $table->LongText('description');
            $table->date('date');
            $table->string('user_role')->nullable();
            $table->string('status')->nullable();
            $table->unsignedInteger('payment_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
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
        Schema::dropIfExists('office_credits');
    }
}
