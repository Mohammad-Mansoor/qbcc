<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToFinishingTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finishing_teams', function (Blueprint $table) {
            if (!Schema::hasColumn('finishing_teams', 'note')) {
                $table->text('note')->nullable()->after('address');
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
        Schema::table('finishing_teams', function (Blueprint $table) {
            if (Schema::hasColumn('finishing_teams', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
}
