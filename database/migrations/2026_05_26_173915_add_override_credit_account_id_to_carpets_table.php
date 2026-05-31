<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverrideCreditAccountIdToCarpetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            if (!Schema::hasColumn('carpets', 'override_credit_account_id')) {
                $table->unsignedBigInteger('override_credit_account_id')->nullable()->after('override_inventory_account_id');
                $table->foreign('override_credit_account_id')->references('id')->on('chart_of_accounts');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            if (Schema::hasColumn('carpets', 'override_credit_account_id')) {
                $table->dropForeign(['override_credit_account_id']);
                $table->dropColumn('override_credit_account_id');
            }
        });
    }
}
