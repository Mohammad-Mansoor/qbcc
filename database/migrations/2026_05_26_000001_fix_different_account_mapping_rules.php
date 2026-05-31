<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixDifferentAccountMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update different_account رسید rule to mapping_key = DIFF_IN
        DB::table('mapping_rules')
            ->where('transaction_type', 'different_account')
            ->where('condition', 'رسید')
            ->update([
                'mapping_key' => 'DIFF_IN',
                'updated_at' => now(),
            ]);

        // Update different_account گرفت rule to mapping_key = DIFF_OUT
        DB::table('mapping_rules')
            ->where('transaction_type', 'different_account')
            ->where('condition', 'گرفت')
            ->update([
                'mapping_key' => 'DIFF_OUT',
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
        // Revert to original PYMT_IN/PYMT_OUT
        DB::table('mapping_rules')
            ->where('transaction_type', 'different_account')
            ->where('condition', 'رسید')
            ->update([
                'mapping_key' => 'PYMT_IN',
                'updated_at' => now(),
            ]);

        DB::table('mapping_rules')
            ->where('transaction_type', 'different_account')
            ->where('condition', 'گرفت')
            ->update([
                'mapping_key' => 'PYMT_OUT',
                'updated_at' => now(),
            ]);
    }
}
