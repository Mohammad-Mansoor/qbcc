<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitPriceToFinishingWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_works', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 4)->nullable()->after('price_af');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finishing_works', function (Blueprint $table) {
            $table->dropColumn('unit_price');
        });
    }
}
