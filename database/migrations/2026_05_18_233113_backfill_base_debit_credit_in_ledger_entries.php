<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BackfillBaseDebitCreditInLedgerEntries extends Migration
{
    /**
     * Backfill base_debit and base_credit for all historical ledger entries.
     *
     * Root cause: base_debit and base_credit were missing from LedgerEntry::$fillable,
     * causing Eloquent to silently discard them on every insert.
     *
     * Formula: base_amount = original_amount * exchange_rate
     * (exchange_rate in this system is stored as USD multiplier, e.g. AFN = 0.01298701)
     */
    public function up()
    {
        // Fix entries where base_debit should be non-zero but is zero
        // (debit > 0 means this is a debit entry)
        DB::statement("
            UPDATE ledger_entries
            SET base_debit = ROUND(debit * exchange_rate, 4),
                base_credit = 0
            WHERE debit > 0
              AND base_debit = 0
              AND exchange_rate IS NOT NULL
              AND exchange_rate > 0
        ");

        // Fix entries where base_credit should be non-zero but is zero
        // (credit > 0 means this is a credit entry)
        DB::statement("
            UPDATE ledger_entries
            SET base_credit = ROUND(credit * exchange_rate, 4),
                base_debit = 0
            WHERE credit > 0
              AND base_credit = 0
              AND exchange_rate IS NOT NULL
              AND exchange_rate > 0
        ");
    }

    /**
     * Reverse the migrations.
     * Note: We cannot safely reverse a data backfill without knowing which
     * records were genuinely zero vs. which were fixed. No-op is safer.
     */
    public function down()
    {
        // Intentional no-op — data backfills are not safely reversible.
        // If needed, re-zero with:
        // DB::statement("UPDATE ledger_entries SET base_debit = 0, base_credit = 0");
    }
}
