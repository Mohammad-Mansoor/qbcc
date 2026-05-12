<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetWash;
use App\WashingPayment;
use App\WashingTeam;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WashingPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $accountingService;
    
    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        //
    }

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $mKey = ($payment->type == 'گرفت') ? 'PYMT_OUT' : 'PYMT_IN';
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;

            $this->accountingService->postAutoTransaction('payment', $mKey, array_merge([
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\WashingTeam',
                'party_id' => $payment->team_id,
                'reference' => 'W-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ], $overrides));
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Washing Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }


    public function money_request()
    {
        $requests = WashingPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('washing.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = WashingPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0) 
                ? " مبلغ " . $payment->amount . "دالر برای شست‌گر تایید شد "
                : " مبلغ " . $payment->amount_af . "افغانی برای شست‌گر تایید شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }
    public function delete_request($id){
        $credit = WashingPayment::find($id);
        $credit->delete();
        return response()->json(['status','error']);
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
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'team_id' => 'required',
            ]);

            $payed = new WashingPayment();
            $payed->team_id = $request->team_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->wash_number = $request->wash_number;

            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }

            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->save();

            if ($payed->status == 1) {
                $overrides = [];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;
                $this->postPaymentToAccounting($payed, $overrides);
            }

            $team_name = DB::table('washing_teams')->where('id', $request->team_id)->first();
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "پرداخت به شست‌گر " . $team_name->name . " به مبلغ " . $request->amount . " " . $request->money_type;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد و در سیستم مالی درج گردید!');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WashingPayment  $washingPayment
     * @return \Illuminate\Http\Response
     */
    public function show($team_id)
    {
        $payments = WashingPayment::where('team_id',$team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = WashingTeam::find($team_id);
        $debits_us = WashingPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = WashingPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = WashingPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = WashingPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('washing.washing-payment',compact(
            'team','payments','paymentEdit','debits_us','debits_af','credit_af',
            'credit_us','wash_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'
        ));
    }
    public function show_all_payment($team_id){
        $payments = WashingPayment::where('team_id',$team_id)->orderBy('created_at','DESC')->get();
        $team = WashingTeam::find($team_id);
        $debits_us = WashingPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = WashingPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = WashingPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = WashingPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        $all = '';
        return view('washing.washing-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','wash_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WashingPayment  $washingPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = WashingPayment::find($payment_id);
        $payments = WashingPayment::where('team_id',$paymentEdit->team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = WashingTeam::find($paymentEdit->team_id);
        $debits_us = WashingPayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $debits_af = WashingPayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $credit_us = WashingPayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $credit_af = WashingPayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $wash_numbers = CarpetWash::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['wash_number_sh']);
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('washing.washing-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','wash_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WashingPayment  $washingPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
            ]);

            $payed = WashingPayment::find($payment_id);
            $team_name = DB::table('washing_teams')->where('id', $request->team_id)->first();

            // Reverse Old Accounting Entry (Only if approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Washing Payment Edited');
            }

            // Update record
            $payed->team_id = $request->team_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->wash_number = $request->wash_number;

            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            $payed->update();

            // Post New Accounting Entry (Only if approved)
            if ($payed->status == 1) {
                $overrides = [];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;
                $this->postPaymentToAccounting($payed, $overrides);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت شست‌گر " . $team_name->name . " اکونت نمبر " . $team_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/washing-payments/'.$request->team_id)->with('status', 'ویرایش با موفقیت انجام و اسناد مالی بروز شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WashingPayment  $washingPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = WashingPayment::find($id);
            $team_name = DB::table('washing_teams')->where('id', $payment->team_id)->first();

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Washing Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت شست‌گر " . $team_name->name . " اکونت نمبر " . $team_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
