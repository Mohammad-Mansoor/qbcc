<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMaterialPurchaseMappingRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Get Inventory account (1400)
        $inventoryAcc = DB::table('chart_of_accounts')->where('account_code', '1400')->first();
        
        if ($inventoryAcc) {
            // Update the debit_account_id for MATERIAL_PURCHASE_CREDIT
            DB::table('mapping_rules')
                ->where('transaction_type', 'material_purchase')
                ->where('condition', 'credit')
                ->update([
                    'debit_account_id' => $inventoryAcc->id,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Get COGS account (5100) or fallback (5000)
        $cogsAcc = DB::table('chart_of_accounts')->where('account_code', '5100')->first();
        if (!$cogsAcc) {
            $cogsAcc = DB::table('chart_of_accounts')->where('account_code', '5000')->first();
        }

        if ($cogsAcc) {
            // Revert the debit_account_id for MATERIAL_PURCHASE_CREDIT
            DB::table('mapping_rules')
                ->where('transaction_type', 'material_purchase')
                ->where('condition', 'credit')
                ->update([
                    'debit_account_id' => $cogsAcc->id,
                    'updated_at' => now(),
                ]);
        }
    }
}
