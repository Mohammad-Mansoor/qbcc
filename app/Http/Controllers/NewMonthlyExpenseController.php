<?php

namespace App\Http\Controllers;

use App\NewMonthlyExpense;
use App\NewMonthlyExpenseBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewMonthlyExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_monthly_expense')->only(['store']);
        // edit, update, destroy are not implemented here but good to protect if they ever are
        $this->middleware('permission:manage_monthly_expense_payments')->only(['edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountEdit = "";
        $months = NewMonthlyExpense::all();

        return view('new-monthly-expense.monthly-expense-accounts',compact('accountEdit','months'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $exp = DB::table('new_monthly_expenses')->where('month_name',$request->month_name)->where('year_name',$request->year_name)->first();

        if ($exp){
            return redirect('/dashboard/monthly-expense-accounts')->with('error', ' نام ماه و سال از قبل ثبت است !');
        }
        else{
            $account = DB::table('new_monthly_expenses')->insertGetId(['month_name'=>$request->month_name, 'year_name'=>$request->year_name]);

            if ($account) {
                return redirect('/dashboard/monthly-expense-accounts')->with('status', ' موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/monthly-expense-accounts')->with('error', 'مشکل در سرور وجود داره!');
            }
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $month_obj = NewMonthlyExpense::find($id);
        
        $expensesQuery = DB::table('new_monthly_expense_balances')->where('month_id', $id);
        
        if (Auth::user()->role != 'SP') {
            $expensesQuery->where('user_role', Auth::user()->role);
        }
        
        $expensesWithOverrides = (clone $expensesQuery)
            ->leftJoin('chart_of_accounts as debit_acc', 'new_monthly_expense_balances.override_debit_account_id', '=', 'debit_acc.id')
            ->leftJoin('chart_of_accounts as credit_acc', 'new_monthly_expense_balances.override_credit_account_id', '=', 'credit_acc.id')
            ->select('new_monthly_expense_balances.*', 'debit_acc.account_name as debit_account_name', 'debit_acc.account_code as debit_account_code', 'credit_acc.account_name as credit_account_name', 'credit_acc.account_code as credit_account_code');

        $expenses = (clone $expensesWithOverrides)->orderBy('new_monthly_expense_balances.id', 'DESC')->paginate(50);
        $expenses_sp = (clone $expensesWithOverrides)->orderBy('new_monthly_expense_balances.id', 'DESC')->paginate(50); // Legacy variable preservation

        // Forensic Summaries
        $categoryTotals = (clone $expensesQuery)
            ->select('category', 'currency_code', 
                DB::raw('SUM(original_amount) as total_original'),
                DB::raw('SUM(base_amount) as total_base'))
            ->groupBy('category', 'currency_code')
            ->get()
            ->groupBy('category');

        if (request()->export === 'pdf') {
            $logoPath = public_path('images/logo.png');
            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
            return view('new-monthly-expense.summary-pdf', compact('month_obj', 'categoryTotals', 'logoBase64'));
        }

        if (request()->export === 'excel') {
            return view('new-monthly-expense.summary-excel', compact('month_obj', 'categoryTotals'));
        }

        $currencies = \App\Currency::all();
        $expenseEdit = '';
        $search = '';

        $selectionService = new \App\Services\AccountSelectionService();
        $expenseKeys = [
            'EXP_FOOD',
            'EXP_MISC_OFFICE',
            'EXPENSE_کرایه_و_برق',
            'EXP_TRANS',
            'CASH_OUT',
            'EXP_FUEL',
            'PAYROLL_ACCRUAL',
            'EXP_WAGES'
        ];

        $allowedAccountsMap = [];
        foreach ($expenseKeys as $key) {
            $allowedAccountsMap[$key] = [
                'debit' => $selectionService->getValidAccounts($key, 'debit')->map(function($acc) {
                    return ['id' => $acc->id, 'code' => $acc->account_code, 'name' => $acc->account_name];
                })->toArray(),
                'credit' => $selectionService->getValidAccounts($key, 'credit')->map(function($acc) {
                    return ['id' => $acc->id, 'code' => $acc->account_code, 'name' => $acc->account_name];
                })->toArray(),
            ];
        }

        return view('new-monthly-expense.expense-account-payments',
            compact('expenseEdit', 'expenses', 'expenses_sp', 'categoryTotals', 'currencies', 'search', 'month_obj', 'allowedAccountsMap'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NewMonthlyExpense  $newMonthlyExpense
     * @return \Illuminate\Http\Response
     */
    public function edit(NewMonthlyExpense $newMonthlyExpense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NewMonthlyExpense  $newMonthlyExpense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NewMonthlyExpense $newMonthlyExpense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NewMonthlyExpense  $newMonthlyExpense
     * @return \Illuminate\Http\Response
     */
    public function destroy(NewMonthlyExpense $newMonthlyExpense)
    {
        //
    }

}
