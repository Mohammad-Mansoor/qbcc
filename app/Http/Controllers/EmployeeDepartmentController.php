<?php

namespace App\Http\Controllers;

use App\Activity;
use App\EmployeeDepartment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departmentEdit = "";
        $departments = EmployeeDepartment::orderBy('department','DESC')->paginate(8);
        return view('office-employee-department.create-department',compact('departments','departmentEdit'));
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
            'department' => 'required|min:2|max:256|unique:employee_departments'
        ]);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " دیپارتمنت کارمندان به نام " . $request->department . " در سیستم اضافه شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

        $department = EmployeeDepartment::create($data);
        if ($department) {
            return redirect('/dashboard/employee-department')->with('status', 'دیپارتمنت موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/employee-department')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeDepartment  $employeeDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeDepartment $employeeDepartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeDepartment  $employeeDepartment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $departmentEdit = EmployeeDepartment::find($id);
        $departments = EmployeeDepartment::orderBy('department')->paginate(6);
        return view('office-employee-department.create-department', compact('departments','departmentEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeDepartment  $employeeDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'department' => 'required|min:2|max:256|unique:employee_departments'
        ]);
        $department =  EmployeeDepartment::find($id);
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " دیپارتمنت کارمندان به نام " . $request->department . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $department->update($data);

        return redirect('/dashboard/employee-department')->with('status', 'موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeDepartment  $employeeDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = EmployeeDepartment::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " دیپارتمنت کارمندان به نام " . $department->department . " از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        $department->delete();
        if ($department) {
            return response()->json(['status' => 'success']);
        }
    }
}
