<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpet_transports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('transport_price');
            $table->LongText('description');
            $table->Integer('carpet_quantity');
            $table->unsignedInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('office_employees')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('carpet_transports');
    }
}
