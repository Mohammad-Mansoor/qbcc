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
                'متفرقه دفتر' => 'EXP_MISC',
                'کرایه و برق' => 'EXP_RENT',
                'ترانسپورت' => 'EXP_TRANS',
                'برداشت' => 'CASH_OUT',
                'ترمیمات و تیل' => 'EXP_FUEL',
                'معاشات' => 'PAYROLL_ACCRUAL',
                'اجوره' => 'EXP_WAGES',
            ];
            
            $key = $slugMap[$expense->category] ?? 'EXP_MISC';

            $this->accountingService->postAutoTransaction('expense', $key, [
                'date' => $expense->date,
                'amount' => $expense->amount,
                'reference' => 'NEXP-' . $expense->id,
                'description' => $expense->description . " (" . $expense->category . ")",
                'source_type' => 'NewMonthlyExpenseBalance',
                'source_id' => $expense->id,
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
            $data = $request->validate([
                'amount' => 'required',
                'currency' => 'required',
                'description' => 'required',
                'date' => 'required',
                'category' => 'required',
                'dollar_rate' => 'required',
                'user_role' => ''
            ]);

            $expense = new NewMonthlyExpenseBalance();
            $expense->amount = $request->amount;
            $expense->currency = $request->currency;
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->category = $request->category;
            $expense->dollar_rate = $request->dollar_rate;
            $expense->user_role = Auth::user()->role;
            $expense->month_id = $request->month_id;
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

        $expenses = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->where('user_role',Auth::user()->role)->orderBy('id','DESC')->paginate(50);
        $expenses_sp = DB::table('new_monthly_expense_balances')->where('month_id',$month->me_id)->orderBy('id','DESC')->paginate(50);

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
                'ajora_sp_af','ajora_sp_usd','ajora_sp_cd','search','month_obj'));
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
                'amount' => 'required',
                'currency' => 'required',
                'description' => 'required',
                'date' => 'required',
                'category' => 'required',
                'dollar_rate' => 'required',
            ]);

            $expense = NewMonthlyExpenseBalance::find($expense_id);
            
            // Reversal
            $this->accountingService->reverseTransactionBySource($expense->id, 'New Expense Record Edited');

            $expense->amount = $request->amount;
            $expense->currency = $request->currency;
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->category = $request->category;
            $expense->dollar_rate = $request->dollar_rate;
            $expense->user_role = Auth::user()->role;
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
