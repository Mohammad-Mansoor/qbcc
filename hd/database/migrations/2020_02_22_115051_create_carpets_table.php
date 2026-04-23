<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpets', function (Blueprint $table) {
            $table->bigIncrements('carpet_id');
            $table->string('carpet_no');
            $table->string('width')->nullable()->default(0);
            $table->string('height')->nullable()->default(0);
            $table->string('area')->nullable()->default(0);
            $table->string('field' )->nullable();
            $table->string('margin')->nullable();
            $table->string('price')->nullable()->default(0);
            $table->string('carpet_price')->nullable()->default(0);
            $table->string('carpet_price_us')->nullable()->default(0);
            $table->string('total_price_af')->nullable()->default(0);
            $table->string('total_price')->nullable()->default(0);
            $table->string('map_number')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('kachaee_id')->nullable();
            $table->unsignedBigInteger('washing_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->unsignedBigInteger('quality_id')->nullable();
            $table->double('status');
            $table->unsignedBigInteger('transport_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('agent_employees');
            $table->foreign('agent_id')->references('agent_id')->on('agents');
            $table->foreign('order_id')->references('id')->on('carpet_orders');
            $table->foreign('type_id')->references('carpet_type_id')->on('carpet_types');
            $table->foreign('kachaee_id')->references('id')->on('kachaees');
            $table->foreign('washing_id')->references('id')->on('washing_teams');
            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('quality_id')->references('id')->on('qualities');
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
        Schema::dropIfExists('carpets');
    }
}
