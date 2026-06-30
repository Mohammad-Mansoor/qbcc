<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Customer;
use App\CustomerPayment;
use App\Invoice;
use App\MaterialSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\AccountingService;
use App\ChartOfAccount;

class CustomerPaymentController extends Controller
{
    protected $accountingService;
    protected $invoiceService;

    public function __construct(AccountingService $accountingService, \App\Services\InvoiceService $invoiceService)
    {
        $this->accountingService = $accountingService;
        $this->invoiceService = $invoiceService;
    }

    public function get_outstanding_invoices(Request $request)
    {
        $invoices = $this->invoiceService->getOutstandingInvoices($request->customer_id);
        return response()->json(['invoices' => $invoices]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('customers.index')->with('info', 'لطفاً برای ثبت پرداخت، ابتدا یک مشتری را انتخاب کنید (Please select a customer first to record a payment).');
    }

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'

            // USE THE FORENSIC BASE AMOUNT FOR THE LEDGER
            $baseAmount = $payment->base_amount > 0 ? $payment->base_amount : $payment->amount;

            $transaction = $this->accountingService->postAutoTransaction('payment', $condition, [
                'date' => $payment->date,
                'amount' => $payment->original_amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\Customer',
                'party_id' => $payment->customer_id,
                'reference' => 'PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
                'source_type' => 'App\CustomerPayment',
                'override_debit_account_id' => $overrides['override_debit_account_id'] ?? $payment->override_debit_account_id ?? null,
                'override_credit_account_id' => $overrides['override_credit_account_id'] ?? $payment->override_credit_account_id ?? null,
            ]);

            $payment->ledger_transaction_id = $transaction->id;
            $payment->save();
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Payment #" . $payment->id . ": " . $e->getMessage());
            throw $e;
        }
    }


    public function request_list()
    {
        $requests = CustomerPayment::with('agent')->where('status', 0)->orderBy('id', 'DESC')->get();
        return view('customers.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = CustomerPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            // Post to Accounting
            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0)
                ? " مبلغ " . $payment->amount . "دالر توسط سوپر ادمین اپروف شد "
                : " مبلغ " . $payment->amount_af . "افغانی توسط سوپر ادمین اپروف شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $credit = CustomerPayment::find($id);
        $credit->delete();
        return response()->json(['status', 'error']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'customer_id' => 'required',
                'exchange_rate' => 'nullable|numeric|gt:0',
            ]);

            $currency = \App\Currency::findOrFail($request->currency_id);
            $exchangeRate = $request->exchange_rate ?? $currency->exchange_rate;

            $payed = new CustomerPayment();
            $payed->customer_id = $request->customer_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->invoice_number = $request->invoice_number;

            // Populate Forensic Columns
            $payed->currency_id = $currency->id;
            $payed->currency_code = $currency->code;
            $payed->exchange_rate = $exchangeRate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = bcmul($request->amount, $exchangeRate, 4);

            // Legacy Support
            $payed->dollar_rate = $exchangeRate > 0 ? (1 / $exchangeRate) : 0;
            if ($currency->code == 'USD') {
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }

            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;
            $payed->save();

            // Handle Invoice Allocations with strict validation
            if ($request->has('allocations')) {
                $totalAllocated = 0;
                foreach ($request->allocations as $invoiceId => $amount) {
                    if ($amount > 0) {
                        $invoice = \App\Invoice::with(['sale', 'payments'])->find($invoiceId);
                        $totalAmount = $invoice->sale->where('is_returned', 0)->sum('sale_cost_total');
                        $paidAmount = $invoice->payments->sum('amount_applied');
                        $remaining = $totalAmount - $paidAmount;

                        if ($amount > ($remaining + 0.01)) { // Allow for minor rounding
                            throw new \Exception("مقدار تخصیص داده شده به انوایس #$invoice->invoice_no ($amount) از باقی مانده انوایس ($remaining) بیشتر است.");
                        }

                        \App\InvoicePayment::create([
                            'payment_id' => $payed->id,
                            'invoice_id' => $invoiceId,
                            'amount_applied' => $amount,
                        ]);
                        $totalAllocated += $amount;

                        // Recalculate and update invoice payment status
                        $newRemaining = $remaining - $amount;
                        if ($newRemaining <= 0.01) {
                            $invoice->payment_status = 'paid';
                        } else {
                            $invoice->payment_status = 'partially_paid';
                        }
                        $invoice->save();
                    }
                }

                // Rule: Allocation Sum Validation comparing USD allocations to the USD base_amount of payment
                if ($totalAllocated > 0 && abs($totalAllocated - $payed->base_amount) > 0.01) {
                    throw new \Exception("مجموع مبالغ تخصیص داده شده ($totalAllocated USD) با معادل دالر کل پرداخت ($" . number_format($payed->base_amount, 2) . ") مطابقت ندارد.");
                }
            }

            // Accounting Posting (Only if approved)
            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $customer_name = DB::table('customers')->where('id', $request->customer_id)->first();
            $currency = ($request->money_type == 'دالر') ? " دالر " : " افغانی ";

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مشتری به نام " . $customer_name->name . " اکونت نمبر " . $customer_name->id . " به مبلغ " . $request->amount . $currency . " را " . $request->type . " کرد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد!');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function show($customer_id)
    {
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return redirect()->route('customers.index')->with('error', 'مشتری مورد نظر یافت نشد.');
        }

        // Fetch unallocated payments for the cash ledger
        $payments = CustomerPayment::where('customer_id', $customer_id)
            ->doesntHave('allocations')
            ->where('status', '!=', 2)
            ->orderBy('date', 'DESC')
            ->paginate(30);

        // Fetch Carpet Sales Invoices
        $salesInvoices = \App\Invoice::where('customer_id', $customer_id)
            ->where('type', 'carpet')
            ->with(['sale', 'payments'])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        // Calculate Totals for Sales Invoices (in USD)
        $totalOwedSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });
        $totalPaidSales = DB::table('invoice_payments')
            ->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.customer_id', $customer_id)
            ->sum('amount_applied');

        // FORENSIC DYNAMIC TOTALS for unallocated cash ledger payments
        $currencyTotals = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD) for unallocated cash ledger payments
        $totalBaseReceived = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->where('type', 'رسید')
            ->sum('base_amount');

        $totalBaseSent = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->where('type', 'گرفت')
            ->sum('base_amount');

        $paymentEdit = '';
        $invoice_numbers = Invoice::where('customer_id', '=', $customer_id)->distinct()->get(['invoice_no']);

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        
        $allowedDebitAccountsOut = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccountsOut = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();
        
        $currencies = \App\Currency::where('is_active', true)->get();

        $allocatedPayments = CustomerPayment::where('customer_id', $customer_id)
            ->has('allocations')
            ->where('status', '!=', 2)
            ->with(['allocations.invoice', 'allocations'])
            ->orderBy('date', 'DESC')
            ->get();

        return view('customers.customer-payment', compact(
            'customer', 'payments', 'paymentEdit', 'currencyTotals', 
            'totalBaseReceived', 'totalBaseSent', 'invoice_numbers', 
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping',
            'allowedDebitAccountsOut', 'allowedCreditAccountsOut', 'mappingOut', 
            'currencies', 'salesInvoices', 'totalOwedSales', 'totalPaidSales', 'allocatedPayments'
        ));
    }

    public function show_all_payment($customer_id)
    {
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return redirect()->route('customers.index')->with('error', 'مشتری مورد نظر یافت نشد.');
        }

        // Fetch all unallocated payments
        $payments = CustomerPayment::where('customer_id', $customer_id)
            ->doesntHave('allocations')
            ->where('status', '!=', 2)
            ->orderBy('date', 'DESC')
            ->get();

        // Fetch Carpet Sales Invoices
        $salesInvoices = \App\Invoice::where('customer_id', $customer_id)
            ->where('type', 'carpet')
            ->with(['sale', 'payments'])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        // Calculate Totals for Sales Invoices (in USD)
        $totalOwedSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });
        $totalPaidSales = DB::table('invoice_payments')
            ->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.customer_id', $customer_id)
            ->sum('amount_applied');

        // FORENSIC DYNAMIC TOTALS for unallocated
        $currencyTotals = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD) for unallocated
        $totalBaseReceived = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->where('type', 'رسید')
            ->sum('base_amount');

        $totalBaseSent = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->where('type', 'گرفت')
            ->sum('base_amount');

        $paymentEdit = '';
        $invoice_numbers = Invoice::where('customer_id', '=', $customer_id)->distinct()->get(['invoice_no']);

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $currencies = \App\Currency::where('is_active', true)->get();

        $all = '';
        return view('customers.customer-payment', compact(
            'customer', 'payments', 'paymentEdit', 'currencyTotals', 
            'totalBaseReceived', 'totalBaseSent', 'invoice_numbers', 
            'all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 
            'currencies', 'salesInvoices', 'totalOwedSales', 'totalPaidSales'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = CustomerPayment::find($payment_id);
        if (!$paymentEdit) {
            return redirect()->back()->with('error', 'سند پرداخت یافت نشد.');
        }
        $customer_id = $paymentEdit->customer_id;
        $customer = Customer::find($customer_id);

        $payments = CustomerPayment::where('customer_id', $customer_id)
            ->doesntHave('allocations')
            ->where('status', '!=', 2)
            ->orderBy('date', 'DESC')
            ->paginate(30);

        // Fetch Carpet Sales Invoices
        $salesInvoices = \App\Invoice::where('customer_id', $customer_id)
            ->where('type', 'carpet')
            ->with(['sale', 'payments'])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        // Calculate Totals for Sales Invoices (in USD)
        $totalOwedSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });
        $totalPaidSales = DB::table('invoice_payments')
            ->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.customer_id', $customer_id)
            ->sum('amount_applied');

        // FORENSIC DYNAMIC TOTALS for unallocated
        $currencyTotals = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD) for unallocated
        $totalBaseReceived = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->where('type', 'رسید')
            ->sum('base_amount');

        $totalBaseSent = CustomerPayment::where('customer_id', $customer_id)
            ->where('status', '!=', 2)
            ->doesntHave('allocations')
            ->where('type', 'گرفت')
            ->sum('base_amount');

        $invoice_numbers = Invoice::where('customer_id', '=', $customer_id)->distinct()->get(['invoice_no']);

        $selectionService = new \App\Services\AccountSelectionService();
        $mKey = ($paymentEdit->type == 'رسید') ? 'PYMT_IN' : 'PYMT_OUT';
        $allowedDebitAccounts = $selectionService->getValidAccounts($mKey, 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts($mKey, 'credit');
        $mapping = \App\MappingRule::where('mapping_key', $mKey)->first();
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('customers.customer-payment', compact(
            'customer', 'payments', 'paymentEdit', 'currencyTotals', 
            'totalBaseReceived', 'totalBaseSent', 'invoice_numbers', 
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 
            'currencies', 'salesInvoices', 'totalOwedSales', 'totalPaidSales'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'exchange_rate' => 'nullable|numeric|gt:0',
            ]);

            $payed = CustomerPayment::find($payment_id);
            $customer_name = DB::table('customers')->where('id', $request->customer_id)->first();
            $currency = \App\Currency::findOrFail($request->currency_id);
            $exchangeRate = $request->exchange_rate ?? $currency->exchange_rate;

            // Reverse Old Accounting Entries (Only if it was approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Payment Record Edited');
            }

            // Update record
            $payed->customer_id = $request->customer_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->invoice_number = $request->invoice_number;

            // Update Forensic Columns
            $payed->currency_id = $currency->id;
            $payed->currency_code = $currency->code;
            $payed->exchange_rate = $exchangeRate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = bcmul($request->amount, $exchangeRate, 4);

            // Legacy Support
            $payed->dollar_rate = $exchangeRate > 0 ? (1 / $exchangeRate) : 0;
            if ($currency->code == 'USD') {
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;
            $payed->update();

            // Clear old allocations and re-apply new ones with strict validation
            $oldInvoices = \App\InvoicePayment::where('payment_id', $payed->id)->pluck('invoice_id')->unique()->toArray();
            \App\InvoicePayment::where('payment_id', $payed->id)->delete();
            $newInvoices = [];
            if ($request->has('allocations')) {
                $totalAllocated = 0;
                foreach ($request->allocations as $invoiceId => $amount) {
                    if ($amount > 0) {
                        $invoice = \App\Invoice::with(['sale', 'payments'])->find($invoiceId);
                        $totalAmount = $invoice->sale->where('is_returned', 0)->sum('sale_cost_total');
                        $paidAmount = $invoice->payments->sum('amount_applied');
                        $remaining = $totalAmount - $paidAmount;

                        if ($amount > ($remaining + 0.01)) {
                            throw new \Exception("مقدار تخصیص داده شده به انوایس #$invoice->invoice_no ($amount) از باقی مانده انوایس ($remaining) بیشتر است.");
                        }

                        \App\InvoicePayment::create([
                            'payment_id' => $payed->id,
                            'invoice_id' => $invoiceId,
                            'amount_applied' => $amount,
                        ]);
                        $totalAllocated += $amount;
                        $newInvoices[] = $invoiceId;
                    }
                }

                // Rule: Allocation Sum Validation comparing USD allocations to the USD base_amount of payment
                if ($totalAllocated > 0 && abs($totalAllocated - $payed->base_amount) > 0.01) {
                    throw new \Exception("مجموع مبالغ تخصیص داده شده ($totalAllocated USD) با معادل دالر کل پرداخت ($" . number_format($payed->base_amount, 2) . ") مطابقت ندارد.");
                }
            }

            // Recalculate status for all affected invoices (both old and new)
            $affectedInvoiceIds = array_unique(array_merge($oldInvoices, $newInvoices));
            foreach ($affectedInvoiceIds as $invId) {
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

            // Post New Accounting Entry (Only if approved)
            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت مشتری " . $customer_name->name . " به مبلغ " . $request->amount . " " . $request->money_type;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/customer-payments/' . $request->customer_id)->with('status', 'ویرایش موفقانه ثبت و اسناد حسابداری بروزرسانی شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = CustomerPayment::find($id);
            $customer_name = DB::table('customers')->where('id', $payment->customer_id)->first();

            // Track affected invoices before deleting
            $affectedInvoices = \App\InvoicePayment::where('payment_id', $payment->id)->pluck('invoice_id')->unique()->toArray();

            // Check Permissions
            if (!Auth::user()->can('cancel_customer_payment')) {
                abort(403, 'شما صلاحیت ابطال پرداخت‌های مشتری را ندارید.');
            }

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Payment Record Deleted', 'App\CustomerPayment');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ابطال پرداخت مشتری " . $customer_name->name . " اکونت نمبر " . $customer_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->status = 2; // Cancelled
            $payment->save();

            // Reverse Invoice Payments (remove allocations to invoices)
            $invoicePayments = \App\InvoicePayment::where('payment_id', $payment->id)->get();
            foreach ($invoicePayments as $invPay) {
                $invPay->delete();
            }

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

            return response()->json(['status' => 'success', 'message' => 'پرداخت موفقانه ابطال گردید!']);
        });
    }
}
