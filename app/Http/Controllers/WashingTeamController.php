<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetWash;
use App\WashingPayment;
use App\WashingTeam;
use App\Carpet;
use App\ReceivedOfWashing;
use App\WashingTotalAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

class WashingTeamController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = WashingTeam::all();
        $credit_us = WashingPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = WashingPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = WashingPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = WashingPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $team = '';
        return view('washing.index', compact('teams', 'credit_us', 'debit_us', 'credit_af', 'debit_af', 'team'));

    }

    public function accounts()
    {
        $teams = WashingTeam::all();
        $credit_us = WashingPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = WashingPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = WashingPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = WashingPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $accounts = '';
        $team = '';
        return view('washing.index', compact('teams', 'credit_us', 'debit_us', 'credit_af', 'debit_af', 'accounts', 'team'));
    }

    public function search(Request $request)
    {
        $search = $request->search;


        $teams = WashingTeam::where('name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%')
            ->orWhere('address', 'like', '%' . $search . '%')
            ->orWhere('contact_no', 'like', '%' . $search . '%')
            ->get();
        $credit_us = WashingPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = WashingPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = WashingPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = WashingPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $team = '';
        return view('washing.index', compact('teams', 'credit_us', 'debit_us', 'credit_af', 'debit_af', 'search', 'team'));


    }

    // SENDING CARPET FOR WASHING
    public function sending_to_washing(Carpet $carpetId)
    {
        $washing_team = WashingTeam::all();
        $lastId = CarpetWash::latest()->first();
        $WashNo = '';
        if ($lastId) {
            $lastId = $lastId->wash_number;
            $lastId++;

            $WashNo = $lastId;
        } else {
            $WashNo = 'WSH-' . 1;
        }
        $mapping = \App\MappingRule::where('mapping_key', 'washing_transfer')->first();
        $defaultWarehouse = $mapping ? $mapping->warehouse_id : 1;
        $warehouses = DB::table('warehouses')->get();
        return view('washing.sending-to-washing', compact('washing_team', 'carpetId', 'WashNo', 'warehouses', 'defaultWarehouse'));
    }

    // WASHING GETTING DONE
    public function washing_team_selected(Request $request, Carpet $carpetId)
    {
        $request->validate([
            'team_id' => 'required',
            'warehouse_id' => 'required'
        ]);

        return DB::transaction(function () use ($request, $carpetId) {
            $lastId = CarpetWash::where('team_id',$request->team_id)->latest()->first();

            $WashNo = '';
            if ($lastId) {
                if ($lastId->wash_number_sh) {
                    $lastId = $lastId->wash_number_sh;
                    $WashNo = $lastId;
                }
                else {
                    $WashNo = 'SH-0';
                }
            }

            $wash = new CarpetWash();
            $wash->wash_number = $request->wash_number;
            $wash->wash_number_sh_c = $WashNo;
            $wash->carpetId = $carpetId->carpet_id;
            $wash->team_id = $request->team_id;
            $wash->save();
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpetId->carpet_no . " به شست ارسال شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $carpetId->status = 3;
            $carpetId->washing_id = $request->team_id;
            $upd =  $carpetId->update();

            // ERP Integration: Log the transfer to WIP Warehouse
            // Assume coming from Main Warehouse (1)
            $this->inventoryService->recordMovement([
                'item_model' => $carpetId,
                'type' => 'Washing Transfer',
                'direction' => 'OUT',
                'quantity' => 1,
                'warehouse_id' => 1,
                'created_by' => auth()->id()
            ]);

            $this->inventoryService->recordMovement([
                'item_model' => $carpetId,
                'type' => 'Washing Transfer',
                'direction' => 'IN',
                'quantity' => 1,
                'warehouse_id' => $request->warehouse_id,
                'created_by' => auth()->id()
            ]);

            if($upd){
                if ($carpetId->agent->contract_type == 'contractional') {
                    return redirect('dashboard/contract-carpet')->with('status', 'موفقانه ارسال شد');

                } elseif ($carpetId->agent->contract_type == 'weight') {
                    return redirect('dashboard/list-weight')->with('status', 'موفقانه ارسال شد');
                } else {
                    return redirect('dashboard/list-buy-carpet')->with('status', 'موفقانه ارسال شد');
                }
            }else{
                return redirect()->back()->with('error','ارسال نشد');
            }
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('washing.create');
    }

    public function team_carpets($id)
    {

        $carpets = CarpetWash::with('carpet')->where('team_id', $id)->get();
        $team = WashingTeam::find($id);

        return view('washing.washing-team-carpet', compact('carpets', 'team'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, WashingTeam $team)
    {
        $done = $team->create($this->valData());
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " شست گر به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($done) {
            return redirect('/dashboard/washing-team')->with('status', 'کارگر موفقانه ثبت شد');
        } else {
            return redirect()->back()->with('error', 'کارگر  ثبت نشد');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WashingTeam $washingTeam
     * @return \Illuminate\Http\Response
     */
    public function show(WashingTeam $team)
    {
        $total = WashingTotalAccount::where('washing_id', $team->id)->sum('total');
        $paid = WashingTotalAccount::where('washing_id', $team->id)->sum('paid');
        $remaining = WashingTotalAccount::where('washing_id', $team->id)->sum('remaining');
        $quantity = Carpet::where('washing_id', $team->id)->count();

        $resived = ReceivedOfWashing::where('washing_id', $team->id)->get();
        $paymentEdit = '';
        return view('washing.washing-payment', compact('total', 'paid', 'remaining', 'team', 'quantity', 'resived', 'paymentEdit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WashingTeam $washingTeam
     * @return \Illuminate\Http\Response
     */
    public function edit(WashingTeam $team)
    {

        $teams = WashingTeam::all();
        $credit_us = WashingPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = WashingPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = WashingPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = WashingPayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('washing.index', compact('teams', 'credit_us', 'debit_us', 'credit_af', 'debit_af', 'team'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\WashingTeam $washingTeam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WashingTeam $team)
    {
        $done = $team->update($this->valData());
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " شست گر به نام " . $request->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($done) {
            return redirect('/dashboard/washing-team')->with('status', 'کارگر موفقانه بروز شد');
        } else {
            return redirect()->back()->with('error', 'کارگر  ویرایش  نشد');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WashingTeam $washingTeam
     * @return \Illuminate\Http\Response
     */
    public function destroy(WashingTeam $washingTeam)
    {
        //
    }

    protected function valData()
    {
        return request()->validate([
            'name' => 'required',
            'last_name' => 'required',
            'contact_no' => 'required',
            'address' => 'required',
        ]);
    }
}
