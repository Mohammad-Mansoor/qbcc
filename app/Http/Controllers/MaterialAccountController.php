<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialAccount;
use App\MaterialAccountPayment;
use App\MaterialType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $accounts = $this->enrichAccounts(MaterialAccount::all());
        return view('material-accounts.accounts',compact('accounts','accountEdit'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $accountEdit = "";
        $accounts = $this->enrichAccounts(MaterialAccount::where('name', 'like','%'.$search.'%')->get());
        return view('material-accounts.accounts',compact('accounts','accountEdit'));
    }

    private function enrichAccounts($accounts)
    {
        return $accounts->map(function($acc) {
            // 1. Accounting Balance (Real-time from ledger)
            $acc->ledger_balance = DB::table('ledger_entries')
                ->where('party_type', 'App\MaterialAccount')
                ->where('party_id', $acc->id)
                ->sum(DB::raw("credit - debit"));

            // 2. Physical Weight (From MaterialAccountPayments)
            $acc->physical_weight = DB::table('material_account_payments')
                ->where('account_id', $acc->id)
                ->where('status', 1)
                ->sum(DB::raw("CASE WHEN type = 'رسید' THEN amount ELSE -amount END"));

            // 3. Stock Valuation (WAC based)
            // Get average WAC for material types associated with this account
            $avgWac = DB::table('material_account_payments')
                ->join('items', 'material_account_payments.type_id', '=', 'items.ref_id')
                ->where('material_account_payments.account_id', $acc->id)
                ->avg('items.current_cost') ?? 0;
            
            $acc->valuation = $acc->physical_weight * $avgWac;

            return $acc;
        });
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
        $payments = MaterialAccountPayment::where('account_id',$id)->orderBy('created_at', 'DESC')->paginate(50);
        
        $debits = MaterialAccountPayment::where('type','=','گرفت')->where('account_id',$id)->where('status',1)->sum('amount');
        $credits = MaterialAccountPayment::where('type','=','رسید')->where('account_id',$id)->where('status',1)->sum('amount');

        $paymentEdit = '';
        $material_type = MaterialType::all();

        $selectionService = new \App\Services\AccountSelectionService();
        
        // For Payment OUT (گرفت)
        $allowedDebitAccountsOut = $selectionService->getValidAccounts('MATERIAL_PAYMENT', 'debit');
        $allowedCreditAccountsOut = $selectionService->getValidAccounts('MATERIAL_PAYMENT', 'credit');
        $mappingOut = \App\MappingRule::where('mapping_key', 'MATERIAL_PAYMENT')->first();

        // For Receipt IN (رسید)
        $allowedDebitAccountsIn = $selectionService->getValidAccounts('MATERIAL_RECEIPT', 'debit');
        $allowedCreditAccountsIn = $selectionService->getValidAccounts('MATERIAL_RECEIPT', 'credit');
        $mappingIn = \App\MappingRule::where('mapping_key', 'MATERIAL_RECEIPT')->first();

        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::where('is_active', 1)->get();
        $baseCurrency = \App\Currency::where('is_base_currency', 1)->first();

        return view('material-accounts.account-payment', compact(
            'account', 'payments', 'paymentEdit', 'material_type', 'debits', 'credits',
            'allowedDebitAccountsOut', 'allowedCreditAccountsOut', 'mappingOut',
            'allowedDebitAccountsIn', 'allowedCreditAccountsIn', 'mappingIn',
            'warehouses', 'currencies', 'baseCurrency'
        ));
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
        return response()->json(['status' => 'success']);
    }
}
