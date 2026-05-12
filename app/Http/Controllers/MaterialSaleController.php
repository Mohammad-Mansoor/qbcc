<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Agents;
use App\Customer;
use App\MaterialCategory;
use App\MaterialSale;
use App\MaterialStock;
use App\MaterialType;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialSaleController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;

    public function __construct(AccountingService $accountingService, \App\Services\InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
    }

    public function get_sale_info(Request $request)
    {
        $agentId = $request->agent_id;
        $catId = $request->category_id;
        $typeId = $request->type_id;

        \Log::info("Fetching sale info for Agent: $agentId, Category: $catId, Type: $typeId");

        $info = [
            'wac' => 0,
            'balance' => 0
        ];

        try {
            if ($agentId) {
                $info['balance'] = $this->accountingService->getAccountBalance('App\Agents', $agentId);
            }

            if ($catId && $typeId) {
                // Find the latest purchase to get a reference cost, or check MaterialStock
                $stock = MaterialStock::where('material_category', $catId)
                    ->where('material_type', $typeId)
                    ->first();
                
                if ($stock) {
                    $info['wac'] = $stock->price_per_kilo;
                    
                    // Also check if we have a WAC item registered
                    $item = DB::table('items')
                        ->where('type', 'App\PurchaseMaterial')
                        ->join('purchase_materials', 'items.ref_id', '=', 'purchase_materials.id')
                        ->where('purchase_materials.material_category', $catId)
                        ->where('purchase_materials.material_type', $typeId)
                        ->orderBy('purchase_materials.id', 'DESC')
                        ->select('items.current_cost')
                        ->first();
                    
                    if ($item) {
                        $info['wac'] = $item->current_cost;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error in get_sale_info: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($info);
    }

    private function postMaterialSaleToAccounting($sale, $overrides = [])
    {
        try {
            // Material sale is also a sale, but we might want a different category
            // For now, let's use 'material_sale' type
            $this->accountingService->postAutoTransaction('material_sale', 'credit', array_merge([
                'date' => $sale->date,
                'amount' => $sale->total_price_af,
                'party_type' => 'App\Agents',
                'party_id' => $sale->agent_id,
                'reference' => $sale->sale_number,
                'description' => "فروش مواد به نماینده " . Agents::find($sale->agent_id)->name,
                'source_id' => $sale->id,
            ], $overrides));
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Material Sale #" . $sale->id . ": " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $material_sales = MaterialSale::orderBy('created_at','DESC')->paginate(60);
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $saleEdit = '';
        $agents = Agents::all();
        $lastId = MaterialSale::latest()->first();
        $SaleNo = '';
        if($lastId) {
            $lastId = $lastId->sale_number;
            $lastId = substr($lastId,-1);
            $lastId++;
            $SaleNo = 'SA-'.sprintf('%01d' , $lastId);
        } else {
            $SaleNo = 'SA-'.sprintf('%01d'  , '1');
        }

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'credit');
        
        // Add COGS accounts for overrides
        $allowedCogsDebit = $selectionService->getValidAccounts('SALES_COGS', 'debit');
        $allowedCogsCredit = $selectionService->getValidAccounts('SALES_COGS', 'credit');

        $mapping = \App\MappingRule::where('mapping_key', 'MATERIAL_REVENUE')->first();
        $warehouses = \App\Warehouse::all();

        return view('mstock.material-sale', compact(
            'material_sales', 'categories', 'material_types', 'saleEdit', 'agents','SaleNo', 
            'allowedDebitAccounts', 'allowedCreditAccounts', 'allowedCogsDebit', 'allowedCogsCredit',
            'mapping', 'warehouses'
        ));
    }

    public function search_sale_number($sale_number,$agent_id){
        $agent = Agents::findOrfail($agent_id);
        $sales = MaterialSale::where('sale_number',$sale_number)->where('agent_id',$agent_id)->get();
        $quantity = MaterialSale::Where('agent_id', '=', $agent_id)->where('sale_number','=',$sale_number)->count();
        return view('mstock.sale-number-list', compact('agent','sales','sale_number','quantity'));
    }

    public function request_list()
    {
        $requests = MaterialSale::with(['agent.user', 'category', 'type', 'warehouse', 'debitAccount', 'creditAccount', 'cogsDebitAccount', 'cogsCreditAccount'])
            ->where('status', 0)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($requests as $req) {
            // Get WAC
            $wac = 0;
            $stockRec = MaterialStock::where('material_category', $req->category_id)
                ->where('material_type', $req->type_id)
                ->first();
            
            if ($stockRec) {
                $wac = $stockRec->price_per_kilo;
                $item = DB::table('items')
                    ->where('type', 'App\PurchaseMaterial')
                    ->join('purchase_materials', 'items.ref_id', '=', 'purchase_materials.id')
                    ->where('purchase_materials.material_category', $req->category_id)
                    ->where('purchase_materials.material_type', $req->type_id)
                    ->orderBy('purchase_materials.id', 'DESC')
                    ->select('items.current_cost')
                    ->first();
                if ($item) $wac = $item->current_cost;
            }
            $req->estimated_wac = $wac;

            // Get Stock (Warehouse-specific from inventory_transactions)
            $req->available_stock = DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->join('purchase_materials', 'items.ref_id', '=', 'purchase_materials.id')
                ->where('items.type', 'App\PurchaseMaterial')
                ->where('purchase_materials.material_category', $req->category_id)
                ->where('purchase_materials.material_type', $req->type_id)
                ->where('inventory_transactions.warehouse_id', $req->warehouse_id ?? 1)
                ->where('inventory_transactions.status', 1)
                ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as balance")
                ->value('balance') ?? 0;
        }

        return view('mstock.material-sale-requested-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = MaterialSale::find($id);

            $this->inventoryManager->processSale($sale, [
                'quantity' => $sale->amount,
                'unit_cost' => 0, // WAC handled
                'warehouse_id' => $sale->warehouse_id ?? 1,
                'date' => $sale->date,
                'sale_amount' => $sale->total_price_af,
                'customer_id' => $sale->agent_id,
                'reference' => $sale->sale_number,
                'description' => "فروش مواد به نماینده " . Agents::find($sale->agent_id)->name,
                'override_debit_account_id' => $sale->override_debit_account_id,
                'override_credit_account_id' => $sale->override_credit_account_id,
                'override_cogs_debit_id' => $sale->override_cogs_debit_id,
                'override_cogs_credit_id' => $sale->override_cogs_credit_id,
            ], function () use ($sale) {
                $sale->status = 1;
                $sale->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " به مقدار " . $sale->amount . "کیلوگرام مواد تایید و در سیستم مالی ثبت شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();
            });

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id){
        $sale = MaterialSale::find($id);
        $sale->delete();
        return response()->json(['status','error']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();

            if (!$material_stock) {
                return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!');
            } elseif ($request->amount > $material_stock->quantity) {
                return redirect()->back()->with('error', ' مواد در گدام ' . $material_stock->quantity . 'kg' . ' میباشد');
            }

            $data = $request->validate([
                'agent_id' => 'required',
                'amount' => 'required',
                'price' => 'required',
                'total_price' => 'required',
                'total_price_af' => 'required',
                'type_id' => 'required',
                'category_id' => 'required',
                'date' => 'required',
                'sale_number' => 'required',
                'warehouse_id' => '',
                'override_debit_account_id' => '',
                'override_credit_account_id' => '',
                'override_cogs_debit_id' => '',
                'override_cogs_credit_id' => '',
                'status' => ''
            ]);

            $data['status'] = (Auth::user()->role == 'SP') ? 1 : 0;

            $sale = new MaterialSale($data);

            if ($sale->status == 1) {
                $this->inventoryManager->processSale($sale, [
                    'quantity' => $sale->amount,
                    'unit_cost' => 0, // WAC handled
                    'warehouse_id' => $sale->warehouse_id ?? 1,
                    'date' => $sale->date,
                    'sale_amount' => $sale->total_price_af,
                    'customer_id' => $sale->agent_id,
                    'reference' => $sale->sale_number,
                    'description' => "فروش مواد به نماینده " . Agents::find($sale->agent_id)->name,
                    'override_debit_account_id' => $sale->override_debit_account_id,
                    'override_credit_account_id' => $sale->override_credit_account_id,
                    'override_cogs_debit_id' => $sale->override_cogs_debit_id,
                    'override_cogs_credit_id' => $sale->override_cogs_credit_id,
                ], function () use ($sale) {
                    $sale->save();
                });
            } else {
                $sale->save();
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " به مقدار " . $request->amount . " مواد فروخته شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/material-sales')->with('status', 'فروش مواد موفقانه ثبت و در سیستم مالی درج گردید!');
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material_sales = MaterialSale::paginate(30);
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $saleEdit = MaterialSale::find($id);
        $agents = Agents::all();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'credit');
        $allowedCogsDebit = $selectionService->getValidAccounts('SALES_COGS', 'debit');
        $allowedCogsCredit = $selectionService->getValidAccounts('SALES_COGS', 'credit');
        
        $mapping = \App\MappingRule::where('mapping_key', 'MATERIAL_REVENUE')->first();
        $warehouses = \App\Warehouse::all();

        return view('mstock.material-sale', compact(
            'material_sales', 'agents', 'categories', 'material_types', 'saleEdit',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'allowedCogsDebit', 'allowedCogsCredit',
            'mapping', 'warehouses'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $materialSale = MaterialSale::find($id);

            // Reverse old transactions (Inventory + Accounting)
            if ($materialSale->status == 1) {
                $this->inventoryManager->reverseTransactions($materialSale, 'Material Sale Edited');
            }

            if (Auth::user()->role == 'SP') {
                $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();
                if (!$material_stock || $request->amount > $material_stock->quantity) {
                    return redirect()->back()->with('error', 'موجودی گدام کافی نیست!');
                }
            }

            $materialSale->agent_id = $request->agent_id;
            $materialSale->sale_number = $request->sale_number;
            $materialSale->amount = $request->amount;
            $materialSale->price = $request->price;
            $materialSale->total_price = $request->total_price;
            $materialSale->total_price_af = $request->total_price_af;
            $materialSale->date = $request->date;
            $materialSale->category_id = $request->category_id;
            $materialSale->type_id = $request->type_id;
            $materialSale->update();

            // Re-process if approved
            if ($materialSale->status == 1) {
                $this->inventoryManager->processSale($materialSale, [
                    'quantity' => $materialSale->amount,
                    'unit_cost' => 0, // WAC handled
                    'warehouse_id' => $request->warehouse_id ?? 1,
                    'date' => $materialSale->date,
                    'sale_amount' => $materialSale->total_price_af,
                    'customer_id' => $materialSale->agent_id,
                    'reference' => $materialSale->sale_number,
                    'description' => "فروش مواد به نماینده " . Agents::find($materialSale->agent_id)->name,
                    'override_debit_account_id' => $request->override_debit_account_id,
                    'override_credit_account_id' => $request->override_credit_account_id,
                    'override_cogs_debit_id' => $request->override_cogs_debit_id,
                    'override_cogs_credit_id' => $request->override_cogs_credit_id,
                ]);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش فروش مواد به مقدار " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/material-sales')->with('status', 'ویرایش موفقانه ثبت و حسابات مالی بروزرسانی شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = MaterialSale::find($id);

            if ($sale->status == 1) {
                $this->inventoryManager->reverseTransactions($sale, 'Material Sale Deleted');
            }

            $sale->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
