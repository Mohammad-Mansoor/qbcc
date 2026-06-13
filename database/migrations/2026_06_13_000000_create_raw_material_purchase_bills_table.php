<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawMaterialPurchaseBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_material_purchase_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bill_number')->unique();
            $table->unsignedInteger('seller_id');
            $table->date('date');
            $table->string('status')->default('open'); // 'open' or 'closed'
            $table->timestamps();

            $table->foreign('seller_id')->references('id')->on('string_sellers')->onDelete('cascade');
        });

        Schema::table('purchase_materials', function (Blueprint $table) {
            $table->unsignedBigInteger('raw_material_purchase_bill_id')->nullable()->after('seller_id');
            $table->foreign('raw_material_purchase_bill_id')->references('id')->on('raw_material_purchase_bills')->onDelete('set null');
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
            $table->dropForeign(['raw_material_purchase_bill_id']);
            $table->dropColumn('raw_material_purchase_bill_id');
        });

        Schema::dropIfExists('raw_material_purchase_bills');
    }
}
