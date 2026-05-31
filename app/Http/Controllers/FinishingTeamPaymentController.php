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

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $mKey = ($payment->type == 'گرفت') ? 'PYMT_OUT' : 'PYMT_IN';
            
            // FORENSIC RULE: Pass original_amount + currency_code so AccountingService
            // performs the USD conversion exactly once (base_amount is already converted,
            // passing it with a non-USD currency_code causes a double-conversion).
            $amount = $payment->original_amount;

            $this->accountingService->postAutoTransaction('finishing_payment', $mKey, array_merge([
                'date' => $payment->date,
                'amount' => $amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\FinishingTeam',
                'party_id' => $payment->team_id,
                'reference' => 'F-PAY-' . $payment->id,
                'description' => "پرداخت بخش تیاری: " . $payment->description,
                'source_id' => $payment->id,
            ], $overrides));
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
        $payment = FinishingTeamPayment::find($id);
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

            $payed = new FinishingTeamPayment();
            $payed->team_id = $request->team_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = (string)$rate;
            $payed->finish_number = $request->finish_number ?: 'General';

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

            $team_name = DB::table('finishing_teams')->where('id', $request->team_id)->first();
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "پرداخت به تیم تیاری " . $team_name->name . " مبلغ " . $request->amount . " " . $currency->code . " " . $request->type;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد و در سیستم مالی درج گردید!');
        });
    }

    public function show($team_id)
    {
        $team = FinishingTeam::find($team_id);
        if (!$team) {
            return redirect('/dashboard/finishing-team')->with('error', 'تیم تیاری یافت نشد (Team not found).');
        }

        $payments = FinishingTeamPayment::where('team_id',$team_id)->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = FinishingTeamPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = FinishingTeamPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = FinishingTeamPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $finish_numbers = FinishingWork::where('team_id','=',$team_id)->distinct()->get(['finish_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('finishing-center.finishing-payment',compact(
            'team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent',
            'finish_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'
        ));
    }

    public function show_all_payment($team_id)
    {
        $team = FinishingTeam::find($team_id);
        if (!$team) {
            return redirect('/dashboard/finishing-team')->with('error', 'تیم تیاری یافت نشد (Team not found).');
        }

        $payments = FinishingTeamPayment::where('team_id',$team_id)->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = FinishingTeamPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = FinishingTeamPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = FinishingTeamPayment::where('team_id', $team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $finish_numbers = FinishingWork::where('team_id','=',$team_id)->distinct()->get(['finish_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();
        $all = 'true';

        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','finish_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
    }

    public function edit($payment_id)
    {
        $paymentEdit = FinishingTeamPayment::find($payment_id);
        $team = FinishingTeam::find($paymentEdit->team_id);
        $payments = FinishingTeamPayment::where('team_id',$paymentEdit->team_id)->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = FinishingTeamPayment::where('team_id', $paymentEdit->team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = FinishingTeamPayment::where('team_id', $paymentEdit->team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = FinishingTeamPayment::where('team_id', $paymentEdit->team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $finish_numbers = FinishingWork::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['finish_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent', 'finish_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
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

            $payed = FinishingTeamPayment::find($payment_id);

            // Reversal - pass class name to avoid ID collision reversals with other models
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Finishing Record Edited', 'Finishing_payment');
            }

            $currency = \App\Currency::find($request->currency_id);
            $rate = $request->exchange_rate ?: $currency->exchange_rate;
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = (string)$rate;
            $payed->finish_number = $request->finish_number ?: 'General';

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

            $team_name = DB::table('finishing_teams')->where('id', $request->team_id)->first();
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش تراکنش تیم تیاری " . $team_name->name . " مبلغ " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/finishing-payments/'.$request->team_id)->with('status', 'بروزرسانی با موفقیت انجام شد!');
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

            // Reverse Accounting Entry (Only if approved) - pass class name to avoid ID collision reversals with other models
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Finishing Team Payment Deleted', 'Finishing_payment');
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
