<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddBaseDebitCreditToLedgerEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->decimal('base_debit', 19, 4)->default(0.0000)->after('base_currency_amount');
            $table->decimal('base_credit', 19, 4)->default(0.0000)->after('base_debit');
        });

        // Backfill existing data for consistency
        DB::statement("UPDATE ledger_entries SET base_debit = base_currency_amount WHERE debit > 0");
        DB::statement("UPDATE ledger_entries SET base_credit = base_currency_amount WHERE credit > 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn(['base_debit', 'base_credit']);
        });
    }
}
