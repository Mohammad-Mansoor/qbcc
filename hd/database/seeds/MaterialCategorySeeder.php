<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('material_categories')->insert([
           [ 'material_category' => 'تار پخته'],
           [ 'material_category' => 'تار پشم'],
           [ 'material_category' => 'تار ابریشم'],

        ]);
    }
}
