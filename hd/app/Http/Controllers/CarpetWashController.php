<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetWash;
use App\Agents;
use App\Carpet;
use App\WashingTeam;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CarpetWashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function return_to_center($wash_id){
        $carpet_wash = CarpetWash::find($wash_id);
        $team_id = $carpet_wash->team_id;
        $carpet = Carpet::where('carpet_id',$carpet_wash->carpetId)->first();
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $carpet->carpet_no . " از شست به  دفتر مرکزی بازگشت داده شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $carpet->status = 1;
        $carpet->washing_id = null;

        $wsh =  DB::table('carpet_washes')->where('carpetId',$carpet_wash->carpetId)->delete();
       $upd =  $carpet->update();



        if ($upd){
            return redirect('/dashboard/carpet-wash')->with('status','موفقانه بازگشت شد !');
        }

    }
    public function return_to_kachaee($wash_id){
        $carpet_wash = CarpetWash::find($wash_id);
        $team_id = $carpet_wash->team_id;
        $carpet = Carpet::where('carpet_id',$carpet_wash->carpetId)->first();
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $carpet->carpet_no . " از شست به  کچایی بازگشت داده شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        
        $carpet->status = 12;
        $carpet->washing_id = null;

        $wsh =  DB::table('carpet_washes')->where('carpetId',$carpet_wash->carpetId)->delete();

        $upd =  $carpet->update();

        if ($upd){
            return redirect('/dashboard/carpet-wash')->with('status','موفقانه بازگشت شد !');
        }
    }
    public function index()
    {
        $agents = Agents::all();
//        $nonwashed = Carpet::where('status', '=', 3)->where('washing_id', '!=', null)->get();
        $washeds = CarpetWash::orderBy('created_at', 'DESC')->paginate(60);

        $team = WashingTeam::all();
        $wash_check = 1;
        return view('carpet-wash.index', compact('agents', 'washeds', 'team', 'wash_check'));
    }

    
    
    public function wash_numbers($team_id)
    {

        $team = WashingTeam::find($team_id);
        $wash = CarpetWash::where('team_id', $team_id)->first();
        $wash_number = $wash->wash_number;
        $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number', '=', $wash->wash_number)->get();

        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number']);
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number', $wash_number)->first();
        $list_for_wash = '';
        $wash_check = 'all';
        return view('carpet-wash.list-from-wash-number', compact('carpet_washes', 'team', 'wash_number', 'list_for_wash', 'wash_numbers', 'wash_date','wash_check'));
    }

    public function search_wash_number_for_wash(Request $request)
    {

        $wash_check = '';

        if ($request->wash_nonwash == 'all'){
            $wash_check = 'all';
        }else if($request->wash_nonwash == 'washed'){
            $wash_check = 'washed';
        }else {
            $wash_check = 'nonwashed';
        }
        $team_id = $request->team_id;
        $wash_number = $request->wash_number;
        $team = WashingTeam::find($team_id);
        $search = $request->search;
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number', $wash_number)->first();
        if ($search) {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })
                ->orderBy('carpetId', 'ASC')->get();
            $wash_number = CarpetWash::Where('team_id', '=', $team_id)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })
                ->first();
				if($wash_number){
                   $wash_number = $wash_number->wash_number;
				}

        } else {

                $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number', '=', $wash_number)->orderBy('carpetId', 'ASC')->get();;


        }
        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number']);
        return view('carpet-wash.list-from-wash-number', compact('carpet_washes', 'team', 'wash_number','list_for_wash', 'wash_numbers', 'wash_date','wash_check'));
    }
    
    public function search_carpet_type_from_wash_number(Request $request)
    {

        $wash_check = '';


        $team_id = $request->team_id;
        $wash_number = $request->wash_number;
        $team = WashingTeam::find($team_id);
        $search = $request->carpet_type_id;
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number', $wash_number)->first();

        $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)
            ->WhereHas('carpet', function ($query) use ($search) {
                $query->where('type_id', $search);
            })
            ->orderBy('carpetId', 'ASC')->get();;


        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number']);
        return view('carpet-wash.list-from-wash-number', compact('carpet_washes', 'team', 'wash_number', 'list_for_wash', 'wash_numbers', 'wash_date', 'wash_check'));
    }
    public function search_wash_numbersh_for_wash(Request $request)
    {
        $team_id = $request->team_id;
        $wash_number_sh = $request->wash_number_sh;
        $team = WashingTeam::find($team_id);
        $search = $request->search;
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number_sh', $wash_number_sh)->first();

        if ($search) {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })
                ->orderBy('carpetId', 'ASC')->get();
            $wash_number_sh = CarpetWash::Where('team_id', '=', $team_id)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })
                ->first();
				if($wash_number_sh){
            $wash_number_sh = $wash_number_sh->wash_number_sh;
				}

        } else {
            $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number_sh', '=', $wash_number_sh)->orderBy('carpetId', 'ASC')->get();;
        }
        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number_sh']);
        return view('carpet-wash.list-from-wash-number-sh', compact('carpet_washes', 'team', 'wash_number_sh','list_for_wash', 'wash_numbers', 'wash_date'));
    }

    public function search_wash_numbersh_payment($wash_number_sh, $team_id)
    {


        $team = WashingTeam::find($team_id);
        $wash_date = CarpetWash::where('team_id', $team_id)->where('wash_number_sh', $wash_number_sh)->first();
        $carpet_washes = CarpetWash::Where('team_id', '=', $team_id)->where('wash_number_sh', '=', $wash_number_sh)->orderBy('carpetId', 'ASC')->get();;
        $list_for_wash = '';
        $wash_numbers = CarpetWash::where('team_id', $team_id)->distinct()->get(['wash_number_sh']);
        return view('carpet-wash.list-from-wash-number-sh', compact('carpet_washes', 'team', 'wash_number_sh', 'list_for_wash', 'wash_numbers', 'wash_date'));
    }

    public function search(Request $request)
    {

        $team_id = $request->team_id;
       $search = $request->search;

        $wash_check = 0;
        if ($request->has('from_non_washed')) {

            $team = WashingTeam::where('id', $team_id)->get();

            $wash_check = 1;
            $washeds = CarpetWash::orderBy('date', 'DESC')->paginate(60);
            return view('carpet-wash.index', compact('washeds', 'team', 'wash_check'));

        } else {

            $washeds = CarpetWash::where('wash_number','like','%'.$search.'%')

                ->orWhere('wash_number_sh','like','%'.$search.'%')
                ->orWhere('date','like','%'.$search.'%')
                ->orWhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%'.$search.'%');
                })
                ->orWhereHas('washing_team', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })

                       ->get();


                $wash_check = 2;
                $team = WashingTeam::all();
                return view('carpet-wash.index', compact('washeds', 'team', 'team_id', 'wash_check'));


        }

    }
    
      public function search_carpet_type(Request $request)
    {

        $search = $request->carpet_type_id;
        $washeds = CarpetWash::WhereHas('carpet', function ($query) use ($search) {
            $query->where('type_id',$search);
        })

            ->get();


            $wash_check = 2;
            $team = WashingTeam::all();
            return view('carpet-wash.index', compact('washeds', 'team', 'wash_check'));




    }



    // CREATEING NEW CARPET WASH RECORD
    public function create_carpet_wash($id)
    {


        $carpet_wash = CarpetWash::find($id);

        $lastId = CarpetWash::where('team_id',$carpet_wash->team_id)->latest()->first();

        $WashNo =  '';


//        if ($lastId) {
            $lastId = $lastId->wash_number_sh_c;
//            $lastId = substr($lastId, -1);
            $lastId++;
//            $WashNo = 'SH-' . sprintf('%01d', $lastId);
            $WashNo = $lastId;

//        } else {
//            $WashNo = 'SH-' . sprintf('%01d', '1');
//            $WashNo = 'SH-1';

//        }



        return view('carpet-wash.create', compact('carpet_wash','WashNo'));
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
    public function store(Request $request, CarpetWash $wash)
    {
        $wa = CarpetWash::find($request->wash_id);
        $team_id = $wa->team_id;
        $carpet_wash = CarpetWash::find($request->wash_id);
        $carpet_wash->wash_number = $request->wash_number;
        $carpet_wash->wash_number_sh = $request->wash_number_sh;
        $carpet_wash->height = $request->height;
        $carpet_wash->width = $request->width;
        $carpet_wash->area = $request->area;
        $carpet_wash->price = $request->price;
        $carpet_wash->af_total_price = $request->af_total_price;
        $carpet_wash->total_price = $request->total_price;
        $carpet_wash->date = $request->date;
        $carpet_wash->description = $request->description;
        $carpet_wash->update();

        $finish = Carpet::where('carpet_id', '=', $request->carpetId)->first();
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $finish->carpet_no . " شسته شد  ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $finish->status = 13;
        $finish->total_price = $finish->total_price + $request->total_price;
        $finish->total_price_af = $finish->total_price_af + $request->af_total_price;
        $finish->update();
        return redirect('/dashboard/carpet-wash/wash-numbers/'.$team_id)->with('status', ' مراحل شست موفقانه ثبت شد');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetWash $wash)
    {
        return view('carpet-wash.show', compact('wash'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetWash $wash)
    {
        $washing_team = WashingTeam::all();

        return view('carpet-wash.edit', compact('wash','washing_team'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetWash $wash)
    {


        $carpet = Carpet::find($request->carpetId);
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " شست قالین نمبر " . $carpet->carpet_no . " ویرایش شد  ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $carpet->total_price = $carpet->total_price - $request->old_price + $request->total_price;
         $carpet->total_price_af = $carpet->total_price_af - $request->af_old_price + $request->af_total_price;
        $carpet->washing_id = $request->team_id;
        $carpet->update();

        $carpet_wash = CarpetWash::find($wash->id);
        $carpet_wash->wash_number = $request->wash_number;
        $carpet_wash->wash_number_sh = $request->wash_number_sh;
        $carpet_wash->height = $request->height;
        $carpet_wash->width = $request->width;
        $carpet_wash->area = $request->area;
        $carpet_wash->price = $request->price;
        $carpet_wash->af_total_price = $request->af_total_price;
        $carpet_wash->total_price = $request->total_price;
        $carpet_wash->date = $request->date;
        $carpet_wash->description = $request->description;
        $carpet_wash->team_id = $request->team_id;
        $carpet_wash->update();
        return redirect('/dashboard/carpet-wash')->with('status', ' مراحل شست موفقانه بروز شد');
    }

    public function sent_to_finishing_center(Carpet $carpet)
    {

        $carpet->status = 4;
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر " . $carpet->carpet_no . " به تیاری ارسال شد  ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $carpet->update();
        return redirect('/dashboard/carpet-wash')->with('status', 'قالین موفقانه به بخش تیاری فرستاده شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetWash $carpetWash
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarpetWash $carpetWash)
    {
        //
    }

    protected function validAll()
    {
        return request()->validate([
            'wash_number' => 'required',
            'price' => 'required',
            'total_price' => 'required',
            'af_total_price' => 'required',
            'date' => 'required',
            'height' => '',
            'width' => '',
            'area' => '',
            'carpetId' => 'required',
            'team_id' => 'required',
            'description' => 'required',
        ]);
    }
}
