<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpet_materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('amount');
            $table->string('price');
            $table->string('total_price');
            $table->string('total_price_af');
            $table->date('date');
            $table->unsignedBigInteger('carpet_id');
            $table->unsignedBigInteger('agent_id'); 
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('type_id');
            
            $table->foreign('agent_id')->references('agent_id')->on('agents')->onUpdate('cascade');
            $table->foreign('carpet_id')->references('carpet_id')->on('carpets')->onUpdate('cascade');
            $table->foreign('category_id')->references('material_category_id')->on('material_categories')->onUpdate('cascade');
            $table->foreign('type_id')->references('material_type_id')->on('material_types')->onUpdate('cascade');
        
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
        Schema::dropIfExists('carpet_materials');
    }
}
