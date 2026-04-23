<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_materials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_number');
            $table->unsignedInteger('material_type');
            $table->unsignedInteger('material_category');
            $table->unsignedInteger('seller_id');
            $table->double('quantity');
            $table->double('price_per_kilo');
            $table->double('total');
            $table->double('total_af');
            $table->string('in_words');
            $table->date('purchase_date');
            $table->foreign('seller_id')->references('id')->on('string_sellers')->onUpdate('cascade');
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
        Schema::dropIfExists('purchase_materials');
    }
}
