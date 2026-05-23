<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeRateToCarpetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            if (!Schema::hasColumn('carpets', 'currency_code')) {
                $table->string('currency_code', 10)->nullable()->after('dollar_rate');
            }
            if (!Schema::hasColumn('carpets', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 9)->nullable()->after('currency_code');
            }
            if (!Schema::hasColumn('carpets', 'original_price')) {
                $table->double('original_price')->nullable()->default(0)->after('exchange_rate');
            }
            if (!Schema::hasColumn('carpets', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('original_price');
                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            }
            if (!Schema::hasColumn('carpets', 'employee_name')) {
                $table->string('employee_name')->nullable()->after('currency_id');
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
        Schema::table('carpets', function (Blueprint $table) {
            try {
                $table->dropForeign(['currency_id']);
            } catch (\Exception $e) {}
            
            $cols = ['currency_code', 'exchange_rate', 'original_price', 'currency_id', 'employee_name'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('carpets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
