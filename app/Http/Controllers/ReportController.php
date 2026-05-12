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
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $agent_id = $request->agent_id ?: 'all';

        if ($agent_id == 'all') {
            $agent_name = 'all';
            $balances = null;
            $agent = DB::table('agents')
                ->join('users', 'agents.user_id', '=', 'users.id')
                ->select('agents.*', 'users.name')
                ->get();
            return view('Reports.agent_balance_report', compact('balances', 'agent_name', 'from_date', 'to_date', 'agent'));
        } 

        // Single Agent Logic
        $ag = \App\Agents::find($agent_id);
        $agent_name = \App\User::find($ag->user_id);
        $agent = '';

        // 1. Calculate Opening Balance from Ledger (All transactions before from_date)
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\Agents')
            ->where('party_id', $agent_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->select(DB::raw('SUM(credit - debit) as balance')) // For agents (Liability/Asset), Credit - Debit usually represents what we owe them
            ->value('balance') ?? 0;

        // 2. Fetch Ledger Transactions for period
        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\Agents')
            ->where('party_id', $agent_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit', 'ledger_entries.currency_code', 'ledger_entries.exchange_rate')
            ->orderBy('ledger_transactions.date', 'ASC')
            ->orderBy('ledger_transactions.id', 'ASC')
            ->get();

        // 3. Fetch Legacy Agent Payments for compatibility (to show original notes/types)
        $balances = DB::table('agent_payments')
            ->where('agent_id', $agent_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->get();

        // 4. Inventory Insights
        $carpetsOnLoom = DB::table('carpets')
            ->where('agent_id', $agent_id)
            ->whereIn('status', [1, 2]) // 1=Active, 2=At Agent
            ->count();

        // Calculate materials issued to agent (from material_sales table)
        $materialsHeld = DB::table('material_sales')
            ->where('agent_id', $agent_id)
            ->where('status', 1)
            ->sum('amount');

        return view('Reports.agent_balance_report', compact(
            'balances', 
            'agent_name', 
            'from_date', 
            'to_date', 
            'agent',
            'openingBalance',
            'ledgerTransactions',
            'carpetsOnLoom',
            'materialsHeld',
            'ag'
        ));
    }


    public function different_account_balance_report()
    {

        $balances = null;
        $all_accounts = null;

        return view('Reports.different_account_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_different_account_balance_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $account_id = $request->account_id ?: 'all';

        if ($account_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('new_different_accounts')->get();
            return view('Reports.different_account_balance_report', compact('balances', 'from_date', 'to_date', 'all_accounts'));
        } 

        // Single Account Logic
        $account = \App\NewDifferentAccount::find($account_id);
        $all_accounts = '';

        // 1. Calculate Opening Balance from Ledger
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\NewDifferentAccount')
            ->where('party_id', $account_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->select(DB::raw('SUM(credit - debit) as balance')) 
            ->value('balance') ?? 0;

        // 2. Fetch Ledger Transactions
        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\NewDifferentAccount')
            ->where('party_id', $account_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit', 'ledger_entries.currency_code')
            ->orderBy('ledger_transactions.date', 'ASC')
            ->orderBy('ledger_transactions.id', 'ASC')
            ->get();

        // 3. Fetch Legacy Payments for compatibility
        $balances = DB::table('new_different_account_payments')
            ->where('account_id', $account_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->get();

        return view('Reports.different_account_balance_report', compact(
            'balances', 
            'from_date', 
            'to_date', 
            'all_accounts', 
            'account',
            'openingBalance',
            'ledgerTransactions'
        ));
    }


    public function kachaee_team_balance_report()
    {


        $balances = null;
        $all_accounts = null;

        return view('Reports.kachaee_team_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_kachaee_team_balance_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $team_id = $request->team_id ?: 'all';

        if ($team_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('kachaees')->get();
            return view('Reports.kachaee_team_balance_report', compact('balances', 'from_date', 'to_date', 'all_accounts'));
        } 

        $account = \App\Kachaee::find($team_id);
        
        // 1. Ledger Data
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->sum(DB::raw('credit - debit'));

        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\Kachaee')->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit')
            ->orderBy('ledger_transactions.date', 'ASC')->orderBy('ledger_transactions.id', 'ASC')->get();

        // 2. WIP Data (Carpets currently with this team)
        $wipCount = DB::table('carpet_repairs')->where('team_id', $team_id)->count();

        // 3. Legacy Data
        $balances = DB::table('kachaee_payments')->where('team_id', $team_id)->whereBetween('date', [$from_date, $to_date])->get();

        return view('Reports.kachaee_team_balance_report', compact('balances', 'from_date', 'to_date', 'account', 'openingBalance', 'ledgerTransactions', 'wipCount'));
    }



    public function washing_team_balance_report()
    {


        $balances = null;
        $all_accounts = null;

        return view('Reports.washing_team_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_washing_team_balance_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $team_id = $request->team_id ?: 'all';

        if ($team_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('washing_teams')->get();
            return view('Reports.washing_team_balance_report', compact('balances', 'from_date', 'to_date', 'all_accounts'));
        } 

        $account = \App\WashingTeam::find($team_id);
        
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->sum(DB::raw('credit - debit'));

        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\WashingTeam')->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit')
            ->orderBy('ledger_transactions.date', 'ASC')->orderBy('ledger_transactions.id', 'ASC')->get();

        $wipCount = DB::table('carpet_washes')->where('team_id', $team_id)->count();
        $balances = DB::table('washing_payments')->where('team_id', $team_id)->whereBetween('date', [$from_date, $to_date])->get();

        return view('Reports.washing_team_balance_report', compact('balances', 'from_date', 'to_date', 'account', 'openingBalance', 'ledgerTransactions', 'wipCount'));
    }


    public function finishing_team_balance_report()
    {


        $balances = null;
        $all_accounts = null;

        return view('Reports.finishing_team_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_finishing_team_balance_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $team_id = $request->team_id ?: 'all';

        if ($team_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('finishing_teams')->get();
            return view('Reports.finishing_team_balance_report', compact('balances', 'from_date', 'to_date', 'all_accounts'));
        } 

        $account = \App\FinishingTeam::find($team_id);
        
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->sum(DB::raw('credit - debit'));

        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\FinishingTeam')->where('party_id', $team_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit')
            ->orderBy('ledger_transactions.date', 'ASC')->orderBy('ledger_transactions.id', 'ASC')->get();

        $wipCount = DB::table('finishing_works')->where('team_id', $team_id)->count();
        $balances = DB::table('finishing_team_payments')->where('team_id', $team_id)->whereBetween('date', [$from_date, $to_date])->get();

        return view('Reports.finishing_team_balance_report', compact('balances', 'from_date', 'to_date', 'account', 'openingBalance', 'ledgerTransactions', 'wipCount'));
    }

    public function string_seller_balance_report()
    {


        $balances = null;
        $all_accounts = null;

        return view('Reports.string_saller_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_string_seller_balance_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $seller_id = $request->seller_id ?: 'all';

        if ($seller_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('string_sellers')->get();
            return view('Reports.string_saller_balance_report', compact('balances', 'from_date', 'to_date', 'all_accounts'));
        } 

        $account = \App\StringSeller::find($seller_id);
        
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\StringSeller')->where('party_id', $seller_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->sum(DB::raw('credit - debit'));

        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\StringSeller')->where('party_id', $seller_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit')
            ->orderBy('ledger_transactions.date', 'ASC')->orderBy('ledger_transactions.id', 'ASC')->get();

        $totalPurchasedWeight = DB::table('purchase_materials')->where('seller_id', $seller_id)->sum('quantity');
        $balances = DB::table('seller_payments')->where('seller_id', $seller_id)->whereBetween('date', [$from_date, $to_date])->get();

        return view('Reports.string_saller_balance_report', compact('balances', 'from_date', 'to_date', 'account', 'openingBalance', 'ledgerTransactions', 'totalPurchasedWeight'));
    }

    public function customer_balance_report()
    {


        $balances = null;
        $all_accounts = null;

        return view('Reports.customer_balance_report', compact('balances', 'all_accounts'));

    }

    public function get_customer_balance_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $customer_id = $request->customer_id ?: 'all';

        if ($customer_id == 'all') {
            $balances = null;
            $all_accounts = DB::table('customers')->get();
            return view('Reports.customer_balance_report', compact('balances', 'from_date', 'to_date', 'all_accounts'));
        } 

        $account = \App\Customer::find($customer_id);
        
        $openingBalance = DB::table('ledger_entries')
            ->where('party_type', 'App\Customer')->where('party_id', $customer_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.date', '<', $from_date)
            ->where('ledger_transactions.status', 'posted')
            ->sum(DB::raw('credit - debit'));

        $ledgerTransactions = DB::table('ledger_entries')
            ->where('party_type', 'App\Customer')->where('party_id', $customer_id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereBetween('ledger_transactions.date', [$from_date, $to_date])
            ->where('ledger_transactions.status', 'posted')
            ->select('ledger_transactions.*', 'ledger_entries.debit', 'ledger_entries.credit')
            ->orderBy('ledger_transactions.date', 'ASC')->orderBy('ledger_transactions.id', 'ASC')->get();

        $outstandingInvoices = DB::table('invoices')->where('customer_id', $customer_id)->where('status', 0)->count();
        $balances = DB::table('customer_payments')->where('customer_id', $customer_id)->whereBetween('date', [$from_date, $to_date])->get();

        return view('Reports.customer_balance_report', compact('balances', 'from_date', 'to_date', 'account', 'openingBalance', 'ledgerTransactions', 'outstandingInvoices'));
    }

    public function expense_report()
    {

        $expenses = '';
        return view('Reports.expense_report', compact('expenses'));
    }

    public function get_expense_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');
        $category_id = $request->category_id ?: 'all';

        if ($category_id == 'all') {
            $all = '1';
            $expenses = DB::table('monthly_expenses')->whereBetween('date', [$from_date, $to_date])->get();
            return view('Reports.expense_report', compact('expenses', 'from_date', 'to_date', 'all'));
        } else {
            $all = '';
            $cat = $category_id;
            $expenses = DB::table('monthly_expenses')->where('category', $category_id)
                ->whereBetween('date', [$from_date, $to_date])->get();
            return view('Reports.expense_report', compact('expenses', 'from_date', 'to_date', 'all', 'cat'));
        }
    }
    
     public function purchase_carpet_report()
    {
        $search = '';
        return view('Reports.purchase_carpet_report', compact('search'));
    }

    public function get_purchase_carpet_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');

        $carpets = Carpet::with('agent')->whereBetween('date', [$from_date, $to_date])->get();
        $search = 'ok';
        $agents = Agents::all();
        return view('Reports.purchase_carpet_report', compact('carpets', 'search', 'agents', 'from_date', 'to_date'));
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

    public function get_sales_report(Request $request)
    {
        $from_date = $request->from_date ?: '2000-01-01';
        $to_date = $request->to_date ?: date('Y-m-d');

        $sales = Sale::whereBetween('created_at', [$from_date, $to_date])->get();
        $invoices = Invoice::orderBy('id', 'DESC')->get();
        $packing_list = PakingList::orderBy('id', 'DESC')->get();
        $carpets = Carpet::where('status', 5)->whereBetween('date', [$from_date, $to_date])->get();
        $sale = '';
        $search = 'yes';
        return view('Reports.sales_report', compact('sales', 'carpets', 'invoices', 'packing_list', 'sale', 'search', 'from_date', 'to_date'));
    }

}
