<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssetImageToAjnasAccountDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ajnas_account_details', 'asset_image')) {
            Schema::table('ajnas_account_details', function (Blueprint $table) {
                $table->string('asset_image')->nullable()->after('estimated_salvage_value');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ajnas_account_details', function (Blueprint $table) {
            $table->dropColumn('asset_image');
        });
    }
}
