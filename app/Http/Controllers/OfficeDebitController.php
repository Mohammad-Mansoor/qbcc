<?php

namespace App\Http\Controllers;

use App\Activity;
use App\OfficeDebit;
use App\OfficeCashBook;
use App\OfficeEmployee;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OfficeDebitController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function postExpenseToAccounting($debit, $overrides = [])
    {
        try {
            // FORENSIC RULE: Always use base_amount (USD) for the GL
            $amount = $debit->base_amount;

            $this->accountingService->postAutoTransaction('office_debit', 'withdrawal', array_merge([
                'date' => $debit->date,
                'amount' => $amount,
                'currency_code' => $debit->currency_code,
                'exchange_rate' => $debit->exchange_rate,
                'reference' => 'OFF-EXP-' . $debit->id,
                'description' => 'مصرف دفتر (Office Expense): ' . ($debit->expense_type ?? 'مصرف') . ' - ' . $debit->description,
                'source_id' => $debit->id,
            ], $overrides));
        } catch (\Exception $e) {
            \Log::error("Accounting posting failed for Office Debit #" . $debit->id . ": " . $e->getMessage());
        }
    }

    public function index()
    {

    }

    public function create()
    {
        //
    }

    public function search(Request $request)
    {
       $from_date = $request->from_date;
       $to_date = $request->to_date;
       $employee = OfficeEmployee::find($request->employee_id);
       $salary = $employee->employee_salary->last();
       $debits = OfficeDebit::where('employee_id',$request->employee_id)->whereBetween('date',[$from_date,$to_date])->orderBy('id','DESC')->get();
       $debits_this_month = OfficeDebit::where('employee_id',$request->employee_id)->whereBetween('date',[$from_date,$to_date])->get();

       $paymentEdit = '';
       return view('office-employee-salary.salary-payment',compact('paymentEdit','employee','salary','debits','debits_this_month','from_date' ,'to_date'));
    }

    public function store(Request $request)
    {
        $this->accountingService->failIfLocked($request->date);
        
        return DB::transaction(function () use ($request) {
            $data = $request->validate([
                'name'=> '',
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'description' => 'required|min:3|max:256',
                'date' => 'required|date',
                'employee_id'=>'',
                'expense_type'=>'',
                'expense_for_where'=>'',
                'user_role' => '',
                'override_debit_account_id' => 'nullable|exists:chart_of_accounts,id',
                'override_credit_account_id' => 'nullable|exists:chart_of_accounts,id'
            ]);

            if($request->employee_id){
                $emp = OfficeEmployee::find($request->employee_id);
                $data['employee_id'] = $request->employee_id;
                $data['name'] = $emp->name;
            }

            $csh = OfficeCashBook::first();

            if($csh){
                $currency = \App\Currency::find($request->currency_id);
                $rate = $request->exchange_rate ?: $currency->exchange_rate;
                $baseAmount = bcmul((string)$request->amount, (string)$rate, 4);

                if(bccomp((string)$csh->balance, (string)$baseAmount, 4) < 0){
                    return redirect()->back()->with('error', ' پول در دخل ' . number_format($csh->balance, 2) . ' USD میباشد و با احتساب نرخ تبدیل مصرف شما معادل ' . number_format((float)$baseAmount, 2) . ' USD میگردد.');
                } else {
                    $data['user_role'] = Auth::user()->role;
                    $data['currency_id'] = $request->currency_id;
                    $data['currency_code'] = $currency->code;
                    $data['exchange_rate'] = $rate;
                    $data['original_amount'] = $request->amount;
                    $data['base_amount'] = $baseAmount;

                    // Legacy dual-amount fallback
                    if ($currency->code == 'AFN') {
                        $data['amount_af'] = $request->amount;
                        $data['amount'] = 0;
                    } else {
                        $data['amount'] = $request->amount;
                        $data['amount_af'] = 0;
                    }

                    $debit = OfficeDebit::create($data);
                    
                    // Accounting Posting with Overrides
                    $overrides = [];
                    if ($request->override_debit_account_id) $overrides['override_debit_account_id'] = $request->override_debit_account_id;
                    if ($request->override_credit_account_id) $overrides['override_credit_account_id'] = $request->override_credit_account_id;

                    $this->postExpenseToAccounting($debit, $overrides);

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " مبلغ " . $request->amount . " " . $currency->code . "  مصرف شد ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();

                    // Balance logic in دخل (Cash Book) - Subtract normalized base USD
                    $csh->balance = bcsub((string)$csh->balance, (string)$baseAmount, 4);
                    $csh->update();

                    if($request->employee_id){
                        return redirect()->back()->with('status', 'مصرف موفقانه ثبت شد !');
                    } else {
                        return redirect('/dashboard/office-cash-book')->with('status', 'مصرف موفقانه ثبت و در روزنامچه درج شد!');
                    }
                }
            } else {
                return redirect()->back()->with('error','پول در دخل موجود نیست');
            }
        });
    }

    public function add_new_expense(){
        $selectionService = new \App\Services\AccountSelectionService();
        $allowedDebitAccounts = $selectionService->getValidAccounts('CASH_OUT', 'debit');
        $allowedCreditAccounts = $selectionService->getValidAccounts('CASH_OUT', 'credit');
        
        $mapping = \App\MappingRule::where('mapping_key', 'CASH_OUT')->first();
        $currencies = \App\Currency::where('is_active', true)->get();

        return view('office-cash-book.add-expense', compact(
            'allowedDebitAccounts', 'allowedCreditAccounts', 'mapping', 'currencies'
        ));
    }

    public function show($id)
    {
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');
        $employee = OfficeEmployee::find($id);
        $salary = $employee->employee_salary->last();
        $debits_this_month = OfficeDebit::where('employee_id',$id)->whereBetween('date',[$start,$end])->get();
        $debits = OfficeDebit::where('employee_id',$id)->orderBy('id','DESC')->get();
        $from_date = '';
        $to_date = '';
        $paymentEdit = '';
        return view('office-employee-salary.salary-payment',compact('paymentEdit','employee','salary','debits','debits_this_month','from_date' , 'to_date'));
    }

    public function edit($id)
    {
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');
        $paymentEdit = officeDebit::find($id);
        $employee = OfficeEmployee::find($paymentEdit->employee_id);
        $debits_this_month = OfficeDebit::where('employee_id',$paymentEdit->employee_id)->whereBetween('date',[$start,$end])->get();
        $salary = $employee->employee_salary->last();
        $debits =  OfficeDebit::where('employee_id',$paymentEdit->employee_id)->orderBy('id','DESC')->get();
        $from_date = '';
        $to_date = '';
        return view('office-employee-salary.salary-payment', compact('paymentEdit','from_date','to_date','debits','employee','salary','debits_this_month'));
    }

    public function update(Request $request, $id)
    {
        $this->accountingService->failIfLocked($request->date);
        
        return DB::transaction(function () use ($request, $id) {
            $db  = OfficeDebit::find($id);
            $csh = OfficeCashBook::count();

            if($csh > 0){
                $balance = OfficeCashBook::first();
                
                // Add the old base USD amount back to cash book
                $oldUSD = $db->base_amount ?: ($db->amount_af ? bcdiv((string)$db->amount_af, '60.0000', 4) : $db->amount);
                $balance->balance = bcadd((string)$balance->balance, (string)$oldUSD, 4);
                $balance->update();

                $currency = \App\Currency::find($request->currency_id);
                $rate = $request->exchange_rate ?: $currency->exchange_rate;
                $baseAmount = bcmul((string)$request->amount, (string)$rate, 4);

                if(bccomp((string)$balance->balance, (string)$baseAmount, 4) < 0){
                    // Revert old USD addition if check fails
                    $balance->balance = bcsub((string)$balance->balance, (string)$oldUSD, 4);
                    $balance->update();
                    return redirect()->back()->with('error', ' پول در دخل ' . number_format($balance->balance, 2) . ' USD میباشد و با احتساب نرخ تبدیل مصرف شما معادل ' . number_format((float)$baseAmount, 2) . ' USD میگردد.');
                } else {
                    // Reverse Old Transaction
                    $this->accountingService->reverseTransactionBySource($id, 'Office Debit Edited');

                    // Subtract new base USD amount from cash book
                    $balance->balance = bcsub((string)$balance->balance, (string)$baseAmount, 4);
                    
                    $db->name = $request->name;
                    $db->description = $request->description;
                    $db->date = $request->date;
                    $db->expense_type = $request->expense_type;
                    $db->expense_for_where = $request->expense_for_where;

                    // FORENSIC SNAPSHOTS
                    $db->currency_id = $request->currency_id;
                    $db->currency_code = $currency->code;
                    $db->exchange_rate = $rate;
                    $db->original_amount = $request->amount;
                    $db->base_amount = $baseAmount;

                    // Legacy dual-amount fallback
                    if ($currency->code == 'AFN') {
                        $db->amount_af = $request->amount;
                        $db->amount = 0;
                    } else {
                        $db->amount = $request->amount;
                        $db->amount_af = 0;
                    }
                    
                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " مبلغ " . $request->amount . " " . $currency->code . "  مصرف ویرایش شد ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();

                    $balance->update();
                    $db->update();

                    // Re-post New Transaction
                    $this->postExpenseToAccounting($db);

                    if ($request->employee_id){
                        return redirect('/dashboard/expenses/'.$request->employee_id)->with('status', 'پرداخت موفقانه ثبت شد !');
                    } else {
                        return redirect('/dashboard/office-cash-book')->with('status', 'موفقانه ثبت و در روزنامچه بروز گردید!');
                    }
                }
            } else {
                return redirect()->back()->with('error','پول در دخل موجود نیست');
            }
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $db = OfficeDebit::find($id);
            if ($db) {
                $this->accountingService->failIfLocked($db->date);
                // Reverse Transaction
                $this->accountingService->reverseTransactionBySource($id, 'Office Debit Deleted');
                
                $balance = OfficeCashBook::first();
                if ($balance) {
                    // Restore original USD base amount back to cash book
                    $oldUSD = $db->base_amount ?: ($db->amount_af ? bcdiv((string)$db->amount_af, '60.0000', 4) : $db->amount);
                    $balance->balance = bcadd((string)$balance->balance, (string)$oldUSD, 4);
                    $balance->update();
                }

                $db->delete();
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error']);
        });
    }
}
