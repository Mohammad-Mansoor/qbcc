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
        $this->middleware('permission:manage_assets_account')->only(['store', 'edit', 'update', 'destroy']);
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
            throw $e; // Bubble up exception to trigger DB transaction rollback
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
        try {
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
                    'currency_id' => 'required',
                    'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                    'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
                ]);

                $imageName = null;
                if ($request->hasFile('asset_image')) {
                    $image = $request->file('asset_image');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move('uploads/assets', $imageName);
                }

                // Fetch Currency snapshot for USD normalization
                $currency = DB::table('currencies')->find($request->currency_id);
                $rate = $currency ? $currency->exchange_rate : 1.0;
                $currencyCode = $currency ? $currency->code : 'USD';

                $originalAmount = (float) $request->acquisition_cost;
                $acquisitionCostUsd = round($originalAmount * $rate, 4);
                $salvageValueUsd = round(((float)$request->estimated_salvage_value) * $rate, 4);

                $id = DB::table('ajnas_account_details')->insertGetId([
                    'asset_name' => $request->asset_name, 
                    'asset_class' => $request->asset_class,
                    'asset_description'=>$request->asset_description,
                    'physical_location'=>$request->physical_location,
                    'asset_number'=>$request->asset_number,
                    'asset_serial_number'=>$request->asset_serial_number,
                    'acquisition_date'=>$request->acquisition_date,
                    'acquisition_cost'=>$acquisitionCostUsd, // USD-normalized base amount
                    'currency_id' => $request->currency_id,
                    'currency_code' => $currencyCode,
                    'exchange_rate' => $rate,
                    'original_amount' => $originalAmount,
                    'estimated_useful_life'=>$request->estimated_useful_life,
                    'estimated_salvage_value'=>$salvageValueUsd, // USD-normalized salvage value
                    'asset_image'=>$imageName,
                    'ajnas_account_id'=>$request->assets_account_id,
                    'override_debit_account_id' => $request->override_debit_account_id,
                    'override_credit_account_id' => $request->override_credit_account_id,
                ]);

                if ($id) {
                    // Post to ledger with full FX overrides
                    $overrides = [
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $rate,
                    ];
                    if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                    if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;

                    $this->postAssetToAccounting($id, $originalAmount, $request->acquisition_date, $request->asset_name, $overrides);
                    return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد و در دفتر روزنامچه ثبت گردید');
                } else {
                    return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('error', 'ذخیره نشد');
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'خطا در ثبت معامله حسابی: ' . $e->getMessage());
        }
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

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('ASSET_PURCH', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('ASSET_PURCH', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'ASSET_PURCH')->first();
        $currencies = DB::table('currencies')->where('is_active', 1)->get();

        return view('assets-accounts.assets-accounts-details', compact(
            'detailEdit', 'account','asset_account_details',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'
        ));
    }

    public function update(Request $request, $details_id)
    {
        try {
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
                    'currency_id' => 'required',
                    'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                    'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
                ]);
                
                // Reversal with correct source_type 'Ajnas_account'
                $this->accountingService->reverseTransactionBySource($details_id, 'Asset details edited', 'Ajnas_account');

                // Fetch Currency snapshot for USD normalization
                $currency = DB::table('currencies')->find($request->currency_id);
                $rate = $currency ? $currency->exchange_rate : 1.0;
                $currencyCode = $currency ? $currency->code : 'USD';

                $originalAmount = (float) $request->acquisition_cost;
                $acquisitionCostUsd = round($originalAmount * $rate, 4);
                $salvageValueUsd = round(((float)$request->estimated_salvage_value) * $rate, 4);

                $updateData = [
                    'asset_name' => $request->asset_name, 
                    'asset_class' => $request->asset_class,
                    'asset_description'=>$request->asset_description,
                    'physical_location'=>$request->physical_location,
                    'asset_number'=>$request->asset_number,
                    'asset_serial_number'=>$request->asset_serial_number,
                    'acquisition_date'=>$request->acquisition_date,
                    'acquisition_cost'=>$acquisitionCostUsd, // USD-normalized base amount
                    'currency_id' => $request->currency_id,
                    'currency_code' => $currencyCode,
                    'exchange_rate' => $rate,
                    'original_amount' => $originalAmount,
                    'estimated_useful_life'=>$request->estimated_useful_life,
                    'estimated_salvage_value'=>$salvageValueUsd, // USD-normalized salvage value
                    'override_debit_account_id' => $request->override_debit_account_id,
                    'override_credit_account_id' => $request->override_credit_account_id,
                ];

                if ($request->hasFile('asset_image')) {
                    $image = $request->file('asset_image');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move('uploads/assets', $imageName);
                    $updateData['asset_image'] = $imageName;
                }

                $updated = DB::table('ajnas_account_details')->where('aad_id',$details_id)->update($updateData);

                // Re-post with full FX overrides
                $overrides = [
                    'currency_code' => $currencyCode,
                    'exchange_rate' => $rate,
                ];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;

                $this->postAssetToAccounting($details_id, $originalAmount, $request->acquisition_date, $request->asset_name, $overrides);

                return redirect('/dashboard/assets-accounts/'.$request->assets_account_id)->with('status', 'موفقانه ذخیره شد و در دفتر روزنامچه ثبت گردید');
            });
        } catch (\Exception $e) {
            // Find assets_account_id to redirect back properly
            $asset = DB::table('ajnas_account_details')->where('aad_id', $details_id)->first();
            $assetsAccountId = $asset ? $asset->ajnas_account_id : 1;
            return redirect('/dashboard/assets-accounts/' . $assetsAccountId)->with('error', 'خطا در ویرایش معامله حسابی: ' . $e->getMessage());
        }
    }

    public function destroy($detail_id)
    {
        $accountingService = $this->accountingService;
        return DB::transaction(function () use ($detail_id, $accountingService) {
            try {
                // Reversal
                $accountingService->reverseTransactionBySource($detail_id, 'Asset details deleted', 'Ajnas_account');

                $ord = DB::table('ajnas_account_details')->where('aad_id', $detail_id)->delete();

                if ($ord) {
                    return response()->json(['status' => 'success']);
                } else {
                    return response()->json(['status' => 'error']);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        });
    }
}
