<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCogsOverridesToMaterialSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('override_cogs_debit_id')->nullable()->after('override_credit_account_id');
            $table->unsignedBigInteger('override_cogs_credit_id')->nullable()->after('override_cogs_debit_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('override_cogs_credit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_sales', function (Blueprint $table) {
            $table->dropColumn(['override_cogs_debit_id', 'override_cogs_credit_id', 'warehouse_id']);
        });
    }
}
