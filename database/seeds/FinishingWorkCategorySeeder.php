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
        $categories = [
            ['id' => 1, 'category' => 'قیتان'],
            ['id' => 2, 'category' => 'رفو'],
            ['id' => 3, 'category' => 'چیت'],
            ['id' => 4, 'category' => 'لبکی'],
            ['id' => 5, 'category' => 'پوپک'],
            ['id' => 6, 'category' => 'کش'],
            ['id' => 7, 'category' => 'رنگ'],
            ['id' => 8, 'category' => 'شیرازه'],
            ['id' => 9, 'category' => 'کنترول کیفیت'],
        ];

        foreach ($categories as $category) {
            DB::table('finishing_team_categories')->updateOrInsert(
                ['id' => $category['id']],
                ['category' => $category['category']]
            );
        }
    }
}
