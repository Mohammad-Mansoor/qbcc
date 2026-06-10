<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToFinishingTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_teams', function (Blueprint $table) {
            $table->string('father_name')->nullable();
            $table->string('tazkira_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->string('grantor_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finishing_teams', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'tazkira_number', 'contact_number', 'address', 'grantor_name']);
        });
    }
}
