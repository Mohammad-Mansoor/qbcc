<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinishingWorkCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('finishing_team_categories')->insert([
            ['category' => 'قیتان'],
            ['category' => 'رفو'],
            ['category' => 'چیت'],
            ['category' => 'لبکی'],
            ['category' => 'پوپک'],
            ['category' => 'کش'],
            ['category' => 'رنگ'],

        ]);
    }
}
