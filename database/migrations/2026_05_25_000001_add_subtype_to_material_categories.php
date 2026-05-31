<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSubtypeToMaterialCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('material_categories', 'subtype')) {
            Schema::table('material_categories', function (Blueprint $table) {
                $table->enum('subtype', ['yarn', 'dye'])->default('yarn')->after('material_category');
            });
        }

        // Backfill existing categories (Grade A, تار پشم, تار ابریشم) to 'yarn'
        DB::table('material_categories')
            ->whereIn('material_category_id', [1, 2, 3])
            ->update(['subtype' => 'yarn']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('material_categories', 'subtype')) {
            Schema::table('material_categories', function (Blueprint $table) {
                $table->dropColumn('subtype');
            });
        }
    }
}
