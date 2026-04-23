<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('material_type');
            $table->unsignedInteger('material_category');
            $table->double('quantity');
            $table->double('price_per_kilo');
            $table->string('in_words');
            $table->foreign('material_type')->references('material_type_id')->on('material_types')->onUpdate('cascade');
            $table->foreign('material_category')->references('material_category_id')->on('material_categories')->onUpdate('cascade');
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
        Schema::dropIfExists('material_stocks');
    }
}
