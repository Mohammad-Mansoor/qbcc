<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinishingWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finishing_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('finish_number');
            $table->string('price');
            $table->string('price_af');
            $table->longText('description');
            $table->date('date');
            $table->unsignedBigInteger('carpetId');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('carpetId')->references('carpet_id')->on('carpets');
            $table->foreign('team_id')->references('id')->on('finishing_teams');
            $table->foreign('category_id')->references('id')->on('finishing_team_categories');
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
        Schema::dropIfExists('finishing_works');
    }
}
