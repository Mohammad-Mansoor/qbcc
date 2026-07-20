<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Services\AccountingService;
use App\Services\AccountSelectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('permission:view_payroll')->only(['index', 'show']);
        $this->middleware('permission:run_payroll')->only(['create', 'store']);
        $this->middleware('permission:view_payroll_slip')->only(['slip']);
    }

    // ─── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $runs = DB::table('payroll_runs')
            ->orderBy('run_date', 'desc')
            ->paginate(12);

        // Aggregate totals for header dashboard
        $totalRuns     = DB::table('payroll_runs')->count();
        $totalPaidUSD  = DB::table('payroll_runs')->sum('total_amount');
        $currentMonth  = Carbon::now()->format('m-Y');
        $thisMonthRun  = DB::table('payroll_runs')->where('month_year', $currentMonth)->first();

        return view('payroll.index', compact('runs', 'totalRuns', 'totalPaidUSD', 'thisMonthRun', 'currentMonth'));
    }

    // ─── Create ─────────────────────────────────────────────────────────────────

    public function create()
    {
        $today = Carbon::today();

        // Join employees with their latest salary contract
        $employees = DB::table('office_employees')
            ->leftJoin('employee_salaries', function ($join) {
                $join->on('office_employees.id', '=', 'employee_salaries.employee_id');
            })
            ->leftJoin('currencies', 'employee_salaries.currency_id', '=', 'currencies.id')
            ->select(
                'office_employees.id',
                'office_employees.name',
                'office_employees.job_title',
                'employee_salaries.salary as base_salary',
                'employee_salaries.contract_number',
                'employee_salaries.from_date',
                'employee_salaries.to_date',
                'employee_salaries.currency_id',
                'employee_salaries.currency_code',
                'employee_salaries.exchange_rate',
                'employee_salaries.salary_usd as base_salary_usd',
                'currencies.symbol as currency_symbol',
                DB::raw("CASE WHEN employee_salaries.to_date < '" . $today->format('Y-m-d') . "' THEN 1 ELSE 0 END as contract_expired"),
                DB::raw("CASE WHEN employee_salaries.id IS NULL THEN 1 ELSE 0 END as no_contract")
            )
            ->orderByRaw('employee_salaries.id DESC')
            ->get()
            ->unique('id'); // One row per employee (latest salary)

        $allowedDebitAccounts  = \App\ChartOfAccount::all();
        $allowedCreditAccounts = \App\ChartOfAccount::all();
        $mapping               = \App\MappingRule::where('mapping_key', 'PAYROLL_ACCRUAL')->first();
        $currencies            = \App\Currency::where('is_active', 1)->get();

        return view('payroll.create', compact(
            'employees', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'
        ));
    }

    // ─── Store ──────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'month_year'  => 'required',
            'run_date'    => 'required|date',
            'employees'   => 'required|array',
        ]);

        // Check duplicate month_year
        $exists = DB::table('payroll_runs')->where('month_year', $request->month_year)->exists();
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'این دوره معاشاتی (' . $request->month_year . ') قبلاً ثبت گردیده است. شما نمی‌توانید برای یک ماه دو بار معاش اجرا کنید.');
        }

        return DB::transaction(function () use ($request) {

            // Fiscal lock check
            $this->accountingService->failIfLocked($request->run_date);

            $totalAmountUSD = 0;
            $items = [];

            foreach ($request->employees as $empId => $data) {
                if (!isset($data['include'])) continue;

                // Resolve currency for this employee's contract
                $currency = \App\Currency::find($data['currency_id'] ?? null);
                if (!$currency) {
                    $currency = \App\Currency::where('is_base_currency', 1)->first();
                }
                $exchangeRate = (float)($data['exchange_rate'] ?? $currency->exchange_rate ?? 1);

                $baseSalary   = (float)($data['base_salary'] ?? 0);
                $bonus        = (float)($data['bonus'] ?? 0);
                $deductions   = (float)($data['deductions'] ?? 0);
                $netSalary    = $baseSalary + $bonus - $deductions;
                $netSalaryUSD = bcmul((string)$netSalary, (string)$exchangeRate, 4);

                $totalAmountUSD = bcadd((string)$totalAmountUSD, (string)$netSalaryUSD, 4);

                $items[] = [
                    'employee_id'    => $empId,
                    'base_salary'    => $baseSalary,
                    'base_salary_usd'=> bcmul((string)$baseSalary, (string)$exchangeRate, 4),
                    'bonus'          => $bonus,
                    'deductions'     => $deductions,
                    'net_salary'     => $netSalary,
                    'net_salary_usd' => $netSalaryUSD,
                    'currency_id'    => $currency->id,
                    'currency_code'  => $currency->code,
                    'exchange_rate'  => $exchangeRate,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            if (empty($items)) {
                return redirect()->back()->with('error', 'هیچ کارمندی انتخاب نشده است.');
            }

            // Create payroll run header (total in USD)
            $runId = DB::table('payroll_runs')->insertGetId([
                'month_year'   => $request->month_year,
                'run_date'     => $request->run_date,
                'total_amount' => $totalAmountUSD,
                'status'       => 'posted',
                'created_by'   => Auth::id(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Stamp the run_id on each item and insert
            foreach ($items as &$item) {
                $item['payroll_run_id'] = $runId;
            }
            DB::table('payroll_items')->insert($items);

            // POST TO ACCOUNTING (ACCRUAL: DR Salary Expense / CR Salary Payable)
            // Instead of a lump sum, we create individual entries per employee for their statement.
            $rule = \App\MappingRule::where('mapping_key', 'PAYROLL_ACCRUAL')->first();
            $debitAccountId = $request->override_debit_account_id ?? $rule->debit_account_id;
            $creditAccountId = $request->override_credit_account_id ?? $rule->credit_account_id;

            $debitAcc = \App\ChartOfAccount::find($debitAccountId);
            $creditAcc = \App\ChartOfAccount::find($creditAccountId);

            $ledgerEntries = [];

            foreach ($items as $item) {
                // Debit Entry (Expense)
                $ledgerEntries[] = [
                    'account_id' => $debitAccountId,
                    'debit' => $item['net_salary'],
                    'credit' => 0,
                    'currency_code' => $item['currency_code'],
                    'exchange_rate' => $item['exchange_rate'],
                    'party_type' => 'App\OfficeEmployee',
                    'party_id' => $item['employee_id'],
                ];

                // Credit Entry (Payable/Cash)
                $ledgerEntries[] = [
                    'account_id' => $creditAccountId,
                    'debit' => 0,
                    'credit' => $item['net_salary'],
                    'currency_code' => $item['currency_code'],
                    'exchange_rate' => $item['exchange_rate'],
                    'party_type' => 'App\OfficeEmployee',
                    'party_id' => $item['employee_id'],
                ];
            }

            $this->accountingService->postTransaction([
                'date' => $request->run_date,
                'reference' => 'PAY-' . $request->month_year,
                'description' => 'Salary Accrual for ' . $request->month_year,
                'source_type' => 'PayrollRun',
                'source_id' => $runId,
                'mapping_key' => 'PAYROLL_ACCRUAL',
                'journal_type' => 'payment',
                'entries' => $ledgerEntries
            ]);


            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = 'ثبت معاشات ماه وار برای تاریخ ' . $request->month_year . ' — مجموع: $' . number_format($totalAmountUSD, 2);
            $activity->user_id = Auth::id();
            $activity->save();

            return redirect()->route('payroll.index')->with('status', 'معاشات با موفقیت ثبت و در دفتر کل درج شد. مجموع USD: $' . number_format($totalAmountUSD, 2));
        });
    }

    // ─── Show ───────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $run = DB::table('payroll_runs')->where('id', $id)->first();
        if (!$run) abort(404);

        $items = DB::table('payroll_items')
            ->join('office_employees', 'payroll_items.employee_id', '=', 'office_employees.id')
            ->leftJoin('employee_departments', 'office_employees.department_id', '=', 'employee_departments.id')
            ->where('payroll_run_id', $id)
            ->select(
                'payroll_items.*',
                'office_employees.name',
                'office_employees.job_title',
                'employee_departments.department'
            )
            ->paginate(50);

        // Aggregates
        $totals = DB::table('payroll_items')->where('payroll_run_id', $id)->selectRaw(
            'SUM(base_salary_usd) as total_base_usd,
             SUM(bonus) as total_bonus,
             SUM(deductions) as total_deductions,
             SUM(net_salary_usd) as total_net_usd,
             COUNT(*) as headcount'
        )->first();

        return view('payroll.show', compact('run', 'items', 'totals'));
    }

    // ─── Salary Slip (Print) ────────────────────────────────────────────────────

    public function slip($runId, $itemId)
    {
        $run  = DB::table('payroll_runs')->where('id', $runId)->first();
        $item = DB::table('payroll_items')
            ->join('office_employees', 'payroll_items.employee_id', '=', 'office_employees.id')
            ->leftJoin('employee_departments', 'office_employees.department_id', '=', 'employee_departments.id')
            ->where('payroll_items.id', $itemId)
            ->where('payroll_items.payroll_run_id', $runId)
            ->select(
                'payroll_items.*',
                'office_employees.name',
                'office_employees.job_title',
                'office_employees.email',
                'office_employees.phone',
                'employee_departments.department'
            )
            ->first();

        if (!$run || !$item) abort(404);

        return view('payroll.slip', compact('run', 'item'));
    }
}
