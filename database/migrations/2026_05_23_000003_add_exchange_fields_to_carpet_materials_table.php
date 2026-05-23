<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeFieldsToCarpetMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpet_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('carpet_materials', 'currency_code')) {
                $table->string('currency_code', 10)->nullable()->after('date');
            }
            if (!Schema::hasColumn('carpet_materials', 'original_price')) {
                $table->double('original_price')->default(0)->nullable()->after('currency_code');
            }
            if (!Schema::hasColumn('carpet_materials', 'exchange_rate')) {
                $table->double('exchange_rate')->default(1)->nullable()->after('original_price');
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
        Schema::table('carpet_materials', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'original_price', 'exchange_rate']);
        });
    }
}
