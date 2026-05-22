<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetCheckBook;
use App\CarpetMaterial;
use App\Carpet;
use App\MaterialCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\MaterialStock;
use App\MaterialType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;
use App\Services\InventoryTransactionManager;

class CarpetMaterialController extends Controller
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
    public function store(Request $request, \App\Services\InventoryTransactionManager $inventoryManager, \App\Services\AccountingService $accountingService)
    {
        $data = $this->valData();
        $carpet = Carpet::where('carpet_id', '=', $request->carpet_id)->first();
        $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();

        if (!$material_stock) {
            return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!');
        } else {
            if ($request->amount > $material_stock->quantity) {
                return redirect()->back()->with('error', ' مواد در گدام' . $material_stock->quantity . 'kg' . 'میباشد');
            } else {
                $material_stock->quantity = $material_stock->quantity - $request->amount;
                $material_stock->update();
            }

        }
        $carpet->total_price = $carpet->total_price + $request->total_price;
        $carpet->total_price_af = $carpet->total_price_af + $request->total_price_af;
        $carpet->update();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "برای قالین نمبر  " . $carpet->carpet_no . " به مقدار " . $request->amount . " کیلوگرام مواد دریافت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        // Remove accounting fields before creating material to avoid mass assignment errors
        $materialData = $data;
        $materialData['currency_code'] = \App\Currency::find($request->currency_id)->code ?? 'AFN';
        $materialData['exchange_rate'] = $request->exchange_rate;
        
        unset($materialData['debit_account_id'], $materialData['credit_account_id'], $materialData['currency_id'], $materialData['warehouse_id']);
        
        $done = $carpet->carpetMaterial()->create($materialData);
        if ($done) {
            
            // Post GL Transaction
            $currencyCode = \App\Currency::find($request->currency_id)->code ?? 'AFN';
            
            $accountingService->postTransaction([
                'date' => $request->date,
                'reference' => 'MT-' . $done->id,
                'description' => "مصرف مواد برای قالین نمبر " . $carpet->carpet_no,
                'source_type' => get_class($done),
                'source_id' => $done->id,
                'journal_type' => 'journal',
                'entries' => [
                    [
                        'account_id' => $request->debit_account_id,
                        'debit' => $request->total_price,
                        'credit' => 0,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $request->exchange_rate,
                    ],
                    [
                        'account_id' => $request->credit_account_id,
                        'debit' => 0,
                        'credit' => $request->total_price,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $request->exchange_rate,
                    ]
                ]
            ]);
            
            // Record Inventory OUT Movement 
            $inventoryManager->processGenericMovement([
                'item_model' => $done,
                'type' => 'PROD_ISSUE',
                'direction' => 'OUT',
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->amount,
                'unit_cost' => $request->price,
                'currency_code' => \App\Currency::find($request->currency_id)->code ?? 'AFN',
                'exchange_rate' => $request->exchange_rate,
                'created_by' => Auth::user()->id,
            ]);

            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ثبت شد !');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ثبت شد !');
            }
        } else {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetMaterial $carpetMaterial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetMaterial $material)
    {
        $carpet_id = $material->carpet_id;
        $carpet = Carpet::find($carpet_id);
        $carpetCheckBook = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price');
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();

        $tar_pakhta = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 1)->sum('amount');
        $tar_pashm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 2)->sum('amount');
        $tar_abrishm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 3)->sum('amount');

        $lastId = CarpetCheckBook::latest()->first();
        $CheckNo = '';
        if ($lastId) {
            $lastId = $lastId->check_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $CheckNo = 'CH-' . sprintf('%01d', $lastId);
        } else {
            $CheckNo = 'CH-' . sprintf('%01d', '1');
        }

        // Forensic Dependencies
        $afg_money = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price_af');
        $usd_money = $materialMoney;
        
        $accountService = new \App\Services\AccountSelectionService();
        $inventoryAccounts = $accountService->getValidAccounts('CARPET_INVENTORY', 'debit');
        $rawMaterialAccounts = $accountService->getValidAccounts('RAW_MATERIAL', 'credit');
        $expenseAccounts = $accountService->getValidAccounts('EXPENSE', 'debit');
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();

        $viewName = ($carpet->agent && $carpet->agent->contract_type == 'weight') ? 'carpets.weight-details' : 'carpets.carpet-contract-details';

        return view($viewName, compact('carpet', 'material', 'carpetCheckBook', 'carpetMaterials', 'categories', 'material_types', 'materialMoney', 'CheckNo', 'tar_pakhta', 'tar_pashm', 'tar_abrishm', 'afg_money', 'usd_money', 'inventoryAccounts', 'rawMaterialAccounts', 'expenseAccounts', 'currencies', 'warehouses'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetMaterial $material, AccountingService $accountingService, InventoryTransactionManager $inventoryManager)
    {
        try {
            return DB::transaction(function() use ($request, $material, $accountingService, $inventoryManager) {
                $data = $this->valData();
                
                // 1. Reverse old transactions
                $inventoryManager->reverseTransactions($material, 'ویرایش مصرف مواد قالین');

                $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();
                $carpet = Carpet::where('carpet_id', $request->carpet_id)->first();

                if (!$material_stock) {
                    throw new \Exception('مواد درخواست شده در گدام نمیباشد‌!');
                }

                // Fresh instance to get the restored quantity from reversal
                $material_stock = $material_stock->fresh();

                if ($request->amount > $material_stock->quantity) {
                    throw new \Exception(' مواد در گدام کافی نمیباشد. موجودی: ' . $material_stock->quantity . 'kg');
                }

                // Decrement the new amount
                $material_stock->quantity = $material_stock->quantity - $request->amount;
                $material_stock->update();

                // Update carpet prices
                $carpet->total_price = $carpet->total_price - $request->old_dollar + $request->total_price;
                $carpet->total_price_af = $carpet->total_price_af - $request->old_af + $request->total_price_af;
                $carpet->update();

                // Log activity
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = "برای قالین نمبر  " . $carpet->carpet_no . " به مقدار " . $request->amount . " کیلوگرام مواد ویرایش شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

                // Prepare and update model
                $updateData = $data;
                $currencyCode = \App\Currency::find($request->currency_id)->code ?? 'AFN';
                $updateData['currency_code'] = $currencyCode;
                $updateData['exchange_rate'] = $request->exchange_rate;
                
                unset($updateData['debit_account_id'], $updateData['credit_account_id'], $updateData['currency_id'], $updateData['warehouse_id']);

                $material->update($updateData);

                // 2. Post new GL Transaction
                $accountingService->postTransaction([
                    'date' => $request->date,
                    'reference' => 'MT-' . $material->id,
                    'description' => "مصرف مواد برای قالین نمبر " . $carpet->carpet_no,
                    'source_type' => get_class($material),
                    'source_id' => $material->id,
                    'journal_type' => 'journal',
                    'entries' => [
                        [
                            'account_id' => $request->debit_account_id,
                            'debit' => $request->total_price,
                            'credit' => 0,
                            'currency_code' => $currencyCode,
                            'exchange_rate' => $request->exchange_rate,
                        ],
                        [
                            'account_id' => $request->credit_account_id,
                            'debit' => 0,
                            'credit' => $request->total_price,
                            'currency_code' => $currencyCode,
                            'exchange_rate' => $request->exchange_rate,
                        ]
                    ]
                ]);

                // 3. Record new Inventory OUT Movement
                $inventoryManager->processGenericMovement([
                    'item_model' => $material,
                    'type' => 'PROD_ISSUE',
                    'direction' => 'OUT',
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => $request->amount,
                    'unit_cost' => $request->price,
                    'currency_code' => $currencyCode,
                    'exchange_rate' => $request->exchange_rate,
                    'created_by' => Auth::user()->id,
                ]);

                if ($carpet->agent->contract_type == 'weight') {
                    return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ویرایش شد !');
                } else {
                    return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ویرایش شد !');
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, \App\Services\InventoryTransactionManager $inventoryManager)
    {
        $material = \App\CarpetMaterial::findOrFail($id);
        $carpet = \App\Carpet::where('carpet_id', $material->carpet_id)->first();

        // Reverse Carpet Values
        $carpet->total_price = $carpet->total_price - $material->total_price;
        $carpet->total_price_af = $carpet->total_price_af - $material->total_price_af;
        $carpet->update();

        // Reverse Inventory and Ledger
        $inventoryManager->reverseTransactions($material, 'لغو صدور مواد توسط کاربر');

        // Log Activity
        $activity = new \App\Activity();
        $activity->date = \Carbon\Carbon::today()->format('Y-m-d');
        $activity->description = "مواد داده شده برای قالین نمبر  " . $carpet->carpet_no . " لغو و ریورس شد ";
        $activity->user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $activity->save();

        $material->delete();

        return redirect()->back()->with('status', 'عملیه صدور مواد موفقانه لغو و ریورس گردید!');
    }

    protected function valData()
    {
        return request()->validate([
            'amount' => 'required',
            'price' => 'required',
            'total_price' => 'required',
            'total_price_af' => 'required',
            'date' => 'required',
            'category_id' => 'required',
            'type_id' => 'nullable',
            'carpet_id' => 'nullable',
            'agent_id' => 'required',
            'warehouse_id' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'currency_id' => 'required',
            'exchange_rate' => 'required',
            'currency_code' => 'nullable',
        ]);
    }
}
