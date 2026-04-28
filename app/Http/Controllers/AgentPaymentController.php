<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AgentPayment;
use App\AgentPhone;
use App\Agents;
use App\Services\AccountingService;
use App\CarpetCheckBook;
use App\MaterialSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentPaymentController extends Controller
{
    protected $accountingService;
    
    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        //
    }

    private function postPaymentToAccounting($payment)
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'
            $amount = ($payment->amount > 0) ? $payment->amount : $payment->amount_af;

            $this->accountingService->postAutoTransaction('agent_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\Agents',
                'party_id' => $payment->agent_id,
                'reference' => 'AGT-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Agent Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function money_request()
    {
        $requests = AgentPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('agents.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = AgentPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0) 
                ? " مبلغ " . $payment->amount . " دالر برای نماینده تایید شد "
                : " مبلغ " . $payment->amount_af . " افغانی برای نماینده تایید شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id){
        $credit = AgentPayment::find($id);
        $credit->delete();
        return response()->json(['status','error']);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'agent_id' => 'required',
            ]);

            $payed = new AgentPayment();
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->check_number = $request->check_number;
            
            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }

            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();
            
            $currency = ($request->money_type == 'دالر') ? " دالر " : " افغانی ";

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت به نماینده " . $agent_name->name . " اکونت نمبر " . $agent_name->account_no . " به مبلغ " . $request->amount . $currency;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'پرداخت نماینده موفقانه ثبت و در سیستم مالی درج گردید!');
        });
    }

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

    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
            ]);

            $payed = AgentPayment::find($payment_id);
            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $request->agent_id)->first();

            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Agent Payment Edited');
            }

            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->agent_id = $request->agent_id;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->check_number = $request->check_number;

            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            $payed->update();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت نماینده " . $agent_name->name . " اکونت نمبر " . $agent_name->account_no;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/agent-payments/' . $request->agent_id)->with('status', 'ویرایش موفقانه انجام شد و حسابات مالی نماینده بروز گردید!');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = AgentPayment::find($id);
            $agent_name = DB::table('agents')
                ->join('users', 'agents.user_id', 'users.id')
                ->where('agents.agent_id', $payment->agent_id)->first();

            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Agent Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت نماینده " . $agent_name->name . " اکونت نمبر " . $agent_name->account_no;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
