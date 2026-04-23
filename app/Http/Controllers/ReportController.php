<?php

namespace App\Http\Controllers;

use App\Agents;
use App\Carpet;
use App\Customer;
use App\FinishingTeam;
use App\Invoice;
use App\Kachaee;
use App\Models\Account;
use App\PakingList;
use App\Sale;
use App\StringSeller;
use App\WashingTeam;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function agent_balance_report()
    {

        $balances = '';
        $agent = '';

        return view('Reports.agent_balance_report', compact('balances', 'agent'));

    }

    public function get_agent_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->agent_id == null) {
            return redirect()->intended(url('agent_balance_report'));
        }

        if ($request->agent_id == 'all') {
            $agent_name = 'all';
            $balances = null;
            $agent = DB::table('agents')->get();


        } else {
            $ag = \App\Agents::find($request->agent_id);
            $agent_name = \App\User::find($ag->user_id);
            $agent = '';
            $balances = DB::table('agent_payments')->where('agent_id', $request->agent_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();
        }

        return view('Reports.agent_balance_report', compact('balances', 'agent_name', 'from_date', 'to_date', 'agent'));
    }


    public function different_account_balance_report()
    {

        $balances = '';
        $all_accounts = '';

        return view('Reports.different_account_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_different_account_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->account_id == null) {
            return redirect()->intended(url('different_account_balance_report'));
        }

        if ($request->account_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('new_different_accounts')->get();
            $account = '';


        } else {
            $account = \App\NewDifferentAccount::find($request->account_id);
            $all_accounts = '';
            $balances = DB::table('new_different_account_payments')->where('account_id', $request->account_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();

        }

        return view('Reports.different_account_balance_report', compact('balances', 'from_date', 'to_date','all_accounts','account'));
    }


    public function kachaee_team_balance_report()
    {


        $balances = '';
        $all_accounts = '';

        return view('Reports.kachaee_team_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_kachaee_team_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->team_id == null) {
            return redirect()->intended(url('kachaee_team_balance_report'));
        }

        if ($request->team_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('kachaees')->get();
            $account = '';


        } else {
            $account = Kachaee::find($request->team_id);
            $all_accounts = '';
            $balances = DB::table('kachaee_payments')->where('team_id', $request->team_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();

        }

        return view('Reports.kachaee_team_balance_report', compact('balances', 'from_date', 'to_date','all_accounts','account'));
    }



    public function washing_team_balance_report()
    {


        $balances = '';
        $all_accounts = '';

        return view('Reports.washing_team_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_washing_team_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->team_id == null) {
            return redirect()->intended(url('washing_team_balance_report'));
        }

        if ($request->team_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('washing_teams')->get();
            $account = '';


        } else {
            $account = WashingTeam::find($request->team_id);
            $all_accounts = '';
            $balances = DB::table('washing_payments')->where('team_id', $request->team_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();

        }

        return view('Reports.washing_team_balance_report', compact('balances', 'from_date', 'to_date','all_accounts','account'));
    }


    public function finishing_team_balance_report()
    {


        $balances = '';
        $all_accounts = '';

        return view('Reports.finishing_team_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_finishing_team_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->team_id == null) {
            return redirect()->intended(url('finishing_team_balance_report'));
        }

        if ($request->team_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('finishing_teams')->get();
            $account = '';


        } else {
            $account = FinishingTeam::find($request->team_id);
            $all_accounts = '';
            $balances = DB::table('finishing_team_payments')->where('team_id', $request->team_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();

        }

        return view('Reports.finishing_team_balance_report', compact('balances', 'from_date', 'to_date','all_accounts','account'));
    }

    public function string_seller_balance_report()
    {


        $balances = '';
        $all_accounts = '';

        return view('Reports.string_saller_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_string_seller_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->seller_id == null) {
            return redirect()->intended(url('string_seller_balance_report'));
        }

        if ($request->seller_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('string_sellers')->get();
            $account = '';


        } else {
            $account = StringSeller::find($request->seller_id);
            $all_accounts = '';
            $balances = DB::table('seller_payments')->where('seller_id', $request->seller_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();

        }

        return view('Reports.string_saller_balance_report', compact('balances', 'from_date', 'to_date','all_accounts','account'));
    }

    public function customer_balance_report()
    {


        $balances = '';
        $all_accounts = '';

        return view('Reports.customer_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_customer_balance_report(Request $request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null || $request->customer_id == null) {
            return redirect()->intended(url('customer_balance_report'));
        }

        if ($request->customer_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('customers')->get();
            $account = '';


        } else {
            $account = Customer::find($request->customer_id);
            $all_accounts = '';
            $balances = DB::table('customer_payments')->where('customer_id', $request->customer_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])->get();

        }

        return view('Reports.customer_balance_report', compact('balances', 'from_date', 'to_date','all_accounts','account'));
    }

    public function expense_report()
    {

        $expenses = '';
        return view('Reports.expense_report', compact('expenses'));
    }

    public function get_expense_report(Request $request)
    {
        if ($request->from_date == null || $request->to_date == null || $request->category_id == null) {
            return redirect()->intended('expense_report');
        }

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($request->category_id == 'all') {

            $all = '1';
            $expenses = DB::table('monthly_expenses')->whereBetween('date', [$from_date, $to_date])->get();

            return view('Reports.expense_report', compact('expenses', 'from_date', 'to_date', 'all'));

        } else {
            $all = '';

            $cat = $request->category_id;
            $expenses = DB::table('monthly_expenses')->where('category', $request->category_id)
                ->whereBetween('date', [$from_date, $to_date])->get();
            return view('Reports.expense_report', compact('expenses', 'from_date', 'to_date','all','cat'));

        }

    }
    
     public function purchase_carpet_report()
    {
        $search = '';
        return view('Reports.purchase_carpet_report', compact('search'));
    }

    public function get_purchase_carpet_report(Request $request)
    {


        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($from_date == null || $to_date == null) {
            return redirect()->intended(url('purchase_carpet_report'));
        }

        $carpets = Carpet::with('agent')->whereBetween('date', [$request->from_date, $request->to_date])->get();;
        $search = 'ok';
        $agents = Agents::all();
          return view('Reports.purchase_carpet_report', compact('carpets','search','agents','from_date','to_date'));






    }
    
    
    public function sales_report(){
        $sales = Sale::orderBy('created_at','DESC')->paginate(60);
        $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->get();
        $sale = '';
        $search = '';
        return view('Reports.sales_report',compact('sales','carpets','invoices','packing_list','sale','search'));
    }

    public function get_sales_report(Request $request){
        $sales = Sale::whereBetween('created_at', [$request->from_date, $request->to_date])->get();
        $invoices = Invoice::orderBy('id','DESC')->get();
        $packing_list = PakingList::orderBy('id','DESC')->get();
        $carpets = Carpet::where('status',5)->whereBetween('date', [$request->from_date, $request->to_date])->get();
        $sale = '';
        $search = 'yes';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        return view('Reports.sales_report',compact('sales','carpets','invoices','packing_list','sale','search','from_date','to_date'));
    }

}
