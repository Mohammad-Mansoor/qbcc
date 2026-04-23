<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Customer;
use App\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $customerEdit = "";
        $customers = Customer::all();
        $credit_us = CustomerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('customers.customers',compact('customers','customerEdit','credit_us','credit_af','debit_us','debit_af'));
    }
    public function search(Request $request)
    {
        $search = $request->search;

        $customerEdit = "";
        $customers = Customer::where('customer_code', 'like','%'.$search.'%')
            ->orWhere('name', 'like', '%' .$search.'%')
            ->orWhere('type', 'like', '%'.$search.'%')
            ->orWhere('company_name', 'like', '%'.$search.'%')
            ->orWhere('company_address', 'like', '%'.$search.'%')
            ->orWhere('phone', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->orWhere('website', 'like', '%'.$search.'%')
            ->get();
        $credit_us = CustomerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('customers.customers',compact('customers','customerEdit','credit_us','credit_af','debit_us','debit_af','search'));


    }
    public function accounts(){
        $customerEdit = "";
        $customers = Customer::all();
        $credit_us = CustomerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        $accounts = '';
        return view('customers.customers',compact('customers','customerEdit','credit_us','credit_af','debit_us','debit_af','accounts'));
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
        $data = $this->valData();
        $customer = Customer::create($data);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " مشتری به نام " . $request->name . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        if ($customer) {
            return redirect('/dashboard/customers')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/customers')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $customerEdit = Customer::find($id);
        $customers = Customer::all();
        $credit_us = CustomerPayment::where('type', '=', 'رسید')->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->sum('amount_af');

        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->sum('amount_af');
        return view('customers.customers',compact('customers', 'customerEdit','credit_us','credit_af','debit_us','debit_af'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer =  Customer::find($id);
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " مشتری به نام " . $customer->name . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $customer->update($this->valData());

        if ($customer) {
            return redirect('/dashboard/customers')->with('status', ' موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/customers')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
    protected function valData()
    {
        return request()->validate([
            'customer_code' => 'required|min:2|max:256',
            'name' => 'required|min:2|max:256',
            'type' => 'required|min:2|max:256',
            'company_name' => 'required|min:2|max:256',
            'company_address' => 'required|min:2|max:256',
            'phone' => 'required|min:3|max:14',
            'email' => '',
            'website' => '',
        ]);
    }
}
