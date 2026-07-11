<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLifecycleDimensionsToCarpetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->string('buying_width')->nullable()->after('area');
            $table->string('buying_height')->nullable()->after('buying_width');
            $table->string('buying_area')->nullable()->after('buying_height');

            $table->string('washed_width')->nullable()->after('buying_area');
            $table->string('washed_height')->nullable()->after('washed_width');
            $table->string('washed_area')->nullable()->after('washed_height');
        });

        // Copy existing data to preserve history for old carpets
        DB::statement('UPDATE carpets SET buying_width = width, buying_height = height, buying_area = area, washed_width = width, washed_height = height, washed_area = area');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropColumn([
                'buying_width',
                'buying_height',
                'buying_area',
                'washed_width',
                'washed_height',
                'washed_area',
            ]);
        });
    }
}
