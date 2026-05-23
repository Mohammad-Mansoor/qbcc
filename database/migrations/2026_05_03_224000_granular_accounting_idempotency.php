<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GranularAccountingIdempotency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Try to drop the too-strict index if it exists (it might exist on some databases but not on fresh ones)
        try {
            Schema::table('ledger_transactions', function (Blueprint $table) {
                $table->dropUnique('ledger_tx_source_unique');
            });
        } catch (\Exception $e) {
            // Silent fallback if index doesn't exist
        }

        Schema::table('ledger_transactions', function (Blueprint $table) {
            // 2. Add mapping_key to track the "Intent" of the transaction
            if (!Schema::hasColumn('ledger_transactions', 'mapping_key')) {
                $table->string('mapping_key')->nullable()->after('source_id')->index();
            }
            
            // 3. Create a more granular unique index
            // This allows Invoice #101 to have multiple financial events 
            // (e.g. Revenue, Tax, COGS) but prevents DUPLICATE Revenue for Invoice #101.
            $table->unique(['source_type', 'source_id', 'mapping_key'], 'ledger_tx_granular_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledger_transactions', function (Blueprint $table) {
            $table->dropUnique('ledger_tx_granular_unique');
            $table->dropColumn('mapping_key');
            $table->unique(['source_type', 'source_id'], 'ledger_tx_source_unique');
        });
    }
}
