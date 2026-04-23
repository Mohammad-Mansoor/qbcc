<?php

namespace App\Http\Controllers;

use App\CustomerAccountOrder;
use App\CustomerOrder;
use App\CustomerOrderDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function close_to_end_customer_order()
    {
        $today = Carbon::today();

        $today->modify('+31 days');
        $modified_date = $today->format('Y-m-d');


        $orders = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('end_date','<=',$modified_date)->where('current_status','On loom')->get();

        return view('customer-orders.close-to-end-order-list', compact('orders'));


    }

    public function index()
    {
        //
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
            'quality' => 'required',
            'height' => 'required',
            'width' => 'required',
            'area' => 'required',
            'warp' => 'required',
            'weft' => 'required',
            'wash_type' => 'required',
            'pile_height' => 'required',
            'weaver_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'photo' => 'required',
            'current_status' => 'required',
            'customer_order_id' => 'required',
        ]);

        $image = '';
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {

                return redirect()->back()->with('error', 'Photo must be Png, Jpg, Jpeg!');
            }
            $fileName = time() . '' . rand(1000, 9999) . '-order-image.' . $fileExt;
            $image = $file->move('uploads/customer-order-image/', $fileName);
        }

        $ord = DB::table('customer_order_details')->insertGetId([ 'quality' => $request->quality, 'height' => $request->height, 'width' => $request->width, 'area' => $request->area, 'warp' => $request->warp,
            'weft' => $request->weft, 'wash_type' => $request->wash_type, 'pile_height' => $request->pile_height, 'weaver_code' => $request->weaver_code, 'start_date' => $request->start_date, 'end_date' => $request->end_date,
            'photo' => $image, 'current_status' => $request->current_status, 'customer_order_id' => $request->customer_order_id]);


        if ($ord) {
            return redirect()->back()->with('status', 'Order Successfully Added!');
        } else {
            return redirect()->back()->with('error', 'Internel Server Error!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerOrderDetails  $customerOrderDetails
     * @return \Illuminate\Http\Response
     */
    public function show($order_id)
    {


        $customer_order = CustomerOrder::find($order_id);
        $customer_order_details = DB::table('customer_order_details')->where('customer_order_id',$order_id)->orderBy('cod_id','DESC')->get();
        $orderEdit = '';

        return view('customer-orders.customer-order-details', compact('orderEdit', 'customer_order','customer_order_details'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerOrderDetails  $customerOrderDetails
     * @return \Illuminate\Http\Response
     */
    public function edit($customer_order_details_id)
    {
        $orderEdit = CustomerOrderDetails::find($customer_order_details_id);
        $customer_order_details = DB::table('customer_order_details')->orderBy('cod_id','DESC')->get();
        $customer_order = CustomerOrder::find($orderEdit->customer_order_id);


        return view('customer-orders.customer-order-details', compact('orderEdit', 'customer_order','customer_order_details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerOrderDetails  $customerOrderDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$order_detail_id)
    {
        $data = $request->validate([
            'quality' => 'required',
            'height' => 'required',
            'width' => 'required',
            'area' => 'required',
            'warp' => 'required',
            'weft' => 'required',
            'wash_type' => 'required',
            'pile_height' => 'required',
            'weaver_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'current_status' => 'required',

        ]);

        $image = '';
        if ($request->photo){

            if ($request->has('photo')) {
                $file = $request->file('photo');
                $fileExt = $file->getClientOriginalExtension();
                if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {

                    return redirect()->back()->with('error', 'Photo must be Png, Jpg, Jpeg!');
                }
                $fileName = time() . '' . rand(1000, 9999) . '-order-image.' . $fileExt;
                $image = $file->move('uploads/customer-order-image/', $fileName);
            }


        }
        if ($image){
            DB::table('customer_order_details')->where('cod_id',$order_detail_id)->update(['photo'=>$image]);
        }

        $ord = DB::table('customer_order_details')->where('cod_id',$order_detail_id)->update(['quality' => $request->quality, 'height' => $request->height, 'width' => $request->width, 'area' => $request->area, 'warp' => $request->warp,
            'weft' => $request->weft, 'wash_type' => $request->wash_type, 'pile_height' => $request->pile_height, 'weaver_code' => $request->weaver_code, 'start_date' => $request->start_date, 'end_date' => $request->end_date,
            'current_status' => $request->current_status]);




        if ($ord) {
            return redirect('/dashboard/customer-order-details/'.$request->customer_order_id)->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/customer-order-details/'.$request->customer_order_id)->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerOrderDetails  $customerOrderDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_detail_id)
    {
        $ord = DB::table('customer_order_details')->where('cod_id', $order_detail_id)->delete();

        if ($ord) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
}
