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
     * @param  \App\NewMonthlyExpense  $newMonthlyExpense
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $month_obj = NewMonthlyExpense::find($id);
        $expenses = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->orderBy('id','DESC')->paginate(50);
        $expenses_sp = DB::table('new_monthly_expense_balances')->where('month_id',$id)->orderBy('id','DESC')->paginate(50);



        $expenseEdit = '';

        $khoraka_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','خوراکه')->where('currency',1)->sum('amount');
        $khoraka_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','خوراکه')->where('currency',2)->sum('amount');
        $khoraka_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','خوراکه')->where('currency',3)->sum('amount');
        $motafrqa_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','متفرقه دفتر')->where('currency',1)->sum('amount');
        $motafrqa_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','متفرقه دفتر')->where('currency',2)->sum('amount');
        $motafrqa_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','متفرقه دفتر')->where('currency',3)->sum('amount');
        $keraia_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','کرایه و برق')->where('currency',1)->sum('amount');
        $keraia_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','کرایه و برق')->where('currency',2)->sum('amount');
        $keraia_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','کرایه و برق')->where('currency',3)->sum('amount');
        $transport_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','ترانسپورت')->where('currency',1)->sum('amount');
        $transport_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','ترانسپورت')->where('currency',2)->sum('amount');
        $transport_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','ترانسپورت')->where('currency',3)->sum('amount');
        $bardasht_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','برداشت')->where('currency',1)->sum('amount');
        $bardasht_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','برداشت')->where('currency',2)->sum('amount');
        $bardasht_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','برداشت')->where('currency',3)->sum('amount');
        $tel_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','ترمیمات و تیل')->where('currency',1)->sum('amount');
        $tel_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','ترمیمات و تیل')->where('currency',2)->sum('amount');
        $tel_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','ترمیمات و تیل')->where('currency',3)->sum('amount');
        $mashat_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','معاشات')->where('currency',1)->sum('amount');
        $mashat_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','معاشات')->where('currency',2)->sum('amount');
        $mashat_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','معاشات')->where('currency',3)->sum('amount');
        $ajora_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','اجوره')->where('currency',1)->sum('amount');
        $ajora_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','اجوره')->where('currency',2)->sum('amount');
        $ajora_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('user_role',Auth::user()->role)->where('category','اجوره')->where('currency',3)->sum('amount');


        $khoraka_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','خوراکه')->where('currency',1)->sum('amount');
        $khoraka_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','خوراکه')->where('currency',2)->sum('amount');
        $khoraka_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','خوراکه')->where('currency',3)->sum('amount');
        $motafrqa_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','متفرقه دفتر')->where('currency',1)->sum('amount');
        $motafrqa_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','متفرقه دفتر')->where('currency',2)->sum('amount');
        $motafrqa_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','متفرقه دفتر')->where('currency',3)->sum('amount');
        $keraia_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','کرایه و برق')->where('currency',1)->sum('amount');
        $keraia_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','کرایه و برق')->where('currency',2)->sum('amount');
        $keraia_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','کرایه و برق')->where('currency',3)->sum('amount');
        $transport_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','ترانسپورت')->where('currency',1)->sum('amount');
        $transport_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','ترانسپورت')->where('currency',2)->sum('amount');
        $transport_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','ترانسپورت')->where('currency',3)->sum('amount');
        $bardasht_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','برداشت')->where('currency',1)->sum('amount');
        $bardasht_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','برداشت')->where('currency',2)->sum('amount');
        $bardasht_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','برداشت')->where('currency',3)->sum('amount');
        $tel_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','ترمیمات و تیل')->where('currency',1)->sum('amount');
        $tel_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','ترمیمات و تیل')->where('currency',2)->sum('amount');
        $tel_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','ترمیمات و تیل')->where('currency',3)->sum('amount');
        $mashat_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','معاشات')->where('currency',1)->sum('amount');
        $mashat_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','معاشات')->where('currency',2)->sum('amount');
        $mashat_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','معاشات')->where('currency',3)->sum('amount');
        $ajora_sp_af = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','اجوره')->where('currency',1)->sum('amount');
        $ajora_sp_usd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','اجوره')->where('currency',2)->sum('amount');
        $ajora_sp_cd = DB::table('new_monthly_expense_balances')->where('month_id',$id)->where('category','اجوره')->where('currency',3)->sum('amount');

        $search ='';



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
