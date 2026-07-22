<?php

namespace App\Http\Controllers;
use App\Activity;
use App\CarpetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
class CarpetTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_carpet_types')->only(['index', 'show']);
        $this->middleware('permission:create_carpet_type')->only(['create', 'store']);
        $this->middleware('permission:edit_carpet_type')->only(['edit', 'update']);
        $this->middleware('permission:delete_carpet_type')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeEdit = "";
        $carpet_type = CarpetType::all();
        
        return view('carpet-types.carpet-types' , compact('typeEdit' , 'carpet_type'));
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
       
        $type = CarpetType::create($this->valData());
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نوعیت " . $request->carpet_type . " در سیستم اضافه شد  ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($type) {
            return redirect('/dashboard/carpet-types')->with('status', 'نوع قالین موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/carpet-types')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetType  $carpetType
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetType $carpetType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetType  $carpetType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $typeEdit = CarpetType::find($id);
        $carpet_type = CarpetType::paginate(6);
        return view('carpet-types.carpet-types',compact('carpet_type', 'typeEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetType  $carpetType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $type =  CarpetType::find($id);
          $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نوعیت " . $type->carpet_type . " در سیستم ویرایش شد  ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $type->update($this->valData());
        return redirect('/dashboard/carpet-types')->with('status', 'موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetType  $carpetType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = DB::table('carpet_types')->where('carpet_type_id' , $id)->delete();
        if($delete){
            return response()->json(['status' => 'success']);
        }
    }

    protected function valData()
    {
        return request()->validate([
            'carpet_type' => 'required|min:3|max:256|unique:carpet_types'
        ]);
    }
}
