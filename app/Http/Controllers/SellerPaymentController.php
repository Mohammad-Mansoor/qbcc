<?php

namespace App\Http\Controllers;

use App\Activity;
use App\PurchaseMaterial;
use App\SellerPayment;
use App\StringSeller;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

            $this->accountingService->postAutoTransaction('seller_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'party_type' => 'App\StringSeller',
                'party_id' => $payment->seller_id,
                'reference' => 'V-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Vendor Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function request_list()
    {
        $requests = SellerPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('string-seller.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = SellerPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0) 
                ? " مبلغ " . $payment->amount . "دالر برای فروشنده مواد تایید شد "
                : " مبلغ " . $payment->amount_af . "افغانی برای فروشنده مواد تایید شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }
    public function delete_request($id){
        $credit = SellerPayment::find($id);
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
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'seller_id' => 'required',
            ]);

            $payed = new SellerPayment();
            $payed->seller_id = $request->seller_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->purchase_number = $request->purchase_number;
            
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

            $seller_name = DB::table('string_sellers')->where('id', $request->seller_id)->first();
            $currency = ($request->money_type == 'دالر') ? " دالر " : " افغانی ";

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت به فروشنده مواد " . $seller_name->name . " اکونت نمبر " . $seller_name->id . " به مبلغ " . $request->amount . $currency;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد و در دفتر روزنامچه درج گردید!');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function show($seller_id)
    {
        $payments = SellerPayment::where('seller_id',$seller_id)->orderBy('created_at','DESC')->paginate(30);
        $seller = StringSeller::find($seller_id);
        $debits_us = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $debits_af = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $credit_us = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $credit_af = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$seller_id)->distinct()->get(['purchase_number']);
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','purchase_numbers'));
    }
    public function show_all_payment($seller_id){
        $payments = SellerPayment::where('seller_id',$seller_id)->orderBy('created_at','DESC')->get();
        $seller = StringSeller::find($seller_id);
        $debits_us = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $debits_af = SellerPayment::where('type','=','گرفت')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $credit_us = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount');
        $credit_af = SellerPayment::where('type','=','رسید')->where('seller_id',$seller_id)->where('status',1)->sum('amount_af');
        $paymentEdit = '';
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$seller_id)->distinct()->get(['purchase_number']);
        $all = '';
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','purchase_numbers','all'));

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = SellerPayment::find($payment_id);
        $payments = SellerPayment::where('seller_id',$paymentEdit->seller_id)->orderBy('created_at','DESC')->paginate(30);
        $seller = StringSeller::find($paymentEdit->seller_id);
        $debits_us = SellerPayment::where('type','=','گرفت')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount');
        $debits_af = SellerPayment::where('type','=','گرفت')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount_af');
        $credit_us = SellerPayment::where('type','=','رسید')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount');
        $credit_af = SellerPayment::where('type','=','رسید')->where('seller_id',$paymentEdit->seller_id)->where('status',1)->sum('amount_af');
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$paymentEdit->seller_id)->distinct()->get(['purchase_number']);
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','debits_us','debits_af','credit_af','credit_us','purchase_numbers'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
            ]);

            $payed = SellerPayment::find($payment_id);
            $seller_name = DB::table('string_sellers')->where('id', $request->seller_id)->first();

            // Reverse Old Accounting Entries (Only if it was approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Vendor Payment Edited');
            }

            // Update record
            $payed->seller_id = $request->seller_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = $request->dollar_rate;
            $payed->purchase_number = $request->purchase_number;

            if($request->money_type == 'دالر'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            }
            $payed->update();

            // Post New Accounting Entry (Only if approved)
            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ویرایش پرداخت به فروشنده مواد " . $seller_name->name . " اکونت نمبر " . $seller_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect('/dashboard/string-seller-payments/'.$request->seller_id)->with('status', 'ویرایش موفقانه انجام شد و حسابات بروزرسانی گردید!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerPayment  $sellerPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = SellerPayment::find($id);
            $seller_name = DB::table('string_sellers')->where('id', $payment->seller_id)->first();

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Vendor Payment Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "حذف پرداخت فروشنده مواد " . $seller_name->name . " اکونت نمبر " . $seller_name->id;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
