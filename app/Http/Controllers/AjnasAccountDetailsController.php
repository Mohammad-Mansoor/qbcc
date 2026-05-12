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

    private function postAssetToAccounting($id, $amount, $date, $name, $overrides = [])
    {
        try {
            $this->accountingService->postAutoTransaction('ajnas_account', 'purchase', array_merge([
                'date' => $date,
                'amount' => $amount,
                'reference' => 'AJN-' . $id,
                'description' => 'خریداری جنس ثابت (Fixed Asset): ' . $name,
                'source_id' => $id,
            ], $overrides));
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

            $imageName = null;
            if ($request->hasFile('asset_image')) {
                $image = $request->file('asset_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move('uploads/assets', $imageName);
            }

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
                'asset_image'=>$imageName,
                'ajnas_account_id'=>$request->assets_account_id
            ]);

            if ($id) {
                // Post to ledger with overrides
                $overrides = [];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;

                $this->postAssetToAccounting($id, $request->acquisition_cost, $request->acquisition_date, $request->asset_name, $overrides);
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

            $updateData = [
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
            ];

            if ($request->hasFile('asset_image')) {
                $image = $request->file('asset_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move('uploads/assets', $imageName);
                $updateData['asset_image'] = $imageName;
            }

            $updated = DB::table('ajnas_account_details')->where('aad_id',$details_id)->update($updateData);

            // Re-post
            $this->postAssetToAccounting($details_id, $request->acquisition_cost, $request->acquisition_date, $request->asset_name);

            return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد و در دفتر روزنامچه ثبت گردید');
        });
    }

    public function destroy($detail_id)
    {
        $accountingService = $this->accountingService;
        return DB::transaction(function () use ($detail_id, $accountingService) {
            // Reversal
            $accountingService->reverseTransactionBySource($detail_id, 'Asset details deleted', 'Ajnas_account');

            $ord = DB::table('ajnas_account_details')->where('aad_id', $detail_id)->delete();

            if ($ord) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error']);
            }
        });
    }
}
