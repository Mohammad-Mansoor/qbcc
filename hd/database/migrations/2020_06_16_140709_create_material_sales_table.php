<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_sales', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('sale_number');
            $table->double('amount');
            $table->double('price');
            $table->double('total_price');
            $table->double('total_price_af');
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('category_id');
            $table->unsignedBigInteger('agent_id');
            $table->foreign('type_id')->references('material_type_id')->on('material_types');
            $table->foreign('category_id')->references('material_category_id')->on('material_categories');
            $table->foreign('agent_id')->references('agent_id')->on('agents');
            $table->date('date');
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
        Schema::dropIfExists('material_sales');
    }
}
