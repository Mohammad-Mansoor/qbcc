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
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " از شست به دفتر مرکزی بازگشت داده شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            $carpet->status = 1;
            $carpet->washing_id = null;
            $carpet->update();

            // Reverse accounting if any
            $this->accountingService->reverseTransactionBySource($carpet_wash->id, 'Return to Center');

            $carpet_wash->delete();

            return redirect('/dashboard/carpet-wash')->with('status','موفقانه بازگشت شد !');
        });
    }

    public function return_to_kachaee($wash_id){
        return DB::transaction(function () use ($wash_id) {
            $carpet_wash = CarpetWash::find($wash_id);
            $carpet = Carpet::where('carpet_id',$carpet_wash->carpetId)->first();
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " از شست به کچایی بازگشت داده شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $carpet->status = 12;
            $carpet->washing_id = null;
            $carpet->update();

            // Reverse accounting if any
            $this->accountingService->reverseTransactionBySource($carpet_wash->id, 'Return to Kachaee');

            $carpet_wash->delete();

            return redirect('/dashboard/carpet-wash')->with('status','موفقانه بازگشت شد !');
        });
    }

    public function index()
    {
        $agents = Agents::all();
        $washeds = CarpetWash::orderBy('created_at', 'DESC')->paginate(60);
        $team = WashingTeam::all();
        $wash_check = 1;
        return view('carpet-wash.index', compact('agents', 'washeds', 'team', 'wash_check'));
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

        if ($request->has('from_non_washed')) {
            $team = WashingTeam::where('id', $team_id)->get();
            $wash_check = 1;
            $washeds = CarpetWash::orderBy('date', 'DESC')->paginate(60);
            return view('carpet-wash.index', compact('washeds', 'team', 'wash_check'));
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
            return view('carpet-wash.index', compact('washeds', 'team', 'team_id', 'wash_check'));
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
        return view('carpet-wash.index', compact('washeds', 'team', 'wash_check'));
    }

    public function create_carpet_wash($id)
    {
        $carpet_wash = CarpetWash::find($id);
        $lastId = CarpetWash::where('team_id',$carpet_wash->team_id)->latest()->first();
        $WashNo = $lastId ? $lastId->wash_number_sh_c + 1 : 1;
        
        $selectionService = new \App\Services\AccountSelectionService();
        $accounts = $selectionService->getValidAccounts('WASHING_CREDIT', 'debit');
        $mapping = \App\MappingRule::where('mapping_key', 'WASHING_CREDIT')->first();
        $defaultAccount = $mapping ? $mapping->debit_account_id : null;
        
        $currency = \App\Currency::getLegacyAFNRate();

        return view('carpet-wash.create', compact('carpet_wash','WashNo', 'accounts', 'defaultAccount', 'currency'));
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
            'currency_code' => 'required|in:USD,AFN',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'account_id' => 'required|integer',
        ]);

        $carpet_wash = CarpetWash::find($request->wash_id);
        $team_id = $carpet_wash->team_id;
        $carpet = Carpet::find($request->carpetId);

        // Convert amount to Base Currency (USD) for the ledger
        $baseAmount = ($request->currency_code === 'USD') 
            ? $request->total_price 
            : $request->total_price; // Total price comes from JS as USD base

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
        ], function () use ($request, $carpet_wash, $carpet, $baseAmount) {
            // Legacy Data Sync + New ERP Fields
            $carpet_wash->wash_number = $request->wash_number;
            $carpet_wash->wash_number_sh = $request->wash_number_sh;
            $carpet_wash->height = $request->height;
            $carpet_wash->width = $request->width;
            $carpet_wash->area = $request->area;
            $carpet_wash->price = $request->price;
            $carpet_wash->af_total_price = $request->af_total_price;
            $carpet_wash->total_price = $request->total_price;
            $carpet_wash->currency_code = $request->currency_code;
            $carpet_wash->exchange_rate = $request->exchange_rate;
            $carpet_wash->base_currency_amount = $baseAmount;
            $carpet_wash->date = $request->date;
            $carpet_wash->description = $request->description;
            // inventory_transaction_id will be saved outside the callback if possible, 
            // but recordProductionService saves the model internally, so we'll update it after.
            $carpet_wash->update();

            $carpet = Carpet::where('carpet_id', '=', $request->carpetId)->first();
            $carpet->status = 13;
            $carpet->total_price = $carpet->total_price + $request->af_total_price;
            $carpet->total_price_af = $carpet->total_price_af + ($request->af_total_price * ($request->exchange_rate ?? 1));
            $carpet->update();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet->carpet_no . " شسته شد و در سیستم مالی ثبت گردید ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
        });

        if ($result && isset($result['inventory_transaction_id'])) {
            $carpet_wash->inventory_transaction_id = $result['inventory_transaction_id']->id;
            $carpet_wash->save();
        }

        return redirect('/dashboard/carpet-wash/wash-numbers/'.$team_id)->with('status', ' مراحل شست موفقانه ثبت شد');
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
        $accounts = $selectionService->getValidAccounts('WASHING_CREDIT', 'debit');
        $mapping = \App\MappingRule::where('mapping_key', 'WASHING_CREDIT')->first();
        $defaultAccount = $mapping ? $mapping->debit_account_id : null;
        
        $currency = \App\Currency::getLegacyAFNRate();

        return view('carpet-wash.edit', compact('wash','washing_team', 'accounts', 'defaultAccount', 'currency'));
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
            'currency_code' => 'required|in:USD,AFN',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'account_id' => 'required|integer',
        ]);

        return DB::transaction(function () use ($request, $wash) {
            $carpet = Carpet::find($request->carpetId);

            $carpet->total_price = $carpet->total_price - $wash->af_total_price + $request->af_total_price;
            $carpet->total_price_af = $carpet->total_price_af - $wash->af_total_price + ($request->af_total_price * ($request->exchange_rate ?? 1));
            $carpet->washing_id = $request->team_id;
            $carpet->update();

            // Accounting Reversal
            $this->accountingService->reverseTransactionBySource($wash->id, 'Wash Record Edited');
            $this->inventoryManager->reverseTransactions($wash, 'Wash Record Edited');

            $baseAmount = ($request->currency_code === 'USD') 
                ? $request->total_price 
                : $request->total_price;

            $wash->wash_number = $request->wash_number;
            $wash->wash_number_sh = $request->wash_number_sh;
            $wash->height = $request->height;
            $wash->width = $request->width;
            $wash->area = $request->area;
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
                'type' => 'WASHING_EDIT',
                'amount' => $baseAmount,
                'date' => $wash->date,
                'party_type' => 'App\WashingTeam',
                'party_id' => $wash->team_id,
                'reference' => $wash->wash_number,
                'description' => "ویرایش هزینه شست قالین نمبر " . $carpet->carpet_no,
                'warehouse_id' => $carpet->warehouse_id ?? 1,
                'override_debit_account_id' => $request->account_id,
            ]);

            if ($result && isset($result['inventory_transaction_id'])) {
                $wash->inventory_transaction_id = $result['inventory_transaction_id']->id;
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

    public function sent_to_finishing_center(Carpet $carpet)
    {
        $carpet->status = 4;
        $carpet->update();
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $carpet->carpet_no . " به تیاری ارسال شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        return redirect('/dashboard/carpet-wash')->with('status', 'قالین موفقانه به بخش تیاری فرستاده شد');
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $wash = CarpetWash::find($id);
            $this->accountingService->reverseTransactionBySource($wash->id, 'Wash Record Deleted');
            $wash->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
