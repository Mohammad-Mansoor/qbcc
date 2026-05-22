<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReturnedColumnsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->tinyInteger('is_returned')->default(0)->after('ledger_transaction_id');
            $table->timestamp('returned_at')->nullable()->after('is_returned');
            $table->unsignedBigInteger('return_ledger_transaction_id')->nullable()->after('returned_at');
            $table->unsignedBigInteger('returned_by')->nullable()->after('return_ledger_transaction_id');

            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['is_returned', 'returned_at', 'return_ledger_transaction_id', 'returned_by']);
        });
    }
}
