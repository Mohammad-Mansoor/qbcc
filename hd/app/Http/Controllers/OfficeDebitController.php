<?php

namespace App\Http\Controllers;

use App\Activity;
use App\OfficeDebit;
use App\OfficeCashBook;
use App\OfficeEmployee;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OfficeDebitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

    public function search(Request $request)
    {
       $from_date = $request->from_date;
       $to_date = $request->to_date;
            // dd($request->to_date);
       $employee = OfficeEmployee::find($request->employee_id);
       $salary = $employee->employee_salary->last();
       $debits = OfficeDebit::where('employee_id',$request->employee_id)->whereBetween('date',[$from_date,$to_date])->orderBy('id','DESC')->get();
    //    dd($debits);
       $debits_this_month = OfficeDebit::where('employee_id',$request->employee_id)->whereBetween('date',[$from_date,$to_date])->get();

       $paymentEdit = '';
       return view('office-employee-salary.salary-payment',compact('paymentEdit','employee','salary','debits','debits_this_month','from_date' ,'to_date'));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


            $data = $request->validate([
                'name'=> '',
                'amount' => 'required',
                'amount_af' => 'required',
                'description' => 'required|min:3|max:256',
                'date' => 'required',
                'employee_id'=>'',
                'expense_type'=>'',
                'expense_for_where'=>'',
                'user_role' => ''

            ]);
            if($request->employee_id){
                $emp = OfficeEmployee::find($request->employee_id);
                $data['employee_id'] = $request->employee_id;
                $data['name'] = $emp->name;
            }
            $csh = OfficeCashBook::where('user_role',Auth::user()->role)->first();

            if($csh){


                if($csh->balance < $request->amount){
                return redirect()->back()->with('error', ' پول در دخل'. $csh->balance .'میباشد');
                }
                else{
                    $data['user_role'] = Auth::user()->role;
                    $debit = OfficeDebit::create($data);
                    
                      $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " مبلغ " . $request->amount . "  مصرف شد ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();

                    $csh->balance =  $csh->balance - $request->amount;
                    $csh->update();

                    if($debit) {
                        if($request->employee_id){
                            return redirect()->back()->with('status', 'مصرف موفقانه ثبت شد !');
                        }
                        else{
                            return redirect('/dashboard/office-cash-book')->with('status', 'مصرف موفقانه ثبت شد !');
                        }

                    }
                    else{
                        return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
                    }
                }
            }
            else{
                return redirect()->back()->with('error','پول در دخل موجود نیست');
            }

    }

    public function add_new_expense(){
        return  view('office-cash-book.add-expense');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OfficeDebit  $officeDebit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');
        $employee = OfficeEmployee::find($id);
        $salary = $employee->employee_salary->last();
        $debits_this_month = OfficeDebit::where('employee_id',$id)->whereBetween('date',[$start,$end])->get();
        $debits = OfficeDebit::where('employee_id',$id)->orderBy('id','DESC')->get();
        $from_date = '';
        $to_date = '';
        $paymentEdit = '';
        return view('office-employee-salary.salary-payment',compact('paymentEdit','employee','salary','debits','debits_this_month','from_date' , 'to_date'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OfficeDebit  $officeDebit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');
        $paymentEdit = officeDebit::find($id);
        $employee = OfficeEmployee::find($paymentEdit->employee_id);
        $debits_this_month = OfficeDebit::where('employee_id',$paymentEdit->employee_id)->whereBetween('date',[$start,$end])->get();
        $salary = $employee->employee_salary->last();
        $debits =  OfficeDebit::where('employee_id',$paymentEdit->employee_id)->orderBy('id','DESC')->get();
        $from_date = '';
        $to_date = '';
        return view('office-employee-salary.salary-payment', compact('paymentEdit','from_date','to_date','debits','employee','salary','debits_this_month'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OfficeDebit  $officeDebit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $db  = OfficeDebit::find($id);
        $csh = OfficeCashBook::count();
        if($csh > 0){
            $balance = OfficeCashBook::where('user_role',Auth::user()->role)->first();
            $balance->balance = $balance->balance + $db->amount;
            $balance->update();
            if($balance->balance < $request->amount){
                $balance->balance - $db->amount;
                $balance->update();
            return redirect()->back()->with('error', ' پول در دخل'. $balance->balance .'میباشد');
            }
            else{


                $balance->balance = $balance->balance - $request->amount;
                $db->name = $request->name;
                $db->amount = $request->amount;
                $db->expense_type = $request->expense_type;
                $db->expense_for_where = $request->expense_for_where;
                $db->amount_af = $request->amount_af;
                $db->description = $request->description;
                $db->date = $request->date;
                
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مبلغ " . $request->amount . "  مصرف ویرایش شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

                $balance->update();
                $db->update();
               if ($request->employee_id){
                   if($db) {

                       return redirect('/dashboard/expenses/'.$request->employee_id)->with('status', 'پرداخت موفقانه ثبت شد !');
                   }
                   else{
                       return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
                   }
               }
               else{
                   if ($db){
                       return redirect('/dashboard/office-cash-book')->with('status', 'موفقانه ثبت شد!');
                   }
                   else{
                       return redirect('/dashboard/office-cash-book')->with('error', 'مشکل در سرور وجود داره!');
                   }

               }

            }
        }
        else{
            return redirect()->back()->with('error','پول در دخل موجود نیست');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OfficeDebit  $officeDebit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
