<?php

namespace App\Http\Controllers;

use App\CustomerAccountOrder;
use App\CustomerOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


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
        $data = $request->validate([
            'order_name' => 'required',
            'order_date' => 'required',
            'customer_id' => 'required',
        ]);


        $ord = DB::table('customer_orders')->insertGetId(['order_name' => $request->order_name, 'order_date' => $request->order_date, 'customer_id' => $request->customer_id]);


        if ($ord) {
            return redirect()->back()->with('status', 'Order Successfully Added!');
        } else {
            return redirect()->back()->with('error', 'Internel Server Error!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\CustomerOrder $customerOrder
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerOrder $customerOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CustomerOrder $customerOrder
     * @return \Illuminate\Http\Response
     */
    public function edit($order_id)
    {
        $orderEdit = CustomerOrder::find($order_id);
        $customer_orders = DB::table('customer_orders')->orderBy('co_id','DESC')->get();
        $customer = CustomerAccountOrder::find($orderEdit->customer_id);


        return view('customer-orders.customer-orders', compact('orderEdit', 'customer','customer_orders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CustomerOrder $customerOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {



        $data = $request->validate([
            'order_name' => 'required',
            'order_date' => 'required',
        ]);


        $ord = DB::table('customer_orders')->where('co_id',$order_id)->update(['order_name' => $request->order_name,   'order_date' => $request->order_date]);


        if ($ord) {
            return redirect('/dashboard/customer-account-for-orders/'.$request->customer_id)->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/customer-account-for-orders/'.$request->customer_id)->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CustomerOrder $customerOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_id)
    {
        $ord = DB::table('customer_orders')->where('co_id', $order_id)->delete();

        if ($ord) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
}
