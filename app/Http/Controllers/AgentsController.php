<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Activity;
use App\AgentPayment;
use App\AgentRecieved;
use App\Agents;
use App\User;
use App\AgentPhone;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Province;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        $data = Agents::where('account_status',1)->orderBy('agent_id', 'desc')->paginate(30);

        $agent= '';
        $lastId = Agents::latest()->first();
        $AccountNo = '';
        if ($lastId) {
            $lastId = $lastId->agent_id;
            $lastId++;
            $AccountNo = 'AG-' . sprintf('%04d', $lastId);
        } else {
            $AccountNo = 'AG-' . sprintf('%04d', '1');
        }
        $province = Province::orderBy('province')->get();
        $currencies = Currency::all();

        return view('agents.index', compact('data', 'agent','AccountNo','province', 'currencies'));
    }
    public function change_status($agent_id){

        $agent = Agents::find($agent_id);

        $agent_name = DB::table('agents')
            ->join('users', 'agents.user_id', 'users.id')
            ->where('agents.agent_id', $agent_id)->first();


        if ($agent->account_status == 0){
            $agent->account_status = 1;
            $agent->update();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حساب نماینده به نام  " . $agent_name->name . " را فعال کرد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            return redirect()->back()->with('status','حساب نماینده فعال شد');
        }else{

                $agent->account_status = 0;
                $agent->update();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حساب نماینده به نام  " . $agent_name->name . " را غیر فعال کرد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
                return redirect()->back()->with('status','حساب نماینده غیر فعال شد');

        }

    }



    public function accounts(){
        $data = Agents::orderBy('agent_id', 'desc')->paginate(30);


        $accounts = '';
        $agent= '';
        $lastId = Agents::latest()->first();
        $AccountNo = '';
        if ($lastId) {
            $lastId = $lastId->agent_id;
            $lastId++;
            $AccountNo = 'AG-' . sprintf('%04d', $lastId);
        } else {
            $AccountNo = 'AG-' . sprintf('%04d', '1');
        }
        $province = Province::orderBy('province')->get();
        $currencies = Currency::all();

        return view('agents.index', compact('data', 'accounts','agent','AccountNo','province', 'currencies'));
    }
    public  function deactive_accounts(){
        $data = Agents::where('account_status',0)->orderBy('agent_id', 'desc')->paginate(30);


        $agent= '';
        $lastId = Agents::latest()->first();
        $AccountNo = '';
        if ($lastId) {
            $lastId = $lastId->agent_id;
            $lastId++;
            $AccountNo = 'AG-' . sprintf('%04d', $lastId);
        } else {
            $AccountNo = 'AG-' . sprintf('%04d', '1');
        }
        $province = Province::orderBy('province')->get();
        $currencies = Currency::all();

        return view('agents.index', compact('data', 'agent','AccountNo','province', 'currencies'));
    }

    public function search(Request $request)
    {
        $search = $request->search;


        $data = Agents::whereHas('user', function ($query) use ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->orWhere('agent_father_name', 'like','%'.$search.'%')
            ->orWhere('national_id', 'like', '%' .$search.'%')
            ->orWhere('contract_type', 'like', '%'.$search.'%')
            ->orWhere('account_no', 'like', '%'.$search.'%')
            ->orwhereHas('phone', function ($query) use ($search) {
                $query->where('phone_no', 'like', '%'.$search.'%');
            })->paginate(30);

        $data->appends(['search' => $search]);

        $agent= '';
        $lastId = Agents::latest()->first();
        $AccountNo = '';
        if ($lastId) {
            $lastId = $lastId->agent_id;
            $lastId++;
            $AccountNo = 'AG-' . sprintf('%04d', $lastId);
        } else {
            $AccountNo = 'AG-' . sprintf('%04d', '1');
        }
        $province = Province::orderBy('province')->get();
        $currencies = Currency::all();


        return view('agents.index', compact('data','search','agent','AccountNo','province', 'currencies'));
    }
//    public function carpet_seller(){
//        $data = Agents::where('contract_type','carpet seller')->orderBy('agent_id' , 'desc')->get();
//        return view('agents.carpet-seller' , compact('data'));
//    }
//    public function carpet_seller_payment($agent)
//    {
//        $agent_received = AgentRecieved::where('agent_id', $agent)->get();
//        $agent = Agents::find($agent);
//        return view('agents.carpet-seller-payments', compact('agent', 'agent_received'));
//    }

    public function agent_payment($agent)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
        $ScanOfFile = '';
        $image = '';
        if ($request->has('contract_scan_file')) {
            $file = $request->file('contract_scan_file');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . '-AGENT-SCAN.' . $fileExt;
            $ScanOfFile = $file->move('uploads/contract-scan', $fileName);
        }
        if ($request->has('image')) {
            $file = $request->file('image');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . '-AGENT-Image.' . $fileExt;
            $image = $file->move('uploads/agent-image', $fileName);
        }


        $data['contract_scan_file'] = $ScanOfFile;
        $data['image'] = $image;
        $data['password'] = bcrypt($request->password);

        $UserId = User::create($data);
        $AgentId = $UserId->agents()->create($data);
        $AgentId->phone()->create($data);
//        if ($request->contract_type == 'carpet seller'){
//            return redirect('/dashboard/carpet-seller');
//        }else{


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نماینده به نام  " . $UserId->name . " اضافه شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect('/dashboard/agents')->with('status','موفقانه ثبت شد!');
//        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agents $agents
     * @return \Illuminate\Http\Response
     */
    public function show(Agents $agent)
    {
        return view('agents.manage', compact('agent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agents $agents
     * @return \Illuminate\Http\Response
     */
    public function edit(Agents $agent)
    {

        $data = Agents::where('account_status',1)->orderBy('agent_id', 'desc')->paginate(30);



        $lastId = Agents::latest()->first();
        $AccountNo = '';
        if ($lastId) {
            $lastId = $lastId->agent_id;
            $lastId++;
            $AccountNo = 'AG-' . sprintf('%04d', $lastId);
        } else {
            $AccountNo = 'AG-' . sprintf('%04d', '1');
        }
        $province = Province::orderBy('province')->get();
        $currencies = Currency::all();


        return view('agents.index', compact('data', 'AccountNo','province','agent', 'currencies'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Agents $agents
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agents $agent)
    {
        $ScanOfFile = '';
        $image = '';
        $data = $this->UpdateValid();
        if ($request->has('contract_scan_file')) {
            $file = $request->file('contract_scan_file');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . '-AGENT-SCAN.' . $fileExt;
            $ScanOfFile = $file->move('uploads/contract-scan', $fileName);
            $data['contract_scan_file'] = $ScanOfFile;
        }
        if ($request->has('image')) {
            $file = $request->file('image');
            $fileExt = $file->getClientOriginalExtension();
            if (!in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
                return redirect()->back()->withErrors(['msg' => 'فایل باید عکس باشد.']);
            }
            $fileName = time() . '' . '-AGENT-Image.' . $fileExt;
            $image = $file->move('uploads/agent-image', $fileName);
            $data['image'] = $image;
        }


        $update = $agent->update($data);
        $user = $agent->user->update($data);
        $agent_phone = AgentPhone::where('agent_id',$agent->agent_id)->first();
        $agent_phone->phone_no = $request->phone_no;
        $agent_phone->update();

        $agent_name = DB::table('agents')
            ->join('users', 'agents.user_id', 'users.id')
            ->where('agents.agent_id', $agent->agent_id)->first();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " نماینده به نام  " . $agent_name->name . " ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect('/dashboard/agents/')->with('status', 'معلومات نماینده موفقانه بروز رسانی شد');
    }

    /**
     * Remove the specified resource from storage.
     *x`x``z
     * @param  \App\Agents $agents
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agents $agents)
    {
        //
    }

    public function StorePhone(Request $request, Agents $id)
    {
        $data = $request->validate(['phone_no' => 'required']);
        $id->phone()->create($data);
        return redirect()->back()->with('status', 'شماره تلفن موفقاه ذخیره شد');
    }

    public function DeletePhone($id)
    {
        $AgentPhone = AgentPhone::find($id);
        $AgentPhone->delete();
        if ($AgentPhone) {
            return redirect()->back()->with('status', 'شماره تلفن پاک شد');
        }
    }

    public function updateNote(Request $request, $id)
    {
        $agent = Agents::findOrFail($id);
        $agent->note = $request->note;
        $agent->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'یادداشت با موفقیت بروز رسانی شد', 'note' => $agent->note]);
        }

        return redirect()->back()->with('status', 'یادداشت با موفقیت بروز رسانی شد');
    }

    protected function Valid()
    {
        return request()->validate([
            'name' => 'required|min:3|max:64',
            'last_name' => 'required|min:3|max:64',
            'email' => 'required|unique:users|email',
            'role' => 'required',
            'password' => 'required|min:8|max:12|required_with:confirm|same:confirm',
            'agent_father_name' => 'required|min:3|max:64',
            'agent_address' => 'required',
            'account_type' => 'required',
            'contract_type' => 'required',
            'contract_date' => 'required',
            'account_no' => 'required|unique:agents',
            'province_id' => 'required',
            'description' => '',
            'contract_scan_file' => '',
            'image' => '',
            'national_id' => '',
            'phone_no' => '',
            'user_id' => '',
            'note' => ''
        ]);
    }

    protected function UpdateValid()
    {
        return request()->validate([
            'name' => 'required|min:3|max:64',
            'last_name' => 'required|min:3|max:64',
            'agent_father_name' => 'required|min:3|max:64',
            'agent_address' => 'required',
            'contract_date' => 'required',
            'contract_type' => 'required',
            'account_type' => 'required',
            'province_id' => 'required',
            'description' => '',
            'contract_scan_file' => '',
            'image' => '',
            'national_id' => '',
            'phone_no' => '',
            'note' => ''
        ]);
    }
}
