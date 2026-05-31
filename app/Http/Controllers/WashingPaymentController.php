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
            
            // FORENSIC RULE: Pass original_amount + currency_code so AccountingService
            // performs the USD conversion exactly once (base_amount is already converted,
            // passing it with a non-USD currency_code causes a double-conversion).
            $amount = $payment->original_amount;

            $this->accountingService->postAutoTransaction('washing_payment', $mKey, array_merge([
                'date' => $payment->date,
                'amount' => $amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\WashingTeam',
                'party_id' => $payment->team_id,
                'reference' => 'W-PAY-' . $payment->id,
                'description' => "پرداخت بخش شست‌وشو: " . $payment->description,
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
        $payment = WashingPayment::find($id);
        $payment->delete();
        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'team_id' => 'required',
            ]);

            $currency = \App\Currency::find($request->currency_id);
            $rate = $request->exchange_rate ?: $currency->exchange_rate;

            // FORENSIC RULE: BCMath Calculation
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed = new WashingPayment();
            $payed->team_id = $request->team_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = (string)$rate;
            $payed->wash_number = $request->wash_number ?: 'General';

            // FORENSIC SNAPSHOTS
            $payed->currency_code = $currency->code;
            $payed->currency_symbol = $currency->symbol;
            $payed->exchange_rate = $rate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = $baseAmount;

            // Legacy dual-amount logic
            if($currency->code == 'USD'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else if($currency->code == 'AFN') {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            } else {
                $payed->amount = $baseAmount;
                $payed->amount_af = 0;
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
            $activity->description = "پرداخت شست‌گر " . $team_name->name . " مبلغ " . $request->amount . " " . $currency->code . " " . $request->type;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد و در سیستم مالی درج گردید!');
        });
    }

    public function show($team_id)
    {
        $team = WashingTeam::find($team_id);
        if (!$team) {
            return redirect('/dashboard/washing-team')->with('error', 'تیم شست‌وشو یافت نشد (Team not found).');
        }

        $payments = WashingPayment::where('team_id',$team_id)->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = WashingPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = WashingPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = WashingPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('washing.washing-payment',compact(
            'team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent',
            'wash_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'
        ));
    }

    public function show_all_payment($team_id)
    {
        $team = WashingTeam::find($team_id);
        if (!$team) {
            return redirect('/dashboard/washing-team')->with('error', 'تیم شست‌وشو یافت نشد (Team not found).');
        }

        $payments = WashingPayment::where('team_id',$team_id)->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = WashingPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = WashingPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = WashingPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();
        $all = 'true';

        return view('washing.washing-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','wash_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
    }

    public function edit($payment_id)
    {
        $paymentEdit = WashingPayment::find($payment_id);
        $team = WashingTeam::find($paymentEdit->team_id);
        $payments = WashingPayment::where('team_id',$paymentEdit->team_id)->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = WashingPayment::where('team_id', $paymentEdit->team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = WashingPayment::where('team_id', $paymentEdit->team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = WashingPayment::where('team_id', $paymentEdit->team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $wash_numbers = CarpetWash::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['wash_number_sh']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('washing.washing-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent', 'wash_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
    }

    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'team_id' => 'required',
            ]);

            $payed = WashingPayment::find($payment_id);

            // Reversal - pass class name to avoid ID collision reversals with other models
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Washing Record Edited', get_class($payed));
            }

            $currency = \App\Currency::find($request->currency_id);
            $rate = $request->exchange_rate ?: $currency->exchange_rate;
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = (string)$rate;
            $payed->wash_number = $request->wash_number ?: 'General';

            // FORENSIC SNAPSHOTS
            $payed->currency_code = $currency->code;
            $payed->currency_symbol = $currency->symbol;
            $payed->exchange_rate = $rate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = $baseAmount;

            // Legacy dual-amount logic
            if($currency->code == 'USD'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else if($currency->code == 'AFN') {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            } else {
                $payed->amount = $baseAmount;
                $payed->amount_af = 0;
            }

            $payed->update();

            // Re-post
            if ($payed->status == 1) {
                $overrides = [];
                if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;
                $this->postPaymentToAccounting($payed, $overrides);
            }

            $team_name = DB::table('washing_teams')->where('id', $request->team_id)->first();
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش تراکنش شست‌گر " . $team_name->name . " مبلغ " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/washing-payments/'.$request->team_id)->with('status', 'بروزرسانی با موفقیت انجام شد!');
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

            // Reverse Accounting Entry (Only if approved) - pass class name to avoid ID collision reversals with other models
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Washing Payment Deleted', get_class($payment));
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
