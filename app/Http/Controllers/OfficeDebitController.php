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

    private function postExpenseToAccounting($debit)
    {
        try {
            // Check if it's a generic expense mapped from expense_type or standard withdrawal
            $this->accountingService->postAutoTransaction('office_debit', 'withdrawal', [
                'date' => $debit->date,
                'amount' => $debit->amount,
                'reference' => 'OFF-EXP-' . $debit->id,
                'description' => 'مصرف دفتر (Office Expense): ' . ($debit->expense_type ?? 'مصرف') . ' - ' . $debit->description,
                'source_id' => $debit->id,
            ]);
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
        return DB::transaction(function () use ($request) {
            $data = $request->validate([
                'name'=> '',
                'amount' => 'required',
                'amount_af' => 'required',
                'description' => 'required|min:3|max:256',
                'date' => 'required',
                'employee_id'=>'',
                'expense_type'=>'',
                'expense_for_where'=>'',
                'user_role' => ''
            ]);

            if($request->employee_id){
                $emp = OfficeEmployee::find($request->employee_id);
                $data['employee_id'] = $request->employee_id;
                $data['name'] = $emp->name;
            }

            $csh = OfficeCashBook::where('user_role',Auth::user()->role)->first();

            if($csh){
                if($csh->balance < $request->amount){
                    return redirect()->back()->with('error', ' پول در دخل'. $csh->balance .'میباشد');
                } else {
                    $data['user_role'] = Auth::user()->role;
                    $debit = OfficeDebit::create($data);
                    
                    // Accounting Posting
                    $this->postExpenseToAccounting($debit);

                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " مبلغ " . $request->amount . "  مصرف شد ";
                    $activity->user_id = Auth::user()->id;
                    $activity->save();

                    $csh->balance =  $csh->balance - $request->amount;
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
        return  view('office-cash-book.add-expense');
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
        return DB::transaction(function () use ($request, $id) {
            $db  = OfficeDebit::find($id);
            $csh = OfficeCashBook::count();

            if($csh > 0){
                $balance = OfficeCashBook::where('user_role',Auth::user()->role)->first();
                $balance->balance = $balance->balance + $db->amount;
                $balance->update();

                if($balance->balance < $request->amount){
                    $balance->balance = $balance->balance - $db->amount;
                    $balance->update();
                    return redirect()->back()->with('error', ' پول در دخل'. $balance->balance .'میباشد');
                } else {
                    // Reverse Old Transaction
                    $this->accountingService->reverseTransactionBySource($id, 'Office Debit Edited');

                    $balance->balance = $balance->balance - $request->amount;
                    $db->name = $request->name;
                    $db->amount = $request->amount;
                    $db->expense_type = $request->expense_type;
                    $db->expense_for_where = $request->expense_for_where;
                    $db->amount_af = $request->amount_af;
                    $db->description = $request->description;
                    $db->date = $request->date;
                    
                    $activity = new Activity();
                    $activity->date = Carbon::today()->format('Y-m-d');
                    $activity->description = " مبلغ " . $request->amount . "  مصرف ویرایش شد ";
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
                // Reverse Transaction
                $this->accountingService->reverseTransactionBySource($id, 'Office Debit Deleted');
                
                $balance = OfficeCashBook::where('user_role', Auth::user()->role)->first();
                if ($balance) {
                    $balance->balance = $balance->balance + $db->amount;
                    $balance->update();
                }

                $db->delete();
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error']);
        });
    }
}
