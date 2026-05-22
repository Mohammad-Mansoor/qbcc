<?php

namespace App\Http\Controllers;

use App\Activity;
use App\EmployeeSalary;
use App\OfficeEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees  = OfficeEmployee::all();
        $salaryEdit = "";
        $currencies = \App\Currency::where('is_active', 1)->get();
        $salaries   = EmployeeSalary::orderBy('salary', 'DESC')->paginate(8);
        return view('office-employee-salary.create-salary', compact('employees', 'salaries', 'salaryEdit', 'currencies'));
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
            'salary' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'in_words' => 'required',
            'employee_id' => 'required',
            'contract_number' => 'required'
        ]);

        $existingSalary = EmployeeSalary::where('employee_id', $request->employee_id)->first();
        if ($request->to_date <= $request->from_date) {
            return redirect()->back()->with('error', 'تاریخ ختم قرارداد کوچک از شروع قرارداد است!');
        } else {
            if ($existingSalary && $request->from_date < $existingSalary->to_date) {
                return redirect()->back()->with('error', 'قرار داد هنوز تکمیل نشده است!');
            } else {
                // Resolve currency
                $currency = \App\Currency::find($request->currency_id);
                if (!$currency) {
                    $currency = \App\Currency::where('is_base_currency', 1)->first();
                }
                $exchangeRate = $request->exchange_rate ?? ($currency ? $currency->exchange_rate : 1);

                $salary = EmployeeSalary::create(array_merge($data, [
                    'currency_id'     => $currency ? $currency->id : null,
                    'currency_code'   => $currency ? $currency->code : null,
                    'exchange_rate'   => $exchangeRate,
                    'salary_currency' => bcmul($request->salary, 1, 4),
                    'salary_usd'      => bcmul($request->salary, $exchangeRate, 4),
                    'contract_number' => $request->contract_number,
                ]));
                if ($salary) {
                    $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " کارمند به نام " . $employee_name->name . " قرار داد جدید با معاش " . number_format($request->salary, 2) . " " . ($currency ? $currency->code : '') . " نمود ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();

                    return redirect()->back()->with('status', 'قرار داد موفقانه ثبت شد !');
                } else {
                    return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
                }
            }
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeSalary $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $employee = OfficeEmployee::find($id);
        $salaries = EmployeeSalary::where('employee_id', $id)->orderBy('to_date', 'DESC')->get();
        $salaryEdit = '';
        $salary = $employee->employee_salary->last();
        $lastSalary = EmployeeSalary::where('employee_id', $id)->latest('id')->first();
        $ContractNo = 'CO-1';
        if ($lastSalary) {
            // Safely extract numeric suffix regardless of length (CO-1, CO-10, CO-100)
            $lastNum = (int) preg_replace('/^CO-/', '', $lastSalary->contract_number);
            $ContractNo = 'CO-' . ($lastNum + 1);
        }

        $currencies = \App\Currency::where('is_active', 1)->get();
        return view('office-employee-salary.create-salary', compact('salaryEdit', 'employee', 'salaries', 'salary', 'ContractNo', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeSalary $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $salaryEdit = EmployeeSalary::find($id);
        $employee_id = $salaryEdit->employee_id;

        $employee = OfficeEmployee::find($employee_id);
        $salary = $employee->employee_salary->last();
        $salaries = EmployeeSalary::where('employee_id', $employee_id)->orderBy('to_date', 'DESC')->get();
        $currencies = \App\Currency::where('is_active', 1)->get();
        return view('office-employee-salary.create-salary', compact('salaryEdit', 'employee', 'salaries', 'salary', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\EmployeeSalary $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'salary' => 'required',
            'from_date' => 'required',
            'in_words' => 'required',
            'to_date' => 'required',

        ]);
        $salary = EmployeeSalary::find($id)->update($data);
        
        $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قرارداد کارمند به نام " . $employee_name->name . " ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        if ($salary){
            return redirect('/dashboard/employee-salary/'.$request->employee_id)->with('status','موفقانه ثبت شد !');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeSalary $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeSalary $employeeSalary)
    {
        //
    }
}
