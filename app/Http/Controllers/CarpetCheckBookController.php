<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Agents;
use App\CarpetCheckBook;
use App\Carpet;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarpetCheckBookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agents = Agents::all();
        // return $check;
        return view('carpet-check-book.index', compact('agents'));
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
    public function store(Request $request, \App\Services\AccountingService $accountingService)
    {


        $data = $this->valData();
        $carpet = Carpet::where('carpet_id', '=', $request->carpet_id)->first();

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " قالین نمبر  " . $carpet->carpet_no . " چک بک شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();


        $carpet->carpet_price = $carpet->carpet_price - $request->kachaee_amount;
        $carpet->carpet_price_us = $carpet->carpet_price_us - $request->kachaee_dollar_amount;
        $carpet->status = 1;
        $carpet->update();
        
        // Remove accounting fields before creating checkbook to avoid mass assignment errors
        $checkBookData = $data;
        $checkBookData['currency_code'] = \App\Currency::find($request->currency_id)->code ?? 'AFN';
        $checkBookData['exchange_rate'] = $request->exchange_rate;
        
        unset($checkBookData['debit_account_id'], $checkBookData['credit_account_id'], $checkBookData['currency_id']);
        
        $done = $carpet->check_book()->create($checkBookData);
        
        if ($done) {
            // Post GL Transaction
            $currencyCode = \App\Currency::find($request->currency_id)->code ?? 'AFN';
            $amount = $request->kachaee_dollar_amount ?: $request->kachaee_amount;
            
            $accountingService->postTransaction([
                'date' => $request->date,
                'reference' => 'CH-' . $done->id,
                'description' => "کسر کچایی برای قالین نمبر " . $carpet->carpet_no,
                'source_type' => get_class($done),
                'source_id' => $done->id,
                'journal_type' => 'journal',
                'entries' => [
                    [
                        'account_id' => $request->debit_account_id,
                        'debit' => $amount,
                        'credit' => 0,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $request->exchange_rate,
                    ],
                    [
                        'account_id' => $request->credit_account_id,
                        'debit' => 0,
                        'credit' => $amount,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $request->exchange_rate,
                    ]
                ]
            ]);

            if ($carpet->agent->contract_type == 'weight') {
                if ($request->has('agent_carpet')) {
                    return redirect('/dashboard/agent-carpet/' . $request->agent_id)->with('status', '  چک بٌک موفقانه ثبت شد !');
                } else {
                    return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('status', '  چک بٌک موفقانه ثبت شد !');
                }
            } elseif ($carpet->agent->contract_type == 'carpet seller') {
                if ($request->has('agent_carpet')) {
                    return redirect('/dashboard/agent-carpet/' . $request->agent_id)->with('status', '  چک بٌک موفقانه ثبت شد !');
                } else {
                    return redirect()->action('CarpetsController@printBuyCarpet', ['id' => $request->carpet_id])->with('status', '  چک بٌک موفقانه ثبت شد !');
                }
            } else {
                if ($request->has('agent_carpet')) {
                    return redirect('/dashboard/agent-carpet/' . $request->agent_id)->with('status', '  چک بٌک موفقانه ثبت شد !');
                } else {
                    return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('status', '  چک بٌک موفقانه ثبت شد !');
                }
            }
        } else {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarpetCheckBook  $carpetCheckBook
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agent = Agents::findOrfail($id);
        //        $checkbooks = CarpetCheckBook::Where('agent_id', '=', $id)->distinct()->get(['check_number','agent_id','date']);
//        return view('carpet-check-book.check-book-list', compact('checkbooks','agent'));
        $check = CarpetCheckBook::where('agent_id', $id)->first();
        $check_number = $check->check_number;
        $checkbooks = CarpetCheckBook::where('agent_id', $id)->where('check_number', $check->check_number)->get();
        $check_date = CarpetCheckBook::where('agent_id', $id)->where('check_number', $check->check_number)->first();

        $check_numbers = CarpetCheckBook::where('agent_id', $id)->distinct()->get(['check_number']);
        return view('carpet-check-book.check-number-list', compact('checkbooks', 'agent', 'check_number', 'check_date', 'check_numbers'));

    }
    public function search_check_number(Request $request)
    {
        $agent_id = $request->agent_id;
        $check_number = $request->check_number;
        $search = $request->search;
        $agent = Agents::findOrfail($agent_id);
        $check_date = CarpetCheckBook::where('agent_id', $agent_id)->where('check_number', $check_number)->first();

        if ($search) {
            $checkbooks = CarpetCheckBook::where('agent_id', $agent_id)->where('check_number', $check_number)
                ->WhereHas('carpet', function ($query) use ($search) {
                    $query->where('carpet_no', 'like', '%' . $search . '%');
                })
                ->orderBy('carpet_id', 'ASC')->get();
        } else {
            $checkbooks = CarpetCheckBook::where('agent_id', $agent_id)->where('check_number', $check_number)->orderBy('carpet_id', 'ASC')->get();
        }
        $check_numbers = CarpetCheckBook::where('agent_id', $agent_id)->distinct()->get(['check_number']);

        return view('carpet-check-book.check-number-list', compact('checkbooks', 'agent', 'check_number', 'check_date', 'check_numbers'));



    }

    public function search_check_number_payment($check_number, $agent_id)
    {


        $agent = Agents::findOrfail($agent_id);
        $check_date = CarpetCheckBook::where('agent_id', $agent_id)->where('check_number', $check_number)->first();
        $checkbooks = CarpetCheckBook::where('agent_id', $agent_id)->where('check_number', $check_number)->orderBy('carpet_id', 'ASC')->get();
        $check_numbers = CarpetCheckBook::where('agent_id', $agent_id)->distinct()->get(['check_number']);

        return view('carpet-check-book.check-number-list', compact('checkbooks', 'agent', 'check_number', 'check_date', 'check_numbers'));



    }

    public function Filter(Request $request)
    {
        $agent = Agents::findOrfail($request->agentId);
        $checkbooks = CarpetCheckBook::where('check_number', '=', $request->search)->Where('agent_id', '=', $request->agentId)->get();
        return view('carpet-check-book.check-book-list', compact('checkbooks', 'agent'));
    }
    public function search_agent(Request $request)
    {
        $search = $request->search;


        $agents = Agents::whereHas('user', function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->orwhereHas('phone', function ($query) use ($search) {
            $query->where('phone_no', 'like', '%' . $search . '%');
        })
            ->get();


        return view('carpet-check-book.index', compact('agents'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarpetCheckBook  $carpetCheckBook
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetCheckBook  $carpetCheckBook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, \App\Services\AccountingService $accountingService)
    {
        $data = $this->valData();
        $carpet = Carpet::where('carpet_id', '=', $request->carpet_id)->first();
        $checkbook = $carpet->check_book;

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "چک بک قالین نمبر  " . $carpet->carpet_no . " ویرایش شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        // Adjust carpet price using USD values for accuracy
        $carpet->carpet_price = $carpet->carpet_price + $request->old_kachaee_amount - $request->kachaee_amount;
        $carpet->carpet_price_us = $carpet->carpet_price_us + $request->old_kachaee_dollar_amount - $request->kachaee_dollar_amount;
        $carpet->update();

        // Forensic FX markers for checkbook
        $data['currency_code'] = \App\Currency::find($request->currency_id)->code ?? 'AFN';
        $data['exchange_rate'] = $request->exchange_rate;

        // Clean data for DB update
        $checkBookData = $data;
        unset($checkBookData['debit_account_id'], $checkBookData['credit_account_id'], $checkBookData['currency_id'], $checkBookData['old_kachaee_amount'], $checkBookData['old_kachaee_dollar_amount']);

        $done = $checkbook->update($checkBookData);
        if ($done) {
            // Reverse and re-post GL transaction for correction
            $accountingService->reverseTransactionBySource($checkbook->id, 'Correction: Checkbook updated', get_class($checkbook));
            
            $currencyCode = $data['currency_code'];
            $amount = $request->kachaee_dollar_amount ?: $request->kachaee_amount;
            
            $accountingService->postTransaction([
                'date' => $data['date'],
                'reference' => 'CH-' . $checkbook->id . '-CORR',
                'description' => "تصحیح کسر کچایی برای قالین نمبر " . $carpet->carpet_no,
                'source_type' => get_class($checkbook),
                'source_id' => $checkbook->id,
                'journal_type' => 'journal',
                'entries' => [
                    [
                        'account_id' => $request->debit_account_id,
                        'debit' => $amount,
                        'credit' => 0,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $request->exchange_rate,
                    ],
                    [
                        'account_id' => $request->credit_account_id,
                        'debit' => 0,
                        'credit' => $amount,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $request->exchange_rate,
                    ]
                ]
            ]);

            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('status', '  موفقانه ویرایش شد !');
            } elseif ($carpet->agent->contract_type == 'carpet seller') {
                return redirect()->action('CarpetsController@printBuyCarpet', ['id' => $request->carpet_id])->with('status', '  چک بٌک موفقانه ثبت شد !');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('status', ' موفقانه ویرایش شد !');
            }
        } else {
            if ($carpet->agent->contract_type == 'weight') {
                return redirect()->action('CarpetsController@showWeight', ['id' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            } else {
                return redirect()->action('CarpetsController@show', ['carpet' => $request->carpet_id])->with('error', 'مشکل در سرور وجود داره!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarpetCheckBook  $carpetCheckBook
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, \App\Services\AccountingService $accountingService)
    {
        $checkbook = \App\CarpetCheckBook::findOrFail($id);
        $carpet = \App\Carpet::where('carpet_id', $checkbook->carpet_id)->first();
        
        // Reverse Carpet Values
        $carpet->carpet_price = $carpet->carpet_price + $checkbook->kachaee_amount;
        $carpet->carpet_price_us = $carpet->carpet_price_us + $checkbook->kachaee_dollar_amount;
        $carpet->status = 0; // Reset to un-checked
        $carpet->update();
        
        // Reverse Ledger Transaction
        $accountingService->reverseTransactionBySource($checkbook->id, 'لغو عملیه چک بٌک توسط کاربر', get_class($checkbook));
        
        // Log Activity
        $activity = new \App\Activity();
        $activity->date = \Carbon\Carbon::today()->format('Y-m-d');
        $activity->description = "چک بک قالین نمبر  " . $carpet->carpet_no . " لغو و ریورس شد ";
        $activity->user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $activity->save();

        $checkbook->delete();

        return redirect()->back()->with('status', 'عملیه چک بٌک موفقانه لغو و ریورس گردید!');
    }

    protected function valData()
    {
        return request()->validate([
            'height' => 'required',
            'width' => 'required',
            'widthwaste' => '',
            'heightwaste' => '',
            'date' => 'required',
            'area' => 'required',
            'carpet_id' => 'nullable',
            'agent_id' => 'nullable',
            'kachaee_amount' => '',
            'kachaee_dollar_amount' => '',
            'check_number' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'currency_id' => 'required',
            'exchange_rate' => 'required',
            'currency_code' => 'nullable',
        ]);
    }
}
