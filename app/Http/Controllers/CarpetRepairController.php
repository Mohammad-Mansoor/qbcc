<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Agents;

use App\CarpetCheckBook;
use App\Kachaee;
use App\FinishingTeam;
use App\CarpetRepair;
use App\OfficeCashBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;
use App\Services\InventoryService;

class CarpetRepairController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;
    protected $inventoryService;

    public function __construct(
        AccountingService $accountingService, 
        \App\Services\InventoryTransactionManager $inventoryManager,
        \App\Services\InventoryService $inventoryService
    ) {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
        $this->inventoryService = $inventoryService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function return_to_center_from_non_repair($carpet_id){
        return DB::transaction(function () use ($carpet_id) {
            $carpet = Carpet::find($carpet_id);
            
            // Get original source warehouse from the latest OUT transaction for this carpet
            $originalTransaction = DB::table('inventory_transactions')
                ->where('reference_type', get_class($carpet))
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Kachaee Transfer')
                ->where('direction', 'OUT')
                ->where('status', 1)
                ->orderByDesc('id')
                ->first();
            $originalWarehouseId = $originalTransaction ? $originalTransaction->warehouse_id : 1;

            $targetWarehouseId = request('warehouse_id') ?? $originalWarehouseId;

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " از کچای نشده ها به دفتر مرکزی بازگشت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            $carpet->status = 1;
            $carpet->kachaee_id = null;
            $carpet->warehouse_id = $targetWarehouseId;
            $carpet->update();

            // Reverse inventory movement
            $this->inventoryService->reverseMovement($carpet, 'Returned from Kachaee (Non-Repair)');

            // If target warehouse is different, record transfer to it
            if ($targetWarehouseId != $originalWarehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $this->inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'type' => 'Warehouse Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $originalWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $this->inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'type' => 'Warehouse Transfer',
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

            return back()->with('status','موفقانه بازگشت شد !');
        });
    }

    public function return_to_center_from_repair($carpet_id){
        return DB::transaction(function () use ($carpet_id) {
            $carpet_repair = CarpetRepair::where('carpetId',$carpet_id)->first();
            
            if ($carpet_repair) {
                // Reverse Accounting - pass class name to avoid ID collision reversals with other models
                $this->accountingService->reverseTransactionBySource($carpet_repair->id, 'Returned to center from repair', get_class($carpet_repair));
                // Reverse Value Adjustment
                $this->inventoryService->reverseMovement($carpet_repair, 'Reversing Repair Cost');
                $carpet_repair->delete();
            }
            
            $carpet = Carpet::find($carpet_id);

            // Get original source warehouse from the latest OUT transaction for this carpet
            $originalTransaction = DB::table('inventory_transactions')
                ->where('reference_type', get_class($carpet))
                ->where('reference_id', $carpet->carpet_id)
                ->where('type', 'Kachaee Transfer')
                ->where('direction', 'OUT')
                ->where('status', 1)
                ->orderByDesc('id')
                ->first();
            $originalWarehouseId = $originalTransaction ? $originalTransaction->warehouse_id : 1;

            $targetWarehouseId = request('warehouse_id') ?? $originalWarehouseId;

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " از کچای شده ها به دفتر مرکزی بازگشت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            $carpet->status = 1;
            $carpet->kachaee_id = null;
            $carpet->warehouse_id = $targetWarehouseId;
            $carpet->update();

            // Reverse the physical transfer
            $this->inventoryService->reverseMovement($carpet, 'Returned from Kachaee (Repaired)');

            // If target warehouse is different, record transfer to it
            if ($targetWarehouseId != $originalWarehouseId) {
                $carpetCost = DB::table('items')
                    ->where('type', 'App\Carpet')
                    ->where('ref_id', $carpet->carpet_id)
                    ->value('current_cost') ?? (float) ($carpet->total_price ?? 0);

                $this->inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'type' => 'Warehouse Transfer',
                    'direction' => 'OUT',
                    'quantity' => 1,
                    'warehouse_id' => $originalWarehouseId,
                    'area' => (float) ($carpet->area ?? 0),
                    'unit_cost' => $carpetCost,
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.0,
                    'created_by' => auth()->id()
                ]);

                $this->inventoryService->recordMovement([
                    'item_model' => $carpet,
                    'type' => 'Warehouse Transfer',
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

            return back()->with('status','موفقانه بازگشت شد !');
        });
    }
    public function index()
    {
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status','=',2)->where('kachaee_id','!=',null)->orderBy('updated_at','DESC')->get();
        $repaireds = CarpetRepair::orderBy('carpetId','DESC')->paginate(30);
        $warehouses = DB::table('warehouses')->get();
        $search = '';
        return view('carpet-repair.index',compact('nonrepaireds', 'agents', 'repaireds','search','warehouses'));
    }

    public function search_kachaee_number($kachaee_number,$team_id){
        $team = Kachaee::find($team_id);
        $carpet_repairs = CarpetRepair::Where('team_id', '=', $team_id)->where('kachaee_number','=',$kachaee_number)->get();

        $quantity = CarpetRepair::Where('team_id', '=', $team_id)->where('kachaee_number','=',$kachaee_number)->count();

        return view('kachaee.kachaee-number-list', compact('carpet_repairs','team','kachaee_number','quantity'));



    }



    // SENDING CARPET FOR REPAIR
    public function sending_to_repair(Carpet $carpetId){
        $check = CarpetCheckBook::where('carpet_id',$carpetId->carpet_id)->first();
        // Fetch warehouse mappings
        $mapping = DB::table('mapping_rules')->where('transaction_type', 'kachaee_transfer')->first();
        $defaultWarehouse = $mapping ? $mapping->warehouse_id : 1;
        
        // Filter warehouses of subtype 'carpet'
        $warehouses = DB::table('warehouses')
            ->where('is_active', 1)
            ->where('subtype', 'carpet')
            ->get();

        // Calculate Kachaee team statistics (carpets currently held with status = 2)
        $teamStats = DB::table('carpets')
            ->select('kachaee_id', DB::raw('count(*) as qty'), DB::raw('sum(area) as total_area'))
            ->where('status', 2)
            ->whereNotNull('kachaee_id')
            ->groupBy('kachaee_id')
            ->get()
            ->keyBy('kachaee_id');

        if(Auth::user()->role != 'SO' && Auth::user()->role != 'SP'){
            if(!empty($check)){
                $okay = CarpetCheckBook::where('carpet_id',$carpetId->carpet_id)->first();
                if($okay->kachaee_amount != 0 || $okay->kachaee_amount != null){
                    $kachaee_team = Kachaee::all();
                    return view('carpet-repair.sending-to-repair',compact('kachaee_team','carpetId', 'warehouses', 'defaultWarehouse', 'teamStats'));
                }else{
                    return back()->with('error','قالین مذکور برای کچایی ثبت نشده است !');
                }
            }else{
                return back()->with('error','قالین مذکور برای کچایی ثبت نشده است !');
            }
        }else{
            $kachaee_team = Kachaee::all();
            return view('carpet-repair.sending-to-repair',compact('kachaee_team','carpetId', 'warehouses', 'defaultWarehouse', 'teamStats'));
        }
    }
    // REPAIR GETTING DONE
    public function repair_team_selected(Request $request, Carpet $carpetId){
        $request->validate([
            'team_id' => 'required',
            'warehouse_id' => 'required'
        ]);

        return DB::transaction(function () use ($request, $carpetId) {
            $carpetId->status = 2;
            $carpetId->kachaee_id = $request->team_id;
            
            $sourceWarehouseId = $carpetId->warehouse_id ?? 1;
            $carpetId->warehouse_id = $request->warehouse_id;
            $carpetId->update();
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر  " . $carpetId->carpet_no . " به کچایی ارسال شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // ERP Integration: Log the transfer to WIP Warehouse with complete cost details
            $carpetCost = DB::table('items')
                ->where('type', 'App\Carpet')
                ->where('ref_id', $carpetId->carpet_id)
                ->value('current_cost') ?? (float) ($carpetId->total_price ?? 0);

            $this->inventoryService->recordMovement([
                'item_model' => $carpetId,
                'type' => 'Kachaee Transfer',
                'direction' => 'OUT',
                'quantity' => 1,
                'warehouse_id' => $sourceWarehouseId,
                'area' => (float) ($carpetId->area ?? 0),
                'unit_cost' => $carpetCost,
                'currency_code' => 'USD',
                'exchange_rate' => 1.0,
                'created_by' => auth()->id()
            ]);

            $this->inventoryService->recordMovement([
                'item_model' => $carpetId,
                'type' => 'Kachaee Transfer',
                'direction' => 'IN',
                'quantity' => 1,
                'warehouse_id' => $request->warehouse_id,
                'area' => (float) ($carpetId->area ?? 0),
                'unit_cost' => $carpetCost,
                'currency_code' => 'USD',
                'exchange_rate' => 1.0,
                'created_by' => auth()->id()
            ]);
            
            if($carpetId->agent->contract_type == 'contractional')
            {
                return redirect('/dashboard/contract-carpet');
            }elseif($carpetId->agent->contract_type == 'weight')
            {
                return redirect('/dashboard/list-weight');
            }else{
                return redirect('/dashboard/list-buy-carpet');
            }
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRepair(Carpet $id)
    {
        $lastId = CarpetRepair::where('team_id',$id->kachaee_id)->latest()->first();
        $KachaeeNo = '';
        if($lastId) {
            $lastId = $lastId->kachaee_number;
            $lastId = substr($lastId,-1);
            $lastId++;
            $KachaeeNo = 'KCH-'.sprintf('%01d' , $lastId);
        } else {
            $KachaeeNo = 'KCH-'.sprintf('%01d'  , '1');
        }
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('kachaee_repair_cost', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('kachaee_repair_cost', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'kachaee_repair_cost')->first();
        $defaultAccount = $mapping ? $mapping->debit_account_id : 1;
        $currency = \App\Currency::getLegacyAFNRate();
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('carpet-repair.create',compact('id','KachaeeNo', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'defaultAccount', 'currency', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CarpetRepair $carpetRepair)
    {
        return DB::transaction(function () use ($request, $carpetRepair) {
            $data = $this->validAll();

            // Strict Currency Processing
            $currency = $request->currency_code ?? 'AFN';
            if ($currency == 'USD') {
                $base_amount = $request->total_price;
                $rate = 1;
            } else {
                $rate = $request->exchange_rate ?? 1;
                $base_amount = $request->af_total_price / ($rate > 0 ? $rate : 1);
            }

            $data['currency_code'] = $currency;
            $data['exchange_rate'] = $rate;
            $data['base_currency_amount'] = $base_amount;

            // account_id is used for ledger entry mapping, not stored directly in carpet_repairs
            unset($data['account_id']);

            $record = $carpetRepair->create($data);
            $finish = Carpet::where('carpet_id','=',$request->carpetId)->first();
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر  " . $finish->carpet_no . " کچایی شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            $finish->status = 12;
            $finish->update();

            // ERP Integration: Capitalize Cost and Post Payable
            $this->inventoryManager->recordProductionService($record, $finish, [
                'type' => 'KACHAEE',
                'mapping_key' => 'kachaee_repair_cost',
                'amount' => $base_amount,
                'date' => $request->date,
                'party_type' => 'App\Kachaee',
                'party_id' => $request->team_id,
                'reference' => $request->kachaee_number,
                'description' => "مصرف ترمیم قالین نمبر " . $finish->carpet_no . " - " . $request->description,
                'warehouse_id' => $finish->warehouse_id ?? 1,
                'override_debit_account_id' => $request->account_id,
                'override_credit_account_id' => $request->override_credit_account_id
            ]);

            return redirect('/dashboard/carpet-repair')->with('status',' مراحل ترمیم موفقانه ثبت شد');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetRepair $carpetRepair)
    {
        return view('carpet-repair.show',['carpet' => $carpetRepair]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetRepair $carpetRepair)
    {
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('kachaee_repair_cost', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('kachaee_repair_cost', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'kachaee_repair_cost')->first();
        $defaultAccount = $mapping ? $mapping->debit_account_id : 1;
        $currency = \App\Currency::getLegacyAFNRate();
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('carpet-repair.edit',compact('carpetRepair', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'defaultAccount', 'currency', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetRepair $carpetRepair)
    {
        return DB::transaction(function () use ($request, $carpetRepair) {
            $data = $this->validAll();
            
            // Reversals - pass class name to avoid ID collision reversals with other models
            $this->accountingService->reverseTransactionBySource($carpetRepair->id, 'Kachaee Repair Edited', get_class($carpetRepair));
            $this->inventoryService->reverseMovement($carpetRepair, 'Reversing Repair Cost for Edit');

            // Strict Currency Processing
            $currency = $request->currency_code ?? 'AFN';
            if ($currency == 'USD') {
                $base_amount = $request->total_price;
                $rate = 1;
            } else {
                $rate = $request->exchange_rate ?? 1;
                $base_amount = $request->af_total_price / ($rate > 0 ? $rate : 1);
            }

            $data['currency_code'] = $currency;
            $data['exchange_rate'] = $rate;
            $data['base_currency_amount'] = $base_amount;
            
            // account_id is used for ledger entry mapping, not stored directly in carpet_repairs
            unset($data['account_id']);
            
            $carpetRepair->update($data);

            $carpet = Carpet::find($request->carpetId);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "کچایی قالین نمبر  " . $carpet->carpet_no . " ویرایش شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            // ERP Integration: Re-post Value addition and accounting
            $this->inventoryManager->recordProductionService($carpetRepair, $carpet, [
                'type' => 'KACHAEE',
                'mapping_key' => 'kachaee_repair_cost',
                'amount' => $base_amount,
                'date' => $request->date,
                'party_type' => 'App\Kachaee',
                'party_id' => $request->team_id,
                'reference' => $request->kachaee_number,
                'description' => "ویرایش مصرف ترمیم قالین نمبر " . $carpet->carpet_no . " - " . $request->description,
                'warehouse_id' => $carpet->warehouse_id ?? 1,
                'override_debit_account_id' => $request->account_id,
                'override_credit_account_id' => $request->override_credit_account_id
            ]);

            return redirect('/dashboard/carpet-repair')->with('status', ' مراحل ترمیم موفقانه بروز شد');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarpetRepair $carpetRepair)
    {
        //
    }

    protected function validAll(){
        return request()->validate([
            'price' => 'required',
            'kachaee_number' => 'required',
            'total_price' => 'required',
            'af_total_price' => 'required',
            'date' => 'required',
            'carpetId' => 'required',
            'team_id' => 'required',
            'description' => 'required',
            'account_id' => 'nullable',
            'currency_code' => 'nullable',
            'exchange_rate' => 'nullable',
        ]);
    }


    public function repair_search(Request $request)
    {
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status', '=', 2)
            ->where('carpet_no', 'like', '%'.$request->search.'%')
           ->get();


        $repaireds = CarpetRepair::orderBy('carpetId','DESC')->paginate(30);
        $warehouses = DB::table('warehouses')->get();
        $search = '';
        return view('carpet-repair.index', compact('nonrepaireds', 'agents', 'repaireds','search','warehouses'));
    }
    public function search_repaired(Request $request)
    {
        $search = $request->search;
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status','=',2)->where('kachaee_id','!=',null)->orderBy('updated_at','DESC')->get();
        $repaireds = CarpetRepair::where('kachaee_number', 'like', '%'.$search.'%')
            ->orWhereHas('carpet', function ($query) use ($search) {
                $query->where('carpet_no', 'like', '%' . $search . '%');
            })
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->get();
        $warehouses = DB::table('warehouses')->get();
        return view('carpet-repair.index', compact('nonrepaireds', 'agents', 'repaireds','search','warehouses'));
    }
    public function repair_date_search(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status','=',2)->where('kachaee_id','!=',null)->orderBy('updated_at','DESC')->get();
        $repaireds = CarpetRepair::whereBetween("date", [$start, $end])->get();
        $warehouses = DB::table('warehouses')->get();
        $search = 'search';
        return view('carpet-repair.index', compact('nonrepaireds', 'agents', 'repaireds','search','warehouses'));
    }
}
