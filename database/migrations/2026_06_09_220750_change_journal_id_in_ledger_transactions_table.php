<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeJournalIdInLedgerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use DB statement to modify column type to VARCHAR(255)
        DB::statement('ALTER TABLE ledger_transactions MODIFY COLUMN journal_id VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE ledger_transactions MODIFY COLUMN journal_id BIGINT UNSIGNED NULL');
    }
}
