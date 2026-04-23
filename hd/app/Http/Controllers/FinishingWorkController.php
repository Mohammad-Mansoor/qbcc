<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Agents;
use App\CarpetWash;
use App\FinishingTeam;
use App\FinishingTeamCategory;
use App\FinishingTotalAccount;
use App\FinishingWork;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FinishingWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function request_list()
    {
        $requests = FinishingWork::where('status', 0)->orderBy('id', 'DESC')->get();


        return view('finishing-center.refinish-request-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $work = FinishingWork::find($id);


        $work->status = 1;

        $carpet = Carpet::where('carpet_id', '=', $work->carpetId)->first();


        $carpet->total_price = $carpet->total_price + $work->price;
        $carpet->total_price_af = $carpet->total_price_af + $work->price_af_qaitan;
        $carpet->update();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        if ($work->category_id == 1) {
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . "دوباره قیتان شد. ";
        }elseif ($work->category_id == 2){
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " دوباره رفو شد. ";
        }
        elseif ($work->category_id == 3){
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " دوباره چیت شد. ";
        }
        elseif ($work->category_id == 4){
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " دوباره لبکی شد. ";
        }
        elseif ($work->category_id == 5){
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . "دوباره پوپک شد. ";
        }
        elseif ($work->category_id == 6){
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " دوباره کش شد. ";
        }
        elseif ($work->category_id == 7){
            $activity->description = " قالین نمبر  " . $carpet->carpet_no . " دوباره رنگ شد. ";
        }
        $activity->user_id = Auth::user()->id;
        $activity->save();


        $work->update();


        return response()->json(['status' => 'success']);

    }

    public function delete_request($id)
    {
        $work = FinishingWork::find($id);


        $work->delete();


        return response()->json(['status', 'error']);
    }


    public function return_to_wash($carpet_id)
    {
        $carpet = Carpet::find($carpet_id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->carpet_no . " از بخش تیاری به شست بازگشت داده شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $carpet->status = 13;
        $upd = $carpet->update();
        if ($upd) {
            return back()->with('status', 'قالین موفقانه به بخش تیاری فرستاده شد');
        }

    }

    public function return_to_center($carpet_id)
    {
        $carpet = Carpet::find($carpet_id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->carpet_no . " از بخش تیاری به دفتر مرکزی بازگشت داده شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $carpet->status = 1;
        $upd = $carpet->update();
        if ($upd) {
            return back()->with('status', 'قالین موفقانه به بخش تیاری فرستاده شد');
        }

    }

    public function index()
    {
        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)->orderBy('updated_at', 'DESC')->paginate(100);
        $finisheds = FinishingWork::paginate(100);

        $team = FinishingTeam::all();
        $check = '';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check'));
    }

    public function search_finish_number($finish_number, $team_id)
    {
        $team = FinishingTeam::find($team_id);
        $finishing_works = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->distinct()->get(['carpetId']);

        $quantity = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->count();
        $finish_numbers = FinishingWork::where('team_id', $team_id)->distinct()->get(['finish_number']);

        return view('finishing-center.finish-number-list', compact('finishing_works', 'team', 'finish_number', 'quantity', 'finish_numbers'));


    }

    public function search_from_finish_number(Request $request)
    {
        $team_id = $request->team_id;
        $finish_number = $request->finish_number;
        $team = FinishingTeam::find($team_id);
        $finishing_works = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->distinct()->get(['carpetId']);
        $quantity = FinishingWork::Where('team_id', '=', $team_id)->where('finish_number', '=', $finish_number)->count();
        $finish_numbers = FinishingWork::where('team_id', $team_id)->distinct()->get(['finish_number']);
        return view('finishing-center.finish-number-list', compact('finishing_works', 'team', 'finish_number', 'quantity', 'finish_numbers'));

    }

    public function search(Request $request)
    {


        $search_finish = $request->search_finish;

        $finisheds = FinishingWork::where('finish_number', 'like', '%' . $search_finish . '%')
            ->orWhere('date', 'like', '%' . $search_finish . '%')
            ->orWhere('date', 'like', '%' . $search_finish . '%')
            ->orWhereHas('carpet', function ($query) use ($search_finish) {
                $query->where('carpet_no', 'like', '%' . $search_finish . '%');
            })
            ->orWhereHas('team', function ($query) use ($search_finish) {
                $query->where('name', 'like', '%' . $search_finish . '%');
            })
            ->orWhereHas('category', function ($query) use ($search_finish) {
                $query->where('category', 'like', '%' . $search_finish . '%');
            })
            ->paginate(100);

        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)->orderBy('updated_at', 'DESC')->paginate(20);
        $team = FinishingTeam::all();
        $check = 'not_null';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check'));
    }

    public function search_non(Request $request)
    {
        $search_non = $request->search_non;

        $agents = Agents::all();
        $nonfinished = Carpet::where('status', '=', 4)
            ->where('carpet_no', 'like', '%' . $search_non . '%')
            ->orWhereHas('carpet_order', function ($query) use ($search_non) {
                $query->where('order_number', 'like', '%' . $search_non . '%');
            })
            ->orWhereHas('type', function ($query) use ($search_non) {
                $query->where('carpet_type', 'like', '%' . $search_non . '%');
            })->orderBy('updated_at', 'DESC')->paginate(20);
        $finisheds = FinishingWork::paginate(20);
        $team = FinishingTeam::all();
        $check = '';
        return view('finishing-center.index', compact('nonfinished', 'team', 'agents', 'finisheds', 'check'));

    }

    // SAVING EVERY STEP OF FINISHING CENTER WORK
    public function saving_the_work(Carpet $carpet)
    {

        $newCarpet = '';
        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first();

        if ($newCarpet == null) {
            $newCarpet = Carpet::where('carpet_id', $carpet->carpet_id)->first();

        }
        $lastId = FinishingWork::latest()->first();
        $FinishNo = '';
        if ($lastId) {
            $lastId = $lastId->finish_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $FinishNo = 'TA-' . sprintf('%01d', $lastId);
        } else {
            $FinishNo = 'TA-' . sprintf('%01d', '1');
        }


        $qaitan_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 1)->first();
        $rofo_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 2)->first();
        $cheet_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 3)->first();
        $labaki_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 4)->first();
        $popak_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 5)->first();
        $kash_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 6)->first();
        $rang_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 7)->first();

        $done = FinishingWork::where('carpetId', $carpet->carpet_id)->distinct()->get('category_id');
        $teams = FinishingTeam::all();
        $team_categories = FinishingTeamCategory::whereNotIn('id', $done)->get();
        return view('finishing-center.create', compact('carpet', 'teams', 'newCarpet', 'FinishNo', 'team_categories', 'qaitan_check', 'rofo_check', 'cheet_check', 'labaki_check', 'popak_check', 'kash_check', 'rang_check'));
    }

    public function re_saving_the_work(Carpet $carpet)
    {
        $newCarpet = '';
        $newCarpet = CarpetWash::where('carpetId', $carpet->carpet_id)->first();

        if ($newCarpet == null) {
            $newCarpet = Carpet::where('carpet_id', $carpet->carpet_id)->first();

        }
        $lastId = FinishingWork::latest()->first();
        $FinishNo = '';
        if ($lastId) {
            $lastId = $lastId->finish_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $FinishNo = 'TA-' . sprintf('%01d', $lastId);
        } else {
            $FinishNo = 'TA-' . sprintf('%01d', '1');
        }


        $qaitan_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 1)->first();
        $rofo_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 2)->first();
        $cheet_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 3)->first();
        $labaki_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 4)->first();
        $popak_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 5)->first();
        $kash_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 6)->first();
        $rang_check = FinishingWork::where('carpetId', $carpet->carpet_id)->where('category_id', 7)->first();

        $done = FinishingWork::where('carpetId', $carpet->carpet_id)->distinct()->get('category_id');
        $teams = FinishingTeam::all();
        $team_categories = FinishingTeamCategory::whereNotIn('id', $done)->get();
        return view('finishing-center.re-finish-work', compact('carpet', 'teams', 'newCarpet', 'FinishNo', 'team_categories', 'qaitan_check', 'rofo_check', 'cheet_check', 'labaki_check', 'popak_check', 'kash_check', 'rang_check'));
    }

    public function store_refinish(Request $request, FinishingWork $work)
    {


        $carpet = Carpet::where('carpet_id', '=', $request->carpetId)->first();
        $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first();

        if ($newCarpet == null) {
            $newCarpet = Carpet::where('carpet_id', $carpet->carpet_id)->first();
        }
        if ($request->qaitan_checkbox != 'on' && $request->rofo_checkbox != 'on' && $request->cheet_checkbox != 'on' && $request->labaki_checkbox != 'on' && $request->popak_checkbox != 'on' && $request->kash_checkbox != 'on' && $request->rang_checkbox != 'on') {
            return redirect()->back()->with('error', 'یکی از این بخش ها را باید انتخاب کنید');
        } else {

            if ($request->qaitan_checkbox == 'on') {

                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_qaitan;
                $finish->price = $newCarpet->area * $request->price_qaitan;
                $finish->price_af = $newCarpet->area * $request->price_af_qaitan;
                $finish->date = $request->date_qaitan;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_qaitan;
                $finish->category_id = $request->category_id_qaitan;
                $finish->description = 'دوباره قیتان شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;
                    $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_qaitan;
                    $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_qaitan;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " قیتان شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();
                } else {
                    $finish->status = 0;
                }
                $finish->save();

            }

            if ($request->rofo_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_rofo;
                $finish->price = $request->price_rofo;
                $finish->price_af = $request->price_af_rofo;
                $finish->date = $request->date_rofo;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_rofo;
                $finish->category_id = $request->category_id_rofo;
                $finish->description = 'دوباره رفو شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;

                    $carpet->total_price = $carpet->total_price + $request->price_rofo;
                    $carpet->total_price_af = $carpet->total_price_af + $request->price_af_rofo;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " رفو شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();
                } else {
                    $finish->status = 0;
                }
                $finish->save();


            }

            if ($request->cheet_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_cheet;
                $finish->price = $newCarpet->area * $request->price_cheet;
                $finish->price_af = $newCarpet->area * $request->price_af_cheet;
                $finish->date = $request->date_cheet;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_cheet;
                $finish->category_id = $request->category_id_cheet;
                $finish->description = 'دوباره چیت شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;

                    $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_cheet;
                    $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_cheet;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " چیت شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();
                } else {
                    $finish->status = 0;
                }
                $finish->save();


            }

            if ($request->labaki_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_labaki;
                $finish->price = $newCarpet->height * $request->price_labaki * 2;
                $finish->price_af = $newCarpet->height * $request->price_af_labaki * 2;
                $finish->date = $request->date_labaki;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_labaki;
                $finish->category_id = $request->category_id_labaki;
                $finish->description = 'دوباره لبکی شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;

                    $carpet->total_price = $carpet->total_price + $newCarpet->height * $request->price_labaki * 2;
                    $carpet->total_price_af = $carpet->total_price_af + $newCarpet->height * $request->price_af_labaki * 2;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " لبکی شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();
                } else {
                    $finish->status = 0;
                }
                $finish->save();


            }

            if ($request->popak_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_popak;
                $finish->price = $newCarpet->area * $request->price_popak;
                $finish->price_af = $newCarpet->area * $request->price_af_popak;
                $finish->date = $request->date_popak;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_popak;
                $finish->category_id = $request->category_id_popak;
                $finish->description = 'دوباره پوپک شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;

                    $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_popak;
                    $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_popak;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " پوپک شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();

                } else {
                    $finish->status = 0;
                }
                $finish->save();


            }

            if ($request->kash_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_kash;
                $finish->price = $newCarpet->area * $request->price_kash;
                $finish->price_af = $newCarpet->area * $request->price_af_kash;
                $finish->date = $request->date_kash;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_kash;
                $finish->category_id = $request->category_id_kash;
                $finish->description = 'دوباره کش شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;

                    $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_kash;
                    $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_kash;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " کش شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();
                } else {
                    $finish->status = 0;
                }
                $finish->save();


            }

            if ($request->rang_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_rang;
                $finish->price = $newCarpet->area * $request->price_rang;
                $finish->price_af = $newCarpet->area * $request->price_af_rang;
                $finish->date = $request->date_rang;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_rang;
                $finish->category_id = $request->category_id_rang;
                $finish->description = 'دوباره رنگ شد';
                if (Auth::user()->role == 'SP') {
                    $finish->status = 1;

                    $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_rang;
                    $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_rang;
                    $carpet->update();

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " قالین نمبر  " . $carpet->carpet_no . " رنگ شد. ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();
                } else {
                    $finish->status = 0;
                }
                $finish->save();


            }

        }

        // SAVING DATA TO FINISHING_WORK TABLE


        return redirect('/dashboard/finishing-center')->with('status', 'تیاری موفقانه ثبت شد');


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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, FinishingWork $work)
    {



        $carpet = Carpet::where('carpet_id', '=', $request->carpetId)->first();
        $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first();

        if ($newCarpet == null) {
            $newCarpet = Carpet::where('carpet_id', $carpet->carpet_id)->first();
        }
        if ($request->qaitan_checkbox != 'on' && $request->rofo_checkbox != 'on' && $request->cheet_checkbox != 'on' && $request->labaki_checkbox != 'on' && $request->popak_checkbox != 'on' && $request->kash_checkbox != 'on' && $request->rang_checkbox != 'on') {
            return redirect()->back()->with('error', 'یکی از این بخش ها را باید انتخاب کنید');
        } else {

            if ($request->qaitan_checkbox == 'on') {

                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_qaitan;
                $finish->price = $newCarpet->area * $request->price_qaitan;
                $finish->price_af = $newCarpet->area * $request->price_af_qaitan;
                $finish->date = $request->date_qaitan;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_qaitan;
                $finish->category_id = $request->category_id_qaitan;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_qaitan;
                $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_qaitan;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " قیتان شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

            if ($request->rofo_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_rofo;
                $finish->price = $request->price_rofo;
                $finish->price_af = $request->price_af_rofo;
                $finish->date = $request->date_rofo;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_rofo;
                $finish->category_id = $request->category_id_rofo;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $request->price_rofo;
                $carpet->total_price_af = $carpet->total_price_af + $request->price_af_rofo;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " رفو شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

            if ($request->cheet_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_cheet;
                $finish->price = $newCarpet->area * $request->price_cheet;
                $finish->price_af = $newCarpet->area * $request->price_af_cheet;
                $finish->date = $request->date_cheet;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_cheet;
                $finish->category_id = $request->category_id_cheet;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_cheet;
                $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_cheet;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " چیت شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

            if ($request->labaki_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_labaki;
                $finish->price = $newCarpet->height * $request->price_labaki * 2;
                $finish->price_af = $newCarpet->height * $request->price_af_labaki * 2;
                $finish->date = $request->date_labaki;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_labaki;
                $finish->category_id = $request->category_id_labaki;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $newCarpet->height * $request->price_labaki * 2;
                $carpet->total_price_af = $carpet->total_price_af + $newCarpet->height * $request->price_af_labaki * 2;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " لبکی شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

            if ($request->popak_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_popak;
                $finish->price = $newCarpet->area * $request->price_popak;
                $finish->price_af = $newCarpet->area * $request->price_af_popak;
                $finish->date = $request->date_popak;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_popak;
                $finish->category_id = $request->category_id_popak;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_popak;
                $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_popak;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " پوپک شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

            if ($request->kash_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_kash;
                $finish->price = $newCarpet->area * $request->price_kash;
                $finish->price_af = $newCarpet->area * $request->price_af_kash;
                $finish->date = $request->date_kash;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_kash;
                $finish->category_id = $request->category_id_kash;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_kash;
                $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_kash;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " کش شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

            if ($request->rang_checkbox == 'on') {


                $finish = new FinishingWork();
                $finish->finish_number = $request->finish_number_rang;
                $finish->price = $newCarpet->area * $request->price_rang;
                $finish->price_af = $newCarpet->area * $request->price_af_rang;
                $finish->date = $request->date_rang;
                $finish->carpetId = $request->carpetId;
                $finish->team_id = $request->team_id_rang;
                $finish->category_id = $request->category_id_rang;
                $finish->status = 1;
                $finish->save();

                $carpet->total_price = $carpet->total_price + $newCarpet->area * $request->price_rang;
                $carpet->total_price_af = $carpet->total_price_af + $newCarpet->area * $request->price_af_rang;
                $carpet->update();

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " قالین نمبر  " . $carpet->carpet_no . " رنگ شد. ";
                $activity->user_id = Auth::user()->id;
                $activity->save();

            }

        }

        // SAVING DATA TO FINISHING_WORK TABLE


        if ($request->finished == 1) {
            $finish = Carpet::where('carpet_id', '=', $request->carpetId)->first();
            $finish->status = 5;
            $finish->update();

        }
//

        return redirect('/dashboard/finishing-center')->with('status', 'تیاری موفقانه ثبت شد');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinishingWork $finishingWork
     * @return \Illuminate\Http\Response
     */
    public function show(FinishingWork $finish)
    {
        $newCarpet = CarpetWash::where('carpetId', $finish->carpetId)->first();
        return view('finishing-center.show', compact('finish', 'newCarpet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FinishingWork $finishingWork
     * @return \Illuminate\Http\Response
     */
    public function edit(FinishingWork $finish)
    {
        $carpet = Carpet::where('carpet_id', '=', $finish->carpetId)->first();
        $newCarpet = CarpetWash::where('carpetId', $finish->carpetId)->first();
        if ($newCarpet == null) {
            $newCarpet = Carpet::where('carpet_id', $carpet->carpet_id)->first();
        }


        $category = FinishingTeamCategory::find($finish->category_id);
        if (Str::contains($category->category, 'لبکی')) {
            $mainPrice = ($finish->price / $newCarpet->height) / 2;
            $mainPrice_af = ($finish->price_af / $newCarpet->height) / 2;
        } elseif (Str::contains($category->category, 'قیتان')) {
            $mainPrice = $finish->price / $newCarpet->area;
            $mainPrice_af = $finish->price_af / $newCarpet->area;
        } elseif (Str::contains($category->category, 'رفو')) {
            $mainPrice = $finish->price;
            $mainPrice_af = $finish->price_af;
        } elseif (Str::contains($category->category, 'چیت')) {
            $mainPrice = $finish->price / $newCarpet->area;
            $mainPrice_af = $finish->price_af / $newCarpet->area;
        } elseif (Str::contains($category->category, 'کش')) {
            $mainPrice = $finish->price / $newCarpet->area;
            $mainPrice_af = $finish->price_af / $newCarpet->area;
        } elseif (Str::contains($category->category, 'رنگ')) {
            $mainPrice = $finish->price / $newCarpet->area;
            $mainPrice_af = $finish->price_af / $newCarpet->area;
        } elseif (Str::contains($category->category, 'پوپک')) {
            $mainPrice = $finish->price / $newCarpet->area;
            $mainPrice_af = $finish->price_af / $newCarpet->area;
        }
        $team = FinishingTeam::where('id', $finish->team_id)->first();
        $teams = FinishingTeam::where('id', '!=', $finish->team_id)->get();
        $category = FinishingTeamCategory::where('id', $finish->category_id)->first();
        $team_categories = FinishingTeamCategory::where('id', '!=', $finish->category_id)->get();
        $lastId = FinishingWork::latest()->first();

        return view('finishing-center.edit', compact('team', 'teams', 'category', 'team_categories', 'finish', 'mainPrice', 'mainPrice_af', 'newCarpet'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\FinishingWork $finishingWork
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinishingWork $finish)
    {

        // SAVING DATA TO FINISHING_WORK TABLE
        $data = $this->valData();

        $carpet = Carpet::where('carpet_id', '=', $request->carpetId)->first();
        $newCarpet = CarpetWash::where('carpetId', $request->carpetId)->first();

        if ($newCarpet == null) {
            $newCarpet = Carpet::where('carpet_id', $carpet->carpet_id)->first();
        }
        $category = FinishingTeamCategory::find($request->category_id);
        if (Str::contains($category->category, 'لبکی')) {
            $data['price'] = $newCarpet->height * $request->price * 2;
            $data['price_af'] = $newCarpet->height * $request->price_af * 2;
        } elseif (Str::contains($category->category, 'قیتان')) {
            $data['price'] = $newCarpet->area * $request->price;
            $data['price_af'] = $newCarpet->area * $request->price_af;
        } elseif (Str::contains($category->category, 'رفو')) {
            $data['price'] = $request->price;
            $data['price_af'] = $request->price_af;
        } elseif (Str::contains($category->category, 'چیت')) {
            $data['price'] = $newCarpet->area * $request->price;
            $data['price_af'] = $newCarpet->area * $request->price_af;
        } elseif (Str::contains($category->category, 'کش')) {
            $data['price'] = $newCarpet->area * $request->price;
            $data['price_af'] = $newCarpet->area * $request->price_af;
        } elseif (Str::contains($category->category, 'رنگ')) {
            $data['price'] = $newCarpet->area * $request->price;
            $data['price_af'] = $newCarpet->area * $request->price_af;
        } elseif (Str::contains($category->category, 'پوپک')) {
            $data['price'] = $newCarpet->area * $request->price;
            $data['price_af'] = $newCarpet->area * $request->price_af;
        }
        // SAVING DATA TO FINISHING_WORK TABLE
        $done = $finish->update($data);
        $carpet->total_price = $carpet->total_price + $data['price'] - $request->old_price;
        $carpet->total_price_af = $carpet->total_price_af + $data['price_af'] - $request->af_old_price;
        $carpet->update();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " تیاری قالین نمبر  " . $carpet->carpet_no . " ویرایش شد. ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        if ($done) {
            return redirect('/dashboard/finishing-center')->with('status', 'تیاری موفقانه ویرایش شد');
        } else {
            return redirect()->back()->with('error', 'تیاری  ویرایش نشد');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinishingWork $finishingWork
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinishingWork $finishingWork)
    {
        //
    }

    protected function valData()
    {
        return request()->validate([
            'finish_number' => 'required',
            'price' => 'required',
            'price_af' => 'required',
            'description' => 'required',
            'date' => 'required',
            'carpetId' => 'required',
            'team_id' => 'required',
            'category_id' => 'required',
        ]);
    }
}
