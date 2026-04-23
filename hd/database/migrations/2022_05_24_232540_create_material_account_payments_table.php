<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialAccountPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_account_payments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('amount');
            $table->string('description');
            $table->date('date');
            $table->unsignedBigInteger('account_id');
            $table->unsignedInteger('type_id');
            $table->string('type');
            $table->tinyInteger('status');
            $table->foreign('type_id')->references('material_type_id')->on('material_types');
            $table->foreign('account_id')->references('id')->on('material_accounts');
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
        Schema::dropIfExists('material_account_payments');
    }
}
