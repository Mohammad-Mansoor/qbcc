<?php

namespace App\Http\Controllers;

use App\Activity;
use App\OfficeEmployee;
use Carbon\Carbon;
use Faker\Provider\File;
use Illuminate\Http\Request;
use App\EmployeeDepartment;
use App\EmployeeSalary;
use Illuminate\Support\Facades\Auth;

class OfficeEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = OfficeEmployee::with('department')->get();
        $department = EmployeeDepartment::all();
        $employeeEdit = '';

        return view('office-employee.employee-list',compact('employees','department','employeeEdit'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
          $employeeEdit = '';
             $department = EmployeeDepartment::all();
        $employees = OfficeEmployee::where('name','like','%'.$search.'%')
            ->orWhere('job_title','like','%'.$search.'%')

            ->orWhere('phone','like','%'.$search.'%')
            ->orWhere('email','like','%'.$search.'%')
            ->paginate(5);
              return view('office-employee.employee-list',compact('employees','employeeEdit','department'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $department = EmployeeDepartment::all();
        return view('office-employee.create-employee',compact('department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image = '';
        if($request->has('image')) {
            $file = $request->file('image');
            $fileExt = $file->getClientOriginalExtension();
            if(!in_array($fileExt , ['jpg' , 'png' , 'jpeg'] )) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time().''.rand(1000,9999).'-employee-image.'.$fileExt;
            $image = $file->move('uploads/employee-image' , $fileName);
        }
        // return $request->all();
        $employeeData = $request->validate([
            'name' => 'required|min:3|max:32',
            'job_title' => 'required|min:3|max:64',
            'phone' => 'required|min:10|max:14|unique:office_employees',
            'email' => 'required|min:3|max:64|unique:office_employees',
            'department_id' => '',
            'image' => '',
            'user_role' => ''
        ]);


        $employeeData['image'] = $image;
        $employeeData['user_role'] = Auth::user()->role;

        // dd($data);
        $employee = OfficeEmployee::create($employeeData);
        
        
        if ($employee){
            $salary = new EmployeeSalary();
            $salary->contract_number = 'CO-1';
            $salary->salary = $request->salary;
            $salary->from_date = $request->from_date;
            $salary->to_date = $request->to_date;
            $salary->employee_id = $employee->id;
            $salary->in_words = $request->in_words;
            $salary->save();
        }
        if($employee) {

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " کارمند جدید به نام " . $request->name . " در سیستم ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            return redirect('/dashboard/office-employee')->with('status', 'کارمند موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/office-employee')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OfficeEmployee  $officeEmployee
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OfficeEmployee  $officeEmployee
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeEdit = OfficeEmployee::find($id);
        $employees = OfficeEmployee::with('department')->get();
        $department = EmployeeDepartment::all();
        return view('office-employee.employee-list',compact('employees','department','employeeEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OfficeEmployee  $officeEmployee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = OfficeEmployee::find($id);

        $image = '';
        if($request->has('image')) {

            $file = $request->file('image');


            $fileExt = $file->getClientOriginalExtension();
            if(!in_array($fileExt , ['jpg' , 'png' , 'jpeg'] )) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time().''.rand(1000,9999).'-employee-image.'.$fileExt;
            $image = $file->move('uploads/employee-image' , $fileName);
            $employee->image = $image;
        }
        $employee->name = $request->name;
        $employee->job_title = $request->job_title;
        $employee->phone = $request->phone;
        $employee->email = $request->email;

        $employee->department_id = $request->department_id;
        $employee->save();
        
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کارمند به نام " . $request->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        if($employee) {
            return redirect('/dashboard/office-employee')->with('status', 'کارمند موفقانه بروز شد !');
        }
        else{
            return redirect('/dashboard/office-employee')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OfficeEmployee  $officeEmployee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = OfficeEmployee::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کارمند به نام " . $employee->name . " از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $employee->delete();
        if($employee) {
            return response()->json(['status' => 'success']);
        }
    }
}
