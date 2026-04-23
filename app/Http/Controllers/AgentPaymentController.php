<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AgentPayment;
use App\AgentPhone;
use App\Agents;

use App\CarpetCheckBook;
use App\MaterialSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentPaymentController extends Controller
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function money_request()
    {
        $requests = AgentPayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('agents.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = AgentPayment::find($id);


        $payment->status = 1 ;
        $payment->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        if ($payment->amount > 0){
            $activity->description = " مبلغ " . $payment->amount . "دالر توسط سوپر ادمین اپروف شد ";
        }
        else{
            $activity->description = " مبلغ " . $payment->amount_af . "افغانی توسط سوپر ادمین اپروف شد ";
        }

        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }
    public function delete_request($id){
        $credit = AgentPayment::find($id);


        $credit->delete();


        return response()->json(['status','error']);
    }





    public function store(Request $request)
    {

        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'agent_id' => '',
            'dollar_rate' => '',
            'check_number' => '',
            'status' => ''

        ]);

        if ($request->money_type == 'دالر') {

            $payed = new AgentPayment();
            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->check_number = $request->check_number;
            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }

            $payed->save();

            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();
            if ($payed) {
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " نماینده به نام  " . $agent_name->name . ' اکونت نمبر '. $agent_name->account_no.  " به مبلغ " . $request->amount . " دالر را " . $request->type . 'کرد';
                $activity->user_id = Auth::user()->id;
                $activity->save();
                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        } else {
            $payed = new AgentPayment();
            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->check_number = $request->check_number;
            if (Auth::user()->role == 'SP'){
                $payed->status = 1;
            }
            else{
                $payed->status = 0;
            }
            $payed->save();

            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();
            if ($payed) {
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " نماینده به نام  " . $agent_name->name . ' اکونت نمبر '. $agent_name->account_no.  " به مبلغ " . $request->amount . " افغانی را " . $request->type . 'کرد';
                $activity->user_id = Auth::user()->id;
                $activity->save();
                return redirect()->back()->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AgentPayment $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function show($agent_id)
    {
        $payments = AgentPayment::where('agent_id', $agent_id)->orderBy('created_at', 'DESC')->paginate(30);
        $agent = Agents::find($agent_id);

        $debits_us = AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $agent_id)->where('status',1)->sum('amount');
        $debits_af = AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $agent_id)->where('status',1)->sum('amount_af');
        $credit_us = AgentPayment::where('type', '=', 'رسید')->where('agent_id', $agent_id)->where('status',1)->sum('amount');
        $credit_af = AgentPayment::where('type', '=', 'رسید')->where('agent_id', $agent_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $check_numbers = CarpetCheckBook::where('agent_id', '=', $agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        return view('agents.agent-payments', compact('agent', 'payments', 'paymentEdit', 'debits_us', 'debits_af', 'credit_af', 'credit_us', 'check_numbers', 'sale_numbers'));
    }

    public function show_all($agent_id)
    {
        $payments = AgentPayment::where('agent_id', $agent_id)->orderBy('created_at', 'DESC')->get();
        $agent = Agents::find($agent_id);

        $debits_us = AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $agent_id)->where('status',1)->sum('amount');
        $debits_af = AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $agent_id)->where('status',1)->sum('amount_af');
        $credit_us = AgentPayment::where('type', '=', 'رسید')->where('agent_id', $agent_id)->where('status',1)->sum('amount');
        $credit_af = AgentPayment::where('type', '=', 'رسید')->where('agent_id', $agent_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $check_numbers = CarpetCheckBook::where('agent_id', '=', $agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        $all = '';
        return view('agents.agent-payments', compact('agent', 'payments', 'paymentEdit', 'debits_us', 'debits_af', 'credit_af', 'credit_us', 'check_numbers', 'all', 'sale_numbers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AgentPayment $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = AgentPayment::find($payment_id);
        $payments = AgentPayment::where('agent_id', $paymentEdit->agent_id)->orderBy('created_at', 'DESC')->paginate(30);
        $agent = Agents::find($paymentEdit->agent_id);
        $debits_us = AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $paymentEdit->agent_id)->where('status',1)->sum('amount');
        $debits_af = AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $paymentEdit->agent_id)->where('status',1)->sum('amount_af');
        $credit_us = AgentPayment::where('type', '=', 'رسید')->where('agent_id', $paymentEdit->agent_id)->where('status',1)->sum('amount');
        $credit_af = AgentPayment::where('type', '=', 'رسید')->where('agent_id', $paymentEdit->agent_id)->where('status',1)->sum('amount_af');

        $check_numbers = CarpetCheckBook::where('agent_id', '=', $paymentEdit->agent_id)->orderBy('check_number', 'DESC')->distinct()->get(['check_number']);
        $sale_numbers = MaterialSale::where('agent_id', $paymentEdit->agent_id)->orderBy('sale_number', 'DESC')->distinct()->get(['sale_number']);
        return view('agents.agent-payments', compact('agent', 'payments', 'paymentEdit', 'debits_us', 'debits_af', 'credit_af', 'credit_us', 'check_numbers', 'sale_numbers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\AgentPayment $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => '',
            'agent_id' => '',
            'dollar_rate' => '',
            'check_number' => '',

        ]);

        if ($request->money_type == 'دالر') {

            $payed = AgentPayment::find($payment_id);

            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();

            $amount = '';
            $money = '';
            if ($payed->amount > 0) {
                $amount = $payed->amount;
                $money = 'دالر';
            } else {
                $money = 'افغانی';
                $amount = $payed->amount_af;
            }
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " در بیلانس نماینده به نام  " . $agent_name->name . ' اکونت نمبر '. $agent_name->account_no.  "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود  ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->amount = $request->amount;
            $payed->amount_af = 0;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->check_number = $request->check_number;
            $payed->update();


            if ($payed) {
                return redirect('/dashboard/agent-payments/' . $request->agent_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/agent-payments/' . $request->agent_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        } else {
            $payed = AgentPayment::find($payment_id);

            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();

            $amount = '';
            $money = '';
            if ($payed->amount > 0) {
                $amount = $payed->amount;
                $money = 'دالر';
            } else {
                $money = 'افغانی';
                $amount = $payed->amount_af;
            }
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " در بیلانس نماینده به نام  " . $agent_name->name  . ' اکونت نمبر '. $agent_name->account_no.  "  مبلغ " . $amount . ' ' . $money . " که " . $payed->type . 'کرده بود ویرایش شد به' . $request->amount . " دالر " . $request->type . ' کرد ';
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payed->amount = 0;
            $payed->amount_af = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->check_number = $request->check_number;
            $payed->update();
            if ($payed) {
                return redirect('/dashboard/agent-payments/' . $request->agent_id)->with('status', 'موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/agent-payments/' . $request->agent_id)->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgentPayment $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $payment = AgentPayment::find($id);

        $agent_name = DB::table('agents')
            ->join('users', 'agents.user_id', 'users.id')
            ->where('agents.agent_id', $payment->agent_id)->first();

        $amount = '';
        $money = '';
        if ($payment->amount > 0) {

            $amount = $payment->amount;
            $money = 'دالر';
        }
        else{
            $money = 'افغانی';
            $amount = $payment->amount_af;
        }

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "از بیلانس نماینده به نام  " . $agent_name->name  . ' اکونت نمبر '. $agent_name->account_no.  " مبلغ " . $amount . ' '. $money . " را حذف کرد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        $payment->delete();

        if ($payment) {
            return response()->json(['status' => 'success']);
        }
    }
}
