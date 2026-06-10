<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseBillToPurchaseMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->string('purchase_bill')->nullable()->after('purchase_number');
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
            $table->dropColumn('purchase_bill');
        });
    }
}
