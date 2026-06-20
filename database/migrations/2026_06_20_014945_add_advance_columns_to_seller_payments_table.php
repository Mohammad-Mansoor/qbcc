<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvanceColumnsToSellerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seller_payments', function (Blueprint $table) {
            $table->boolean('is_advance')->default(0)->after('type');
            $table->string('payment_status')->default('allocated')->after('is_advance');
            $table->decimal('remaining_unallocated_amount', 15, 4)->default(0.0000)->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seller_payments', function (Blueprint $table) {
            $table->dropColumn(['is_advance', 'payment_status', 'remaining_unallocated_amount']);
        });
    }
}
