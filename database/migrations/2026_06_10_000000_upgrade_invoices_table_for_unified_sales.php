<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpgradeInvoicesTableForUnifiedSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Temporarily save existing invoices data to preserve customer_id
        $invoicesData = [];
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'customer_id')) {
            $invoicesData = DB::table('invoices')->select('id', 'customer_id')->get()->toArray();
        }

        // 2. Drop foreign key and column customer_id
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        // 3. Re-create customer_id as nullable, and add type, status, and agent_id
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('invoice_date');
            $table->string('type')->default('carpet')->after('invoice_no');
            $table->string('status')->default('open')->after('type');
            $table->unsignedBigInteger('agent_id')->nullable()->after('customer_id');

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('agent_id')->references('agent_id')->on('agents');
        });

        // 4. Restore the saved customer_ids back
        foreach ($invoicesData as $row) {
            DB::table('invoices')->where('id', $row->id)->update([
                'customer_id' => $row->customer_id
            ]);
        }

        // 5. Add invoice_id to material_sales table
        Schema::table('material_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('agent_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Remove foreign key and column from material_sales
        Schema::table('material_sales', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });

        // 2. Temporarily save invoices customer_id
        $invoicesData = [];
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'customer_id')) {
            $invoicesData = DB::table('invoices')->select('id', 'customer_id')->get()->toArray();
        }

        // 3. Drop all added fields and constraints
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['agent_id']);
            $table->dropColumn(['customer_id', 'agent_id', 'type', 'status']);
        });

        // 4. Restore customer_id as non-nullable
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('invoice_date');
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        // 5. Restore the customer_id values
        foreach ($invoicesData as $row) {
            if ($row->customer_id) {
                DB::table('invoices')->where('id', $row->id)->update([
                    'customer_id' => $row->customer_id
                ]);
            }
        }
    }
}
