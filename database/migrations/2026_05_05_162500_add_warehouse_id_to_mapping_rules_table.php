<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToMappingRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mapping_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('mapping_rules', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('credit_account_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapping_rules', function (Blueprint $table) {
            if (Schema::hasColumn('mapping_rules', 'warehouse_id')) {
                $table->dropColumn('warehouse_id');
            }
        });
    }
}
