<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\PurchaseInvoice;
use App\Carpet;

class MigrateLegacyCheckbooksToPurchaseInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all legacy checkbooks
        $legacyCheckbooks = DB::table('carpet_check_books')->get();

        foreach ($legacyCheckbooks as $legacy) {
            // Check if a purchase invoice with this invoice_number and agent_id already exists
            $invoice = PurchaseInvoice::where('invoice_number', $legacy->check_number)
                ->where('agent_id', $legacy->agent_id)
                ->first();

            if (!$invoice) {
                $invoice = PurchaseInvoice::create([
                    'invoice_number' => $legacy->check_number,
                    'agent_id' => $legacy->agent_id,
                    'status' => 'open',
                    'date' => $legacy->date ?? date('Y-m-d'),
                ]);
            }

            // Update the carpet to point to the purchase invoice
            Carpet::where('carpet_id', $legacy->carpet_id)
                ->update(['purchase_invoice_id' => $invoice->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Carpet::query()->update(['purchase_invoice_id' => null]);
        
        // Disable foreign key checks to truncate safely
        Schema::disableForeignKeyConstraints();
        PurchaseInvoice::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
