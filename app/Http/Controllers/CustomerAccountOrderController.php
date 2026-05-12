<?php

namespace App\Http\Controllers;

use App\CustomerAccountOrder;
use App\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAccountOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountEdit = "";
        $customers = CustomerAccountOrder::all();

        return view('customer-orders.customer-accounts', compact('accountEdit', 'customers'));
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $account = DB::table('customer_account_orders')->insertGetId(['customer_name' => $request->customer_name, 'customer_country' => $request->customer_country]);

        if ($account) {
            return redirect('/dashboard/customer-account-for-orders')->with('status', 'CustomerSuccesfully Added');
        } else {
            return redirect('/dashboard/customer-account-for-orders')->with('error', 'Internel Server Error');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CustomerAccountOrder $customerAccountOrder
     * @return \Illuminate\Http\Response
     */
    public function show($customer_id)
    {
        $customer = CustomerAccountOrder::find($customer_id);
        $customer_orders = DB::table('customer_orders')->where('customer_id',$customer_id)->orderBy('co_id','DESC')->get();
        $orderEdit = null;
        $main_customers = \App\Customer::all();

        return view('customer-orders.customer-orders', compact('orderEdit', 'customer','customer_orders', 'main_customers'));



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CustomerAccountOrder $customerAccountOrder
     * @return \Illuminate\Http\Response
     */
    public function edit($customer_id)
    {
        $accountEdit = DB::table('customer_account_orders')->where('c_id', $customer_id)->first();
        $customers = CustomerAccountOrder::all();

        return view('customer-orders.customer-accounts', compact('accountEdit', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CustomerAccountOrder $customerAccountOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customer_id)
    {


        $customer = DB::table('customer_account_orders')->where('c_id', $customer_id)->update(['customer_name' => $request->customer_name, 'customer_country' => $request->customer_country]);
        if ($customer) {
            return redirect('/dashboard/customer-account-for-orders')->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/customer-account-for-orders')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CustomerAccountOrder $customerAccountOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy($customer_id)
    {
        $customer = DB::table('customer_account_orders')->where('c_id', $customer_id)->delete();

        return response()->json(['status' => 'success']);

    }
}
