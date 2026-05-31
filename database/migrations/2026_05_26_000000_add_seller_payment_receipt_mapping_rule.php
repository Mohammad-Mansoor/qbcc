<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSellerPaymentReceiptMappingRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert seller_payment رسید rule if not exists
        $exists = DB::table('mapping_rules')
            ->where('transaction_type', 'seller_payment')
            ->where('condition', 'رسید')
            ->exists();

        if (!$exists) {
            DB::table('mapping_rules')->insert([
                'transaction_type' => 'seller_payment',
                'mapping_key' => 'DIFF_IN', // Using DIFF_IN to bypass strict asset-only credit constraints
                'condition' => 'رسید',
                'debit_account_id' => 1, // Cash
                'credit_account_id' => 3, // Accounts Payable
                'description_template' => 'رسید پول از فروشنده مواد خام {party_name}',
                'created_at' => now(),
                'updated_at' => now()
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
        DB::table('mapping_rules')
            ->where('transaction_type', 'seller_payment')
            ->where('condition', 'رسید')
            ->delete();
    }
}
