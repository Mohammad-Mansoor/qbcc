<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Services\AccountingService;
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
    }

    public function index()
    {
        $runs = DB::table('payroll_runs')->orderBy('run_date', 'desc')->paginate(12);
        return view('payroll.index', compact('runs'));
    }

    public function create()
    {
        $employees = DB::table('office_employees')
            ->join('employee_salaries', 'office_employees.id', '=', 'employee_salaries.employee_id')
            ->select('office_employees.*', 'employee_salaries.salary as base_salary')
            ->get();
            
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PAYROLL_ACCRUAL', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PAYROLL_ACCRUAL', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PAYROLL_ACCRUAL')->first();

        return view('payroll.create', compact('employees', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'));
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'month_year' => 'required',
                'run_date' => 'required|date',
            ]);

            $totalAmount = 0;
            $items = [];
            
            foreach ($request->employees as $empId => $data) {
                if (!isset($data['include'])) continue;
                
                $net = $data['base_salary'] + ($data['bonus'] ?? 0) - ($data['deductions'] ?? 0);
                $totalAmount += $net;
                
                $items[] = [
                    'employee_id' => $empId,
                    'base_salary' => $data['base_salary'],
                    'bonus' => $data['bonus'] ?? 0,
                    'deductions' => $data['deductions'] ?? 0,
                    'net_salary' => $net,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($items)) {
                return redirect()->back()->with('error', 'هیچ کارمندی انتخاب نشده است.');
            }

            $runId = DB::table('payroll_runs')->insertGetId([
                'month_year' => $request->month_year,
                'run_date' => $request->run_date,
                'total_amount' => $totalAmount,
                'status' => 'posted',
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as &$item) {
                $item['payroll_run_id'] = $runId;
            }
            DB::table('payroll_items')->insert($items);

            // POST TO ACCOUNTING (ACCRUAL: DR Salary Expense / CR Salary Payable)
            $this->accountingService->postAutoTransaction('payroll', 'PAYROLL_ACCRUAL', [
                'date' => $request->run_date,
                'amount' => $totalAmount,
                'reference' => 'PAY-' . $request->month_year,
                'description' => 'Salary Accrual for ' . $request->month_year,
                'source_type' => 'PayrollRun',
                'source_id' => $runId,
                'override_debit_account_id' => $request->override_debit_account_id,
                'override_credit_account_id' => $request->override_credit_account_id,
            ]);

            Activity::create([
                'date' => Carbon::today()->format('Y-m-d'),
                'description' => "ثبت معاشات ماه وار برای تاریخ " . $request->month_year,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('payroll.index')->with('status', 'معاشات با موفقیت ثبت و در دفتر کل درج شد.');
        });
    }

    public function show($id)
    {
        $run = DB::table('payroll_runs')->where('id', $id)->first();
        $items = DB::table('payroll_items')
            ->join('office_employees', 'payroll_items.employee_id', '=', 'office_employees.id')
            ->where('payroll_run_id', $id)
            ->select('payroll_items.*', 'office_employees.name')
            ->paginate(50);
            
        return view('payroll.show', compact('run', 'items'));
    }
}
