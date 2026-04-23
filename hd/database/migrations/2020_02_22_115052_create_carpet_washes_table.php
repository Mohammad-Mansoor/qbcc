<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetWashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpet_washes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wash_number');
            $table->string('wash_number_sh')->nullable();
            $table->string('price')->nullable();
            $table->string('total_price')->nullable();
            $table->string('af_total_price')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('area',64)->nullable();
            $table->date('date')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('carpetId');
            $table->unsignedBigInteger('team_id');
            $table->foreign('carpetId')->references('carpet_id')->on('carpets');
            $table->foreign('team_id')->references('id')->on('washing_teams');
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
        Schema::dropIfExists('carpet_washes');
    }
}
