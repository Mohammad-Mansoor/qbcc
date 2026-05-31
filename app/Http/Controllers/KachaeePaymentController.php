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
            
            // FORENSIC RULE: Pass original_amount + currency_code so AccountingService
            // performs the USD conversion exactly once (base_amount is already converted;
            // passing it with a non-USD currency_code causes a double-conversion).
            $amount = $payment->original_amount;

            $this->accountingService->postAutoTransaction('kachaee_payment', $mKey, array_merge([
                'date' => $payment->date,
                'amount' => $amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\Kachaee',
                'party_id' => $payment->team_id,
                'reference' => 'KCH-PAY-' . $payment->id,
                'description' => "پرداخت بخش کچایی: " . $payment->description,
                'source_id' => $payment->id,
                'source_type' => 'App\KachaeePayment',
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
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'type' => 'required',
                'team_id' => 'required',
            ]);

            $currency = \App\Currency::find($request->currency_id);
            $rate = $request->exchange_rate ?: $currency->exchange_rate;

            // FORENSIC RULE: BCMath Calculation (base_amount = original * rate)
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed = new KachaeePayment();
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            
            // Legacy Support
            $payed->dollar_rate = (string)$rate;
            $payed->kachaee_number = $request->kachaee_number;
            
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

            $team = Kachaee::find($request->team_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " تراکنش کچایی: " . $team->name . " مبلغ " . $request->amount . " " . $currency->code . " " . $request->type . " ثبت شد ";
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
    public function show_all_payment($team_id)
    {
        $team = Kachaee::find($team_id);
        if (!$team) {
            return redirect('/dashboard/kachaee-team')->with('error', 'تیم کچایی یافت نشد (Team not found).');
        }

        $payments = KachaeePayment::where('team_id',$team_id)->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = KachaeePayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = KachaeePayment::where('team_id', $team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = KachaeePayment::where('team_id', $team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $kachaee_numbers = CarpetRepair::where('team_id','=',$team_id)->distinct()->get(['kachaee_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit')
            ->merge($selectionService->getValidAccounts('PYMT_IN', 'debit'))
            ->unique('id');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit')
            ->merge($selectionService->getValidAccounts('PYMT_IN', 'credit'))
            ->unique('id');
        $mappingIn = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();
        $all = 'true';

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'all'));
    }

    public function show($team_id)
    {
        $team = Kachaee::find($team_id);
        if (!$team) {
            return redirect('/dashboard/kachaee-team')->with('error', 'تیم کچایی یافت نشد (Team not found).');
        }

        $payments = KachaeePayment::where('team_id',$team_id)->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = KachaeePayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = KachaeePayment::where('team_id', $team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = KachaeePayment::where('team_id', $team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $kachaee_numbers = CarpetRepair::where('team_id','=',$team_id)->distinct()->get(['kachaee_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit')
            ->merge($selectionService->getValidAccounts('PYMT_IN', 'debit'))
            ->unique('id');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit')
            ->merge($selectionService->getValidAccounts('PYMT_IN', 'credit'))
            ->unique('id');
        $mappingIn = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies'));
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
        $team = Kachaee::find($paymentEdit->team_id);
        $payments = KachaeePayment::where('team_id',$paymentEdit->team_id)->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = KachaeePayment::where('team_id', $paymentEdit->team_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = KachaeePayment::where('team_id', $paymentEdit->team_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = KachaeePayment::where('team_id', $paymentEdit->team_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $kachaee_numbers = CarpetRepair::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['kachaee_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit')
            ->merge($selectionService->getValidAccounts('PYMT_IN', 'debit'))
            ->unique('id');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit')
            ->merge($selectionService->getValidAccounts('PYMT_IN', 'credit'))
            ->unique('id');
        $mappingIn = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        // Retrieve existing transaction accounts to auto-select in edit view
        $transaction = \App\LedgerTransaction::with('entries')
            ->where('source_id', $paymentEdit->id)
            ->where('source_type', 'App\KachaeePayment')
            ->where('status', 'posted')
            ->first();
        
        $currentDebitAccountId = null;
        $currentCreditAccountId = null;
        if ($transaction) {
            foreach ($transaction->entries as $entry) {
                if ($entry->debit > 0) $currentDebitAccountId = $entry->account_id;
                if ($entry->credit > 0) $currentCreditAccountId = $entry->account_id;
            }
        }

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'currentDebitAccountId', 'currentCreditAccountId'));
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
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'type' => 'required',
                'team_id' => 'required',
            ]);

            $payed = KachaeePayment::find($payment_id);

            // Reversal - pass class name to avoid ID collision reversals with other models
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Kachaee Record Edited', get_class($payed));
            }

            $currency = \App\Currency::find($request->currency_id);
            $rate = $request->exchange_rate ?: $currency->exchange_rate;
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->team_id = $request->team_id;
            $payed->dollar_rate = (string)$rate;
            $payed->kachaee_number = $request->kachaee_number;

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

            // Reversal - pass class name to avoid ID collision reversals with other models
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Kachaee Record Deleted', get_class($payment));
            }

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
