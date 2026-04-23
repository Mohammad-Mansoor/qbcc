<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ExpenseDetails;
use App\OfficeDebit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseDetailsController extends Controller
{
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'expense_id' => 'required',
        ]);
        $expense = ExpenseDetails::create($data);
        if($expense) {
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " جزییات مصرف به مبلغ " . $request->amount . " در سیستم ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            return redirect()->back()->with('status', 'پرداخت موفقانه ثبت شد !');
        }
        else{
            return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExpenseDetails $expenseDetails
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseDetails $expenseDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ExpenseDetails $expenseDetails
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $paymentEdit = ExpenseDetails::find($id);
        $debits = OfficeDebit::find($paymentEdit->expense_id);

        $details = ExpenseDetails::where('expense_id',$paymentEdit->expense_id)->get();
        return view('office-cash-book.expense-details',compact('debits','details','paymentEdit'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ExpenseDetails $expenseDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'expense_id' => 'required',
        ]);
        $expense = ExpenseDetails::find($id);
        $expense->update($data);
        if($expense) {
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " جزییات مصرف به مبلغ " . $request->amount . " در سیستم ویرایش شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            return redirect('/dashboard/office-cash-book/'.$request->expense_id)->with('status', 'پرداخت موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/office-cash-book/'.$request->expense_id)->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExpenseDetails $expenseDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseDetails $expenseDetails)
    {
        //
    }
}
