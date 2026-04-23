<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAjnasAccountDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajnas_account_details', function (Blueprint $table) {
            $table->bigIncrements('aad_id');
            $table->string('asset_name');
            $table->string('asset_class');
            $table->string('asset_description');
            $table->string('physical_location');
            $table->string('asset_number');
            $table->string('asset_serial_number');
            $table->string('acquisition_date');
            $table->string('acquisition_cost');
            $table->string('estimated_useful_life');
            $table->string('estimated_salvage_value');
            $table->unsignedBigInteger('ajnas_account_id');
            $table->foreign('ajnas_account_id')->references('aa_id')->on('ajnas_accounts');
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
        Schema::dropIfExists('ajnas_account_details');
    }
}
