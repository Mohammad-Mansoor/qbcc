<?php

namespace App\Http\Controllers;

use App\Activity;
use App\MaterialAccount;
use App\MaterialAccountPayment;
use App\MaterialType;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialAccountPaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postPaymentToAccounting($payment, $request = null)
    {
        try {
            $txType = ($payment->type === 'گرفت') ? 'material_payment_out' : 'material_payment_in';
            $mappingKey = ($payment->type === 'گرفت') ? 'MATERIAL_PAYMENT' : 'MATERIAL_RECEIPT';

            $this->accountingService->postAutoTransaction(
                $txType,
                $mappingKey,
                [
                    'date' => $payment->date,
                    'amount' => $payment->amount,
                    'party_type' => 'App\MaterialAccount',
                    'party_id' => $payment->account_id,
                    'reference' => 'MAP-' . $payment->id,
                    'description' => $payment->description,
                    'source_type' => get_class($payment),
                    'source_id' => $payment->id,
                    'override_debit_account_id' => $request ? $request->override_debit_account_id : null,
                    'override_credit_account_id' => $request ? $request->override_credit_account_id : null,
                ]
            );
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Material Payment #" . $payment->id . ": " . $e->getMessage());
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
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'type' => 'required',
            'type_id' => 'required',
            'account_id' => 'required',
        ]);

        $payed = new MaterialAccountPayment();
        $payed->amount = $request->amount;
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

        return view('material-accounts.account-payment', compact(
            'account', 'payments', 'paymentEdit', 'material_type', 'debits', 'credits',
            'allowedDebitAccountsOut', 'allowedCreditAccountsOut', 'mappingOut',
            'allowedDebitAccountsIn', 'allowedCreditAccountsIn', 'mappingIn'
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
            $data = $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'type' => '',
                'type_id' => '',
                'account_id' => '',
            ]);

            $payed = MaterialAccountPayment::find($payment_id);

            if ($payed->status == 1) {
                $this->accountingService->reverseTransactionBySource($payed->id, 'Material Payment Edited', get_class($payed));
            }

            $payed->amount = $request->amount;
            $payed->description = $request->description;
            $payed->date = $request->date;
            $payed->type = $request->type;
            $payed->type_id = $request->type_id;
            $payed->account_id = $request->account_id;
            $payed->update();

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

            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Material Payment Deleted', get_class($payment));
            }

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
