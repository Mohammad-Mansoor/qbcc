<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetCheckBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpet_check_books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('height',64);
            $table->string('width',64);
            $table->string('area',64);
            $table->string('widthwaste',64)->nullable();
            $table->string('heightwaste',64)->nullable(); 
            $table->string('check_number'); 
            $table->string('kachaee_amount')->default(0)->nullable();
            $table->string('kachaee_dollar_amount')->default(0)->nullable();
            $table->date('date');
            $table->unsignedBigInteger('carpet_id');
            $table->unsignedBigInteger('agent_id');
            $table->foreign('carpet_id')->references('carpet_id')->on('carpets');
            $table->foreign('agent_id')->references('agent_id')->on('agents');
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
        Schema::dropIfExists('carpet_check_books');
    }
}
