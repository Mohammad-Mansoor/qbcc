<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialCategory;
use App\MaterialSale;
use App\MaterialStock;
use App\MaterialType;
use App\StringSeller;
use Carbon\Carbon;
use \DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MaterialStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch stock data from inventory_transactions to get warehouse breakdown and asset value
        $stock = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('purchase_materials', 'items.ref_id', '=', 'purchase_materials.id')
            ->leftJoin('warehouses', 'inventory_transactions.warehouse_id', '=', 'warehouses.id')
            ->join('material_categories', 'purchase_materials.material_category', '=', 'material_categories.material_category_id')
            ->join('material_types', 'purchase_materials.material_type', '=', 'material_types.material_type_id')
            ->where('items.type', 'App\PurchaseMaterial')
            ->where('inventory_transactions.status', 1)
            ->select(
                'purchase_materials.material_category as cat_id',
                'purchase_materials.material_type as type_id',
                'material_categories.material_category',
                'material_types.material_type',
                'warehouses.name as warehouse_name',
                'inventory_transactions.warehouse_id',
                DB::raw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as quantity"),
                DB::raw('AVG(items.current_cost) as price_per_kilo'),
                DB::raw("SUM((CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) * items.current_cost) as total_value")
            )
            ->groupBy(
                'purchase_materials.material_category', 
                'purchase_materials.material_type', 
                'inventory_transactions.warehouse_id',
                'material_categories.material_category',
                'material_types.material_type',
                'warehouses.name'
            )
            ->having('quantity', '>', 0)
            ->get();

        $sales = MaterialSale::all();
        
        $categories = MaterialCategory::all();
        $categoryTotals = [];
        foreach ($categories as $category) {
            $total = DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->join('purchase_materials', 'items.ref_id', '=', 'purchase_materials.id')
                ->where('items.type', 'App\PurchaseMaterial')
                ->where('purchase_materials.material_category', $category->material_category_id)
                ->where('inventory_transactions.status', 1)
                ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as balance")
                ->value('balance') ?? 0;

            $categoryTotals[] = (object)[
                'name' => $category->material_category,
                'total' => $total
            ];
        }

        return view('mstock.index', compact('stock', 'sales', 'categoryTotals'));
    }

    public function history($cat, $type)
    {
        $category = MaterialCategory::where('material_category_id', $cat)->firstOrFail();
        $typeModel = MaterialType::where('material_type_id', $type)->firstOrFail();

        $movements = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('purchase_materials', 'items.ref_id', '=', 'purchase_materials.id')
            ->leftJoin('warehouses', 'inventory_transactions.warehouse_id', '=', 'warehouses.id')
            ->where('items.type', 'App\PurchaseMaterial')
            ->where('purchase_materials.material_category', $cat)
            ->where('purchase_materials.material_type', $type)
            ->select('inventory_transactions.*', 'warehouses.name as warehouse_name')
            ->orderBy('inventory_transactions.created_at', 'DESC')
            ->get();

        return view('mstock.history', compact('movements', 'category', 'typeModel'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $material_type = MaterialType::all();
        $string_seller = StringSeller::all();
        return view('mstock.create', compact('material_type', 'string_seller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->valData();
        $stock = new MaterialStock();
        $stock->total_kg = $request->total_kg;
        $stock->purchase_per_kg = $request->purchase_per_kg;
        $stock->puchase_price = $request->puchase_price;
        $stock->selling_price = $request->selling_price;
        $stock->selling_per_kg = $request->selling_per_kg;
        $stock->date = $request->date;
        $stock->description = $request->description;
        $stock->type_id = $request->type_id;
        $stock->seller_id = $request->seller_id;
        $stock->save();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $request->total_kg . " مواد در گدام اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        // $stock = MaterialStock::create($this->valData());
        if ($stock) {
            return redirect('/dashboard/material-stock')->with('status', 'گدام موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/material-stock')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialStock $materialStock
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialStock $materialStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialStock $materialStock
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stock = MaterialStock::find($id);
        $material_type = MaterialType::all();
        $string_seller = StringSeller::all();
        return view('material-stocks.edit-stock', compact('stock', 'material_type', 'string_seller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\MaterialStock $materialStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $this->valData();
        $stock = MaterialStock::find($id);
        $stock->total_kg = $request->total_kg;
        $stock->purchase_per_kg = $request->purchase_per_kg;
        $stock->puchase_price = $request->puchase_price;
        $stock->selling_price = $request->selling_price;
        $stock->selling_per_kg = $request->selling_per_kg;
        $stock->date = $request->date;
        $stock->description = $request->description;
        $stock->type_id = $request->type_id;
        $stock->seller_id = $request->seller_id;
        $stock->save();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $request->total_kg . " مواد در گدام ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        if ($stock) {
            return redirect('/dashboard/material-stock')->with('status', 'گدام موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/material-stock')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialStock $materialStock
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stock = MaterialStock::find($id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $stock->quantity . " مواد از گدام حذف شد. ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        $stock->delete();
        if ($stock) {
            return response()->json(['status' => 'success']);
        }
    }

    protected function valData()
    {
        return request()->validate([
            'total_kg' => 'required',
            'puchase_price' => 'required',
            'puchase_price' => 'required',
            'selling_price' => 'required',
            'selling_per_kg' => 'required',
            'date' => 'required',
            'description' => 'required',
            'selling_price' => 'required',

        ]);
    }
}
