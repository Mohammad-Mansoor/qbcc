<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetRepair;
use App\Kachaee;
use App\KachaeePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KachaeePaymentController extends Controller
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
        $requests = KachaeePayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('kachaee.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = KachaeePayment::find($id);


        $payment->status = 1 ;
        $payment->update();


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
        $credit = KachaeePayment::find($id);


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
            'kachaee_number' => '',

        ]);

        $team_name = DB::table('kachaees')->where('id', $request->team_id)->first();


        if($request->money_type == 'دالر'){

            $payed = new KachaeePayment();
            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->kachaee_number = $request->kachaee_number;
            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();
            if ($payed) {

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مشتری به نام  " . $team_name->name .  ' اکونت نمبر '. $team_name->id. " به مبلغ " . $request->amount . " دالر را " . $request->type . ' کرد ';
                $activity->user_id = Auth::user()->id;
                $activity->save();

                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = new KachaeePayment();
            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->kachaee_number = $request->kachaee_number;
            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();
            if ($payed) {

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مشتری به نام  " . $team_name->name .  ' اکونت نمبر '. $team_name->id. " به مبلغ " . $request->amount . " افغانی را " . $request->type . ' کرد ';
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
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function show($team_id)
    {


        $payments = KachaeePayment::where('team_id',$team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = Kachaee::find($team_id);

        $debits_us = KachaeePayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = KachaeePayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = KachaeePayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = KachaeePayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $kachaee_numbers = CarpetRepair::where('team_id','=',$team_id)->distinct()->get(['kachaee_number']);
        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','kachaee_numbers'));

    }
    public function show_all_payment($team_id){
        $payments = KachaeePayment::where('team_id',$team_id)->orderBy('created_at','DESC')->get();
        $team = Kachaee::find($team_id);

        $debits_us = KachaeePayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = KachaeePayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = KachaeePayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = KachaeePayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $kachaee_numbers = CarpetRepair::where('team_id','=',$team_id)->distinct()->get(['kachaee_number']);
        $all = '';
        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','kachaee_numbers','all'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = KachaeePayment::find($payment_id);
        $payments = KachaeePayment::where('team_id',$paymentEdit->team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = Kachaee::find($paymentEdit->team_id);
        $debits_us = KachaeePayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $debits_af = KachaeePayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $credit_us = KachaeePayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $credit_af = KachaeePayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $kachaee_numbers = CarpetRepair::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['kachaee_number']);
        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','kachaee_numbers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$payment_id)
    {
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'team_id' => '',
            'dollar_rate' => '',
            'kachaee_number' => '',

        ]);
        $team_name = DB::table('kachaees')->where('id', $request->team_id)->first();

        if($request->money_type == 'دالر'){

            $payed = KachaeePayment::find($payment_id);

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
            $activity->description = " در بیلانس کچایی گر به نام  " . $team_name->name .  ' اکونت نمبر '. $team_name->id. "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود  ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();


            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->kachaee_number = $request->kachaee_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/kachaee-payments/'.$request->team_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/kachaee-payments/'.$request->team_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = KachaeePayment::find($payment_id);

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
            $activity->description = " در بیلانس کچایی گر به نام  " . $team_name->name .  ' اکونت نمبر '. $team_name->id. "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->kachaee_number = $request->kachaee_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/kachaee-payments/'.$request->team_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/kachaee-payments/'.$request->team_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = KachaeePayment::find($id);
        $team_name = DB::table('kachaees')->where('id', $payment->team_id)->first();

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
        $activity->description = "از بیلانس کچایی گر به نام  " . $team_name->name .  ' اکونت نمبر '. $team_name->id. " مبلغ " . $amount . ' '. $money . " را حذف کرد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
