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
            $sale = Sale::where('carpet_id', $carpet_id)->where('is_returned', 0)->first();

            if (!$sale) {
                return response()->json(['status' => 'error', 'message' => 'Sale record not found']);
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

            // Find the reversal ledger transaction
            $reversalTx = \App\LedgerTransaction::where('source_id', $sale->id)
                ->where('source_type', 'App\Sale')
                ->where('reference', 'like', 'REV-%')
                ->orderBy('id', 'desc')
                ->first();

            // 4. Mark Sale Record as Returned (Non-destructive)
            $sale->is_returned = 1;
            $sale->returned_at = Carbon::now();
            $sale->return_ledger_transaction_id = $reversalTx ? $reversalTx->id : null;
            $sale->returned_by = Auth::user()->id;
            $sale->save();

            // 5. Recalculate invoice payment_status now that one item is returned
            if ($sale->invoice_id) {
                $invoice = \App\Invoice::with(['sale', 'payments'])->find($sale->invoice_id);
                if ($invoice) {
                    $newTotal = $invoice->sale->where('is_returned', 0)->sum('sale_cost_total');
                    $totalPaid = \App\InvoicePayment::where('invoice_id', $invoice->id)->sum('amount_applied') ?? 0;
                    $remaining = $newTotal - $totalPaid;

                    if ($totalPaid <= 0) {
                        $invoice->payment_status = 'unpaid';
                    } elseif ($remaining <= 0.01) {
                        $invoice->payment_status = 'paid';
                    } else {
                        $invoice->payment_status = 'partially_paid';
                    }
                    $invoice->save();
                }
            }

            return response()->json(['status' => 'success']);
        });
    }
    public function index()
    {
        $customers = Customer::where('type','مشتری قالین')->get();
        $agents = \App\Agents::with('user')->get();
        $invoices = Invoice::orderBy('id', 'desc')->paginate(30);
        $invoiceEdit = "";
        $invoice_no = Invoice::generateNextInvoiceNo();
        return view('invoices.index',compact('customers','agents','invoiceEdit','invoice_no','invoices'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $customers = Customer::where('type','مشتری قالین')->get();
        $agents = \App\Agents::with('user')->get();
        
        $invoices = Invoice::where('invoice_no', 'like', '%' . $search . '%')
            ->orWhere('invoice_date', 'like', '%' . $search . '%')
            ->orWhereHas('customer', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('agent.user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(30);

        $invoiceEdit = "";
        $invoice_no = Invoice::generateNextInvoiceNo();
        return view('invoices.index',compact('customers','agents','invoiceEdit','invoice_no','invoices'));
    }

    public function search_carpet(Request $request)
    {
        $search = $request->search;
        $invoice = Invoice::find($request->invoice_id);

        if ($invoice->type === 'carpet') {
            $sales =  DB::table('sales')
                ->join('carpets', 'sales.carpet_id', 'carpets.carpet_id')
                ->join('carpet_types','carpet_types.carpet_type_id','carpets.type_id')
                ->where('sales.invoice_id', $request->invoice_id)
                ->where(function($q) use ($search) {
                    $q->where('carpets.carpet_no', 'like', '%' . $search . '%')
                      ->orWhere('carpets.width', 'like', '%' . $search . '%')
                      ->orWhere('carpets.height', 'like', '%' . $search . '%')
                      ->orWhere('carpets.area', 'like', '%' . $search . '%')
                      ->orWhere('carpet_types.carpet_type', 'like', '%' . $search . '%');
                })
                ->select('sales.*', 'carpets.carpet_no', 'carpets.width', 'carpets.height', 'carpets.area')
                ->paginate(30);
        } else {
            $sales = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', 'material_categories.material_category_id')
                ->join('material_types', 'material_sales.type_id', 'material_types.material_type_id')
                ->where('material_sales.invoice_id', $request->invoice_id)
                ->where(function($q) use ($search) {
                    $q->where('material_sales.sale_number', 'like', '%' . $search . '%')
                      ->orWhere('material_categories.material_category', 'like', '%' . $search . '%')
                      ->orWhere('material_types.material_type', 'like', '%' . $search . '%');
                })
                ->select('material_sales.*', 'material_categories.material_category', 'material_types.material_type')
                ->paginate(30);
        }

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
        
        $rules = [
            'invoice_no' => 'required|unique:invoices,invoice_no',
            'invoice_date' => 'required',
            'type' => 'required|in:carpet,dye,yarn',
            'invoice_description' => '',
        ];
        
        if ($request->type === 'carpet') {
            $rules['customer_id'] = 'required';
        } else {
            $rules['agent_id'] = 'required';
        }
        
        $data = $request->validate($rules);
        $data['status'] = 'open';
        
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
        if ($invoice->type === 'carpet') {
            $sales = Sale::where('invoice_id',$id)->paginate(30);
        } else {
            $sales = \App\MaterialSale::where('invoice_id',$id)->paginate(30);
        }
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
        $customers = Customer::where('type','مشتری قالین')->get();
        $agents = \App\Agents::with('user')->get();
        $invoices = Invoice::orderBy('id', 'desc')->paginate(30);
        $invoiceEdit = Invoice::findOrFail($id);
        
        if ($invoiceEdit->status === 'closed') {
            return redirect('/dashboard/invoices')->with('error', 'امکان ویرایش انوایس بسته شده وجود ندارد!');
        }

        return view('invoices.index',compact('customers','agents','invoiceEdit','invoices'));
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
        
        if ($invoice->status === 'closed') {
            return redirect('/dashboard/invoices')->with('error', 'امکان ویرایش انوایس بسته شده وجود ندارد!');
        }
        
        $rules = [
            'invoice_no' => 'required|unique:invoices,invoice_no,' . $invoice->id,
            'invoice_date' => 'required',
            'type' => 'required|in:carpet,dye,yarn',
            'invoice_description' => '',
        ];
        
        if ($request->type === 'carpet') {
            $rules['customer_id'] = 'required';
            $request->merge(['agent_id' => null]);
        } else {
            $rules['agent_id'] = 'required';
            $request->merge(['customer_id' => null]);
        }
        
        $data = $request->validate($rules);
        $data['customer_id'] = $request->customer_id;
        $data['agent_id'] = $request->agent_id;
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "  انوایس نمبر  " . $request->invoice_no . " ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $inv = $invoice->update($data);
        if($inv) {
            return redirect('/dashboard/invoices')->with('status', ' موفقانه ثبت شد !');
        }
        else{
            return redirect('/dashboard/invoices')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Close the invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function closeInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = 'closed';
        $invoice->save();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "انوایس نمبر " . $invoice->invoice_no . " بسته شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect()->back()->with('status', 'انوایس با موفقیت بسته شد!');
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
