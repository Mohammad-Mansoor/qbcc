<?php

namespace App\Http\Controllers;

use App\Activity;
use App\NewDifferentAccount;
use App\NewDifferentAccountPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewDifferentAccountPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function money_request()
    {
        $requests = NewDifferentAccountPayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('new-different-account.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = NewDifferentAccountPayment::find($id);


        $payment->status = 1 ;
        $payment->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');

        $activity->description = " مبلغ " . $payment->amount . "دالر توسط سوپر ادمین اپروف شد ";

        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }
    public function delete_request($id){
        $credit = NewDifferentAccountPayment::find($id);


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
        $received_data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'currency' => 'required',
            'insert_credit' => '',
            'type' => '',
            'account_id' => '',
            'status' =>''


        ]);
        if (Auth::user()->role == 'SP'){
            $received_data['status'] = 1;
        }
        else{
            $received_data['status'] = 0;
        }

        $payed = NewDifferentAccountPayment::create($received_data);

        $account = NewDifferentAccount::find($request->account_id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $account->name . ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . ' ' . $request->type . ' کرد ';
        $activity->user_id = Auth::user()->id;
        $activity->save();


        if ($payed) {
            return redirect()->back()->with('status', 'گرفت موفقانه ثبت شد !');
        } else {
            return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function show(NewDifferentAccountPayment $newDifferentAccountPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paymentEdit = NewDifferentAccountPayment::find($id);
        $payments = NewDifferentAccountPayment::where('account_id', $paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = NewDifferentAccount::where('id', '=', $paymentEdit->account_id)->first();


        $debits_af = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',1)->where('account_id',$id)->where('status',1)->sum('amount');
        $debits_usd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',2)->where('account_id',$id)->where('status',1)->sum('amount');
        $debits_cd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',3)->where('account_id',$id)->where('status',1)->sum('amount');

        $credits_af = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',1)->where('account_id',$id)->where('status',1)->sum('amount');
        $credits_usd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',2)->where('account_id',$id)->where('status',1)->sum('amount');
        $credits_cd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',3)->where('account_id',$id)->where('status',1)->sum('amount');


        return view('new-different-account.account-payment', compact('account', 'payments', 'debits_af','debits_usd','debits_cd', 'credits_af','credits_usd','credits_cd', 'paymentEdit'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $payment_data = $request->validate([
            'amount' => 'required',
            'currency' => 'required',
            'description' => 'required',
            'date' => 'required',
            'insert_credit' => '',
            'type' => '',
            'account_id' => ''

        ]);



        $payment = NewDifferentAccountPayment::find($id);
        $payment->amount = $request->amount;
        $payment->currency = $request->currency;
        $payment->description = $request->description;
        $payment->date = $request->date;
        $payment->type = $request->type;
        $payment->save();

        $account = NewDifferentAccount::find($request->account_id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $account->name .  ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . ' ' . $request->currency . ' ' . $request->type . 'کرد' ;
        $activity->user_id = Auth::user()->id;
        $activity->save();




        if ($payment) {
            return redirect()->to('/dashboard/new-different-account/' . $request->account_id)->with('status', '  رسید پول موفقانه بروز شد !');
        } else {
            return redirect()->to('/dashboard/new-different-account/' . $request->account_id)->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = NewDifferentAccountPayment::find($id);

        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
