<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKachaeePaymentAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('kachaee_payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kachaee_payment_id');
            $table->string('allocatable_type');
            $table->unsignedBigInteger('allocatable_id');
            $table->decimal('allocated_amount', 18, 4);
            $table->decimal('exchange_rate', 18, 8);
            $table->decimal('base_allocated_amount', 18, 4);
            $table->timestamps();

            $table->foreign('kachaee_payment_id', 'kch_pay_alloc_payment_id_foreign')
                  ->references('id')
                  ->on('kachaee_payments')
                  ->onDelete('cascade');

            $table->index(['allocatable_type', 'allocatable_id'], 'kch_pay_allocatable_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kachaee_payment_allocations');
    }
}
