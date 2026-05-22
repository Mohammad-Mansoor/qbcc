<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class HardenInventoryValuation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Upgrade precision of inventory_transactions
        DB::statement("ALTER TABLE inventory_transactions MODIFY unit_cost DECIMAL(19, 4) DEFAULT 0.0000");
        DB::statement("ALTER TABLE inventory_transactions MODIFY total_cost DECIMAL(19, 4) DEFAULT 0.0000");

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('unit_cost');
            $table->decimal('exchange_rate', 19, 12)->default(1.000000000000)->after('currency_code');
            $table->decimal('base_unit_cost', 19, 4)->default(0.0000)->after('exchange_rate');
        });

        // 2. Upgrade precision of items table (WAC storage)
        DB::statement("ALTER TABLE items MODIFY current_cost DECIMAL(19, 4) DEFAULT 0.0000");

        // Backfill: Assume existing costs are in Base Currency
        DB::statement("UPDATE inventory_transactions SET base_unit_cost = unit_cost");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_unit_cost']);
        });
    }
}
