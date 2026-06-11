<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCarpetsOrderIdForeignKey extends Migration
{
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE carpets MODIFY order_id INT UNSIGNED NULL');

        Schema::table('carpets', function (Blueprint $table) {
            $table->foreign('order_id')->references('co_id')->on('customer_orders')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE carpets MODIFY order_id BIGINT UNSIGNED NULL');

        Schema::table('carpets', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('carpet_orders');
        });
    }
}
