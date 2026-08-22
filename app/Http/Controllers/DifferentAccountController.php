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
    private function calculateBalances()
    {
        $rates = Currency::pluck('exchange_rate', 'code')->toArray();
        $totals = DifferentAccountTotal::all();
        $remaining = 0;
        $talab = 0;
        foreach ($totals as $t) {
            $rate = $rates[$t->currency_code] ?? 1.0;
            $val = $t->remaining * $rate;
            if ($val < 0) {
                $remaining += abs($val);
            } else {
                $talab += $val;
            }
        }
        return [
            'remaining' => $remaining,
            'talab' => $talab
        ];
    }

    public function index()
    {
        $accountEdit = "";
        $all_accounts = DifferentAccount::all();
        $center_accounts = $all_accounts;
        $froshat_accounts = $all_accounts;
        $mo_accounts = $all_accounts;
        $sp_accounts = $all_accounts;
        
        $balances = $this->calculateBalances();
        $remaining = $balances['remaining'];
        $talab = $balances['talab'];

        return view('different-account.accounts',compact('accountEdit','center_accounts','froshat_accounts','mo_accounts','sp_accounts','remaining','talab'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $accountEdit = "";

        $matched_accounts = DifferentAccount::where('name','like','%'.$search.'%')
            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('address','like','%'.$search.'%')
            ->get();

        $center_accounts = $matched_accounts;
        $froshat_accounts = $matched_accounts;
        $mo_accounts = $matched_accounts;
        $sp_accounts = $matched_accounts;

        $balances = $this->calculateBalances();
        $remaining = $balances['remaining'];
        $talab = $balances['talab'];

        return view('different-account.accounts',compact('accountEdit','center_accounts','froshat_accounts','mo_accounts','sp_accounts','search','remaining','talab'));
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
        $payments = DifferentAccountPayment::with(['debitAccount', 'creditAccount'])->where('account_id',$id)->orderBy('created_at','DESC')->paginate(30);
        $totals = \App\DifferentAccountTotal::where('account_id', $id)->get();
        $paymentEdit = '';
        $currencies = Currency::all();
        $chartOfAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingIn = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'DIFF_IN')->first();
        $mappingOut = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'DIFF_OUT')->first();
        return view('different-account.account-payment',compact('account','payments','totals','paymentEdit', 'currencies', 'chartOfAccounts', 'mappingIn', 'mappingOut'));
    }

    public function show_all_payment($account_id){
        $account = DifferentAccount::find($account_id);
        $payments = DifferentAccountPayment::with(['debitAccount', 'creditAccount'])->where('account_id',$account_id)->orderBy('created_at','DESC')->get();
        $totals = \App\DifferentAccountTotal::where('account_id', $account_id)->get();
        $paymentEdit = '';
        $all = '';
        $currencies = Currency::all();
        $chartOfAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingIn = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'DIFF_IN')->first();
        $mappingOut = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'DIFF_OUT')->first();
        return view('different-account.account-payment',compact('account','payments','totals','paymentEdit','all', 'currencies', 'chartOfAccounts', 'mappingIn', 'mappingOut'));
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
        $balances = $this->calculateBalances();
        $remaining = $balances['remaining'];
        $talab = $balances['talab'];
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

    public function updateNote(Request $request, $id)
    {
        $account = DifferentAccount::findOrFail($id);
        $account->note = $request->note;
        $account->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'یادداشت با موفقیت بروز رسانی شد', 'note' => $account->note]);
        }

        return redirect()->back()->with('status', 'یادداشت با موفقیت بروز رسانی شد');
    }

    protected function valData()
    {
        return request()->validate([
            'name' => 'required|min:2|max:256',
            'address' => 'required|min:2|max:256',
            'phone' => 'required|min:3|max:14',
            'user_role' => '',
            'note' => 'nullable',
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
