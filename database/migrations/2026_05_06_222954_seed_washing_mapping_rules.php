<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedWashingMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add washing_transfer for sending carpet to wash
        \Illuminate\Support\Facades\DB::table('mapping_rules')->insert([
            'transaction_type' => 'washing_transfer',
            'condition' => 'transfer',
            'debit_account_id' => 1, // WIP - Washing (To be configured)
            'credit_account_id' => 1, // Main Inventory (To be configured)
            'description_template' => 'انتقال به گدام شست (Washing Transfer)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::table('mapping_rules')->where('transaction_type', 'washing_transfer')->delete();
    }
}
