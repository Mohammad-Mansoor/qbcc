<?php

namespace App\Http\Controllers;

use App\CustomerAccountOrder;
use App\CustomerOrder;
use App\CustomerOrderDetails;
use App\Services\AccountingService;
use App\Services\InventoryTransactionManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderDetailsController extends Controller
{
    public function __construct()
    {
        // Standalone specifications tracker
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function close_to_end_customer_order()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }

        $notifications = auth()->check() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->take(50)->get() : collect();

        $today = \Carbon\Carbon::today();
        $modified_date = $today->copy()->addDays(31)->format('Y-m-d');

        $orders = \App\CustomerOrder::with('customer')
            ->where('status', '!=', 'completed')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $modified_date)
            ->orderBy('co_id', 'desc')
            ->get();

        return view('customer-orders.close-to-end-order-list', compact('orders', 'notifications'));
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
            'warp' => 'nullable',
            'weft' => 'nullable',
            'wash_type' => 'nullable',
            'pile_height' => 'nullable',
            'weaver_code' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_status' => 'required',
            'customer_order_id' => 'required',
            'unit_price' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric',
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

        $currency_code = 'USD';
        $currency_id = $request->currency_id;
        if ($currency_id) {
            $currency = \App\Currency::find($currency_id);
            if ($currency) {
                $currency_code = $currency->code;
            }
        }

        $ord = DB::table('customer_order_details')->insertGetId([ 
            'quality' => $request->quality, 
            'height' => $request->height, 
            'width' => $request->width, 
            'area' => $request->area, 
            'warp' => $request->warp,
            'weft' => $request->weft, 
            'wash_type' => $request->wash_type, 
            'pile_height' => $request->pile_height, 
            'weaver_code' => $request->weaver_code, 
            'start_date' => $request->start_date, 
            'end_date' => $request->end_date,
            'unit_price' => $request->unit_price ?? 0,
            'total_amount' => $request->total_amount ?? 0,
            'currency_id' => $currency_id,
            'currency_code' => $currency_code,
            'exchange_rate' => $request->exchange_rate ?? 1,
            'photo' => $image, 
            'current_status' => $request->current_status, 
            'customer_order_id' => $request->customer_order_id
        ]);

        if ($ord) {
            return redirect()->back()->with('status', 'Order Successfully Added!');
        } else {
            return redirect()->back()->with('error', 'Internal Server Error!');
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
        $customer_order = CustomerOrder::with('customer')->find($order_id);
        $customer_order_details = DB::table('customer_order_details')->where('customer_order_id',$order_id)->orderBy('cod_id','DESC')->get();
        
        if (request()->export === 'pdf') {
            $logoPath = public_path('images/logo.png');
            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

            return view('customer-orders.customer-order-details-pdf', compact('customer_order', 'customer_order_details', 'logoBase64'));
        }

        $orderEdit = null;
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('customer-orders.customer-order-details', compact('orderEdit', 'customer_order','customer_order_details', 'currencies'));
    }

    public function show_carpet($carpet_id)
    {
        $carpet = DB::table('customer_order_details')->where('cod_id', $carpet_id)->first();
        if (!$carpet) {
            return redirect()->back()->with('error', 'قالین یافت نشد.');
        }
        $customer_order = CustomerOrder::find($carpet->customer_order_id);
        
        return view('customer-orders.carpet-specification-details', compact('carpet', 'customer_order'));
    }

    public function edit($customer_order_details_id)
    {
        $orderEdit = CustomerOrderDetails::find($customer_order_details_id);
        $customer_order_details = DB::table('customer_order_details')->where('customer_order_id', $orderEdit->customer_order_id)->orderBy('cod_id','DESC')->get();
        $customer_order = CustomerOrder::find($orderEdit->customer_order_id);
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('customer-orders.customer-order-details', compact('orderEdit', 'customer_order','customer_order_details', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerOrderDetails  $customerOrderDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_detail_id)
    {
        $data = $request->validate([
            'quality' => 'required',
            'height' => 'required',
            'width' => 'required',
            'area' => 'required',
            'warp' => 'nullable',
            'weft' => 'nullable',
            'wash_type' => 'nullable',
            'pile_height' => 'nullable',
            'weaver_code' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'current_status' => 'required',
            'unit_price' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric',
        ]);

        $image = '';
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $fileExt = $file->getClientOriginalExtension();
            $fileName = time() . '' . rand(1000, 9999) . '-order-image.' . $fileExt;
            $image = $file->move('uploads/customer-order-image/', $fileName);
        }

        $currency_code = 'USD';
        $currency_id = $request->currency_id;
        if ($currency_id) {
            $currency = \App\Currency::find($currency_id);
            if ($currency) {
                $currency_code = $currency->code;
            }
        }

        $updateData = [
            'quality' => $request->quality,
            'height' => $request->height,
            'width' => $request->width,
            'area' => $request->area,
            'warp' => $request->warp,
            'weft' => $request->weft,
            'wash_type' => $request->wash_type,
            'pile_height' => $request->pile_height,
            'weaver_code' => $request->weaver_code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'unit_price' => $request->unit_price ?? 0,
            'total_amount' => $request->total_amount ?? 0,
            'currency_id' => $currency_id,
            'currency_code' => $currency_code,
            'exchange_rate' => $request->exchange_rate ?? 1,
            'current_status' => $request->current_status,
        ];

        if ($image != '') {
            $updateData['photo'] = $image;
        }

        DB::table('customer_order_details')->where('cod_id', $order_detail_id)->update($updateData);

        return redirect('/dashboard/customer-order-details/' . $request->customer_order_id)->with('status', 'Order Successfully Updated!');
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

    /**
     * Receive the custom order into physical stock (Disabled/Stubbed)
     */
    public function receiveIntoStock($id, Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'ERP/Inventory integration is disabled.']);
    }

    /**
     * Process the final sale of the custom order (Disabled/Stubbed)
     */
    public function processFinalSale($id, Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'ERP/Financial integration is disabled.']);
    }

    /**
     * Change carpet status inline
     */
    public function changeStatus(Request $request, $order_detail_id)
    {
        $request->validate([
            'status' => 'required|in:graphing,dyeing,on_loom,off_loom,washing,finishing,repairing,ready,shipped,paused,cancelled',
            'carpet_number' => 'nullable|string'
        ]);

        $detail = DB::table('customer_order_details')->where('cod_id', $order_detail_id)->first();
        if (!$detail) {
            return redirect()->back()->with('error', 'قالین یافت نشد.');
        }

        $updateData = ['current_status' => $request->status];
        
        if ($request->status === 'off_loom' && $request->filled('carpet_number')) {
            $updateData['carpet_number'] = $request->carpet_number;
        }

        DB::table('customer_order_details')
            ->where('cod_id', $order_detail_id)
            ->update($updateData);

        // If carpet is downgraded to non-completed, check if order is completed and downgrade it to in_progress
        if (!in_array($request->status, ['ready', 'shipped'])) {
            DB::table('customer_orders')
                ->where('co_id', $detail->customer_order_id)
                ->where('status', 'completed')
                ->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('status', 'وضعیت قالین با موفقیت بروزرسانی شد.');
    }
}
