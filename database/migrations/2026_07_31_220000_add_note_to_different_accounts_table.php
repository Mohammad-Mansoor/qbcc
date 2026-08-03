<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToDifferentAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('different_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('different_accounts', 'note')) {
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
        Schema::table('different_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('different_accounts', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
}
