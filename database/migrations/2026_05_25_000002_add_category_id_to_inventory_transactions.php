<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCategoryIdToInventoryTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('inventory_transactions', 'category_id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('warehouse_id');
            });
        }

        // Backpopulate existing transactions using COALESCE join logic
        DB::statement("
            UPDATE inventory_transactions it
            LEFT JOIN purchase_materials pm ON it.reference_id = pm.id AND it.reference_type = 'App\\\\PurchaseMaterial'
            LEFT JOIN material_sales ms ON it.reference_id = ms.id AND it.reference_type = 'App\\\\MaterialSale'
            LEFT JOIN carpet_materials cm ON it.reference_id = cm.id AND it.reference_type = 'App\\\\CarpetMaterial'
            LEFT JOIN (
                SELECT material_type, MIN(material_category) as material_category 
                FROM material_stocks 
                GROUP BY material_type
            ) mstk ON (pm.material_type = mstk.material_type OR ms.type_id = mstk.material_type OR cm.type_id = mstk.material_type)
            SET it.category_id = COALESCE(pm.material_category, ms.category_id, cm.category_id, mstk.material_category)
            WHERE it.reference_type IN ('App\\\\PurchaseMaterial', 'App\\\\MaterialSale', 'App\\\\CarpetMaterial')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('inventory_transactions', 'category_id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }
    }
}
