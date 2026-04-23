<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AgentEmployee;
use App\Agents;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = AgentEmployee::all();
        return view('agent-employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agents = Agents::all();
        return view('agent-employee.create',compact('agents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->valData();
        $employee = new AgentEmployee();
        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->father_name = $request->father_name;
        $employee->agent_id = $request->agent_id;
        $employee->save();
        if ($employee) {
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "استفاده کننده بنام ". Auth::user()->name . " حساب کاریگر به نام " . $request->first_name . " را ایجاد کرد.";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            return redirect('/dashboard/agent-employees')->with('status', ' کارگر موفقانه ثبت نام شد!');
        } else {
            return redirect('/dashboard/agent-employees')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AgentEmployee  $agentEmployee
     * @return \Illuminate\Http\Response
     */
    public function show(AgentEmployee $agentEmployee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AgentEmployee  $agentEmployee
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = AgentEmployee::findOrFail($id);
        $agents = Agents::all();
        return view('agent-employee.edit',compact('employee','agents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AgentEmployee  $agentEmployee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->valData();
        $employee = AgentEmployee::findOrFail($id);
        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->father_name = $request->father_name;
        $employee->agent_id = $request->agent_id;
        $employee->update();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "کاریگر بنام ". $request->first_name . " ویرایش شد";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect('/dashboard/agent-employees')->with('status', 'موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgentEmployee  $agentEmployee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
        $employee = AgentEmployee::find($id);
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "کاریگر بنام ". $employee->first_name . " حذف شد";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $employee->delete();
        if ($employee) {


            return response()->json(['status' => 'success']);
        }  
    }

    protected function valData(){
        return request()->validate([
            'frist_name' => 'min:3|max:40',
            'last_name' => 'min:3|max:40',
            'father_name' => 'min:3|max:40',
        ]);
    }
}
