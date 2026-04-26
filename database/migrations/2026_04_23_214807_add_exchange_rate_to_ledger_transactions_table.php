<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeRateToLedgerTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('ledger_transactions', function (Blueprint $table) {
            $table->decimal('exchange_rate', 15, 4)->default(1.0000)->after('description');
        });
    }

    public function down()
    {
        Schema::table('ledger_transactions', function (Blueprint $table) {
            $table->dropColumn('exchange_rate');
        });
    }
}
