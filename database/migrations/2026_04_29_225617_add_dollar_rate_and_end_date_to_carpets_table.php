<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDollarRateAndEndDateToCarpetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->string('dollar_rate')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropColumn(['dollar_rate', 'end_date']);
        });
    }
}
