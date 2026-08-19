<?php

namespace App\Http\Controllers;

use App\Activity;
use App\NewDifferentAccount;
use App\NewDifferentAccountPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\AccountingService;

class NewDifferentAccountPaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('permission:manage_different_account_payments')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('permission:view_different_account_money_requests')->only(['money_request']);
        $this->middleware('permission:approve_different_account_money_requests')->only(['approve_request']);
        $this->middleware('permission:reject_different_account_money_requests')->only(['delete_request']);
    }

    private function postPaymentToAccounting($payment)
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'

            $currencyMap = [
                1 => 'AFN',
                2 => 'USD',
                3 => 'PKR'
            ];
            $currencyCode = $currencyMap[$payment->currency] ?? 'USD';
            $dbCurrency = \App\Currency::where('code', $currencyCode)->first();
            $exchangeRate = $dbCurrency ? $dbCurrency->exchange_rate : 1.0;
            
            $this->accountingService->postAutoTransaction('different_account', $condition, [
                'date' => $payment->date,
                'amount' => $payment->amount,
                'currency_code' => $currencyCode,
                'exchange_rate' => $exchangeRate,
                'party_type' => 'App\NewDifferentAccount',
                'party_id' => $payment->account_id,
                'reference' => 'MISC-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Misc Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }
    public function index()
    {
        //
    }

    public function money_request()
    {
        $requests = NewDifferentAccountPayment::where('status', 0)->orderBy('id', 'DESC')->get();




        return view('new-different-account.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {

        $payment = NewDifferentAccountPayment::find($id);


        $payment->status = 1 ;
        $payment->update();


        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');

        $activity->description = " مبلغ " . $payment->amount . "دالر توسط سوپر ادمین اپروف شد ";

        $activity->user_id = Auth::user()->id;
        $activity->save();


        return response()->json(['status' => 'success']);

    }
    public function delete_request($id){
        $credit = NewDifferentAccountPayment::find($id);


        $credit->delete();


        return response()->json(['status','error']);
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
        $received_data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'currency' => 'required',
            'insert_credit' => '',
            'type' => '',
            'account_id' => '',
            'status' =>''


        ]);
        if (Auth::user()->isSuperAdmin()){
            $received_data['status'] = 1;
        }
        else{
            $received_data['status'] = 0;
        }

        $payed = NewDifferentAccountPayment::create($received_data);

        $account = NewDifferentAccount::find($request->account_id);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " حساب متفرقه به نام " . $account->name . ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . ' ' . $request->type . ' کرد ';
        $activity->user_id = Auth::user()->id;
        $activity->save();


        if ($payed) {
            return redirect()->back()->with('status', 'گرفت موفقانه ثبت شد !');
        } else {
            return redirect()->back()->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function show(NewDifferentAccountPayment $newDifferentAccountPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paymentEdit = NewDifferentAccountPayment::find($id);
        $payments = NewDifferentAccountPayment::where('account_id', $paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = NewDifferentAccount::where('id', '=', $paymentEdit->account_id)->first();


        $debits_af = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',1)->where('account_id',$id)->where('status',1)->sum('amount');
        $debits_usd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',2)->where('account_id',$id)->where('status',1)->sum('amount');
        $debits_cd = NewDifferentAccountPayment::where('type','=','گرفت')->where('currency',3)->where('account_id',$id)->where('status',1)->sum('amount');

        $credits_af = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',1)->where('account_id',$id)->where('status',1)->sum('amount');
        $credits_usd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',2)->where('account_id',$id)->where('status',1)->sum('amount');
        $credits_cd = NewDifferentAccountPayment::where('type','=','رسید')->where('currency',3)->where('account_id',$id)->where('status',1)->sum('amount');


        return view('new-different-account.account-payment', compact('account', 'payments', 'debits_af','debits_usd','debits_cd', 'credits_af','credits_usd','credits_cd', 'paymentEdit'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        return DB::transaction(function () use ($request, $id) {
            $request->validate([
                'amount' => 'required',
                'currency' => 'required',
                'description' => 'required',
                'date' => 'required',
                'type' => '',
                'account_id' => ''
            ]);

            $payment = NewDifferentAccountPayment::find($id);
            
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Misc Payment Edited');
            }

            $payment->amount = $request->amount;
            $payment->currency = $request->currency;
            $payment->description = $request->description;
            $payment->date = $request->date;
            $payment->type = $request->type;
            $payment->save();

            if ($payment->status == 1) {
                $this->postPaymentToAccounting($payment);
            }

            $account = NewDifferentAccount::find($request->account_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " حساب متفرقه به نام " . $account->name .  ' اکونت نمبر '. $account->id. " مبلغ " . $request->amount . ' ' . $request->currency . ' ' . $request->type . 'کرد' ;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->to('/dashboard/new-different-account/' . $request->account_id)->with('status', ' رسید پول موفقانه بروز شد !');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NewDifferentAccountPayment  $newDifferentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = NewDifferentAccountPayment::find($id);

            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Misc Payment Deleted');
            }

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
