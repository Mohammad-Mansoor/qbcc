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
    protected $inventoryManager;
    protected $accountingService;

    public function __construct(InventoryTransactionManager $inventoryManager, AccountingService $accountingService)
    {
        $this->inventoryManager = $inventoryManager;
        $this->accountingService = $accountingService;
    }
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
            'currency_code' => 'required',
            'exchange_rate' => 'required|numeric',
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
            'currency_code' => $request->currency_code ?? 'USD',
            'exchange_rate' => $request->exchange_rate ?? 1,
            'photo' => $image, 
            'current_status' => $request->current_status, 
            'customer_order_id' => $request->customer_order_id
        ]);


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
        $orderEdit = null;

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
            'currency_code' => 'required',
            'exchange_rate' => 'required|numeric',
        ]);

        $image = '';
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $fileExt = $file->getClientOriginalExtension();
            $fileName = time() . '' . rand(1000, 9999) . '-order-image.' . $fileExt;
            $image = $file->move('uploads/customer-order-image/', $fileName);
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
            'currency_code' => $request->currency_code ?? 'USD',
            'exchange_rate' => $request->exchange_rate ?? 1,
            'current_status' => $request->current_status,
        ];

        if ($image != '') {
            $updateData['photo'] = $image;
        }

        DB::table('customer_order_details')->where('cod_id', $order_detail_id)->update($updateData);

        return redirect()->back()->with('status', 'Order Successfully Updated!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerOrderDetails  $customerOrderDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_detail_id)
    {
        $ordDetail = CustomerOrderDetails::find($order_detail_id);
        
        // Reverse any linked transactions if they exist
        if ($ordDetail->carpet_id) {
             $this->inventoryManager->reverseTransactions($ordDetail, 'Order Detail Record Deleted');
        }

        $ord = DB::table('customer_order_details')->where('cod_id', $order_detail_id)->delete();

        if ($ord) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    /**
     * Receive the custom order into physical stock
     */
    public function receiveIntoStock($id, Request $request)
    {
        try {
            return DB::transaction(function () use ($id, $request) {
                $detail = CustomerOrderDetails::with('order.customer')->findOrFail($id);
                
                if ($detail->carpet_id) {
                    return response()->json(['status' => 'error', 'message' => 'Already received into stock']);
                }

                // 1. Create a physical Carpet record (Legacy requirement)
                $carpet = new \App\Carpet();
                $carpet->carpet_no = $request->carpet_no;
                
                // Ensure valid quality_id or leave null (if allowed)
                $q_id = $detail->quality_id ?? $request->quality_id;
                if ($q_id && \App\Quality::where('id', $q_id)->exists()) {
                    $carpet->quality_id = $q_id;
                } else {
                    $carpet->quality_id = \App\Quality::value('id'); // Get first available or null
                }

                // Ensure valid type_id
                $t_id = $request->type_id ?? 1;
                if (\App\CarpetType::where('carpet_type_id', $t_id)->exists()) {
                    $carpet->type_id = $t_id;
                } else {
                    $carpet->type_id = \App\CarpetType::value('carpet_type_id');
                }

                $carpet->width = $detail->height; 
                $carpet->height = $detail->width;
                $carpet->area = $detail->area;
                $carpet->warehouse_id = $request->warehouse_id ?? 1;
                $carpet->agent_id = \App\Agents::value('agent_id') ?? 1; 
                $carpet->status = 5; // In Stock
                $carpet->date = \Carbon\Carbon::today()->format('Y-m-d');
                $carpet->save();

                // 2. Orchestrate through Inventory Manager (Accounting + Inventory Tx)
                $this->inventoryManager->processProductionCompletion($carpet, [
                    'amount' => $detail->total_amount, // Initial value
                    'quantity' => 1,
                    'reference' => 'ORD-REC-' . $detail->cod_id,
                    'description' => "Order #" . $detail->order->order_name . " received into stock",
                    'warehouse_id' => $carpet->warehouse_id,
                ]);

                // 3. Link back
                $detail->carpet_id = $carpet->carpet_id;
                $detail->current_status = 'Ready';
                $detail->save();

                return response()->json(['status' => 'success', 'message' => 'Carpet received into stock successfully']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Process the final sale of the custom order
     */
    public function processFinalSale($id, Request $request)
    {
        try {
            return DB::transaction(function () use ($id, $request) {
                $detail = CustomerOrderDetails::with(['order.customer', 'carpet'])->findOrFail($id);
                
                if (!$detail->carpet_id) {
                    return response()->json(['status' => 'error', 'message' => 'Carpet not yet in stock']);
                }

                if ($detail->current_status == 'Shipped') {
                    return response()->json(['status' => 'error', 'message' => 'Already sold and shipped']);
                }

                // 1. Orchestrate Sale through Inventory Manager
                // This handles: DR AR, CR Revenue, DR COGS, CR Inventory
                $this->inventoryManager->processSale($detail->carpet, [
                    'sale_amount' => $detail->total_amount,
                    'customer_id' => $detail->order->main_customer_id ?? $detail->order->customer_id, // Link to MASTER customer
                    'date' => \Carbon\Carbon::today()->format('Y-m-d'),
                    'reference' => 'ORD-SALE-' . $detail->cod_id,
                    'description' => "Final delivery for Custom Order #" . $detail->order->order_name,
                    'quantity' => 1,
                    'warehouse_id' => $detail->carpet->warehouse_id ?? 1,
                ]);

                // 2. Update Status
                $detail->current_status = 'Shipped';
                $detail->save();

                // Update physical carpet status
                $detail->carpet->status = 6; // Sold
                $detail->carpet->save();

                return response()->json(['status' => 'success', 'message' => 'Final sale processed and order shipped']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }
}
