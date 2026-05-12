<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CleanupCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $blueprint) {
            // Add proper header fields
            if (!Schema::hasColumn('customer_orders', 'order_name')) {
                $blueprint->string('order_name')->nullable()->after('co_id');
            }
            if (!Schema::hasColumn('customer_orders', 'order_date')) {
                $blueprint->date('order_date')->nullable()->after('order_name');
            }
            if (!Schema::hasColumn('customer_orders', 'main_customer_id')) {
                $blueprint->unsignedBigInteger('main_customer_id')->nullable()->after('customer_id');
            }
        });

        // Use raw SQL to make columns nullable (avoiding Doctrine DBAL requirement)
        DB::statement('ALTER TABLE customer_orders MODIFY quality VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY height VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY width VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY area VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY warp VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY weft VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY wash_type VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY pile_height VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY weaver_code VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY start_date VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY end_date VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY current_status VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY photo VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customer_orders MODIFY customer_order VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_orders', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['order_name', 'order_date', 'main_customer_id']);
        });
    }
}
