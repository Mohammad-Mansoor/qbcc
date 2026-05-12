<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Carpet;
use App\Customer;
use App\Invoice;
use App\Package;
use App\PakingList;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\AccountingService;
use App\ChartOfAccount;

class SaleController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;

    public function __construct(AccountingService $accountingService, \App\Services\InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
      public function get_by_carpet(Request $request)
    {
        if (!$request->carpet_id) {
            $html = '<option value="">' . trans('لطفا انتخاب کنید') . '</option>';
        } else {

          $html = Carpet::find($request->carpet_id);
          return $html;

        }

        return response()->json(['html' => $html]);
    }

    public function search(Request $request)
    {
         $search = $request->search;

        $sales =  DB::table('sales')
            ->join('carpets', 'sales.carpet_id', 'carpets.carpet_id')
//            ->join('invoices', 'sales.invoice_id', 'invoices.id')
//            ->join('customers', 'sales.customer_id', 'customers.id')
            ->where('sales.type', 'like', '%' . $search . '%')
            ->orWhere('sales.quality', 'like', '%' . $search . '%')
//            ->orWhere('invoices.invoice_no', 'like', '%' . $search . '%')
//            ->orWhere('customers.name', 'like', '%' . $search . '%')
//            ->orWhere('customers.customer_code', 'like', '%' . $search . '%')
            ->orWhere('carpets.carpet_no', 'like', '%' . $search . '%')
            ->orWhere('carpets.width', 'like', '%' . $search . '%')
            ->orWhere('carpets.height', 'like', '%' . $search . '%')
            ->orWhere('carpets.area', 'like', '%' . $search . '%')
            ->paginate(30);


        $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';


        return view('sales.sales-list',compact('sales','carpets','invoices','packing_list','sale','search'));



    }

    public function index()
    {
       
        $sales = Sale::with(['carpet', 'invoice', 'customer'])->orderBy('created_at','DESC')->paginate(60);
        $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';
        return view('sales.sales-list',compact('sales','carpets','invoices','packing_list','sale'));
    }
    public function show_all(){
        $sales = Sale::with(['carpet', 'invoice', 'customer'])->orderBy('created_at','DESC')->paginate(50);
        $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';
        $all = '';
        return view('sales.sales-list',compact('sales','carpets','invoices','packing_list','sale','all'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $carpets = Carpet::where('status', 5)->get();
        $invoices = Invoice::orderBy('id', 'DESC')->get();
        $packing_list = PakingList::orderBy('id', 'DESC')->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('SALES_REVENUE', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('SALES_REVENUE', 'credit');
        
        $mapping = \App\MappingRule::where('mapping_key', 'SALES_REVENUE')->first();

        return view('sales.create-sale', compact(
            'carpets', 'invoices', 'packing_list',
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->accountingService->failIfLocked(Carbon::today()->format('Y-m-d'));
        return DB::transaction(function () use ($request) {
            $carpet_id = Carpet::find($request->carpet_id);
            $invoice_id = Invoice::find($request->invoice_id);

            $sale = new Sale();
            $sale->sale_cost_per_meter = $request->sale_cost_per_meter;
            $sale->sale_cost_total = $request->sale_cost_total;
            $sale->profit = $request->sale_cost_total - $request->total_price_cost;
            $sale->type = $request->carpet_type;
            $sale->quality = $request->carpet_quality;
            $sale->carpet_id = $request->carpet_id;
            $sale->invoice_id = $request->invoice_id;
            $sale->customer_id = $invoice_id->customer->id;
            $sale->customer_code = $request->customer_code;
            $sale->carpet_height = $request->carpet_height;
            $sale->carpet_width = $request->carpet_width;
            $sale->carpet_area = $request->carpet_area;
            $sale->save();

            $carpet_id->status = 6;
            $carpet_id->package_id = $request->package_id;
            $carpet_id->save();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر " . $carpet_id->carpet_no . " به فروش رسید ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // Unified Orchestration: Inventory OUT + Revenue Post + COGS Post
            $results = $this->inventoryManager->processSale($carpet_id, [
                'date' => Carbon::today()->format('Y-m-d'),
                'sale_amount' => $sale->sale_cost_total,
                'customer_id' => $sale->customer_id,
                'quantity' => 1,
                'reference' => 'SALE-' . $sale->id,
                'description' => "فروش قالین نمبر " . $carpet_id->carpet_no . " به مشتری " . $sale->customer_code,
                'override_debit_account_id' => $request->override_debit_account_id,
                'override_credit_account_id' => $request->override_credit_account_id,
                'override_cogs_debit_id' => $request->override_cogs_debit_id,
                'override_cogs_credit_id' => $request->override_cogs_credit_id,
            ]);

            if ($results && isset($results['revenue_transaction'])) {
                $sale->ledger_transaction_id = $results['revenue_transaction']->id;
                $sale->save();
            }

            return redirect('/dashboard/carpet-stock')->with('status', ' موفقانه ثبت شد و در دفتر روزنامچه ثبت گردید!');
        });
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sale = Sale::find($id);
        $invoices = Invoice::orderBy('id','DESC')->get();
        $carpet = Carpet::where('carpet_id',$sale->carpet_id)->first();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $package = Package::find($carpet->package_id);
        $carpets = Carpet::where('status',5)->get();
        $sales = Sale::orderBy('created_at','DESC')->paginate(30);

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedRevenueDebit = $selectionService->getValidAccounts('SALES_REVENUE', 'debit');
        $allowedRevenueCredit = $selectionService->getValidAccounts('SALES_REVENUE', 'credit');
        $mappingRevenue = \App\MappingRule::where('mapping_key', 'SALES_REVENUE')->first();

        $allowedCogsDebit = $selectionService->getValidAccounts('SALES_COGS', 'debit');
        $allowedCogsCredit = $selectionService->getValidAccounts('SALES_COGS', 'credit');
        $mappingCogs = \App\MappingRule::where('mapping_key', 'SALES_COGS')->first();

        return view('sales.sales-list', compact(
            'carpets', 'invoices', 'sale', 'carpet', 'packing_list', 'package', 'sales',
            'allowedRevenueDebit', 'allowedRevenueCredit', 'mappingRevenue',
            'allowedCogsDebit', 'allowedCogsCredit', 'mappingCogs'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->accountingService->failIfLocked(Carbon::today()->format('Y-m-d'));
        return DB::transaction(function () use ($request, $id) {
            $carpet_id = Carpet::find($request->carpet_id);
            $carpet_id->package_id = $request->package_id;
            $carpet_id->save();
            
            $invoice_id = Invoice::find($request->invoice_id);
            $sale = Sale::find($id);

            $sale->sale_cost_per_meter = $request->sale_cost_per_meter;
            $sale->sale_cost_total = $request->sale_cost_total;
            $sale->profit = $request->sale_cost_total - $request->total_price_cost;
            $sale->type = $request->carpet_type;
            $sale->quality = $request->carpet_quality;
            $sale->carpet_id = $request->carpet_id;
            $sale->invoice_id = $request->invoice_id;
            $sale->customer_id = $invoice_id->customer->id;
            $sale->customer_code = $request->customer_code;
            $sale->carpet_height = $request->carpet_height;
            $sale->carpet_width = $request->carpet_width;
            $sale->carpet_area = $request->carpet_area;
            $sale->update();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "فروش قالین نمبر " . $carpet_id->carpet_no . " ویرایش ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            // Reverse Old Accounting & Inventory Entries for this Sale and Post New Ones
            $this->inventoryManager->reverseTransactions($sale, 'Sale Record Edited (Re-posting)');
            
            // Re-post using Unified Orchestration
            $results = $this->inventoryManager->processSale($carpet_id, [
                'date' => Carbon::today()->format('Y-m-d'),
                'sale_amount' => $sale->sale_cost_total,
                'customer_id' => $sale->customer_id,
                'quantity' => 1,
                'reference' => 'SALE-' . $sale->id,
                'description' => "ویرایش فروش قالین نمبر " . $carpet_id->carpet_no . " به مشتری " . $sale->customer_code,
                'override_debit_account_id' => $request->override_debit_account_id,
                'override_credit_account_id' => $request->override_credit_account_id,
                'override_cogs_debit_id' => $request->override_cogs_debit_id,
                'override_cogs_credit_id' => $request->override_cogs_credit_id,
            ]);

            if ($results && isset($results['revenue_transaction'])) {
                $sale->ledger_transaction_id = $results['revenue_transaction']->id;
                $sale->save();
            }

            return redirect('/dashboard/sales')->with('status', 'ویرایش موفقانه ثبت شد و اسناد حسابداری بروزرسانی گردید!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
