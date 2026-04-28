<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Agents;
use App\Customer;
use App\MaterialCategory;
use App\MaterialSale;
use App\MaterialStock;
use App\MaterialType;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialSaleController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postMaterialSaleToAccounting($sale)
    {
        try {
            // Material sale is also a sale, but we might want a different category
            // For now, let's use 'material_sale' type
            $this->accountingService->postAutoTransaction('material_sale', 'credit', [
                'date' => $sale->date,
                'amount' => $sale->total_price_af,
                'party_type' => 'App\Agents',
                'party_id' => $sale->agent_id,
                'reference' => $sale->sale_number,
                'description' => "فروش مواد به نماینده " . Agents::find($sale->agent_id)->name,
                'source_id' => $sale->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Material Sale #" . $sale->id . ": " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $material_sales = MaterialSale::orderBy('created_at','DESC')->paginate(60);
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $saleEdit = '';
        $agents = Agents::all();
        $lastId = MaterialSale::latest()->first();
        $SaleNo = '';
        if($lastId) {
            $lastId = $lastId->sale_number;
            $lastId = substr($lastId,-1);
            $lastId++;
            $SaleNo = 'SA-'.sprintf('%01d' , $lastId);
        } else {
            $SaleNo = 'SA-'.sprintf('%01d'  , '1');
        }

        return view('mstock.material-sale', compact('material_sales', 'categories', 'material_types', 'saleEdit', 'agents','SaleNo'));
    }

    public function search_sale_number($sale_number,$agent_id){
        $agent = Agents::findOrfail($agent_id);
        $sales = MaterialSale::where('sale_number',$sale_number)->where('agent_id',$agent_id)->get();
        $quantity = MaterialSale::Where('agent_id', '=', $agent_id)->where('sale_number','=',$sale_number)->count();
        return view('mstock.sale-number-list', compact('agent','sales','sale_number','quantity'));
    }

    public function request_list()
    {
        $requests = MaterialSale::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('mstock.material-sale-requested-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = MaterialSale::find($id);
            $material_stock = MaterialStock::where('material_category', '=', $sale->category_id)->where('material_type', '=', $sale->type_id)->first();

            if ($material_stock) {
                $material_stock->quantity = $material_stock->quantity - $sale->amount;
                $material_stock->update();
            }

            $sale->status = 1 ;
            $sale->update();

            // Accounting Posting
            $this->postMaterialSaleToAccounting($sale);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " به مقدار " . $sale->amount . "کیلوگرام مواد تایید و در سیستم مالی ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id){
        $sale = MaterialSale::find($id);
        $sale->delete();
        return response()->json(['status','error']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();

            if (!$material_stock) {
                return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!');
            } else {
                if ($request->amount > $material_stock->quantity) {
                    return redirect()->back()->with('error', ' مواد در گدام ' . $material_stock->quantity . 'kg' . ' میباشد');
                }
                else {
                    if (Auth::user()->role == 'SP'){
                        $material_stock->quantity = $material_stock->quantity - $request->amount;
                        $material_stock->update();
                    }
                }
            }

            $data = $request->validate([
                'agent_id' => 'required',
                'amount' => 'required',
                'price' => 'required',
                'total_price' => 'required',
                'total_price_af' => 'required',
                'type_id' => 'required',
                'category_id' => 'required',
                'date' => 'required',
                'sale_number' => 'required',
                'status' => ''
            ]);

            $data['status'] = (Auth::user()->role == 'SP') ? 1 : 0;

            $sale = MaterialSale::create($data);

            if ($sale->status == 1) {
                $this->postMaterialSaleToAccounting($sale);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " به مقدار " . $request->amount . " مواد فروخته شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/material-sales')->with('status', 'فروش مواد موفقانه ثبت و در سیستم مالی درج گردید!');
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material_sales = MaterialSale::paginate(30);
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $saleEdit = MaterialSale::find($id);
        $agents = Agents::all();
        return view('mstock.material-sale', compact('material_sales', 'agents', 'categories', 'material_types', 'saleEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $materialSale = MaterialSale::find($id);
            $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();

            if (!$material_stock) {
                return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!');
            } else {
               if (Auth::user()->role == 'SP') {
                    $old_amount = $materialSale->amount;
                    $material_stock->quantity = $material_stock->quantity + $old_amount;
                    
                    if ($request->amount > $material_stock->quantity) {
                        return redirect()->back()->with('error', 'موجودی گدام کافی نیست!');
                    }
                    
                    $material_stock->quantity = $material_stock->quantity - $request->amount;
                    $material_stock->update();
                }
            }

            // Accounting Reversal
            if ($materialSale->status == 1) {
                $this->accountingService->reverseTransactionBySource($materialSale->id, 'Material Sale Edited');
            }

            $materialSale->agent_id = $request->agent_id;
            $materialSale->sale_number = $request->sale_number;
            $materialSale->amount = $request->amount;
            $materialSale->price = $request->price;
            $materialSale->total_price = $request->total_price;
            $materialSale->total_price_af = $request->total_price_af;
            $materialSale->date = $request->date;
            $materialSale->category_id = $request->category_id;
            $materialSale->type_id = $request->type_id;
            $materialSale->update();

            // Re-post if approved
            if ($materialSale->status == 1) {
                $this->postMaterialSaleToAccounting($materialSale);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش فروش مواد به مقدار " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/material-sales')->with('status', 'ویرایش موفقانه ثبت و حسابات مالی بروزرسانی شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = MaterialSale::find($id);

            if ($sale->status == 1) {
                $this->accountingService->reverseTransactionBySource($sale->id, 'Material Sale Deleted');
            }

            $sale->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
