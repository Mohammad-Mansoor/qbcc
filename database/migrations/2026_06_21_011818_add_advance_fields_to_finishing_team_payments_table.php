<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvanceFieldsToFinishingTeamPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_team_payments', function (Blueprint $table) {
            $table->boolean('is_advance')->default(0)->after('status');
            $table->string('payment_status')->default('unallocated')->after('is_advance');
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
        Schema::table('finishing_team_payments', function (Blueprint $table) {
            $table->dropColumn(['is_advance', 'payment_status', 'remaining_unallocated_amount']);
        });
    }
}
