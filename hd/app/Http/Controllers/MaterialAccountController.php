<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialAccount;
use App\MaterialAccountPayment;
use App\MaterialType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountEdit = "";
        $accounts = MaterialAccount::all();
        return view('material-accounts.accounts',compact('accounts','accountEdit'));
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
            'name' => 'required',
            'date' => 'required',
            'account_year' => 'required',

        ]);

        $account = MaterialAccount::create($data);

        if ($account) {
            return redirect('/dashboard/material-accounts')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/material-accounts')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialAccount  $materialAccount
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $account = MaterialAccount::find($id);
        $payments = MaterialAccountPayment::where('account_id',$id)->get();

        $debits = MaterialAccountPayment::where('type','=','گرفت')->where('account_id',$id)->where('status',1)->sum('amount');
        $credits = MaterialAccountPayment::where('type','=','رسید')->where('account_id',$id)->where('status',1)->sum('amount');

        $paymentEdit = '';
        $material_type = MaterialType::all();
        return view('material-accounts.account-payment',compact('account','payments','paymentEdit','material_type','debits','credits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialAccount  $materialAccount
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accountEdit = MaterialAccount::find($id);
        $accounts = MaterialAccount::all();
        return view('material-accounts.accounts',compact('accounts','accountEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MaterialAccount  $materialAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $account =  MaterialAccount::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب تار به نام " . $account->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $data = $request->validate([
            'name' => 'required',
            'date' => 'required',
            'account_year' => 'required',

        ]);

        $account->update($data);


        if ($account) {
            return redirect('/dashboard/material-accounts')->with('status', ' موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/material-accounts')->with('error', 'مشکل در سرور وجود داره!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialAccount  $materialAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = MaterialAccount::find($id);


        $account->delete();


        return response()->json(['status','error']);
    }
}
