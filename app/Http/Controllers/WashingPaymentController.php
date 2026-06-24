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
        
        $this->middleware('permission:view_washing_money_requests')->only(['money_request']);
        $this->middleware('permission:approve_washing_money_requests')->only(['approve_request']);
        $this->middleware('permission:reject_washing_money_requests')->only(['delete_request']);
        $this->middleware('permission:manage_washing_payments')->only(['store', 'show', 'show_all_payment', 'edit', 'update', 'destroy', 'allocateAdvance', 'unallocateAdvance']);
    }

    public function index()
    {
        //
    }

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $mKey = ($payment->type == 'گرفت') ? 'PYMT_OUT' : 'PYMT_IN';
            if ($payment->is_advance) {
                $mKey = ($payment->type == 'گرفت') ? 'WASH_ADVANCE_OUT' : 'WASH_ADVANCE_IN';
            }
            
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
            
            // Advance logic
            $isAdvance = $request->has('is_advance') && $request->is_advance;
            $payed->is_advance = $isAdvance ? 1 : 0;
            if ($isAdvance) {
                $payed->payment_status = 'unallocated';
                $payed->remaining_unallocated_amount = $request->amount;
                $payed->wash_number = 'General'; // Force ref number to 'General' for advances
            } else {
                $payed->payment_status = 'unallocated';
                $payed->remaining_unallocated_amount = 0.0000;
                $payed->wash_number = $request->wash_number ?: 'General';
            }

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

        $payments = WashingPayment::where('team_id',$team_id)->where('wash_number', 'General')->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->where('type', 'رسید')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $totalBaseSent = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->where('type', 'گرفت')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $paymentEdit = '';
        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);
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

        // Fetch Completed Washes & Match with Specific Payments
        $washes = \App\CarpetWash::where('team_id', $team_id)
            ->with('carpet')
            ->where('total_price', '>', 0)
            ->orderBy('date', 'DESC')
            ->get();

        $paymentsByRef = \App\WashingPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('wash_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
            )
            ->groupBy('wash_number')
            ->get()
            ->keyBy('wash_number');

        $batchRefs = $washes->pluck('wash_number_sh')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('washing_payment_allocations')
            ->join('production_batches', 'washing_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('washing_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        // Map each wash with its paid/remaining metrics
        $groupedWashes = [];
        foreach ($washes as $w) {
            $refPayments = $paymentsByRef->get($w->wash_number_sh);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($w->wash_number_sh) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $w->total_cost = (float)($w->total_price ?: $w->af_total_price);
            $w->total_paid = (float)$totalPaid;
            $w->remaining_balance = max(0, $w->total_cost - $w->total_paid);
            
            if ($w->total_paid == 0) {
                $w->payment_status = 'unpaid';
            } elseif ($w->remaining_balance <= 0) {
                $w->payment_status = 'paid';
            } else {
                $w->payment_status = 'partial';
            }

            $ref = $w->wash_number_sh ?: 'General';
            if (!isset($groupedWashes[$ref])) {
                $groupedWashes[$ref] = [
                    'reference' => $ref,
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $w->date,
                ];
            }
            $groupedWashes[$ref]['total_carpets']++;
            $groupedWashes[$ref]['total_cost'] += $w->total_cost;
        }

        foreach ($groupedWashes as $ref => &$group) {
            $group['remaining_balance'] = max(0.0, $group['total_cost'] - $group['total_paid']);
            if ($group['total_paid'] == 0) {
                $group['payment_status'] = 'unpaid';
            } elseif ($group['remaining_balance'] <= 0) {
                $group['payment_status'] = 'paid';
            } else {
                $group['payment_status'] = 'partial';
            }
        }
        unset($group);

        $totalBaseWashes = \App\CarpetWash::where('team_id', $team_id)->sum('base_currency_amount');

        // Unified Ledger Audit Statement
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')
            ->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->select(
                'ledger_transactions.date',
                'ledger_transactions.description',
                'ledger_transactions.reference',
                'ledger_entries.debit',
                'ledger_entries.credit',
                'ledger_entries.currency_code',
                'ledger_entries.base_debit',
                'ledger_entries.base_credit'
            )
            ->orderBy('ledger_transactions.date', 'ASC')
            ->orderBy('ledger_transactions.id', 'ASC')
            ->get();

        return view('washing.washing-payment',compact(
            'team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent',
            'wash_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies',
            'washes', 'groupedWashes', 'totalBaseWashes', 'ledgerStatement'
        ));
    }

    public function show_all_payment($team_id)
    {
        $team = WashingTeam::find($team_id);
        if (!$team) {
            return redirect('/dashboard/washing-team')->with('error', 'تیم شست‌وشو یافت نشد (Team not found).');
        }

        $payments = WashingPayment::where('team_id',$team_id)->where('wash_number', 'General')->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->where('type', 'رسید')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $totalBaseSent = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->where('type', 'گرفت')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $paymentEdit = '';
        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);
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

        // Fetch Completed Washes & Match with Specific Payments
        $washes = \App\CarpetWash::where('team_id', $team_id)
            ->with('carpet')
            ->where('total_price', '>', 0)
            ->orderBy('date', 'DESC')
            ->get();

        $paymentsByRef = \App\WashingPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('wash_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
            )
            ->groupBy('wash_number')
            ->get()
            ->keyBy('wash_number');

        $batchRefs = $washes->pluck('wash_number_sh')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('washing_payment_allocations')
            ->join('production_batches', 'washing_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('washing_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        // Map each wash with its paid/remaining metrics
        $groupedWashes = [];
        foreach ($washes as $w) {
            $refPayments = $paymentsByRef->get($w->wash_number_sh);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($w->wash_number_sh) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $w->total_cost = (float)($w->total_price ?: $w->af_total_price);
            $w->total_paid = (float)$totalPaid;
            $w->remaining_balance = max(0, $w->total_cost - $w->total_paid);
            
            if ($w->total_paid == 0) {
                $w->payment_status = 'unpaid';
            } elseif ($w->remaining_balance <= 0) {
                $w->payment_status = 'paid';
            } else {
                $w->payment_status = 'partial';
            }

            $ref = $w->wash_number_sh ?: 'General';
            if (!isset($groupedWashes[$ref])) {
                $groupedWashes[$ref] = [
                    'reference' => $ref,
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $w->date,
                ];
            }
            $groupedWashes[$ref]['total_carpets']++;
            $groupedWashes[$ref]['total_cost'] += $w->total_cost;
        }

        foreach ($groupedWashes as $ref => &$group) {
            $group['remaining_balance'] = max(0.0, $group['total_cost'] - $group['total_paid']);
            if ($group['total_paid'] == 0) {
                $group['payment_status'] = 'unpaid';
            } elseif ($group['remaining_balance'] <= 0) {
                $group['payment_status'] = 'paid';
            } else {
                $group['payment_status'] = 'partial';
            }
        }
        unset($group);

        $totalBaseWashes = \App\CarpetWash::where('team_id', $team_id)->sum('base_currency_amount');

        // Unified Ledger Audit Statement
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')
            ->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->select(
                'ledger_transactions.date',
                'ledger_transactions.description',
                'ledger_transactions.reference',
                'ledger_entries.debit',
                'ledger_entries.credit',
                'ledger_entries.currency_code',
                'ledger_entries.base_debit',
                'ledger_entries.base_credit'
            )
            ->orderBy('ledger_transactions.date', 'ASC')
            ->orderBy('ledger_transactions.id', 'ASC')
            ->get();

        return view('washing.washing-payment',compact(
            'team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent',
            'wash_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies',
            'washes', 'groupedWashes', 'totalBaseWashes', 'ledgerStatement'
        ));
    }

    public function edit($payment_id)
    {
        $paymentEdit = WashingPayment::find($payment_id);
        $team = WashingTeam::find($paymentEdit->team_id);
        $team_id = $paymentEdit->team_id;
        $payments = WashingPayment::where('team_id',$team_id)->where('wash_number', 'General')->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->where('type', 'رسید')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $totalBaseSent = WashingPayment::where('team_id', $team_id)
            ->where('wash_number', 'General')
            ->where('status', 1)
            ->where('type', 'گرفت')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $wash_numbers = CarpetWash::where('team_id','=',$team_id)->distinct()->get(['wash_number_sh']);
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

        // Fetch Completed Washes & Match with Specific Payments
        $washes = \App\CarpetWash::where('team_id', $team_id)
            ->with('carpet')
            ->where('total_price', '>', 0)
            ->orderBy('date', 'DESC')
            ->get();

        $paymentsByRef = \App\WashingPayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('wash_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
            )
            ->groupBy('wash_number')
            ->get()
            ->keyBy('wash_number');

        $batchRefs = $washes->pluck('wash_number_sh')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('washing_payment_allocations')
            ->join('production_batches', 'washing_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('washing_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        // Map each wash with its paid/remaining metrics
        $groupedWashes = [];
        foreach ($washes as $w) {
            $refPayments = $paymentsByRef->get($w->wash_number_sh);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($w->wash_number_sh) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $w->total_cost = (float)($w->total_price ?: $w->af_total_price);
            $w->total_paid = (float)$totalPaid;
            $w->remaining_balance = max(0, $w->total_cost - $w->total_paid);
            
            if ($w->total_paid == 0) {
                $w->payment_status = 'unpaid';
            } elseif ($w->remaining_balance <= 0) {
                $w->payment_status = 'paid';
            } else {
                $w->payment_status = 'partial';
            }

            $ref = $w->wash_number_sh ?: 'General';
            if (!isset($groupedWashes[$ref])) {
                $groupedWashes[$ref] = [
                    'reference' => $ref,
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $w->date,
                ];
            }
            $groupedWashes[$ref]['total_carpets']++;
            $groupedWashes[$ref]['total_cost'] += $w->total_cost;
        }

        foreach ($groupedWashes as $ref => &$group) {
            $group['remaining_balance'] = max(0.0, $group['total_cost'] - $group['total_paid']);
            if ($group['total_paid'] == 0) {
                $group['payment_status'] = 'unpaid';
            } elseif ($group['remaining_balance'] <= 0) {
                $group['payment_status'] = 'paid';
            } else {
                $group['payment_status'] = 'partial';
            }
        }
        unset($group);

        $totalBaseWashes = \App\CarpetWash::where('team_id', $team_id)->sum('base_currency_amount');

        // Unified Ledger Audit Statement
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')
            ->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->select(
                'ledger_transactions.date',
                'ledger_transactions.description',
                'ledger_transactions.reference',
                'ledger_entries.debit',
                'ledger_entries.credit',
                'ledger_entries.currency_code',
                'ledger_entries.base_debit',
                'ledger_entries.base_credit'
            )
            ->orderBy('ledger_transactions.date', 'ASC')
            ->orderBy('ledger_transactions.id', 'ASC')
            ->get();

        return view('washing.washing-payment',compact(
            'team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent',
            'wash_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies',
            'washes', 'groupedWashes', 'totalBaseWashes', 'ledgerStatement'
        ));
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

            // Advance logic
            $isAdvance = $request->has('is_advance') && $request->is_advance;
            $payed->is_advance = $isAdvance ? 1 : 0;
            if ($isAdvance) {
                $payed->wash_number = 'General';
                // Adjust remaining unallocated if not allocated yet
                if ($payed->payment_status == 'unallocated') {
                    $payed->remaining_unallocated_amount = $request->amount;
                }
            } else {
                $payed->remaining_unallocated_amount = 0.0000;
                $payed->payment_status = 'unallocated';
                $payed->wash_number = $request->wash_number ?: 'General';
            }

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

            // Reverse allocations if any exist
            if ($payment->allocations) {
                foreach ($payment->allocations as $alloc) {
                    $this->accountingService->reverseTransactionBySource($alloc->id, 'Parent Washing Payment Deleted', get_class($alloc));
                }
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

    public function allocateAdvance(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'washing_payment_id' => 'required|exists:washing_payments,id',
                'allocatable_id' => 'required|integer',
                'allocatable_type' => 'required|string|in:App\ProductionBatch',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $payment = WashingPayment::where('id', $request->washing_payment_id)->lockForUpdate()->firstOrFail();
            $modelClass = $request->allocatable_type;
            $document = $modelClass::where('id', $request->allocatable_id)->lockForUpdate()->firstOrFail();

            // Check if date is locked
            $this->accountingService->failIfLocked($payment->date);
            $this->accountingService->failIfLocked($document->date ?? now()->format('Y-m-d'));

            // Validation logic
            $availableAdvance = $payment->remaining_unallocated_amount;
            $remainingBill = $document->remaining_balance;
            $baseAllocated = bcmul($request->amount, $payment->exchange_rate, 4);

            if (bccomp($request->amount, $availableAdvance, 4) > 0) {
                return response()->json(['status' => 'error', 'message' => "مقدار تخصیص (" . number_format($request->amount, 2) . " " . $payment->currency_code . ") بیش از موجودی علی‌الحساب (" . number_format($availableAdvance, 2) . " " . $payment->currency_code . ") است."]);
            }
            if (bccomp($baseAllocated, $remainingBill, 4) > 0) {
                return response()->json(['status' => 'error', 'message' => "مقدار تخصیص معادل دالر ($" . number_format($baseAllocated, 2) . ") بیش از باقیمانده بل ($" . number_format($remainingBill, 2) . ") است."]);
            }

            // Create allocation record
            $allocation = \App\WashingPaymentAllocation::create([
                'washing_payment_id' => $payment->id,
                'allocatable_type' => $request->allocatable_type,
                'allocatable_id' => $document->id,
                'allocated_amount' => $request->amount,
                'exchange_rate' => $payment->exchange_rate,
                'base_allocated_amount' => $baseAllocated,
            ]);

            // Update parent statuses
            $payment->remaining_unallocated_amount = bcsub($payment->remaining_unallocated_amount, $request->amount, 4);
            $payment->payment_status = $payment->remaining_unallocated_amount <= 0.01 ? 'allocated' : 'partially_allocated';
            $payment->save();

            // Post accounting entry for settlement
            $this->accountingService->postAutoTransaction('washing_advance_settlement', 'ADVANCE_SETTLEMENT', [
                'date' => now()->format('Y-m-d'),
                'amount' => $request->amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\WashingTeam',
                'party_id' => $payment->team_id,
                'reference' => $document->reference_number,
                'description' => "تصفیه بل شست‌وشو " . $document->reference_number . " از پیش‌پرداخت شماره " . $payment->id,
                'source_id' => $allocation->id,
                'source_type' => 'App\WashingPaymentAllocation',
            ]);

            // Log activity
            $activity = new Activity();
            $activity->date = now()->format('Y-m-d');
            $activity->description = "تخصیص پیش‌پرداخت به مبلغ " . $request->amount . " " . $payment->currency_code . " از پیش‌پرداخت شماره " . $payment->id . " به بل شست‌وشو " . $document->reference_number;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success', 'message' => 'تخصیص موفقانه انجام شد!']);
        });
    }

    public function removeAllocation($id)
    {
        return DB::transaction(function () use ($id) {
            $allocation = \App\WashingPaymentAllocation::lockForUpdate()->findOrFail($id);
            $payment = $allocation->payment;
            $doc = $allocation->allocatable;

            // Check if date is locked
            $this->accountingService->failIfLocked($payment->date);
            if ($doc) {
                $this->accountingService->failIfLocked($doc->date ?? now()->format('Y-m-d'));
            }

            // Reverse the accounting transaction
            $this->accountingService->reverseTransactionBySource($allocation->id, 'Washing Allocation Deleted', 'App\WashingPaymentAllocation');

            // Restore the payment's unallocated amount
            if ($payment->is_advance) {
                $payment->remaining_unallocated_amount = bcadd($payment->remaining_unallocated_amount, $allocation->allocated_amount, 4);
                $payment->payment_status = $payment->remaining_unallocated_amount >= $payment->original_amount - 0.01 ? 'unallocated' : 'partially_allocated';
                $payment->save();
            }

            // Delete the allocation record
            $allocation->delete();

            // Log activity
            $activity = new Activity();
            $activity->date = now()->format('Y-m-d');
            $activity->description = "حذف تخصیص پیش‌پرداخت به مبلغ " . $allocation->allocated_amount . " از پیش‌پرداخت شماره " . $payment->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success', 'message' => 'تخصیص موفقانه حذف گردید!']);
        });
    }
}
