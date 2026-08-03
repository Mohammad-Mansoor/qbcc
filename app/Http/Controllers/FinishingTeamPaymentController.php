<?php

namespace App\Http\Controllers;

use App\Activity;
use App\FinishingTeam;
use App\FinishingTeamPayment;
use App\FinishingPaymentAllocation;
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
        
        $this->middleware('permission:view_finishing_money_requests')->only(['money_request']);
        $this->middleware('permission:approve_finishing_money_requests')->only(['approve_request']);
        $this->middleware('permission:reject_finishing_money_requests')->only(['delete_request']);
        $this->middleware('permission:manage_finishing_payments')->only(['store', 'show', 'show_all_payment', 'edit', 'update', 'destroy', 'allocateAdvance', 'removeAllocation']);
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
                $mKey = ($payment->type == 'گرفت') ? 'FINISH_ADVANCE_OUT' : 'FINISH_ADVANCE_IN';
            }
            
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
                'source_type' => 'App\FinishingTeamPayment',
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
            
            // Advance logic
            $isAdvance = $request->has('is_advance') && $request->is_advance;
            $payed->is_advance = $isAdvance ? 1 : 0;
            if ($isAdvance) {
                $payed->payment_status = 'unallocated';
                $payed->remaining_unallocated_amount = $request->amount;
                $payed->finish_number = 'General'; // Force ref number to 'General' for advances
            } else {
                $payed->payment_status = 'unallocated';
                $payed->remaining_unallocated_amount = 0.0000;
                $payed->finish_number = $request->finish_number ?: 'General';
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

        $payments = FinishingTeamPayment::where('team_id',$team_id)->where('finish_number', 'General')->where('status', '!=', 2)->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = FinishingTeamPayment::where('team_id', $team_id)
            ->where('finish_number', 'General')
            ->where('status', '!=', 2)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = FinishingTeamPayment::where('team_id', $team_id)->where('finish_number', 'General')->where('status', '!=', 2)->where('type', 'رسید')->sum(\DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));
        $totalBaseSent = FinishingTeamPayment::where('team_id', $team_id)->where('finish_number', 'General')->where('status', '!=', 2)->where('type', 'گرفت')->sum(\DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $paymentEdit = '';
        $finish_numbers = FinishingWork::where('team_id','=',$team_id)->distinct()->get(['finish_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mappingIn = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        // Fetch Approved Finishing Jobs
        $finishingWorks = \App\FinishingWork::where('team_id', $team_id)
            ->with(['carpet', 'category'])
            ->where('status', '!=', 2)
            ->orderBy('date', 'DESC')
            ->get();

        $paymentsByRef = \App\FinishingTeamPayment::where('team_id', $team_id)
            ->where('status', '!=', 2)
            ->select('finish_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN base_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN base_amount ELSE 0 END) as total_received")
            )
            ->groupBy('finish_number')
            ->get()
            ->keyBy('finish_number');

        $batchRefs = $finishingWorks->pluck('finish_number')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('finishing_payment_allocations')
            ->join('production_batches', 'finishing_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('finishing_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(base_allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        $groupedFinishingWorks = [];
        foreach ($finishingWorks as $w) {
            $refPayments = $paymentsByRef->get($w->finish_number);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($w->finish_number) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $w->total_cost = (float)$w->price;
            $w->total_paid = (float)$totalPaid;
            
            $ref = $w->finish_number ?: 'General';
            if (!isset($groupedFinishingWorks[$ref])) {
                $groupedFinishingWorks[$ref] = [
                    'reference' => $ref,
                    'unique_carpets' => [],
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $w->date,
                ];
            }
            if (!in_array($w->carpetId, $groupedFinishingWorks[$ref]['unique_carpets'])) {
                $groupedFinishingWorks[$ref]['unique_carpets'][] = $w->carpetId;
                $groupedFinishingWorks[$ref]['total_carpets']++;
            }
            $groupedFinishingWorks[$ref]['total_cost'] += (float)$w->price;
        }

        foreach ($groupedFinishingWorks as $ref => &$group) {
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

        $totalBaseFinishes = \App\FinishingWork::where('team_id', $team_id)->where('status', '!=', 2)->sum('price');

        $finishingAdvances = \App\FinishingTeamPayment::where('team_id', $team_id)->where('is_advance', 1)->where('status', '!=', 2)->orderBy('date', 'DESC')->get();
        $finishingAllocations = \App\FinishingPaymentAllocation::whereHas('payment', function($q) use ($team_id) { $q->where('team_id', $team_id); })->with(['payment', 'allocatable'])->orderBy('created_at', 'DESC')->get();

        return view('finishing-center.finishing-payment',compact(
            'team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent',
            'finish_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies',
            'finishingWorks', 'groupedFinishingWorks', 'totalBaseFinishes',
            'finishingAdvances', 'finishingAllocations'
        ));
    }

    public function show_all_payment($team_id)
    {
        $team = FinishingTeam::find($team_id);
        if (!$team) {
            return redirect('/dashboard/finishing-team')->with('error', 'تیم تیاری یافت نشد (Team not found).');
        }

        $payments = FinishingTeamPayment::where('team_id',$team_id)->where('finish_number', 'General')->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = FinishingTeamPayment::where('team_id', $team_id)
            ->where('finish_number', 'General')
            ->where('status', '!=', 2)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = FinishingTeamPayment::where('team_id', $team_id)->where('finish_number', 'General')->where('status', '!=', 2)->where('type', 'رسید')->sum(\DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));
        $totalBaseSent = FinishingTeamPayment::where('team_id', $team_id)->where('finish_number', 'General')->where('status', '!=', 2)->where('type', 'گرفت')->sum(\DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $paymentEdit = '';
        $finish_numbers = FinishingWork::where('team_id','=',$team_id)->distinct()->get(['finish_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mappingIn = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();
        $all = 'true';

        // Fetch Approved Finishing Jobs
        $finishingWorks = \App\FinishingWork::where('team_id', $team_id)
            ->with(['carpet', 'category'])
            ->where('status', '!=', 2)
            ->orderBy('date', 'DESC')
            ->get();

        $paymentsByRef = \App\FinishingTeamPayment::where('team_id', $team_id)
            ->where('status', '!=', 2)
            ->select('finish_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN base_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN base_amount ELSE 0 END) as total_received")
            )
            ->groupBy('finish_number')
            ->get()
            ->keyBy('finish_number');

        $batchRefs = $finishingWorks->pluck('finish_number')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('finishing_payment_allocations')
            ->join('production_batches', 'finishing_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('finishing_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(base_allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        $groupedFinishingWorks = [];
        foreach ($finishingWorks as $w) {
            $refPayments = $paymentsByRef->get($w->finish_number);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($w->finish_number) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $w->total_cost = (float)$w->price;
            $w->total_paid = (float)$totalPaid;
            
            $ref = $w->finish_number ?: 'General';
            if (!isset($groupedFinishingWorks[$ref])) {
                $groupedFinishingWorks[$ref] = [
                    'reference' => $ref,
                    'unique_carpets' => [],
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $w->date,
                ];
            }
            if (!in_array($w->carpetId, $groupedFinishingWorks[$ref]['unique_carpets'])) {
                $groupedFinishingWorks[$ref]['unique_carpets'][] = $w->carpetId;
                $groupedFinishingWorks[$ref]['total_carpets']++;
            }
            $groupedFinishingWorks[$ref]['total_cost'] += (float)$w->price;
        }

        foreach ($groupedFinishingWorks as $ref => &$group) {
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

        $totalBaseFinishes = \App\FinishingWork::where('team_id', $team_id)->where('status', '!=', 2)->sum('price');

        // Unified Ledger Audit Statement
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
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

        $finishingAdvances = \App\FinishingTeamPayment::where('team_id', $team_id)->where('is_advance', 1)->where('status', '!=', 2)->orderBy('date', 'DESC')->get();
        $finishingAllocations = \App\FinishingPaymentAllocation::whereHas('payment', function($q) use ($team_id) { $q->where('team_id', $team_id); })->with(['payment', 'allocatable'])->orderBy('created_at', 'DESC')->get();

        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','finish_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'finishingWorks', 'groupedFinishingWorks', 'totalBaseFinishes', 'ledgerStatement', 'finishingAdvances', 'finishingAllocations'));
    }

    public function edit($payment_id)
    {
        $paymentEdit = FinishingTeamPayment::find($payment_id);
        $team_id = $paymentEdit->team_id;
        $team = FinishingTeam::find($team_id);
        $payments = FinishingTeamPayment::where('team_id',$paymentEdit->team_id)->where('finish_number', 'General')->where('status', '!=', 2)->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = FinishingTeamPayment::where('team_id', $paymentEdit->team_id)
            ->where('finish_number', 'General')
            ->where('status', '!=', 2)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = FinishingTeamPayment::where('team_id', $paymentEdit->team_id)->where('finish_number', 'General')->where('status', '!=', 2)->where('type', 'رسید')->sum(\DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));
        $totalBaseSent = FinishingTeamPayment::where('team_id', $paymentEdit->team_id)->where('finish_number', 'General')->where('status', '!=', 2)->where('type', 'گرفت')->sum(\DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $finish_numbers = FinishingWork::where('team_id','=',$paymentEdit->team_id)->distinct()->get(['finish_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mappingIn = \App\MappingRule::where('mapping_key', 'PYMT_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        // Fetch Approved Finishing Jobs
        $finishingWorks = \App\FinishingWork::where('team_id', $paymentEdit->team_id)
            ->with(['carpet', 'category'])
            ->where('status', '!=', 2)
            ->orderBy('date', 'DESC')
            ->get();

        $paymentsByRef = \App\FinishingTeamPayment::where('team_id', $paymentEdit->team_id)
            ->where('status', '!=', 2)
            ->select('finish_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN base_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN base_amount ELSE 0 END) as total_received")
            )
            ->groupBy('finish_number')
            ->get()
            ->keyBy('finish_number');

        $batchRefs = $finishingWorks->pluck('finish_number')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('finishing_payment_allocations')
            ->join('production_batches', 'finishing_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('finishing_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(base_allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        $groupedFinishingWorks = [];
        foreach ($finishingWorks as $w) {
            $refPayments = $paymentsByRef->get($w->finish_number);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($w->finish_number) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $w->total_cost = (float)$w->price;
            $w->total_paid = (float)$totalPaid;
            
            $ref = $w->finish_number ?: 'General';
            if (!isset($groupedFinishingWorks[$ref])) {
                $groupedFinishingWorks[$ref] = [
                    'reference' => $ref,
                    'unique_carpets' => [],
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $w->date,
                ];
            }
            if (!in_array($w->carpetId, $groupedFinishingWorks[$ref]['unique_carpets'])) {
                $groupedFinishingWorks[$ref]['unique_carpets'][] = $w->carpetId;
                $groupedFinishingWorks[$ref]['total_carpets']++;
            }
            $groupedFinishingWorks[$ref]['total_cost'] += (float)$w->price;
        }

        foreach ($groupedFinishingWorks as $ref => &$group) {
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

        $totalBaseFinishes = \App\FinishingWork::where('team_id', $paymentEdit->team_id)->where('status', '!=', 2)->sum('price');

        // Unified Ledger Audit Statement
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->where('party_id', $paymentEdit->team_id)
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

        $finishingAdvances = \App\FinishingTeamPayment::where('team_id', $paymentEdit->team_id)->where('is_advance', 1)->where('status', '!=', 2)->orderBy('date', 'DESC')->get();
        $finishingAllocations = \App\FinishingPaymentAllocation::whereHas('payment', function($q) use ($team_id) { $q->where('team_id', $team_id); })->with(['payment', 'allocatable'])->orderBy('created_at', 'DESC')->get();

        return view('finishing-center.finishing-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent', 'finish_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'finishingWorks', 'groupedFinishingWorks', 'totalBaseFinishes', 'ledgerStatement', 'finishingAdvances', 'finishingAllocations'));
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
                $this->accountingService->reverseTransactionBySource($payed->id, 'Finishing Record Edited', get_class($payed));
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
                $payed->finish_number = 'General';
                // Adjust remaining unallocated if not allocated yet
                if ($payed->payment_status == 'unallocated') {
                    $payed->remaining_unallocated_amount = $request->amount;
                }
            } else {
                $payed->remaining_unallocated_amount = 0.0000;
                $payed->payment_status = 'unallocated';
                $payed->finish_number = $request->finish_number ?: 'General';
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

            $team_name = DB::table('finishing_teams')->where('id', $request->team_id)->first();
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش تراکنش تیم تیاری " . $team_name->name . " مبلغ " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/finishing-payments/'.$request->team_id)->with('status', 'بروزرسانی با موفقیت انجام شد!');
        });
    }

    public function allocateAdvance(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'finishing_team_payment_id' => 'required|exists:finishing_team_payments,id',
                'allocatable_id' => 'required|integer',
                'allocatable_type' => 'required|string|in:App\ProductionBatch',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $payment = FinishingTeamPayment::where('id', $request->finishing_team_payment_id)->lockForUpdate()->firstOrFail();
            $modelClass = $request->allocatable_type;
            $document = $modelClass::where('id', $request->allocatable_id)->lockForUpdate()->firstOrFail();

            // Lock validation
            $this->accountingService->failIfLocked($payment->date);
            $this->accountingService->failIfLocked($document->date ?? now()->format('Y-m-d'));

            $availableAdvance = $payment->remaining_unallocated_amount;
            $remainingBill = $document->remaining_balance;
            $baseAllocated = bcmul($request->amount, $payment->exchange_rate, 4);

            if (bccomp($request->amount, $availableAdvance, 4) > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "مقدار تخصیص (" . number_format($request->amount, 2) . " " . $payment->currency_code . ") بیش از موجودی علی‌الحساب (" . number_format($availableAdvance, 2) . " " . $payment->currency_code . ") است."
                ]);
            }
            if (bccomp($baseAllocated, $remainingBill, 4) > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "مقدار تخصیص معادل دالر ($" . number_format($baseAllocated, 2) . ") بیش از باقیمانده بل ($" . number_format($remainingBill, 2) . ") است."
                ]);
            }

            // Create allocation record
            $allocation = FinishingPaymentAllocation::create([
                'finishing_team_payment_id' => $payment->id,
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
            $this->accountingService->postAutoTransaction('finishing_advance_settlement', 'ADVANCE_SETTLEMENT', [
                'date' => now()->format('Y-m-d'),
                'amount' => $request->amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\FinishingTeam',
                'party_id' => $payment->team_id,
                'reference' => $document->reference_number,
                'description' => "تصفیه کار تیاری " . $document->reference_number . " از پیش‌پرداخت شماره " . $payment->id,
                'source_id' => $allocation->id,
                'source_type' => 'App\FinishingPaymentAllocation',
            ]);

            // Log activity
            $activity = new Activity();
            $activity->date = now()->format('Y-m-d');
            $activity->description = "تخصیص پیش‌پرداخت به مبلغ " . $request->amount . " " . $payment->currency_code . " از پیش‌پرداخت شماره " . $payment->id . " به بل تیاری " . $document->reference_number;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success', 'message' => 'تخصیص موفقانه انجام شد!']);
        });
    }

    public function removeAllocation($id)
    {
        return DB::transaction(function () use ($id) {
            $allocation = FinishingPaymentAllocation::lockForUpdate()->findOrFail($id);
            $payment = FinishingTeamPayment::where('id', $allocation->finishing_team_payment_id)->lockForUpdate()->firstOrFail();

            $this->accountingService->failIfLocked($payment->date);

            // Revert parent status/balance
            $payment->remaining_unallocated_amount = bcadd($payment->remaining_unallocated_amount, $allocation->allocated_amount, 4);
            $payment->payment_status = bccomp($payment->remaining_unallocated_amount, $payment->original_amount, 4) === 0 ? 'unallocated' : 'partially_allocated';
            $payment->save();

            // Reverse ledger entry
            $this->accountingService->reverseTransactionBySource($allocation->id, 'Finishing Allocation Deleted', 'App\FinishingPaymentAllocation');

            // Log activity
            $activity = new Activity();
            $activity->date = now()->format('Y-m-d');
            $activity->description = "حذف تخصیص پیش‌پرداخت به مبلغ " . $allocation->allocated_amount . " از پیش‌پرداخت شماره " . $payment->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // Delete allocation record
            $allocation->delete();

            return response()->json(['status' => 'success', 'message' => 'تخصیص موفقانه حذف گردید!']);
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

            // Check Permissions
            if (!Auth::user()->can('cancel_finishing_payment')) {
                abort(403, 'شما صلاحیت ابطال پرداخت‌های بخش تیاری را ندارید.');
            }

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Finishing Team Payment Deleted', 'App\FinishingTeamPayment');
            }

            // Reverse Allocations
            if ($payment->is_advance) {
                $allocations = \App\FinishingPaymentAllocation::where('finishing_team_payment_id', $payment->id)->get();
                foreach ($allocations as $allocation) {
                    $this->accountingService->reverseTransactionBySource($allocation->id, 'Finishing Allocation Deleted', 'App\FinishingPaymentAllocation');
                    $allocation->delete();
                }
            }

            $payment->status = 2; // 2 = Cancelled
            $payment->save();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ابطال پرداخت تیاری: " . $team_name->name . " مبلغ " . $payment->original_amount . " " . $payment->currency_code;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }
}
