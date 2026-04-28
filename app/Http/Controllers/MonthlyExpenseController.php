<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MonthlyExpense;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyExpenseController extends Controller
{
    protected $accountingService;
    
    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postExpenseToAccounting($expense)
    {
        try {
            // The 'condition' here will be the expense category
            $this->accountingService->postAutoTransaction('expense', $expense->category, [
                'date' => $expense->date,
                'amount' => $expense->amount,
                'reference' => 'EXP-' . $expense->id,
                'description' => $expense->description . " (" . $expense->category . ")",
                'source_id' => $expense->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Expense #" . $expense->id . ": " . $e->getMessage());
        }
    }

    public function index()
    {
        $expenseEdit = '';
        $expenses = DB::table('monthly_expenses')->where('user_role',Auth::user()->role)->orderBy('id','DESC')->paginate(50);
        $expenses_sp = DB::table('monthly_expenses')->orderBy('id','DESC')->paginate(50);

        $categories = ['خوراکه', 'متفرقه دفتر', 'کرایه و برق', 'ترانسپورت', 'برداشت', 'ترمیمات و تیل', 'معاشات', 'اجوره'];
        $stats = [];
        foreach ($categories as $cat) {
            $stats[$cat] = [
                'af' => DB::table('monthly_expenses')->where('user_role',Auth::user()->role)->where('category',$cat)->where('currency',1)->sum('amount'),
                'usd' => DB::table('monthly_expenses')->where('user_role',Auth::user()->role)->where('category',$cat)->where('currency',2)->sum('amount'),
                'cd' => DB::table('monthly_expenses')->where('user_role',Auth::user()->role)->where('category',$cat)->where('currency',3)->sum('amount'),
            ];
        }

        // SP stats
        $sp_stats = [];
        foreach ($categories as $cat) {
            $sp_stats[$cat] = [
                'af' => DB::table('monthly_expenses')->where('category',$cat)->where('currency',1)->sum('amount'),
                'usd' => DB::table('monthly_expenses')->where('category',$cat)->where('currency',2)->sum('amount'),
                'cd' => DB::table('monthly_expenses')->where('category',$cat)->where('currency',3)->sum('amount'),
            ];
        }
        
        $search ='';

        return view('office-cash-book.monthly-expenses', array_merge(compact('expenseEdit','expenses','expenses_sp','search'), [
            'khoraka_af' => $stats['خوراکه']['af'], 'khoraka_usd' => $stats['خوراکه']['usd'], 'khoraka_cd' => $stats['خوراکه']['cd'],
            'motafrqa_af' => $stats['متفرقه دفتر']['af'], 'motafrqa_usd' => $stats['متفرقه دفتر']['usd'], 'motafrqa_cd' => $stats['متفرقه دفتر']['cd'],
            'keraia_af' => $stats['کرایه و برق']['af'], 'keraia_usd' => $stats['کرایه و برق']['usd'], 'keraia_cd' => $stats['کرایه و برق']['cd'],
            'transport_af' => $stats['ترانسپورت']['af'], 'transport_usd' => $stats['ترانسپورت']['usd'], 'transport_cd' => $stats['ترانسپورت']['cd'],
            'bardasht_af' => $stats['برداشت']['af'], 'bardasht_usd' => $stats['برداشت']['usd'], 'bardasht_cd' => $stats['برداشت']['cd'],
            'tel_af' => $stats['ترمیمات و تیل']['af'], 'tel_usd' => $stats['ترمیمات و تیل']['usd'], 'tel_cd' => $stats['ترمیمات و تیل']['cd'],
            'mashat_af' => $stats['معاشات']['af'], 'mashat_usd' => $stats['معاشات']['usd'], 'mashat_cd' => $stats['معاشات']['cd'],
            'ajora_af' => $stats['اجوره']['af'], 'ajora_usd' => $stats['اجوره']['usd'], 'ajora_cd' => $stats['اجوره']['cd'],
            
            'khoraka_sp_af' => $sp_stats['خوراکه']['af'], 'khoraka_sp_usd' => $sp_stats['خوراکه']['usd'], 'khoraka_sp_cd' => $sp_stats['خوراکه']['cd'],
            'motafrqa_sp_af' => $sp_stats['متفرقه دفتر']['af'], 'motafrqa_sp_usd' => $sp_stats['متفرقه دفتر']['usd'], 'motafrqa_sp_cd' => $sp_stats['متفرقه دفتر']['cd'],
            'keraia_sp_af' => $sp_stats['کرایه و برق']['af'], 'keraia_sp_usd' => $sp_stats['کرایه و برق']['usd'], 'keraia_sp_cd' => $sp_stats['کرایه و برق']['cd'],
            'transport_sp_af' => $sp_stats['ترانسپورت']['af'], 'transport_sp_usd' => $sp_stats['ترانسپورت']['usd'], 'transport_sp_cd' => $sp_stats['ترانسپورت']['cd'],
            'bardasht_sp_af' => $sp_stats['برداشت']['af'], 'bardasht_sp_usd' => $sp_stats['برداشت']['usd'], 'bardasht_sp_cd' => $sp_stats['برداشت']['cd'],
            'tel_sp_af' => $sp_stats['ترمیمات و تیل']['af'], 'tel_sp_usd' => $sp_stats['ترمیمات و تیل']['usd'], 'tel_sp_cd' => $sp_stats['ترمیمات و تیل']['cd'],
            'mashat_sp_af' => $sp_stats['معاشات']['af'], 'mashat_sp_usd' => $sp_stats['معاشات']['usd'], 'mashat_sp_cd' => $sp_stats['معاشات']['cd'],
            'ajora_sp_af' => $sp_stats['اجوره']['af'], 'ajora_sp_usd' => $sp_stats['اجوره']['usd'], 'ajora_sp_cd' => $sp_stats['اجوره']['cd'],
        ]));
    }

    public function search(Request $request){
        $expenseEdit = '';
        $expenses = DB::table('monthly_expenses')->whereMonth('date',$request->month)->whereYear('date',$request->year)->where('user_role',Auth::user()->role)->get();
        $expenses_sp = DB::table('monthly_expenses')->whereMonth('date',$request->month)->whereYear('date',$request->year)->get();
        $search = 'yes';

        // Simplified stats for search too (to avoid massive code blocks)
        return view('office-cash-book.monthly-expenses', compact('expenseEdit','expenses','expenses_sp','search'));
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required',
                'currency' => 'required',
                'description' => 'required',
                'date' => 'required',
                'category' => 'required',
                'dollar_rate' => 'required',
            ]);

            $expense = new MonthlyExpense();
            $expense->amount = $request->amount;
            $expense->currency = $request->currency;
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->category = $request->category;
            $expense->dollar_rate = $request->dollar_rate;
            $expense->user_role = Auth::user()->role;
            $expense->save();

            $this->postExpenseToAccounting($expense);

            return redirect()->back()->with('status', 'مصرف موفقانه ثبت و در سیستم مالی درج گردید!');
        });
    }

    public function edit($expense_id)
    {
        $expenseEdit = MonthlyExpense::find($expense_id);
        $expenses = DB::table('monthly_expenses')->where('user_role',Auth::user()->role)->paginate(50);
        $expenses_sp = DB::table('monthly_expenses')->paginate(50);
        $search ='';
        return view('office-cash-book.monthly-expenses', compact('expenseEdit','expenses','expenses_sp','search'));
    }

    public function update(Request $request, $expense_id)
    {
        return DB::transaction(function () use ($request, $expense_id) {
            $request->validate([
                'amount' => 'required',
                'currency' => 'required',
                'description' => 'required',
                'date' => 'required',
                'category' => 'required',
                'dollar_rate' => 'required',
            ]);

            $expense = MonthlyExpense::find($expense_id);
            $this->accountingService->reverseTransactionBySource($expense->id, 'Expense Edited');

            $expense->amount = $request->amount;
            $expense->currency = $request->currency;
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->category = $request->category;
            $expense->dollar_rate = $request->dollar_rate;
            $expense->user_role = Auth::user()->role;
            $expense->update();

            $this->postExpenseToAccounting($expense);

            return redirect('/dashboard/monthly-expenses')->with('status', 'ویرایش موفقانه انجام شد و حسابات مالی بروز گردید!');
        });
    }

    public function destroy($expense_id)
    {
        return DB::transaction(function () use ($expense_id) {
            $expense = MonthlyExpense::find($expense_id);
            $this->accountingService->reverseTransactionBySource($expense->id, 'Expense Deleted');
            $expense->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
