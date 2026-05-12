<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Customer;
use App\Invoice;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class InvoiceController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;

    public function __construct(\App\Services\AccountingService $accountingService, \App\Services\InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
    }

    public function sending_to_stock($carpet_id)
    {
        return DB::transaction(function () use ($carpet_id) {
            $carpet = Carpet::findOrFail($carpet_id);
            $sale = Sale::where('carpet_id', $carpet_id)->first();

            if (!$sale) {
                return response()->json(['error' => 'success']);
            }

            // 1. Unified Reversal (Inventory + Accounting)
            $this->inventoryManager->reverseTransactions($sale, 'Carpet Returned to Stock (Invoice Action)');

            // 2. Log Activity
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "  قالین نمبر  " . $carpet->carpet_no . " از لیست فروشات به گدام بازگشت شد و اسناد مالی معکوس گردید. ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // 3. Return Carpet to Stock
            $carpet->status = 5;
            $carpet->package_id = null; 
            $carpet->update();

            // 4. Delete Sale Record
            $sale->delete();

            return response()->json(['status' => 'success']);
        });
    }
    public function index()
    {
        $customers = Customer::where('type','مشتری قالین')->get();
        $invoices = Invoice::paginate(30);
        $invoiceEdit = "";
        $lastId = Invoice::latest()->first();

        if($lastId) {
            $lastId = $lastId->invoice_no;
            $exp = explode('-',$lastId);
            $invoice_no = end($exp);
            $invoice_no++;
            $invoice_no = 'QB-'.$invoice_no;
        } else {
            $invoice_no = 'QB-'.'1';
        }
        return view('invoices.index',compact('customers','invoiceEdit','invoice_no','invoices'));
    }
    public function search(Request $request)
    {
        $search = $request->search;
        $customers = Customer::where('type','مشتری قالین')->get();
        $invoices = Invoice::where('invoice_no', 'like', '%' . $search . '%')
            ->orwhere('invoice_date', 'like', '%' . $search . '%')
            ->orwhereHas('customer', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->paginate(30);

        $invoiceEdit = "";
        $lastId = Invoice::latest()->first();

        if($lastId) {
            $lastId = $lastId->invoice_no;
            $exp = explode('-',$lastId);
            $invoice_no = end($exp);
            $invoice_no++;
            $invoice_no = 'QB-'.$invoice_no;
        } else {
            $invoice_no = 'QB-'.'1';
        }
        return view('invoices.index',compact('customers','invoiceEdit','invoice_no','invoices'));

    }

    public function search_carpet(Request $request)
    {
        $search = $request->search;
        $invoice = Invoice::find($request->invoice_id);

        $sales =  DB::table('sales')
            ->join('carpets', 'sales.carpet_id', 'carpets.carpet_id')
            ->join('carpet_types','carpet_types.carpet_type_id','carpets.type_id')

            ->where('carpets.carpet_no', 'like', '%' . $search . '%')
            ->orWhere('carpets.width', 'like', '%' . $search . '%')
            ->orWhere('carpets.height', 'like', '%' . $search . '%')
            ->orWhere('carpets.area', 'like', '%' . $search . '%')
            ->orWhere('carpet_types.carpet_type', 'like', '%' . $search . '%')
            ->paginate(30);



        return view('invoices.invoice-details',compact('invoice','sales','search'));



    }

    public function search_invoice_number($invoice_number , $customer_id){


        $customer = Customer::find($customer_id);
        $invoice = Invoice::Where('customer_id', '=', $customer_id)->where('invoice_no','=',$invoice_number)->first();

        $sales = Sale::where('invoice_id',$invoice->id)->where('customer_id',$customer_id)->paginate(30);

        return view('customers.invoice-number-list', compact('sales','customer','invoice_number'));



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
        $this->accountingService->failIfLocked($request->invoice_date);
        $data = $request->validate([
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'customer_id' => 'required',
            'invoice_description' => '',
        ]);
        $invoice = Invoice::create($data);
        
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "  انوایس نمبر  " . $request->invoice_no . " در سیستم اضافه شد. ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        
        if($invoice) {
            return redirect('/dashboard/invoices')->with('status', ' موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/invoices')->with('error', 'مشکل در سرور وجود داره!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::find($id);
        $sales  = Sale::where('invoice_id',$id)->paginate(30);
        return view('invoices.invoice-details',compact('invoice','sales'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $customers = Customer::all();
        $invoices = Invoice::paginate(10);
        $invoiceEdit = Invoice::find($id);

        return view('invoices.index',compact('customers','invoiceEdit','invoices'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->accountingService->failIfLocked($request->invoice_date);
        $data = $request->validate([
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'customer_id' => 'required',
            'invoice_description' => '',
        ]);
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "  انوایس نمبر  " . $request->invoice_no . " ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        
      $inv =   $invoice->update($data);
        if($inv) {
            return redirect('/dashboard/invoices')->with('status', ' موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/invoices')->with('error', 'مشکل در سرور وجود داره!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
