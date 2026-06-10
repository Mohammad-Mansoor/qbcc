<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseInvoiceIdToCarpetsTable extends Migration
{
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_invoice_id')->nullable()->after('agent_id');
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropForeign(['purchase_invoice_id']);
            $table->dropColumn('purchase_invoice_id');
        });
    }
}
