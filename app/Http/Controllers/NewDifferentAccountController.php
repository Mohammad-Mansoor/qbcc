<?php

namespace App\Http\Controllers;

use App\Activity;
use App\NewDifferentAccount;
use App\NewDifferentAccountPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewDifferentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_different_account')->only(['create', 'store']);
        $this->middleware('permission:edit_different_account')->only(['edit', 'update']);
        $this->middleware('permission:delete_different_account')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountEdit = "";
        $all_accounts = NewDifferentAccount::all();
        $center_accounts = $all_accounts;
        $froshat_accounts = $all_accounts;
        $mo_accounts = $all_accounts;
        $sp_accounts = $all_accounts;

        return view('new-different-account.accounts',compact('accountEdit','center_accounts','froshat_accounts','mo_accounts','sp_accounts'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $accountEdit = "";

        $matched_accounts = NewDifferentAccount::where('name','like','%'.$search.'%')
            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('address','like','%'.$search.'%')
            ->get();

        $center_accounts = $matched_accounts;
        $froshat_accounts = $matched_accounts;
        $mo_accounts = $matched_accounts;
        $sp_accounts = $matched_accounts;

        return view('new-different-account.accounts',compact('accountEdit','center_accounts','froshat_accounts','mo_accounts','sp_accounts','search'));
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
        $data = $this->valData();
        $data['user_role'] = Auth::user()->role;

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $account = NewDifferentAccount::create($data);

        if ($account) {
            return redirect('/dashboard/new-different-account')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/new-different-account')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NewDifferentAccount  $newDifferentAccount
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = NewDifferentAccount::find($id);

        $payments = NewDifferentAccountPayment::where('account_id',$id)->orderBy('created_at','DESC')->paginate(30);


        $debits_af = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',1)->where('account_id',$id)->where('status',1)->sum('amount');
        $debits_usd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',2)->where('account_id',$id)->where('status',1)->sum('amount');
        $debits_cd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',3)->where('account_id',$id)->where('status',1)->sum('amount');

        $credits_af = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',1)->where('account_id',$id)->where('status',1)->sum('amount');
        $credits_usd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',2)->where('account_id',$id)->where('status',1)->sum('amount');
        $credits_cd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',3)->where('account_id',$id)->where('status',1)->sum('amount');

        $paymentEdit = '';

        return view('new-different-account.account-payment',compact('account','payments','debits_af','debits_usd','debits_cd','credits_af','credits_usd','credits_cd','paymentEdit'));


    }
    public function show_all_payment($account_id){
        $account = NewDifferentAccount::find($account_id);

        $payments = NewDifferentAccountPayment::where('account_id',$account_id)->orderBy('created_at','DESC')->get();

        $debits_af = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',1)->where('account_id',$account_id)->where('status',1)->sum('amount');
        $debits_usd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',2)->where('account_id',$account_id)->where('status',1)->sum('amount');
        $debits_cd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',3)->where('account_id',$account_id)->where('status',1)->sum('amount');

        $credits_af = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',1)->where('account_id',$account_id)->where('status',1)->sum('amount');
        $credits_usd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',2)->where('account_id',$account_id)->where('status',1)->sum('amount');
        $credits_cd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',3)->where('account_id',$account_id)->where('status',1)->sum('amount');

        $paymentEdit = '';
        $all = '';
        return view('new-different-account.account-payment',compact('account','payments','debits_af','debits_usd','debits_cd','credits_af','credits_usd','credits_cd','paymentEdit','all'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NewDifferentAccount  $newDifferentAccount
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accountEdit = NewDifferentAccount::find($id);

        $center_accounts = NewDifferentAccount::where('user_role','CO')->orWhere('user_role','CCO')->get();
        $froshat_accounts = NewDifferentAccount::where('user_role','SO')->orWhere('user_role','SCO')->get();
        $mo_accounts = NewDifferentAccount::where('user_role','MO')->get();
        $sp_accounts = NewDifferentAccount::all();
        return view('new-different-account.accounts',compact('center_accounts','froshat_accounts','mo_accounts','sp_accounts', 'accountEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NewDifferentAccount  $newDifferentAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $account =  NewDifferentAccount::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $account->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $account->update($this->valData());



        if ($account) {
            return redirect('/dashboard/new-different-account')->with('status', ' موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/new-different-account')->with('error', 'مشکل در سرور وجود داره!');
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
     * @param  \App\NewDifferentAccount  $newDifferentAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = NewDifferentAccount::find($id);


        $account->delete();


        return response()->json(['status','error']);
    }
}
