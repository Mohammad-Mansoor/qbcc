<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Agents;
use App\Customer;
use App\MaterialCategory;
use App\MaterialSale;
use App\MaterialStock;
use App\MaterialType;
use App\Currency;
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

        // Apply Spatie Permissions
        $this->middleware('permission:view_material_sales')->only(['index', 'get_sale_info', 'search_sale_number']);
        $this->middleware('permission:create_material_sale')->only(['create', 'store']);
        $this->middleware('permission:edit_material_sale')->only(['edit', 'update']);
        $this->middleware('permission:delete_material_sale')->only('destroy');
        $this->middleware('permission:view_material_sale_requests')->only('request_list');
        $this->middleware('permission:approve_material_sale_requests')->only('approve_request');
        $this->middleware('permission:reject_material_sale_requests')->only('delete_request');
    }

    public function get_sale_info(Request $request)
    {
        $agentId = $request->agent_id;
        $catId = $request->category_id;
        $typeId = $request->type_id;
        $whId = $request->warehouse_id ?? 1;

        \Log::info("Fetching sale info for Agent: $agentId, Category: $catId, Type: $typeId, WH: $whId");

        $info = [
            'wac' => 0,
            'balance' => 0,
            'available_stock' => 0
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
                }

                // Also check if we have a WAC item registered
                $item = DB::table('items')
                    ->where('type', 'App\MaterialType')
                    ->where('ref_id', $typeId)
                    ->first();

                if ($item) {
                    $info['wac'] = $item->current_cost;

                    // Available stock in this warehouse for this category and type
                    $stockQuery = DB::table('inventory_transactions')
                        ->where('item_id', $item->id)
                        ->where('warehouse_id', $whId)
                        ->where('status', 1);

                    if ($catId) {
                        $stockQuery->where('category_id', $catId);
                    }

                    $balance = $stockQuery->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
                        ->value('balance') ?? 0;

                    // If editing an existing sale, add back the quantity allocated to it to prevent lockout
                    if ($request->sale_id) {
                        $allocatedQty = DB::table('inventory_transactions')
                            ->where('reference_type', 'App\MaterialSale')
                            ->where('reference_id', $request->sale_id)
                            ->where('status', 1)
                            ->where('direction', 'OUT')
                            ->sum('quantity') ?? 0;

                        $balance += $allocatedQty;
                    }

                    $info['available_stock'] = $balance;
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
        $material_sales = MaterialSale::with(['category', 'type', 'agent', 'warehouse'])->orderBy('created_at', 'DESC')->paginate(60);
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $saleEdit = '';
        $agents = Agents::all();
        $lastId = MaterialSale::latest()->first();
        $SaleNo = '';
        if ($lastId) {
            $lastId = $lastId->sale_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $SaleNo = 'SA-' . sprintf('%01d', $lastId);
        } else {
            $SaleNo = 'SA-' . sprintf('%01d', '1');
        }

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'credit');

        // Add COGS accounts for overrides
        $allowedCogsDebit = $selectionService->getValidAccounts('SALES_COGS', 'debit');
        $allowedCogsCredit = $selectionService->getValidAccounts('SALES_COGS', 'credit');

        $mapping = \App\MappingRule::where('mapping_key', 'MATERIAL_REVENUE')->first();
        $warehouses = \App\Warehouse::all();
        $currencies = Currency::where('is_active', 1)->get();
        $baseCurrency = Currency::where('is_base_currency', 1)->first();

        $invoices = \App\Invoice::whereIn('type', ['dye', 'yarn'])
            ->where('status', 'open')
            ->orderBy('id', 'DESC')
            ->get();

        return view('mstock.material-sale', compact(
            'material_sales',
            'categories',
            'material_types',
            'saleEdit',
            'agents',
            'SaleNo',
            'allowedDebitAccounts',
            'allowedCreditAccounts',
            'allowedCogsDebit',
            'allowedCogsCredit',
            'mapping',
            'warehouses',
            'currencies',
            'baseCurrency',
            'invoices'
        ));
    }

    public function search_sale_number($sale_number, $agent_id)
    {
        $agent = Agents::findOrfail($agent_id);
        $sales = MaterialSale::where('sale_number', $sale_number)->where('agent_id', $agent_id)->get();
        $quantity = MaterialSale::Where('agent_id', '=', $agent_id)->where('sale_number', '=', $sale_number)->count();
        return view('mstock.sale-number-list', compact('agent', 'sales', 'sale_number', 'quantity'));
    }

    public function request_list()
    {
        $requests = MaterialSale::with(['agent.user', 'category', 'type', 'warehouse', 'debitAccount', 'creditAccount', 'cogsDebitAccount', 'cogsCreditAccount'])
            ->where('status', 0)
            ->orderBy('id', 'DESC')
            ->paginate(30);

        $revenueMapping = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'MATERIAL_REVENUE')->first();
        $cogsMapping = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'SALES_COGS')->first();

        foreach ($requests as $req) {
            // Get WAC from App\MaterialType item
            $wac = 0;
            $item = DB::table('items')
                ->where('type', 'App\MaterialType')
                ->where('ref_id', $req->type_id)
                ->first();
            if ($item) {
                $wac = $item->current_cost;
            } else {
                $stockRec = MaterialStock::where('material_category', $req->category_id)
                    ->where('material_type', $req->type_id)
                    ->first();
                if ($stockRec)
                    $wac = $stockRec->price_per_kilo;
            }
            $req->estimated_wac = $wac;

            // Get Stock (Warehouse-specific from inventory_transactions)
            $stockQuery = DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->where('items.type', 'App\MaterialType')
                ->where('items.ref_id', $req->type_id)
                ->where('inventory_transactions.warehouse_id', $req->warehouse_id ?? 1)
                ->where('inventory_transactions.status', 1);

            if ($req->category_id) {
                $stockQuery->where('inventory_transactions.category_id', $req->category_id);
            }

            $req->available_stock = $stockQuery->selectRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as balance")
                ->value('balance') ?? 0;
        }

        return view('mstock.material-sale-requested-list', compact('requests', 'revenueMapping', 'cogsMapping'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = MaterialSale::find($id);

            $this->inventoryManager->processSale($sale, [
                'quantity' => $sale->amount,
                'unit_cost' => 0, // WAC handled
                'currency_code' => $sale->currency_code,
                'exchange_rate' => $sale->exchange_rate,
                'warehouse_id' => $sale->warehouse_id ?? 1,
                'date' => $sale->date,
                'sale_amount' => $sale->original_amount,
                'party_type' => 'App\Agents',
                'party_id' => $sale->agent_id,
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

    public function delete_request($id)
    {
        $sale = MaterialSale::find($id);
        $sale->delete();
        return response()->json(['status' => 'success']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        foreach (['amount', 'price', 'total_price', 'total_price_af', 'exchange_rate', 'original_amount'] as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = str_replace(',', '', $inputs[$field]);
            }
        }
        $request->replace($inputs);

        return DB::transaction(function () use ($request) {
            $itemId = DB::table('items')
                ->where('type', 'App\MaterialType')
                ->where('ref_id', $request->type_id)
                ->value('id');

            $availableStock = 0;
            if ($itemId) {
                $stockQuery = DB::table('inventory_transactions')
                    ->where('item_id', $itemId)
                    ->where('warehouse_id', $request->warehouse_id ?? 1)
                    ->where('status', 1);

                if ($request->category_id) {
                    $stockQuery->where('category_id', $request->category_id);
                }

                $availableStock = $stockQuery->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;
            }

            if ($availableStock <= 0) {
                return redirect()->back()->with('error', 'مواد مورد نظر در گدام انتخاب شده موجود نمی‌باشد!');
            } elseif ($request->amount > $availableStock) {
                return redirect()->back()->with('error', 'مقدار فروش بیشتر از موجودی گدام است! موجودی فعلی: ' . number_format($availableStock, 2) . ' کیلوگرام');
            }

            $data = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'agent_id' => 'required',
                'amount' => 'required',
                'price' => 'required',
                'total_price' => 'required',
                'total_price_af' => 'required',
                'date' => 'required',
                'category_id' => 'required',
                'type_id' => 'required',
                'warehouse_id' => 'nullable',
                'override_debit_account_id' => 'nullable',
                'override_credit_account_id' => 'nullable',
                'override_cogs_debit_id' => 'nullable',
                'override_cogs_credit_id' => 'nullable',
                'currency_id' => 'required',
                'exchange_rate' => 'required',
                'original_amount' => 'required'
            ]);

            $invoice = \App\Invoice::findOrFail($request->invoice_id);
            if ($invoice->status === 'closed') {
                return redirect()->back()->with('error', 'انوایس مورد نظر بسته شده است و امکان ثبت فروش جدید در آن وجود ندارد!');
            }
            $data['sale_number'] = $invoice->invoice_no;

            $currency = Currency::findOrFail($request->currency_id);
            $data['currency_code'] = $currency->code;

            // Normalized USD Base Calculation
            $data['base_currency_amount'] = bcmul($request->original_amount, $request->exchange_rate, 4);

            $data['status'] = (Auth::user()->isSuperAdmin()) ? 1 : 0;

            $sale = new MaterialSale($data);

            if ($sale->status == 1) {
                $this->inventoryManager->processSale($sale, [
                    'quantity' => $sale->amount,
                    'unit_cost' => 0, // WAC handled
                    'currency_code' => $sale->currency_code,
                    'exchange_rate' => $sale->exchange_rate,
                    'warehouse_id' => $sale->warehouse_id ?? 1,
                    'date' => $sale->date,
                    'sale_amount' => $sale->original_amount,
                    'party_type' => 'App\Agents',
                    'party_id' => $sale->agent_id,
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

        if ($saleEdit && $saleEdit->invoice && $saleEdit->invoice->status === 'closed') {
            return redirect('/dashboard/material-sales')->with('error', 'این فروش در یک انوایس بسته شده قرار دارد و امکان ویرایش آن وجود ندارد!');
        }

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('MATERIAL_REVENUE', 'credit');
        $allowedCogsDebit = $selectionService->getValidAccounts('SALES_COGS', 'debit');
        $allowedCogsCredit = $selectionService->getValidAccounts('SALES_COGS', 'credit');

        $mapping = \App\MappingRule::where('mapping_key', 'MATERIAL_REVENUE')->first();
        $warehouses = \App\Warehouse::all();
        $currencies = Currency::where('is_active', 1)->get();
        $baseCurrency = Currency::where('is_base_currency', 1)->first();

        $invoices = \App\Invoice::whereIn('type', ['dye', 'yarn'])
            ->where(function ($q) use ($saleEdit) {
                $q->where('status', 'open');
                if ($saleEdit && $saleEdit->invoice_id) {
                    $q->orWhere('id', $saleEdit->invoice_id);
                }
            })
            ->orderBy('id', 'DESC')
            ->get();

        return view('mstock.material-sale', compact(
            'material_sales',
            'agents',
            'categories',
            'material_types',
            'saleEdit',
            'allowedDebitAccounts',
            'allowedCreditAccounts',
            'allowedCogsDebit',
            'allowedCogsCredit',
            'mapping',
            'warehouses',
            'currencies',
            'baseCurrency',
            'invoices'
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
        $inputs = $request->all();
        foreach (['amount', 'price', 'total_price', 'total_price_af', 'exchange_rate', 'original_amount'] as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = str_replace(',', '', $inputs[$field]);
            }
        }
        $request->replace($inputs);

        return DB::transaction(function () use ($request, $id) {
            $materialSale = MaterialSale::find($id);

            if ($materialSale->invoice && $materialSale->invoice->status === 'closed') {
                return redirect()->back()->with('error', 'این فروش در یک انوایس بسته شده قرار دارد و امکان ویرایش آن وجود ندارد!');
            }

            $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'agent_id' => 'required',
                'amount' => 'required',
                'price' => 'required',
                'total_price' => 'required',
                'total_price_af' => 'required',
                'date' => 'required',
                'category_id' => 'required',
                'type_id' => 'required',
            ]);

            $invoice = \App\Invoice::findOrFail($request->invoice_id);
            if ($invoice->status === 'closed') {
                return redirect()->back()->with('error', 'انوایس مقصد بسته شده است!');
            }

            // Reverse old transactions (Inventory + Accounting)
            if ($materialSale->status == 1) {
                $this->inventoryManager->reverseTransactions($materialSale, 'Material Sale Edited');
            }

            $itemId = DB::table('items')
                ->where('type', 'App\MaterialType')
                ->where('ref_id', $request->type_id)
                ->value('id');

            $availableStock = 0;
            if ($itemId) {
                $stockQuery = DB::table('inventory_transactions')
                    ->where('item_id', $itemId)
                    ->where('warehouse_id', $request->warehouse_id ?? 1)
                    ->where('status', 1);

                if ($request->category_id) {
                    $stockQuery->where('category_id', $request->category_id);
                }

                $availableStock = $stockQuery->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;
            }

            if ($availableStock <= 0) {
                return redirect()->back()->with('error', 'مواد مورد نظر در گدام انتخاب شده موجود نمی‌باشد!');
            } elseif ($request->amount > $availableStock) {
                return redirect()->back()->with('error', 'مقدار فروش بیشتر از موجودی گدام است! موجودی فعلی: ' . number_format($availableStock, 2) . ' کیلوگرام');
            }

            $materialSale->invoice_id = $request->invoice_id;
            $materialSale->sale_number = $invoice->invoice_no;
            $materialSale->agent_id = $request->agent_id;
            $materialSale->amount = $request->amount;
            $materialSale->price = $request->price;
            $materialSale->total_price = $request->total_price;
            $materialSale->total_price_af = $request->total_price_af;
            $materialSale->date = $request->date;
            $materialSale->category_id = $request->category_id;
            $materialSale->type_id = $request->type_id;

            // Forensic Updates
            $materialSale->currency_id = $request->currency_id;
            $materialSale->exchange_rate = $request->exchange_rate;
            $materialSale->original_amount = $request->original_amount;

            $currency = Currency::findOrFail($request->currency_id);
            $materialSale->currency_code = $currency->code;
            $materialSale->base_currency_amount = bcmul($request->original_amount, $request->exchange_rate, 4);

            $materialSale->update();

            // Re-process if approved
            if ($materialSale->status == 1) {
                $this->inventoryManager->processSale($materialSale, [
                    'quantity' => $materialSale->amount,
                    'unit_cost' => 0, // WAC handled
                    'currency_code' => $materialSale->currency_code,
                    'exchange_rate' => $materialSale->exchange_rate,
                    'warehouse_id' => $request->warehouse_id ?? 1,
                    'date' => $materialSale->date,
                    'sale_amount' => $materialSale->original_amount,
                    'party_type' => 'App\Agents',
                    'party_id' => $materialSale->agent_id,
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
