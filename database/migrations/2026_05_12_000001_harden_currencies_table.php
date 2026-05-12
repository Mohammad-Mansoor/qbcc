<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HardenCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            // Drop legacy amount column if it exists
            if (Schema::hasColumn('currencies', 'amount')) {
                $table->dropColumn('amount');
            }

            // Add new hardened columns
            $table->string('code', 3)->unique()->after('id');
            $table->string('name')->after('code');
            $table->string('symbol', 10)->after('name');
            $table->decimal('exchange_rate', 18, 8)->default(1)->after('symbol');
            $table->boolean('is_base_currency')->default(false)->after('exchange_rate');
            $table->boolean('is_active')->default(true)->after('is_base_currency');
            $table->integer('decimal_precision')->default(2)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('amount')->nullable()->after('id');
            $table->dropColumn([
                'code', 'name', 'symbol', 'exchange_rate', 
                'is_base_currency', 'is_active', 'decimal_precision'
            ]);
        });
    }
}
