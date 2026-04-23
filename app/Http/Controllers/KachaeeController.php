<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Kachaee;
use App\KachaeePayment;
use App\OfficeDebit;
use App\OfficeCashBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KachaeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $team = Kachaee::orderBy('id','DESC')->get();
        $credit_us = KachaeePayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = KachaeePayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = KachaeePayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = KachaeePayment::where('type', '=', 'گرفت')->sum('amount_af');
        $teamEdit = '';
        return view('kachaee.index',compact('team','credit_af','credit_us','debit_us','debit_af','teamEdit'));
    }
    public function accounts(){
        $team = Kachaee::orderBy('id','DESC')->get();
        $credit_us = KachaeePayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = KachaeePayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = KachaeePayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = KachaeePayment::where('type', '=', 'گرفت')->sum('amount_af');

        $accounts = '';
        $teamEdit = '';
        return view('kachaee.index',compact('team','credit_af','credit_us','debit_us','debit_af','accounts','teamEdit'));
    }

    public function search(Request $request)
    {
        $search = $request->search;


        $team = Kachaee::where('name', 'like','%'.$search.'%')
            ->orWhere('father_name', 'like', '%' .$search.'%')
            ->orWhere('grand_father_name', 'like', '%'.$search.'%')
            ->orWhere('g_name', 'like', '%'.$search.'%')
            ->orWhere('address', 'like', '%'.$search.'%')
            ->orWhere('national_id', 'like', '%'.$search.'%')
            ->orWhere('contact_no', 'like', '%'.$search.'%')
            ->get();
        $credit_us = KachaeePayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = KachaeePayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = KachaeePayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = KachaeePayment::where('type', '=', 'گرفت')->sum('amount_af');
        $teamEdit = '';
        return view('kachaee.index',compact('team','credit_af','credit_us','debit_us','debit_af','search','teamEdit'));


    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('kachaee.create');
    }

    // SAVING RECEIVED OF KACHAEE WORKER


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Kachaee $team)
    {
        $done = $team->create($this->valData());
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "  کچایی گر به نام  " . $request->name . " در سیستم اضافه شد. ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if($done){
            return redirect('/dashboard/kachaee-team')->with('status','کارگر موفقانه ثبت شد');
        }else{
            return redirect()->back()->with('error','کارگر  ثبت نشد');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kachaee  $kachaee
     * @return \Illuminate\Http\Response
     */
    public function show(Kachaee $team)
    {

        $quantity = Carpet::where('kachaee_id', $team->id)->where('status','!=',2)->count();

        $paymentEdit = '';
        return view('kachaee.kachaee-payment',compact('total','paid','remaining','team','quantity','resived','paymentEdit'));
    }

    // EDITING RECEIVED OF KACHAEE



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kachaee  $kachaee
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teamEdit = Kachaee::find($id);
        $team = Kachaee::orderBy('id','DESC')->get();
        $credit_us = KachaeePayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = KachaeePayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = KachaeePayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = KachaeePayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('kachaee.index',compact('team','credit_af','credit_us','debit_us','debit_af','teamEdit'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kachaee  $kachaee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kachaee $team)
    {
        $done = $team->update($this->valData());
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "  کچایی گر به نام  " . $request->name . " در سیستم ویرایش شد. ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if ($done) {
            return redirect('/dashboard/kachaee-team')->with('status', 'کارگر موفقانه بروز شد');
        } else {
            return redirect()->back()->with('error', 'کارگر  ویرایش  نشد');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kachaee  $kachaee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kachaee $kachaee)
    {
        //
    }

    protected function valData(){
        return request()->validate([
            'name' => 'required',
            'father_name' => 'required',
            'grand_father_name' => 'required',
            'g_name' => '',
            'national_id' => 'required',
            'contact_no' => 'required',
            'address' => 'required',
        ]);
    }
    protected function valReceived(){
        return request()->validate([
            'amount' => 'required',
            'af_amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'kachaee_id' => 'required',
            'debit_id' => ''
        ]);
    }
}
