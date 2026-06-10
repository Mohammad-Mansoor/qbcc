<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusAndAllocations extends Migration
{
    public function up()
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
        });

        Schema::create('agent_payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_payment_id');
            $table->string('allocatable_type');
            $table->unsignedBigInteger('allocatable_id');
            $table->decimal('allocated_amount', 18, 4);
            $table->decimal('exchange_rate', 18, 8);
            $table->decimal('base_allocated_amount', 18, 4);
            $table->timestamps();

            $table->foreign('agent_payment_id')
                  ->references('id')
                  ->on('agent_payments')
                  ->onDelete('cascade');

            $table->index(['allocatable_type', 'allocatable_id'], 'allocatable_document_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_payment_allocations');

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
}
