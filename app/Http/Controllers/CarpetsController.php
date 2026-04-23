<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AgentEmployee;

use App\Carpet;
use App\CarpetRepair;
use App\CarpetWash;
use App\FinishingWork;
use App\Invoice;
use App\MaterialCategory;
use App\CarpetType;
use App\CarpetOrder;
use App\Agents;

use App\CarpetCheckBook;
use App\CarpetMaterial;
use App\MaterialType;
use App\PakingList;
use App\Quality;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarpetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function carpets_in_center_office()
    {
        $carpets = Carpet::with('agent')->whereIn('status', [1, 2, 3, 13])->get();
        $agents = Agents::all();
        return view('carpets.central-carpet-list', compact('carpets', 'agents'));
    }

    public function carpets_in_sales_office()
    {
        $carpets = Carpet::with('agent')->whereIn('status', [3,13,4,5,6])->get();
        $agents = Agents::all();
        return view('carpets.sales-carpets-list', compact('carpets', 'agents'));
    }


    public function index()
    {

        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status',0)
            ->where('contract_type','contractional')
            ->orderBy('parcha_number','DESC')
            ->paginate(20);



//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }


         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }
        $agents = Agents::where('contract_type', 'contractional')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';

        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet'));
    }


    public function show_all_contract_carpet()
    {
        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status',0)
            ->where('contract_type','contractional')
            ->orderBy('parcha_number','DESC')
            ->get();




        $agents = Agents::where('contract_type', 'contractional')->get();

//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }


        $agents_contract_carpet = Agents::where('contract_type', 'contractional')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet'));
    }

    public function search_contract_carpet(Request $request)
    {

        $search = $request->search;

        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('carpets.parcha_number', 'like', '%' . $search . '%')
            ->orWhere('carpets.map_number', 'like', '%' . $search . '%')
            ->orWhere('carpet_types.carpet_type', 'like', '%' . $search . '%')
            ->orWhere('carpet_orders.order_number', 'like', '%' . $search . '%')
            ->orWhere('qualities.quality', 'like', '%' . $search . '%')


            ->get();




        $agents = Agents::where('contract_type', 'contractional')->get();


//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents_contract_carpet = Agents::where('contract_type', 'contractional')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet','search'));

    }


    public function search_contract_carpet_by_agent(Request $request)
    {

        $agent_id = $request->agent_id;

        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->get();


        $tar_pakhta  = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->where('carpet_materials.category_id',1)
            ->sum('carpet_materials.amount');

        $tar_pashm  = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->where('carpet_materials.category_id',2)
            ->sum('carpet_materials.amount');
        $tar_abrishm  = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->where('carpet_materials.category_id',3)
            ->sum('carpet_materials.amount');

        $afg_money = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->sum('carpet_materials.total_price_af');

        $usd_money = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->sum('carpet_materials.total_price');


        $agents = Agents::where('contract_type', 'contractional')->get();


//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents_contract_carpet = Agents::where('contract_type', 'contractional')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet','agent_id','tar_pakhta','tar_pashm','tar_abrishm','afg_money','usd_money'));

    }


    public function sending_to_stock(Carpet $id)
    {
        $id->status = 5;


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $id->carpet_no . " به گدام ارسال شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


         $upd = $id->update();
         if ($upd){
             return response()->json(['status' => 'success']);
         }else{
             return response()->json(['error' => 'success']);
         }


    }

    public function all_carpets()
    {
        $carpets = Carpet::whereNotIn('status', [6,0])->orderBy('carpet_no', 'DESC')->paginate(30);
        $agents = Agents::all();
        $carpet_types = CarpetType::all();

        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }

    public function all_carpets_search(Request $request)
    {
        $search = $request->search;

        $carpets = Carpet::where('status','!=',0)->where('carpet_no', 'like', '%' . $search . '%')
            ->orWhere('width', 'like', '%' . $search . '%')
            ->orWhere('height', 'like', '%' . $search . '%')
            ->orWhere('area', 'like', '%' . $search . '%')
            ->orWhere('field', 'like', '%' . $search . '%')
            ->orWhere('margin', 'like', '%' . $search . '%')
            ->orWhere('price', 'like', '%' . $search . '%')
            ->orWhere('total_price_af', 'like', '%' . $search . '%')
            ->orWhere('total_price', 'like', '%' . $search . '%')
            ->orWhere('map_number', 'like', '%' . $search . '%')
            ->orWhere('date', 'like', '%' . $search . '%')
            ->orWhereHas('carpet_order', function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            })
            ->orWhereHas('type', function ($query) use ($search) {
                $query->where('carpet_type', 'like', '%' . $search . '%');
            })
            ->orWhereHas('quality', function ($query) use ($search) {
                $query->where('quality', 'like', '%' . $search . '%');
            })
            ->get();

        $agents = Agents::all();
        $carpet_types = CarpetType::all();
        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }

    public function all_carpets_view()
    {
        $carpets = Carpet::whereNotIn('status', [6,0])->get();
        $agents = Agents::all();
        $carpet_types = CarpetType::all();
        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }

    public function filter_based_carpet_type(Request $request)
    {
        $carpet_type = $request->carpet_type;
        $carpets = Carpet::where('status', 1)->where('type_id', $carpet_type)->get();
        $agents = Agents::all();
        $carpet_types = CarpetType::all();
        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));

    }


   public
    function carpet_stock()
    {
        $carpets = Carpet::where('status', 5)->orderBY('carpet_no', 'DESC')->paginate(50);
        $carpet_types = CarpetType::all();
        $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();

        return view('carpet-stock.carpet-stock', compact('carpets', 'carpet_types','invoices','packing_list'));
    }


    public function filter_ba_asas_type(Request $request)
    {
        $carpets = Carpet::where('status', 5)->where('type_id', $request->carpet_type)->get();
        $carpet_types = CarpetType::all();
        return view('carpet-stock.carpet-stock', compact('carpets', 'carpet_types'));
    }


    public function carpet_stock_details($carpet_id)
    {
        $carpet = Carpet::find($carpet_id);
        $checkBook = CarpetCheckBook::where('carpet_id', $carpet_id)->first();

        $kachaee_expense = CarpetRepair::where('carpetId', $carpet_id)->first();
        $wash_expense = CarpetWash::where('carpetId', $carpet_id)->sum('total_price');
        $finishing_expense = FinishingWork::where('carpetId', $carpet_id)->get();
        return view('carpet-stock.carpet-stock-details', compact('carpet', 'checkBook', 'kachaee_expense', 'wash_expense', 'finishing_expense'));
    }

    public function carpet_stock_search(Request $request)
    {
        $search = $request->search;
        $carpets = Carpet::where('status','=',5)->where('carpet_no', 'like', '%' . $search . '%')
            ->orWhere('date', 'like', '%' . $search . '%')
            ->orWhereHas('carpet_order', function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            })
            ->orWhereHas('type', function ($query) use ($search) {
                $query->where('carpet_type', 'like', '%' . $search . '%');
            })
            ->get();
        $carpet_types = CarpetType::all();
        
             $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        return view('carpet-stock.carpet-stock', compact('carpets', 'carpet_types','search','invoices','packing_list'));
    }

    public function stock_search_date_range(Request $request)
    {
        $from_date = $request->from_date;

        $to_date = $request->to_date;

        $carpets = Carpet::where('status', 5)->whereBetween('date', [$from_date, $to_date])->get();
        $carpet_types = CarpetType::all();
        return view('carpet-stock.carpet-stock', compact('carpets','carpet_types','from_date','to_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }


        $agents = Agents::where('contract_type', 'contractional')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        return view('carpets.create-contract-carpet', compact('AccountNo', 'agents', 'orders', 'types', 'employees'));
    }

    /** Weight Carpet codes start */


    public function listWeight()
    {



        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status',0)
            ->where('contract_type','weight')
            ->orderBy('parcha_number','DESC')
            ->paginate(20);


//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }


        $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';
        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet'));
    }

    public function show_all_weight_carpet()
    {


        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status',0)
            ->where('contract_type','weight')
            ->orderBy('parcha_number','DESC')
            ->get();



        $agents = Agents::where('contract_type', 'weight')->get();


//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents_contract_carpet = Agents::where('contract_type', 'weight')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet'));
    }


    public function search_weight_carpet(Request $request)
    {
        $search = $request->search;

        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('carpets.parcha_number', 'like', '%' . $search . '%')
            ->orWhere('carpets.map_number', 'like', '%' . $search . '%')
            ->orWhere('carpet_types.carpet_type', 'like', '%' . $search . '%')
            ->orWhere('carpet_orders.order_number', 'like', '%' . $search . '%')
            ->orWhere('qualities.quality', 'like', '%' . $search . '%')


            ->get();


//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

        $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';
        $all = '';
        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'all','search'));
    }

    public function search_weight_carpet_by_agent(Request $request)
    {
        $agent_id = $request->agent_id;

        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)

            ->get();


        $tar_pakhta  = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->where('carpet_materials.category_id',1)
            ->sum('carpet_materials.amount');

        $tar_pashm  = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->where('carpet_materials.category_id',2)
            ->sum('carpet_materials.amount');
        $tar_abrishm  = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->where('carpet_materials.category_id',3)
            ->sum('carpet_materials.amount');

        $afg_money = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->sum('carpet_materials.total_price_af');

        $usd_money = DB::table('carpet_materials')
            ->join('carpets','carpet_materials.carpet_id','carpets.carpet_id')
            ->where('carpets.status',0)
            ->where('carpets.agent_id',$agent_id)
            ->sum('carpet_materials.total_price');




//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';
        $all = '';
        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'all','tar_pakhta','tar_pashm','tar_abrishm','afg_money','usd_money'));
    }




    public function PostWeight(Request $request)
    {

        $data = $this->Valid();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $request->parcha_number . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        $carpet = Carpet::create($data);
        if ($carpet) {
            return redirect('/dashboard/list-weight')->with('status', 'پارچه موفقانه ثبت شد !');
        } else {
            return redirect('/dashboard/list-weight')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    public function showWeight($id)
    {
        $carpet = Carpet::find($id);
        $carpetCheckBook = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
//        $agentRecieveds = 0; //AgentRecieved::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->paginate(8);
//        $agentMoney = AgentRecieved::where('carpet_id', $carpet->carpet_id)->sum('amount');
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price');
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();

        $tar_pakhta  = CarpetMaterial::where('carpet_id',$carpet->carpet_id)->where('category_id',1)->sum('amount');
        $tar_pashm  = CarpetMaterial::where('carpet_id',$carpet->carpet_id)->where('category_id',2)->sum('amount');
        $tar_abrishm  = CarpetMaterial::where('carpet_id',$carpet->carpet_id)->where('category_id',3)->sum('amount');

        $lastId = CarpetCheckBook::latest()->first();
        $CheckNo = '';
        if ($lastId) {
            $lastId = $lastId->check_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $CheckNo = 'CH-' . sprintf('%01d', $lastId);
        } else {
            $CheckNo = 'CH-' . sprintf('%01d', '1');
        }

        $material = '';
        return view('carpets.weight-details', compact('carpet', 'carpetCheckBook', 'carpetMaterials', 'categories', 'materialMoney', 'material_types', 'CheckNo', 'material','tar_pakhta','tar_pashm','tar_abrishm'));
    }

    public function editWeight($id)
    {

        $editCarpet = Carpet::find($id);



        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status',0)
            ->where('contract_type','weight')
            ->orderBy('parcha_number','DESC')
            ->paginate(20);

//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

         $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $qualities = Quality::all();
        return view('carpets.list-weight', compact('carpets', 'editCarpet', 'agents', 'orders', 'types', 'employees', 'AccountNo', 'qualities'));
    }

    public function UpdatetWeight(Request $request, $carpet_id)
    {

        $carpet =  Carpet::find($carpet_id);

        if ($carpet->check_book != null){
            $check = CarpetCheckBook::where('carpet_id',$carpet->carpet_id)->first();
            $check->agent_id = $request->agent_id;
            $check->update();
        }
        $data = $this->UpdateValid();

        $update = Carpet::where('carpet_id', $carpet_id)->update($data);
        if ($update) {
            return redirect('/dashboard/list-weight')->with('status', 'پارچه موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/list-weight')->with('error', 'مشکل در سرور وجود داره!');
        }
    }
    /** Weight Carpet codes end */

    /** buy Carpet codes start */


    public function pass_parcha(Request $request){


        $carpet = Carpet::find($request->carpet_id);
        $carpet->carpet_no = $request->carpet_no;
        $carpet->width = $request->width;
        $carpet->height = $request->height;
        $carpet->area = $request->area;
        $carpet->map_number = $request->map_number;
        $carpet->date = $request->date;
        $carpet->end_date = $request->end_date;
        $carpet->status = 1;
        $carpet->update();


        return response()->json(['status' => 'success']);

    }




    public function listBuyCarpet()
    {
        $carpets = Carpet::orderBy('carpet_no', 'DESC')->with('agent')->whereIn('status', [1, 12])->paginate(20);

        $lastId = Carpet::max('carpet_no');


        if ($lastId) {
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB' . sprintf('%05d', '10101');
        }
        $agents = Agents::all();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $editCarpet = '';
        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'editCarpet'));
    }

    public function show_all_buy_carpet()
    {
        $carpets = Carpet::orderBy('carpet_no', 'DESC')->with('agent')->whereIn('status', [1, 12])->get();
        $lastId = Carpet::max('carpet_no');
        if ($lastId) {
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB' . sprintf('%05d', '10101');
        }
        $agents = Agents::where('contract_type', 'carpet seller')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $all = '';
        $editCarpet = '';
        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'editCarpet', 'all'));
    }


    public function search_buy_carpet(Request $request)
    {
        $search = $request->search;

        $carpets = Carpet::whereIn('status', [1, 12])
            ->where('carpet_no', 'like', '%' . $search . '%')
            ->orWhere('width', 'like', '%' . $search . '%')
            ->orWhere('height', 'like', '%' . $search . '%')
            ->orWhere('area', 'like', '%' . $search . '%')
            ->orWhere('field', 'like', '%' . $search . '%')
            ->orWhere('margin', 'like', '%' . $search . '%')
            ->orWhere('price', 'like', '%' . $search . '%')
            ->orWhere('total_price_af', 'like', '%' . $search . '%')
            ->orWhere('total_price', 'like', '%' . $search . '%')
            ->orWhere('map_number', 'like', '%' . $search . '%')
            ->orWhere('date', 'like', '%' . $search . '%')
            ->orWhereHas('carpet_order', function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            })
            ->orWhereHas('type', function ($query) use ($search) {
                $query->where('carpet_type', 'like', '%' . $search . '%');
            })
            ->orWhereHas('quality', function ($query) use ($search) {
                $query->where('quality', 'like', '%' . $search . '%');
            })
            ->get();

        $lastId = Carpet::max('carpet_no');
        if ($lastId) {
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB' . sprintf('%05d', '10101');
        }
        $agents = Agents::all();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $all = '';
        $editCarpet = '';
        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'search', 'AccountNo', 'orders', 'types', 'all', 'editCarpet'));
    }


    function PostBuyCarpet(Request $request)
    {

        $data = $this->Valid();

        $image = '';
        if($request->has('carpet_image')) {
            $file = $request->file('carpet_image');
            $fileExt = $file->getClientOriginalExtension();
            if(!in_array($fileExt , ['jpg' , 'png' , 'jpeg'] )) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time().''.rand(1000,9999).'-carpet-image.'.$fileExt;
            $image = $file->move('uploads/carpet-image/' , $fileName);
        }

        $data['carpet_image'] = $image;

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $request->carpet_no . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

//        }
        $carpet = Carpet::create($data);
        if ($carpet) {
            return redirect('/dashboard/list-buy-carpet')->with('status', 'پارچه موفقانه ثبت شد!');;
        } else {
            return redirect('/dashboard/list-buy-carpet')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    public function printBuyCarpet($id)
    {
        $carpet = Carpet::find($id);
        $carpetCheckBook = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();

        $lastId = CarpetCheckBook::latest()->first();
        $CheckNo = '';
        if ($lastId) {
            $lastId = $lastId->check_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $CheckNo = 'CH-' . sprintf('%01d', $lastId);
        } else {
            $CheckNo = 'CH-' . sprintf('%01d', '1');


        }

        return view('carpets.print-buy-carpet', compact('carpet', 'carpetCheckBook', 'CheckNo'));
    }

    public function showBuyCarpet($id)
    {
        $carpet = Carpet::find($id);
        $carpetCheckBook = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
//        $agnetRecieveds = AgentRecieved::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->paginate(8);
//        $agentMoney = AgentRecieved::where('carpet_id', $carpet->carpet_id)->sum('amount');
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('price');
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        return view('carpets.buy-carpet-details', compact('carpet', 'carpetCheckBook', 'agnetRecieveds', 'carpetMaterials', 'categories', 'material_types', 'materialMoney', 'agentMoney'));
    }

    public function editBuyCarpet($id)
    {

        $editCarpet = Carpet::find($id);
        $carpets = Carpet::orderBy('carpet_no', 'DESC')->with('agent')->whereIn('status', [1, 12])->paginate(20);

        $lastId = Carpet::max('carpet_no');
        if ($lastId) {
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB' . sprintf('%05d', '10101');
        }
        $agents = Agents::all();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $qualities = Quality::all();
        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'editCarpet', 'qualities'));
    }

    function UpdatetBuyCarpet(Request $request, $carpet_id)
    {
        $carpet = Carpet::find($carpet_id);
        if ($carpet->check_book != null) {
            $check = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
            $check->agent_id = $request->agent_id;
            $check->update();
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->carpet_no . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
        $data = $this->UpdateValid();

        if($request->has('carpet_image')) {

            $file = $request->file('carpet_image');


            $fileExt = $file->getClientOriginalExtension();
            if(!in_array($fileExt , ['jpg' , 'png' , 'jpeg'] )) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time().''.rand(1000,9999).'-carpet-image.'.$fileExt;
            $image = $file->move('uploads/carpet-image/' , $fileName);

            $data['carpet_image'] = $image;
        }




        $update = Carpet::where('carpet_id', $carpet_id)->update($data);
        if ($update) {
            return redirect('/dashboard/list-buy-carpet')->with('status', 'پارچه موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/list-buy-carpet')->with('error', 'مشکل در سرور وجود داره!');
        }
    }
    /** buy carpet Carpet codes end */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
     public
    function store(Request $request)
    {

        $data = $this->Valid();
        $image = '';
        if($request->has('carpet_image')) {
            $file = $request->file('carpet_image');
            $fileExt = $file->getClientOriginalExtension();
            if(!in_array($fileExt , ['jpg' , 'png' , 'jpeg'] )) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time().''.rand(1000,9999).'-carpet-image.'.$fileExt;
            $image = $file->move('uploads/carpet-image/' , $fileName);
        }

        $data['carpet_image'] = $image;

        $carpet = Carpet::create($data);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $request->parcha_number . " در سیستم اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        if ($carpet) {
            if ($request->agent_carpet) {
                return redirect('/dashboard/agent-carpet/' . $request->agent_id)->with('status', 'پارچه موفقانه بروز شد !');

            } else {
                return redirect('/dashboard/contract-carpet')->with('status', 'پارچه موفقانه ثبت شد !');
            }
        } else {
            return redirect('/dashboard/contract-carpet')->with('error', 'مشکل در سرور وجود داره!');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Carpets $carpets
     * @return \Illuminate\Http\Response
     */
    public function show(Carpet $carpet)
    {
        $carpetCheckBook = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
//        $agentRecieveds = 0; //AgentRecieved::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->get();
//        $agentMoney = AgentRecieved::where('carpet_id', $carpet->carpet_id)->sum('amount');
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price');
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();

        $tar_pakhta  = CarpetMaterial::where('carpet_id',$carpet->carpet_id)->where('category_id',1)->sum('amount');
        $tar_pashm  = CarpetMaterial::where('carpet_id',$carpet->carpet_id)->where('category_id',2)->sum('amount');
        $tar_abrishm  = CarpetMaterial::where('carpet_id',$carpet->carpet_id)->where('category_id',3)->sum('amount');

        $lastId = CarpetCheckBook::where('agent_id',$carpet->agent_id)->latest()->first();

        $CheckNo = '';
        if ($lastId) {
            $lastId = $lastId->check_number;
            $lastId = substr($lastId, -1);
            $lastId++;
            $CheckNo = 'CH-' . sprintf('%01d', $lastId);
        } else {
            $CheckNo = 'CH-' . sprintf('%01d', '1');
        }

        $material = '';

        return view('carpets.carpet-contract-details', compact('carpet', 'material', 'carpetCheckBook', 'carpetMaterials', 'categories', 'material_types', 'materialMoney', 'CheckNo','tar_pakhta','tar_pashm','tar_abrishm'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Carpets $carpets
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $editCarpet = Carpet::find($id);

//        $carpets = Carpet::with('agent')->where('status',10)->whereHas('agent', function ($q) {
//            $q->where('contract_type', '=', 'contractional');
//        })->orderBy('parcha_number', 'DESC')->paginate(20);

        $carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status',0)
            ->where('contract_type','contractional')
            ->orderBy('parcha_number','DESC')
            ->paginate(20);

//        $lastId = Carpet::latest()->first();
//        $CarpetNo = '';
//        if ($lastId) {
//            $lastId = $lastId->carpet_no;
//            $lastId = substr($lastId, -5);
//            $lastId++;
//            $AccountNo = 'QB' . sprintf('%05d', $lastId);
//        } else {
//            $AccountNo = 'QB' . sprintf('%05d', '10101');
//        }

        $lastId = Carpet::where('parcha_number','!=','Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId->parcha_number) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents = Agents::where('contract_type', 'contractional')->get();
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $qualities = Quality::all();

        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'qualities'));


    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Carpets $carpets
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, Carpet $carpet)
    {

        if ($carpet->check_book != null) {
            $check = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
            $check->agent_id = $request->agent_id;
            $check->update();
        }
        $data = $this->UpdateValid();
        if($request->has('carpet_image')) {

            $file = $request->file('carpet_image');


            $fileExt = $file->getClientOriginalExtension();
            if(!in_array($fileExt , ['jpg' , 'png' , 'jpeg'] )) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time().''.rand(1000,9999).'-carpet-image.'.$fileExt;
            $image = $file->move('uploads/carpet-image/' , $fileName);

            $data['carpet_image'] = $image;
        }



        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->parcha_number . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $update = $carpet->update($data);

        if ($update) {
            if ($request->agent_carpet) {
                return redirect('/dashboard/agent-carpet/' . $request->agent_id)->with('status', 'پارچه موفقانه بروز شد !');

            } elseif ($request->has('edit_all_carpet')) {
                return redirect('/dashboard')->with('status', 'پارچه موفقانه بروز شد !');

            } else {
                return redirect('/dashboard/contract-carpet')->with('status', 'پارچه موفقانه بروز شد !');
            }


        } else {
            return redirect('/dashboard/contract-carpet')->with('error', 'مشکل در سرور وجود داره!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Carpets $carpets
     * @return \Illuminate\Http\Response
     */
    public function destroy(Carpet $carpets)
    {
        //
    }

    protected function Valid()
    {
        return request()->validate([
            'parcha_number' => '',
            'dollar_rate' => '',
            'carpet_no' => '',
            'width' => '',
            'height' => '',
            'area' => '',
            'field' => '',
            'margin' => '',
            'price' => '',
            'carpet_price' => '',
            'carpet_price_us' => '',
            'total_price' => '',
            'total_price_af' => '',
            'map_number' => '',
            'date' => '',
            'end_date' => '',
            'agent_id' => 'required',
            'employee_id' => '',
            'order_id' => '',
            'type_id' => '',
            'quality_id' => '',
            'status' => '',
        ]);
    }

    protected function UpdateValid()
    {
        return request()->validate([
            'parcha_number' => '',
            'dollar_rate' => '',
            'carpet_no' => '',
            'width' => '',
            'height' => '',
            'area' => '',
            'field' => '',
            'margin' => '',
            'price' => '',
            'carpet_price' => '',
            'carpet_price_us' => '',
            'total_price' => '',
            'total_price_af' => '',
            'map_number' => '',
            'date' => '',
            'end_date' => '',
            'agent_id' => 'required',
            'employee_id' => '',
            'order_id' => '',
            'type_id' => '',
            'quality_id' => '',
            'status' => '',

        ]);
    }

}
