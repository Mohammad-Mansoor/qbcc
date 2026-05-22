<?php

namespace App\Http\Controllers;

use App\Activity;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\OfficeCredit;
use App\OfficeCashBook;
use App\OfficeDebit;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeCreditController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postCreditToAccounting($credit)
    {
        try {
            $amount = $credit->base_amount ?: $credit->amount;
            $this->accountingService->postAutoTransaction('office_credit', 'deposit', [
                'date' => $credit->date,
                'amount' => $amount,
                'currency_code' => $credit->currency_code,
                'exchange_rate' => $credit->exchange_rate,
                'reference' => 'OFF-CRED-' . $credit->id,
                'description' => 'تزریق سرمایه به دخل (Cash Injection): ' . $credit->description,
                'source_id' => $credit->id,
            ]);
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Office Credit #" . $credit->id . ": " . $e->getMessage());
        }
    }

    public function index()
    {
        $creditEdit = "";
        $center_credits = OfficeCredit::where('user_role','CO')->orWhere('user_role','CCO')->orderBy('id', 'DESC')->get();
        $froshat_credits = OfficeCredit::where('user_role','SO')->orWhere('user_role','SCO')->orderBy('id', 'DESC')->get();
        $sp_credits = OfficeCredit::where('user_role','SP')->orderBy('id', 'DESC')->get();

        $other_user = OfficeCredit::where('user_role', '!=', 'SP')->where('payment_id', null)->where('customer_id', null)->where('status','!=',0)->sum('amount');
        
        $center_total = OfficeCredit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_total = OfficeCredit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_total = OfficeCredit::where('user_role', '=', 'SP')->sum('amount');

        $cashbook = OfficeCashBook::count();
        $cash = '';

        $center_debits = OfficeDebit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_debits = OfficeDebit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_debits = OfficeDebit::where('user_role', '=', 'SP')->sum('amount');

        $so_cashbook = OfficeCashBook::where('user_role','SO')->orWhere('user_role','SCO')->sum('balance');
        $co_cashbook = OfficeCashBook::where('user_role','CO')->orWhere('user_role','CCO')->sum('balance');

        if ($cashbook > 0) {
            $cash = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');
        }
        $currencies = \App\Currency::where('is_active', true)->get();
        return view('office-cash-book.add-credit', compact('center_credits','froshat_credits','sp_credits','center_total','froshat_total','sp_total','center_debits','froshat_debits','sp_debits', 'creditEdit', 'cash', 'other_user', 'so_cashbook', 'co_cashbook', 'currencies'));
    }

    public function money_request()
    {
        $credits = OfficeCredit::where('user_role', '!=', 'SP')->where('status', 0)->orderBy('id', 'DESC')->get();
        return view('office-cash-book.requested-money-list', compact('credits'));
    }

    public function approve_request($id)
    {
        return DB::transaction(function () use ($id) {
            $credit = OfficeCredit::find($id);
            $this->accountingService->failIfLocked($credit->date);

            $sp_cashbook = OfficeCashBook::where('user_role','SP')->first();
            $user_cashbook = OfficeCashBook::where('user_role',$credit->user_role)->first();

            if ($sp_cashbook->balance < $credit->amount){
                return response()->json(['status' => 'error']);
            }
            else{
                $sp_cashbook->balance = $sp_cashbook->balance - $credit->amount;
                $sp_cashbook->update();
                $user_cashbook->balance = $user_cashbook->balance + $credit->amount;
                $user_cashbook->update();
                
                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مبلغ " . $credit->amount . " توسط سوپر ادمین اپروف شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();
            }

            $debit = new OfficeDebit();
            if ($credit->user_role == 'CO' || $credit->user_role == 'CCO'){
                $debit->name =  '  مصارف کاربر دفتر مرکزی ';
            }
            else{
                $debit->name =  '  مصارف کاربر دفتر فروشات ';
            }

            $debit->amount = $credit->amount;
            $debit->description = $credit->description;
            $debit->date = $credit->date;
            $debit->expense_type = 'مصرف دفاتر';
            $debit->expense_for_where = 'مصرف دفاتر';
            $debit->user_role = 'SP';
            $debit->credit_id = $id;

            // Copy multi-currency snapshot
            $debit->currency_id = $credit->currency_id;
            $debit->currency_code = $credit->currency_code;
            $debit->exchange_rate = $credit->exchange_rate;
            $debit->original_amount = $credit->original_amount;
            $debit->base_amount = $credit->base_amount;
            if ($credit->currency_code == 'AFN') {
                $debit->amount_af = $credit->amount;
                $debit->amount = 0;
            } else {
                $debit->amount = $credit->amount;
                $debit->amount_af = 0;
            }
            $debit->save();

            // Post double-entry transaction inside GL for the cash relocation
            $amount = $credit->base_amount ?: $credit->amount;
            $this->accountingService->postTransaction([
                'date' => $credit->date,
                'reference' => 'OFF-TRSF-' . $credit->id,
                'description' => "انتقال داخلی پول از صندوق عمومی به " . ($credit->user_role == 'CO' || $credit->user_role == 'CCO' ? 'دفتر مرکزی' : 'دفتر فروشات') . ": " . $credit->description,
                'source_type' => 'OfficeCredit',
                'source_id' => $credit->id,
                'journal_type' => 'journal',
                'entries' => [
                    [
                        'account_id' => 1, // Cash
                        'debit' => $amount,
                        'credit' => 0,
                        'currency_code' => $credit->currency_code ?: 'USD',
                        'exchange_rate' => $credit->exchange_rate,
                    ],
                    [
                        'account_id' => 1, // Cash
                        'debit' => 0,
                        'credit' => $amount,
                        'currency_code' => $credit->currency_code ?: 'USD',
                        'exchange_rate' => $credit->exchange_rate,
                    ]
                ]
            ]);

            $credit->status = 1 ;
            $credit->update();
            return response()->json(['status' => 'success']);
        });
    }

    public function delete_request($id){
        return DB::transaction(function () use ($id) {
            $credit = OfficeCredit::find($id);
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . $credit->amount . " که درخواست شده بود  توسط سوپر ادمین رد شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
            $credit->delete();
            return response()->json(['status','error']);
        });
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->accountingService->failIfLocked($request->date);
        return DB::transaction(function () use ($request) {
            $data = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required|min:3|max:256',
                'date' => 'required|date',
            ]);
            $data['user_role'] = Auth::user()->role;
            $data['status'] = 0;

            $currency = \App\Currency::find($request->currency_id);
            $rate = $request->exchange_rate ?: $currency->exchange_rate;
            $baseAmount = bcmul((string)$request->amount, (string)$rate, 4);

            $data['currency_id'] = $request->currency_id;
            $data['currency_code'] = $currency->code;
            $data['exchange_rate'] = $rate;
            $data['original_amount'] = $request->amount;
            $data['base_amount'] = $baseAmount;
            $data['amount'] = $request->amount;

            if (Auth::user()->role != 'SP') {
                $cashbook = OfficeCashBook::where('user_role', Auth::user()->role)->first();
                $sp_cashbook = OfficeCashBook::where('user_role', 'SP')->first();

                if (!$sp_cashbook) {
                    return back()->with('error', 'دخل عمومی هنوز ثبت نشده است !');
                } else {
                    if ($sp_cashbook->balance == 0) {
                        return back()->with('error', 'پول در دخل عمومی موجود نیست');
                    } else {
                        if ($request->amount > $sp_cashbook->balance) {
                            return back()->with('error', 'پول خواسته از پول دخل عمومی زیاد است ');
                        } else {
                            $credit = OfficeCredit::create($data);
                            if (!$cashbook) {
                                $cash = new OfficeCashBook();
                                $cash->balance = 0;
                                $cash->user_role = Auth::user()->role;
                                $cash->save();
                            }
                        }
                    }
                }
            } else {
                $cashbook = OfficeCashBook::where('user_role', Auth::user()->role)->first();
                if (!$cashbook) {
                    $cash = new OfficeCashBook();
                    $cash->balance = $request->amount;
                    $cash->user_role = Auth::user()->role;
                    $cash->save();
                } else {
                    $cashbook->balance = $cashbook->balance + $request->amount;
                    $cashbook->update();
                }
                $data['status'] = 1;
                $credit = OfficeCredit::create($data);
                
                // Accounting Posting (Only when SP adds generic cash)
                $this->postCreditToAccounting($credit);

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مبلغ " . $request->amount . "  دخل شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();
            }

            if ($credit) {
                if (Auth::user()->role == 'SP') {
                    return redirect('/dashboard/add-office-credit')->with('status', 'مقدار پول موفقانه در دخل و روزنامچه مالی ثبت شد!');
                } else {
                    return redirect('/dashboard/add-office-credit')->with('status', 'درخواست شما موفقانه ارسال شد تا تایید ان منتظر بمانید !');
                }
            } else {
                return redirect('/dashboard/add-office-credit')->with('error', 'مشکل در سرور وجود داره!');
            }
        });
    }

    public function show(OfficeCredit $officeCredit)
    {
        //
    }

    public function edit($id)
    {
        $creditEdit = OfficeCredit::find($id);
        
        $center_credits = OfficeCredit::where('user_role','CO')->orWhere('user_role','CCO')->orderBy('id', 'DESC')->get();
        $froshat_credits = OfficeCredit::where('user_role','SO')->orWhere('user_role','SCO')->orderBy('id', 'DESC')->get();
        $sp_credits = OfficeCredit::where('user_role','SP')->orderBy('id', 'DESC')->get();

        $other_user = OfficeCredit::where('user_role', '!=', 'SP')->where('payment_id', null)->where('customer_id', null)->where('status','!=',0)->sum('amount');

        $center_total = OfficeCredit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_total = OfficeCredit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_total = OfficeCredit::where('user_role', '=', 'SP')->sum('amount');

        $cashbook = OfficeCashBook::count();
        $cash = '';

        $center_debits = OfficeDebit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_debits = OfficeDebit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_debits = OfficeDebit::where('user_role', '=', 'SP')->sum('amount');
        
        $so_cashbook = OfficeCashBook::where('user_role','SO')->orWhere('user_role','SCO')->sum('balance');
        $co_cashbook = OfficeCashBook::where('user_role','CO')->orWhere('user_role','CCO')->sum('balance');
        
        if ($cashbook > 0) {
            $cash = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');
        }
        $currencies = \App\Currency::where('is_active', true)->get();
        return view('office-cash-book.add-credit', compact('center_credits','froshat_credits','sp_credits','center_total','froshat_total','sp_total','center_debits','froshat_debits','sp_debits', 'creditEdit', 'cash', 'other_user', 'so_cashbook', 'co_cashbook', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $this->accountingService->failIfLocked($request->date);
        return DB::transaction(function () use ($request, $id) {
            $credit = OfficeCredit::find($id);
            $sp = OfficeCashBook::where('user_role', 'SP')->first();

            if (Auth::user()->role == 'SP') {
                $sp->balance = $sp->balance - $credit->amount + $request->amount;
                $sp->update();
            }

            if ($sp->balance  < $request->amount){
                return back()->with('error', 'پول خواسته از پول دخل عمومی زیاد است ');
            } else {
                // Reversal
                if ($credit->status == 1) {
                    $this->accountingService->reverseTransactionBySource($id, 'Office Credit Edited');
                }

                $currency = \App\Currency::find($request->currency_id);
                $rate = $request->exchange_rate ?: $currency->exchange_rate;
                $baseAmount = bcmul((string)$request->amount, (string)$rate, 4);

                $credit->amount = $request->amount;
                $credit->description = $request->description;
                $credit->date = $request->date;

                $credit->currency_id = $request->currency_id;
                $credit->currency_code = $currency->code;
                $credit->exchange_rate = $rate;
                $credit->original_amount = $request->amount;
                $credit->base_amount = $baseAmount;

                $credit->update();
                
                // Repost
                if ($credit->status == 1) {
                    if ($credit->user_role == 'SP') {
                        $this->postCreditToAccounting($credit);
                    } else {
                        // Repost approved requested transfer to GL
                        $amount = $credit->base_amount ?: $credit->amount;
                        $this->accountingService->postTransaction([
                            'date' => $credit->date,
                            'reference' => 'OFF-TRSF-' . $credit->id,
                            'description' => "انتقال داخلی پول از صندوق عمومی به " . ($credit->user_role == 'CO' || $credit->user_role == 'CCO' ? 'دفتر مرکزی' : 'دفتر فروشات') . ": " . $credit->description,
                            'source_type' => 'OfficeCredit',
                            'source_id' => $credit->id,
                            'journal_type' => 'journal',
                            'entries' => [
                                [
                                    'account_id' => 1, // Cash
                                    'debit' => $amount,
                                    'credit' => 0,
                                    'currency_code' => $credit->currency_code ?: 'USD',
                                    'exchange_rate' => $credit->exchange_rate,
                                ],
                                [
                                    'account_id' => 1, // Cash
                                    'debit' => 0,
                                    'credit' => $amount,
                                    'currency_code' => $credit->currency_code ?: 'USD',
                                    'exchange_rate' => $credit->exchange_rate,
                                ]
                            ]
                        ]);
                    }
                }

                $activity = new Activity();
                $activity->date = Carbon::today()->format('Y-m-d');
                $activity->description = " مبلغ " . $request->amount . "  دخل شده ویرایش شد ";
                $activity->user_id = Auth::user()->id;
                $activity->save();
            }

            return redirect('/dashboard/add-office-credit')->with('status', 'موفقانه بروز و در روزنامچه ثبت گردید');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $credit = OfficeCredit::find($id);
            
            // Reversal
            if ($credit->status == 1) {
                $this->accountingService->reverseTransactionBySource($id, 'Office Credit Deleted');
            }

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . $credit->amount . "  دخل شده حذف شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();        
            
            $ca = OfficeCashBook::where('user_role', 'SP')->first();
            if ($ca) {
                $ca->balance = $ca->balance - $credit->amount;
                $ca->save();
            }
            
            // Delete linked OfficeDebit transfer record if exists
            \App\OfficeDebit::where('credit_id', $credit->id)->delete();

            $credit->delete();
            return response()->json(['status' => 'success']);
        });
    }
}
