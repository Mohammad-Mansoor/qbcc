<?php

namespace App\Http\Controllers;

use App\AjnasAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjnasAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_assets_account')->only(['create', 'store']);
        $this->middleware('permission:edit_assets_account')->only(['edit', 'update']);
        $this->middleware('permission:delete_assets_account')->only(['destroy']);
    }

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
        $asset_account_details = \App\AjnasAccountDetails::where('ajnas_account_id', $account_id)
            ->with(['debitAccount', 'creditAccount'])
            ->orderBy('aad_id', 'DESC')
            ->get();
        $detailEdit = '';

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('ASSET_PURCH', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('ASSET_PURCH', 'credit');
        $mapping = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'ASSET_PURCH')->first();
        $currencies = DB::table('currencies')->where('is_active', 1)->get();

        return view('assets-accounts.assets-accounts-details', compact(
            'detailEdit', 'account','asset_account_details',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'
        ));
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
        $hasAssets = DB::table('ajnas_account_details')->where('ajnas_account_id', $account_id)->exists();

        if ($hasAssets) {
            return response()->json([
                'status' => 'error',
                'message' => 'شما نمی‌توانید این دسته بندی را حذف کنید زیرا اجناس (دارایی‌هایی) به آن مرتبط هستند. لطفاً ابتدا تمام اجناس مرتبط را حذف کنید و سپس این دسته بندی را پاک نمایید.'
            ]);
        }

        $customer = DB::table('ajnas_accounts')->where('aa_id', $account_id)->delete();

        return response()->json(['status' => 'success']);
    }
}
