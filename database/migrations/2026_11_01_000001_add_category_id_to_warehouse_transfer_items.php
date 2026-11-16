<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToWarehouseTransferItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('warehouse_transfer_items') && !Schema::hasColumn('warehouse_transfer_items', 'category_id')) {
            Schema::table('warehouse_transfer_items', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('ref_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('warehouse_transfer_items') && Schema::hasColumn('warehouse_transfer_items', 'category_id')) {
            Schema::table('warehouse_transfer_items', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }
    }
}
