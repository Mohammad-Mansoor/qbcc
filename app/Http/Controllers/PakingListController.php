<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Package;
use App\PakingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PakingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $packing_list = PakingList::all();

        $packingEdit = "";
        $lastId = PakingList::latest()->first();

        if($lastId) {
            $lastId = $lastId->packing_no;
            $exp = explode('-',$lastId);
            $packing_no = end($exp);
            $packing_no++;
            $packing_no = 'PACKING-LIST-'.$packing_no;
        } else {
            $packing_no = 'PACKING-LIST-'.'1';
        }


        return view('packing-list.index',compact('packing_list','packingEdit','packing_no'));

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
            'packing_no' => 'required',
        ]);
        $packing = PakingList::create($data);
        if($packing) {

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پکینگ  " . $request->packing_no . " در سیستم اضافه شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            return redirect('/dashboard/packing-list')->with('status', ' موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/packing-list')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PakingList  $pakingList
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $no = 1;
        $package_list = Package::where('packing_id',$id)->with('carpet')->get();

        $packing_id = PakingList::find($id);


        $lastId = Package::where('packing_id',$packing_id->id)->latest()->first();




        if($lastId) {
            $lastId = $lastId->package_no;
            $exp = explode('-',$lastId);
            $package_no = end($exp);
            $package_no++;
              $package_no = 'BALE-'.$package_no;


        } else {
            $package_no = 'BALE-no-'.'1';

        }

        return view('packing-list.package-list',compact('packing_id','package_no','package_list','no'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PakingList  $pakingList
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {



    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PakingList  $pakingList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PakingList $pakingList)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PakingList  $pakingList
     * @return \Illuminate\Http\Response
     */
    public function destroy(PakingList $pakingList)
    {
        //
    }
}
