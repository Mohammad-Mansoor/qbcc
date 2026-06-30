<?php

$content = file_get_contents('app/Http/Controllers/CustomerPaymentController.php');

$destroy_code = <<<HTML
    public function destroy(\$id)
    {
        return DB::transaction(function () use (\$id) {
            \$payment = CustomerPayment::find(\$id);
            \$customer_name = DB::table('customers')->where('id', \$payment->customer_id)->first();

            // Check Permissions
            if (!Auth::user()->can('cancel_customer_payment')) {
                abort(403, 'شما صلاحیت ابطال پرداخت‌های مشتری را ندارید.');
            }

            // Track affected invoices before cancelling
            \$affectedInvoices = \App\InvoicePayment::where('payment_id', \$payment->id)->pluck('invoice_id')->unique()->toArray();

            // Reverse Accounting Entry (Only if approved)
            if (\$payment->status == 1) {
                \$this->accountingService->reverseTransactionBySource(\$payment->id, 'Payment Record Deleted', 'App\CustomerPayment');
            }

            \$activity = new Activity();
            \$activity->date = Carbon::today()->format('Y-m-d');
            \$activity->description = "ابطال پرداخت مشتری " . \$customer_name->name . " اکونت نمبر " . \$customer_name->id;
            \$activity->user_id = Auth::user()->id;
            \$activity->save();

            \$payment->status = 2; // Cancelled
            \$payment->save();

            // Delete allocations since the payment is cancelled
            \App\InvoicePayment::where('payment_id', \$payment->id)->delete();

            // Recalculate status for affected invoices
            foreach (\$affectedInvoices as \$invId) {
                \$inv = \App\Invoice::with(['sale', 'payments'])->find(\$invId);
                if (\$inv) {
                    \$total = \$inv->sale->where('is_returned', 0)->sum('sale_cost_total');
                    \$paid = \App\InvoicePayment::where('invoice_id', \$invId)->sum('amount_applied') ?? 0;
                    \$rem = \$total - \$paid;
                    
                    if (\$paid <= 0) {
                        \$inv->payment_status = 'unpaid';
                    } elseif (\$rem <= 0.01) {
                        \$inv->payment_status = 'paid';
                    } else {
                        \$inv->payment_status = 'partially_paid';
                    }
                    \$inv->save();
                }
            }

            return response()->json(['status' => 'success']);
        });
    }
HTML;

$content = preg_replace(
    "/    public function destroy\(\\\$id\)\n    \{.*?(?=\n    \})    \}/s",
    $destroy_code,
    $content
);

$content = preg_replace(
    "/(CustomerPayment::where\('customer_id',\s*\\\$customer_id\)\n\s*->doesntHave\('allocations'\))/",
    "$1\n            ->where('status', '!=', 2)",
    $content
);

$content = preg_replace(
    "/(CustomerPayment::where\('customer_id',\s*\\\$customer_id\)\n\s*->where\('status',\s*1\))/",
    "CustomerPayment::where('customer_id', \$customer_id)\n            ->where('status', '!=', 2)",
    $content
);

$content = str_replace(
    "'source_id' => \$payment->id,",
    "'source_id' => \$payment->id,\n                'source_type' => 'App\CustomerPayment',",
    $content
);

file_put_contents('app/Http/Controllers/CustomerPaymentController.php', $content);
echo "PHP replacement successful.\n";
