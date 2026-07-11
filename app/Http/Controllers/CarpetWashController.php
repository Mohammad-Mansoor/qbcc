<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetWash;
use App\Agents;
use App\Carpet;
use App\WashingTeam;
use App\Services\AccountingService;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CarpetWashController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;

    public function __construct(AccountingService $accountingService, \App\Services\InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
        
        $this->middleware('permission:view_carpet_washes')->only(['index', 'wash_numbers', 'search_wash_number_for_wash', 'search_carpet_type_from_wash_number', 'search_wash_numbersh_for_wash', 'search_wash_numbersh_payment', 'search', 'search_carpet_type', 'show']);
        $this->middleware('permission:create_carpet_wash')->only(['create_carpet_wash', 'store']);
        $this->middleware('permission:edit_carpet_wash')->only(['edit', 'update']);
        $this->middleware('permission:return_carpet_from_wash')->only(['return_to_center', 'return_to_kachaee']);
        $this->middleware('permission:send_carpet_to_finishing')->only(['sent_to_finishing_center']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function return_to_center($wash_id){
        return DB::transaction(function () use ($wash_id) {
            $carpet_wash = CarpetWash::find($wash_id);
            $carpet = Carpet::where('carpet_id',$carpet_wash->carpetId)->first();
            
            // Get original source warehouse from the latest active Washing Transfer OUT transaction for this carpet
            $originalTransaction = DB::table('inventory_transactions')
                ->where('reference_type', get_class($carpet))
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Washing Transfer')
                ->where('direction', 'OUT')
                ->where('status', 1)
                ->orderByDesc('id')
                ->first();
            $originalWarehouseId = $originalTransaction ? $originalTransaction->warehouse_id : 1;

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " از شست به دفتر مرکزی بازگشت داده شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            $carpet->status = 1;
            $carpet->washing_id = null;
            $carpet->warehouse_id = $originalWarehouseId;
            $carpet->update();

            // Reverse both accounting entries and the washing cost value-addition using the manager
            $this->inventoryManager->reverseTransactions($carpet_wash, 'Return to Center');

            // Safe, targeted reversal of original Washing Transfer transactions for this carpet
            $transactions = DB::table('inventory_transactions')
                ->where('reference_type', get_class($carpet))
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Washing Transfer')
                ->where('status', 1)
                ->get();

            foreach ($transactions as $tx) {
                // Insert a reversing entry
                DB::table('inventory_transactions')->insert([
                    'item_id' => $tx->item_id,
                    'warehouse_id' => $tx->warehouse_id,
                    'category_id' => $tx->category_id,
                    'type' => 'REVERSAL',
                    'direction' => ($tx->direction === 'IN' ? 'OUT' : 'IN'),
                    'quantity' => $tx->quantity,
                    'area' => $tx->area,
                    'unit_cost' => $tx->unit_cost,
                    'total_cost' => $tx->total_cost,
                    'is_value_adjustment' => $tx->is_value_adjustment,
                    'reference_type' => get_class($carpet),
                    'reference_id' => $carpet->carpet_id,
                    'status' => 0,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Mark original transaction as inactive (reversed)
                DB::table('inventory_transactions')->where('id', $tx->id)->update(['status' => 0]);
            }

            $carpet_wash->delete();

            return redirect('/dashboard/carpet-wash')->with('status','موفقانه بازگشت شد !');
        });
    }

    public function return_to_kachaee(Request $request, $wash_id){
        return DB::transaction(function () use ($request, $wash_id) {
            $carpet_wash = CarpetWash::find($wash_id);
            $carpet = Carpet::where('carpet_id',$carpet_wash->carpetId)->first();

            $sourceWarehouseId = $carpet->warehouse_id;

            // Update warehouse if provided via the modal's warehouse selector
            $warehouseId = $request->input('warehouse_id');
            $warehouseName = null;
            if ($warehouseId) {
                $carpet->warehouse_id = $warehouseId;
                $wh = \App\Warehouse::find($warehouseId);
                $warehouseName = $wh ? $wh->name : null;
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " از شست به کچایی بازگشت داده شد" .
                ($warehouseName ? " (انبار: " . $warehouseName . ")" : "") . " ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $carpet->status = 12;
            $carpet->washing_id = null;
            $carpet->update();

            // Reverse both accounting entries and the washing cost value-addition using the manager
            $this->inventoryManager->reverseTransactions($carpet_wash, 'Return to Kachaee');

            $carpet_wash->delete();

            // Record physical inventory movement (transfer) if the warehouse actually changed
            if ($warehouseId && $warehouseId != $sourceWarehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $inventoryService = app(\App\Services\InventoryService::class);

                $inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'type' => 'Warehouse Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $sourceWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'type' => 'Warehouse Transfer',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'warehouse_id' => $warehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);
            }

            return redirect('/dashboard/carpet-wash')->with('status','موفقانه بازگشت شد !');
        });
    }

    public function index()
    {
        $agents = Agents::all();
        $washeds = CarpetWash::orderBy('created_at', 'DESC')->paginate(60);
        $team = WashingTeam::all();
        $wash_check = 1;

        $kachaee_teams = \App\Kachaee::all()->map(function ($t) {
            $wip_carpets = \App\Carpet::where('status', 2)->where('kachaee_id', $t->id)->get();
            $t->wip_area = $wip_carpets->sum('area');
            $t->wip_count = $wip_carpets->count();
            return $t;
        });

        $finishing_teams = \App\FinishingTeam::all()->map(function ($t) {
            $wip_carpets = \App\Carpet::where('status', 4)->where('finishing_id', $t->id)->get();
            $t->wip_area = $wip_carpets->sum('area');
            $t->wip_count = $wip_carpets->count();
            return $t;
        });

        $carpet_warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        return view('carpet-wash.index', compact('agents', 'washeds', 'team', 'wash_check', 'kachaee_teams', 'finishing_teams', 'carpet_warehouses'));
    }

    public function wash_numbers($team_id)
    {
        $team = WashingTeam::find($team_id);
        $wash = CarpetWash::where('team_id', $team_id)->first();
        if (!$wash) return redirect()->back()->with('error', 'هیچ رکوردی یافت نشد');
        
        $wash_number = $wash->wash_number;
        $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number', '=', $wash->wash_number)->get();
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number']);
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number', $wash_number)->first();
        $list_for_wash = '';
        $wash_check = 'all';
        return view('carpet-wash.list-from-wash-number', compact('carpet_washes', 'team', 'wash_number', 'list_for_wash', 'wash_numbers', 'wash_date','wash_check'));
    }

    public function search_wash_number_for_wash(Request $request)
    {
        $wash_check = $request->wash_nonwash;
        $team_id = $request->team_id;
        $wash_number = $request->wash_number;
        $team = WashingTeam::find($team_id);
        $search = $request->search;
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number', $wash_number)->first();
        
        if ($search) {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })->orderBy('carpetId', 'ASC')->get();
        } else {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number', '=', $wash_number)->orderBy('carpetId', 'ASC')->get();
        }
        
        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number']);
        return view('carpet-wash.list-from-wash-number', compact('carpet_washes', 'team', 'wash_number','list_for_wash', 'wash_numbers', 'wash_date','wash_check'));
    }

    public function search_carpet_type_from_wash_number(Request $request)
    {
        $team_id = $request->team_id;
        $wash_number = $request->wash_number;
        $team = WashingTeam::find($team_id);
        $search = $request->carpet_type_id;
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number', $wash_number)->first();

        $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)
            ->WhereHas('carpet', function ($query) use ($search) {
                $query->where('type_id', $search);
            })->orderBy('carpetId', 'ASC')->get();

        $list_for_wash = '';
        $wash_check = 'all';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number']);
        return view('carpet-wash.list-from-wash-number', compact('carpet_washes', 'team', 'wash_number', 'list_for_wash', 'wash_numbers', 'wash_date', 'wash_check'));
    }

    public function search_wash_numbersh_for_wash(Request $request)
    {
        $team_id = $request->team_id;
        $wash_number_sh = $request->wash_number_sh;
        $team = WashingTeam::find($team_id);
        $search = $request->search;
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number_sh', $wash_number_sh)->first();

        if ($search) {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })->orderBy('carpetId', 'ASC')->get();
        } else {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number_sh', '=', $wash_number_sh)->orderBy('carpetId', 'ASC')->get();
        }
        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number_sh']);
        return view('carpet-wash.list-from-wash-number-sh', compact('carpet_washes', 'team', 'wash_number_sh','list_for_wash', 'wash_numbers', 'wash_date'));
    }

    public function search_wash_numbersh_payment($wash_number_sh, $team_id)
    {
        $team = WashingTeam::find($team_id);
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number_sh', $wash_number_sh)->first();
        $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number_sh', '=', $wash_number_sh)->orderBy('carpetId', 'ASC')->get();
        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number_sh']);
        return view('carpet-wash.list-from-wash-number-sh', compact('carpet_washes', 'team', 'wash_number_sh', 'list_for_wash', 'wash_numbers', 'wash_date'));
    }

    public function search(Request $request)
    {
        $team_id = $request->team_id;
        $search = $request->search;
        $wash_check = 0;

        $kachaee_teams = \App\Kachaee::all()->map(function ($t) {
            $wip_carpets = \App\Carpet::where('status', 2)->where('kachaee_id', $t->id)->get();
            $t->wip_area = $wip_carpets->sum('area');
            $t->wip_count = $wip_carpets->count();
            return $t;
        });

        $finishing_teams = \App\FinishingTeam::all()->map(function ($t) {
            $wip_carpets = \App\Carpet::where('status', 4)->where('finishing_id', $t->id)->get();
            $t->wip_area = $wip_carpets->sum('area');
            $t->wip_count = $wip_carpets->count();
            return $t;
        });

        $carpet_warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        if ($request->has('from_non_washed')) {
            $team = WashingTeam::where('id', $team_id)->get();
            $wash_check = 1;
            $washeds = CarpetWash::orderBy('date', 'DESC')->paginate(60);
            return view('carpet-wash.index', compact('washeds', 'team', 'wash_check', 'kachaee_teams', 'finishing_teams', 'carpet_warehouses'));
        } else {
            $washeds = CarpetWash::where('wash_number','like','%'.$search.'%')
                ->orWhere('wash_number_sh','like','%'.$search.'%')
                ->orWhere('date','like','%'.$search.'%')
                ->orWhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%'.$search.'%');
                })->orWhereHas('washing_team', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })->get();

            $wash_check = 2;
            $team = WashingTeam::all();
            return view('carpet-wash.index', compact('washeds', 'team', 'team_id', 'wash_check', 'kachaee_teams', 'finishing_teams', 'carpet_warehouses'));
        }
    }
    
    public function search_carpet_type(Request $request)
    {
        $search = $request->carpet_type_id;
        $washeds = CarpetWash::WhereHas('carpet', function ($query) use ($search) {
            $query->where('type_id',$search);
        })->get();

        $wash_check = 2;
        $team = WashingTeam::all();

        $kachaee_teams = \App\Kachaee::all()->map(function ($t) {
            $wip_carpets = \App\Carpet::where('status', 2)->where('kachaee_id', $t->id)->get();
            $t->wip_area = $wip_carpets->sum('area');
            $t->wip_count = $wip_carpets->count();
            return $t;
        });

        $finishing_teams = \App\FinishingTeam::all()->map(function ($t) {
            $wip_carpets = \App\Carpet::where('status', 4)->where('finishing_id', $t->id)->get();
            $t->wip_area = $wip_carpets->sum('area');
            $t->wip_count = $wip_carpets->count();
            return $t;
        });

        $carpet_warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        return view('carpet-wash.index', compact('washeds', 'team', 'wash_check', 'kachaee_teams', 'finishing_teams', 'carpet_warehouses'));
    }

    public function create_carpet_wash($id)
    {
        $carpet_wash = CarpetWash::find($id);
        
        $openBatches = \App\ProductionBatch::where('type', 'wash')
            ->where('status', 'open')
            ->where('team_id', $carpet_wash->team_id)
            ->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('WASHING_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('WASHING_CREDIT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'WASHING_CREDIT')->first();
        $defaultAccount = $mapping ? $mapping->debit_account_id : null;
        
        $currencies = \App\Currency::where('is_active', true)->get();
        $currency = \App\Currency::getLegacyAFNRate();

        $warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        return view('carpet-wash.create', compact('carpet_wash', 'openBatches', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'defaultAccount', 'currency', 'currencies', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'account_id' => 'required|integer',
            'override_credit_account_id' => 'required|integer',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
        ]);

        return DB::transaction(function () use ($request) {
            $carpet_wash = CarpetWash::where('id', $request->wash_id)->lockForUpdate()->firstOrFail();
            $carpet = Carpet::where('carpet_id', $request->carpetId)->lockForUpdate()->firstOrFail();
            $team_id = $carpet_wash->team_id;

            // Idempotency State Guard
            if ($carpet->status == 13 || $carpet_wash->total_price > 0) {
                return redirect('/dashboard/carpet-wash/wash-numbers/' . $team_id)
                    ->with('status', 'این شست قبلاً ثبت شده است (Already processed)');
            }

            // Convert amount to Base Currency (USD) for the ledger
            $baseAmount = ($request->currency_code === 'USD') 
                ? $request->total_price 
                : $request->total_price; // Total price comes from JS as USD base

            // Resolve the initial Washing WIP warehouse
            $wipWarehouseId = DB::table('inventory_transactions')
                ->where('reference_type', 'App\Carpet')
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Washing Transfer')
                ->where('direction', 'IN')
                ->where('status', 1)
                ->value('warehouse_id') ?? $carpet->warehouse_id ?? 1;

            $warehouseId = $request->input('warehouse_id');

            if ($warehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $inventoryService = app(\App\Services\InventoryService::class);

                // Resolve original area from WIP entry
                $wipArea = DB::table('inventory_transactions')
                    ->where('reference_type', 'App\Carpet')
                    ->where('reference_id', $carpet->carpet_id)
                    ->where('type', 'Washing Transfer')
                    ->where('direction', 'IN')
                    ->where('status', 1)
                    ->value('area') ?? $carpet->area ?? 0;

                $newArea = $request->area ?? $carpet->area ?? 0;

                // Record Transfer OUT from WIP washing warehouse
                $inventoryService->recordMovement([
                    'item_model' => $carpet_wash,
                    'parent_item_model' => $carpet,
                    'type' => 'Washing Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $wipWarehouseId,
                    'area' => (float) $wipArea,
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                // Record Transfer IN to selected target warehouse
                $inventoryService->recordMovement([
                    'item_model' => $carpet_wash,
                    'parent_item_model' => $carpet,
                    'type' => 'Washing Transfer',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'warehouse_id' => $warehouseId,
                    'area' => (float) $newArea,
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $carpet->warehouse_id = $warehouseId;
                $carpet->update();
            }

            $result = $this->inventoryManager->recordProductionService($carpet_wash, $carpet, [
                'type' => 'WASHING',
                'amount' => $baseAmount,
                'date' => $request->date,
                'party_type' => 'App\WashingTeam',
                'party_id' => $carpet_wash->team_id,
                'reference' => $request->wash_number,
                'description' => "هزینه شست قالین نمبر " . $carpet->carpet_no,
                'warehouse_id' => $carpet->warehouse_id ?? 1,
                'override_debit_account_id' => $request->account_id,
                'override_credit_account_id' => $request->override_credit_account_id,
                'area' => $request->area,
            ], function () use ($request, $carpet_wash, $carpet, $baseAmount) {
                // Legacy Data Sync + New ERP Fields
                $carpet = Carpet::where('carpet_id', '=', $request->carpetId)->first(); // Query first to get original area

                $carpet_wash->wash_number = $request->wash_number;
                $carpet_wash->wash_number_sh = $request->wash_number_sh;
                $carpet_wash->height = $request->height;
                $carpet_wash->width = $request->width;
                $carpet_wash->area = $request->area;
                $carpet_wash->area_difference = $request->area - ($carpet->area ?? 0);
                $carpet_wash->price = $request->price;
                $carpet_wash->af_total_price = $request->af_total_price;
                $carpet_wash->total_price = $request->total_price;
                $carpet_wash->currency_code = $request->currency_code;
                $carpet_wash->exchange_rate = $request->exchange_rate;
                $carpet_wash->base_currency_amount = $baseAmount;
                $carpet_wash->date = $request->date;
                $carpet_wash->description = $request->description;
                $carpet_wash->update();

                $carpet->status = 13;
                // Sync carpet dimensions in warehouse
                $carpet->height = $request->height;
                $carpet->width = $request->width;
                $carpet->area = $request->area;
                $carpet->washed_width = $request->width;
                $carpet->washed_height = $request->height;
                $carpet->washed_area = $request->area;
                $carpet->total_price = $carpet->total_price + $request->af_total_price;
                $carpet->total_price_af = $carpet->total_price_af + ($request->af_total_price * ($request->exchange_rate ?? 1));
                $carpet->warehouse_id = $request->warehouse_id;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر " . $carpet->carpet_no . " شسته شد و در سیستم مالی ثبت گردید ";
                $activity->user_id = Auth::user()->id;
                $activity->save();
            });

            if ($result && isset($result['inventory_transaction_id'])) {
                $carpet_wash->inventory_transaction_id = $result['inventory_transaction_id'];
                $carpet_wash->save();
            }

            return redirect('/dashboard/carpet-wash/wash-numbers/'.$team_id)->with('status', ' مراحل شست موفقانه ثبت شد');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetWash $wash)
    {
        return view('carpet-wash.show', compact('wash'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetWash $wash)
    {
        $washing_team = WashingTeam::all();
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('WASHING_CREDIT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('WASHING_CREDIT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'WASHING_CREDIT')->first();
        $defaultAccount = $mapping ? $mapping->debit_account_id : null;
        
        $currencies = \App\Currency::where('is_active', true)->get();
        $currency = \App\Currency::getLegacyAFNRate();

        // Forensic Account Selection Lookup
        $transaction = \App\LedgerTransaction::where('source_type', get_class($wash))
            ->where('source_id', $wash->id)
            ->where('status', 'posted')
            ->first();
        
        $existingDebitAccount = null;
        $existingCreditAccount = null;
        
        if ($transaction) {
            $debitEntry = $transaction->entries()->where('debit', '>', 0)->first();
            $creditEntry = $transaction->entries()->where('credit', '>', 0)->first();
            if ($debitEntry) $existingDebitAccount = $debitEntry->account_id;
            if ($creditEntry) $existingCreditAccount = $creditEntry->account_id;
        }

        $warehouses = \App\Warehouse::where('is_active', true)->where('subtype', 'carpet')->get();

        return view('carpet-wash.edit', compact(
            'wash',
            'washing_team',
            'allowedDebitAccounts',
            'allowedCreditAccounts',
            'mapping',
            'defaultAccount',
            'currency',
            'currencies',
            'existingDebitAccount',
            'existingCreditAccount',
            'warehouses'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetWash $wash)
    {
        $request->validate([
            'currency_code' => 'required|string',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'account_id' => 'required|integer',
            'override_credit_account_id' => 'required|integer',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
        ]);

        return DB::transaction(function () use ($request, $wash) {
            // Apply pessimistic locking for safety
            $wash = CarpetWash::where('id', $wash->id)->lockForUpdate()->firstOrFail();
            $carpet = Carpet::where('carpet_id', $request->carpetId)->lockForUpdate()->firstOrFail();

            // Accounting & Inventory Reversals are safely handled in one transaction by the manager
            $this->inventoryManager->reverseTransactions($wash, 'Wash Record Edited');

            // Resolve the initial Washing WIP warehouse
            $wipWarehouseId = DB::table('inventory_transactions')
                ->where('reference_type', 'App\Carpet')
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Washing Transfer')
                ->where('direction', 'IN')
                ->where('status', 1)
                ->value('warehouse_id') ?? $carpet->warehouse_id ?? 1;

            $warehouseId = $request->input('warehouse_id');

            if ($warehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $inventoryService = app(\App\Services\InventoryService::class);

                // Resolve original area from WIP entry
                $wipArea = DB::table('inventory_transactions')
                    ->where('reference_type', 'App\Carpet')
                    ->where('reference_id', $carpet->carpet_id)
                    ->where('type', 'Washing Transfer')
                    ->where('direction', 'IN')
                    ->where('status', 1)
                    ->value('area') ?? $carpet->area ?? 0;

                $newArea = $request->area ?? $carpet->area ?? 0;

                // Record Transfer OUT from WIP washing warehouse
                $inventoryService->recordMovement([
                    'item_model' => $wash,
                    'parent_item_model' => $carpet,
                    'type' => 'Washing Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $wipWarehouseId,
                    'area' => (float) $wipArea,
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                // Record Transfer IN to selected target warehouse
                $inventoryService->recordMovement([
                    'item_model' => $wash,
                    'parent_item_model' => $carpet,
                    'type' => 'Washing Transfer',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'warehouse_id' => $warehouseId,
                    'area' => (float) $newArea,
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $carpet->warehouse_id = $warehouseId;
            }

            // Sync carpet pricing and dimensions
            $originalArea = $carpet->area ?? 0;
            $carpet->total_price = $carpet->total_price - $wash->af_total_price + $request->af_total_price;
            $carpet->total_price_af = $carpet->total_price_af - $wash->af_total_price + ($request->af_total_price * ($request->exchange_rate ?? 1));
            $carpet->washing_id = $request->team_id;
            $carpet->height = $request->height;
            $carpet->width = $request->width;
            $carpet->area = $request->area;
            $carpet->washed_width = $request->width;
            $carpet->washed_height = $request->height;
            $carpet->washed_area = $request->area;
            $carpet->update();

            $baseAmount = ($request->currency_code === 'USD') 
                ? $request->total_price 
                : $request->total_price;

            $wash->wash_number = $request->wash_number;
            $wash->wash_number_sh = $request->wash_number_sh;
            $wash->height = $request->height;
            $wash->width = $request->width;
            $wash->area = $request->area;
            $wash->area_difference = $request->area - $originalArea;
            $wash->price = $request->price;
            $wash->af_total_price = $request->af_total_price;
            $wash->total_price = $request->total_price;
            $wash->currency_code = $request->currency_code;
            $wash->exchange_rate = $request->exchange_rate;
            $wash->base_currency_amount = $baseAmount;
            $wash->date = $request->date;
            $wash->description = $request->description;
            $wash->team_id = $request->team_id;
            $wash->update();

            // ERP Integration: Re-post value addition and accounting
            $result = $this->inventoryManager->recordProductionService($wash, $carpet, [
                'type' => 'WASHING',
                'mapping_key' => 'WASHING_CREDIT',
                'amount' => $baseAmount,
                'date' => $wash->date,
                'party_type' => 'App\WashingTeam',
                'party_id' => $wash->team_id,
                'reference' => $wash->wash_number,
                'description' => "ویرایش هزینه شست قالین نمبر " . $carpet->carpet_no,
                'warehouse_id' => $carpet->warehouse_id ?? 1,
                'override_debit_account_id' => $request->account_id,
                'override_credit_account_id' => $request->override_credit_account_id,
                'area' => $request->area,
            ]);

            if ($result && isset($result['inventory_transaction_id'])) {
                $wash->inventory_transaction_id = $result['inventory_transaction_id'];
                $wash->save();
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " شست قالین نمبر " . $carpet->carpet_no . " ویرایش شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/carpet-wash')->with('status', ' مراحل شست موفقانه بروز شد');
        });
    }

    public function sent_to_finishing_center(Request $request, Carpet $carpet)
    {
        if ($request->isMethod('get')) {
            $finishing_teams = \App\FinishingTeam::all();
            $warehouses = DB::table('warehouses')
                ->where('is_active', 1)
                ->where('subtype', 'carpet')
                ->get();
            $mapping = DB::table('mapping_rules')->where('mapping_key', 'finishing_transfer')->first();
            $defaultWarehouse = $mapping ? $mapping->warehouse_id : 1;

            $teamStats = DB::table('carpets')
                ->select('finishing_id', DB::raw('count(*) as qty'), DB::raw('sum(area) as total_area'))
                ->where('status', 4)
                ->whereNotNull('finishing_id')
                ->groupBy('finishing_id')
                ->get()
                ->keyBy('finishing_id');

            return view('finishing-center.sending-to-finishing', compact('finishing_teams', 'carpet', 'warehouses', 'defaultWarehouse', 'teamStats'));
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'finishing_id' => 'required|exists:finishing_teams,id'
        ]);

        return DB::transaction(function () use ($request, $carpet) {
            // Concurrency protection via pessimistic write-lock
            $carpet = Carpet::where('carpet_id', $carpet->carpet_id)->lockForUpdate()->firstOrFail();

            // Status check restriction removed to allow direct sending to Tayaari from any step
            // if ($carpet->status != 13) {
            //     return redirect('/dashboard/carpet-wash')->with('error', 'قالین مذکور شسته شده نیست یا قبلا تعیین وضعیت شده است.');
            // }

            $sourceWarehouseId = $carpet->warehouse_id ?? 1;
            $targetWarehouseId = $request->warehouse_id;

            // Update carpet state
            $carpet->status = 4; // In Tayaari (Finishing)
            $carpet->finishing_id = $request->finishing_id;
            $carpet->warehouse_id = $targetWarehouseId;
            $carpet->update();

            $wh = \App\Warehouse::find($targetWarehouseId);
            $warehouseName = $wh ? $wh->name : null;

            // Record action in system activity log
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " به بخش تیاری ارسال شد " .
                ($warehouseName ? " (انبار: " . $warehouseName . ")" : "") . " ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // Record physical inventory movement (transfer) if the warehouse actually changed
            if ($targetWarehouseId != $sourceWarehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $inventoryService = app(\App\Services\InventoryService::class);

                $inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'parent_item_model' => $carpet,
                    'type' => 'Finishing Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $sourceWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'parent_item_model' => $carpet,
                    'type' => 'Finishing Transfer',
                    'direction' => 'IN',
                    'quantity' => 1,
                    'warehouse_id' => $targetWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);
            }

            $redirectTo = $request->input('redirect_to', '/dashboard/carpet-wash');
            return redirect($redirectTo)->with('status', 'قالین موفقانه به بخش تیاری فرستاده شد');
        });
    }

    public function sent_to_kachaee(Request $request, Carpet $carpet)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'kachaee_id' => 'required|exists:kachaees,id'
        ]);

        return DB::transaction(function () use ($request, $carpet) {
            // Concurrency protection via pessimistic write-lock
            $carpet = Carpet::where('carpet_id', $carpet->carpet_id)->lockForUpdate()->firstOrFail();

            if ($carpet->status != 13) {
                return redirect('/dashboard/carpet-wash')->with('error', 'قالین مذکور شسته شده نیست یا قبلا تعیین وضعیت شده است.');
            }

            $sourceWarehouseId = $carpet->warehouse_id ?? 1;
            $targetWarehouseId = $request->warehouse_id;

            // Update carpet state
            $carpet->status = 2; // In Kachaee
            $carpet->kachaee_id = $request->kachaee_id;
            $carpet->warehouse_id = $targetWarehouseId;
            $carpet->update();

            // Record action in system activity log
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " به بخش کچایی ارسال شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // ERP Integration: Physical movement with WAC cost
            $carpetCost = DB::table('items')
                ->where('type', 'App\Carpet')
                ->where('ref_id', $carpet->carpet_id)
                ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

            $inventoryService = app(\App\Services\InventoryService::class);

            // Record Transfer OUT from old warehouse
            $inventoryService->recordMovement([
                'item_model' => $carpet,
                'parent_item_model' => $carpet,
                'type' => 'Kachaee Transfer',
                'direction' => 'OUT',
                'quantity' => 1,
                'warehouse_id' => $sourceWarehouseId,
                'area' => (float) ($carpet->area ?? 0),
                'unit_cost' => $carpetCost,
                'currency_code' => 'USD',
                'exchange_rate' => 1.0,
                'created_by' => auth()->id()
            ]);

            // Record Transfer IN to selected target warehouse
            $inventoryService->recordMovement([
                'item_model' => $carpet,
                'parent_item_model' => $carpet,
                'type' => 'Kachaee Transfer',
                'direction' => 'IN',
                'quantity' => 1,
                'warehouse_id' => $targetWarehouseId,
                'area' => (float) ($carpet->area ?? 0),
                'unit_cost' => $carpetCost,
                'currency_code' => 'USD',
                'exchange_rate' => 1.0,
                'created_by' => auth()->id()
            ]);

            return redirect('/dashboard/carpet-wash')->with('status', 'قالین موفقانه به بخش کچایی ارسال گردید');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $wash = CarpetWash::find($id);
            // Reverse Accounting - pass class name to avoid ID collision reversals with other models
            $this->accountingService->reverseTransactionBySource($wash->id, 'Wash Record Deleted', get_class($wash));
            $wash->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
