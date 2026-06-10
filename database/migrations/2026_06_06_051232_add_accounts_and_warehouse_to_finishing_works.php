<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountsAndWarehouseToFinishingWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_works', function (Blueprint $table) {
            $table->unsignedBigInteger('debit_account_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('credit_account_id')->nullable()->after('debit_account_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('credit_account_id');

            $table->foreign('debit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('credit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finishing_works', function (Blueprint $table) {
            $table->dropForeign(['debit_account_id']);
            $table->dropForeign(['credit_account_id']);
            $table->dropForeign(['warehouse_id']);

            $table->dropColumn(['debit_account_id', 'credit_account_id', 'warehouse_id']);
        });
    }
}
