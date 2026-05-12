<?php

namespace App\Http\Controllers;

use App\ExpenseDetails;
use App\OfficeCashBook;
use App\OfficeDebit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\OfficeCredit;
use Illuminate\Support\Facades\Auth;

class OfficeCashBookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $credit = OfficeCredit::where('user_role',Auth::user()->role)->sum('amount');
        $cashbook = OfficeCashBook::where('user_role',Auth::user()->role)->first();


        $debits =  OfficeDebit::where('user_role',Auth::user()->role)->orderBy('id','DESC')->paginate(20);
        $debit_sum = OfficeDebit::where('user_role',Auth::user()->role)->sum('amount');
        $pagination = '';
        $expenseEdit = '';
        return view('office-cash-book.recieveds',compact('debits','credit','cashbook','debit_sum','pagination','expenseEdit'));

    }
    public function all_expenses(){
        $credit = OfficeCredit::where('user_role',Auth::user()->role)->sum('amount');
        $cashbook = OfficeCashBook::where('user_role',Auth::user()->role)->first();


        $debits =  OfficeDebit::where('user_role',Auth::user()->role)->orderBy('id','DESC')->paginate(50);
        $debit_sum = OfficeDebit::where('user_role',Auth::user()->role)->sum('amount');
        $expenseType = OfficeDebit::where('user_role',Auth::user()->role)->select('expense_type')->distinct()->get();
        $expenseEdit = '';
        return view('office-cash-book.recieveds',compact('debits','credit','cashbook','expenseType','debit_sum','expenseEdit'));

    }
    public function expense_search(Request $request)
    {
        $credit = OfficeCredit::where('user_role',Auth::user()->role)->sum('amount');
        $cashbook = OfficeCashBook::where('user_role',Auth::user()->role)->first();
        $search = $request->search;
        $start = $request->from_date;
        $end = $request->to_date;

        $debits = OfficeDebit::where('user_role',Auth::user()->role)->where('name','like','%'.$search.'%')->whereBetween('date',[$start,$end])->orderBy('id','DESC')->paginate(50);


        $debit_sum = OfficeDebit::where('user_role',Auth::user()->role)->sum('amount');
        $expenseEdit = '';
        return view('office-cash-book.recieveds',compact('debits','credit','cashbook','search','debit_sum','expenseEdit','start','end'));


    }



    public function expense_search_date_range(Request $request)
    {


        $start = $request->from_date;
        $end = $request->to_date;

        if ($request->expense_type == 'همه مصارف' && $request->search_for_where == 'همه مصارف'){
            $debits = OfficeDebit::where('user_role',Auth::user()->role)->whereBetween('date',[$start,$end])->orderBy('id','DESC')->paginate(50);

        }elseif ($request->expense_type == 'همه مصارف' && $request->search_for_where != 'همه مصارف'){
            $debits = OfficeDebit::where('user_role',Auth::user()->role)->where('expense_for_where',$request->search_for_where)->whereBetween('date',[$start,$end])->orderBy('id','DESC')->paginate(50);

        }elseif ($request->expense_type != 'همه مصارف' && $request->search_for_where == 'همه مصارف'){
            $debits = OfficeDebit::where('user_role',Auth::user()->role)->where('expense_type',$request->expense_type)->whereBetween('date',[$start,$end])->orderBy('id','DESC')->paginate(50);

        }else{
            $debits = OfficeDebit::where('user_role',Auth::user()->role)->where('expense_type',$request->expense_type)->where('expense_for_where',$request->search_for_where)->whereBetween('date',[$start,$end])->orderBy('id','DESC')->paginate(50);

        }

        $search = $request->expense_for_where.' '.$request->expense_type;
        $credit = OfficeCredit::where('user_role',Auth::user()->role)->sum('amount');
        $cashbook = OfficeCashBook::where('user_role',Auth::user()->role)->first();



        $expenseType = OfficeDebit::where('user_role',Auth::user()->role)->select('expense_type')->distinct()->get();
        $debit_sum = OfficeDebit::where('user_role',Auth::user()->role)->sum('amount');
        $expenseEdit = '';
        return view('office-cash-book.recieveds',compact('debits','credit','cashbook','start','end','expenseType','search','search','debit_sum','expenseEdit'));

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OfficeCashBook  $officeCashBook
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $debits = OfficeDebit::find($id);
        $paymentEdit = '';

        $details = ExpenseDetails::where('expense_id',$id)->get();
        return view('office-cash-book.expense-details',compact('debits','details','paymentEdit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OfficeCashBook  $officeCashBook
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $credit = OfficeCredit::where('user_role',Auth::user()->role)->sum('amount');
        $cashbook = OfficeCashBook::where('user_role',Auth::user()->role)->first();


        $debits =  OfficeDebit::where('user_role',Auth::user()->role)->orderBy('id','DESC')->paginate(20);
        $debit_sum = OfficeDebit::where('user_role',Auth::user()->role)->sum('amount');

        $pagination = '';
        $expenseEdit = OfficeDebit::find($id);
        return view('office-cash-book.recieveds',compact('debits','credit','cashbook','debit_sum','pagination','expenseEdit'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OfficeCashBook  $officeCashBook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OfficeCashBook $officeCashBook)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OfficeCashBook  $officeCashBook
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficeCashBook $officeCashBook)
    {
        //
    }
}
