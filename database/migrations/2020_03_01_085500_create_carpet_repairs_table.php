<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpet_repairs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->LongText('description');
            $table->string('kachaee_number');
            $table->string('price');
            $table->string('total_price');
            $table->string('af_total_price');
            $table->date('date');
            $table->unsignedBigInteger('carpetId');
            $table->unsignedBigInteger('team_id');
            
            $table->foreign('carpetId')->references('carpet_id')->on('carpets')->onUpdate('cascade');
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
        Schema::dropIfExists('carpet_repairs');
    }
}
