<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingRulesTable extends Migration
{
    public function up()
    {
        Schema::create('mapping_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_type'); // e.g., 'sale', 'purchase', 'payment', 'receipt'
            $table->string('condition')->nullable(); // e.g., 'cash', 'credit'
            
            // Link to the COA
            $table->unsignedBigInteger('debit_account_id');
            $table->unsignedBigInteger('credit_account_id');
            
            $table->string('description_template')->nullable();
            
            $table->timestamps();

            $table->foreign('debit_account_id')->references('id')->on('chart_of_accounts');
            $table->foreign('credit_account_id')->references('id')->on('chart_of_accounts');
            
            // Unique constraint to prevent duplicate rules for the same event
            $table->unique(['transaction_type', 'condition']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mapping_rules');
    }
}
