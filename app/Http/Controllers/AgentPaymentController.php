<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AgentPayment;
use App\AgentPhone;
use App\Agents;
use App\Services\AccountingService;
use App\CarpetCheckBook;
use App\MaterialSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentPaymentController extends Controller
{
    protected $accountingService;
    
    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        return redirect('/dashboard/agents')->with('error', 'لطفاً یک نماینده را انتخاب کنید (Please select an agent).');
    }

    private function postPaymentToAccounting($payment)
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'

            // FORENSIC RULE: Pass original_amount + currency_code so AccountingService
            // performs the USD conversion exactly once (base_amount is already converted;
            // passing it with a non-USD currency_code causes a double-conversion).
            //
            // Legacy fallback for old records that pre-date the forensic snapshot columns:
            //   - If original_amount exists  → use it (native amount in original currency)
            //   - Else if amount > 0         → use amount as native USD
            //   - Else                       → use amount_af as native AFN
            // The currency_code and exchange_rate fallbacks follow the same priority.
            if ($payment->original_amount) {
                $amount       = $payment->original_amount;
                $currencyCode = $payment->currency_code ?: ($payment->amount > 0 ? 'USD' : 'AFN');
                $exchangeRate = $payment->exchange_rate ?: ($payment->dollar_rate ?: 1);
            } elseif ($payment->amount > 0) {
                // Legacy USD record — amount column stores native USD, no conversion needed
                $amount       = $payment->amount;
                $currencyCode = 'USD';
                $exchangeRate = 1;
            } else {
                // Legacy AFN record — amount_af stores native AFN
                $amount       = $payment->amount_af;
                $currencyCode = 'AFN';
                $exchangeRate = $payment->dollar_rate ?: 1;
            }

            $this->accountingService->postAutoTransaction('agent_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'currency_code' => $currencyCode,
                'exchange_rate' => $exchangeRate,
                'party_type' => 'App\Agents',
                'party_id' => $payment->agent_id,
                'reference' => 'AGT-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Agent Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function money_request()
    {
        $requests = AgentPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('agents.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = AgentPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " تایید درخواست " . $payment->type . " مبلغ " . ($payment->original_amount ?: ($payment->amount ?: $payment->amount_af)) . " " . ($payment->currency_code ?: ($payment->amount > 0 ? 'USD' : 'AFN')) . " برای نماینده ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id){
        $credit = AgentPayment::find($id);
        $credit->delete();
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
                'agent_id' => 'required',
            ]);

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;

            // FORENSIC RULE: BCMath Calculation (base_amount = original * rate)
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed = new AgentPayment();
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            
            // Legacy Support (Store in dollar_rate for audit continuity)
            $payed->dollar_rate = (string)$rate;
            $payed->check_number = $request->check_number;
            
            // FORENSIC SNAPSHOTS
            $payed->currency_code = $currency->code;
            $payed->currency_symbol = $currency->symbol;
            $payed->exchange_rate = $rate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = $baseAmount;

            // Legacy dual-amount logic (for old reports compatibility)
            if($currency->code == 'USD'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else if($currency->code == 'AFN') {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            } else {
                $payed->amount = $baseAmount; // Approximate USD for legacy columns
                $payed->amount_af = 0;
            }

            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت به نماینده " . $agent_name->name . " اکونت نمبر " . $agent_name->account_no . " به مبلغ " . $request->amount . " " . $currency->code;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'پرداخت نماینده موفقانه ثبت و در سیستم مالی درج گردید!');
        });
    }

    public function show($agent_id)
    {
        $agent = Agents::find($agent_id);
        
        if (!$agent) {
            return redirect('/dashboard/agents')->with('error', 'نماینده مورد نظر یافت نشد (Agent not found).');
        }

        $payments = AgentPayment::where('agent_id', $agent_id)->orderBy('date', 'DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = AgentPayment::where('agent_id', $agent_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $check_numbers = CarpetCheckBook::where('agent_id', '=', $agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('agents.agent-payments', compact('agent', 'payments', 'paymentEdit', 'currencyTotals', 'totalBaseReceived', 'totalBaseSent', 'check_numbers', 'sale_numbers', 'currencies'));
    }

    public function show_all($agent_id)
    {
        $payments = AgentPayment::where('agent_id', $agent_id)->orderBy('date', 'DESC')->get();
        $agent = Agents::find($agent_id);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = AgentPayment::where('agent_id', $agent_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $check_numbers = CarpetCheckBook::where('agent_id', '=', $agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        $all = '';
        return view('agents.agent-payments', compact('agent', 'payments', 'paymentEdit', 'currencyTotals', 'totalBaseReceived', 'totalBaseSent', 'check_numbers', 'all', 'sale_numbers', 'currencies'));
    }

    public function edit($payment_id)
    {
        $paymentEdit = AgentPayment::find($payment_id);
        $payments = AgentPayment::where('agent_id', $paymentEdit->agent_id)->orderBy('date', 'DESC')->paginate(30);
        $agent = Agents::find($paymentEdit->agent_id);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = AgentPayment::where('agent_id', $paymentEdit->agent_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = AgentPayment::where('agent_id', $paymentEdit->agent_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = AgentPayment::where('agent_id', $paymentEdit->agent_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $check_numbers = CarpetCheckBook::where('agent_id', '=', $paymentEdit->agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $paymentEdit->agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('agents.agent-payments', compact('agent', 'payments', 'paymentEdit', 'currencyTotals', 'totalBaseReceived', 'totalBaseSent', 'check_numbers', 'sale_numbers', 'currencies'));
    }

    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
            ]);

            $payed = AgentPayment::find($payment_id);
            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();

            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Agent Payment Edited');
            }

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = (string)$rate;
            $payed->check_number = $request->check_number;

            // FORENSIC SNAPSHOTS
            $payed->currency_code = $currency->code;
            $payed->currency_symbol = $currency->symbol;
            $payed->exchange_rate = $rate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = $baseAmount;

            // Legacy Support
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

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت نماینده " . $agent_name->name . " اکونت نمبر " . $agent_name->account_no;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/agent-payments/' . $request->agent_id)->with('status', 'ویرایش موفقانه انجام شد و حسابات مالی نماینده بروز گردید!');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = AgentPayment::find($id);
            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $payment->agent_id)->first();

            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Agent Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت نماینده " . $agent_name->name . " اکونت نمبر " . $agent_name->account_no;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
