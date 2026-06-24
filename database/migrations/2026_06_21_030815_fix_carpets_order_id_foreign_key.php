<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCarpetsOrderIdForeignKey extends Migration
{
    public function up()
    {
        // Drop the old FK before modifying the column
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        // 1. Change column to bigint to match carpet_orders.id (was int unsigned)
        // 2. Add new FK pointing to carpet_orders.id
        DB::statement('ALTER TABLE carpets MODIFY order_id BIGINT UNSIGNED NULL');

        Schema::table('carpets', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')
                  ->on('carpet_orders')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropForeign('carpets_order_id_foreign');
        });

        DB::statement('ALTER TABLE carpets MODIFY order_id INT UNSIGNED NULL');
    }
}
