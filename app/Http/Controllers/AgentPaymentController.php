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
                'override_debit_account_id' => $payment->override_debit_account_id,
                'override_credit_account_id' => $payment->override_credit_account_id,
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
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'allocatable_id' => 'nullable|integer',
                'allocatable_type' => 'nullable|string|in:App\PurchaseInvoice,App\Invoice',
            ]);

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;

            // FORENSIC RULE: BCMath Calculation (base_amount = original * rate)
            $baseAmount = bcmul($request->amount, $rate, 4);

            // --- OVERPAYMENT PREVENTION & ALLOCATION VALIDATION ---
            $document = null;
            if ($request->allocatable_id && $request->allocatable_type) {
                $modelClass = $request->allocatable_type;
                $document = $modelClass::where('id', $request->allocatable_id)->lockForUpdate()->first();
                
                if (!$document) {
                    return redirect()->back()->with('error', 'سند مورد نظر پیدا نشد (Document not found).')->withInput();
                }

                if ($document->payment_status === 'paid' || $document->remaining_balance <= 0.01) {
                    return redirect()->back()->with('error', 'این سند قبلاً تصفیه شده است و نیاز به پرداخت ندارد.')->withInput();
                }

                if ($baseAmount > ($document->remaining_balance + 0.01)) {
                    return redirect()->back()->withErrors([
                        'amount' => "مبلغ پرداختی ($" . number_format($baseAmount, 2) . ") بزرگتر از باقیمانده سند ($" . number_format($document->remaining_balance, 2) . ") است."
                    ])->withInput();
                }
            }

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
            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;

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

            // Store allocation if linked
            if ($document) {
                \App\AgentPaymentAllocation::create([
                    'agent_payment_id' => $payed->id,
                    'allocatable_type' => $request->allocatable_type,
                    'allocatable_id' => $request->allocatable_id,
                    'allocated_amount' => $request->amount,
                    'exchange_rate' => $rate,
                    'base_allocated_amount' => $baseAmount,
                ]);

                // Recalculate document status
                $remaining = $document->remaining_balance;
                if ($remaining <= 0.01) {
                    $document->payment_status = 'paid';
                } else {
                    $document->payment_status = 'partially_paid';
                }
                $document->save();
            }

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

        $payments = AgentPayment::where('agent_id', $agent_id)->doesntHave('allocations')->orderBy('date', 'DESC')->paginate(30);

        // Fetch Carpet Purchase Bills (Bills)
        $purchaseBills = \App\PurchaseInvoice::where('agent_id', $agent_id)
            ->with(['carpets'])
            ->orderBy('date', 'DESC')
            ->get();
            
        // Fetch Dye & Yarn Sales Invoices
        $salesInvoices = \App\Invoice::where('agent_id', $agent_id)
            ->whereIn('type', ['dye', 'yarn'])
            ->with(['material_sales'])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        // Calculate Totals for Bills and Invoices
        $totalOwedPurchases = $purchaseBills->sum(function($bill) { return $bill->total_amount; });
        $totalPaidPurchases = DB::table('agent_payment_allocations')
            ->join('purchase_invoices', 'agent_payment_allocations.allocatable_id', '=', 'purchase_invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\PurchaseInvoice')
            ->where('purchase_invoices.agent_id', $agent_id)
            ->sum('base_allocated_amount');

        $totalReceivableSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });
        $totalReceivedSales = DB::table('agent_payment_allocations')
            ->join('invoices', 'agent_payment_allocations.allocatable_id', '=', 'invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\Invoice')
            ->where('invoices.agent_id', $agent_id)
            ->sum('base_allocated_amount');

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = AgentPayment::where('agent_id', $agent_id)
            ->where('status', 1)
            ->doesntHave('allocations')
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->doesntHave('allocations')->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->doesntHave('allocations')->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $check_numbers = CarpetCheckBook::where('agent_id', '=', $agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $pymtInDebit = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $pymtInCredit = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $pymtOutDebit = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $pymtOutCredit = $selectionService->getValidAccounts('PYMT_OUT', 'credit');

        return view('agents.agent-payments', compact(
            'agent', 'payments', 'paymentEdit', 'currencyTotals', 'totalBaseReceived', 
            'totalBaseSent', 'check_numbers', 'sale_numbers', 'currencies',
            'pymtInDebit', 'pymtInCredit', 'pymtOutDebit', 'pymtOutCredit',
            'purchaseBills', 'salesInvoices', 'totalOwedPurchases', 'totalPaidPurchases',
            'totalReceivableSales', 'totalReceivedSales'
        ));
    }

    public function show_all($agent_id)
    {
        $payments = AgentPayment::where('agent_id', $agent_id)->doesntHave('allocations')->orderBy('date', 'DESC')->get();
        $agent = Agents::find($agent_id);

        // Fetch Carpet Purchase Bills (Bills)
        $purchaseBills = \App\PurchaseInvoice::where('agent_id', $agent_id)
            ->with(['carpets'])
            ->orderBy('date', 'DESC')
            ->get();
            
        // Fetch Dye & Yarn Sales Invoices
        $salesInvoices = \App\Invoice::where('agent_id', $agent_id)
            ->whereIn('type', ['dye', 'yarn'])
            ->with(['material_sales'])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        // Calculate Totals for Bills and Invoices
        $totalOwedPurchases = $purchaseBills->sum(function($bill) { return $bill->total_amount; });
        $totalPaidPurchases = DB::table('agent_payment_allocations')
            ->join('purchase_invoices', 'agent_payment_allocations.allocatable_id', '=', 'purchase_invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\PurchaseInvoice')
            ->where('purchase_invoices.agent_id', $agent_id)
            ->sum('base_allocated_amount');

        $totalReceivableSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });
        $totalReceivedSales = DB::table('agent_payment_allocations')
            ->join('invoices', 'agent_payment_allocations.allocatable_id', '=', 'invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\Invoice')
            ->where('invoices.agent_id', $agent_id)
            ->sum('base_allocated_amount');

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = AgentPayment::where('agent_id', $agent_id)
            ->where('status', 1)
            ->doesntHave('allocations')
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->doesntHave('allocations')->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = AgentPayment::where('agent_id', $agent_id)->where('status', 1)->doesntHave('allocations')->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $check_numbers = CarpetCheckBook::where('agent_id', '=', $agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        $all = '';

        $selectionService = new \App\Services\AccountSelectionService();
        $pymtInDebit = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $pymtInCredit = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $pymtOutDebit = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $pymtOutCredit = $selectionService->getValidAccounts('PYMT_OUT', 'credit');

        return view('agents.agent-payments', compact(
            'agent', 'payments', 'paymentEdit', 'currencyTotals', 'totalBaseReceived', 
            'totalBaseSent', 'check_numbers', 'all', 'sale_numbers', 'currencies',
            'pymtInDebit', 'pymtInCredit', 'pymtOutDebit', 'pymtOutCredit',
            'purchaseBills', 'salesInvoices', 'totalOwedPurchases', 'totalPaidPurchases',
            'totalReceivableSales', 'totalReceivedSales'
        ));
    }

    public function edit($payment_id)
    {
        $paymentEdit = AgentPayment::find($payment_id);
        $payments = AgentPayment::where('agent_id', $paymentEdit->agent_id)->doesntHave('allocations')->orderBy('date', 'DESC')->paginate(30);
        $agent = Agents::find($paymentEdit->agent_id);

        // Fetch Carpet Purchase Bills (Bills)
        $purchaseBills = \App\PurchaseInvoice::where('agent_id', $paymentEdit->agent_id)
            ->with(['carpets'])
            ->orderBy('date', 'DESC')
            ->get();
            
        // Fetch Dye & Yarn Sales Invoices
        $salesInvoices = \App\Invoice::where('agent_id', $paymentEdit->agent_id)
            ->whereIn('type', ['dye', 'yarn'])
            ->with(['material_sales'])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        // Calculate Totals for Bills and Invoices
        $totalOwedPurchases = $purchaseBills->sum(function($bill) { return $bill->total_amount; });
        $totalPaidPurchases = DB::table('agent_payment_allocations')
            ->join('purchase_invoices', 'agent_payment_allocations.allocatable_id', '=', 'purchase_invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\PurchaseInvoice')
            ->where('purchase_invoices.agent_id', $paymentEdit->agent_id)
            ->sum('base_allocated_amount');

        $totalReceivableSales = $salesInvoices->sum(function($inv) { return $inv->total_amount; });
        $totalReceivedSales = DB::table('agent_payment_allocations')
            ->join('invoices', 'agent_payment_allocations.allocatable_id', '=', 'invoices.id')
            ->where('agent_payment_allocations.allocatable_type', 'App\Invoice')
            ->where('invoices.agent_id', $paymentEdit->agent_id)
            ->sum('base_allocated_amount');

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = AgentPayment::where('agent_id', $paymentEdit->agent_id)
            ->where('status', 1)
            ->doesntHave('allocations')
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = AgentPayment::where('agent_id', $paymentEdit->agent_id)->where('status', 1)->doesntHave('allocations')->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = AgentPayment::where('agent_id', $paymentEdit->agent_id)->where('status', 1)->doesntHave('allocations')->where('type', 'گرفت')->sum('base_amount');

        $check_numbers = CarpetCheckBook::where('agent_id', '=', $paymentEdit->agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $paymentEdit->agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $pymtInDebit = $selectionService->getValidAccounts('PYMT_IN', 'debit');
        $pymtInCredit = $selectionService->getValidAccounts('PYMT_IN', 'credit');
        $pymtOutDebit = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $pymtOutCredit = $selectionService->getValidAccounts('PYMT_OUT', 'credit');

        return view('agents.agent-payments', compact(
            'agent', 'payments', 'paymentEdit', 'currencyTotals', 'totalBaseReceived', 
            'totalBaseSent', 'check_numbers', 'sale_numbers', 'currencies',
            'pymtInDebit', 'pymtInCredit', 'pymtOutDebit', 'pymtOutCredit',
            'purchaseBills', 'salesInvoices', 'totalOwedPurchases', 'totalPaidPurchases',
            'totalReceivableSales', 'totalReceivedSales'
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
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
            ]);

            $payed = AgentPayment::find($payment_id);
            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();

            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Agent Payment Edited');
            }

            // Fetch and remove old allocations, updating document statuses first
            $oldAllocations = \App\AgentPaymentAllocation::where('agent_payment_id', $payed->id)->get();
            foreach ($oldAllocations as $alloc) {
                $doc = $alloc->allocatable;
                if ($doc) {
                    $alloc->delete(); // Delete first
                    $docPaid = \App\AgentPaymentAllocation::where('allocatable_type', $alloc->allocatable_type)
                        ->where('allocatable_id', $alloc->allocatable_id)
                        ->sum('base_allocated_amount') ?? 0;
                    $docRemaining = $doc->total_amount - $docPaid;
                    if ($docRemaining >= $doc->total_amount - 0.01) {
                        $doc->payment_status = 'unpaid';
                    } else if ($docRemaining <= 0.01) {
                        $doc->payment_status = 'paid';
                    } else {
                        $doc->payment_status = 'partially_paid';
                    }
                    $doc->save();
                }
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
            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;

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

            // Remove allocations and update document statuses
            $allocations = \App\AgentPaymentAllocation::where('agent_payment_id', $payment->id)->get();
            foreach ($allocations as $alloc) {
                $doc = $alloc->allocatable;
                if ($doc) {
                    $alloc->delete(); // Delete first
                    $docPaid = \App\AgentPaymentAllocation::where('allocatable_type', $alloc->allocatable_type)
                        ->where('allocatable_id', $alloc->allocatable_id)
                        ->sum('base_allocated_amount') ?? 0;
                    $docRemaining = $doc->total_amount - $docPaid;
                    if ($docRemaining >= $doc->total_amount - 0.01) {
                        $doc->payment_status = 'unpaid';
                    } else if ($docRemaining <= 0.01) {
                        $doc->payment_status = 'paid';
                    } else {
                        $doc->payment_status = 'partially_paid';
                    }
                    $doc->save();
                }
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
