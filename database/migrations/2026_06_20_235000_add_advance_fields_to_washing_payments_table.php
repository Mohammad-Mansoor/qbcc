<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvanceFieldsToWashingPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('washing_payments', function (Blueprint $table) {
            $table->boolean('is_advance')->default(false)->after('status');
            $table->string('payment_status', 50)->default('unallocated')->after('is_advance'); // unallocated, partially_allocated, allocated
            $table->decimal('remaining_unallocated_amount', 18, 4)->default(0.0000)->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('washing_payments', function (Blueprint $table) {
            $table->dropColumn(['is_advance', 'payment_status', 'remaining_unallocated_amount']);
        });
    }
}
