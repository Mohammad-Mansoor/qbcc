<?php

namespace App\Http\Controllers;

use App\Activity;
use App\DifferentAccount;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DifferentAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountEdit = "";
        $center_accounts = DifferentAccount::where('user_role','CO')->orWhere('user_role','CCO')->get();
        $remaining = DifferentAccountTotal::where('remaining','<',0)->sum('remaining');
        $talab = DifferentAccountTotal::where('remaining','>',0)->sum('remaining');

        $froshat_accounts = DifferentAccount::where('user_role','SO')->orWhere('user_role','SCO')->get();
        $mo_accounts = DifferentAccount::where('user_role','MO')->get();
        $sp_accounts = DifferentAccount::all();
        return view('different-account.accounts',compact('accountEdit','center_accounts','froshat_accounts','mo_accounts','sp_accounts'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $accountEdit = "";
        $center_accounts = DifferentAccount::where('user_role','CO')->orWhere('user_role','CCO')->where('name','like','%'.$search.'%')
            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('address','like','%'.$search.'%')
            ->get();
        $froshat_accounts = DifferentAccount::where('user_role','SO')->orWhere('user_role','SCO')->where('name','like','%'.$search.'%')
            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('address','like','%'.$search.'%')
            ->get();
        $mo_accounts = DifferentAccount::where('user_role','MO')->where('name','like','%'.$search.'%')
            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('address','like','%'.$search.'%')
            ->get();
        $sp_accounts = DifferentAccount::where('name','like','%'.$search.'%')
            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('address','like','%'.$search.'%')
            ->get();
        return view('different-account.accounts',compact('accountEdit','center_accounts','froshat_accounts','mo_accounts','sp_accounts','search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->valData();
        $data['user_role'] = Auth::user()->role;

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $account = DifferentAccount::create($data);

        if ($account) {
            return redirect('/dashboard/different-account')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/different-account')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = DifferentAccount::find($id);
        $payments = DifferentAccountPayment::where('account_id',$id)->orderBy('created_at','DESC')->paginate(30);
        $totals = \App\DifferentAccountTotal::where('account_id', $id)->get();
        $paymentEdit = '';
        $currencies = Currency::all();
        return view('different-account.account-payment',compact('account','payments','totals','paymentEdit', 'currencies'));
    }

    public function show_all_payment($account_id){
        $account = DifferentAccount::find($account_id);
        $payments = DifferentAccountPayment::where('account_id',$account_id)->orderBy('created_at','DESC')->get();
        $totals = \App\DifferentAccountTotal::where('account_id', $account_id)->get();
        $paymentEdit = '';
        $all = '';
        $currencies = Currency::all();
        return view('different-account.account-payment',compact('account','payments','totals','paymentEdit','all', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accountEdit = DifferentAccount::find($id);
        $remaining = DifferentAccountTotal::where('remaining','<',0)->sum('remaining');
        $talab = DifferentAccountTotal::where('remaining','>',0)->sum('remaining');
        $center_accounts = DifferentAccount::where('user_role','CO')->orWhere('user_role','CCO')->get();
        $froshat_accounts = DifferentAccount::where('user_role','SO')->orWhere('user_role','SCO')->get();
        $mo_accounts = DifferentAccount::where('user_role','MO')->get();
        $sp_accounts = DifferentAccount::all();
        return view('different-account.accounts',compact('center_accounts','froshat_accounts','mo_accounts','sp_accounts', 'accountEdit','remaining','talab'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $account =  DifferentAccount::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $account->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $account->update($this->valData());

        if ($account) {
            return redirect('/dashboard/different-account')->with('status', ' موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/different-account')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    protected function valData()
    {
        return request()->validate([
            'name' => 'required|min:2|max:256',
            'address' => 'required|min:2|max:256',
            'phone' => 'required|min:3|max:14',
            'user_role' => '',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = DifferentAccount::find($id);
        $account->delete();
        return response()->json(['status' => 'success']);
    }
}
