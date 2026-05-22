<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HardenMaterialSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_sales', function (Blueprint $table) {
            // Forensic Currency Snapshot Columns
            $table->unsignedBigInteger('currency_id')->nullable()->after('agent_id');
            $table->string('currency_code', 10)->nullable()->after('currency_id');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');
            
            // Financial Amounts
            $table->decimal('original_amount', 18, 4)->nullable()->after('exchange_rate');
            $table->decimal('base_currency_amount', 18, 4)->nullable()->after('original_amount');
            
            // Audit Metadata
            $table->text('forensic_notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_sales', function (Blueprint $table) {
            $table->dropColumn([
                'currency_id', 
                'currency_code', 
                'exchange_rate', 
                'original_amount', 
                'base_currency_amount',
                'forensic_notes'
            ]);
        });
    }
}
