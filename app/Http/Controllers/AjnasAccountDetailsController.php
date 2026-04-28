<?php

namespace App\Http\Controllers;

use App\AjnasAccount;
use App\AjnasAccountDetails;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjnasAccountDetailsController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postAssetToAccounting($id, $amount, $date, $name)
    {
        try {
            $this->accountingService->postAutoTransaction('ajnas_account', 'purchase', [
                'date' => $date,
                'amount' => $amount,
                'reference' => 'AJN-' . $id,
                'description' => 'خریداری جنس ثابت (Fixed Asset): ' . $name,
                'source_id' => $id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Ajnas Account Detail #" . $id . ": " . $e->getMessage());
        }
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
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

            $id = DB::table('ajnas_account_details')->insertGetId([
                'asset_name' => $request->asset_name, 
                'asset_class' => $request->asset_class,
                'asset_description'=>$request->asset_description,
                'physical_location'=>$request->physical_location,
                'asset_number'=>$request->asset_number,
                'asset_serial_number'=>$request->asset_serial_number,
                'acquisition_date'=>$request->acquisition_date,
                'acquisition_cost'=>$request->acquisition_cost,
                'estimated_useful_life'=>$request->estimated_useful_life,
                'estimated_salvage_value'=>$request->estimated_salvage_value,
                'ajnas_account_id'=>$request->assets_account_id
            ]);

            if ($id) {
                // Post to ledger
                $this->postAssetToAccounting($id, $request->acquisition_cost, $request->acquisition_date, $request->asset_name);
                return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد و در دفتر روزنامچه ثبت گردید');
            } else {
                return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('error', 'ذخیره نشد');
            }
        });
    }

    public function show($detail_id)
    {
        // Parameter name should match resource if needed, using the existing signature
    }

    public function edit($detail_id)
    {
        $detailEdit = DB::table('ajnas_account_details')->where('aad_id', $detail_id)->first();
        if (!$detailEdit) abort(404);
        
        $asset_account_details = DB::table('ajnas_account_details')->where('ajnas_account_id',$detailEdit->ajnas_account_id)->orderBy('aad_id','DESC')->get();
        $account = AjnasAccount::find($detailEdit->ajnas_account_id);

        return view('assets-accounts.assets-accounts-details', compact('detailEdit', 'account','asset_account_details'));
    }

    public function update(Request $request, $details_id)
    {
        return DB::transaction(function () use ($request, $details_id) {
            $request->validate([
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
            
            // Reversal
            $this->accountingService->reverseTransactionBySource($details_id, 'Asset details edited');

            $updated = DB::table('ajnas_account_details')->where('aad_id',$details_id)->update([
                'asset_name' => $request->asset_name, 
                'asset_class' => $request->asset_class,
                'asset_description'=>$request->asset_description,
                'physical_location'=>$request->physical_location,
                'asset_number'=>$request->asset_number,
                'asset_serial_number'=>$request->asset_serial_number,
                'acquisition_date'=>$request->acquisition_date,
                'acquisition_cost'=>$request->acquisition_cost,
                'estimated_useful_life'=>$request->estimated_useful_life,
                'estimated_salvage_value'=>$request->estimated_salvage_value
            ]);

            // Re-post
            $this->postAssetToAccounting($details_id, $request->acquisition_cost, $request->acquisition_date, $request->asset_name);

            return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد و در دفتر روزنامچه ثبت گردید');
        });
    }

    public function destroy($detail_id)
    {
        return DB::transaction(function () use ($detail_id) {
            // Reversal
            $this->accountingService->reverseTransactionBySource($detail_id, 'Asset details deleted');

            $ord = DB::table('ajnas_account_details')->where('aad_id', $detail_id)->delete();

            if ($ord) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error']);
            }
        });
    }
}
