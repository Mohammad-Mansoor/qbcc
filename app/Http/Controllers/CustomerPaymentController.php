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
            $condition = ($payment->type == 'رسید') ? 'receipt' : 'withdrawal';
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;

            $this->accountingService->postAutoTransaction('customer_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\Customer',
                'party_id' => $payment->customer_id,
                'reference' => 'PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ]);
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
        $payment = CustomerPayment::find($id);
        $payment->status = 1; // Explicitly set to approved
        $payment->update();
        
        $this->postPaymentToAccounting($payment);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        if ($payment->amount > 0){
            $activity->description = " مبلغ " . $payment->amount . "دالر توسط سوپر ادمین اپروف شد ";
        }
        else{
            $activity->description = " مبلغ " . $payment->amount_af . "افغانی توسط سوپر ادمین اپروف شد ";
        }

        $activity->user_id = Auth::user()->id;
        $activity->save();

        return response()->json(['status' => 'success']);
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
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'team_id' => '',
            'dollar_rate' => '',
            'check_number' => '',
        ]);


        if($request->money_type == 'دالر'){
            $payed = new CustomerPayment();
            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->customer_id = $request->customer_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->invoice_number = $request->invoice_number;
            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $customer_name = DB::table('customers')->where('id', $request->customer_id)->first();

            if ($payed) {
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مشتری به نام  " . $customer_name->name . ' اکونت نمبر '. $customer_name->id. " به مبلغ " . $request->amount . " دالر را " . $request->type . ' کرد ';
                $activity->user_id = Auth::user()->id;
                $activity->save();
                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = new CustomerPayment();
            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->customer_id = $request->customer_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->invoice_number = $request->invoice_number;
            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $customer_name = DB::table('customers')->where('id', $request->customer_id)->first();

            if ($payed) {
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مشتری به نام  " . $customer_name->name . ' اکونت نمبر '. $customer_name->id.  " به مبلغ " . $request->amount . " افغانی را " . $request->type . ' کرد ';
                $activity->user_id = Auth::user()->id;
                $activity->save();

                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
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
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'agent_id' => '',
            'dollar_rate' => '',
            'finish_number' => '',
        ]);
        $customer_name = DB::table('customers')->where('id', $request->customer_id)->first();

        if($request->money_type == 'دالر'){
            $payed = CustomerPayment::find($payment_id);
            $amount = '';
            $money = '';
            if ($payed->amount > 0) {
                $amount = $payed->amount;
                $money = 'دالر';
            } else {
                $money = 'افغانی';
                $amount = $payed->amount_af;
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " در بیلانس مشتری به نام  " . $customer_name->name . ' اکونت نمبر '. $customer_name->id.  "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود  ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->customer_id = $request->customer_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->invoice_number = $request->invoice_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/customer-payments/'.$request->customer_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/customer-payments/'.$request->customer_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = CustomerPayment::find($payment_id);
            $amount = '';
            $money = '';
            if ($payed->amount > 0) {
                $amount = $payed->amount;
                $money = 'دالر';
            } else {
                $money = 'افغانی';
                $amount = $payed->amount_af;
            }
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " در بیلانس مشتری به نام  " . $customer_name->name . ' اکونت نمبر '. $customer_name->id.  "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->customer_id = $request->customer_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->invoice_number = $request->invoice_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/customer-payments/'.$request->customer_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/customer-payments/'.$request->customer_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = CustomerPayment::find($id);
        $customer_name = DB::table('customers')->where('id', $payment->customer_id)->first();

        $amount = '';
        $money = '';
        if ($payment->amount > 0) {
            $amount = $payment->amount;
            $money = 'دالر';
        }
        else{
            $money = 'افغانی';
            $amount = $payment->amount_af;
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "از بیلانس مشتری به نام  " . $customer_name->name . ' اکونت نمبر '. $customer_name->id.  " مبلغ " . $amount . ' '. $money . " را حذف کرد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
