<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


    }

    public function get_by_packing(Request $request)
    {
        if (!$request->packing_id) {
            $html = '<option value="">' . trans('لطفا انتخاب کنید') . '</option>';
        } else {
            $html = '';
            $packages = Package::where('packing_id', $request->packing_id)->get();
            foreach ($packages as $p) {
                $html .= '<option value="' . $p->id . '">' . $p->package_no . '</option>';
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $data = $request->validate([
            'package_no' => 'required',
            'packing_id' => 'required',
        ]);
        $packing = Package::create($data);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " پکیج  " . $request->package_no . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        

        if ($packing) {
            return back()->with('status', ' موفقانه ثبت شد !');
        } else {
            return back()->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Package $package
     * @return \Illuminate\Http\Response
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Package $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Package $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Package $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
        //
    }
}
