<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimplifyCustomerOrdersSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_orders', 'status')) {
                $table->string('status')->default('pending')->after('order_date');
            }
            if (!Schema::hasColumn('customer_orders', 'main_customer_id')) {
                $table->unsignedInteger('main_customer_id')->nullable()->after('customer_id');
                $table->foreign('main_customer_id')->references('id')->on('customers')->onDelete('set null');
            }
            
            // Decouple from legacy customer_account_orders table
            $table->dropForeign(['customer_id']);
        });

        // Run raw statement to allow NULL in customer_id
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_orders MODIFY customer_id INT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (Schema::hasColumn('customer_orders', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('customer_orders', 'main_customer_id')) {
                $table->dropForeign(['main_customer_id']);
                $table->dropColumn('main_customer_id');
            }
        });

        // Restore legacy non-nullable and foreign key
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_orders MODIFY customer_id INT UNSIGNED NOT NULL');
        
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('c_id')->on('customer_account_orders');
        });
    }
}
