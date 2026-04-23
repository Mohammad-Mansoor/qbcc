<?php

namespace App\Http\Controllers;

use App\Activity;
use App\FinishingTeam;
use App\FinishingTeamCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinishingTeamCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fteamEdit = "";
        $categories = FinishingTeamCategory::paginate(15);
        return view('finish-team-category.index', compact('categories', 'fteamEdit'));
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
            'category' => 'required|max:32|unique:finishing_team_categories'
        ]);
        $category = FinishingTeamCategory::create($data);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کتگوری تیم تیاری به به نام " . $request->category . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        if ($category) {
            return redirect('/dashboard/finish-team-category')->with('status', 'دسته بندی موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/finish-team-category')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinishingTeamCategory  $finishingTeamCategory
     * @return \Illuminate\Http\Response
     */
    public function show(FinishingTeamCategory $finishingTeamCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FinishingTeamCategory  $finishingTeamCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fteamEdit= FinishingTeamCategory::find($id);
        $categories = FinishingTeamCategory::paginate(15);
        return view('finish-team-category.index', compact('categories', 'fteamEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinishingTeamCategory  $finishingTeamCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinishingTeamCategory $category)
    {
        $data = $request->validate([
            'category' => 'required|max:32|unique:finishing_team_categories'
        ]);
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " کتگوری تیم تیاری به به نام " . $request->category . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $category->update($data);
        return redirect('/dashboard/finish-team-category')->with('status', 'موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinishingTeamCategory  $finishingTeamCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinishingTeamCategory $finishingTeamCategory)
    {
        //
    }
}
