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
        $employees = OfficeEmployee::all();
        $salaryEdit = "";
        $salaries = EmployeeSalary::orderBy('salary', 'DESC')->paginate(8);
        return view('office-employee-salary.create-salary', compact('employees', 'salaries', 'salaryEdit'));
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

        $employee = EmployeeSalary::where('employee_id', $request->employee_id)->first();
        if ($request->to_date <= $request->from_date) {
            return redirect()->back()->with('error', 'تاریخ ختم قرارداد کوچک از شروع قرارداد است!');
        } else {
            if ($request->from_date < $employee->to_date) {
                return redirect()->back()->with('error', 'قرار داد هنوز تکمیل نشده است!');
            } else {
                $salary = EmployeeSalary::create($data);
                if ($salary) {
                    $employee_name = DB::table('office_employees')->where('id', $request->employee_id)->first();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " کارمند به نام " . $employee_name->name . " قرار داد جدید نمود ";
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
        $lastId = EmployeeSalary::where('employee_id', $id)->latest()->first();
        $ContractNo = '';
        if ($lastId) {
            $lastId = $lastId->contract_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $ContractNo = 'CO-' . sprintf('%01d', $lastId);
        } else {
            $ContractNo = 'CO-' . sprintf('%01d', '1');
        }

        return view('office-employee-salary.create-salary', compact('salaryEdit', 'employee', 'salaries', 'salary', 'ContractNo'));
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
        return view('office-employee-salary.create-salary', compact('salaryEdit', 'employee', 'salaries', 'salary'));
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
