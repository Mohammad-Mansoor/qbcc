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
        
        $this->middleware('permission:view_kachaee_money_requests')->only(['money_request']);
        $this->middleware('permission:approve_kachaee_money_requests')->only(['approve_request']);
        $this->middleware('permission:reject_kachaee_money_requests')->only(['delete_request']);
        $this->middleware('permission:manage_kachaee_payments')->only(['store', 'update', 'edit', 'destroy', 'show_all_payment', 'show', 'allocateAdvance', 'removeAllocation']);
    }

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $mKey = ($payment->type == 'گرفت') ? 'PYMT_OUT' : 'PYMT_IN';
            if ($payment->is_advance) {
                $mKey = ($payment->type == 'گرفت') ? 'KACHAEE_ADVANCE_OUT' : 'KACHAEE_ADVANCE_IN';
            }
            
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
                'is_advance' => 'nullable|boolean',
            ]);

            // Overpayment check
            if ($request->kachaee_number !== 'نقد' && $request->type === 'گرفت') {
                $repairCosts = \App\CarpetRepair::where('team_id', $request->team_id)
                    ->where('kachaee_number', $request->kachaee_number)
                    ->get();
                    
                if ($repairCosts->count() > 0) {
                    $totalCost = $repairCosts->sum(function($item) {
                        return (float)($item->total_price ?: $item->af_total_price);
                    });
                    
                    $otherPayments = \App\KachaeePayment::where('team_id', $request->team_id)
                        ->where('kachaee_number', $request->kachaee_number)
                        ->where('status', 1)
                        ->select(
                            \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                            \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
                        )->first();
                    
                    $netPaid = $otherPayments ? ($otherPayments->total_sent - $otherPayments->total_received) : 0;
                    $maxAllowed = max(0.0, $totalCost - $netPaid);
                    
                    if ($request->amount > $maxAllowed) {
                        return redirect()->back()->withErrors([
                            'amount' => "مبلغ پرداختی بیشتر از باقی‌مانده انوایس است. حداکثر مبلغ مجاز: " . number_format($maxAllowed, 2)
                        ])->withInput();
                    }
                }
            }

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

            $isAdvance = $request->has('is_advance') && (bool)$request->is_advance;
            if ($request->kachaee_number !== 'نقد') {
                $isAdvance = false;
            }
            $payed->is_advance = $isAdvance ? 1 : 0;
            $payed->remaining_unallocated_amount = $isAdvance ? $request->amount : 0.0;
            $payed->payment_status = 'unallocated';
            
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

        $payments = KachaeePayment::where('team_id',$team_id)->where('kachaee_number', 'نقد')->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = KachaeePayment::where('team_id', $team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = KachaeePayment::where('team_id', $team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->where('type', 'رسید')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $totalBaseSent = KachaeePayment::where('team_id', $team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->where('type', 'گرفت')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

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

        // Fetch all repairs completed by this team
        $repairs = \App\CarpetRepair::where('team_id', $team_id)
            ->with('carpet')
            ->orderBy('date', 'DESC')
            ->get();

        // Fetch all payments for this team grouped by kachaee_number to calculate partial payment metrics
        $paymentsByRef = \App\KachaeePayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('kachaee_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
            )
            ->groupBy('kachaee_number')
            ->get()
            ->keyBy('kachaee_number');

        $batchRefs = $repairs->pluck('kachaee_number')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('kachaee_payment_allocations')
            ->join('production_batches', 'kachaee_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('kachaee_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        // Map each repair with its paid/remaining metrics
        foreach ($repairs as $rep) {
            $refPayments = $paymentsByRef->get($rep->kachaee_number);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($rep->kachaee_number) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $rep->total_cost = (float)($rep->total_price ?: $rep->af_total_price);
            $rep->total_paid = (float)$totalPaid;
            $rep->remaining_balance = max(0, $rep->total_cost - $rep->total_paid);
            
            if ($rep->total_paid == 0) {
                $rep->payment_status = 'unpaid';
            } elseif ($rep->remaining_balance <= 0) {
                $rep->payment_status = 'paid';
            } else {
                $rep->payment_status = 'partial';
            }
        }

        // Calculate Repair Cost Totals
        $totalBaseRepairs = \App\CarpetRepair::where('team_id', $team_id)->sum('base_currency_amount');

        // Fetch Unified Ledger Statement (Double-Entry Log)
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')
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

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'all', 'repairs', 'totalBaseRepairs', 'ledgerStatement'));
    }

    public function show($team_id)
    {
        $team = Kachaee::find($team_id);
        if (!$team) {
            return redirect('/dashboard/kachaee-team')->with('error', 'تیم کچایی یافت نشد (Team not found).');
        }

        $payments = KachaeePayment::where('team_id',$team_id)->where('kachaee_number', 'نقد')->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = KachaeePayment::where('team_id', $team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN (CASE WHEN is_advance = 1 THEN remaining_unallocated_amount ELSE original_amount END) ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = KachaeePayment::where('team_id', $team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->where('type', 'رسید')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

        $totalBaseSent = KachaeePayment::where('team_id', $team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->where('type', 'گرفت')
            ->sum(DB::raw('CASE WHEN is_advance = 1 THEN remaining_unallocated_amount * exchange_rate ELSE base_amount END'));

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

        // Fetch all repairs completed by this team
        $repairs = \App\CarpetRepair::where('team_id', $team_id)
            ->with('carpet')
            ->orderBy('date', 'DESC')
            ->get();

        // Fetch all payments for this team grouped by kachaee_number to calculate partial payment metrics
        $paymentsByRef = \App\KachaeePayment::where('team_id', $team_id)
            ->where('status', 1)
            ->select('kachaee_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
            )
            ->groupBy('kachaee_number')
            ->get()
            ->keyBy('kachaee_number');

        $batchRefs = $repairs->pluck('kachaee_number')->unique()->filter()->toArray();
        $allocationsByRef = \DB::table('kachaee_payment_allocations')
            ->join('production_batches', 'kachaee_payment_allocations.allocatable_id', '=', 'production_batches.id')
            ->where('kachaee_payment_allocations.allocatable_type', 'App\ProductionBatch')
            ->whereIn('production_batches.reference_number', $batchRefs)
            ->select('production_batches.reference_number', \DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('production_batches.reference_number')
            ->pluck('total_allocated', 'reference_number');

        // Map each repair with its paid/remaining metrics
        $groupedRepairs = [];
        foreach ($repairs as $rep) {
            $refPayments = $paymentsByRef->get($rep->kachaee_number);
            $directPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            $allocatedPaid = $allocationsByRef->get($rep->kachaee_number) ?? 0.0;
            $totalPaid = $directPaid + $allocatedPaid;
            
            $rep->total_cost = (float)($rep->total_price ?: $rep->af_total_price);
            $rep->total_paid = (float)$totalPaid;
            $rep->remaining_balance = max(0, $rep->total_cost - $rep->total_paid);
            
            if ($rep->total_paid == 0) {
                $rep->payment_status = 'unpaid';
            } elseif ($rep->remaining_balance <= 0) {
                $rep->payment_status = 'paid';
            } else {
                $rep->payment_status = 'partial';
            }

            $ref = $rep->kachaee_number ?: 'نقد';
            if (!isset($groupedRepairs[$ref])) {
                $groupedRepairs[$ref] = [
                    'reference' => $ref,
                    'total_carpets' => 0,
                    'total_cost' => 0.0,
                    'total_paid' => (float)$totalPaid,
                    'remaining_balance' => 0.0,
                    'payment_status' => 'unpaid',
                    'date' => $rep->date,
                ];
            }
            $groupedRepairs[$ref]['total_carpets']++;
            $groupedRepairs[$ref]['total_cost'] += $rep->total_cost;
        }

        foreach ($groupedRepairs as $ref => &$group) {
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

        // Calculate Repair Cost Totals
        $totalBaseRepairs = \App\CarpetRepair::where('team_id', $team_id)->sum('base_currency_amount');

        // Fetch Unified Ledger Statement (Double-Entry Log)
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')
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

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'repairs', 'groupedRepairs', 'totalBaseRepairs', 'ledgerStatement'));
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
        $payments = KachaeePayment::where('team_id',$paymentEdit->team_id)->where('kachaee_number', 'نقد')->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = KachaeePayment::where('team_id', $paymentEdit->team_id)
            ->where('kachaee_number', 'نقد')
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = KachaeePayment::where('team_id', $paymentEdit->team_id)->where('kachaee_number', 'نقد')->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = KachaeePayment::where('team_id', $paymentEdit->team_id)->where('kachaee_number', 'نقد')->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

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

        // Fetch all repairs completed by this team
        $repairs = \App\CarpetRepair::where('team_id', $team->id)
            ->with('carpet')
            ->orderBy('date', 'DESC')
            ->get();

        // Fetch all payments for this team grouped by kachaee_number to calculate partial payment metrics
        $paymentsByRef = \App\KachaeePayment::where('team_id', $team->id)
            ->where('status', 1)
            ->select('kachaee_number', 
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
            )
            ->groupBy('kachaee_number')
            ->get()
            ->keyBy('kachaee_number');

        // Map each repair with its paid/remaining metrics
        foreach ($repairs as $rep) {
            $refPayments = $paymentsByRef->get($rep->kachaee_number);
            $totalPaid = $refPayments ? ($refPayments->total_sent - $refPayments->total_received) : 0;
            
            $rep->total_cost = (float)($rep->total_price ?: $rep->af_total_price);
            $rep->total_paid = (float)$totalPaid;
            $rep->remaining_balance = max(0, $rep->total_cost - $rep->total_paid);
            
            if ($rep->total_paid == 0) {
                $rep->payment_status = 'unpaid';
            } elseif ($rep->remaining_balance <= 0) {
                $rep->payment_status = 'paid';
            } else {
                $rep->payment_status = 'partial';
            }
        }

        // Calculate Repair Cost Totals
        $totalBaseRepairs = \App\CarpetRepair::where('team_id', $team->id)->sum('base_currency_amount');

        // Fetch Unified Ledger Statement (Double-Entry Log)
        $ledgerStatement = \DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')
            ->where('party_id', $team->id)
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

        return view('kachaee.kachaee-payment',compact('team','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','kachaee_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingIn', 'mappingOut', 'currencies', 'currentDebitAccountId', 'currentCreditAccountId', 'repairs', 'totalBaseRepairs', 'ledgerStatement'));
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
                'is_advance' => 'nullable|boolean',
            ]);

            // Overpayment check
            if ($request->kachaee_number !== 'نقد' && $request->type === 'گرفت') {
                $repairCosts = \App\CarpetRepair::where('team_id', $request->team_id)
                    ->where('kachaee_number', $request->kachaee_number)
                    ->get();
                    
                if ($repairCosts->count() > 0) {
                    $totalCost = $repairCosts->sum(function($item) {
                        return (float)($item->total_price ?: $item->af_total_price);
                    });
                    
                    $otherPayments = \App\KachaeePayment::where('team_id', $request->team_id)
                        ->where('kachaee_number', $request->kachaee_number)
                        ->where('id', '!=', $payment_id)
                        ->where('status', 1)
                        ->select(
                            \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent"),
                            \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received")
                        )->first();
                    
                    $netPaid = $otherPayments ? ($otherPayments->total_sent - $otherPayments->total_received) : 0;
                    $maxAllowed = max(0.0, $totalCost - $netPaid);
                    
                    if ($request->amount > $maxAllowed) {
                        return redirect()->back()->withErrors([
                            'amount' => "مبلغ پرداختی بیشتر از باقی‌مانده انوایس است. حداکثر مبلغ مجاز: " . number_format($maxAllowed, 2)
                        ])->withInput();
                    }
                }
            }

            $payed = KachaeePayment::find($payment_id);

            // Reversal - pass class name to avoid ID collision reversals with other models
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Kachaee Record Edited', get_class($payed));
            }

            // Fetch and remove old allocations, reversing their transactions
            $oldAllocations = \App\KachaeePaymentAllocation::where('kachaee_payment_id', $payed->id)->get();
            foreach ($oldAllocations as $alloc) {
                // Reverse the accounting transaction
                $this->accountingService->reverseTransactionBySource($alloc->id, 'Kachaee Allocation Replaced via Edit', 'App\KachaeePaymentAllocation');
                $alloc->delete();
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

            $isAdvance = $request->has('is_advance') && (bool)$request->is_advance;
            if ($request->kachaee_number !== 'نقد') {
                $isAdvance = false;
            }
            $payed->is_advance = $isAdvance ? 1 : 0;
            $payed->remaining_unallocated_amount = $isAdvance ? $request->amount : 0.0;
            $payed->payment_status = 'unallocated';

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

    public function allocateAdvance(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'kachaee_payment_id' => 'required|exists:kachaee_payments,id',
                'allocatable_id' => 'required|integer',
                'allocatable_type' => 'required|string|in:App\ProductionBatch',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $payment = KachaeePayment::where('id', $request->kachaee_payment_id)->lockForUpdate()->firstOrFail();
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
            $allocation = \App\KachaeePaymentAllocation::create([
                'kachaee_payment_id' => $payment->id,
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
            $this->accountingService->postAutoTransaction('kachaee_advance_settlement', 'ADVANCE_SETTLEMENT', [
                'date' => now()->format('Y-m-d'),
                'amount' => $request->amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\Kachaee',
                'party_id' => $payment->team_id,
                'reference' => $document->reference_number,
                'description' => "تصفیه بل کچایی " . $document->reference_number . " از پیش‌پرداخت شماره " . $payment->id,
                'source_id' => $allocation->id,
                'source_type' => 'App\KachaeePaymentAllocation',
            ]);

            // Log activity
            $activity = new Activity();
            $activity->date = now()->format('Y-m-d');
            $activity->description = "تخصیص پیش‌پرداخت به مبلغ " . $request->amount . " " . $payment->currency_code . " از پیش‌پرداخت شماره " . $payment->id . " به بل کچایی " . $document->reference_number;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success', 'message' => 'تخصیص موفقانه انجام شد!']);
        });
    }

    public function removeAllocation($id)
    {
        return DB::transaction(function () use ($id) {
            $allocation = \App\KachaeePaymentAllocation::lockForUpdate()->findOrFail($id);
            $payment = $allocation->payment;
            $doc = $allocation->allocatable;

            // Check if date is locked
            $this->accountingService->failIfLocked($payment->date);
            if ($doc) {
                $this->accountingService->failIfLocked($doc->date ?? now()->format('Y-m-d'));
            }

            // Reverse the accounting transaction
            $this->accountingService->reverseTransactionBySource($allocation->id, 'Kachaee Allocation Deleted', 'App\KachaeePaymentAllocation');

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
