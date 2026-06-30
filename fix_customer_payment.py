import re

with open('app/Http/Controllers/CustomerPaymentController.php', 'r') as f:
    content = f.read()

# 1. Update destroy method
destroy_code = """    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = CustomerPayment::find($id);
            $customer_name = DB::table('customers')->where('id', $payment->customer_id)->first();

            // Check Permissions
            if (!Auth::user()->can('cancel_customer_payment')) {
                abort(403, 'شما صلاحیت ابطال پرداخت‌های مشتری را ندارید.');
            }

            // Track affected invoices before cancelling
            $affectedInvoices = \App\InvoicePayment::where('payment_id', $payment->id)->pluck('invoice_id')->unique()->toArray();

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Payment Record Deleted', 'App\\\\CustomerPayment');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ابطال پرداخت مشتری " . $customer_name->name . " اکونت نمبر " . $customer_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->status = 2; // Cancelled
            $payment->save();

            // Delete allocations since the payment is cancelled
            \App\InvoicePayment::where('payment_id', $payment->id)->delete();

            // Recalculate status for affected invoices
            foreach ($affectedInvoices as $invId) {
                $inv = \App\Invoice::with(['sale', 'payments'])->find($invId);
                if ($inv) {
                    $total = $inv->sale->where('is_returned', 0)->sum('sale_cost_total');
                    $paid = \App\InvoicePayment::where('invoice_id', $invId)->sum('amount_applied') ?? 0;
                    $rem = $total - $paid;
                    
                    if ($paid <= 0) {
                        $inv->payment_status = 'unpaid';
                    } elseif ($rem <= 0.01) {
                        $inv->payment_status = 'paid';
                    } else {
                        $inv->payment_status = 'partially_paid';
                    }
                    $inv->save();
                }
            }

            return response()->json(['status' => 'success']);
        });
    }"""

content = re.sub(
    r"    public function destroy\(\$id\)\n    \{.*?(?=\n    \})    \}",
    destroy_code,
    content,
    flags=re.DOTALL
)

# 2. Add where('status', '!=', 2) to main queries
content = re.sub(
    r"(CustomerPayment::where\('customer_id',\s*\$customer_id\)\n\s*->doesntHave\('allocations'\))",
    r"\1\n            ->where('status', '!=', 2)",
    content
)

content = re.sub(
    r"(CustomerPayment::where\('customer_id',\s*\$customer_id\)\n\s*->where\('status',\s*1\))",
    r"CustomerPayment::where('customer_id', $customer_id)\n            ->where('status', '!=', 2)",
    content
)

# 3. Add source_type to postPaymentToAccounting
content = content.replace(
    "'source_id' => $payment->id,",
    "'source_id' => $payment->id,\n                'source_type' => 'App\\\\CustomerPayment',"
)

with open('app/Http/Controllers/CustomerPaymentController.php', 'w') as f:
    f.write(content)

print("Replacement complete")
