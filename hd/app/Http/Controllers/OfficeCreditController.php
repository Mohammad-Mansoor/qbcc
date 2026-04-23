<?php

namespace App\Http\Controllers;

use App\Activity;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\OfficeCredit;
use App\OfficeCashBook;
use App\OfficeDebit;
use Carbon\Carbon;
use const http\Client\Curl\AUTH_ANY;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OfficeCreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $creditEdit = "";
        /** list credits from three type user */
        $center_credits = OfficeCredit::where('user_role','CO')->orWhere('user_role','CCO')->orderBy('id', 'DESC')->get();
        $froshat_credits = OfficeCredit::where('user_role','SO')->orWhere('user_role','SCO')->orderBy('id', 'DESC')->get();
        $sp_credits = OfficeCredit::where('user_role','SP')->orderBy('id', 'DESC')->get();
        /** end list three type user credit */

        /** show credits of two center and sales office */
        $other_user = OfficeCredit::where('user_role', '!=', 'SP')->where('payment_id', null)->where('customer_id', null)->where('status','!=',0)->sum('amount');
        /** end two type user credits list */


        /** show total credits of three type user */
        $center_total = OfficeCredit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_total = OfficeCredit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_total = OfficeCredit::where('user_role', '=', 'SP')->sum('amount');
        /** end credit of three type users */

        $cashbook = OfficeCashBook::count();
        $cash = '';

        /** list debits from three type user */
        $center_debits = OfficeDebit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_debits = OfficeDebit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_debits = OfficeDebit::where('user_role', '=', 'SP')->sum('amount');
        /** end list three type user debits */
        /** balance of so and co office */
        $so_cashbook = OfficeCashBook::where('user_role','SO')->orWhere('user_role','SCO')->sum('balance');
        $co_cashbook = OfficeCashBook::where('user_role','CO')->orWhere('user_role','CCO')->sum('balance');
        /** end balance of so and CO office */
        if ($cashbook > 0) {
            $cash = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');
        }
        return view('office-cash-book.add-credit', compact('center_credits','froshat_credits','sp_credits','center_total','froshat_total','sp_total','center_debits','froshat_debits','sp_debits', 'creditEdit', 'cash', 'other_user', 'so_cashbook', 'co_cashbook'));
    }

    public function money_request()
    {
        $credits = OfficeCredit::where('user_role', '!=', 'SP')->where('status', 0)->orderBy('id', 'DESC')->get();

        return view('office-cash-book.requested-money-list', compact('credits'));
    }

    public function approve_request($id)
    {

        $credit = OfficeCredit::find($id);

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
        $debit->save();

        $credit->status = 1 ;
        $credit->update();
        return response()->json(['status' => 'success']);

    }
    public function delete_request($id){
        $credit = OfficeCredit::find($id);
         $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " مبلغ " . $credit->amount . " که درخواست شده بود  توسط سوپر ادمین رد شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'user_role' => '',
            'status' => ''

        ]);
        $data['user_role'] = Auth::user()->role;
        $data['status'] = 0;


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
                        $credit = officeCredit::create($data);
                        if (!$cashbook) {
                            $cash = new OfficeCashBook();
                            $cash->balance = 0;
                            $cash->user_role = Auth::user()->role;
                            $cash->save();
                        }


//
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
            $credit = officeCredit::create($data);
            
            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . $request->amount . "  دخل شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
        }

        if ($credit) {
            if (Auth::user()->role == 'SP') {
                return redirect('/dashboard/add-office-credit')->with('status', 'مقدار پول موفقانه ثبت شد !');
            } else {
                return redirect('/dashboard/add-office-credit')->with('status', 'درخواست شما موفقانه ارسال شد تا تایید ان منتظر بمانید !');
            }

        } else {
            return redirect('/dashboard/add-office-credit')->with('error', 'مشکل در سرور وجود داره!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OfficeCredit $officeCredit
     * @return \Illuminate\Http\Response
     */
    public function show(OfficeCredit $officeCredit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OfficeCredit $officeCredit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $creditEdit = officeCredit::find($id);
        /** list credits from three type user */
        $center_credits = OfficeCredit::where('user_role','CO')->orWhere('user_role','CCO')->orderBy('id', 'DESC')->get();
        $froshat_credits = OfficeCredit::where('user_role','SO')->orWhere('user_role','SCO')->orderBy('id', 'DESC')->get();
        $sp_credits = OfficeCredit::where('user_role','SP')->orderBy('id', 'DESC')->get();
        /** end list three type user credit */

        /** show credits of two center and sales office */
        $other_user = OfficeCredit::where('user_role', '!=', 'SP')->where('payment_id', null)->where('customer_id', null)->where('status','!=',0)->sum('amount');
        /** end two type user credits list */


        /** show total credits of three type user */
        $center_total = OfficeCredit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_total = OfficeCredit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_total = OfficeCredit::where('user_role', '=', 'SP')->sum('amount');
        /** end credit of three type users */

        $cashbook = OfficeCashBook::count();
        $cash = '';

        /** list debits from three type user */
        $center_debits = OfficeDebit::where('user_role', '=', 'CO')->orWhere('user_role', '=', 'CCO')->sum('amount');
        $froshat_debits = OfficeDebit::where('user_role', '=', 'SO')->orWhere('user_role', '=', 'SCO')->sum('amount');
        $sp_debits = OfficeDebit::where('user_role', '=', 'SP')->sum('amount');
        /** end list three type user debits */
        /** balance of so and co office */
        $so_cashbook = OfficeCashBook::where('user_role','SO')->orWhere('user_role','SCO')->sum('balance');
        $co_cashbook = OfficeCashBook::where('user_role','CO')->orWhere('user_role','CCO')->sum('balance');
        /** end balance of so and CO office */
        if ($cashbook > 0) {
            $cash = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');
        }
        return view('office-cash-book.add-credit', compact('center_credits','froshat_credits','sp_credits','center_total','froshat_total','sp_total','center_debits','froshat_debits','sp_debits', 'creditEdit', 'cash', 'other_user', 'so_cashbook', 'co_cashbook'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\OfficeCredit $officeCredit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $credit = officeCredit::find($id);

        $ca = OfficeCashBook::where('user_role', Auth::user()->role)->first();

        $sp = OfficeCashBook::where('user_role', 'SP')->first();

        if (Auth::user()->role == 'SP') {
            $sp->balance = $sp->balance - $credit->amount + $request->amount;
            $sp->update();
        }



        if ($sp->balance  < $request->amount){
            return back()->with('error', 'پول خواسته از پول دخل عمومی زیاد است ');
        }
        else {

            $credit->amount = $request->amount;
            $credit->description = $request->description;
            $credit->date = $request->date;
            $credit->update();
            
              $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = " مبلغ " . $request->amount . "  دخل شده ویرایش شد ";
            $activity->user_id = Auth::user()->id;
            $activity->save();
        }

        return redirect('/dashboard/add-office-credit')->with('status', 'موفقانه بروز شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OfficeCredit $officeCredit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $credit = officeCredit::find($id);
        
          $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " مبلغ " . $credit->amount . "  دخل شده حذف شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();        
        
        $ca = OfficeCashBook::find(1);
        $ca->balance = $ca->balance - $credit->amount;
        $ca->save();
        $credit->delete();
        if ($credit) {
            return response()->json(['status' => 'success']);
        }
    }
}
