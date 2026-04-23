<?php

namespace App\Http\Controllers;

use App\Activity;
use App\EmployeePayment;
use App\OfficeEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeePaymentController extends Controller
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

    public function request_list()
    {
        $requests = EmployeePayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('office-employee.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = EmployeePayment::find($id);


        $payment->status = 1 ;
        $payment->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        if ($payment->amount > 0){
            $activity->description = " مبلغ " . $payment->amount . "دالر توسط سوپر ادمین اپروف شد ";
        }
        else{
            $activity->description = " مبلغ " . $payment->amount_af . "افغانی توسط سوپر ادمین اپروف شد ";
        }

        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }
    public function delete_request($id){
        $credit = EmployeePayment::find($id);


        $credit->delete();


        return response()->json(['status','error']);
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
        $data = $request->validate([
            'contract_number' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'employee_id' => '',
            'dollar_rate' => '',
        ]);
        $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();

        if($request->money_type == 'دالر'){

            $payed = new EmployeePayment();
            $payed->contract_number = $request->contract_number;
            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->employee_id = $request->employee_id;
            $payed->dollar_rate = $request->dollar_rate;

            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();


            if ($payed) {

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " کارمند به نام  " . $employee_name->name . ' اکونت نمبر '. $employee_name->id. " به مبلغ " . $request->amount . " دالر را " . $request->type . 'کرد';
                $activity->user_id = Auth::user()->id;
                $activity->save();


                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed = new EmployeePayment();
            $payed->contract_number = $request->contract_number;
            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->employee_id = $request->employee_id;
            $payed->dollar_rate = $request->dollar_rate;

            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();
            if ($payed) {

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مشتری به نام  " . $employee_name->name .  ' اکونت نمبر '. $employee_name->id. " به مبلغ " . $request->amount . " افغانی را " . $request->type . 'کرد';
                $activity->user_id = Auth::user()->id;
                $activity->save();

                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeePayment  $employeePayment
     * @return \Illuminate\Http\Response
     */
    public function show($employee_id)
    {

        $employee = OfficeEmployee::find($employee_id);
        $contract_number = $employee->employee_salary->last();
        $debits_us = EmployeePayment::where('type','=','گرفت')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount');
        $debits_af = EmployeePayment::where('type','=','گرفت')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type','=','رسید')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount');
        $credit_af = EmployeePayment::where('type','=','رسید')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount_af');

        $payments = EmployeePayment::where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->orderBy('created_at','DESC')->paginate(30);
        $paymentEdit = '';
        $contract_number_list  = $employee->employee_salary;
        return view('office-employee.employee-payment',compact('employee','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','contract_number','contract_number_list'));
    }

    public function show_all_payment($employee_id){
        $employee = OfficeEmployee::find($employee_id);
        $contract_number = $employee->employee_salary->last();
        $debits_us = EmployeePayment::where('type','=','گرفت')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount');
        $debits_af = EmployeePayment::where('type','=','گرفت')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type','=','رسید')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount');
        $credit_af = EmployeePayment::where('type','=','رسید')->where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->where('status',1)->sum('amount_af');

        $payments = EmployeePayment::where('employee_id',$employee_id)->where('contract_number',$contract_number->contract_number)->orderBy('created_at','DESC')->get();
        $paymentEdit = '';
        $all = '';
        $contract_number_list  = $employee->employee_salary;
        return view('office-employee.employee-payment',compact('employee','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','contract_number','all','contract_number_list'));

    }
    public function show_contract_payment(Request $request){
        $employee_id = $request->employee_id;
        $employee = OfficeEmployee::find($employee_id);
        $contract_number = $employee->employee_salary->last();
        $debits_us = EmployeePayment::where('type','=','گرفت')->where('employee_id',$employee_id)->where('contract_number',$request->contract_number)->where('status',1)->sum('amount');
        $debits_af = EmployeePayment::where('type','=','گرفت')->where('employee_id',$employee_id)->where('contract_number',$request->contract_number)->where('status',1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type','=','رسید')->where('employee_id',$employee_id)->where('contract_number',$request->contract_number)->where('status',1)->sum('amount');
        $credit_af = EmployeePayment::where('type','=','رسید')->where('employee_id',$employee_id)->where('contract_number',$request->contract_number)->where('status',1)->sum('amount_af');

        $payments = EmployeePayment::where('employee_id',$employee_id)->where('contract_number',$request->contract_number)->orderBy('created_at','DESC')->paginate(30);
        $paymentEdit = '';
        $contract_number_list  = $employee->employee_salary;
        $check_contract = '';
        return view('office-employee.employee-payment',compact('employee','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','contract_number','contract_number_list','check_contract'));

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeePayment  $employeePayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = EmployeePayment::find($payment_id);
        $employee = OfficeEmployee::find($paymentEdit->employee_id);
        $debits_us = EmployeePayment::where('type','=','گرفت')->where('employee_id',$paymentEdit->employee_id)->where('status',1)->sum('amount');
        $debits_af = EmployeePayment::where('type','=','گرفت')->where('employee_id',$paymentEdit->employee_id)->where('status',1)->sum('amount_af');
        $credit_us = EmployeePayment::where('type','=','رسید')->where('employee_id',$paymentEdit->employee_id)->where('status',1)->sum('amount');
        $credit_af = EmployeePayment::where('type','=','رسید')->where('employee_id',$paymentEdit->employee_id)->where('status',1)->sum('amount_af');
        $contract_number = $employee->employee_salary->last();
        $payments = EmployeePayment::where('employee_id',$paymentEdit->employee_id)->where('contract_number',$contract_number->contract_number)->orderBy('created_at','DESC')->paginate(30);

        $contract_number_list  = $employee->employee_salary;
        return view('office-employee.employee-payment',compact('employee','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','contract_number','contract_number_list'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeePayment  $employeePayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        $data = $request->validate([
            'contract_number' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'employee_id' => '',
            'dollar_rate' => '',

        ]);

        $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();


        if($request->money_type == 'دالر'){

            $payed = EmployeePayment::find($payment_id);

            $amount = '';
            $money = '';
            if ($payed->amount > 0) {
                $amount = $payed->amount;
                $money = 'دالر';
            } else {
                $money = 'افغانی';
                $amount = $payed->amount_af;
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " در بیلانس کارمند به نام  " . $employee_name->name .  ' اکونت نمبر '. $employee_name->id. "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود  ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->contract_number = $request->contract_number;
            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->employee_id = $request->employee_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/employee-payments/'.$request->employee_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/employee-payments/'.$request->employee_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
        else{
            $payed =EmployeePayment::find($payment_id);


            $amount = '';
            $money = '';
            if ($payed->amount > 0) {
                $amount = $payed->amount;
                $money = 'دالر';
            } else {
                $money = 'افغانی';
                $amount = $payed->amount_af;
            }
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " در بیلانس کارمند به نام  " . $employee_name->name .  ' اکونت نمبر '. $employee_name->id. "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->contract_number = $request->contract_number;
            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->employee_id = $request->employee_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/employee-payments/'.$request->employee_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/employee-payments/'.$request->employee_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeePayment  $employeePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = EmployeePayment::find($id);

        $employee_name = DB::table('office_employees')->where('id', $payment->employee_id)->first();

        $amount = '';
        $money = '';
        if ($payment->amount > 0) {

            $amount = $payment->amount;
            $money = 'دالر';
        }
        else{
            $money = 'افغانی';
            $amount = $payment->amount_af;
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "از بیلانس کارمند به نام  " . $employee_name->name .  ' اکونت نمبر '. $employee_name->id. " مبلغ " . $amount . ' '. $money . " را حذف کرد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
