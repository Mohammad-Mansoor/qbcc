<?php

namespace App\Http\Controllers;

use App\AgentEmployee;
use App\CarpetCheckBook;
use App\CarpetOrder;
use App\CarpetType;
use App\MaterialCategory;
use App\MaterialType;
use App\Quality;
use Illuminate\Http\Request;
use App\Agents;
use App\Carpet;
use App\CarpetMaterial;
use App\AgentRecieved;

class AgentsCarpetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }
    public function carpet_details($carpet_id)
    {
        $carpet = Carpet::find($carpet_id);
        $carpetCheckBook = CarpetCheckBook::where('carpet_id', $carpet->carpet_id)->first();
        //        $agentRecieveds = 0; //AgentRecieved::where('carpet_id', $carpet->carpet_id)->paginate(8);
        $carpetMaterials = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->paginate(8);
        //        $agentMoney = AgentRecieved::where('carpet_id', $carpet->carpet_id)->sum('amount');
        $materialMoney = CarpetMaterial::where('carpet_id', $carpet->carpet_id)->sum('total_price');
        $categories = MaterialCategory::all();
        $material_types = MaterialType::all();
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();
        $debitAccounts = \App\ChartOfAccount::where('normal_balance', 'debit')->orderBy('account_code')->get();
        $creditAccounts = \App\ChartOfAccount::where('normal_balance', 'credit')->orderBy('account_code')->get();

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
        return view('agents-carpet.agent-carpet-details', compact('carpet', 'material', 'carpetCheckBook', 'carpetMaterials', 'categories', 'material_types', 'materialMoney', 'CheckNo', 'currencies', 'warehouses', 'debitAccounts', 'creditAccounts'));


    }

    public function agent_balance(Carpet $carpet)
    {
        $agent = Agents::where('agent_id', $carpet->agent_id)->first();
        $totalReceiv = 0;
        $mawad_ranga = CarpetMaterial::where('carpet_id', '=', $carpet->carpet_id)->where('category_id', '=', 2)->sum('amount');
        $mawad_pakhta = CarpetMaterial::where('carpet_id', '=', $carpet->carpet_id)->where('category_id', '=', 1)->sum('amount');

        return view('agents-carpet.carpet-balance', compact('carpet', 'agent', 'mawad_ranga', 'mawad_pakhta', 'totalReceiv'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agent = Agents::find($id);

        $carpets = Carpet::where('agent_id', '=', $agent->agent_id)->where('status', '0')->paginate(20);
        $metrazh = $carpets->sum('area');

        $mawad_ranga = CarpetMaterial::where('agent_id', '=', $agent->agent_id)->where('category_id', '=', 2)->get();
        $mawad_pakhta = CarpetMaterial::where('agent_id', '=', $agent->agent_id)->where('category_id', '=', 1)->get();



        $lastId = Carpet::latest()->first();
        $CarpetNo = '';
        if ($lastId) {
            $lastId = $lastId->carpet_no;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB-' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB-' . sprintf('%05d', '10101');
        }
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();
        $editCarpet = '';


        return view('agents-carpet.index', compact('carpets', 'agent', 'mawad_ranga', 'mawad_pakhta', 'metrazh', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'currencies', 'warehouses'));

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editCarpet = Carpet::find($id);
        $agent = Agents::find($editCarpet->agent_id);
        $carpets = Carpet::where('agent_id', '=', $editCarpet->agent_id)->where('status', '0')->paginate(20);
        $metrazh = $carpets->sum('area');

        $mawad_ranga = CarpetMaterial::where('agent_id', '=', $editCarpet->agent_id)->where('category_id', '=', 2)->get();
        $mawad_pakhta = CarpetMaterial::where('agent_id', '=', $editCarpet->agent_id)->where('category_id', '=', 1)->get();

        $lastId = Carpet::latest()->first();
        $CarpetNo = '';
        if ($lastId) {
            $lastId = $lastId->carpet_no;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB-' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB-' . sprintf('%05d', '10101');
        }
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $qualities = Quality::all();
        $currencies = \App\Currency::all();
        $warehouses = \App\Warehouse::all();

        return view('agents-carpet.index', compact('carpets', 'agent', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'qualities', 'metrazh', 'mawad_ranga', 'mawad_pakhta', 'currencies', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function agent_carpet_search(Request $request)
    {
        $agent = Agents::find($request->agent_id);
        $search = $request->search;
        $carpets = Carpet::where('agent_id', $request->agent_id)->where('status', '0')->where('carpet_no', 'like', '%' . $search . '%')
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
            ->WhereHas('carpet_order', function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            })
            ->WhereHas('type', function ($query) use ($search) {
                $query->where('carpet_type', 'like', '%' . $search . '%');
            })
            ->WhereHas('quality', function ($query) use ($search) {
                $query->where('quality', 'like', '%' . $search . '%');
            })
            ->paginate(20);



        $metrazh = $carpets->sum('area');

        $mawad_ranga = CarpetMaterial::where('agent_id', '=', $agent->agent_id)->where('category_id', '=', 2)->get();
        $mawad_pakhta = CarpetMaterial::where('agent_id', '=', $agent->agent_id)->where('category_id', '=', 1)->get();



        $lastId = Carpet::latest()->first();
        $CarpetNo = '';
        if ($lastId) {
            $lastId = $lastId->carpet_no;
            $lastId = substr($lastId, -5);
            $lastId++;
            $AccountNo = 'QB-' . sprintf('%05d', $lastId);
        } else {
            $AccountNo = 'QB-' . sprintf('%05d', '10101');
        }
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $employees = AgentEmployee::all();
        $editCarpet = '';



        return view('agents-carpet.index', compact('carpets', 'agent', 'mawad_ranga', 'mawad_pakhta', 'metrazh', 'AccountNo', 'orders', 'types', 'employees', 'editCarpet', 'search'));



    }


}
