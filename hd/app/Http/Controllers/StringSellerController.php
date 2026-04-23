<?php

namespace App\Http\Controllers;

use App\Activity;
use App\SellerPayment;
use App\StringSeller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StringSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellerEdit = "";
        $sellers = StringSeller::all();
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');

        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af'));
    }
    public function search(Request $request)
    {
        $search = $request->search;


        $sellers = StringSeller::where('name', 'like','%'.$search.'%')
            ->orWhere('phone', 'like', '%' .$search.'%')
            ->orWhere('address', 'like', '%'.$search.'%')
            ->get();
        $sellerEdit = "";
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');

        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af'));


    }
    public function accounts(){
        $sellerEdit = "";
        $sellers = StringSeller::all();
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $accounts = '';
        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af','accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $this->valData();
        $string_seller = StringSeller::create($data);
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " فروشنده مواد به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if($string_seller) {
            return redirect('/dashboard/string-seller')->with('status', 'حساب موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/string-seller')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StringSeller  $stringSeller
     * @return \Illuminate\Http\Response
     */
    public function show(StringSeller $stringSeller)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StringSeller  $stringSeller
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sellerEdit = StringSeller::find($id);
        $sellers = StringSeller::all();
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');

        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StringSeller  $stringSeller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $seller = StringSeller::find($id);
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " فروشنده مواد به نام " . $request->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $seller->update($this->valData());
        if($seller) {
            return redirect('/dashboard/string-seller')->with('status', 'حساب تار فروش موفقانه بروز شد !');
        }
        else{
            return redirect('/dashboard/string-seller')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StringSeller  $stringSeller
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seller = StringSeller::find($id);
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " فروشنده مواد به نام " . $seller->name . " از سیستم حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $seller->delete();
        if($seller) {
            return response()->json(['status' => 'success']);
        }
    }
    protected function valData()
    {
        return request()->validate([
            'name' => 'required|min:2|max:256',
            'phone' => 'required|min:3|max:14',
            'address' => 'required',
        ]);
    }
}
