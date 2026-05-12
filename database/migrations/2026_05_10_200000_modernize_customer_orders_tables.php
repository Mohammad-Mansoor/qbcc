<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModernizeCustomerOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Update customer_orders to link to main customers table
        Schema::table('customer_orders', function (Blueprint $blueprint) {
            if (!Schema::hasColumn('customer_orders', 'customer_id')) {
                $blueprint->unsignedBigInteger('customer_id')->nullable()->after('co_id');
            }
        });

        // 2. Update customer_order_details with financial and inventory fields
        Schema::table('customer_order_details', function (Blueprint $blueprint) {
            if (!Schema::hasColumn('customer_order_details', 'unit_price')) {
                $blueprint->decimal('unit_price', 15, 2)->default(0)->after('cod_id');
            }
            if (!Schema::hasColumn('customer_order_details', 'total_amount')) {
                $blueprint->decimal('total_amount', 15, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('customer_order_details', 'currency_code')) {
                $blueprint->string('currency_code', 3)->default('USD')->after('total_amount');
            }
            if (!Schema::hasColumn('customer_order_details', 'exchange_rate')) {
                $blueprint->decimal('exchange_rate', 15, 4)->default(1.0)->after('currency_code');
            }
            if (!Schema::hasColumn('customer_order_details', 'carpet_id')) {
                $blueprint->unsignedBigInteger('carpet_id')->nullable()->after('customer_order_id');
            }
        });

        // 3. Update customer_payments to link to specific orders
        Schema::table('customer_payments', function (Blueprint $blueprint) {
            if (!Schema::hasColumn('customer_payments', 'order_id')) {
                $blueprint->unsignedBigInteger('order_id')->nullable()->after('customer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_orders', function (Blueprint $blueprint) {
            $blueprint->dropColumn('customer_id');
        });

        Schema::table('customer_order_details', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['unit_price', 'total_amount', 'currency_code', 'exchange_rate', 'carpet_id']);
        });

        Schema::table('customer_payments', function (Blueprint $blueprint) {
            $blueprint->dropColumn('order_id');
        });
    }
}
