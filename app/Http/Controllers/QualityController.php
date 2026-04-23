<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetType;
use App\Quality;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qualityEdit = "";
        $carpet_types = CarpetType::all();
        $qualities = Quality::all();
        return view('carpet-qualities.carpet-qualities' , compact('qualityEdit' , 'carpet_types','qualities'));

    }

    public function get_by_type(Request $request)
    {
        if (!$request->type_id) {
            $html = '<option value="">' . trans('لطفا انتخاب کنید') . '</option>';
        } else {
            $html = '';
            $qualities = Quality::where('type_id', $request->type_id)->get();
            foreach ($qualities as $p) {
                $html .= '<option value="' . $p->id . '">' . $p->quality . '</option>';
            }
        }

        return response()->json(['html' => $html]);
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
        $quality = Quality::create($this->valData());
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کوالتی " . $request->quality . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($quality) {
            return redirect('/dashboard/carpet-qualities')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/carpet-qualities')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Quality  $quality
     * @return \Illuminate\Http\Response
     */
    public function show(Quality $quality)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Quality  $quality
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $qualityEdit = Quality::find($id);
        $carpet_types = CarpetType::all();
        $qualities = Quality::all();
        return view('carpet-qualities.carpet-qualities',compact('carpet_types', 'qualityEdit','qualities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quality  $quality
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $quality =  Quality::find($id);
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کوالتی " . $request->quality . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $quality->update($this->valData());
        return redirect('/dashboard/carpet-qualities')->with('status', 'موفقانه بروز شد');
    }
    protected function valData()
    {
        return request()->validate([
            'quality' => 'required|min:3|max:256',
            'type_id' => 'required'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Quality  $quality
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quality $quality)
    {
        //
    }
}
