<?php

namespace App\Http\Controllers;

use App\FinishingTeam;
use App\FinishingTeamCategory;
use App\FinishingTeamPayment;
use Illuminate\Http\Request;
use App\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $teams = FinishingTeam::orderBy('id', 'desc')->get();

        $netUsd = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('base_credit - base_debit'));

        $netAf = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->where('ledger_entries.currency_code', 'AFN')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('credit - debit'));

        $credit_us = $netUsd;
        $debit_us = 0;
        $credit_af = $netAf;
        $debit_af = 0;
        return view('finish-team.index', compact('teams', 'fteamEdit','credit_us','credit_af','debit_us','debit_af'));
    }
        public function search(Request $request)
        {
            $search = $request->search;

            $fteamEdit = "";
            $teams = FinishingTeam::where('name', 'like','%'.$search.'%')
                ->orderBy('id', 'desc')
                ->get();
            $netUsd = \DB::table('ledger_entries')
                ->where('party_type', 'App\FinishingTeam')
                ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                ->where('ledger_transactions.status', 'posted')
                ->sum(\DB::raw('base_credit - base_debit'));

            $netAf = \DB::table('ledger_entries')
                ->where('party_type', 'App\FinishingTeam')
                ->where('ledger_entries.currency_code', 'AFN')
                ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                ->where('ledger_transactions.status', 'posted')
                ->sum(\DB::raw('credit - debit'));

            $credit_us = $netUsd;
            $debit_us = 0;
            $credit_af = $netAf;
            $debit_af = 0;
            return view('finish-team.index', compact('teams', 'fteamEdit','credit_us','credit_af','debit_us','debit_af','search'));


        }
    public function accounts(){
        $fteamEdit = "";
        $teams = FinishingTeam::orderBy('id', 'desc')->get();

        $netUsd = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('base_credit - base_debit'));

        $netAf = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->where('ledger_entries.currency_code', 'AFN')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('credit - debit'));

        $credit_us = $netUsd;
        $debit_us = 0;
        $credit_af = $netAf;
        $debit_af = 0;
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
            'father_name' => 'nullable|string|max:255',
            'tazkira_number' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'grantor_name' => 'nullable|string|max:255',
        ]);
        $team = FinishingTeam::create($data);
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " تیم تیاری به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($team) {
            return redirect('/dashboard/finish-team')->with('status', 'تیم تیاری موفقانه ثبت شد !');
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
        $teams = FinishingTeam::orderBy('id', 'desc')->paginate(6);
        $netUsd = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('base_credit - base_debit'));

        $netAf = \DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')
            ->where('ledger_entries.currency_code', 'AFN')
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->sum(\DB::raw('credit - debit'));

        $credit_us = $netUsd;
        $debit_us = 0;
        $credit_af = $netAf;
        $debit_af = 0;
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
            'father_name' => 'nullable|string|max:255',
            'tazkira_number' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'grantor_name' => 'nullable|string|max:255',
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
