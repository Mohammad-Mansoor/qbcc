<?php

namespace App\Http\Controllers;

use App\Activity;
use App\DifferentAccount;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\OfficeCashBook;
use App\OfficeCredit;
use App\OfficeDebit;
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
            $this->accountingService->postAutoTransaction('different_account', $payment->type, [
                'date' => $payment->date,
                'amount' => $payment->amount,
                'reference' => 'DIFF-' . $payment->id,
                'description' => "تراکنش حساب متفرقه: " . ($account->name ?? 'N/A') . " - " . $payment->description,
                'source_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Different Account Payment #" . $payment->id . ": " . $e->getMessage());
        }
    }

    public function money_request()
    {
        $requests = DifferentAccountPayment::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('different-account.requested-money-list', compact('requests'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = DifferentAccountPayment::find($id);
            $payment->status = 1;
            $payment->update();

            // Accounting Posting
            $this->postPaymentToAccounting($payment);

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . $payment->amount . " دالر توسط سوپر ادمین تایید و در سیستم مالی ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id)
    {
        $payment = DifferentAccountPayment::find($id);
        $payment->delete();
        return response()->json(['status', 'error']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'type' => 'required',
                'account_id' => 'required',
            ]);

            $data['status'] = (Auth::user()->role == 'SP') ? 1 : 0;
            
            $payment = DifferentAccountPayment::create($data);

            // Update Totals
            $totalUpdate = DifferentAccountTotal::firstOrNew(['account_id' => $request->account_id]);
            if ($request->type == 'گرفت') {
                $totalUpdate->paid += $request->amount;
            } else {
                $totalUpdate->total += $request->amount;
            }
            $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
            $totalUpdate->save();

            if ($payment->status == 1) {
                $this->postPaymentToAccounting($payment);
            }

            $account = DifferentAccount::find($request->account_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " حساب متفرقه: " . $account->name . " مبلغ " . $request->amount . " " . $request->type . " ثبت شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'تراکنش با موفقیت ثبت و در سیستم مالی درج گردید!');
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paymentEdit = DifferentAccountPayment::find($id);
        $debits = DifferentAccountPayment::where('type', '=', 'گرفت')->where('account_id', $paymentEdit->account_id)->where('status',1)->sum('amount');
        $credits = DifferentAccountPayment::where('type', '=', 'رسید')->where('account_id', $paymentEdit->account_id)->where('status',1)->sum('amount');
        $payments = DifferentAccountPayment::where('account_id', $paymentEdit->account_id)->orderBy('created_at','DESC')->paginate(30);
        $account = DifferentAccount::find($paymentEdit->account_id);
        $total = DifferentAccountTotal::where('account_id', $paymentEdit->account_id)->sum('total');
        return view('different-account.account-payment', compact('account', 'payments', 'total', 'debits', 'credits', 'paymentEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DifferentAccountPayment $differentAccountPayment)
    {
        return DB::transaction(function () use ($request, $differentAccountPayment) {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'date' => 'required',
                'type' => 'required',
                'account_id' => 'required'
            ]);

            // Reversal
            if ($differentAccountPayment->status == 1) {
                $this->accountingService->reverseTransactionBySource($differentAccountPayment->id, 'Different Account Record Edited');
            }

            // Update Totals (subtract old)
            $totalUpdate = DifferentAccountTotal::where('account_id', $request->account_id)->first();
            if ($differentAccountPayment->type == 'گرفت') {
                $totalUpdate->paid -= $differentAccountPayment->amount;
            } else {
                $totalUpdate->total -= $differentAccountPayment->amount;
            }

            // Update record
            $differentAccountPayment->update($request->all());

            // Add new amounts to totals
            if ($request->type == 'گرفت') {
                $totalUpdate->paid += $request->amount;
            } else {
                $totalUpdate->total += $request->amount;
            }
            $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
            $totalUpdate->update();

            // Re-post if approved
            if ($differentAccountPayment->status == 1) {
                $this->postPaymentToAccounting($differentAccountPayment);
            }

            $account = DifferentAccount::find($request->account_id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " ویرایش تراکنش حساب متفرقه: " . $account->name . " به مبلغ " . $request->amount;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->to('/dashboard/different-account/' . $request->account_id)->with('status', 'تراکنش با موفقیت بروزرسانی شد!');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DifferentAccountPayment $differentAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = DifferentAccountPayment::find($id);

            // Reversal
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Different Account Record Deleted');
            }

            // Update Totals
            $totalUpdate = DifferentAccountTotal::where('account_id', $payment->account_id)->first();
            if ($payment->type == 'گرفت') {
                $totalUpdate->paid -= $payment->amount;
            } else {
                $totalUpdate->total -= $payment->amount;
            }
            $totalUpdate->remaining = $totalUpdate->total - $totalUpdate->paid;
            $totalUpdate->update();

            $payment->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
