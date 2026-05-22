<?php

namespace App\Http\Controllers;

use App\NewMonthlyExpense;
use App\NewMonthlyExpenseBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewMonthlyExpenseController extends Controller
{
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
        
        $expenses = (clone $expensesQuery)->orderBy('id', 'DESC')->paginate(50);
        $expenses_sp = (clone $expensesQuery)->orderBy('id', 'DESC')->paginate(50); // Legacy variable preservation

        // Forensic Summaries
        $categoryTotals = (clone $expensesQuery)
            ->select('category', 'currency_code', 
                DB::raw('SUM(original_amount) as total_original'),
                DB::raw('SUM(base_amount) as total_base'))
            ->groupBy('category', 'currency_code')
            ->get()
            ->groupBy('category');

        $currencies = \App\Currency::all();
        $expenseEdit = '';
        $search = '';

        return view('new-monthly-expense.expense-account-payments',
            compact('expenseEdit', 'expenses', 'expenses_sp', 'categoryTotals', 'currencies', 'search', 'month_obj'));
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
