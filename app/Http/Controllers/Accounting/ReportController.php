<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\ChartOfAccount;
use App\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\AccountingAnalyticsService;

use App\Customer;

class ReportController extends Controller
{
    protected $analyticsService;

    public function __construct(AccountingAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function customerStatement(Request $request)
    {
        $customerId = $request->customer_id;
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $customer = Customer::find($customerId);
        $customers = Customer::orderBy('name')->get();

        $entries = [];
        $openingBalance = 0;

        if ($customerId) {
            // 1. Calculate Opening Balance for this customer across all AR accounts
            $opening = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select(DB::raw('SUM(le.debit - le.credit) as balance'))
                ->where('le.party_type', 'App\Customer')
                ->where('le.party_id', $customerId)
                ->where('lt.date', '<', $startDate)
                ->where('lt.status', 'posted')
                ->first();
            
            $openingBalance = $opening->balance ?? 0;

            // 2. Get Transactions for the period
            $entries = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select('lt.id as transaction_id', 'lt.date', 'lt.reference', 'lt.description', 'le.debit', 'le.credit')
                ->where('le.party_type', 'App\Customer')
                ->where('le.party_id', $customerId)
                ->whereBetween('lt.date', [$startDate, $endDate])
                ->where('lt.status', 'posted')
                ->orderBy('lt.date')
                ->orderBy('lt.id')
                ->get();
        }

        return view('accounting.reports.customer_statement', compact('entries', 'customer', 'customers', 'openingBalance', 'startDate', 'endDate'));
    }


    public function cashFlow(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $data = $this->analyticsService->getIndirectCashFlow($startDate, $endDate);

        return view('accounting.reports.cash_flow', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }

    public function accountLedger(Request $request)
    {
        $accountId = $request->account_id;
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $account = ChartOfAccount::find($accountId);
        $accounts = ChartOfAccount::orderBy('account_code')->get();

        $entries = [];
        $openingBalance = 0;

        if ($accountId) {
            // 1. Calculate Opening Balance
            $opening = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select(DB::raw('SUM(le.debit - le.credit) as balance'))
                ->where('le.account_id', $accountId)
                ->where('lt.date', '<', $startDate)
                ->where('lt.status', 'posted')
                ->first();
            
            $openingBalance = $opening->balance ?? 0;
            if ($account->normal_balance == 'credit') {
                $openingBalance = -$openingBalance;
            }

            // 2. Get Transactions for the period
            $entries = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select('lt.id as transaction_id', 'lt.date', 'lt.reference', 'lt.description', 'le.debit', 'le.credit')
                ->where('le.account_id', $accountId)
                ->whereBetween('lt.date', [$startDate, $endDate])
                ->where('lt.status', 'posted')
                ->orderBy('lt.date')
                ->orderBy('lt.id')
                ->get();
        }

        return view('accounting.reports.account_ledger', compact('entries', 'account', 'accounts', 'openingBalance', 'startDate', 'endDate'));
    }

    public function trialBalance(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $report = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_code',
                'coa.account_name',
                DB::raw('SUM(le.debit) as total_debit'),
                DB::raw('SUM(le.credit) as total_credit'),
                DB::raw('SUM(le.debit - le.credit) as balance')
            )
            ->whereBetween('lt.date', [$startDate, $endDate])
            ->where('lt.status', 'posted')
            ->groupBy('coa.id', 'coa.account_code', 'coa.account_name')
            ->get();

        return view('accounting.reports.trial_balance', compact('report', 'startDate', 'endDate'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $revenue = $this->getAccountTypeBalance('Revenue', $startDate, $endDate);
        $expenses = $this->getAccountTypeBalance('Expense', $startDate, $endDate);

        $netProfit = $revenue->sum('balance') - $expenses->sum('balance');

        return view('accounting.reports.profit_loss', compact('revenue', 'expenses', 'netProfit', 'startDate', 'endDate'));
    }

    public function balanceSheet(Request $request)
    {
        $endDate = $request->end_date ?? date('Y-m-d');

        $assets = $this->getAccountTypeBalance('Asset', null, $endDate);
        $liabilities = $this->getAccountTypeBalance('Liability', null, $endDate);
        $equity = $this->getAccountTypeBalance('Equity', null, $endDate);

        // P&L Net Profit for the year to date needs to be added to Equity
        $yearStart = date('Y-01-01', strtotime($endDate));
        $revenue = $this->getAccountTypeBalance('Revenue', $yearStart, $endDate);
        $expenses = $this->getAccountTypeBalance('Expense', $yearStart, $endDate);
        $currentNetProfit = $revenue->sum('balance') - $expenses->sum('balance');

        return view('accounting.reports.balance_sheet', compact('assets', 'liabilities', 'equity', 'currentNetProfit', 'endDate'));
    }

    private function getAccountTypeBalance($type, $startDate, $endDate)
    {
        $query = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_code',
                'coa.account_name',
                'coa.normal_balance',
                DB::raw('SUM(le.debit) as total_debit'),
                DB::raw('SUM(le.credit) as total_credit'),
                DB::raw('SUM(CASE WHEN coa.normal_balance = "debit" THEN (le.debit - le.credit) ELSE (le.credit - le.debit) END) as balance')
            )
            ->where('coa.account_type', $type)
            ->where('lt.status', 'posted');

        if ($startDate) {
            $query->where('lt.date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('lt.date', '<=', $endDate);
        }

        return $query->groupBy('coa.id', 'coa.account_code', 'coa.account_name', 'coa.normal_balance')->get();
    }
}
