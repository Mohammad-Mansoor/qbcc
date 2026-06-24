<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Auth;

class MaterialCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_material_categories')->only(['index', 'show']);
        $this->middleware('permission:create_material_category')->only(['create', 'store']);
        $this->middleware('permission:edit_material_category')->only(['edit', 'update']);
        $this->middleware('permission:delete_material_category')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryEdit = "";
        $material_category = MaterialCategory::all();
        return view('material-category.material-category' , compact('material_category' , 'categoryEdit'));
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
        
        $mtype = MaterialCategory::create($this->valData());

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کتگوری مواد به نام " . $request->material_category . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        if ($mtype) {
            return redirect('/dashboard/material-category')->with('status', 'کتگوری مواد موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/material-category')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialCategory  $materialCategory
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialCategory $materialCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialCategory  $materialCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categoryEdit = MaterialCategory::find($id);
        $material_category = MaterialCategory::paginate(6);
        return view('material-category.material-category',compact('material_category', 'categoryEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MaterialCategory  $materialCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category =  MaterialCategory::find($id);
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کتگوری مواد به نام " . $request->material_category . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $category->update($this->valData($id));
        if($category){
            return redirect('/dashboard/material-category')->with('status', 'موفقانه بروز شد');
        }else{
            return redirect('/dashboard/material-category')->with('error', 'مشکل در سرور وجود داره');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialCategory  $materialCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = MaterialCategory::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کتگوری مواد به نام " . $category->material_category . " از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $category->delete();
        if ($category) {
            return response()->json(['status' => 'success']);
        }

    
    }

    protected function valData($id = null)
    {
        return request()->validate([
            'material_category' => 'required|min:3|max:32|unique:material_categories,material_category' . ($id ? ",$id,material_category_id" : ""),
            'subtype' => 'required|in:yarn,dye'
        ]);
    }
}
