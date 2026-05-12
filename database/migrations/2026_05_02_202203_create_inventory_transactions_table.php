<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->string('type'); // Purchase, Sale, Wash, Transfer, etc.
            $table->enum('direction', ['IN', 'OUT']);
            $table->decimal('quantity', 15, 2);
            $table->decimal('area', 15, 2)->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            
            // Reference to the source model
            $table->string('reference_type')->nullable(); // e.g. App\Carpet, App\PurchaseMaterial
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->integer('status')->default(1); // 0:Pending, 1:Approved, 2:Rejected
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign Keys (indexes added implicitly)
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            // Performance Indexes
            $table->index(['item_id', 'warehouse_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
}
