<?php

namespace App\Http\Controllers;

use App\Activity;
use App\SellerPayment;
use App\StringSeller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StringSellerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_string_sellers')->only(['index', 'search', 'accounts', 'show']);
        $this->middleware('permission:create_string_seller')->only(['create', 'store']);
        $this->middleware('permission:edit_string_seller')->only(['edit', 'update']);
        $this->middleware('permission:delete_string_seller')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellerEdit = "";
        $sellers = $this->getSellersQuery();

        // Global Aggregates (Legacy)
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');
        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $currencies = \App\Currency::all();

        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af','currencies'));
    }
    public function search(Request $request)
    {
        $search = $request->search;
        $sellers = $this->getSellersQuery($search);
        $sellerEdit = "";
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $currencies = \App\Currency::all();

        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af','currencies'));
    }
    public function accounts(){
        $sellerEdit = "";
        $sellers = $this->getSellersQuery();
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $accounts = '';
        $currencies = \App\Currency::all();
        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af','accounts','currencies'));
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
        $sellers = $this->getSellersQuery();
        $credit_us = SellerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = SellerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = SellerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = SellerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $currencies = \App\Currency::all();

        return view('string-seller.string-seller' , compact('sellers','sellerEdit','credit_us','credit_af','debit_us','debit_af','currencies'));
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
    private function getSellersQuery($search = null)
    {
        $query = StringSeller::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        
        return $query->orderBy('id', 'desc')->get()->map(function($seller) {
            // 1. Accounting Balance (Real-time from ledger)
            $seller->accounting_balance = DB::table('ledger_entries')
                ->where('party_type', 'App\StringSeller')
                ->where('party_id', $seller->id)
                ->sum(DB::raw("credit - debit"));

            // 2. Legacy Balance (AFN & USD)
            $seller->legacy_af = DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'رسید')->sum('amount_af') 
                               - DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'گرفت')->sum('amount_af');
            
            $seller->legacy_usd = DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'رسید')->sum('amount') 
                                - DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'گرفت')->sum('amount');
            
            // 3. Total Supplied (kg)
            $seller->total_supplied = DB::table('purchase_materials')
                ->where('seller_id', $seller->id)
                ->where('status', 1)
                ->sum('quantity');

            // 4. Last Activity
            $lastPurchase = DB::table('purchase_materials')->where('seller_id', $seller->id)->latest('created_at')->value('created_at');
            $lastPayment = DB::table('seller_payments')->where('seller_id', $seller->id)->latest('created_at')->value('created_at');
            $seller->last_activity = max($lastPurchase, $lastPayment);

            return $seller;
        });
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
