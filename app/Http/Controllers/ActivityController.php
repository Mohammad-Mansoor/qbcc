<?php

namespace App\Http\Controllers;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function search_activities(Request $request)
    {
        if($request->search == null){
            return redirect()->intended(url('dashboard/activities'));
        }
        $today = $request->search;
        $activities = '';
        if ($request->user_id == 'همه'){
            $activities = DB::table('activities')->where('date', $today)->orderBy('id','DESC')->get();
        }else{
            $activities = DB::table('activities')->where('date', $today)->where('user_id',$request->user_id)->orderBy('id','DESC')->get();
        }
        $users = DB::table('users')->get();

        return view('activities', compact('activities','users'));

    }



    public function index()
    {

        $activities = DB::table('activities')

            ->where('date', Carbon::today()->format('Y-m-d'))->orderBy('id','DESC')->get();

        $users = DB::table('users')->get();

        return view('activities', compact('activities','users'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy($activity_id)
    {
        $delete = DB::table('activities')->where('id', $activity_id)->delete();
        if ($delete) {
            return response()->json(['smessage' => 'موفقانه حذف شد']);
        } else {
            return response()->json(['emessage' => 'حذف نشد']);
        }
    }
}
