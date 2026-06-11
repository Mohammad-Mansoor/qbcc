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
        $customer_orders = CustomerOrder::with(['customer', 'details'])->orderBy('co_id', 'DESC')->get();
        $main_customers = \App\Customer::orderBy('name')->get();
        $orderEdit = null;
        $nextOrderNumber = $this->calculateNextOrderNumber(date('Y-m-d'));

        return view('customer-orders.customer-orders', compact('orderEdit', 'customer_orders', 'main_customers', 'nextOrderNumber'));
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
        // 1. Generate the unique order name if not provided or set to placeholder
        if (!$request->order_name || $request->order_name === 'تولید خودکار سریالی' || strpos($request->order_name, 'ORD-') !== 0) {
            $generatedName = $this->calculateNextOrderNumber($request->order_date ?? date('Y-m-d'));
            $request->merge(['order_name' => $generatedName]);
        }

        $data = $request->validate([
            'order_name' => 'required|unique:customer_orders,order_name',
            'order_date' => 'required|date',
            'main_customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($request->status == 'completed') {
            return redirect()->back()->with('error', 'یک فرمایش جدید نمی‌تواند مستقیماً تکمیل شده باشد. ابتدا باید قالین‌ها را ثبت و تکمیل کنید.');
        }

        $ord = CustomerOrder::create([
            'order_name' => $request->order_name,
            'order_date' => $request->order_date,
            'main_customer_id' => $request->main_customer_id,
            'status' => $request->status,
            'customer_order' => $request->order_name, // fallback for legacy column
            'customer_id' => null, // null out legacy c_id
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
        $orderEdit = CustomerOrder::findOrFail($order_id);
        $customer_orders = CustomerOrder::with(['customer', 'details'])->orderBy('co_id', 'DESC')->get();
        $main_customers = \App\Customer::orderBy('name')->get();
        $nextOrderNumber = $orderEdit->order_name;

        return view('customer-orders.customer-orders', compact('orderEdit', 'customer_orders', 'main_customers', 'nextOrderNumber'));
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
            'order_name' => 'required|unique:customer_orders,order_name,' . $order_id . ',co_id',
            'order_date' => 'required|date',
            'main_customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($request->status == 'completed') {
            $totalCarpets = DB::table('customer_order_details')->where('customer_order_id', $order_id)->count();
            $nonCompleted = DB::table('customer_order_details')
                ->where('customer_order_id', $order_id)
                ->where('current_status', '!=', 'completed')
                ->count();

            if ($totalCarpets == 0) {
                return redirect()->back()->with('error', 'نمی‌توانید وضعیت فرمایش را به تکمیل شده تغییر دهید زیرا هیچ قالینی برای این فرمایش ثبت نشده است.');
            }
            if ($nonCompleted > 0) {
                return redirect()->back()->with('error', 'نمی‌توانید وضعیت فرمایش را به تکمیل شده تغییر دهید زیرا همه قالین‌های این فرمایش تکمیل نشده‌اند.');
            }
        }

        $order = CustomerOrder::findOrFail($order_id);
        $ord = $order->update([
            'order_name' => $request->order_name,
            'order_date' => $request->order_date,
            'main_customer_id' => $request->main_customer_id,
            'status' => $request->status,
            'customer_order' => $request->order_name
        ]);

        if ($ord) {
            return redirect('/dashboard/customer-orders')->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/customer-orders')->with('error', 'مشکل در سرور وجود داره!');
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
        DB::table('customer_order_details')->where('customer_order_id', $order_id)->delete();
        $ord = DB::table('customer_orders')->where('co_id', $order_id)->delete();

        if ($ord) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    public function calculateNextOrderNumber($date)
    {
        $year = Carbon::parse($date)->format('Y');
        $prefix = "ORD-" . $year . "-";
        
        $nextSeq = 1;
        do {
            $lastOrder = CustomerOrder::where('order_name', 'like', $prefix . '%')
                ->orderBy('order_name', 'desc')
                ->first();
                
            if ($lastOrder) {
                $parts = explode('-', $lastOrder->order_name);
                $lastSeq = intval(end($parts));
                $nextSeq = $lastSeq + 1;
            } else {
                $nextSeq = 1;
            }
            
            $generatedName = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            
            $exists = CustomerOrder::where('order_name', $generatedName)->exists();
            if (!$exists) {
                break;
            }
            $nextSeq++; // try next number
        } while (true);

        return $generatedName;
    }

    public function getNextOrderNumber(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $nextNumber = $this->calculateNextOrderNumber($date);
        return response()->json(['next_number' => $nextNumber]);
    }
}
