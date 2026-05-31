<?php

namespace App\Http\Controllers;

use App\Activity;
use App\DifferentAccount;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DifferentAccountPaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postPaymentToAccounting($payment)
    {
        try {
            $account = DifferentAccount::find($payment->account_id);
            $key = ($payment->type == 'رسید') ? 'DIFF_IN' : 'DIFF_OUT';

            $transaction = $this->accountingService->postAutoTransaction('different_account', $key, [
                'date' => $payment->date,
                'amount' => $payment->amount,
                'original_amount' => $payment->amount,
                'currency_code' => $payment->currency_code ?: 'AFN',
                'exchange_rate' => $payment->exchange_rate ?: 1,
                'reference' => 'DIFF-' . $payment->id,
                'description' => "تراکنش حساب متفرقه: " . ($account->name ?? 'N/A') . " - " . $payment->description,
                'source_type' => 'DifferentAccountPayment',
                'source_id' => $payment->id,
                'override_debit_account_id' => $payment->override_debit_account_id ?? null,
                'override_credit_account_id' => $payment->override_credit_account_id ?? null,
            ]);

            if ($transaction) {
                DB::table('different_account_payments')
                    ->where('id', $payment->id)
                    ->update(['ledger_transaction_id' => $transaction->id]);
            }
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Different Account Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    private function recalculateTotals($accountId)
    {
        $totals = DB::table('different_account_payments')
            ->select('currency_code', 
                DB::raw("SUM(CASE WHEN type = 'رسید' THEN amount ELSE 0 END) as total_receipts"),
                DB::raw("SUM(CASE WHEN type = 'گرفت' THEN amount ELSE 0 END) as total_payments")
            )
            ->where('account_id', $accountId)
            ->groupBy('currency_code')
            ->get();

        DifferentAccountTotal::where('account_id', $accountId)->delete();

        foreach ($totals as $t) {
            DifferentAccountTotal::create([
                'account_id' => $accountId,
                'currency_code' => $t->currency_code,
                'total' => $t->total_receipts,
                'paid' => $t->total_payments,
                'remaining' => $t->total_receipts - $t->total_payments
            ]);
        }
    }

    public function money_request()
    {
        $requests = DifferentAccountPayment::with('account')->where('status', 0)->orderBy('id', 'DESC')->get();
        return view('different-account.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = DifferentAccountPayment::find($id);
            $payment->status = 1;
            $payment->update();

            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . $payment->amount . " " . $payment->currency_code . " توسط سوپر ادمین تایید و در سیستم مالی ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $payment = DifferentAccountPayment::find($id);
        $payment->delete();
        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validate([
                'amount' => 'required|numeric',
                'currency_code' => 'required',
                'exchange_rate' => 'required|numeric|min:0.000001',
                'description' => 'required',
                'date' => 'required',
                'type' => 'required',
                'account_id' => 'required',
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
            ]);

            $currency = \App\Currency::where('code', $data['currency_code'])->firstOrFail();
            $exchangeRate = $request->exchange_rate ?? $currency->exchange_rate;
            $data['exchange_rate'] = ($data['currency_code'] == 'USD') ? 1.000000 : $exchangeRate;

            // FORENSIC PILLAR 5: Multiplication for USD Normalization
            $data['base_amount'] = bcmul($data['amount'], $data['exchange_rate'], 4);
            $data['status'] = (Auth::user()->role == 'SP') ? 1 : 0;
            
            $payment = DifferentAccountPayment::create($data);

            $this->recalculateTotals($request->account_id);

            if ($payment->status == 1) {
                $this->postPaymentToAccounting($payment);
            }

            $account = DifferentAccount::find($request->account_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " حساب متفرقه: " . $account->name . " مبلغ " . $request->amount . " " . $request->currency_code . " " . $request->type . " ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'تراکنش با موفقیت ثبت و در سیستم مالی درج گردید!');
        });
    }

    public function edit($id)
    {
        $paymentEdit = DifferentAccountPayment::find($id);
        $payments = DifferentAccountPayment::where('account_id', $paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = DifferentAccount::find($paymentEdit->account_id);
        $totals = DifferentAccountTotal::where('account_id', $paymentEdit->account_id)->get();
        $currencies = \App\Currency::all();
        $chartOfAccounts = \App\ChartOfAccount::orderBy('account_code')->get();
        $mappingIn = \App\MappingRule::where('mapping_key', 'DIFF_IN')->first();
        $mappingOut = \App\MappingRule::where('mapping_key', 'DIFF_OUT')->first();
        return view('different-account.account-payment', compact('account', 'payments', 'totals', 'paymentEdit', 'currencies', 'chartOfAccounts', 'mappingIn', 'mappingOut'));
    }

    public function update(Request $request, DifferentAccountPayment $differentAccountPayment)
    {
        return DB::transaction(function () use ($request, $differentAccountPayment) {
            $data = $request->validate([
                'amount' => 'required|numeric',
                'currency_code' => 'required',
                'exchange_rate' => 'required|numeric|min:0.000001',
                'description' => 'required',
                'date' => 'required',
                'type' => 'required',
                'account_id' => 'required',
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id',
            ]);

            $currency = \App\Currency::where('code', $data['currency_code'])->firstOrFail();
            $exchangeRate = $request->exchange_rate ?? $currency->exchange_rate;
            $data['exchange_rate'] = ($data['currency_code'] == 'USD') ? 1.000000 : $exchangeRate;

            // FORENSIC PILLAR 5: Multiplication for USD Normalization
            $data['base_amount'] = bcmul($data['amount'], $data['exchange_rate'], 4);

            if ($differentAccountPayment->status == 1) {
                $this->accountingService->reverseTransactionBySource($differentAccountPayment->id, 'Different Account Record Edited');
            }

            $differentAccountPayment->update($data);

            $this->recalculateTotals($request->account_id);

            if ($differentAccountPayment->status == 1) {
                $this->postPaymentToAccounting($differentAccountPayment);
            }

            $account = DifferentAccount::find($request->account_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " ویرایش تراکنش حساب متفرقه: " . $account->name . " به مبلغ " . $request->amount . " " . $request->currency_code;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->to('/dashboard/different-account/' . $request->account_id)->with('status', 'تراکنش با موفقیت بروزرسانی شد!');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = DifferentAccountPayment::find($id);
            $accountId = $payment->account_id;

            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Different Account Record Deleted');
            }

            $payment->delete();
            $this->recalculateTotals($accountId);

            return response()->json(['status' => 'success']);
        });
    }
}
