<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = CarpetOrder::all();
        $orderEdit = '';
        return view('carpet-order.carpet-orders' , compact('orders','orderEdit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('carpet-order.create-carpet-order');
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
        $order = new CarpetOrder();
        $order->order_number = $request->order_number;
        $order->design_number = $request->design_number;


        $order->save();
        if($order) {
            
               $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " شماره فرمایش  " . $request->order_number . " در سیستم اضافه شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            
            return redirect('/dashboard/carpet-orders')->with('status', ' موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/carpet-orders')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetOrder  $carpetOrder
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetOrder $carpetOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetOrder  $carpetOrder
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderEdit = CarpetOrder::find($id);
        $orders = CarpetOrder::all();
        return view('carpet-order.carpet-orders' , compact('orders','orderEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetOrder  $carpetOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $this->valData();
        $order =  CarpetOrder::find($id);
        $order->order_number = $request->order_number;
        $order->design_number = $request->design_number;

        $order->update();
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " شماره فرمایش  " . $request->order_number . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        if($order) {
            return redirect('/dashboard/carpet-orders')->with('status', ' موفقانه ویرایش شد !');
        }
        else{
            return redirect('/dashboard/carpet-orders')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetOrder  $carpetOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seller = CarpetOrder::find($id);
        $seller->delete();
        if($seller) {
            return response()->json(['status' => 'success']);
        }
    }
    protected function valData()
    {
        return request()->validate([
            'order_number' => 'required',
            'design_number' => 'required',
   
        ]);
    }
   
}
