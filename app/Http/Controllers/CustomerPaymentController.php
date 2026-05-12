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
        //
    }

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;

            $transaction = $this->accountingService->postAutoTransaction('customer_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\Customer',
                'party_id' => $payment->customer_id,
                'reference' => 'PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
                'override_debit_account_id' => $overrides['override_debit_account_id'] ?? $payment->override_debit_account_id ?? null,
                'override_credit_account_id' => $overrides['override_credit_account_id'] ?? $payment->override_credit_account_id ?? null,
            ]);
            
            $payment->ledger_transaction_id = $transaction->id;
            // Also store overrides on the payment record for future reference/reversal
            $payment->override_debit_account_id = $overrides['override_debit_account_id'] ?? null;
            $payment->override_credit_account_id = $overrides['override_credit_account_id'] ?? null;
            $payment->save();
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }


    public function request_list()
    {
        $requests = CustomerPayment::where('status', 0)->orderBy('id', 'DESC')->get();
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

    public function delete_request($id){
        $credit = CustomerPayment::find($id);
        $credit->delete();
        return response()->json(['status','error']);
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
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'customer_id' => 'required',
            ]);

            $payed = new CustomerPayment();
            $payed->customer_id = $request->customer_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->invoice_number = $request->invoice_number;
            
            if($request->money_type == 'دالر'){
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
                        $totalAmount = $invoice->sale->sum('sale_cost_total');
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
                    }
                }

                // Rule: Allocation Sum Validation (Optional: allow unallocated if business rules permit, 
                // but here we enforce strict match if any allocation is provided)
                if ($totalAllocated > 0 && abs($totalAllocated - $request->amount) > 0.01) {
                    throw new \Exception("مجموع مبالغ تخصیص داده شده ($totalAllocated) با مبلغ کل پرداخت ($request->amount) مطابقت ندارد.");
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
        $payments = CustomerPayment::where('customer_id',$customer_id)->orderBy('created_at','DESC')->paginate(30);
        $customer = Customer::find($customer_id);
        $debits_us = CustomerPayment::where('type','=','گرفت')->where('customer_id',$customer_id)->where('status',1)->sum('amount');
        $debits_af = CustomerPayment::where('type','=','گرفت')->where('customer_id',$customer_id)->where('status',1)->sum('amount_af');
        $credit_us = CustomerPayment::where('type','=','رسید')->where('customer_id',$customer_id)->where('status',1)->sum('amount');
        $credit_af = CustomerPayment::where('type','=','رسید')->where('customer_id',$customer_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $invoice_numbers = Invoice::where('customer_id','=',$customer_id)->distinct()->get(['invoice_no']);

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();

        return view('customers.customer-payment',compact('customer','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','invoice_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
    }

    public function show_all_payment($customer_id){
        $payments = CustomerPayment::where('customer_id',$customer_id)->orderBy('created_at','DESC')->get();
        $customer = Customer::find($customer_id);
        $debits_us = CustomerPayment::where('type','=','گرفت')->where('customer_id',$customer_id)->where('status',1)->sum('amount');
        $debits_af = CustomerPayment::where('type','=','گرفت')->where('customer_id',$customer_id)->where('status',1)->sum('amount_af');
        $credit_us = CustomerPayment::where('type','=','رسید')->where('customer_id',$customer_id)->where('status',1)->sum('amount');
        $credit_af = CustomerPayment::where('type','=','رسید')->where('customer_id',$customer_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $invoice_numbers = Invoice::where('customer_id','=',$customer_id)->distinct()->get(['invoice_no']);
    
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();

        $all = '';
        return view('customers.customer-payment',compact('customer','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','invoice_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
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
        $payments = CustomerPayment::where('customer_id',$paymentEdit->customer_id)->orderBy('created_at','DESC')->paginate(30);
        $customer = Customer::find($paymentEdit->customer_id);
        $debits_us = CustomerPayment::where('type','=','گرفت')->where('customer_id',$paymentEdit->customer_id)->where('status',1)->sum('amount');
        $debits_af = CustomerPayment::where('type','=','گرفت')->where('customer_id',$paymentEdit->customer_id)->where('status',1)->sum('amount_af');
        $credit_us = CustomerPayment::where('type','=','رسید')->where('customer_id',$paymentEdit->customer_id)->where('status',1)->sum('amount');
        $credit_af = CustomerPayment::where('type','=','رسید')->where('customer_id',$paymentEdit->customer_id)->where('status',1)->sum('amount_af');
        $invoice_numbers = Invoice::where('customer_id','=',$paymentEdit->customer_id)->distinct()->get(['invoice_no']);
    
        $selectionService = new \App\Services\AccountSelectionService();
        $mKey = ($paymentEdit->type == 'رسید') ? 'PYMT_IN' : 'PYMT_OUT';
        $allowedDebitAccounts = $selectionService->getValidAccounts($mKey, 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts($mKey, 'credit');
        $mapping = \App\MappingRule::where('mapping_key', $mKey)->first();

        return view('customers.customer-payment',compact('customer','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','invoice_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
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
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
            ]);

            $payed = CustomerPayment::find($payment_id);
            $customer_name = DB::table('customers')->where('id', $request->customer_id)->first();

            // Reverse Old Accounting Entries (Only if it was approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Payment Record Edited');
            }

            // Update record
            $payed->customer_id = $request->customer_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->invoice_number = $request->invoice_number;

            if($request->money_type == 'دالر'){
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
            \App\InvoicePayment::where('payment_id', $payed->id)->delete();
            if ($request->has('allocations')) {
                $totalAllocated = 0;
                foreach ($request->allocations as $invoiceId => $amount) {
                    if ($amount > 0) {
                        $invoice = \App\Invoice::with(['sale', 'payments'])->find($invoiceId);
                        $totalAmount = $invoice->sale->sum('sale_cost_total');
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
                    }
                }

                if ($totalAllocated > 0 && abs($totalAllocated - $request->amount) > 0.01) {
                    throw new \Exception("مجموع مبالغ تخصیص داده شده ($totalAllocated) با مبلغ کل پرداخت ($request->amount) مطابقت ندارد.");
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

            return redirect('/dashboard/customer-payments/'.$request->customer_id)->with('status', 'ویرایش موفقانه ثبت و اسناد حسابداری بروزرسانی شد!');
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

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Payment Record Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت مشتری " . $customer_name->name . " اکونت نمبر " . $customer_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
