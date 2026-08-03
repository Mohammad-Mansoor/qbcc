<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToStringSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('string_sellers', function (Blueprint $table) {
            if (!Schema::hasColumn('string_sellers', 'note')) {
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
        Schema::table('string_sellers', function (Blueprint $table) {
            if (Schema::hasColumn('string_sellers', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
}
