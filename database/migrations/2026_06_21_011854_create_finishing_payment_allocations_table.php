<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinishingPaymentAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finishing_payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('finishing_team_payment_id');
            $table->string('allocatable_type');
            $table->unsignedBigInteger('allocatable_id');
            $table->decimal('allocated_amount', 18, 4);
            $table->decimal('exchange_rate', 18, 8);
            $table->decimal('base_allocated_amount', 18, 4);
            $table->timestamps();

            $table->foreign('finishing_team_payment_id', 'fin_pay_alloc_payment_id_foreign')
                  ->references('id')
                  ->on('finishing_team_payments')
                  ->onDelete('cascade');

            $table->index(['allocatable_type', 'allocatable_id'], 'fin_pay_allocatable_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finishing_payment_allocations');
    }
}
