<?php

namespace App\Http\Controllers;

use App\Activity;
use App\OfficeDebit;
use App\PurchaseMaterial;
use App\MaterialType;
use App\MaterialCategory;
use App\StringSeller;
use App\MaterialStock;
use App\PurchaseTotalAcount;
use App\RecievedOfSeller;
use App\OfficeCashBook;
use App\Services\AccountingService;
use App\Services\InventoryTransactionManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseMaterialController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;

    public function __construct(AccountingService $accountingService, InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
    }

    private function postPurchaseToAccounting($purchase)
    {
        try {
            $this->accountingService->postAutoTransaction('material_purchase', 'credit', [
                'date' => $purchase->purchase_date,
                'amount' => $purchase->total_af,
                'party_type' => 'App\StringSeller',
                'party_id' => $purchase->seller_id,
                'reference' => $purchase->purchase_number,
                'description' => "خریداری مواد از " . StringSeller::find($purchase->seller_id)->name,
                'source_id' => $purchase->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Purchase #" . $purchase->id . ": " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchase = PurchaseMaterial::latest()->paginate(30);
        $material_type = MaterialType::all();
        $material_category = MaterialCategory::all();
        $sellers = StringSeller::all();
        $purchaseMaterial = '';
        $lastId = PurchaseMaterial::latest()->first();
        $PurchaseNo = '';
        if ($lastId) {
            $lastId = $lastId->purchase_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $PurchaseNo = 'PO-' . sprintf('%01d', $lastId);
        } else {
            $PurchaseNo = 'PO-' . sprintf('%01d', '1');
        }
        $warehouses = \App\Warehouse::all();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('MATERIAL_PURCHASE_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('MATERIAL_PURCHASE_CREDIT', 'credit');
        
        // Fetch current mapping as defaults
        $mapping = \App\MappingRule::where('mapping_key', 'MATERIAL_PURCHASE_CREDIT')->first();

        return view('mpurchase.index', compact(
            'purchase', 'material_type', 'material_category', 'sellers', 
            'purchaseMaterial', 'PurchaseNo', 'warehouses',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'
        ));
    }


    public function search_purchase_number($purchase_number, $seller_id)
    {
        $seller = StringSeller::findOrfail($seller_id);
        $purchases = PurchaseMaterial::where('purchase_number', $purchase_number)->where('seller_id', $seller_id)->paginate(30);
        $quantity = PurchaseMaterial::Where('seller_id', '=', $seller_id)->where('purchase_number', '=', $purchase_number)->count();
        return view('mpurchase.purchase-number-list', compact('purchases', 'seller', 'purchase_number', 'quantity'));
    }


    public function request_list()
    {
        $requests = PurchaseMaterial::where('status', 0)->orderBy('id', 'DESC')->paginate(30);
        return view('mpurchase.requested-list', compact('requests'));
    }

    public function approve_request($id)
    {
        $purchase = PurchaseMaterial::find($id);

        $this->inventoryManager->processPurchase($purchase, [
            'quantity' => $purchase->quantity,
            'unit_cost' => $purchase->price_per_kilo,
            'warehouse_id' => $purchase->warehouse_id ?? 1,
            'date' => $purchase->purchase_date,
            'total_amount' => $purchase->total_af,
            'party_type' => 'App\StringSeller',
            'party_id' => $purchase->seller_id,
            'reference' => $purchase->purchase_number,
            'description' => "خریداری مواد از " . StringSeller::find($purchase->seller_id)->name,
            'override_debit_account_id' => $purchase->override_debit_account_id,
            'override_credit_account_id' => $purchase->override_credit_account_id,
        ], function () use ($purchase) {
            // Legacy Sync
            $purchase->status = 1;
            $purchase->update();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " به مقدار " . $purchase->quantity . " کیلوگرام مواد توسط سوپر ادمین تایید و در سیستم مالی ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
        });

        return response()->json(['status' => 'success']);
    }

    public function delete_request($id)
    {
        $purchase = PurchaseMaterial::find($id);
        $purchase->delete();
        return response()->json(['status', 'error']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $material_type = MaterialType::all();
        $material_category = MaterialCategory::all();
        $sellers = StringSeller::all();
        $warehouses = \App\Warehouse::all();
        return view('mpurchase.create', compact('material_type', 'material_category', 'sellers', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->Valid();
        if (Auth::user()->role == 'SP') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        // For direct purchases (SP role), we use the manager's transactional callback
        if ($data['status'] == 1) {
            // Pre-create the instance to have a model reference
            $purchase = new PurchaseMaterial($data);
            
            $this->inventoryManager->processPurchase($purchase, [
                'quantity' => $purchase->quantity,
                'unit_cost' => $purchase->price_per_kilo,
                'warehouse_id' => $purchase->warehouse_id ?? 1,
                'date' => $purchase->purchase_date,
                'total_amount' => $purchase->total_af, // Use AFN for ledger
                'party_type' => 'App\StringSeller',
                'party_id' => $purchase->seller_id,
                'reference' => $purchase->purchase_number,
                'description' => "خریداری مواد از " . StringSeller::find($purchase->seller_id)->name,
                'override_debit_account_id' => $purchase->override_debit_account_id,
                'override_credit_account_id' => $purchase->override_credit_account_id,
            ], function () use ($purchase, $request) {
                // Legacy Sync: Actual Save
                $purchase->save();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " به مقدار " . $request->quantity . " کیلوگرام مواد ثبت شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();
            });
        } else {
            // For requests, just save legacy (no inventory/accounting yet)
            $purchase = PurchaseMaterial::create($data);
        }

        return redirect('/dashboard/material-purchase')->with('status', 'خریداری موفقانه صورت گرفت و در سیستم مالی ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseMaterial $purchaseMaterial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseMaterial $purchaseMaterial)
    {
        $purchase = PurchaseMaterial::latest()->paginate(30);
        $material_type = MaterialType::all();
        $material_category = MaterialCategory::all();
        $sellers = StringSeller::all();
        $warehouses = \App\Warehouse::all();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('MATERIAL_PURCHASE_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('MATERIAL_PURCHASE_CREDIT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'MATERIAL_PURCHASE_CREDIT')->first();

        return view('mpurchase.index', compact(
            'purchase', 'material_type', 'material_category', 'sellers', 'purchaseMaterial', 'warehouses',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseMaterial $purchaseMaterial)
    {
        return DB::transaction(function () use ($request, $purchaseMaterial) {
            // Reverse old transactions (Inventory + Accounting)
            if ($purchaseMaterial->status == 1) {
                $this->inventoryManager->reverseTransactions($purchaseMaterial, 'Purchase Record Edited');
            }

            $data = $this->Valid();
            $purchaseMaterial->update($data);

            // Re-process new transactions if approved
            if ($purchaseMaterial->status == 1) {
                $this->inventoryManager->processPurchase($purchaseMaterial, [
                    'quantity' => $purchaseMaterial->quantity,
                    'unit_cost' => $purchaseMaterial->price_per_kilo,
                    'warehouse_id' => $request->warehouse_id ?? 1,
                    'date' => $purchaseMaterial->purchase_date,
                    'total_amount' => $purchaseMaterial->total_af,
                    'party_type' => 'App\StringSeller',
                    'party_id' => $purchaseMaterial->seller_id,
                    'reference' => $purchaseMaterial->purchase_number,
                    'description' => "خریداری مواد از " . StringSeller::find($purchaseMaterial->seller_id)->name,
                    'override_debit_account_id' => $request->override_debit_account_id,
                    'override_credit_account_id' => $request->override_credit_account_id,
                ]);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " به مقدار " . $request->quantity . " کیلوگرام مواد ویرایش شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/material-purchase')->with('status', 'خریداری موفقانه ویرایش و سیستم مالی بروزرسانی شد.');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $purchase = PurchaseMaterial::find($id);

            if ($purchase->status == 1) {
                $this->inventoryManager->reverseTransactions($purchase, 'Purchase Record Deleted');
            }

            $purchase->delete();
            return response()->json(['status' => 'success']);
        });
    }

    protected function Valid()
    {
        return request()->validate([
            'material_type' => 'required',
            'material_category' => 'required',
            'seller_id' => 'required',
            'quantity' => 'required',
            'purchase_date' => 'required',
            'price_per_kilo' => 'required',
            'in_words' => 'required',
            'total' => 'required',
            'total_af' => 'required',
            'purchase_number' => 'required',
            'warehouse_id' => 'required',
            'override_debit_account_id' => '',
            'override_credit_account_id' => '',
            'status' => ''
        ]);
    }
}
