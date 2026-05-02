<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToFinishingTeamPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_team_payments', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->nullable();
            
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
            $table->dropColumn(['status']);
        });
    }
}
