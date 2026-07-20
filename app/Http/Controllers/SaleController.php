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

        $this->middleware('permission:create_sale')->only(['create', 'store']);
        $this->middleware('permission:edit_sale')->only(['edit', 'update']);
        $this->middleware('permission:delete_sale')->only('destroy');
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

        $sales = Sale::with(['carpet', 'invoice', 'customer'])
            ->where(function($query) use ($search) {
                $query->where('type', 'like', '%' . $search . '%')
                      ->orWhere('quality', 'like', '%' . $search . '%')
                      ->orWhereHas('carpet', function($q) use ($search) {
                          $q->where('carpet_no', 'like', '%' . $search . '%')
                            ->orWhere('width', 'like', '%' . $search . '%')
                            ->orWhere('height', 'like', '%' . $search . '%')
                            ->orWhere('area', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('invoice', function($q) use ($search) {
                          $q->where('invoice_no', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('customer_code', 'like', '%' . $search . '%');
                      });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);


        $invoices = Invoice::where('type', 'carpet')->where('status', 'open')->orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';


        return view('sales.sales-list',compact('sales','carpets','invoices','packing_list','sale','search'));
    }

    public function index()
    {
       
        $sales = Sale::with(['carpet', 'invoice', 'customer'])->orderBy('created_at','DESC')->paginate(60);
        $invoices = Invoice::where('type', 'carpet')->where('status', 'open')->orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';
        return view('sales.sales-list',compact('sales','carpets','invoices','packing_list','sale'));
    }
    public function show_all(){
        $sales = Sale::with(['carpet', 'invoice', 'customer'])->orderBy('created_at','DESC')->paginate(50);
        $invoices = Invoice::where('type', 'carpet')->where('status', 'open')->orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';
        $all = '';
        return view('sales.sales-list',compact('sales','carpets','invoices','packing_list','sale','all'));
    }
    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $query = Sale::with(['carpet', 'invoice', 'customer']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('type', 'like', '%' . $search . '%')
                  ->orWhere('quality', 'like', '%' . $search . '%')
                  ->orWhereHas('carpet', function($q2) use ($search) {
                      $q2->where('carpet_no', 'like', '%' . $search . '%')
                        ->orWhere('width', 'like', '%' . $search . '%')
                        ->orWhere('height', 'like', '%' . $search . '%')
                        ->orWhere('area', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('invoice', function($q3) use ($search) {
                      $q3->where('invoice_no', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('customer', function($q4) use ($search) {
                      $q4->where('name', 'like', '%' . $search . '%')
                        ->orWhere('customer_code', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $sales = $query->orderBy('created_at', 'DESC')->get();
        
        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        return view('sales.pdf_sales', compact('sales', 'search', 'logoBase64'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $carpets = Carpet::where('status', 5)->get();
        $invoices = Invoice::where('type', 'carpet')->where('status', 'open')->orderBy('id', 'DESC')->get();
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

            if (!$invoice_id) {
                return redirect()->back()->with('error', 'انوایس نامعتبر است!');
            }
            if ($invoice_id->status === 'closed') {
                return redirect()->back()->with('error', 'این انوایس بسته شده است و امکان افزودن فروش جدید وجود ندارد!');
            }

            $sale = new Sale();
            $sale->sale_cost_per_meter = $request->sale_cost_per_meter;
            $sale->sale_cost_total = $request->sale_cost_total;
            $total_price_cost = $request->total_price_cost ?? $carpet_id->total_price;
            
            $currencyCode = \App\Currency::find($request->currency_id)->code ?? 'USD';
            $exchangeRate = $request->exchange_rate ?? 1.0;
            // FORENSIC RULE: Use safe BCMath multiplication to match AccountingService
            $saleCostUsd = ($currencyCode == 'USD') ? $sale->sale_cost_total : bcmul((string)$sale->sale_cost_total, (string)$exchangeRate, 4);
            
            $sale->profit = $saleCostUsd - $total_price_cost;
            $sale->type = $request->carpet_type;
            $sale->quality = $request->carpet_quality;
            $sale->carpet_id = $request->carpet_id;
            $sale->invoice_id = $request->invoice_id;
            $sale->customer_id = $invoice_id->customer->id;
            $sale->currency_id = $request->currency_id;
            $sale->currency_code = \App\Currency::find($request->currency_id)->code ?? 'USD';
            $sale->exchange_rate = $request->exchange_rate ?? 1.0;
            $sale->sale_date = Carbon::today()->format('Y-m-d');
            $sale->description = "Sale of Carpet " . $carpet_id->carpet_no . " (Type: " . $request->carpet_type . ", Quality: " . $request->carpet_quality . ", Size: " . $carpet_id->width . "x" . $carpet_id->height . ", Area: " . $carpet_id->area . "m²)";
            $sale->save();

            $carpet_id->status = 6;
            $carpet_id->package_id = $request->package_id ?? $carpet_id->package_id;
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
                'warehouse_id' => $carpet_id->warehouse_id,
                'area' => (float)($carpet_id->area ?? 0),
                'unit_cost' => (float)($carpet_id->total_price ?? 0),
                'reference' => $invoice_id->invoice_no,
                'description' => "فروش انوایس نمبر " . $invoice_id->invoice_no . " به مشتری " . ($invoice_id->customer->customer_code ?? ''),
                'currency_code' => $sale->currency_code,
                'exchange_rate' => $sale->exchange_rate,
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
        if ($sale->invoice && $sale->invoice->status === 'closed') {
            return redirect('/dashboard/sales')->with('error', 'این فروش در یک انوایس بسته شده قرار دارد و امکان ویرایش آن وجود ندارد!');
        }

        $invoices = Invoice::where('type', 'carpet')
            ->where(function($q) use ($sale) {
                $q->where('status', 'open');
                if ($sale && $sale->invoice_id) {
                    $q->orWhere('id', $sale->invoice_id);
                }
            })
            ->orderBy('id','DESC')
            ->get();
            
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
        
        $currencies = \App\Currency::all();

        return view('sales.sales-list', compact(
            'carpets', 'invoices', 'sale', 'carpet', 'packing_list', 'package', 'sales',
            'allowedRevenueDebit', 'allowedRevenueCredit', 'mappingRevenue',
            'allowedCogsDebit', 'allowedCogsCredit', 'mappingCogs', 'currencies'
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
            $sale = Sale::find($id);
            if ($sale->invoice && $sale->invoice->status === 'closed') {
                return redirect()->back()->with('error', 'این فروش در یک انوایس بسته شده قرار دارد و امکان ویرایش آن وجود ندارد!');
            }

            $invoice_id = Invoice::find($request->invoice_id);
            if (!$invoice_id) {
                return redirect()->back()->with('error', 'انوایس نامعتبر است!');
            }
            if ($invoice_id->status === 'closed') {
                return redirect()->back()->with('error', 'انوایس مقصد بسته شده است!');
            }

            $carpet_id = Carpet::find($request->carpet_id);
            $carpet_id->package_id = $request->package_id ?? $carpet_id->package_id;
            $carpet_id->save();
            
            $sale = Sale::find($id);

            $sale->sale_cost_per_meter = $request->sale_cost_per_meter;
            $sale->sale_cost_total = $request->sale_cost_total;
            $total_price_cost = $request->total_price_cost ?? $carpet_id->total_price;
            
            $currencyCode = \App\Currency::find($request->currency_id)->code ?? 'USD';
            $exchangeRate = $request->exchange_rate ?? 1.0;
            // FORENSIC RULE: Use safe BCMath multiplication to match AccountingService
            $saleCostUsd = ($currencyCode == 'USD') ? $sale->sale_cost_total : bcmul((string)$sale->sale_cost_total, (string)$exchangeRate, 4);
            
            $sale->profit = $saleCostUsd - $total_price_cost;
            
            $sale->type = $request->carpet_type;
            $sale->quality = $request->carpet_quality;
            $sale->carpet_id = $request->carpet_id;
            $sale->invoice_id = $request->invoice_id;
            $sale->customer_id = $invoice_id->customer->id;
            $sale->currency_id = $request->currency_id;
            $sale->currency_code = \App\Currency::find($request->currency_id)->code ?? 'USD';
            $sale->exchange_rate = $request->exchange_rate ?? 1.0;
            $sale->sale_date = Carbon::today()->format('Y-m-d');
            $sale->description = "Sale of Carpet " . $carpet_id->carpet_no . " (Type: " . $request->carpet_type . ", Quality: " . $request->carpet_quality . ", Size: " . $carpet_id->width . "x" . $carpet_id->height . ", Area: " . $carpet_id->area . "m²)";
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
                'warehouse_id' => $carpet_id->warehouse_id,
                'area' => (float)($carpet_id->area ?? 0),
                'unit_cost' => (float)($carpet_id->total_price ?? 0),
                'reference' => 'SALE-' . $sale->id,
                'description' => "ویرایش فروش انوایس نمبر " . $invoice_id->invoice_no . " به مشتری " . ($invoice_id->customer->customer_code ?? ''),
                'currency_code' => $sale->currency_code,
                'exchange_rate' => $sale->exchange_rate,
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
