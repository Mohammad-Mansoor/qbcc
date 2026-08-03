<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToWashingTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('washing_teams', function (Blueprint $table) {
            if (!Schema::hasColumn('washing_teams', 'note')) {
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
        Schema::table('washing_teams', function (Blueprint $table) {
            if (Schema::hasColumn('washing_teams', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
}
