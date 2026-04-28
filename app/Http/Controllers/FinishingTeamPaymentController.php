<?php

namespace App\Http\Controllers;

use App\Activity;
use App\FinishingTeam;
use App\FinishingTeamPayment;
use App\FinishingWork;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinishingTeamPaymentController extends Controller
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

    private function postPaymentToAccounting($payment)
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;

            $this->accountingService->postAutoTransaction('finishing_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\FinishingTeam',
                'party_id' => $payment->team_id,
                'reference' => 'F-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Finishing Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function money_request()
    {
        $requests = FinishingTeamPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('finishing-center.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = FinishingTeamPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0) 
                ? " مبلغ " . $payment->amount . "دالر برای تیم تیاری تایید شد "
                : " مبلغ " . $payment->amount_af . "افغانی برای تیم تیاری تایید شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }
    public function delete_request($id){
        $credit = FinishingTeamPayment::find($id);
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

            $payed = new FinishingTeamPayment();
            $payed->team_id = $request->team_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->finish_number = $request->finish_number;
            
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
                $this->postPaymentToAccounting($payed);
            }

            $team_name = DB::table('finishing_teams')->where('id', $request->team_id)->first();
            $currency = ($request->money_type == 'دالر') ? " دالر " : " افغانی ";

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت به تیم تیاری " . $team_name->name . " اکونت نمبر " . $team_name->id . " به مبلغ " . $request->amount . $currency;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد و در سیستم مالی درج گردید!');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinishingTeamPayment  $finishingTeamPayment
     * @return \Illuminate\Http\Response
     */
    public function show($team_id)
    {
        $payments = FinishingTeamPayment::where('team_id',$team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = FinishingTeam::find($team_id);
        $debits_us = FinishingTeamPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = FinishingTeamPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = FinishingTeamPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = FinishingTeamPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $finish_numbers = FinishingWork::where('team_id','=',$team_id)->distinct()->get(['finish_number']);
        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','finish_numbers'));
    }
    public function show_all_payment($team_id){
        $payments = FinishingTeamPayment::where('team_id',$team_id)->orderBy('created_at','DESC')->get();
        $team = FinishingTeam::find($team_id);
        $debits_us = FinishingTeamPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = FinishingTeamPayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = FinishingTeamPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = FinishingTeamPayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $finish_numbers = FinishingWork::where('team_id','=',$team_id)->distinct()->get(['finish_number']);
        $all = '';
        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','finish_numbers','all'));

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FinishingTeamPayment  $finishingTeamPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = FinishingTeamPayment::find($payment_id);
        $payments = FinishingTeamPayment::where('team_id',$paymentEdit->team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = FinishingTeam::find($paymentEdit->team_id);
        $debits_us = FinishingTeamPayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $debits_af = FinishingTeamPayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $credit_us = FinishingTeamPayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $credit_af = FinishingTeamPayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $finish_numbers = FinishingWork::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['finish_number']);
        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','finish_numbers'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinishingTeamPayment  $finishingTeamPayment
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

            $payed = FinishingTeamPayment::find($payment_id);
            $team_name = DB::table('finishing_teams')->where('id', $request->team_id)->first();

            // Reverse Old Accounting Entries (Only if approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Finishing Team Payment Edited');
            }

            // Update record
            $payed->team_id = $request->team_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->finish_number = $request->finish_number;

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
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت تیم تیاری " . $team_name->name . " اکونت نمبر " . $team_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/finishing-payments/'.$request->team_id)->with('status', 'ویرایش با موفقیت انجام شد و گزارشات مالی بروز گردید!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinishingTeamPayment  $finishingTeamPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = FinishingTeamPayment::find($id);
            $team_name = DB::table('finishing_teams')->where('id', $payment->team_id)->first();

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Finishing Team Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت تیم تیاری " . $team_name->name . " اکونت نمبر " . $team_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
