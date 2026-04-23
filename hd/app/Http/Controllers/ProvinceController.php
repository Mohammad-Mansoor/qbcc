<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Province;
use Carbon\Carbon;
use Illuminate\Http\Request;
use\DB;
use Illuminate\Support\Facades\Auth;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provinceEdit = "";
        $provinces = Province::orderBy('province')->paginate(8);
        return view('provinces.index', compact('provinces','provinceEdit'));
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
            'province' => 'required|min:3|max:32|unique:provinces'
        ]);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " ولایت  " . $request->province . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $province = Province::create($data);
        if ($province) {
            return redirect('/dashboard/provinces')->with('status', 'ولایت موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/provinces')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $provinceEdit = Province::find($id);
        $provinces = Province::orderBy('province')->paginate(6);
        return view('provinces.index', compact('provinces','provinceEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'province' => 'required|min:3|max:32|unique:provinces'
        ]);
        $province =  Province::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " ولایت  " . $request->province . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $province->update($data);
        if ($province){
            return redirect('/dashboard/provinces')->with('status', 'موفقانه بروز شد');
        }
        else{
            return redirect('/dashboard/provinces')->with('error', 'مشکل در سرور موجود است');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $province = Province::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " ولایت  " . $province->province . " از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $province->delete();
        if ($province) {
            return response()->json(['status' => 'success']);
        }
    }
}
