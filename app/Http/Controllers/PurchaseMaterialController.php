<?php

namespace App\Http\Controllers;

use App\Activity;
use App\OfficeDebit;
use App\PurchaseMaterial;
use App\MaterialType;
use App\MaterialCategory;
use App\StringSeller;
use App\MaterialStock;
use App\PurchaseTotalAcount;
use App\RecievedOfSeller;
use App\OfficeCashBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchase = PurchaseMaterial::latest()->get();
        $material_type = MaterialType::all();
        $material_category = MaterialCategory::all();
        $sellers = StringSeller::all();
        $purchaseMaterial = '';
        $lastId = PurchaseMaterial::latest()->first();
        $PurchaseNo = '';
        if ($lastId) {
            $lastId = $lastId->purchase_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $PurchaseNo = 'PO-' . sprintf('%01d', $lastId);
        } else {
            $PurchaseNo = 'PO-' . sprintf('%01d', '1');
        }
        return view('mpurchase.index', compact('purchase', 'material_type', 'material_category', 'sellers', 'purchaseMaterial', 'PurchaseNo'));
    }


    public function search_purchase_number($purchase_number, $seller_id)
    {

        $seller = StringSeller::findOrfail($seller_id);
        $purchases = PurchaseMaterial::where('purchase_number', $purchase_number)->where('seller_id', $seller_id)->get();
        $quantity = PurchaseMaterial::Where('seller_id', '=', $seller_id)->where('purchase_number', '=', $purchase_number)->count();

        return view('mpurchase.purchase-number-list', compact('purchases', 'seller', 'purchase_number', 'quantity'));


    }


    public function request_list()
    {
        $requests = PurchaseMaterial::where('status', 0)->orderBy('id', 'DESC')->get();


        return view('mpurchase.requested-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $purchase = PurchaseMaterial::find($id);

        /** check stock */
        $stock = MaterialStock::where(
            ['material_type' => $purchase->material_type,
                'material_category' => $purchase->material_category]);
        if ($stock->count() == 0) {

            $stock2 = new MaterialStock();
            $stock2->material_type = $purchase->material_type;
            $stock2->material_category = $purchase->material_category;
            $stock2->quantity = $purchase->quantity;
            $stock2->price_per_kilo = $purchase->price_per_kilo;
            $stock2->in_words = $purchase->in_words;
            $stock2->save();

        } else {
            $stock_quantity = $stock->pluck('quantity')[0];
            $stock_price_per_kilo = $stock->pluck('price_per_kilo')[0];
            $purchase_quantity = $purchase->quantity;
            $purchase_price_per_kilo = $purchase->price_per_kilo;

            $new_quantity = $stock_quantity + $purchase_quantity;
            $middle_price = ($stock_quantity * $stock_price_per_kilo + $purchase_quantity * $purchase_price_per_kilo) / $new_quantity;
            $new_price = round($middle_price, 2);
            $stock->update([
                'quantity' => $new_quantity,
                'price_per_kilo' => $new_price
            ]);


        }


        $purchase->status = 1;
        $purchase->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $purchase->quantity . "کیلوگرام مواد توسط سوپر ادمین ثبت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }

    public function delete_request($id)
    {
        $purchase = PurchaseMaterial::find($id);


        $purchase->delete();


        return response()->json(['status', 'error']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('mpurchase.create', compact('material_type', 'material_category', 'sellers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $data = $this->Valid();

        if (Auth::user()->role == 'SP') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }


        if (Auth::user()->role == 'SP') {
            /** check stock */
            $stock = MaterialStock::where(
                ['material_type' => $request->material_type,
                    'material_category' => $request->material_category]);
            if ($stock->count() == 0) {

                $stock2 = new MaterialStock();
                $stock2->material_type = $request->material_type;
                $stock2->material_category = $request->material_category;
                $stock2->quantity = $request->quantity;
                $stock2->price_per_kilo = $request->price_per_kilo;
                $stock2->in_words = $request->in_words;
                $stock2->save();

            } else {
                $stock_quantity = $stock->pluck('quantity')[0];
                $stock_price_per_kilo = $stock->pluck('price_per_kilo')[0];
                $purchase_quantity = $request->quantity;
                $purchase_price_per_kilo = $request->price_per_kilo;

                $new_quantity = $stock_quantity + $purchase_quantity;
                $middle_price = ($stock_quantity * $stock_price_per_kilo + $purchase_quantity * $purchase_price_per_kilo) / $new_quantity;
                $new_price = round($middle_price, 2);
                $stock->update([
                    'quantity' => $new_quantity,
                    'price_per_kilo' => $new_price
                ]);


            }
        }
        $purchase = PurchaseMaterial::create($data);


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $request->quantity . "کیلوگرام مواد ثبت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
//
//        /** check stock */
//        $stock = MaterialStock::where(
//            ['material_type' => $purchase->material_type,
//                'material_category' => $purchase->material_category]);
//        if ($stock->count() == 0) {
//            MaterialStock::create($data);
//        } else {
//            $stock_quantity = $stock->pluck('quantity')[0];
//            $stock_price_per_kilo = $stock->pluck('price_per_kilo')[0];
//            $purchase_quantity = $request->quantity;
//            $purchase_price_per_kilo = $request->price_per_kilo;
//            $new_quantity = $stock_quantity + $purchase_quantity;
//            $middle_price = ($stock_quantity * $stock_price_per_kilo + $purchase_quantity * $purchase_price_per_kilo) / $new_quantity;
//            $new_price = round($middle_price, 2);
//            $stock->update([
//                'quantity' => $new_quantity,
//                'price_per_kilo' => $new_price
//            ]);
//
//
//        }

        return redirect('/dashboard/material-purchase')->with('status', 'خریداری موفقانه صورت  گرفت.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseMaterial $purchaseMaterial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseMaterial $purchaseMaterial)
    {
        $purchase = PurchaseMaterial::latest()->get();
        $material_type = MaterialType::all();
        $material_category = MaterialCategory::all();
        $sellers = StringSeller::all();
        return view('mpurchase.index', compact('purchase', 'material_type', 'material_category', 'sellers', 'purchaseMaterial'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseMaterial $purchaseMaterial)
    {


        /** check stock */
        if (Auth::user()->role == 'SP') {

            // remove old quantity from stock
            $stock2 = MaterialStock::where(
                ['material_type' => $request->old_material_type,
                    'material_category' => $request->old_material_category]);
            $stock2_quantity = $stock2->pluck('quantity')[0];
            $new_quantity2 = $stock2_quantity - $request->old_quantity;

            $stock2->update([
                'quantity' => $new_quantity2
            ]);
            // end remove old quantity from stock

            //add new quantity to stock
            $stock = MaterialStock::where(
                ['material_type' => $request->material_type,
                    'material_category' => $request->material_category]);
            if ($stock->count() == 0) {

                $stock3 = new MaterialStock();
                $stock3->material_type = $request->material_type;
                $stock3->material_category = $request->material_category;
                $stock3->quantity = $request->quantity;
                $stock3->price_per_kilo = $request->price_per_kilo;
                $stock3->in_words = $request->in_words;
                $stock3->save();

            } else {
                $stock_quantity = $stock->pluck('quantity')[0];
                $stock_price_per_kilo = $stock->pluck('price_per_kilo')[0];
                $purchase_quantity = $request->quantity;
                $purchase_price_per_kilo = $request->price_per_kilo;

                $new_quantity = $stock_quantity + $purchase_quantity;
                $middle_price = ($stock_quantity * $stock_price_per_kilo + $purchase_quantity * $purchase_price_per_kilo) / $new_quantity;
                $new_price = round($middle_price, 2);
                $stock->update([
                    'quantity' => $new_quantity,
                    'price_per_kilo' => $new_price
                ]);


            }

        }


        $data = $this->Valid();
        $purchase = $purchaseMaterial->update($data);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " به مقدار " . $request->quantity . "کیلوگرام مواد ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        return redirect('/dashboard/material-purchase')->with('status', 'خریداری موفقانه صورت  گرفت.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseMaterial $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseMaterial $purchaseMaterial)
    {
        //
    }

    protected function Valid()
    {
        return request()->validate([
            'material_type' => 'required',
            'material_category' => 'required',
            'seller_id' => 'required',
            'quantity' => 'required',
            'purchase_date' => 'required',
            'price_per_kilo' => 'required',
            'in_words' => 'required',
            'total' => 'required',
            'total_af' => 'required',
            'purchase_number' => 'required',
            'status' => ''
        ]);
    }
}
