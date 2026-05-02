<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Agents;

use App\CarpetCheckBook;
use App\Kachaee;
use App\FinishingTeam;
use App\CarpetRepair;
use App\OfficeCashBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetRepairController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function return_to_center_from_non_repair($carpet_id){

        $carpet = Carpet::find($carpet_id);
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->carpet_no . " از کچای نشده ها به دفتر مرکزی بازگشت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $carpet->status = 1;
        $carpet->kachaee_id = null;
        $carpet->update();

        return back()->with('status','موفقانه بازگشت شد !');
    }
    public function return_to_center_from_repair($carpet_id){

        $carpet_repair = CarpetRepair::where('carpetId',$carpet_id)->first();
    
        
        $carpet_repair->delete();
        $carpet = Carpet::find($carpet_id);
        
          $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->carpet_no . " از کچای شده ها به دفتر مرکزی بازگشت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $carpet->status = 1;
        $carpet->kachaee_id = null;
        $carpet->update();

        return back()->with('status','موفقانه بازگشت شد !');
    }
    public function index()
    {
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status','=',2)->where('kachaee_id','!=',null)->orderBy('updated_at','DESC')->get();
        $repaireds = CarpetRepair::orderBy('carpetId','DESC')->paginate(30);
        $search = '';
        return view('carpet-repair.index',compact('nonrepaireds', 'agents', 'repaireds','search'));
    }

    public function search_kachaee_number($kachaee_number,$team_id){
        $team = Kachaee::find($team_id);
        $carpet_repairs = CarpetRepair::Where('team_id', '=', $team_id)->where('kachaee_number','=',$kachaee_number)->get();

        $quantity = CarpetRepair::Where('team_id', '=', $team_id)->where('kachaee_number','=',$kachaee_number)->count();

        return view('kachaee.kachaee-number-list', compact('carpet_repairs','team','kachaee_number','quantity'));



    }



    // SENDING CARPET FOR REPAIR
    public function sending_to_repair(Carpet $carpetId){
        $check = CarpetCheckBook::where('carpet_id',$carpetId->carpet_id)->first();
        if(Auth::user()->role != 'SO' && Auth::user()->role != 'SP'){
            if(!empty($check)){
                $okay = CarpetCheckBook::where('carpet_id',$carpetId->carpet_id)->first();
                if($okay->kachaee_amount != 0 || $okay->kachaee_amount != null){
                    $kachaee_team = Kachaee::all();
                    return view('carpet-repair.sending-to-repair',compact('kachaee_team','carpetId'));
                }else{
                    return back()->with('error','قالین مذکور برای کچایی ثبت نشده است !');
                }
            }else{
                return back()->with('error','قالین مذکور برای کچایی ثبت نشده است !');
            }
        }else{
            $kachaee_team = Kachaee::all();
            return view('carpet-repair.sending-to-repair',compact('kachaee_team','carpetId'));
        }


    }
    // REPAIR GETTING DONE
    public function repair_team_selected(Request  $request,Carpet $carpetId){
        $carpetId->status = 2;
        $carpetId->kachaee_id = $request->team_id;
        $carpetId->update();
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpetId->carpet_no . " به کچایی ارسال شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if($carpetId->agent->contract_type == 'contractional')
        {
            return redirect('/dashboard/contract-carpet');
        }elseif($carpetId->agent->contract_type == 'weight')
        {
            return redirect('/dashboard/list-weight');
        }else{
            return redirect('/dashboard/list-buy-carpet');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRepair(Carpet $id)
    {
        $lastId = CarpetRepair::where('team_id',$id->kachaee_id)->latest()->first();
        $KachaeeNo = '';
        if($lastId) {
            $lastId = $lastId->kachaee_number;
            $lastId = substr($lastId,-1);
            $lastId++;
            $KachaeeNo = 'KCH-'.sprintf('%01d' , $lastId);
        } else {
            $KachaeeNo = 'KCH-'.sprintf('%01d'  , '1');
        }
        return view('carpet-repair.create',compact('id','KachaeeNo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CarpetRepair $carpetRepair)
    {

        $data = $this->validAll();
        $carpetRepair->create($data);
        $finish = Carpet::where('carpet_id','=',$request->carpetId)->first();
        
          $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $finish->carpet_no . " کچایی شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $finish->status = 12;
        $finish->update();
        return redirect('/dashboard/carpet-repair')->with('status',' مراحل ترمیم موفقانه ثبت شد');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetRepair $carpetRepair)
    {
        return view('carpet-repair.show',['carpet' => $carpetRepair]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetRepair $carpetRepair)
    {
        return view('carpet-repair.edit',compact('carpetRepair'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetRepair $carpetRepair)
    {

        $data = $this->validAll();
        
        $carpet = Carpet::find($request->carpetId);

         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "کچایی قالین نمبر  " . $carpet->carpet_no . " ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $carpetRepair->update($data);

        return redirect('/dashboard/carpet-repair')->with('status', ' مراحل ترمیم موفقانه بروز شد');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetRepair  $carpetRepair
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarpetRepair $carpetRepair)
    {
        //
    }

    protected function validAll(){
        return request()->validate([
            'price' => 'required',
            'kachaee_number' => 'required',
            'total_price' => 'required',
            'af_total_price' => 'required',
            'date' => 'required',
            'carpetId' => 'required',
            'team_id' => 'required',
            'description' => 'required',
        ]);
    }


    public function repair_search(Request $request)
    {
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status', '=', 2)
            ->where('carpet_no', 'like', '%'.$request->search.'%')
           ->get();


        $repaireds = CarpetRepair::orderBy('carpetId','DESC')->paginate(30);
        $search = '';
        return view('carpet-repair.index', compact('nonrepaireds', 'agents', 'repaireds','search'));
    }
    public function search_repaired(Request $request)
    {
        $search = $request->search;
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status','=',2)->where('kachaee_id','!=',null)->orderBy('updated_at','DESC')->get();
        $repaireds = CarpetRepair::where('kachaee_number', 'like', '%'.$search.'%')
            ->orWhereHas('carpet', function ($query) use ($search) {
                $query->where('carpet_no', 'like', '%' . $search . '%');
            })
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->get();
        return view('carpet-repair.index', compact('nonrepaireds', 'agents', 'repaireds','search'));
    }
    public function repair_date_search(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $agents = Agents::all();
        $nonrepaireds = Carpet::where('status','=',2)->where('kachaee_id','!=',null)->orderBy('updated_at','DESC')->get();
        $repaireds = CarpetRepair::whereBetween("date", [$start, $end])->get();
        $search = 'search';
        return view('carpet-repair.index', compact('nonrepaireds', 'agents', 'repaireds','search'));
    }
}
