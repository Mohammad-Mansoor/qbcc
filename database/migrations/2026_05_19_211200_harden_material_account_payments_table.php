<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HardenMaterialAccountPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_account_payments', function (Blueprint $table) {
            // Price & Currency Snapshot Columns
            $table->decimal('price', 18, 4)->default(0)->after('amount');
            $table->unsignedBigInteger('currency_id')->nullable()->after('price');
            $table->string('currency_code', 10)->nullable()->after('currency_id');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');

            // Financial Amounts
            $table->decimal('original_amount', 18, 4)->nullable()->after('exchange_rate');
            $table->decimal('base_currency_amount', 18, 4)->nullable()->after('original_amount');

            // Overrides & Warehouse Link
            $table->unsignedBigInteger('override_debit_account_id')->nullable()->after('status');
            $table->unsignedBigInteger('override_credit_account_id')->nullable()->after('override_debit_account_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('override_credit_account_id');

            // Add FKs
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->foreign('override_debit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('override_credit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });

        // Dynamically resolve account IDs by code to support fresh migrations
        $inventoryId = DB::table('chart_of_accounts')->where('account_code', '1400')->value('id');
        if (!$inventoryId) {
            $inventoryId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '1400',
                'account_name' => 'Inventory',
                'account_type' => 'Asset',
                'report_group' => 'Current Asset',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $apId = DB::table('chart_of_accounts')->where('account_code', '2100')->value('id');
        if (!$apId) {
            $apId = DB::table('chart_of_accounts')->insertGetId([
                'account_code' => '2100',
                'account_name' => 'Accounts Payable',
                'account_type' => 'Liability',
                'report_group' => 'Current Liability',
                'currency' => 'USD',
                'is_cash_account' => 0,
                'normal_balance' => 'credit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed mapping rules for material accounts payment & receipt
        // Debit/Credit default accounts: Inventory (Asset), Accounts Payable (Liability)
        DB::table('mapping_rules')->insert([
            [
                'transaction_type' => 'material_payment_in',
                'condition' => 'رسید',
                'mapping_key' => 'MATERIAL_RECEIPT',
                'debit_account_id' => $inventoryId, 
                'credit_account_id' => $apId, 
                'description_template' => 'رسید مواد از حساب: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transaction_type' => 'material_payment_out',
                'condition' => 'گرفت',
                'mapping_key' => 'MATERIAL_PAYMENT',
                'debit_account_id' => $apId, 
                'credit_account_id' => $inventoryId, 
                'description_template' => 'خروج مواد از حساب: {reference}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_account_payments', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['override_debit_account_id']);
            $table->dropForeign(['override_credit_account_id']);
            $table->dropForeign(['warehouse_id']);

            $table->dropColumn([
                'price',
                'currency_id',
                'currency_code',
                'exchange_rate',
                'original_amount',
                'base_currency_amount',
                'override_debit_account_id',
                'override_credit_account_id',
                'warehouse_id'
            ]);
        });

        DB::table('mapping_rules')->whereIn('mapping_key', ['MATERIAL_RECEIPT', 'MATERIAL_PAYMENT'])->delete();
    }
}
