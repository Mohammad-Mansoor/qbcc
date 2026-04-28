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

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
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

    private function postPaymentToAccounting($payment)
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
            ]);
            
            $payment->ledger_transaction_id = $transaction->id;
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
            $payed->save();

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

        return view('customers.customer-payment',compact('customer','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','invoice_numbers'));
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
    
        $all = '';
        return view('customers.customer-payment',compact('customer','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','invoice_numbers','all'));
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
    
        return view('customers.customer-payment',compact('customer','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','invoice_numbers'));
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
            $payed->update();

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
