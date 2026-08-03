<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToKachaeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kachaees', function (Blueprint $table) {
            if (!Schema::hasColumn('kachaees', 'note')) {
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
        Schema::table('kachaees', function (Blueprint $table) {
            if (Schema::hasColumn('kachaees', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
}
