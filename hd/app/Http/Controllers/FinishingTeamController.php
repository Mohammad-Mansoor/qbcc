<?php

namespace App\Http\Controllers;

use App\FinishingTeam;
use App\FinishingTeamCategory;
use App\FinishingTeamPayment;
use Illuminate\Http\Request;

class FinishingTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fteamEdit = "";
        $teams = FinishingTeam::all();

        $credit_us = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('finish-team.index', compact('teams', 'fteamEdit','credit_us','credit_af','debit_us','debit_af'));
    }
        public function search(Request $request)
        {
            $search = $request->search;

            $fteamEdit = "";
            $teams = FinishingTeam::where('name', 'like','%'.$search.'%')
                ->get();
            $credit_us = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount');
            $credit_af = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount_af');

            $debit_us = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount');
            $debit_af = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount_af');
            return view('finish-team.index', compact('teams', 'fteamEdit','credit_us','credit_af','debit_us','debit_af','search'));


        }
    public function accounts(){
        $fteamEdit = "";
        $teams = FinishingTeam::all();

        $credit_us = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $accounts = '';
        return view('finish-team.index', compact('teams', 'fteamEdit','credit_us','credit_af','debit_us','debit_af','accounts'));
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
            'name' => 'required|max:32|unique:finishing_teams',
        ]);
        $team = FinishingTeam::create($data);
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " تیم تیاری به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($team) {
            return redirect('/dashboard/finish-team')->with('status', 'دسته بندی موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/finish-team')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinishingTeam  $finishingTeam
     * @return \Illuminate\Http\Response
     */
    public function show(FinishingTeam $finishingTeam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FinishingTeam  $finishingTeam
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fteamEdit = FinishingTeam::find($id);
        $teams = FinishingTeam::paginate(6);
        $credit_us = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = FinishingTeamPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = FinishingTeamPayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('finish-team.index', compact('teams', 'fteamEdit','credit_us','credit_af','debit_us','debit_af'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinishingTeam  $finishingTeam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinishingTeam $team)
    {
        $data = $request->validate([
            'name' => 'required|max:32',
        ]);
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " تیم تیاری به نام " . $request->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $team->update($data);
        return redirect('/dashboard/finish-team')->with('status', 'موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinishingTeam  $finishingTeam
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinishingTeam $finishingTeam)
    {
        //
    }
}
