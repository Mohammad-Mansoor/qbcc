<?php

namespace App\Http\Controllers;

use App\AjnasAccount;
use App\AjnasAccountDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjnasAccountDetailsController extends Controller
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
            'asset_name' => 'required',
            'asset_class' => 'required',
            'asset_description' => 'required',
            'physical_location' => 'required',
            'asset_number' => 'required',
            'asset_serial_number' => 'required',
            'acquisition_date' => 'required',
            'acquisition_cost' => 'required',
            'estimated_useful_life' => 'required',
            'estimated_salvage_value' => 'required',
            'assets_account_id' => 'required',

        ]);
        $account = DB::table('ajnas_account_details')->insertGetId(['asset_name' => $request->asset_name, 'asset_class' => $request->asset_class,
            'asset_description'=>$request->asset_description,'physical_location'=>$request->physical_location,'asset_number'=>$request->asset_number,
            'asset_serial_number'=>$request->asset_serial_number,'acquisition_date'=>$request->acquisition_date,'acquisition_cost'=>$request->acquisition_cost,
            'estimated_useful_life'=>$request->estimated_useful_life,'estimated_salvage_value'=>$request->estimated_salvage_value,'ajnas_account_id'=>$request->assets_account_id
        ]);

        if ($account) {
            return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد');
        } else {
            return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('error', 'ذخیره نشد');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AjnasAccountDetails  $ajnasAccountDetails
     * @return \Illuminate\Http\Response
     */
    public function show(AjnasAccountDetails $ajnasAccountDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AjnasAccountDetails  $ajnasAccountDetails
     * @return \Illuminate\Http\Response
     */
    public function edit($detail_id)
    {
        $detailEdit = AjnasAccountDetails::find($detail_id);
        $asset_account_details = DB::table('ajnas_account_details')->where('ajnas_account_id',$detailEdit->ajnas_account_id)->orderBy('aad_id','DESC')->get();
        $account = AjnasAccount::find($detailEdit->ajnas_account_id);


        return view('assets-accounts.assets-accounts-details', compact('detailEdit', 'account','asset_account_details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AjnasAccountDetails  $ajnasAccountDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $details_id)
    {
        $data = $request->validate([
            'asset_name' => 'required',
            'asset_class' => 'required',
            'asset_description' => 'required',
            'physical_location' => 'required',
            'asset_number' => 'required',
            'asset_serial_number' => 'required',
            'acquisition_date' => 'required',
            'acquisition_cost' => 'required',
            'estimated_useful_life' => 'required',
            'estimated_salvage_value' => 'required',

        ]);
        $account = DB::table('ajnas_account_details')->where('aad_id',$details_id)->update(['asset_name' => $request->asset_name, 'asset_class' => $request->asset_class,
            'asset_description'=>$request->asset_description,'physical_location'=>$request->physical_location,'asset_number'=>$request->asset_number,
            'asset_serial_number'=>$request->asset_serial_number,'acquisition_date'=>$request->acquisition_date,'acquisition_cost'=>$request->acquisition_cost,
            'estimated_useful_life'=>$request->estimated_useful_life,'estimated_salvage_value'=>$request->estimated_salvage_value
        ]);

        if ($account) {
            return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد');
        } else {
            return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('error', 'ذخیره نشد');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AjnasAccountDetails  $ajnasAccountDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy($detail_id)
    {
        $ord = DB::table('ajnas_account_details')->where('aad_id', $detail_id)->delete();

        if ($ord) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
}
