<?php

namespace App\Http\Controllers;

use App\Activity;
use App\EmployeePayment;
use App\OfficeEmployee;
use App\Services\AccountingService;
use App\Services\AccountSelectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeePaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('permission:manage_employee_payments')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    // ─── Private: Post to GL ────────────────────────────────────────────────

    private function postPaymentToAccounting($payment)
    {
        try {
            $amount = $payment->original_amount ?? $payment->amount ?? 0;
            $mappingKey = ($payment->type === 'رسید') ? 'PYMT_IN' : 'PYMT_OUT';

            $this->accountingService->postAutoTransaction('employee_payment', $mappingKey, [
                'date'         => $payment->date,
                'amount'       => $amount,
                'currency_code'=> $payment->currency_code,
                'exchange_rate'=> $payment->exchange_rate,
                'party_type'   => 'App\OfficeEmployee',
                'party_id'     => $payment->employee_id,
                'reference'    => 'EMP-PAY-' . $payment->id,
                'description'  => $payment->description,
                'source_type'  => get_class($payment),
                'source_id'    => $payment->id,
                'type'         => $payment->type,
                'override_debit_account_id'  => $payment->override_debit_account_id,
                'override_credit_account_id' => $payment->override_credit_account_id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Employee Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    // ─── Pending Request List ────────────────────────────────────────────────

    public function request_list()
    {
        $requests = EmployeePayment::with('employee')->where('status', 0)->orderBy('id', 'DESC')->get();
        return view('office-employee.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = EmployeePayment::find($id);
            if (!$payment) {
                return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
            }

            $payment->status = 1;
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $origAmt  = $payment->original_amount ?? $payment->amount ?? $payment->amount_af ?? 0;
            $currCode = $payment->currency_code ?? ($payment->amount > 0 ? 'USD' : 'AFN');

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . number_format($origAmt, 2) . " " . $currCode . " معاش برای کارمند #" . $payment->employee_id . " تایید شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $credit = EmployeePayment::find($id);
        if (!$credit) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }
        $credit->delete();
        return response()->json(['status' => 'success']);
    }

    // ─── Index ───────────────────────────────────────────────────────────────

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'contract_number' => 'required',
                'amount'          => 'required|numeric|min:0',
                'description'     => 'required',
                'date'            => 'required|date',
                'employee_id'     => 'required|exists:office_employees,id',
                'currency_id'     => 'required|exists:currencies,id',
                'exchange_rate'   => 'required|numeric|gt:0',
                'type'            => 'required|in:رسید,گرفت',
                'money_type'      => 'required|in:دالر,افغانی,USD,AFN,EUR,PKR',
                'override_debit_account_id'  => 'required|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'required|exists:chart_of_accounts,id|different:override_debit_account_id',
            ]);

            $currency = \App\Currency::findOrFail($request->currency_id);

            $payed = new EmployeePayment();
            $payed->contract_number = $request->contract_number;
            $payed->contract_no     = $request->contract_number; // Legacy support for non-nullable DB column
            $payed->description     = $request->description;
            $payed->date            = $request->date;
            $payed->type            = $request->type;
            $payed->employee_id     = $request->employee_id;
            $payed->dollar_rate     = $request->exchange_rate; // legacy field
            $payed->override_debit_account_id  = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;

            // Forensic FX snapshot
            $payed->currency_id          = $currency->id;
            $payed->currency_code        = $currency->code;
            $payed->exchange_rate        = $request->exchange_rate;
            $payed->original_amount      = bcmul($request->amount, 1, 4); // amount in chosen currency
            $payed->base_currency_amount = bcmul($request->amount, $request->exchange_rate, 4); // USD equivalent

            // Legacy dual-column support
            if ($currency->is_base_currency || strtoupper($currency->code) === 'USD') {
                $payed->amount    = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount    = $payed->base_currency_amount;
                $payed->amount_af = $request->amount;
            }

            $payed->status = (Auth::user()->isSuperAdmin()) ? 1 : 0;
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $employee = OfficeEmployee::find($request->employee_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت معاش به کارمند " . ($employee->name ?? 'Unknown') .
                " به مبلغ " . number_format($request->amount, 2) . " " . $currency->code .
                " (معادل USD: " . number_format($payed->base_currency_amount, 2) . ")";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'معاش با موفقیت ثبت و در دفتر روزنامچه درج شد!');
        });
    }

    // ─── Show (حساب button) ──────────────────────────────────────────────────

    public function show($employee_id)
    {
        $employee = OfficeEmployee::find($employee_id);
        if (!$employee) {
            abort(404, 'Employee not found');
        }

        $contract_number = $employee->employee_salary->last();

        // Guard: no salary record yet
        if (!$contract_number) {
            $payments         = collect();
            $debits_us        = 0;
            $debits_af        = 0;
            $credit_us        = 0;
            $credit_af        = 0;
            $contract_number_list = collect();
            $paymentEdit      = '';
            $currencies       = \App\Currency::where('is_active', 1)->get();
            $baseCurrency     = \App\Currency::where('is_base_currency', 1)->first();
            $allowedDebitAccounts  = \App\ChartOfAccount::orderBy('account_code')->get();
            $allowedCreditAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
            $mappingPayroll   = \App\MappingRule::where('mapping_key', 'PAYROLL_PAYMENT')->first();

            return view('office-employee.employee-payment', compact(
                'employee', 'payments', 'paymentEdit', 'debits_us', 'debits_af',
                'credit_af', 'credit_us', 'contract_number', 'contract_number_list',
                'currencies', 'baseCurrency', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mappingPayroll'
            ));
        }

        $cn = $contract_number->contract_number;

        $debits_us = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount');
        $debits_af = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount');
        $credit_af = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount_af');
        $total_debit_usd  = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('base_currency_amount');
        $total_credit_usd = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('base_currency_amount');

        $payments            = EmployeePayment::where('employee_id', $employee_id)->where('contract_number', $cn)->with(['debitAccount', 'creditAccount'])->orderBy('date', 'DESC')->paginate(30);
        $paymentEdit         = '';
        $contract_number_list = $employee->employee_salary;
        $currencies          = \App\Currency::where('is_active', 1)->get();
        $baseCurrency        = \App\Currency::where('is_base_currency', 1)->first();

            $allowedDebitAccounts  = \App\ChartOfAccount::orderBy('account_code')->get();
            $allowedCreditAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingPayroll      = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PAYROLL_PAYMENT')->first();
        if (!$mappingPayroll) {
            $mappingPayroll  = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PYMT_OUT')->where('transaction_type', 'employee_payment')->first();
        }

        return view('office-employee.employee-payment', compact(
            'employee', 'payments', 'paymentEdit', 'debits_us', 'debits_af',
            'credit_af', 'credit_us', 'contract_number', 'contract_number_list',
            'currencies', 'baseCurrency', 'allowedDebitAccounts', 'allowedCreditAccounts',
            'mappingPayroll', 'total_debit_usd', 'total_credit_usd'
        ));
    }

    // ─── Show All ────────────────────────────────────────────────────────────

    public function show_all_payment($employee_id)
    {
        $employee = OfficeEmployee::find($employee_id);
        if (!$employee) { abort(404); }

        $contract_number = $employee->employee_salary->last();
        if (!$contract_number) { abort(404, 'No contract found'); }

        $cn = $contract_number->contract_number;

        $debits_us = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount');
        $debits_af = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount');
        $credit_af = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount_af');
        $total_debit_usd  = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('base_currency_amount');
        $total_credit_usd = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('base_currency_amount');

        $payments            = EmployeePayment::where('employee_id', $employee_id)->where('contract_number', $cn)->with(['debitAccount', 'creditAccount'])->orderBy('date', 'DESC')->get();
        $paymentEdit         = '';
        $all                 = true;
        $contract_number_list = $employee->employee_salary;
        $currencies          = \App\Currency::where('is_active', 1)->get();
        $baseCurrency        = \App\Currency::where('is_base_currency', 1)->first();
        $allowedDebitAccounts  = \App\ChartOfAccount::orderBy('account_code')->get();
        $allowedCreditAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingPayroll      = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PAYROLL_PAYMENT')->first();
        if (!$mappingPayroll) {
            $mappingPayroll  = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PYMT_OUT')->where('transaction_type', 'employee_payment')->first();
        }

        return view('office-employee.employee-payment', compact(
            'employee', 'payments', 'paymentEdit', 'debits_us', 'debits_af',
            'credit_af', 'credit_us', 'contract_number', 'all', 'contract_number_list',
            'currencies', 'baseCurrency', 'allowedDebitAccounts', 'allowedCreditAccounts',
            'mappingPayroll', 'total_debit_usd', 'total_credit_usd'
        ));
    }

    // ─── Show by Contract ────────────────────────────────────────────────────

    public function show_contract_payment(Request $request)
    {
        $employee_id = $request->employee_id;
        $employee = OfficeEmployee::find($employee_id);
        if (!$employee) { abort(404); }

        $contract_number = $employee->employee_salary->last();
        $cn = $request->contract_number;

        $debits_us = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount');
        $debits_af = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount');
        $credit_af = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('amount_af');
        $total_debit_usd  = EmployeePayment::where('type', 'گرفت')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('base_currency_amount');
        $total_credit_usd = EmployeePayment::where('type', 'رسید')->where('employee_id', $employee_id)->where('contract_number', $cn)->where('status', 1)->sum('base_currency_amount');

        $payments            = EmployeePayment::where('employee_id', $employee_id)->where('contract_number', $cn)->with(['debitAccount', 'creditAccount'])->orderBy('date', 'DESC')->paginate(30);
        $paymentEdit         = '';
        $contract_number_list = $employee->employee_salary;
        $check_contract      = true;
        $currencies          = \App\Currency::where('is_active', 1)->get();
        $baseCurrency        = \App\Currency::where('is_base_currency', 1)->first();
        $allowedDebitAccounts  = \App\ChartOfAccount::orderBy('account_code')->get();
        $allowedCreditAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingPayroll      = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PAYROLL_PAYMENT')->first();
        if (!$mappingPayroll) {
            $mappingPayroll  = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PYMT_OUT')->where('transaction_type', 'employee_payment')->first();
        }

        return view('office-employee.employee-payment', compact(
            'employee', 'payments', 'paymentEdit', 'debits_us', 'debits_af',
            'credit_af', 'credit_us', 'contract_number', 'contract_number_list', 'check_contract',
            'currencies', 'baseCurrency', 'allowedDebitAccounts', 'allowedCreditAccounts',
            'mappingPayroll', 'total_debit_usd', 'total_credit_usd'
        ));
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function edit($payment_id)
    {
        $paymentEdit = EmployeePayment::find($payment_id);
        if (!$paymentEdit) { abort(404); }

        $employee = OfficeEmployee::find($paymentEdit->employee_id);
        $contract_number = $employee->employee_salary->last();
        $cn = $contract_number ? $contract_number->contract_number : null;

        $debits_us = EmployeePayment::where('type', 'گرفت')->where('employee_id', $paymentEdit->employee_id)->where('status', 1)->sum('amount');
        $debits_af = EmployeePayment::where('type', 'گرفت')->where('employee_id', $paymentEdit->employee_id)->where('status', 1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type', 'رسید')->where('employee_id', $paymentEdit->employee_id)->where('status', 1)->sum('amount');
        $credit_af = EmployeePayment::where('type', 'رسید')->where('employee_id', $paymentEdit->employee_id)->where('status', 1)->sum('amount_af');
        $total_debit_usd  = EmployeePayment::where('type', 'گرفت')->where('employee_id', $paymentEdit->employee_id)->where('status', 1)->sum('base_currency_amount');
        $total_credit_usd = EmployeePayment::where('type', 'رسید')->where('employee_id', $paymentEdit->employee_id)->where('status', 1)->sum('base_currency_amount');

        $payments = $cn
            ? EmployeePayment::where('employee_id', $paymentEdit->employee_id)->where('contract_number', $cn)->with(['debitAccount', 'creditAccount'])->orderBy('date', 'DESC')->paginate(30)
            : collect();

        $contract_number_list = $employee->employee_salary;
        $currencies           = \App\Currency::where('is_active', 1)->get();
        $baseCurrency         = \App\Currency::where('is_base_currency', 1)->first();
        $allowedDebitAccounts  = \App\ChartOfAccount::orderBy('account_code')->get();
        $allowedCreditAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingPayroll       = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PAYROLL_PAYMENT')->first();
        if (!$mappingPayroll) {
            $mappingPayroll   = \App\MappingRule::with(['debitAccount', 'creditAccount'])->where('mapping_key', 'PYMT_OUT')->where('transaction_type', 'employee_payment')->first();
        }

        return view('office-employee.employee-payment', compact(
            'employee', 'payments', 'paymentEdit', 'debits_us', 'debits_af',
            'credit_af', 'credit_us', 'contract_number', 'contract_number_list',
            'currencies', 'baseCurrency', 'allowedDebitAccounts', 'allowedCreditAccounts',
            'mappingPayroll', 'total_debit_usd', 'total_credit_usd'
        ));
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'contract_number' => 'required',
                'amount'          => 'required|numeric|min:0',
                'description'     => 'required',
                'date'            => 'required|date',
                'currency_id'     => 'required|exists:currencies,id',
                'exchange_rate'   => 'required|numeric|gt:0',
                'type'            => 'required|in:رسید,گرفت',
                'money_type'      => 'required',
                'override_debit_account_id'  => 'required|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'required|exists:chart_of_accounts,id|different:override_debit_account_id',
            ]);

            $payed = EmployeePayment::find($payment_id);
            if (!$payed) { abort(404); }

            // Reverse old GL entry if approved
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Employee Payment Edited');
            }

            $currency = \App\Currency::findOrFail($request->currency_id);
            $employee = OfficeEmployee::find($request->employee_id);

            $payed->contract_number      = $request->contract_number;
            $payed->contract_no          = $request->contract_number; // Legacy support for non-nullable DB column
            $payed->description          = $request->description;
            $payed->date                 = $request->date;
            $payed->type                 = $request->type;
            $payed->employee_id          = $request->employee_id;
            $payed->dollar_rate          = $request->exchange_rate;
            $payed->override_debit_account_id  = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;
            $payed->currency_id          = $currency->id;
            $payed->currency_code        = $currency->code;
            $payed->exchange_rate        = $request->exchange_rate;
            $payed->original_amount      = bcmul($request->amount, 1, 4);
            $payed->base_currency_amount = bcmul($request->amount, $request->exchange_rate, 4);

            if ($currency->is_base_currency || strtoupper($currency->code) === 'USD') {
                $payed->amount    = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount    = $payed->base_currency_amount;
                $payed->amount_af = $request->amount;
            }

            $payed->update();

            // Re-post if approved
            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت معاش کارمند " . ($employee->name ?? 'Unknown') .
                " مبلغ " . number_format($request->amount, 2) . " " . $currency->code;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/employee-payments/' . $request->employee_id)
                ->with('status', 'ویرایش موفقانه انجام شد و حسابات مالی بروز گردید!');
        });
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = EmployeePayment::find($id);
            if (!$payment) {
                return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
            }

            $employee = OfficeEmployee::find($payment->employee_id);

            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Employee Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت معاش کارمند " . ($employee->name ?? 'Unknown');
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
