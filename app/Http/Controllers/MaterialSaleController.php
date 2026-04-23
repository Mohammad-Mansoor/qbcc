<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Agents;
use App\Customer;
use App\MaterialCategory;
use App\MaterialSale;
use App\MaterialStock;
use App\MaterialType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $material_sales = MaterialSale::orderBy('created_at','DESC')->get();
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

        $sale = MaterialSale::find($id);


        $material_stock = MaterialStock::where('material_category', '=', $sale->category_id)->where('material_type', '=', $sale->type_id)->first();


        $material_stock->quantity = $material_stock->quantity - $sale->amount;
        $material_stock->update();


        $sale->status = 1 ;
        $sale->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $sale->amount . "کیلوگرام مواد توسط سوپر ادمین ثبت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }
    public function delete_request($id){
        $purchase = MaterialSale::find($id);


        $purchase->delete();


        return response()->json(['status','error']);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();

        if (!$material_stock) {
            return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!');
        } else {
            if ($request->amount > $material_stock->quantity) {
                return redirect()->back()->with('error', ' مواد در گدام' . $material_stock->quantity . 'kg' . 'میباشد');
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
        if (Auth::user()->role == 'SP'){
            $data['status'] = 1;
        }
        else{
            $data['status'] = 0;
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $request->amount . " فروخته شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $done = MaterialSale::create($data);


        if ($done) {
            return redirect('/dashboard/material-sales')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/material-sales')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialSale $materialSale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material_sales = MaterialSale::paginate(10);
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
    public function update(Request $request, MaterialSale $materialSale)
    {

        $material_stock = MaterialStock::where('material_category', '=', $request->category_id)->where('material_type', '=', $request->type_id)->first();

        if (!$material_stock) {
            return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!');
        } else {
           if (Auth::user()->role == 'SP') {
                $old_amount = $materialSale->amount;
                $material_stock->quantity = $material_stock->quantity + $old_amount;
                $material_stock->update();

                if ($request->amount > $material_stock->quantity) {

                    $old_amount = $materialSale->amount;
                    $material_stock->quantity = $material_stock->quantity - $old_amount;
                    $material_stock->update();
                    return redirect()->back()->with('error', ' مواد در گدام' . $material_stock->quantity . 'kg' . 'میباشد');
                } else {

                    $material_stock->quantity = $material_stock->quantity - $request->amount;

                    $material_stock->save();
                }
//
            }
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

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $request->amount . " فروخته شده ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        if ($materialSale) {
            return redirect('/dashboard/material-sales')->with('status', ' موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/material-sales')->with('error', 'مشکل در سرور وجود داره!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialSale $materialSale
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialSale $materialSale)
    {
        //
    }
}
