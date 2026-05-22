<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeLaborNumbersNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE washing_payments MODIFY wash_number VARCHAR(255) NULL');
        DB::statement('ALTER TABLE finishing_team_payments MODIFY finish_number VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE washing_payments MODIFY wash_number VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE finishing_team_payments MODIFY finish_number VARCHAR(255) NOT NULL');
    }
}
