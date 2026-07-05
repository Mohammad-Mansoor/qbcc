<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaDifferenceToCarpetWashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpet_washes', function (Blueprint $table) {
            $table->decimal('area_difference', 10, 4)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpet_washes', function (Blueprint $table) {
            $table->dropColumn('area_difference');
        });
    }
}
