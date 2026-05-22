<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HardenExpenseBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_monthly_expense_balances', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('category');
            $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            $table->decimal('exchange_rate', 18, 8)->default(1.00000000)->after('currency_symbol');
            $table->decimal('original_amount', 18, 4)->default(0.0000)->after('exchange_rate');
            $table->decimal('base_amount', 18, 4)->default(0.0000)->after('original_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_monthly_expense_balances', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'exchange_rate', 'original_amount', 'base_amount']);
        });
    }
}
