<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSubtypeToWarehousesAndMaterialTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add subtype to warehouses table
        if (!Schema::hasColumn('warehouses', 'subtype')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->enum('subtype', ['carpet', 'yarn', 'dye'])->default('carpet')->after('is_active');
            });
        }

        // 2. Add subtype to material_types table
        if (!Schema::hasColumn('material_types', 'subtype')) {
            Schema::table('material_types', function (Blueprint $table) {
                $table->enum('subtype', ['yarn', 'dye'])->default('yarn')->after('material_type');
            });
        }

        // 3. Non-destructive data migration step
        // Resolve subtype based on historic transactions to prevent locking out existing stock
        $warehouses = DB::table('warehouses')->get();
        foreach ($warehouses as $warehouse) {
            $hasMaterials = DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->where('inventory_transactions.warehouse_id', $warehouse->id)
                ->where('items.type', 'App\MaterialType')
                ->exists();

            if ($hasMaterials) {
                DB::table('warehouses')
                    ->where('id', $warehouse->id)
                    ->update(['subtype' => 'yarn']);
            } else {
                DB::table('warehouses')
                    ->where('id', $warehouse->id)
                    ->update(['subtype' => 'carpet']);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('warehouses', 'subtype')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropColumn('subtype');
            });
        }

        if (Schema::hasColumn('material_types', 'subtype')) {
            Schema::table('material_types', function (Blueprint $table) {
                $table->dropColumn('subtype');
            });
        }
    }
}
