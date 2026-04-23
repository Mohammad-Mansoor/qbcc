<?php

namespace App\Http\Controllers;

use App\AjnasAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjnasAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountEdit = '';
        $asset_accounts = DB::table('ajnas_accounts')->get();
        return view('assets-accounts.assets-accounts',compact('accountEdit','asset_accounts'));
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
        $account = DB::table('ajnas_accounts')->insertGetId(['aa_name' => $request->aa_name, 'aa_type' => $request->aa_type,'aa_date' => $request->aa_date]);

        if ($account) {
            return redirect('/dashboard/assets-accounts')->with('status', 'موفقانه ذخیره شد');
        } else {
            return redirect('/dashboard/assets-accounts')->with('error', 'ذخیره نشد');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AjnasAccount  $ajnasAccount
     * @return \Illuminate\Http\Response
     */
    public function show($account_id)
    {

        $account = AjnasAccount::find($account_id);
        $asset_account_details = DB::table('ajnas_account_details')->where('ajnas_account_id',$account_id)->orderBy('aad_id','DESC')->get();
        $detailEdit = '';


        return view('assets-accounts.assets-accounts-details', compact('detailEdit', 'account','asset_account_details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AjnasAccount  $ajnasAccount
     * @return \Illuminate\Http\Response
     */
    public function edit($account_id)
    {
        $accountEdit = DB::table('ajnas_accounts')->where('aa_id', $account_id)->first();
        $asset_accounts = DB::table('ajnas_accounts')->get();
        return view('assets-accounts.assets-accounts',compact('accountEdit','asset_accounts'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AjnasAccount  $ajnasAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $account_id)
    {
        $account = DB::table('ajnas_accounts')->where('aa_id', $account_id)->update(['aa_name' => $request->aa_name, 'aa_type' => $request->aa_type,'aa_date' => $request->aa_date]);
        if ($account) {
            return redirect('/dashboard/assets-accounts')->with('status', 'موفقانه ذخیره شد');
        } else {
            return redirect('/dashboard/assets-accounts')->with('error', 'ذخیره نشد');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AjnasAccount  $ajnasAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($account_id)
    {
        $customer = DB::table('ajnas_accounts')->where('aa_id', $account_id)->delete();

        return response()->json(['status' => 'success']);
    }
}
