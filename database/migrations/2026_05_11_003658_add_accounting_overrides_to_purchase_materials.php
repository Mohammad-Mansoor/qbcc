<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingOverridesToPurchaseMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->unsignedBigInteger('override_debit_account_id')->nullable()->after('status');
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
        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->dropColumn(['override_debit_account_id', 'override_credit_account_id']);
        });
    }
}
