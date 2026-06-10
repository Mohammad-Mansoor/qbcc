<?php

namespace App\Http\Controllers;

use App\NewMonthlyExpense;
use App\NewMonthlyExpenseBalance;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewMonthlyExpenseBalanceController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postExpenseToAccounting($expense)
    {
        try {
            $slugMap = [
                'خوراکه' => 'EXP_FOOD',
                'متفرقه دفتر' => 'EXP_MISC_OFFICE',
                'کرایه و برق' => 'EXPENSE_کرایه_و_برق',
                'ترانسپورت' => 'EXP_TRANS',
                'برداشت' => 'CASH_OUT',
                'ترمیمات و تیل' => 'EXP_FUEL',
                'معاشات' => 'PAYROLL_ACCRUAL',
                'اجوره' => 'EXP_WAGES',
            ];
            
            $key = $slugMap[$expense->category] ?? 'EXP_MISC_OFFICE';

            // Determine transaction type based on category mapping rule
            $type = 'expense';
            if ($expense->category === 'برداشت') {
                $type = 'office_debit';
            } elseif ($expense->category === 'معاشات') {
                $type = 'payroll';
            }

            // FORENSIC RULE: Pass original_amount + currency_code so AccountingService
            // performs the USD conversion exactly once (base_amount is already converted;
            // passing it with a non-USD currency_code causes a double-conversion).
            if ($expense->original_amount) {
                $amount       = $expense->original_amount;
                $currencyCode = $expense->currency_code;
                $exchangeRate = $expense->exchange_rate;
            } else {
                // Legacy fallback: amount is already base USD
                $amount       = $expense->amount;
                $currencyCode = $expense->currency_code ?: 'USD';
                $exchangeRate = $expense->exchange_rate ?: 1;
            }

            $this->accountingService->postAutoTransaction($type, $key, [
                'date' => $expense->date,
                'amount' => $amount,
                'currency_code' => $currencyCode,
                'exchange_rate' => $exchangeRate,
                'reference' => 'NEXP-' . $expense->id,
                'description' => $expense->description . " (" . $expense->category . ")",
                'source_type' => 'NewMonthlyExpenseBalance',
                'source_id' => $expense->id,
                'override_debit_account_id' => $expense->override_debit_account_id,
                'override_credit_account_id' => $expense->override_credit_account_id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for New Expense #" . $expense->id . ": " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $this->accountingService->failIfLocked($request->date);
        
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'category' => 'required',
                'month_id' => 'required',
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
            ]);

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;

            // FORENSIC RULE: BCMath Calculation
            $baseAmount = bcmul($request->amount, $rate, 4);

            $expense = new NewMonthlyExpenseBalance();
            $expense->amount = (double)$baseAmount; // Keep for legacy compatibility
            $expense->currency = $request->currency_id; // Legacy ID
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->category = $request->category;
            $expense->dollar_rate = (string)$rate;
            $expense->user_role = Auth::user()->role;
            $expense->month_id = $request->month_id;

            // FORENSIC SNAPSHOTS
            $expense->currency_code = $currency->code;
            $expense->currency_symbol = $currency->symbol;
            $expense->exchange_rate = $rate;
            $expense->original_amount = $request->amount;
            $expense->base_amount = $baseAmount;
            $expense->override_debit_account_id = $request->override_debit_account_id;
            $expense->override_credit_account_id = $request->override_credit_account_id;

            $expense->save();

            // Post to Accounting
            $this->postExpenseToAccounting($expense);

            return redirect()->back()->with('status', 'موفقانه ثبت و در سیستم مالی درج گردید!');
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NewMonthlyExpenseBalance  $newMonthlyExpenseBlanace
     * @return \Illuminate\Http\Response
     */
    public function edit($expense_id)
    {
        $expenseEdit = NewMonthlyExpenseBalance::find($expense_id);
        $month = DB::table('new_monthly_expenses')->where('me_id',$expenseEdit->month_id)->first();

        $expensesWithOverrides = DB::table('new_monthly_expense_balances')
            ->leftJoin('chart_of_accounts as debit_acc', 'new_monthly_expense_balances.override_debit_account_id', '=', 'debit_acc.id')
            ->leftJoin('chart_of_accounts as credit_acc', 'new_monthly_expense_balances.override_credit_account_id', '=', 'credit_acc.id')
            ->select('new_monthly_expense_balances.*', 'debit_acc.account_name as debit_account_name', 'debit_acc.account_code as debit_account_code', 'credit_acc.account_name as credit_account_name', 'credit_acc.account_code as credit_account_code')
            ->where('month_id',$month->me_id);

        $expenses = (clone $expensesWithOverrides)->where('user_role',Auth::user()->role)->orderBy('new_monthly_expense_balances.id','DESC')->paginate(50);
        $expenses_sp = (clone $expensesWithOverrides)->orderBy('new_monthly_expense_balances.id','DESC')->paginate(50);

        $currencies = \App\Currency::all();

        $expensesQuery = DB::table('new_monthly_expense_balances')->where('month_id', $month->me_id);
        if (Auth::user()->role != 'SP') {
            $expensesQuery->where('user_role', Auth::user()->role);
        }
        $categoryTotals = (clone $expensesQuery)
            ->select('category', 'currency_code', 
                DB::raw('SUM(original_amount) as total_original'),
                DB::raw('SUM(base_amount) as total_base'))
            ->groupBy('category', 'currency_code')
            ->get()
            ->groupBy('category');

        // ... stats calculation code ...
        // (Keeping existing UI logic but focusing on accounting integration)
        
        $khoraka_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','خوراکه')->where('currency',1)->sum('amount');
        $khoraka_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','خوراکه')->where('currency',2)->sum('amount');
        $khoraka_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','خوراکه')->where('currency',3)->sum('amount');
        $motafrqa_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','متفرقه دفتر')->where('currency',1)->sum('amount');
        $motafrqa_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','متفرقه دفتر')->where('currency',2)->sum('amount');
        $motafrqa_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','متفرقه دفتر')->where('currency',3)->sum('amount');
        $keraia_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','کرایه و برق')->where('currency',1)->sum('amount');
        $keraia_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','کرایه و برق')->where('currency',2)->sum('amount');
        $keraia_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','کرایه و برق')->where('currency',3)->sum('amount');
        $transport_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','ترانسپورت')->where('currency',1)->sum('amount');
        $transport_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','ترانسپورت')->where('currency',2)->sum('amount');
        $transport_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','ترانسپورت')->where('currency',3)->sum('amount');
        $bardasht_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','برداشت')->where('currency',1)->sum('amount');
        $bardasht_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','برداشت')->where('currency',2)->sum('amount');
        $bardasht_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','برداشت')->where('currency',3)->sum('amount');
        $tel_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','ترمیمات و تیل')->where('currency',1)->sum('amount');
        $tel_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','ترمیمات و تیل')->where('currency',2)->sum('amount');
        $tel_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','ترمیمات و تیل')->where('currency',3)->sum('amount');
        $mashat_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','معاشات')->where('currency',1)->sum('amount');
        $mashat_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','معاشات')->where('currency',2)->sum('amount');
        $mashat_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','معاشات')->where('currency',3)->sum('amount');
        $ajora_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','اجوره')->where('currency',1)->sum('amount');
        $ajora_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','اجوره')->where('currency',2)->sum('amount');
        $ajora_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->where('category','اجوره')->where('currency',3)->sum('amount');

        $khoraka_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','خوراکه')->where('currency',1)->sum('amount');
        $khoraka_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','خوراکه')->where('currency',2)->sum('amount');
        $khoraka_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','خوراکه')->where('currency',3)->sum('amount');
        $motafrqa_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','متفرقه دفتر')->where('currency',1)->sum('amount');
        $motafrqa_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','متفرقه دفتر')->where('currency',2)->sum('amount');
        $motafrqa_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','متفرقه دفتر')->where('currency',3)->sum('amount');
        $keraia_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','کرایه و برق')->where('currency',1)->sum('amount');
        $keraia_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','کرایه و برق')->where('currency',2)->sum('amount');
        $keraia_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','کرایه و برق')->where('currency',3)->sum('amount');
        $transport_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','ترانسپورت')->where('currency',1)->sum('amount');
        $transport_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','ترانسپورت')->where('currency',2)->sum('amount');
        $transport_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','ترانسپورت')->where('currency',3)->sum('amount');
        $bardasht_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','برداشت')->where('currency',1)->sum('amount');
        $bardasht_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','برداشت')->where('currency',2)->sum('amount');
        $bardasht_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','برداشت')->where('currency',3)->sum('amount');
        $tel_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','ترمیمات و تیل')->where('currency',1)->sum('amount');
        $tel_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','ترمیمات و تیل')->where('currency',2)->sum('amount');
        $tel_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','ترمیمات و تیل')->where('currency',3)->sum('amount');
        $mashat_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','معاشات')->where('currency',1)->sum('amount');
        $mashat_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','معاشات')->where('currency',2)->sum('amount');
        $mashat_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','معاشات')->where('currency',3)->sum('amount');
        $ajora_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','اجوره')->where('currency',1)->sum('amount');
        $ajora_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','اجوره')->where('currency',2)->sum('amount');
        $ajora_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('category','اجوره')->where('currency',3)->sum('amount');

        $search ='';
        $month_obj = NewMonthlyExpense::find($month->me_id);

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
            compact('expenseEdit','expenses','expenses_sp',
                'khoraka_af','khoraka_usd','khoraka_cd',
                'motafrqa_af','motafrqa_usd','motafrqa_cd',
                'keraia_af','keraia_usd','keraia_cd',
                'transport_af','transport_usd','transport_cd',
                'bardasht_af','bardasht_usd','bardasht_cd',
                'tel_af','tel_usd','tel_cd',
                'mashat_af','mashat_usd','mashat_cd',
                'ajora_af','ajora_usd','ajora_cd',
                'khoraka_sp_af','khoraka_sp_usd','khoraka_sp_cd',
                'motafrqa_sp_af','motafrqa_sp_usd','motafrqa_sp_cd',
                'keraia_sp_af','keraia_sp_usd','keraia_sp_cd',
                'transport_sp_af','transport_sp_usd','transport_sp_cd',
                'bardasht_sp_af','bardasht_sp_usd','bardasht_sp_cd',
                'tel_sp_af','tel_sp_usd','tel_sp_cd',
                'mashat_sp_af','mashat_sp_usd','mashat_sp_cd',
                'ajora_sp_af','ajora_sp_usd','ajora_sp_cd','search','month_obj','allowedAccountsMap', 'categoryTotals', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NewMonthlyExpenseBalance  $newMonthlyExpenseBlanace
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $expense_id)
    {
        $this->accountingService->failIfLocked($request->date);
        
        return DB::transaction(function () use ($request, $expense_id) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'category' => 'required',
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
            ]);

            $expense = NewMonthlyExpenseBalance::find($expense_id);
            
            // Reversal
            $this->accountingService->reverseTransactionBySource($expense->id, 'New Expense Record Edited');

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;
            $baseAmount = bcmul($request->amount, $rate, 4);

            $expense->amount = (double)$baseAmount; // Legacy
            $expense->currency = $request->currency_id;
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->category = $request->category;
            $expense->dollar_rate = (string)$rate;
            $expense->user_role = Auth::user()->role;

            // FORENSIC SNAPSHOTS
            $expense->currency_code = $currency->code;
            $expense->currency_symbol = $currency->symbol;
            $expense->exchange_rate = $rate;
            $expense->original_amount = $request->amount;
            $expense->base_amount = $baseAmount;
            $expense->override_debit_account_id = $request->override_debit_account_id;
            $expense->override_credit_account_id = $request->override_credit_account_id;

            $expense->update();

            // Re-post
            $this->postExpenseToAccounting($expense);

            return redirect('/dashboard/monthly-expense-accounts/'.$request->month_id)->with('status', 'ویرایش موفقانه ثبت و سیستم مالی بروزرسانی شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NewMonthlyExpenseBalance  $newMonthlyExpenseBlanace
     * @return \Illuminate\Http\Response
     */
    public function destroy($expense_id)
    {
        try {
            return DB::transaction(function () use ($expense_id) {
                $expense = NewMonthlyExpenseBalance::find($expense_id);
                if ($expense) {
                    $this->accountingService->failIfLocked($expense->date);
                    
                    // Reversal
                    $this->accountingService->reverseTransactionBySource($expense->id, 'New Expense Record Deleted');
                    
                    $expense->delete();
                    return response()->json(['status' => 'success', 'message' => 'موفقانه حذف شد']);
                }
                return response()->json(['status' => 'error', 'message' => 'دیتا یافت نشد'], 404);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}
