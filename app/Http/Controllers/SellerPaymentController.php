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

    private function postPaymentToAccounting($payment, $overrides = [])
    {
        try {
            $condition = $payment->type; // 'رسید' or 'گرفت'
            
            // FORENSIC RULE: Pass original_amount + currency_code so AccountingService
            // performs the USD conversion exactly once (base_amount is already converted,
            // passing it with a non-USD currency_code causes a double-conversion).
            $amount = $payment->original_amount;

            $this->accountingService->postAutoTransaction('seller_payment', $condition, [
                'date' => $payment->date,
                'amount' => $amount,
                'currency_code' => $payment->currency_code,
                'exchange_rate' => $payment->exchange_rate,
                'party_type' => 'App\StringSeller',
                'party_id' => $payment->seller_id,
                'reference' => 'V-PAY-' . $payment->id,
                'description' => $payment->description,
                'source_id' => $payment->id,
                'override_debit_account_id' => $overrides['override_debit_account_id'] ?? $payment->override_debit_account_id ?? null,
                'override_credit_account_id' => $overrides['override_credit_account_id'] ?? $payment->override_credit_account_id ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Vendor Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function request_list()
    {
        $requests = SellerPayment::where('status', 0)->orderBy('id', 'DESC')->get()->map(function($req) {
            // 1. Fetch Supplier Name and Balance
            $seller = StringSeller::find($req->seller_id);
            if ($seller) {
                $req->seller_name = $seller->name;
                $req->current_balance = DB::table('ledger_entries')
                    ->where('party_type', 'App\StringSeller')
                    ->where('party_id', $req->seller_id)
                    ->sum(DB::raw("credit - debit"));
            }

            // 2. Prepare Accounting Preview
            $rule = \App\MappingRule::where('transaction_type', 'seller_payment')
                ->where(function($q) use ($req) {
                    $q->where('mapping_key', $req->type)->orWhere('condition', $req->type);
                })->first();

            if ($rule) {
                $debitAccId = $req->override_debit_account_id ?? $rule->debit_account_id;
                $creditAccId = $req->override_credit_account_id ?? $rule->credit_account_id;
                
                $req->debit_account_name = DB::table('chart_of_accounts')->where('id', $debitAccId)->value('account_name');
                $req->credit_account_name = DB::table('chart_of_accounts')->where('id', $creditAccId)->value('account_name');
            }

            return $req;
        });

        return view('string-seller.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = SellerPayment::find($id);
            $payment->status = 1; // Approved
            $payment->update();
            
            // Post to Accounting
            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = ($payment->amount > 0) 
                ? " مبلغ " . $payment->amount . " دالر برای فروشنده مواد اپروف شد "
                : " مبلغ " . $payment->amount_af . " افغانی برای فروشنده مواد اپروف شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id){
        $payment = SellerPayment::find($id);
        $payment->delete();
        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
                'seller_id' => 'required',
            ]);

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;

            // FORENSIC RULE: BCMath Calculation
            $baseAmount = bcmul($request->amount, $rate, 4);

            $payed = new SellerPayment();
            $payed->seller_id = $request->seller_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = (string)$rate;
            $payed->purchase_number = $request->purchase_number;
            
            // FORENSIC SNAPSHOTS
            $payed->currency_code = $currency->code;
            $payed->currency_symbol = $currency->symbol;
            $payed->exchange_rate = $rate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = $baseAmount;

            // Legacy dual-amount logic
            if($currency->code == 'USD'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else if($currency->code == 'AFN') {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            } else {
                $payed->amount = $baseAmount;
                $payed->amount_af = 0;
            }

            $payed->status = (Auth::user()->role == 'SP') ? 1 : 0;
            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed);
            }

            $seller_name = DB::table('string_sellers')->where('id', $request->seller_id)->first();
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " پرداخت به فروشنده مواد " . $seller_name->name . " اکونت نمبر " . $seller_name->id . " به مبلغ " . $request->amount . " " . $currency->code;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'موفقانه ثبت شد و در دفتر روزنامچه درج گردید!');
        });
    }

    public function show($seller_id)
    {
        $seller = StringSeller::find($seller_id);
        if (!$seller) {
            return redirect('/dashboard/string-seller')->with('error', 'فروشنده یافت نشد (Seller not found).');
        }

        $payments = SellerPayment::where('seller_id',$seller_id)->orderBy('date','DESC')->paginate(30);
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = SellerPayment::where('seller_id', $seller_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = SellerPayment::where('seller_id', $seller_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = SellerPayment::where('seller_id', $seller_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$seller_id)->distinct()->get(['purchase_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','purchase_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
    }
    public function show_all_payment($seller_id){
        $seller = StringSeller::find($seller_id);
        if (!$seller) {
            return redirect('/dashboard/string-seller')->with('error', 'فروشنده یافت نشد (Seller not found).');
        }

        $payments = SellerPayment::where('seller_id',$seller_id)->orderBy('date','DESC')->get();
        
        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = SellerPayment::where('seller_id', $seller_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = SellerPayment::where('seller_id', $seller_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = SellerPayment::where('seller_id', $seller_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $paymentEdit = '';
        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$seller_id)->distinct()->get(['purchase_number']);
        $currencies = \App\Currency::where('is_active', true)->get();

        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('PYMT_OUT', 'credit');
        $mapping = \App\MappingRule::where('mapping_key', 'PYMT_OUT')->first();

        $all = 'true';
        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','purchase_numbers','all', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
    }
    public function edit($payment_id)
    {
        $paymentEdit = SellerPayment::find($payment_id);
        $seller = StringSeller::find($paymentEdit->seller_id);
        $payments = SellerPayment::where('seller_id',$paymentEdit->seller_id)->orderBy('date','DESC')->paginate(30);

        // FORENSIC DYNAMIC TOTALS
        $currencyTotals = SellerPayment::where('seller_id', $paymentEdit->seller_id)
            ->where('status', 1)
            ->select('currency_code', 
                \DB::raw("SUM(CASE WHEN type = 'رسید' THEN original_amount ELSE 0 END) as total_received"),
                \DB::raw("SUM(CASE WHEN type = 'گرفت' THEN original_amount ELSE 0 END) as total_sent")
            )
            ->groupBy('currency_code')
            ->get()
            ->keyBy('currency_code');

        // Total in Base Currency (USD)
        $totalBaseReceived = SellerPayment::where('seller_id', $paymentEdit->seller_id)->where('status', 1)->where('type', 'رسید')->sum('base_amount');
        $totalBaseSent = SellerPayment::where('seller_id', $paymentEdit->seller_id)->where('status', 1)->where('type', 'گرفت')->sum('base_amount');

        $purchase_numbers = PurchaseMaterial::where('seller_id','=',$paymentEdit->seller_id)->distinct()->get(['purchase_number']);
        $currencies = \App\Currency::where('is_active', true)->get();
        
        $selectionService = new \App\Services\AccountSelectionService();
        $mKey = ($paymentEdit->type == 'رسید') ? 'PYMT_IN' : 'PYMT_OUT';
        $allowedDebitAccounts = $selectionService->getValidAccounts($mKey, 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts($mKey, 'credit');
        $mapping = \App\MappingRule::where('mapping_key', $mKey)->first();

        return view('string-seller.seller-payment',compact('seller','payments','paymentEdit','currencyTotals', 'totalBaseReceived', 'totalBaseSent','purchase_numbers', 'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'));
    }

    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required',
                'date' => 'required|date',
            ]);

            $payed = SellerPayment::find($payment_id);
            $seller_name = DB::table('string_sellers')->where('id', $request->seller_id)->first();

            // Reverse Old Accounting Entries (Only if it was approved)
            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Vendor Payment Edited');
            }

            $currency = \App\Currency::find($request->currency_id);
            $rate = $currency->exchange_rate;
            $baseAmount = bcmul($request->amount, $rate, 4);

            // Update record
            $payed->seller_id = $request->seller_id;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->dollar_rate = (string)$rate;
            $payed->purchase_number = $request->purchase_number;

            // FORENSIC SNAPSHOTS
            $payed->currency_code = $currency->code;
            $payed->currency_symbol = $currency->symbol;
            $payed->exchange_rate = $rate;
            $payed->original_amount = $request->amount;
            $payed->base_amount = $baseAmount;

            // Legacy dual-amount logic
            if($currency->code == 'USD'){
                $payed->amount = $request->amount;
                $payed->amount_af = 0;
            } else if($currency->code == 'AFN') {
                $payed->amount = 0;
                $payed->amount_af = $request->amount;
            } else {
                $payed->amount = $baseAmount;
                $payed->amount_af = 0;
            }

            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;
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
