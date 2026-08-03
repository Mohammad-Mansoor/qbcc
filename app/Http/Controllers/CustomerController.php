<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Customer;
use App\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

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
        $customers = Customer::paginate(30);
        $this->enrichCustomerRecordsBatch($customers->getCollection());

        $credit_us = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount_af');
        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount_af');

        // Global Receivable Total from Ledger in Base Currency (USD)
        $total_receivable = DB::table('ledger_entries')
            ->where('party_type', 'App\Customer')
            ->sum(DB::raw('base_credit - base_debit'));

        return view('customers.customers', compact('customers', 'customerEdit', 'credit_us', 'credit_af', 'debit_us', 'debit_af', 'total_receivable'));
    }

    private function enrichCustomerRecordsBatch($customers)
    {
        if ($customers->isEmpty()) {
            return $customers;
        }

        $customerIds = $customers->pluck('id')->toArray();

        // 1. Lifetime Sales Value
        $sales = DB::table('sales')
            ->select('customer_id', DB::raw('SUM(sale_cost_total) as total'))
            ->whereIn('customer_id', $customerIds)
            ->where('is_returned', 0)
            ->groupBy('customer_id')
            ->pluck('total', 'customer_id');

        // 2. Live Ledger Balance (Net Position in Base Currency USD)
        $ledgerBalances = DB::table('ledger_entries')
            ->select('party_id', DB::raw('SUM(base_credit - base_debit) as balance'))
            ->where('party_type', 'App\Customer')
            ->whereIn('party_id', $customerIds)
            ->groupBy('party_id')
            ->pluck('balance', 'party_id');

        // 3. Payments totals for USD & AFN
        $paymentBalances = DB::table('customer_payments')
            ->select(
                'customer_id',
                DB::raw("SUM(CASE WHEN type = 'رسید' THEN amount ELSE -amount END) as usd_balance"),
                DB::raw("SUM(CASE WHEN type = 'رسید' THEN amount_af ELSE -amount_af END) as af_balance")
            )
            ->whereIn('customer_id', $customerIds)
            ->where('status', 1)
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        // 4. Last Activity Date
        $lastPayments = DB::table('customer_payments')
            ->select('customer_id', DB::raw('MAX(date) as last_date'))
            ->whereIn('customer_id', $customerIds)
            ->groupBy('customer_id')
            ->pluck('last_date', 'customer_id');

        $lastSales = DB::table('sales')
            ->select('customer_id', DB::raw('MAX(sale_date) as last_date'))
            ->whereIn('customer_id', $customerIds)
            ->groupBy('customer_id')
            ->pluck('last_date', 'customer_id');

        $now = Carbon::now();

        foreach ($customers as $cust) {
            $cust->lifetime_sales = $sales->get($cust->id, 0);
            $cust->ledger_balance = $ledgerBalances->get($cust->id, 0);

            $payBal = $paymentBalances->get($cust->id);
            $cust->total_usd = $payBal ? $payBal->usd_balance : 0;
            $cust->total_af = $payBal ? $payBal->af_balance : 0;

            $lastPay = $lastPayments->get($cust->id);
            $lastSale = $lastSales->get($cust->id);
            $cust->last_activity = max($lastPay, $lastSale);

            if ($cust->last_activity) {
                $cust->days_since_active = $now->diffInDays(Carbon::parse($cust->last_activity));
            } else {
                $cust->days_since_active = null;
            }
        }

        return $customers;
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $customerEdit = "";

        $customers = Customer::where('customer_code', 'like', '%' . $search . '%')
            ->orWhere('name', 'like', '%' . $search . '%')
            ->orWhere('type', 'like', '%' . $search . '%')
            ->orWhere('company_name', 'like', '%' . $search . '%')
            ->orWhere('company_address', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('website', 'like', '%' . $search . '%')
            ->paginate(30);

        $this->enrichCustomerRecordsBatch($customers->getCollection());

        $credit_us = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount_af');
        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount_af');

        // Global Receivable Total from Ledger in Base Currency (USD)
        $total_receivable = DB::table('ledger_entries')
            ->where('party_type', 'App\Customer')
            ->sum(DB::raw('base_credit - base_debit'));

        return view('customers.customers', compact('customers', 'customerEdit', 'credit_us', 'credit_af', 'debit_us', 'debit_af', 'search', 'total_receivable'));
    }

    public function accounts()
    {
        $customerEdit = "";
        $customers = Customer::paginate(30);
        $this->enrichCustomerRecordsBatch($customers->getCollection());

        $credit_us = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount_af');
        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount_af');

        // Global Receivable Total from Ledger in Base Currency (USD)
        $total_receivable = DB::table('ledger_entries')
            ->where('party_type', 'App\Customer')
            ->sum(DB::raw('base_credit - base_debit'));

        $accounts = '';
        return view('customers.customers', compact('customers', 'customerEdit', 'credit_us', 'credit_af', 'debit_us', 'debit_af', 'accounts', 'total_receivable'));
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
        $customers = Customer::paginate(30);
        $credit_us = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount');
        $credit_af = CustomerPayment::where('type', '=', 'رسید')->where('status', 1)->sum('amount_af');

        $debit_us = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount');
        $debit_af = CustomerPayment::where('type', '=', 'گرفت')->where('status', 1)->sum('amount_af');
        return view('customers.customers', compact('customers', 'customerEdit', 'credit_us', 'credit_af', 'debit_us', 'debit_af'));

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
        $customer = Customer::find($id);

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
    public function updateNote(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->note = $request->note;
        $customer->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'یادداشت با موفقیت بروز رسانی شد', 'note' => $customer->note]);
        }

        return redirect()->back()->with('status', 'یادداشت با موفقیت بروز رسانی شد');
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
            'note' => 'nullable',
        ]);
    }
}
