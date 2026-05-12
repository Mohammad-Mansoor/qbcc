<?php

namespace App\Http\Controllers;

use App\Activity;
use App\EmployeePayment;
use App\OfficeEmployee;
use App\Services\AccountingService;
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
    protected $accountingService;
    
    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        //
    }

    private function postPaymentToAccounting($payment)
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;

            $this->accountingService->postAutoTransaction('employee_payment', 'PAYROLL_PAYMENT', [
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\OfficeEmployee',
                'party_id' => $payment->employee_id,
                'reference' => 'EMP-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_type' => 'EmployeePayment',
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Employee Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function request_list()
    {
        $requests = EmployeePayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('office-employee.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = EmployeePayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0) 
                ? " مبلغ " . $payment->amount . " دالر معاش برای کارمند تایید شد "
                : " مبلغ " . $payment->amount_af . " افغانی معاش برای کارمند تایید شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
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
        return DB::transaction(function () use ($request) {
            $request->validate([
                'contract_number' => 'required',
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'employee_id' => 'required',
            ]);

            $payed = new EmployeePayment();
            $payed->contract_number = $request->contract_number;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->employee_id = $request->employee_id;
            $payed->dollar_rate = $request->dollar_rate;
            
            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }

            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();
            $currency = ($request->money_type == 'دالر') ? " دالر " : " افغانی ";

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت معاش به کارمند " . $employee_name->name . " اکونت نمبر " . $employee_name->id . " به مبلغ " . $request->amount . $currency;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'معاش با موفقیت ثبت و در دفتر روزنامچه درج شد!');
        });
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
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'contract_number' => 'required',
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
            ]);

            $payed = EmployeePayment::find($payment_id);
            $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();

            // Reverse Old Accounting Entries (Only if approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Employee Payment Edited');
            }

            // Update record
            $payed->contract_number = $request->contract_number;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->employee_id = $request->employee_id;
            $payed->dollar_rate = $request->dollar_rate;

            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            $payed->update();

            // Post New Accounting Entry (Only if approved)
            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت معاش کارمند " . $employee_name->name . " اکونت نمبر " . $employee_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/employee-payments/'.$request->employee_id)->with('status', 'ویرایش موفقانه انجام شد و حسابات مالی بروز گردید!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeePayment  $employeePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = EmployeePayment::find($id);
            $employee_name = DB::table('office_employees')->where('id', $payment->employee_id)->first();

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Employee Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت معاش کارمند " . $employee_name->name . " اکونت نمبر " . $employee_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
