<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAdvanceFieldsToAgentPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('agent_payments', function (Blueprint $table) {
            $table->boolean('is_advance')->default(false)->after('status');
            $table->string('payment_status')->default('unallocated')->after('is_advance');
            $table->decimal('remaining_unallocated_amount', 18, 4)->default(0.0000)->after('base_amount');
        });

        // Backfill remaining_unallocated_amount for existing records using original_amount (or amount/amount_af for legacy)
        DB::table('agent_payments')->chunkById(100, function ($payments) {
            foreach ($payments as $payment) {
                $orig = $payment->original_amount ?: ($payment->amount ?: $payment->amount_af);
                DB::table('agent_payments')
                    ->where('id', $payment->id)
                    ->update(['remaining_unallocated_amount' => $orig]);
            }
        });
    }

    public function down()
    {
        Schema::table('agent_payments', function (Blueprint $table) {
            $table->dropColumn(['is_advance', 'payment_status', 'remaining_unallocated_amount']);
        });
    }
}
