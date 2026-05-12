<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMappingKeyToMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mapping_rules', function (Blueprint $table) {
            $table->string('mapping_key')->nullable()->after('condition')->index();
        });
        
        // Populate initial slugs based on condition (simplified for now)
        DB::table('mapping_rules')->where('mapping_key', null)->get()->each(function($rule) {
            $slug = '';
            switch ($rule->condition) {
                case 'خوراکه': $slug = 'EXP_FOOD'; break;
                case 'ترانسپورت': $slug = 'EXP_TRANS'; break;
                case 'متفرقه': $slug = 'EXP_MISC'; break;
                case 'گرفت': $slug = 'PYMT_OUT'; break;
                case 'رسید': $slug = 'PYMT_IN'; break;
                case 'deposit': $slug = 'CASH_IN'; break;
                case 'withdrawal': $slug = 'CASH_OUT'; break;
                case 'purchase': $slug = 'ASSET_PURCH'; break;
                default: $slug = strtoupper(str_replace(' ', '_', $rule->transaction_type . '_' . $rule->condition));
            }
            DB::table('mapping_rules')->where('id', $rule->id)->update(['mapping_key' => $slug]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapping_rules', function (Blueprint $table) {
            $table->dropColumn('mapping_key');
        });
    }
}
