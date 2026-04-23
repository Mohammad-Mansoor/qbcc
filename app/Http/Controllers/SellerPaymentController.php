<?php

namespace App\Http\Controllers;

use App\Activity;
use App\PurchaseMaterial;
use App\SellerPayment;
use App\StringSeller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerPaymentController extends Controller
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

    public function request_list()
    {
        $requests = SellerPayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('string-seller.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = SellerPayment::find($id);


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
        $credit = SellerPayment::find($id);


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
            'seller_id' => '',
            'dollar_rate' => '',
            'purchase_number' => '',
            'status' => ''

        ]);
        $seller_name = DB::table('string_sellers')->where('id', $request->seller_id)->first();

        if($request->money_type == 'دالر'){

            $payed = new SellerPayment();
            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->seller_id = $request->seller_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->purchase_number = $request->purchase_number;
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
                $activity->description = " فروشنده مواد به نام  " . $seller_name->name .  ' اکونت نمبر '. $seller_name->id. " به مبلغ " . $request->amount . " دالر را " . $request->type . ' کرد ';
                $activity->user_id = Auth::user()->id;
                $activity->save();

                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = new SellerPayment();
            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->seller_id = $request->seller_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->purchase_number = $request->purchase_number;
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
                $activity->description = " فروشنده مواد به نام  " . $seller_name->name .   ' اکونت نمبر '. $seller_name->id. " به مبلغ " . $request->amount . " افغانی را " . $request->type . ' کرد ';
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
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function show($seller_id)
    {
        $payments = SellerPayment::where('seller_id',$seller_id)->orderBy('created_at','DESC')->paginate(30);
        $seller = StringSeller::find($seller_id);
        $debits_us = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $debits_af = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $credit_us = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $credit_af = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$seller_id)->distinct()->get(['purchase_number']);
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','purchase_numbers'));
    }
    public function show_all_payment($seller_id){
        $payments = SellerPayment::where('seller_id',$seller_id)->orderBy('created_at','DESC')->get();
        $seller = StringSeller::find($seller_id);
        $debits_us = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $debits_af = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $credit_us = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $credit_af = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$seller_id)->distinct()->get(['purchase_number']);
        $all = '';
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','purchase_numbers','all'));

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = SellerPayment::find($payment_id);
        $payments = SellerPayment::where('seller_id',$paymentEdit->seller_id)->orderBy('created_at','DESC')->paginate(30);
        $seller = StringSeller::find($paymentEdit->seller_id);
        $debits_us = SellerPayment::where('type','=','گرفت')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount');
        $debits_af = SellerPayment::where('type','=','گرفت')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount_af');
        $credit_us = SellerPayment::where('type','=','رسید')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount');
        $credit_af = SellerPayment::where('type','=','رسید')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount_af');
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$paymentEdit->seller_id)->distinct()->get(['purchase_number']);
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','purchase_numbers'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'seller_id' => '',
            'dollar_rate' => '',
            'purchase_number' => '',

        ]);
        $seller_name = DB::table('string_sellers')->where('id', $request->seller_id)->first();

        if($request->money_type == 'دالر'){

            $payed = SellerPayment::find($payment_id);

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
            $activity->description = " در بیلانس فروشنده مواد به نام  " . $seller_name->name .   ' اکونت نمبر '. $seller_name->id. "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود  ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->seller_id = $request->seller_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->purchase_number = $request->purchase_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/string-seller-payments/'.$request->seller_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/string-seller-payments/'.$request->seller_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = SellerPayment::find($payment_id);

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
            $activity->description = " در بیلانس فروشنده مواد به نام  " . $seller_name->name .   ' اکونت نمبر '. $seller_name->id. "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();


            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->seller_id = $request->seller_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->purchase_number = $request->purchase_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/string-seller-payments/'.$request->seller_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/string-seller-payments/'.$request->seller_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = SellerPayment::find($id);

        $seller_name = DB::table('string_sellers')->where('id', $payment->seller_id)->first();

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
        $activity->description = "از بیلانس فروشنده مواد به نام  " . $seller_name->name .   ' اکونت نمبر '. $seller_name->id. " مبلغ " . $amount . ' '. $money . " را حذف کرد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
