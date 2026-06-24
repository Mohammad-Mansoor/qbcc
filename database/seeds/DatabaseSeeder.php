<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(PermissionSeeder::class);
         $this->call(UsersTableSeeder::class);
         $this->call(CurrencySeeder::class);
         $this->call(FinishingWorkCategorySeeder::class);
         $this->call(MaterialCategorySeeder::class);
         $this->call(ChartOfAccountsSeeder::class);
         $this->call(MappingRulesSeeder::class);
         $this->call(ERPMappingRulesSeeder::class);
         $this->call(LaborPaymentMappingSeeder::class);
    }
}
