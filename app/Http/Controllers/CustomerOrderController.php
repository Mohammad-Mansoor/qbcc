<?php

namespace App\Http\Controllers;

use App\CustomerAccountOrder;
use App\CustomerOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewCustomerOrderAlert;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all main customers with their order counts
        $main_customers = \App\Customer::withCount(['orders', 'orders as pending_orders_count' => function ($query) {
            $query->where('status', 'pending');
        }, 'orders as in_progress_orders_count' => function ($query) {
            $query->where('status', 'in_progress');
        }])->orderBy('name')->get();

        return view('customer-orders.customers-list', compact('main_customers'));
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
            'end_date' => 'nullable|date',
            'main_customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,completed,cancel',
            'customer_order_number' => 'nullable|string|max:255',
        ]);

        if ($request->status == 'completed') {
            return redirect()->back()->with('error', 'یک فرمایش جدید نمی‌تواند مستقیماً تکمیل شده باشد. ابتدا باید قالین‌ها را ثبت و تکمیل کنید.');
        }

        $ord = CustomerOrder::create([
            'order_name' => $request->order_name,
            'order_date' => $request->order_date,
            'end_date' => $request->end_date,
            'main_customer_id' => $request->main_customer_id,
            'status' => $request->status,
            'customer_order_number' => $request->customer_order_number,
            'customer_order' => $request->order_name, // fallback for legacy column
            'customer_id' => null, // null out legacy c_id
        ]);

        if ($ord) {
            $receivers = User::permission('receive_customer_order_alerts')->get();
            if ($receivers->isNotEmpty()) {
                Notification::send($receivers, new NewCustomerOrderAlert($ord));
            }
            return redirect('/dashboard/customer-orders/' . $request->main_customer_id)->with('status', 'Order Successfully Added!');
        } else {
            return redirect()->back()->with('error', 'Internal Server Error!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $customer_id
     * @return \Illuminate\Http\Response
     */
    /**
     * Display the specified resource.
     *
     * @param int $customer_id
     * @return \Illuminate\Http\Response
     */
    public function show($customer_id)
    {
        $customer = \App\Customer::findOrFail($customer_id);
        $customer_orders = CustomerOrder::with('details')->where('main_customer_id', $customer_id)->orderBy('co_id', 'DESC')->get();
        $orderEdit = null;
        $nextOrderNumber = $this->calculateNextOrderNumber(date('Y-m-d'));

        return view('customer-orders.customer-orders', compact('customer', 'orderEdit', 'customer_orders', 'nextOrderNumber'));
    }

    public function exportPdf($customer_id, Request $request)
    {
        return $this->generateCustomerOrdersReport($customer_id, $request, 'pdf');
    }

    public function exportExcel($customer_id, Request $request)
    {
        return $this->generateCustomerOrdersReport($customer_id, $request, 'excel');
    }

    private function generateCustomerOrdersReport($customer_id, Request $request, $type)
    {
        $customer = \App\Customer::findOrFail($customer_id);

        $query = CustomerOrder::with('details')->where('main_customer_id', $customer_id);

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Filter (order_name or customer_order_number)
        if ($request->filled('search') || $request->filled('q')) {
            $searchTerm = $request->input('search', $request->input('q'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_order_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Date Filter
        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        $customer_orders = $query->orderBy('co_id', 'DESC')->get();

        // Calculate aggregate statistics
        $totalOrders = $customer_orders->count();
        $pendingOrders = $customer_orders->where('status', 'pending')->count();
        $inProgressOrders = $customer_orders->where('status', 'in_progress')->count();
        $completedOrders = $customer_orders->where('status', 'completed')->count();
        $canceledOrders = $customer_orders->where('status', 'cancel')->count();

        $totalCarpets = 0;
        $completedCarpets = 0;
        $totalArea = 0;

        foreach ($customer_orders as $co) {
            $totalCarpets += $co->details->count();
            $completedCarpets += $co->details->whereIn('current_status', ['ready', 'shipped'])->count();
            foreach ($co->details as $detail) {
                $totalArea += (float)$detail->area;
            }
        }

        $kpis = [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'in_progress_orders' => $inProgressOrders,
            'completed_orders' => $completedOrders,
            'canceled_orders' => $canceledOrders,
            'total_carpets' => $totalCarpets,
            'completed_carpets' => $completedCarpets,
            'total_area' => $totalArea,
        ];

        $issueDate = Carbon::now()->format('Y-m-d H:i');

        if ($type === 'excel') {
            return view('customer-orders.reports.customer_orders_excel', compact(
                'customer', 'customer_orders', 'kpis', 'issueDate', 'request'
            ));
        } else {
            $logoPath = public_path('images/logo.png');
            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

            return view('customer-orders.reports.customer_orders_pdf', compact(
                'customer', 'customer_orders', 'kpis', 'issueDate', 'request', 'logoBase64'
            ));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $order_id
     * @return \Illuminate\Http\Response
     */
    public function edit($order_id)
    {
        if (!auth()->user()->can('edit_customer_order')) {
            abort(403, 'شما اجازه ویرایش این بخش را ندارید.');
        }
        $orderEdit = CustomerOrder::findOrFail($order_id);
        $customer = \App\Customer::findOrFail($orderEdit->main_customer_id);
        $customer_orders = CustomerOrder::with('details')->where('main_customer_id', $customer->id)->orderBy('co_id', 'DESC')->get();
        $nextOrderNumber = $orderEdit->order_name;

        return view('customer-orders.customer-orders', compact('customer', 'orderEdit', 'customer_orders', 'nextOrderNumber'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $order_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        if (!auth()->user()->can('edit_customer_order')) {
            abort(403, 'شما اجازه ویرایش این بخش را ندارید.');
        }
        $data = $request->validate([
            'order_name' => 'required|unique:customer_orders,order_name,' . $order_id . ',co_id',
            'order_date' => 'required|date',
            'end_date' => 'nullable|date',
            'main_customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,completed,cancel',
            'customer_order_number' => 'nullable|string|max:255',
        ]);

        if ($request->status == 'completed') {
            $totalCarpets = DB::table('customer_order_details')->where('customer_order_id', $order_id)->count();
            $nonCompleted = DB::table('customer_order_details')
                ->where('customer_order_id', $order_id)
                ->whereNotIn('current_status', ['ready', 'shipped', 'cancelled'])
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
            'end_date' => $request->end_date,
            'main_customer_id' => $request->main_customer_id,
            'status' => $request->status,
            'customer_order_number' => $request->customer_order_number,
            'customer_order' => $request->order_name
        ]);

        if ($ord) {
            return redirect('/dashboard/customer-orders/' . $request->main_customer_id)->with('status', 'موفقانه ثبت شد !');
        } else {
            return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
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
        if (!auth()->user()->can('delete_customer_order')) {
            return response()->json(['status' => 'error', 'message' => 'شما اجازه حذف ندارید.']);
        }
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
