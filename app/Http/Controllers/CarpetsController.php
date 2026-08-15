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
use App\CustomerOrder;
use App\Agents;

use App\CarpetCheckBook;
use App\CarpetMaterial;
use App\MaterialType;
use App\PakingList;
use App\Quality;
use App\Warehouse;
use App\MappingRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\InventoryTransactionManager;
use App\Services\AccountSelectionService;

class CarpetsController extends Controller
{
    protected $inventoryManager;
    protected $accountSelectionService;

    public function __construct(InventoryTransactionManager $inventoryManager, AccountSelectionService $accountSelectionService)
    {
        $this->inventoryManager = $inventoryManager;
        $this->accountSelectionService = $accountSelectionService;
    }
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
        $carpets = Carpet::with('agent')->whereIn('status', [3, 13, 4, 5, 6])->get();
        $agents = Agents::all();
        return view('carpets.sales-carpets-list', compact('carpets', 'agents'));
    }


    public function index()
    {
        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();

        $carpets = DB::table('carpets')
            ->join('agents', 'carpets.agent_id', 'agents.agent_id')
            ->join('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('status', 0)
            ->where('contract_type', 'contractional')
            ->orderBy('parcha_number', 'DESC')
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


        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }
        $agents = Agents::where('contract_type', 'contractional')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';

        $qualities = \App\Quality::all();
        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::all();
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'warehouses', 'inventoryAccounts', 'currencies', 'qualities'));
    }


    public function show_all_contract_carpet()
    {
        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();
        $carpets = DB::table('carpets')
            ->join('agents', 'carpets.agent_id', 'agents.agent_id')
            ->join('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('status', 0)
            ->where('contract_type', 'contractional')
            ->orderBy('parcha_number', 'DESC')
            ->paginate(50);




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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }


        $agents_contract_carpet = Agents::where('contract_type', 'contractional')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        $qualities = \App\Quality::all();
        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::all();
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet', 'warehouses', 'inventoryAccounts', 'currencies', 'qualities'));
    }

    public function search_contract_carpet(Request $request)
    {
        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();

        $search = $request->search;

        $carpets = DB::table('carpets')
            ->join('agents', 'carpets.agent_id', 'agents.agent_id')
            ->join('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('carpets.parcha_number', 'like', '%' . $search . '%')
            ->orWhere('carpets.map_number', 'like', '%' . $search . '%')
            ->orWhere('carpet_types.carpet_type', 'like', '%' . $search . '%')
            ->orWhere('customer_orders.order_name', 'like', '%' . $search . '%')
            ->orWhere('qualities.quality', 'like', '%' . $search . '%')


            ->paginate(50);




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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents_contract_carpet = Agents::where('contract_type', 'contractional')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        $qualities = \App\Quality::all();
        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::all();
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet', 'search', 'warehouses', 'inventoryAccounts', 'currencies', 'qualities'));

    }


    public function search_contract_carpet_by_agent(Request $request)
    {
        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();

        $agent_id = $request->agent_id;

        $carpets = DB::table('carpets')
            ->join('agents', 'carpets.agent_id', 'agents.agent_id')
            ->join('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->paginate(50);


        $tar_pakhta = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->where('carpet_materials.category_id', 1)
            ->sum('carpet_materials.amount');

        $tar_pashm = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->where('carpet_materials.category_id', 2)
            ->sum('carpet_materials.amount');
        $tar_abrishm = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->where('carpet_materials.category_id', 3)
            ->sum('carpet_materials.amount');

        $afg_money = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->sum('carpet_materials.total_price_af');

        $usd_money = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents_contract_carpet = Agents::where('contract_type', 'contractional')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        $qualities = \App\Quality::all();
        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::all();
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet', 'agent_id', 'tar_pakhta', 'tar_pashm', 'tar_abrishm', 'afg_money', 'usd_money', 'warehouses', 'inventoryAccounts', 'qualities', 'currencies'));

    }


    public function sending_to_stock(Carpet $id)
    {
        $oldWarehouse = $id->warehouse_id;
        $oldAccount = $id->override_inventory_account_id;
        $oldCreditAccount = $id->override_credit_account_id;
        $oldTotalPrice = $id->total_price;
        $oldArea = $id->area;
        $oldDate = $id->date;
        $oldAgentId = $id->agent_id;
        $oldCarpetNo = $id->carpet_no;
        $oldParchaNo = $id->parcha_number;

        if (request()->has('warehouse_id')) {
            $id->warehouse_id = request()->get('warehouse_id');
        }

        // Reconcile/Re-post if warehouse or accounts changed (while status is still WIP / Purchased)
        $this->syncAccounting(
            $id,
            $oldWarehouse,
            $oldAccount,
            $oldCreditAccount,
            $oldTotalPrice,
            $oldArea,
            $oldDate,
            $oldAgentId,
            $oldCarpetNo,
            $oldParchaNo
        );

        $id->status = 5;

        $warehouseName = '';
        if ($id->warehouse_id) {
            $warehouse = Warehouse::find($id->warehouse_id);
            if ($warehouse) {
                $warehouseName = " (" . $warehouse->name . ")";
            }
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $id->carpet_no . " به گدام" . $warehouseName . " ارسال شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $upd = $id->update();
        if ($upd) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['error' => 'success']);
        }
    }

    public function all_carpets()
    {
        $carpets = Carpet::whereNotIn('status', [6, 0])->orderBy('carpet_no', 'DESC')->paginate(30);
        $agents = Agents::all();
        $carpet_types = CarpetType::all();

        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }

    public function all_carpets_search(Request $request)
    {
        $search = $request->search;

        $carpets = Carpet::where('status', '!=', 0)->where('carpet_no', 'like', '%' . $search . '%')
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
            ->paginate(50);

        $agents = Agents::all();
        $carpet_types = CarpetType::all();
        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }

    public function all_carpets_view()
    {
        $carpets = Carpet::whereNotIn('status', [6, 0])->paginate(50);
        $agents = Agents::all();
        $carpet_types = CarpetType::all();
        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }

    public function filter_based_carpet_type(Request $request)
    {
        $carpet_type = $request->carpet_type;
        $carpets = Carpet::where('status', 1)->where('type_id', $carpet_type)->paginate(50);
        $agents = Agents::all();
        $carpet_types = CarpetType::all();
        return view('carpets.all-carpets', compact('carpets', 'agents', 'carpet_types'));
    }


    public function carpet_stock(Request $request)
    {
        $query = Carpet::where('status', 5);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('carpet_type')) {
            $query->where('type_id', $request->carpet_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('carpet_no', 'like', '%' . $search . '%')
                  ->orWhere('date', 'like', '%' . $search . '%')
                  ->orWhereHas('carpet_order', function ($query) use ($search) {
                      $query->where('order_number', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('type', function ($query) use ($search) {
                      $query->where('carpet_type', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        $carpetsQuery = $query->with(['type', 'warehouse', 'carpet_order', 'quality'])->orderBy('carpet_no', 'DESC');
        
        if ($request->export) {
            return $this->exportStock($carpetsQuery->get(), $request, $request->export);
        }

        $carpets = $carpetsQuery->paginate(50);

        $data = $this->getStockDependencies();
        $data['carpets'] = $carpets;

        return view('carpet-stock.carpet-stock', $data);
    }


    public function filter_ba_asas_type(Request $request)
    {
        $query = Carpet::where('status', 5)->where('type_id', $request->carpet_type);
        
        if ($request->export) {
            return $this->exportStock($query->with(['type', 'warehouse', 'carpet_order', 'quality'])->get(), $request, $request->export);
        }

        $carpets = $query->paginate(50);

        $data = $this->getStockDependencies();
        $data['carpets'] = $carpets;

        return view('carpet-stock.carpet-stock', $data);
    }


    public function carpet_stock_details($carpet_id)
    {
        $carpet = Carpet::with(['type', 'warehouse', 'quality'])->find($carpet_id);
        $checkBook = CarpetCheckBook::where('carpet_id', $carpet_id)->first();

        $kachaee_expense = CarpetRepair::where('carpetId', $carpet_id)->first();
        $wash_expense = CarpetWash::where('carpetId', $carpet_id)->sum('total_price');
        $finishing_expense = FinishingWork::with('category')->where('carpetId', $carpet_id)->get();

        // Fetch movement log based on carpet number in description
        $history = Activity::where('description', 'like', '%' . $carpet->carpet_no . '%')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('carpet-stock.carpet-stock-details', compact('carpet', 'checkBook', 'kachaee_expense', 'wash_expense', 'finishing_expense', 'history'));
    }

    public function carpet_stock_search(Request $request)
    {
        $search = $request->search;
        $query = Carpet::where('status', '=', 5)->where(function($q) use ($search) {
            $q->where('carpet_no', 'like', '%' . $search . '%')
              ->orWhere('date', 'like', '%' . $search . '%')
              ->orWhereHas('carpet_order', function ($query) use ($search) {
                  $query->where('order_number', 'like', '%' . $search . '%');
              })
              ->orWhereHas('type', function ($query) use ($search) {
                  $query->where('carpet_type', 'like', '%' . $search . '%');
              });
        });

        if ($request->export) {
            return $this->exportStock($query->with(['type', 'warehouse', 'carpet_order', 'quality'])->get(), $request, $request->export);
        }

        $carpets = $query->paginate(50);

        $data = $this->getStockDependencies();
        $data['carpets'] = $carpets;
        $data['search'] = $search;

        return view('carpet-stock.carpet-stock', $data);
    }

    public function stock_search_date_range(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = Carpet::where('status', 5)->whereBetween('date', [$from_date, $to_date]);
        
        if ($request->export) {
            return $this->exportStock($query->with(['type', 'warehouse', 'carpet_order', 'quality'])->get(), $request, $request->export);
        }

        $carpets = $query->paginate(50);

        $data = $this->getStockDependencies();
        $data['carpets'] = $carpets;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        return view('carpet-stock.carpet-stock', $data);
    }

    protected function exportStock($carpets, $request, $format)
    {
        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $filter_wh = 'همه گدام‌ها';
        if ($request->filled('warehouse_id')) {
            $wh = \App\Warehouse::find($request->warehouse_id);
            if ($wh) $filter_wh = $wh->name;
        }

        $filter_type = 'همه نوعیت‌ها';
        if ($request->filled('carpet_type')) {
            $ct = \App\CarpetType::where('carpet_type_id', $request->carpet_type)->first();
            if ($ct) $filter_type = $ct->carpet_type;
        }

        $filter_search = $request->filled('search') ? $request->search : '---';
        
        $from = $request->filled('from_date') ? $request->from_date : '---';
        $to = $request->filled('to_date') ? $request->to_date : '---';
        $filter_date = ($from == '---' && $to == '---') ? '---' : ($from . ' الی ' . $to);

        if ($format === 'pdf') {
            return view('carpet-stock.carpet_stock_pdf', compact('carpets', 'request', 'logoBase64', 'filter_wh', 'filter_type', 'filter_search', 'filter_date'));
        }

        $filename = 'carpet_stock_' . date('Y_m_d_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo view('carpet-stock.carpet_stock_excel', compact('carpets', 'request', 'filter_wh', 'filter_type', 'filter_search', 'filter_date'))->render();
        exit;
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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }


        $agents = Agents::where('contract_type', 'contractional')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        return view('carpets.create-contract-carpet', compact('AccountNo', 'agents', 'orders', 'types', 'employees'));
    }

    /** Weight Carpet codes start */


    public function listWeight()
    {



        $carpets = DB::table('carpets')
            ->leftJoin('agents', 'carpets.agent_id', 'agents.agent_id')
            ->leftJoin('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('status', 0)
            ->where('agents.contract_type', 'weight')
            ->orderBy('parcha_number', 'DESC')
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


        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents = Agents::with('user')->where('contract_type', 'weight')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $warehouses = Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;

        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();

        $currencies = \App\Currency::all();
        $qualities = \App\Quality::all();
        $editCarpet = '';
        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'warehouses', 'defaultWarehouseId', 'inventoryAccounts', 'currencies', 'qualities'));
    }

    public function show_all_weight_carpet()
    {


        $carpets = DB::table('carpets')
            ->leftJoin('agents', 'carpets.agent_id', 'agents.agent_id')
            ->leftJoin('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('status', 0)
            ->where('agents.contract_type', 'weight')
            ->orderBy('parcha_number', 'DESC')
            ->paginate(50);



        $agents = Agents::with('user')->where('contract_type', 'weight')->get();

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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents_contract_carpet = Agents::with('user')->whereIn('contract_type', ['weight', 'contractional'])->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $all = '';
        $editCarpet = '';
        $warehouses = Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;

        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();

        $currencies = \App\Currency::all();
        $qualities = \App\Quality::all();
        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'agents_contract_carpet', 'orders', 'types', 'employees', 'all', 'editCarpet', 'warehouses', 'defaultWarehouseId', 'inventoryAccounts', 'currencies', 'qualities'));
    }


    public function search_weight_carpet(Request $request)
    {
        $search = $request->search;

        $carpets = DB::table('carpets')
            ->leftJoin('agents', 'carpets.agent_id', 'agents.agent_id')
            ->leftJoin('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('carpets.parcha_number', 'like', '%' . $search . '%')
            ->orWhere('carpets.map_number', 'like', '%' . $search . '%')
            ->orWhere('carpet_types.carpet_type', 'like', '%' . $search . '%')
            ->orWhere('customer_orders.order_name', 'like', '%' . $search . '%')
            ->orWhere('qualities.quality', 'like', '%' . $search . '%')


            ->paginate(50);


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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';
        $all = '';
        $warehouses = Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;

        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();
        $qualities = \App\Quality::all();
        $currencies = \App\Currency::all();

        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'all', 'search', 'warehouses', 'defaultWarehouseId', 'inventoryAccounts', 'qualities', 'currencies'));
    }

    public function search_weight_carpet_by_agent(Request $request)
    {
        $agent_id = $request->agent_id;

        $carpets = DB::table('carpets')
            ->leftJoin('agents', 'carpets.agent_id', 'agents.agent_id')
            ->leftJoin('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)

            ->paginate(50);


        $tar_pakhta = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->where('carpet_materials.category_id', 1)
            ->sum('carpet_materials.amount');

        $tar_pashm = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->where('carpet_materials.category_id', 2)
            ->sum('carpet_materials.amount');
        $tar_abrishm = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->where('carpet_materials.category_id', 3)
            ->sum('carpet_materials.amount');

        $afg_money = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
            ->sum('carpet_materials.total_price_af');

        $usd_money = DB::table('carpet_materials')
            ->join('carpets', 'carpet_materials.carpet_id', 'carpets.carpet_id')
            ->where('carpets.status', 0)
            ->where('carpets.agent_id', $agent_id)
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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }

        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';
        $all = '';
        $warehouses = Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;

        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();
        $qualities = \App\Quality::all();
        $currencies = \App\Currency::all();

        return view('carpets.list-weight', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'all', 'tar_pakhta', 'tar_pashm', 'tar_abrishm', 'afg_money', 'usd_money', 'warehouses', 'defaultWarehouseId', 'inventoryAccounts', 'qualities', 'currencies'));
    }




    public function PostWeight(Request $request)
    {
        $data = $this->Valid();

        // Forensic FX Snapshot
        $currency = \App\Currency::find($request->currency_id);
        $afnCurrency = \App\Currency::where('code', 'AFN')->first();
        $currencyCode = $currency ? $currency->code : 'USD';
        $data['original_price'] = $request->price_input;
        $data['currency_id'] = $request->currency_id;
        $data['currency_code'] = $currencyCode;
        $data['carpet_no'] = $request->parcha_number;
        $data['exchange_rate'] = $request->exchange_rate;
        
        // USD Normalization
        $data['total_price'] = $request->total_price; // Total in USD
        $data['total_price_af'] = $request->total_price_af; // Forensic Native Total (as per user request)
        $data['carpet_price_us'] = $request->total_price;
        $data['carpet_price'] = $request->total_price_af;
        
        // Forensic AFN rate for legacy reporting
        $data['dollar_rate'] = ($afnCurrency && $afnCurrency->exchange_rate > 0) ? (1 / $afnCurrency->exchange_rate) : 1;

        $image = '';
        if ($request->has('carpet_image')) {
            $file = $request->file('carpet_image');
            $fileExt = $file->getClientOriginalExtension();
            $fileName = time() . '' . rand(1000, 9999) . '-carpet-image.' . $fileExt;
            $image = $file->move('uploads/carpet-image/', $fileName);
        }
        $data['carpet_image'] = $image;

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . ($request->parcha_number ?? $request->carpet_no) . " در سیستم اضافه شد ";
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

        $tar_pakhta = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 1)->sum('amount');
        $tar_pashm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 2)->sum('amount');
        $tar_abrishm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 3)->sum('amount');

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
        $afg_money = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price_af');
        $usd_money = $materialMoney;

        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();
        $rawMaterialAccounts = $this->accountSelectionService->getValidAccounts('RAW_MATERIAL', 'credit');
        $expenseAccounts = $this->accountSelectionService->getValidAccounts('EXPENSE', 'debit');
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();

        return view('carpets.weight-details', compact('carpet', 'carpetCheckBook', 'carpetMaterials', 'categories', 'materialMoney', 'material_types', 'CheckNo', 'material', 'tar_pakhta', 'tar_pashm', 'tar_abrishm', 'afg_money', 'usd_money', 'inventoryAccounts', 'rawMaterialAccounts', 'expenseAccounts', 'currencies', 'warehouses'));
    }

    public function editWeight($id)
    {

        $editCarpet = Carpet::find($id);



        $carpets = DB::table('carpets')
            ->leftJoin('agents', 'carpets.agent_id', 'agents.agent_id')
            ->leftJoin('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('status', 0)
            ->where('agents.contract_type', 'weight')
            ->orderBy('parcha_number', 'DESC')
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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents = Agents::where('contract_type', 'weight')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $qualities = Quality::all();
        $warehouses = Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;

        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();
        $currencies = \App\Currency::all();

        return view('carpets.list-weight', compact('carpets', 'editCarpet', 'agents', 'orders', 'types', 'employees', 'AccountNo', 'qualities', 'warehouses', 'defaultWarehouseId', 'inventoryAccounts', 'currencies'));
    }

    public function UpdatetWeight(Request $request, $carpet_id)
    {
        $carpet = Carpet::find($carpet_id);

        if ($carpet->check_book != null) {
            $check = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
            $check->agent_id = $request->agent_id;
            $check->update();
        }
        $data = $this->UpdateValid();

        // Forensic FX Snapshot update
        $currency = \App\Currency::find($request->currency_id);
        $afnCurrency = \App\Currency::where('code', 'AFN')->first();
        
        $data['original_price'] = $request->price_input;
        $data['currency_id'] = $request->currency_id;
        $data['currency_code'] = $currency ? $currency->code : 'USD';
        $data['carpet_no'] = $request->parcha_number;
        
        // USD Normalization update
        $data['total_price'] = $request->total_price;
        $data['carpet_price_us'] = $request->total_price;
        $data['total_price_af'] = $request->total_price_af;
        $data['exchange_rate'] = $request->exchange_rate;
        
        $data['dollar_rate'] = ($afnCurrency && $afnCurrency->exchange_rate > 0) ? (1 / $afnCurrency->exchange_rate) : 1;

        if ($request->has('carpet_image')) {
            $file = $request->file('carpet_image');
            $fileExt = $file->getClientOriginalExtension();
            $fileName = time() . '' . rand(1000, 9999) . '-carpet-image.' . $fileExt;
            $image = $file->move('uploads/carpet-image/', $fileName);
            $data['carpet_image'] = $image;
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->parcha_number . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $oldWarehouse = $carpet->warehouse_id;
        $oldAccount = $carpet->override_inventory_account_id;
        $oldCreditAccount = $carpet->override_credit_account_id;
        $oldTotalPrice = $carpet->total_price;
        $oldArea = $carpet->area;
        $oldDate = $carpet->date;
        $oldAgentId = $carpet->agent_id;
        $oldCarpetNo = $carpet->carpet_no;
        $oldParchaNo = $carpet->parcha_number;

        $update = Carpet::where('carpet_id', $carpet_id)->update($data);
        if ($update) {
            $this->syncAccounting(
                Carpet::find($carpet_id),
                $oldWarehouse,
                $oldAccount,
                $oldCreditAccount,
                $oldTotalPrice,
                $oldArea,
                $oldDate,
                $oldAgentId,
                $oldCarpetNo,
                $oldParchaNo
            );
        }
        if ($update) {
            return redirect('/dashboard/list-weight')->with('status', 'پارچه موفقانه بروز شد !');
        } else {
            return redirect('/dashboard/list-weight')->with('error', 'مشکل در سرور وجود داره!');
        }
    }
    /** Weight Carpet codes end */

    /** buy Carpet codes start */


    public function pass_parcha(Request $request)
    {
        $carpet = Carpet::find($request->carpet_id);
        if (!$carpet)
            return response()->json(['status' => 'error', 'message' => 'Carpet not found']);

        $carpet->status = 1;
        if ($request->has('warehouse_id')) {
            $carpet->warehouse_id = $request->warehouse_id;
        }
        $carpet->update();

        $accountingService = resolve(\App\Services\AccountingService::class);
        $accountingService->failIfLocked(now()->format('Y-m-d'));

        try {
            $this->inventoryManager->processProductionCompletion($carpet, [
                'quantity' => 1,
                'amount' => $carpet->total_price,
                'warehouse_id' => $request->warehouse_id ?? ($carpet->warehouse_id ?? 1),
                'area' => (float) ($carpet->area ?? 0),
                'date' => now()->format('Y-m-d'),
                'reference' => $carpet->parcha_number,
                'description' => "Production Completion: #" . $carpet->parcha_number,
                'override_debit_account_id' => $request->override_debit_account_id ?? $carpet->override_inventory_account_id,
                'override_credit_account_id' => $request->override_credit_account_id ?? $carpet->override_credit_account_id,
            ]);
        } catch (\Exception $e) {
            \Log::error("ERP Sync failed: " . $e->getMessage());
        }

        return response()->json(['status' => 'success']);
    }




    public function listBuyCarpet(Request $request)
    {
        $allAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        
        $query = Carpet::orderBy('carpet_no', 'DESC')
            ->whereIn('status', [1, 12])
            ->whereHas('agent', function($q) {
                $q->where('contract_type', 'carpet seller');
            })
            ->with('agent');

        // Advanced Filter Logic
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }
        if ($request->filled('from_id') && $request->filled('to_id')) {
            $query->whereBetween('carpet_no', [$request->from_id, $request->to_id]);
        } elseif ($request->filled('from_id')) {
            $query->where('carpet_no', 'like', '%' . $request->from_id . '%');
        }
        if ($request->filled('map_number')) {
            $query->where('map_number', 'like', '%' . $request->map_number . '%');
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->filled('quality_id')) {
            $query->where('quality_id', $request->quality_id);
        }
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        $carpets = $query->paginate(20);
        $carpets->appends($request->all());

        $lastId = Carpet::where('carpet_no', 'LIKE', 'QB%')->max('carpet_no');

        if ($lastId) {
            $numericPart = preg_replace('/[^0-9]/', '', $lastId);
            $nextVal = intval($numericPart) + 1;
            $len = strlen($numericPart);
            $AccountNo = 'QB' . sprintf('%0' . $len . 'd', $nextVal);
        } else {
            $AccountNo = 'QB1000';
        }
        $agents = Agents::where('contract_type', 'carpet seller')->get();
        $orders = CarpetOrder::orderBy('order_number')->get();
        $types = CarpetType::all();
        $qualities = \App\Quality::all();
        $currencies = \App\Currency::all();
        $editCarpet = '';
        $warehouses = \App\Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;
        $purchaseInvoices = \App\PurchaseInvoice::where('status', 'open')->with('agent.user')->get();

        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'qualities', 'currencies', 'editCarpet', 'warehouses', 'defaultWarehouseId', 'allAccounts', 'purchaseInvoices'));
    }

    public function show_all_buy_carpet()
    {
        $allAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $carpets = Carpet::orderBy('carpet_no', 'DESC')
            ->whereIn('status', [1, 12])
            ->whereHas('agent', function($q) {
                $q->where('contract_type', 'carpet seller');
            })
            ->with('agent')
            ->get();
        $lastId = Carpet::where('carpet_no', 'LIKE', 'QB%')->max('carpet_no');
        if ($lastId) {
            $numericPart = preg_replace('/[^0-9]/', '', $lastId);
            $nextVal = intval($numericPart) + 1;
            $len = strlen($numericPart);
            $AccountNo = 'QB' . sprintf('%0' . $len . 'd', $nextVal);
        } else {
            $AccountNo = 'QB1000';
        }
        $agents = Agents::where('contract_type', 'carpet seller')->get();
        $orders = CarpetOrder::orderBy('order_number')->get();
        $types = CarpetType::all();
        $all = '';
        $editCarpet = '';
        $warehouses = \App\Warehouse::all();
        $qualities = \App\Quality::all();
        $currencies = \App\Currency::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;
        $purchaseInvoices = \App\PurchaseInvoice::where('status', 'open')->with('agent.user')->get();

        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'qualities', 'currencies', 'editCarpet', 'all', 'warehouses', 'defaultWarehouseId', 'allAccounts', 'purchaseInvoices'));
    }


    public function search_buy_carpet(Request $request)
    {
        $allAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $search = $request->search;

        $carpets = Carpet::whereIn('status', [1, 12])
            ->whereHas('agent', function($q) {
                $q->where('contract_type', 'carpet seller');
            })
            ->where(function($q) use ($search) {
                $q->where('carpet_no', 'like', '%' . $search . '%')
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
                    });
            })
            ->get();

        $lastId = Carpet::where('carpet_no', 'LIKE', 'QB%')->max('carpet_no');
        if ($lastId) {
            $numericPart = preg_replace('/[^0-9]/', '', $lastId);
            $nextVal = intval($numericPart) + 1;
            $len = strlen($numericPart);
            $AccountNo = 'QB' . sprintf('%0' . $len . 'd', $nextVal);
        } else {
            $AccountNo = 'QB1000';
        }
        $agents = Agents::where('contract_type', 'carpet seller')->get();
        $orders = CarpetOrder::orderBy('order_number')->get();
        $types = CarpetType::all();
        $all = '';
        $editCarpet = '';
        $warehouses = \App\Warehouse::all();
        $qualities = \App\Quality::all();
        $currencies = \App\Currency::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;
        $purchaseInvoices = \App\PurchaseInvoice::where('status', 'open')->with('agent.user')->get();

        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'search', 'AccountNo', 'orders', 'types', 'all', 'editCarpet', 'qualities', 'currencies', 'warehouses', 'defaultWarehouseId', 'allAccounts', 'purchaseInvoices'));
    }


    function PostBuyCarpet(Request $request)
    {

        $data = $this->Valid();

        $glReference = null;
        if ($request->purchase_invoice_id) {
            $invoice = \App\PurchaseInvoice::findOrFail($request->purchase_invoice_id);
            if ($invoice->status === 'closed') {
                return redirect()->back()->withErrors(['purchase_invoice_id' => 'این بل خرید بسته شده است و امکان اضافه کردن قالین جدید به آن وجود ندارد.'])->withInput();
            }
            $glReference = $invoice->invoice_number;
        }
        $data['purchase_invoice_id'] = $request->purchase_invoice_id;

        $image = '';
        if ($request->has('carpet_image')) {
            $file = $request->file('carpet_image');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . rand(1000, 9999) . '-carpet-image.' . $fileExt;
            $image = $file->move('uploads/carpet-image/', $fileName);
        }

        $data['carpet_image'] = $image;

        // Forensic FX Snapshot
        $currency = \App\Currency::find($request->currency_id);
        $afnCurrency = \App\Currency::where('code', 'AFN')->first();
        $currencyCode = $currency ? $currency->code : 'USD';
        
        $data['original_price'] = $request->price_input;
        $data['currency_id'] = $request->currency_id;
        $data['currency_code'] = $currencyCode;
        $data['exchange_rate'] = $request->exchange_rate;
        $data['total_price'] = $request->total_price;
        $data['total_price_af'] = $request->total_price_af;
        $data['carpet_price_us'] = $request->total_price;
        $data['carpet_price'] = $request->total_price_af;
        $data['dollar_rate'] = ($afnCurrency && $afnCurrency->exchange_rate > 0) ? (1 / $afnCurrency->exchange_rate) : 1;
        
        $data['buying_width'] = $request->width ?? 0;
        $data['buying_height'] = $request->height ?? 0;
        $data['buying_area'] = $request->area ?? 0;

        // Wrap legacy creation and ERP logic in a single atomic transaction via the Manager
        $carpet = new Carpet($data);

        $this->inventoryManager->processPurchase($carpet, [
            'transaction_type' => 'carpet_purchase',
            'quantity' => 1,
            'unit_cost' => $carpet->original_price,
            'warehouse_id' => $request->warehouse_id ?? 1,
            'area' => (float) ($carpet->area ?? 0),
            'date' => $carpet->date ?? now()->format('Y-m-d'),
            'total_amount' => (float)($carpet->area ?? 0) * (float)($carpet->original_price ?? 0),
            'currency_code' => $carpet->currency_code,
            'exchange_rate' => $carpet->exchange_rate,
            'party_type' => 'App\Agents',
            'party_id' => $carpet->agent_id,
            'reference' => $glReference ?? $carpet->carpet_no,
            'description' => "Direct Purchase of Carpet #" . $carpet->carpet_no,
            'override_debit_account_id' => $request->override_inventory_account_id,
            'override_credit_account_id' => $request->override_credit_account_id,
        ], function () use ($carpet, $request) {
            $carpet->save();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر  " . $request->carpet_no . " در سیستم اضافه شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
        });

        return redirect('/dashboard/list-buy-carpet')->with('status', 'پارچه موفقانه ثبت شد!');
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
        
        // Financial Summaries
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price'); // Forensic USD total
        $agentMoney = 0; // Legacy Agent money if needed, currently 0 for direct buy
        
        // Forensic Dependencies
        $currencies = \App\Currency::all();
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $expenseAccounts = $this->accountSelectionService->getValidAccounts('EXPENSE', 'debit');
        $inventoryAccounts = \App\ChartOfAccount::orderBy('account_code')->get();

        // Check Number Generation
        $lastCheck = CarpetCheckBook::latest()->first();
        $CheckNo = 'CH-' . ($lastCheck ? (int)substr($lastCheck->check_number, -1) + 1 : 1);

        return view('carpets.buy-carpet-details', compact('carpet', 'carpetCheckBook', 'carpetMaterials', 'categories', 'material_types', 'materialMoney', 'agentMoney', 'currencies', 'expenseAccounts', 'inventoryAccounts', 'CheckNo'));
    }

    public function editBuyCarpet($id)
    {
        $allAccounts = \App\ChartOfAccount::orderBy('account_code')->get();

        $editCarpet = Carpet::find($id);
        $carpets = Carpet::orderBy('carpet_no', 'DESC')
            ->whereIn('status', [1, 12])
            ->whereHas('agent', function($q) {
                $q->where('contract_type', 'carpet seller');
            })
            ->with('agent')
            ->paginate(20);

        $lastId = Carpet::where('carpet_no', 'LIKE', 'QB%')->max('carpet_no');
        if ($lastId) {
            $numericPart = preg_replace('/[^0-9]/', '', $lastId);
            $nextVal = intval($numericPart) + 1;
            $len = strlen($numericPart);
            $AccountNo = 'QB' . sprintf('%0' . $len . 'd', $nextVal);
        } else {
            $AccountNo = 'QB1000';
        }
        $agents = Agents::where('contract_type', 'carpet seller')->get();
        $orders = CarpetOrder::orderBy('order_number')->get();
        $types = CarpetType::all();
        $qualities = Quality::all();
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();
        $mapping = MappingRule::where('mapping_key', 'WEIGHT_CARPET_ENTRY')->first();
        $defaultWarehouseId = ($mapping && $mapping->warehouse_id) ? $mapping->warehouse_id : 1;
        $purchaseInvoices = \App\PurchaseInvoice::where('status', 'open')->with('agent.user')->get();

        return view('carpets.list-buy-carpet', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'editCarpet', 'qualities', 'currencies', 'warehouses', 'defaultWarehouseId', 'allAccounts', 'purchaseInvoices'));
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

        if ($request->purchase_invoice_id) {
            $invoice = \App\PurchaseInvoice::findOrFail($request->purchase_invoice_id);
            if ($invoice->status === 'closed' && $carpet->purchase_invoice_id != $request->purchase_invoice_id) {
                return redirect()->back()->withErrors(['purchase_invoice_id' => 'این بل خرید بسته شده است و امکان اضافه کردن قالین جدید به آن وجود ندارد.'])->withInput();
            }
        }
        $data['purchase_invoice_id'] = $request->purchase_invoice_id;

        if ($request->has('carpet_image')) {

            $file = $request->file('carpet_image');


            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . rand(1000, 9999) . '-carpet-image.' . $fileExt;
            $image = $file->move('uploads/carpet-image/', $fileName);

            $data['carpet_image'] = $image;
        }

        // Forensic FX Snapshot update
        $currency = \App\Currency::find($request->currency_id);
        $afnCurrency = \App\Currency::where('code', 'AFN')->first();
        
        $data['original_price'] = $request->price_input;
        $data['currency_id'] = $request->currency_id;
        $data['currency_code'] = $currency ? $currency->code : 'USD';
        $data['exchange_rate'] = $request->exchange_rate;
        $data['total_price'] = $request->total_price;
        $data['carpet_price_us'] = $request->total_price;
        $data['total_price_af'] = $request->total_price_af;
        $data['dollar_rate'] = ($afnCurrency && $afnCurrency->exchange_rate > 0) ? (1 / $afnCurrency->exchange_rate) : 1;

        // Map width/height/area from edit form specifically to buying dimensions only
        $data['buying_width'] = $request->width ?? ($carpet->buying_width ?? $carpet->width);
        $data['buying_height'] = $request->height ?? ($carpet->buying_height ?? $carpet->height);
        $data['buying_area'] = $request->area ?? ($carpet->buying_area ?? $carpet->area);
        unset($data['width'], $data['height'], $data['area']);

        $oldWarehouse = $carpet->warehouse_id;
        $oldAccount = $carpet->override_inventory_account_id;
        $oldCreditAccount = $carpet->override_credit_account_id;
        $oldTotalPrice = $carpet->total_price;
        $oldArea = $carpet->area;
        $oldDate = $carpet->date;
        $oldAgentId = $carpet->agent_id;
        $oldCarpetNo = $carpet->carpet_no;
        $oldParchaNo = $carpet->parcha_number;

        $update = Carpet::where('carpet_id', $carpet_id)->update($data);
        if ($update) {
            $this->syncAccounting(
                Carpet::find($carpet_id),
                $oldWarehouse,
                $oldAccount,
                $oldCreditAccount,
                $oldTotalPrice,
                $oldArea,
                $oldDate,
                $oldAgentId,
                $oldCarpetNo,
                $oldParchaNo
            );
        }
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
        function store(
        Request $request
    ) {

        $data = $this->Valid();
        $image = '';
        if ($request->has('carpet_image')) {
            $file = $request->file('carpet_image');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . rand(1000, 9999) . '-carpet-image.' . $fileExt;
            $image = $file->move('uploads/carpet-image/', $fileName);
        }

        $data['carpet_image'] = $image;
        if (!isset($data['carpet_no']) || empty($data['carpet_no'])) {
            $data['carpet_no'] = $data['parcha_number'] ?? 'TEMP-' . time();
        }

        // Set multi-currency forensic markers
        $currency = \App\Currency::find($request->currency_id);
        $afnCurrency = \App\Currency::where('code', 'AFN')->first();
        $currencyCode = $currency ? $currency->code : 'USD';

        $data['original_price'] = $request->price_input;
        $data['currency_id'] = $request->currency_id;
        $data['currency_code'] = $currencyCode;

        // Calculate dollar_rate (AFN per 1 USD) for reporting
        $data['dollar_rate'] = ($afnCurrency && $afnCurrency->exchange_rate > 0) ? (1 / $afnCurrency->exchange_rate) : 1;
        $data['total_price_af'] = $data['total_price'] * $data['dollar_rate'];
        $data['carpet_price_us'] = $data['total_price'];
        $data['carpet_price'] = $data['total_price_af'];
        
        $data['buying_width'] = $request->width ?? 0;
        $data['buying_height'] = $request->height ?? 0;
        $data['buying_area'] = $request->area ?? 0;

        // Wrap legacy creation and ERP logic in a single atomic transaction via the Manager
        $carpet = new Carpet($data);

        // Fetch currency details if provided
        $currency = \App\Currency::find($request->currency_id);
        $currencyCode = $currency ? $currency->code : 'USD';
        $exchangeRate = $currency ? $currency->exchange_rate : 1.0;

        $this->inventoryManager->processPurchase($carpet, [
            'transaction_type' => 'carpet_purchase',
            'quantity' => 1,
            'unit_cost' => $carpet->original_price,
            'warehouse_id' => $request->warehouse_id ?? 1,
            'area' => (float) ($carpet->area ?? 0),
            'date' => $carpet->date ?? now()->format('Y-m-d'),
            'total_amount' => (float)($carpet->area ?? 0) * (float)($carpet->original_price ?? 0),
            'currency_code' => $carpet->currency_code,
            'exchange_rate' => $carpet->exchange_rate,
            'party_type' => 'App\Agents',
            'party_id' => $carpet->agent_id,
            'reference' => $carpet->parcha_number ?? $carpet->carpet_no,
            'description' => "Purchase of Carpet #" . ($carpet->parcha_number ?? $carpet->carpet_no),
            'currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'override_debit_account_id' => $request->override_inventory_account_id,
            'override_credit_account_id' => $request->override_credit_account_id,
        ], function () use ($carpet, $request) {
            $carpet->save();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " قالین نمبر  " . ($request->parcha_number ?? $request->carpet_no) . " در سیستم اضافه شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
        });

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

        $tar_pakhta = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 1)->sum('amount');
        $tar_pashm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 2)->sum('amount');
        $tar_abrishm = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->where('category_id', 3)->sum('amount');

        $lastId = CarpetCheckBook::where('agent_id', $carpet->agent_id)->latest()->first();

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
        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();
        $rawMaterialAccounts = $this->accountSelectionService->getValidAccounts('MATERIAL_INVENTORY', 'credit');
        $expenseAccounts = $this->accountSelectionService->getValidAccounts('REPAIR_EXPENSE', 'debit');
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();

        return view('carpets.carpet-contract-details', compact('carpet', 'material', 'carpetCheckBook', 'carpetMaterials', 'categories', 'material_types', 'materialMoney', 'CheckNo', 'tar_pakhta', 'tar_pashm', 'tar_abrishm', 'inventoryAccounts', 'rawMaterialAccounts', 'expenseAccounts', 'currencies', 'warehouses'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Carpets $carpets
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allAccounts = AppChartOfAccount::orderBy('account_code')->get();

        $editCarpet = Carpet::find($id);

        //        $carpets = Carpet::with('agent')->where('status',10)->whereHas('agent', function ($q) {
//            $q->where('contract_type', '=', 'contractional');
//        })->orderBy('parcha_number', 'DESC')->paginate(20);

        $carpets = DB::table('carpets')
            ->join('agents', 'carpets.agent_id', 'agents.agent_id')
            ->join('users', 'agents.user_id', 'users.id')
            ->leftJoin('customer_orders', 'carpets.order_id', 'customer_orders.co_id')
            ->leftJoin('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
            ->leftJoin('qualities', 'carpets.quality_id', 'qualities.id')
            ->select('carpets.*', 'customer_orders.order_name as order_number', 'users.name', 'agents.account_no', 'carpet_types.carpet_type', 'qualities.quality')

            ->where('status', 0)
            ->where('contract_type', 'contractional')
            ->orderBy('parcha_number', 'DESC')
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

        $lastId = Carpet::where('parcha_number', '!=', 'Null')->latest()->first();
        $ParchaNo = '';
        if ($lastId) {
            $lastId = $lastId->parcha_number;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'PN' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'PN' . sprintf('%05d', '10001');
        }



        $agents = Agents::where('contract_type', 'contractional')->get();
        $orders = CustomerOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $qualities = Quality::all();

        $qualities = \App\Quality::all();
        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::all();
        return view('carpets.contract-carpet-list', compact('carpets', 'agents', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'qualities', 'warehouses', 'inventoryAccounts', 'currencies'));


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
        if ($request->has('carpet_image')) {

            $file = $request->file('carpet_image');


            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . rand(1000, 9999) . '-carpet-image.' . $fileExt;
            $image = $file->move('uploads/carpet-image/', $fileName);

            $data['carpet_image'] = $image;
        }



        $currency = \App\Currency::find($request->currency_id);
        $afnCurrency = \App\Currency::where('code', 'AFN')->first();

        $data['original_price'] = $request->price_input;
        $data['currency_id'] = $request->currency_id;
        $data['currency_code'] = $currency ? $currency->code : 'USD';

        // Update dollar_rate and AFN reporting total
        $data['dollar_rate'] = ($afnCurrency && $afnCurrency->exchange_rate > 0) ? (1 / $afnCurrency->exchange_rate) : 1;
        $data['total_price_af'] = $data['total_price'] * $data['dollar_rate'];

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->parcha_number . " در سیستم ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $oldWarehouse = $carpet->warehouse_id;
        $oldAccount = $carpet->override_inventory_account_id;
        $oldCreditAccount = $carpet->override_credit_account_id;
        $oldTotalPrice = $carpet->total_price;
        $oldArea = $carpet->area;
        $oldDate = $carpet->date;
        $oldAgentId = $carpet->agent_id;
        $oldCarpetNo = $carpet->carpet_no;
        $oldParchaNo = $carpet->parcha_number;

        $update = $carpet->update($data);
        if ($update) {
            $this->syncAccounting(
                $carpet,
                $oldWarehouse,
                $oldAccount,
                $oldCreditAccount,
                $oldTotalPrice,
                $oldArea,
                $oldDate,
                $oldAgentId,
                $oldCarpetNo,
                $oldParchaNo
            );
        }

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
            'carpet_no' => 'required|unique:carpets,carpet_no',
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
            'warehouse_id' => '',
            'override_inventory_account_id' => '',
            'override_credit_account_id' => '',
            'currency_id' => '',
            'currency_code' => '',
            'original_price' => '',
            'employee_name' => '',
            'carpet_image' => 'nullable',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
        ]);
    }

    protected function UpdateValid($carpetId = null)
    {
        if (is_null($carpetId)) {
            $route = request()->route();
            if ($route) {
                $carpetId = $route->parameter('carpet') ?? $route->parameter('id') ?? $route->parameter('carpet_id');
                if (is_object($carpetId) && method_exists($carpetId, 'getKey')) {
                    $carpetId = $carpetId->getKey();
                }
            }
        }

        return request()->validate([
            'parcha_number' => '',
            'dollar_rate' => '',
            'carpet_no' => 'required|unique:carpets,carpet_no,' . ($carpetId ?? 'NULL') . ',carpet_id',
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
            'warehouse_id' => '',
            'override_inventory_account_id' => '',
            'override_credit_account_id' => '',
            'currency_id' => '',
            'currency_code' => '',
            'original_price' => '',
            'employee_name' => '',
            'carpet_image' => 'nullable',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
        ]);
    }


    /**
     * Reverses and re-posts transactions if warehouse or account changed on a posted carpet
     */
    private function syncAccounting(
        $carpet,
        $oldWarehouseId,
        $oldAccountId,
        $oldCreditAccountId = null,
        $oldTotalPrice = null,
        $oldArea = null,
        $oldDate = null,
        $oldAgentId = null,
        $oldCarpetNo = null,
        $oldParchaNo = null
    ) {
        if (in_array($carpet->status, [1, 12])) {
            if ($carpet->warehouse_id != $oldWarehouseId || 
                $carpet->override_inventory_account_id != $oldAccountId || 
                $carpet->override_credit_account_id != $oldCreditAccountId ||
                ($oldTotalPrice !== null && $carpet->total_price != $oldTotalPrice) ||
                ($oldArea !== null && $carpet->area != $oldArea) ||
                ($oldDate !== null && $carpet->date != $oldDate) ||
                ($oldAgentId !== null && $carpet->agent_id != $oldAgentId) ||
                ($oldCarpetNo !== null && $carpet->carpet_no != $oldCarpetNo) ||
                ($oldParchaNo !== null && $carpet->parcha_number != $oldParchaNo)) {
                try {
                    $this->inventoryManager->reverseTransactions($carpet, 'Correction: Warehouse/Account/Detail change');
                    if ($carpet->agent && $carpet->agent->contract_type == 'carpet seller') {
                        $this->inventoryManager->processPurchase($carpet, [
                            'transaction_type' => 'carpet_purchase',
                            'quantity' => 1,
                            'unit_cost' => $carpet->original_price,
                            'warehouse_id' => $carpet->warehouse_id,
                            'area' => (float) ($carpet->area ?? 0),
                            'date' => $carpet->date ?? now()->format('Y-m-d'),
                            'total_amount' => (float)($carpet->area ?? 0) * (float)($carpet->original_price ?? 0),
                            'currency_code' => $carpet->currency_code,
                            'exchange_rate' => $carpet->exchange_rate,
                            'party_type' => 'App\Agents',
                            'party_id' => $carpet->agent_id,
                            'reference' => $carpet->carpet_no,
                            'override_debit_account_id' => $carpet->override_inventory_account_id,
                            'override_credit_account_id' => $carpet->override_credit_account_id,
                        ]);
                    } else {
                        $this->inventoryManager->processProductionCompletion($carpet, [
                            'quantity' => 1,
                            'amount' => $carpet->total_price,
                            'warehouse_id' => $carpet->warehouse_id,
                            'area' => (float) ($carpet->area ?? 0),
                            'date' => now()->format('Y-m-d'),
                            'reference' => $carpet->parcha_number,
                            'override_debit_account_id' => $carpet->override_inventory_account_id,
                            'override_credit_account_id' => $carpet->override_credit_account_id,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Reversal sync failed: " . $e->getMessage());
                }
            }
        }
    }

    private function getStockDependencies()
    {
        $carpet_types = CarpetType::all();
        $warehouses = Warehouse::all();
        $invoices = Invoice::where('type', 'carpet')->where('status', 'open')->orderBy('id', 'DESC')->get();
        $packing_list = PakingList::orderBy('id', 'DESC')->get();

        $currencies = \App\Currency::all();
        $selectionService = $this->accountSelectionService;
        $allowedRevenueDebit = $selectionService->getValidAccounts('SALES_REVENUE', 'debit');
        $allowedRevenueCredit = $selectionService->getValidAccounts('SALES_REVENUE', 'credit');
        $mappingRevenue = MappingRule::where('mapping_key', 'SALES_REVENUE')->first();

        $allowedCogsDebit = $selectionService->getValidAccounts('SALES_COGS', 'debit');
        $allowedCogsCredit = $selectionService->getValidAccounts('SALES_COGS', 'credit');
        $mappingCogs = MappingRule::where('mapping_key', 'SALES_COGS')->first();

        return compact(
            'carpet_types',
            'invoices',
            'packing_list',
            'warehouses',
            'currencies',
            'allowedRevenueDebit',
            'allowedRevenueCredit',
            'mappingRevenue',
            'allowedCogsDebit',
            'allowedCogsCredit',
            'mappingCogs'
        );
    }

    public function update_dimensions(Request $request)
    {
        $request->validate([
            'carpet_id' => 'required',
            'height' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0'
        ]);

        $carpet = Carpet::where('carpet_id', $request->carpet_id)->firstOrFail();
        
        $old_width = $carpet->width;
        $old_height = $carpet->height;
        $old_area = $carpet->area;

        $carpet->width = $request->width;
        $carpet->height = $request->height;
        $carpet->area = $request->area;
        $carpet->save();

        $activity = new \App\Activity();
        $activity->user_id = auth()->user()->id;
        $activity->date = \Carbon\Carbon::today()->format('Y-m-d');
        $activity->description = "ابعاد قالین {$carpet->carpet_no} از {$old_height}x{$old_width} ({$old_area}m²) به {$request->height}x{$request->width} ({$request->area}m²) تغییر یافت.";
        $activity->save();

        return redirect()->back()->with('status', 'ابعاد نهایی قالین با موفقیت بروزرسانی شد.');
    }
}
