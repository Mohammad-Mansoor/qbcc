<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transfer_number')->unique();
            $table->date('transfer_date');
            $table->enum('item_type', ['carpet', 'yarn', 'dye']);
            $table->unsignedBigInteger('source_warehouse_id');
            $table->unsignedBigInteger('destination_warehouse_id');
            $table->decimal('quantity', 15, 4);
            $table->text('description')->nullable();
            
            $table->string('status', 20)->default('posted');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('source_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('warehouse_transfer_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_transfer_id');
            $table->string('item_type');
            $table->unsignedBigInteger('ref_id');
            $table->decimal('quantity', 15, 4);
            $table->timestamps();

            $table->foreign('warehouse_transfer_id')
                  ->references('id')->on('warehouse_transfers')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_transfer_items');
        Schema::dropIfExists('warehouse_transfers');
    }
}
