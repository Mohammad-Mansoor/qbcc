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

        $ledgerTotals = $this->getCustomerLedgerTotals();
        $customers_owe_company = $ledgerTotals ? (float)$ledgerTotals->customers_owe_company : 0;
        $company_owes_customers = $ledgerTotals ? (float)$ledgerTotals->company_owes_customers : 0;
        $total_receivable = $customers_owe_company - $company_owes_customers;

        return view('customers.customers', compact('customers', 'customerEdit', 'customers_owe_company', 'company_owes_customers', 'ledgerTotals', 'total_receivable'));
    }

    private function getCustomerLedgerTotals()
    {
        $subQuery = DB::table('ledger_entries')
            ->select('party_id', DB::raw('SUM(base_debit - base_credit) as balance'))
            ->where('party_type', 'App\Customer')
            ->groupBy('party_id');

        return DB::query()->fromSub($subQuery, 'cust_balances')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN balance > 0.0001 THEN balance ELSE 0 END), 0) as customers_owe_company,
                COALESCE(SUM(CASE WHEN balance < -0.0001 THEN ABS(balance) ELSE 0 END), 0) as company_owes_customers,
                COUNT(CASE WHEN balance > 0.0001 THEN 1 END) as count_customers_owe,
                COUNT(CASE WHEN balance < -0.0001 THEN 1 END) as count_company_owes
            ')
            ->first();
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

        // 2. Live Ledger Balance (Net Position in Base Currency USD: Debit > Credit means customer owes company)
        $ledgerBalances = DB::table('ledger_entries')
            ->select('party_id', DB::raw('SUM(base_debit - base_credit) as balance'))
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

        $ledgerTotals = $this->getCustomerLedgerTotals();
        $customers_owe_company = $ledgerTotals ? (float)$ledgerTotals->customers_owe_company : 0;
        $company_owes_customers = $ledgerTotals ? (float)$ledgerTotals->company_owes_customers : 0;
        $total_receivable = $customers_owe_company - $company_owes_customers;

        return view('customers.customers', compact('customers', 'customerEdit', 'search', 'customers_owe_company', 'company_owes_customers', 'ledgerTotals', 'total_receivable'));
    }

    public function accounts()
    {
        $customerEdit = "";
        $customers = Customer::paginate(30);
        $this->enrichCustomerRecordsBatch($customers->getCollection());

        $ledgerTotals = $this->getCustomerLedgerTotals();
        $customers_owe_company = $ledgerTotals ? (float)$ledgerTotals->customers_owe_company : 0;
        $company_owes_customers = $ledgerTotals ? (float)$ledgerTotals->company_owes_customers : 0;
        $total_receivable = $customers_owe_company - $company_owes_customers;

        $accounts = '';
        return view('customers.customers', compact('customers', 'customerEdit', 'accounts', 'customers_owe_company', 'company_owes_customers', 'ledgerTotals', 'total_receivable'));
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
        $this->enrichCustomerRecordsBatch($customers->getCollection());

        $ledgerTotals = $this->getCustomerLedgerTotals();
        $customers_owe_company = $ledgerTotals ? (float)$ledgerTotals->customers_owe_company : 0;
        $company_owes_customers = $ledgerTotals ? (float)$ledgerTotals->company_owes_customers : 0;
        $total_receivable = $customers_owe_company - $company_owes_customers;

        return view('customers.customers', compact('customers', 'customerEdit', 'customers_owe_company', 'company_owes_customers', 'ledgerTotals', 'total_receivable'));

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
