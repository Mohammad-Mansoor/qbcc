<?php

namespace App\Http\Controllers;

use App\Activity;
use App\CarpetCheckBook;
use App\CarpetMaterial;
use App\Carpet;
use App\MaterialCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\MaterialStock;
use App\MaterialType;
use Illuminate\Support\Facades\Auth;
class CarpetMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $carpet = Carpet::where('carpet_id', '=', $request->carpet_id)->first();
        $material_stock = MaterialStock::where('material_category','=',$request->category_id)->where('material_type','=',$request->type_id)->first();
   
        if(!$material_stock){
            return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!'); 
        }
        else{
            if($request->amount > $material_stock->quantity){
                return redirect()->back()->with('error', ' مواد در گدام'. $material_stock->quantity.'kg' .'میباشد');
            }
            else{
                $material_stock->quantity = $material_stock->quantity - $request->amount;
                $material_stock->update(); 
            }
           
        }
        $carpet->total_price = $carpet->total_price + $request->total_price;
        $carpet->total_price_af = $carpet->total_price_af + $request->total_price_af;
        $carpet->update();
        
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "برای قالین نمبر  " . $carpet->carpet_no . " به مقدار " . $request->amount . " کیلوگرام مواد دریافت شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $done = $carpet->carpetMaterial()->create($data);
        if ($done) {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ثبت شد !');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ثبت شد !');
            }
        } else {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetMaterial $carpetMaterial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
       public function edit(CarpetMaterial $material)
    {
       $carpet_id =  $material->carpet_id;
       $carpet = Carpet::find($carpet_id);
        $carpetCheckBook = CarpetCheckBook::where('carpet_id',$carpet->carpet_id)->first();
//        $agentRecieveds = 0; //AgentRecieved::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->paginate(8);
//        $agentMoney = AgentRecieved::where('carpet_id', $carpet->carpet_id)->sum('amount');
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price');
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();




        $tar_pakhta = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 1)->sum('amount');
        $tar_pashm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 2)->sum('amount');
        $tar_abrishm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 3)->sum('amount');

        $lastId = CarpetCheckBook::latest()->first();
        $CheckNo = '';
        if($lastId) {
            $lastId = $lastId->check_number;
            $lastId = substr($lastId,-1);
            $lastId++;
            $CheckNo = 'CH-'.sprintf('%01d' , $lastId);
        } else {
            $CheckNo = 'CH-'.sprintf('%01d'  , '1');
        }

        return view('carpets.carpet-contract-details', compact('carpet','material', 'carpetCheckBook',  'carpetMaterials', 'categories','material_types', 'materialMoney','CheckNo','tar_pakhta','tar_pashm','tar_abrishm'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetMaterial $material)
    {
        $data = $this->valData();
        $material_stock = MaterialStock::where('material_category','=',$request->category_id)->where('material_type','=',$request->type_id)->first();
        $carpet = Carpet::where('carpet_id',$request->carpet_id)->first();
        
        if(!$material_stock){
            return redirect()->back()->with('error', 'مواد درخواست شده در گدام نمیباشد‌!'); 
        }
        else{
            $material_stock->quantity = $material_stock->quantity + $request->oldMawad;
            $material_stock->update();
            if($request->amount > $material_stock->quantity){
                $material_stock->quantity = $material_stock->quantity - $request->oldMawad;
                $material_stock->update();
                return redirect()->back()->with('error', ' مواد در گدام'. $material_stock->quantity.'kg' .'میباشد');
            }
            else{             
                $material_stock->quantity = $material_stock->quantity - $request->amount;
                $material_stock->update();
            }
           
        }

        $carpet->total_price = $carpet->total_price - $request->old_dollar + $request->total_price;
        $carpet->total_price_af = $carpet->total_price_af - $request->old_af + $request->total_price_af;
        $carpet->update();
        
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "برای قالین نمبر  " . $carpet->carpet_no . " به مقدار " . $request->amount . " کیلوگرام مواد ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        
        $done = $material->update($data);
      
        if ($done) {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ویرایش شد !');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('status', '  رسید مواد موفقانه ویرایش شد !');
            }
        } else {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarpetMaterial $carpetMaterial)
    {
        //
    }

    protected function valData()
    {
        return request()->validate([
            'amount' => 'required',
            'price' => 'required',
            'total_price' => 'required',
            'total_price_af' => 'required',
            'date' => 'required',
            'category_id' => 'required',
            'type_id' => 'nullable',
            'carpet_id' => 'nullable',
            'agent_id'=>'required'
        ]);
    }
}
