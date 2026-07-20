<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialAccount;
use App\MaterialAccountPayment;
use App\MaterialType;
use App\Services\AccountingService;
use App\Services\InventoryTransactionManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialAccountPaymentController extends Controller
{
    protected $accountingService;
    protected $inventoryManager;

    public function __construct(AccountingService $accountingService, InventoryTransactionManager $inventoryManager)
    {
        $this->accountingService = $accountingService;
        $this->inventoryManager = $inventoryManager;
    }

    private function postPaymentToAccounting($payment, $request = null)
    {
        try {
            $txType = ($payment->type === 'گرفت') ? 'material_payment_out' : 'material_payment_in';
            $mappingKey = ($payment->type === 'گرفت') ? 'MATERIAL_PAYMENT' : 'MATERIAL_RECEIPT';

            // 1. Post General Ledger Transaction in original currency
            $this->accountingService->postAutoTransaction(
                $txType,
                $mappingKey,
                [
                    'date' => $payment->date,
                    'amount' => $payment->original_amount ?? 0,
                    'currency_code' => $payment->currency_code,
                    'exchange_rate' => $payment->exchange_rate,
                    'party_type' => 'App\MaterialAccount',
                    'party_id' => $payment->account_id,
                    'reference' => 'MAP-' . $payment->id,
                    'description' => $payment->description,
                    'source_type' => get_class($payment),
                    'source_id' => $payment->id,
                    'override_debit_account_id' => $payment->override_debit_account_id,
                    'override_credit_account_id' => $payment->override_credit_account_id,
                ]
            );

            // 2. Record Physical Inventory Movement
            $this->inventoryManager->processGenericMovement([
                'item_model' => $payment,
                'type' => ($payment->type === 'گرفت' ? 'PROD_ISSUE' : 'PURCHASE'),
                'direction' => ($payment->type === 'گرفت' ? 'OUT' : 'IN'),
                'warehouse_id' => $payment->warehouse_id ?? 1,
                'quantity' => $payment->amount, // physical KG
                'unit_cost' => $payment->price ?? 0, // unit cost in original currency
                'currency_code' => $payment->currency_code ?? 'AFN',
                'exchange_rate' => $payment->exchange_rate ?? 1.0,
                'created_by' => auth()->id() ?? 1,
            ]);

        } catch (\Exception $e) {
            \Log::error("Accounting/Inventory posting failed for Material Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function request_material()
    {
        $requests = MaterialAccountPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('material-accounts.requested-material-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = MaterialAccountPayment::find($id);

            $payment->status = 1;
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مقدار " . $payment->amount . "کیلوگرام توسط سوپر ادمین اپروف شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $credit = MaterialAccountPayment::find($id);
        $credit->delete();
        return response()->json(['status', 'error']);
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
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required|numeric|gt:0',
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'required',
            'date' => 'required|date',
            'type' => 'required',
            'type_id' => 'required',
            'account_id' => 'required',
        ]);

        $payed = new MaterialAccountPayment();
        $payed->amount = $request->amount;
        $payed->price = $request->price;
        $payed->currency_id = $request->currency_id;

        $currency = \App\Currency::findOrFail($request->currency_id);
        $payed->currency_code = $currency->code;
        $payed->exchange_rate = $request->exchange_rate;

        // Calculate original and base amounts
        $originalAmount = bcmul($request->amount, $request->price, 4);
        $payed->original_amount = $originalAmount;
        $payed->base_currency_amount = bcmul($originalAmount, $request->exchange_rate, 4);

        $payed->override_debit_account_id = $request->override_debit_account_id;
        $payed->override_credit_account_id = $request->override_credit_account_id;
        $payed->warehouse_id = $request->warehouse_id;

        $payed->description = $request->description;
        $payed->date = $request->date;
        $payed->type = $request->type;
        $payed->type_id = $request->type_id;
        $payed->account_id = $request->account_id;

        if (Auth::user()->role == 'SP'){
            $payed->status = 1;
        }
        else{
            $payed->status = 0;
        }

        return DB::transaction(function () use ($payed, $request) {
            $payed->save();

            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed, $request);
            }

            return redirect()->back()->with('status', 'موفقانه ثبت شد !');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialAccountPayment $materialAccountPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $paymentEdit = MaterialAccountPayment::find($payment_id);

        $payments = MaterialAccountPayment::where('account_id',$paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = MaterialAccount::find($paymentEdit->account_id);

        $debits = MaterialAccountPayment::where('type','=','گرفت')->where('account_id',$paymentEdit->account_id)->where('status',1)->sum('amount');
        $credits = MaterialAccountPayment::where('type','=','رسید')->where('account_id',$paymentEdit->account_id)->where('status',1)->sum('amount');
        $material_type = MaterialType::all();

        $selectionService = new \App\Services\AccountSelectionService();
        
        // For Payment OUT (گرفت)
        $allowedDebitAccountsOut = $selectionService->getValidAccounts('MATERIAL_PAYMENT', 'debit');
        $allowedCreditAccountsOut = $selectionService->getValidAccounts('MATERIAL_PAYMENT', 'credit');
        $mappingOut = \App\MappingRule::where('mapping_key', 'MATERIAL_PAYMENT')->first();

        // For Receipt IN (رسید)
        $allowedDebitAccountsIn = $selectionService->getValidAccounts('MATERIAL_RECEIPT', 'debit');
        $allowedCreditAccountsIn = $selectionService->getValidAccounts('MATERIAL_RECEIPT', 'credit');
        $mappingIn = \App\MappingRule::where('mapping_key', 'MATERIAL_RECEIPT')->first();

        $warehouses = \App\Warehouse::all();
        $currencies = \App\Currency::where('is_active', 1)->get();
        $baseCurrency = \App\Currency::where('is_base_currency', 1)->first();

        return view('material-accounts.account-payment', compact(
            'account', 'payments', 'paymentEdit', 'material_type', 'debits', 'credits',
            'allowedDebitAccountsOut', 'allowedCreditAccountsOut', 'mappingOut',
            'allowedDebitAccountsIn', 'allowedCreditAccountsIn', 'mappingIn',
            'warehouses', 'currencies', 'baseCurrency'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        return DB::transaction(function () use ($request, $payment_id) {
            $request->validate([
                'amount' => 'required|numeric|gt:0',
                'price' => 'required|numeric|min:0',
                'currency_id' => 'required|exists:currencies,id',
                'exchange_rate' => 'required|numeric|gt:0',
                'warehouse_id' => 'required|exists:warehouses,id',
                'description' => 'required',
                'date' => 'required|date',
                'type' => 'required',
                'type_id' => 'required',
                'account_id' => 'required',
            ]);

            $payed = MaterialAccountPayment::find($payment_id);

            // Revert previous posting if it was approved
            if ($payed->status == 1) {
                $this->inventoryManager->reverseTransactions($payed, 'Material Payment Edited');
            }

            $payed->amount = $request->amount;
            $payed->price = $request->price;
            $payed->currency_id = $request->currency_id;

            $currency = \App\Currency::findOrFail($request->currency_id);
            $payed->currency_code = $currency->code;
            $payed->exchange_rate = $request->exchange_rate;

            // Calculate original and base amounts
            $originalAmount = bcmul($request->amount, $request->price, 4);
            $payed->original_amount = $originalAmount;
            $payed->base_currency_amount = bcmul($originalAmount, $request->exchange_rate, 4);

            $payed->override_debit_account_id = $request->override_debit_account_id;
            $payed->override_credit_account_id = $request->override_credit_account_id;
            $payed->warehouse_id = $request->warehouse_id;

            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->type_id = $request->type_id;
            $payed->account_id = $request->account_id;
            $payed->update();

            // Re-post if approved
            if ($payed->status == 1) {
                $this->postPaymentToAccounting($payed, $request);
            }

            return redirect('/dashboard/material-accounts/'.$request->account_id)->with('status', 'موفقانه ویرایش و بروزرسانی شد !');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterialAccountPayment $materialAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = MaterialAccountPayment::find($id);

            // Revert previous posting if it was approved
            if ($payment->status == 1) {
                $this->inventoryManager->reverseTransactions($payment, 'Material Payment Deleted');
            }

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
