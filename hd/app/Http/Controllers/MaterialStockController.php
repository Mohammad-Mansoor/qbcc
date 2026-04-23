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

        $stock = MaterialStock::all();
        if (count($stock) == 0) {
            $firstName = (object)['material_category' => 'تار پخته'];

            $firstTotal = '0';
            $secondName = (object)['material_category' => 'تار پشم'];
            $secondTotal = '0';
            $thirdName = (object)['material_category' => 'تار ابریشم'];
            $thirdTotal = '0';
            $sales = MaterialSale::all();
        } else {
            $ids = MaterialStock::distinct()->get('material_category');
            $id_count = $ids->count();

            if ($id_count == 1) {
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $secondName = (object)['material_category' => 'تار پشم'];
                $secondTotal = '0';
                $thirdName = (object)['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            } elseif ($id_count == 2) {
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');
                $thirdName = (object)['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            } else {
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');

                $third = $ids[2]->material_category;
                $thirdName = MaterialCategory::where('material_category_id', $third)->first('material_category');
                $thirdTotal = MaterialStock::where('material_category', $third)->sum('quantity');
            }


            $sales = MaterialSale::all();

        }

        return view('mstock.index', compact('stock', 'firstName', 'firstTotal', 'secondName', 'secondTotal', 'thirdName', 'thirdTotal', 'sales'));
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
