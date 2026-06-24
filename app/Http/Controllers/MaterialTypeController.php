<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_material_types')->only(['index', 'show']);
        $this->middleware('permission:create_material_type')->only(['create', 'store']);
        $this->middleware('permission:edit_material_type')->only(['edit', 'update']);
        $this->middleware('permission:delete_material_type')->only('destroy');
    }
    public function index()
    {
        $mtypeEdit = "";
        $mtype = MaterialType::orderBy('material_type_id')->get();
        return view('material_type.index', compact('mtype', 'mtypeEdit'));
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
        $mtype = MaterialType::create($this->valData());

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نوعیت مواد  به نام " . $request->material_type . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        
        if ($mtype) {
            return redirect('/dashboard/materialtypes')->with('status', 'نوع مواد موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/materialtypes')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialType  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialType $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialType  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mtypeEdit = MaterialType::find($id);
        $mtype = MaterialType::orderBy('material_type_id')->paginate(6);
        return view('material_type.index', compact('mtype', 'mtypeEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MaterialType  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request , $id)
    {
        $mtype = MaterialType::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نوعیت مواد  به نام " . $request->material_type . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        
        $mtype->update($this->valData($id));
        if (!$mtype){
            return redirect('/dashboard/materialtypes')->with('error', 'مشکل در سرور وجود دارد');

        }else{
            return redirect('/dashboard/materialtypes')->with('status', 'موفقانه بروز شد');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialType  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   $mtype = MaterialType::find($id);
    
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نوعیت مواد  از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        
        $mtype = $mtype->delete();
        if ($mtype) {
            return response()->json(['status' => 'success']);
        }
    }


    protected function valData($id = null)
    {
        return request()->validate([
            'material_type' => 'required|min:3|max:32|unique:material_types,material_type,' . ($id ?: 'NULL') . ',material_type_id',
            'subtype' => 'required|in:yarn,dye'
        ]);
    }
}

