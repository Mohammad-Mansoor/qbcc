<?php

namespace App\Services;

use App\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Get all invoices for a customer with their remaining balances
     */
    public function getOutstandingInvoices($customerId)
    {
        return Invoice::where('customer_id', $customerId)
            ->with(['sale', 'payments'])
            ->get()
            ->map(function ($invoice) {
                $totalAmount = $invoice->sale->sum('sale_cost_total');
                $paidAmount = $invoice->payments->sum('amount_applied');
                $invoice->total_amount = $totalAmount;
                $invoice->paid_amount = $paidAmount;
                $invoice->remaining_balance = $totalAmount - $paidAmount;
                return $invoice;
            })
            ->filter(function ($invoice) {
                return $invoice->remaining_balance > 0.01;
            })
            ->values();
    }
}
