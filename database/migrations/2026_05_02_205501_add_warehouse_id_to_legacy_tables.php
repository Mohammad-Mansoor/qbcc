<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToLegacyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->default(1)->after('transport_id');
        });

        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->default(1)->after('status');
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
            $table->dropColumn('warehouse_id');
        });

        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->dropColumn('warehouse_id');
        });
    }
}
