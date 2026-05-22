<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class HardenPurchaseMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_materials', function (Blueprint $table) {
            // Forensic snapshots
            $table->unsignedBigInteger('currency_id')->nullable()->after('ledger_transaction_id');
            $table->string('currency_code', 3)->nullable()->after('currency_id');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');
            $table->decimal('original_amount', 18, 4)->nullable()->after('exchange_rate');
            $table->decimal('base_currency_amount', 18, 4)->nullable()->after('original_amount');
            
            // Note: We keep price_per_kilo, total, and total_af for legacy compatibility
        });

        // Optional: Backfill existing data if possible
        // For simplicity, we'll leave legacy records as is for now.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->dropColumn([
                'currency_id',
                'currency_code',
                'exchange_rate',
                'original_amount',
                'base_currency_amount'
            ]);
        });
    }
}
