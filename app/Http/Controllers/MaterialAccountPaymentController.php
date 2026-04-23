<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialAccount;
use App\MaterialAccountPayment;
use App\MaterialType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialAccountPaymentController extends Controller
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

    public function request_material()
    {
        $requests = MaterialAccountPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        
        
     

        return view('material-accounts.requested-material-list', compact('requests'));
    }


    public function approve_request($id)
    {

        $payment = MaterialAccountPayment::find($id);


        $payment->status = 1;
        $payment->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');

        $activity->description = " مقدار " . $payment->amount . "کیلوگرام توسط سوپر ادمین اپروف شد ";

        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }

    public function delete_request($id)
    {
        $credit = MaterialAccountPayment::find($id);


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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => 'required',
            'type_id' => 'required',
            'account_id' => 'required',

        ]);


        $payed = new MaterialAccountPayment();
        $payed->amount = $request->amount;
        $payed->description = $request->description;
        $payed->date = $request->date;
        $payed->type = $request->type;
        $payed->type_id = $request->type_id;
        $payed->account_id = $request->account_id;

        if (Auth::user()->role == 'SP'){
            $payed->status = 1;
        }
        else{
            $payed->status = 0;
        }
        $payed->save();

        if ($payed) {

            return redirect()->back()->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialAccountPayment $materialAccountPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = MaterialAccountPayment::find($payment_id);

        $payments = MaterialAccountPayment::where('account_id',$paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = MaterialAccount::find($paymentEdit->account_id);

        $debits = MaterialAccountPayment::where('type','=','گرفت')->where('account_id',$paymentEdit->account_id)->where('status',1)->sum('amount');
        $credits = MaterialAccountPayment::where('type','=','رسید')->where('account_id',$paymentEdit->account_id)->where('status',1)->sum('amount');
        $material_type = MaterialType::all();

        return view('material-accounts.account-payment',compact('account','payments','paymentEdit','material_type','debits','credits'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'type_id' => '',
            'account_id' => '',

        ]);
        $payed = MaterialAccountPayment::find($payment_id);
        $payed->amount = $request->amount;
        $payed->description = $request->description;
        $payed->date = $request->date;
        $payed->type = $request->type;
        $payed->type_id = $request->type_id;
        $payed->account_id = $request->account_id;
        $payed->update();
        if ($payed) {
            return redirect('/dashboard/material-accounts/'.$request->account_id)->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/material-accounts/'.$request->account_id)->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = MaterialAccountPayment::find($id);
        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
