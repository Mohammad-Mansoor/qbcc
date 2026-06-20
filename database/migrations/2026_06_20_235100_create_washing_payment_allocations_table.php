<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWashingPaymentAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('washing_payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('washing_payment_id');
            $table->string('allocatable_type'); // App\ProductionBatch
            $table->unsignedBigInteger('allocatable_id');
            $table->decimal('allocated_amount', 18, 4); // in washing_payment currency
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('base_allocated_amount', 18, 4); // in base USD
            $table->timestamps();

            $table->foreign('washing_payment_id', 'wash_alloc_pay_fk')
                  ->references('id')
                  ->on('washing_payments')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('washing_payment_allocations');
    }
}
