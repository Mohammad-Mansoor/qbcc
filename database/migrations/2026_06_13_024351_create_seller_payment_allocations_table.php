<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerPaymentAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_material_purchase_bills', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
        });

        Schema::create('seller_payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('seller_payment_id');
            $table->unsignedBigInteger('raw_material_purchase_bill_id');
            $table->decimal('allocated_amount', 18, 4);
            $table->decimal('exchange_rate', 18, 8);
            $table->decimal('base_allocated_amount', 18, 4);
            $table->timestamps();

            $table->foreign('seller_payment_id')
                  ->references('id')
                  ->on('seller_payments')
                  ->onDelete('cascade');

            $table->foreign('raw_material_purchase_bill_id', 'fk_sm_pb_id')
                  ->references('id')
                  ->on('raw_material_purchase_bills')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_payment_allocations');

        Schema::table('raw_material_purchase_bills', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
}
