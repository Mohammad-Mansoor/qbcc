<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverrideAccountsToCarpetRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpet_repairs', function (Blueprint $table) {
            $table->unsignedBigInteger('override_debit_account_id')->nullable()->after('base_currency_amount');
            $table->unsignedBigInteger('override_credit_account_id')->nullable()->after('override_debit_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpet_repairs', function (Blueprint $table) {
            $table->dropColumn(['override_debit_account_id', 'override_credit_account_id']);
        });
    }
}
