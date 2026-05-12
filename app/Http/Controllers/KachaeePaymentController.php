<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetRepair;
use App\Kachaee;
use App\KachaeePayment;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KachaeePaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $mKey = ($payment->type == 'گرفت') ? 'PYMT_OUT' : 'PYMT_IN';
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;
            $rate = $payment->dollar_rate ?? 1;

            $this->accountingService->postAutoTransaction('payment', $mKey, array_merge([
                'date' => $payment->date,
                'amount' => $amount,
                'exchange_rate' => $rate,
                'party_type' => 'App\Kachaee',
                'party_id' => $payment->team_id,
                'reference' => 'KCH-' . $payment->id,
                'description' => "پرداخت بخش کچایی: " . $payment->description,
                'source_id' => $payment->id,
            ], $overrides));
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Kachaee Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function money_request()
    {
        $requests = KachaeePayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('kachaee.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = KachaeePayment::find($id);
            $payment->status = 1;
            $payment->update();

            // Accounting Posting
            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $currency = ($payment->amount > 0) ? "دالر" : "افغانی";
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;
            $activity->description = " مبلغ " . $amount . " " . $currency . " توسط سوپر ادمین تایید و در سیستم مالی ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $payment = KachaeePayment::find($id);
        $payment->delete();
        return response()->json(['status', 'error']);
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
                'type' => 'required',
                'team_id' => 'required',
            ]);

            $payed = new KachaeePayment();
            if ($request->money_type == 'دالر') {
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->kachaee_number = $request->kachaee_number;
            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->save();

            if ($payed->status == 1) {
                $overrides = [];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;
                $this->postPaymentToAccounting($payed, $overrides);
            }

            $team = Kachaee::find($request->team_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " تراکنش کچایی: " . $team->name . " مبلغ " . $request->amount . " " . $request->money_type . " " . $request->type . " ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'تراکنش با موفقیت ثبت شد!');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function show($team_id)
    {
        $payments = KachaeePayment::where('team_id',$team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = Kachaee::find($team_id);
        $debits_us = KachaeePayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $debits_af = KachaeePayment::where('type','=','گرفت')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $credit_us = KachaeePayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount');
        $credit_af = KachaeePayment::where('type','=','رسید')->where('team_id',$team_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $kachaee_numbers = CarpetRepair::where('team_id','=',$team_id)->distinct()->get(['kachaee_number']);
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = KachaeePayment::find($payment_id);
        $payments = KachaeePayment::where('team_id',$paymentEdit->team_id)->orderBy('created_at','DESC')->paginate(30);
        $team = Kachaee::find($paymentEdit->team_id);
        $debits_us = KachaeePayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $debits_af = KachaeePayment::where('type','=','گرفت')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $credit_us = KachaeePayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount');
        $credit_af = KachaeePayment::where('type','=','رسید')->where('team_id',$paymentEdit->team_id)->where('status',1)->sum('amount_af');
        $kachaee_numbers = CarpetRepair::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['kachaee_number']);
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'type' => 'required',
                'team_id' => 'required',
            ]);

            $payed = KachaeePayment::find($payment_id);

            // Reversal
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Kachaee Record Edited');
            }

            if ($request->money_type == 'دالر') {
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->kachaee_number = $request->kachaee_number;
            $payed->update();

            // Re-post
            if ($payed->status == 1) {
                $overrides = [];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;
                $this->postPaymentToAccounting($payed, $overrides);
            }

            $team = Kachaee::find($request->team_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " ویرایش تراکنش کچایی: " . $team->name . " مبلغ " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/kachaee-payments/'.$request->team_id)->with('status', 'بروزرسانی با موفقیت انجام شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KachaeePayment  $kachaeePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = KachaeePayment::find($id);

            // Reversal
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Kachaee Record Deleted');
            }

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
