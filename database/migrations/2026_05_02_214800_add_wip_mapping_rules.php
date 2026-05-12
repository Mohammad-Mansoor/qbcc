<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddWipMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. DR WIP (14) / CR Inventory (8) - Production Start
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'production_start', 'condition' => 'transfer'],
            [
                'debit_account_id' => 14,
                'credit_account_id' => 8,
                'description_template' => 'Production Start: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. DR WIP (14) / CR Payable (3) - Production Service (Washing/Finishing)
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'production_service', 'condition' => 'credit'],
            [
                'debit_account_id' => 14,
                'credit_account_id' => 3,
                'description_template' => 'Service Cost: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. DR Finished Goods (15) / CR WIP (14) - Production Completion
        DB::table('mapping_rules')->updateOrInsert(
            ['transaction_type' => 'production_completion', 'condition' => 'transfer'],
            [
                'debit_account_id' => 15,
                'credit_account_id' => 14,
                'description_template' => 'Production Completion: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Update washing and finishing to use WIP if desired, 
        // but user specifically asked for these 3 flows.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('mapping_rules')->whereIn('transaction_type', ['production_start', 'production_service', 'production_completion'])->delete();
    }
}
