<?php

namespace App\Http\Controllers;

use App\Activity;
use App\DifferentAccount;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\OfficeCashBook;
use App\OfficeCredit;
use App\OfficeDebit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DifferentAccountPaymentController extends Controller
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
        $requests = DifferentAccountPayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('different-account.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = DifferentAccountPayment::find($id);


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
        $credit = DifferentAccountPayment::find($id);


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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($request->type == 'گرفت') {
            $received_data = $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
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
            $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();
            if (!$totalUpdate) {
                $total = new DifferentAccountTotal();
                $total->total = 0;
                $total->account_id = $request->account_id;
                $total->paid = $total->paid + $request->amount;
                $total->remaining = $total->total - $total->paid;
                $total->save();

                $account = DifferentAccount::find($request->account_id);

            } else {
                $totalUpdate->paid = $totalUpdate->paid + $request->amount;
                $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
                $totalUpdate->update();

            }


            $payed = DifferentAccountPayment::create($received_data);

            $account = DifferentAccount::find($request->account_id);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " حساب متفرقه به نام " . $account->name . ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . 'دالر گرفت کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();


            if ($payed) {
                return redirect()->back()->with('status', 'گرفت موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        } else {
            $payment_data = $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'insert_credit' => '',
                'type' => '',
                'account_id' => '',
                'status' =>''


            ]);
            if (Auth::user()->role == 'SP'){
                $payment_data['status'] = 1;
            }
            else{
                $payment_data['status'] = 0;
            }
            $payment = DifferentAccountPayment::create($payment_data);

            $account = DifferentAccount::find($request->account_id);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " حساب متفرقه به نام " . $account->name . ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . 'دالر رسید کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();
            if (!$totalUpdate) {
                $total = new DifferentAccountTotal();
                $total->total = $request->amount;
                $total->account_id = $request->account_id;
                $total->paid = 0;
                $total->remaining = $total->total - $total->paid;
                $total->save();


            } else {
                $totalUpdate->total = $totalUpdate->total + $request->amount;
                $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
                $totalUpdate->update();


            }
        }


        if ($payment) {
            return redirect()->back()->with('status', 'رسید موفقانه ثبت شد !');
        } else {
            return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function show(DifferentAccountPayment $differentAccountPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {




        $debits = DifferentAccountPayment::where('type', '=', 'گرفت')->where('account_id', $id)->where('status',1)->sum('amount');
        $credits = DifferentAccountPayment::where('type', '=', 'رسید')->where('account_id', $id)->where('status',1)->sum('amount');
        $paymentEdit = DifferentAccountPayment::find($id);
        $payments = DifferentAccountPayment::where('account_id', $paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = DifferentAccount::where('id', '=', $paymentEdit->account_id)->first();
        $total = DifferentAccountTotal::where('account_id', $paymentEdit->account_id)->sum('total');
        return view('different-account.account-payment', compact('account', 'payments', 'total', 'debits', 'credits', 'paymentEdit'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DifferentAccountPayment $differentAccountPayment)
    {

        $payment_data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'insert_credit' => '',
            'type' => '',
            'account_id' => ''

        ]);


        if ($request->type == 'گرفت') {
            if ($request->old_type == 'گرفت') {
                /** Updating total account agent */
                $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();
                $totalUpdate->paid = $totalUpdate->paid + $request->amount - $request->old_amount;
                $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
                $totalUpdate->update();
            } else {
                $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();
                $totalUpdate->total = $totalUpdate->total - $request->old_amount;
                $totalUpdate->paid = $totalUpdate->paid + $request->amount;
                $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
                $totalUpdate->update();
            }


        } else {
            if ($request->old_type == 'رسید') {
                $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();

                $totalUpdate->total = $totalUpdate->total + $request->amount - $request->old_amount;
                $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
                $totalUpdate->update();
            }
            else{
                $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();
                $totalUpdate->paid = $totalUpdate->paid - $request->old_amount;
                $totalUpdate->total = $totalUpdate->total + $request->amount;
                $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
                $totalUpdate->update();
            }
        }


        $payment = $differentAccountPayment->update($payment_data);

        $account = DifferentAccount::find($request->account_id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $account->name .  ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . 'دالر ویرایش شد  ';
        $activity->user_id = Auth::user()->id;
        $activity->save();


        if ($payment) {
            return redirect()->to('/dashboard/different-account/' . $request->account_id)->with('status', '  رسید پول موفقانه بروز شد !');
        } else {
            return redirect()->to('/dashboard/different-account/' . $request->account_id)->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DifferentAccountPayment $differentAccountPayment)
    {
        //
    }
}
