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
                ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
                ->where('le.party_type', 'App\Customer')
                ->where('le.party_id', $customerId)
                ->where('lt.date', '<', $startDate)
                ->where('lt.status', 'posted')
                ->first();
            
            $openingBalance = $opening->balance ?? 0;

            // 2. Get Transactions for the period
            $entries = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select('lt.id as transaction_id', 'lt.date', 'lt.reference', 'lt.description', 'le.base_debit as debit', 'le.base_credit as credit', 'le.currency_code', 'le.original_amount')
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


    private function classifyAccountForCashFlow($account)
    {
        if (!empty($account->cashflow_group)) {
            return $account->cashflow_group;
        }

        $type = strtolower($account->account_type);
        $code = $account->account_code;
        $group = $account->report_group;

        if ($type === 'asset') {
            if ($group === 'Fixed Asset' || $group === 'Fixed Assets' || $group === 'Investing' || strpos($code, '15') === 0) {
                return 'Investing';
            }
            return 'Operating';
        }

        if ($type === 'liability') {
            if ($group === 'Long-term Liability' || $group === 'Financing' || strpos($code, '22') === 0 || strpos($code, '25') === 0) {
                return 'Financing';
            }
            return 'Operating';
        }

        if ($type === 'equity') {
            return 'Financing';
        }

        return 'Operating';
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        // Sanitize string date formats to prevent any SQL injection or formatting issues
        if (!is_string($startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-m-01');
        }
        if (!is_string($endDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-d');
        }

        // 1. Calculate Beginning Cash Balance (posted ledger entries before start date)
        $openingCashQuery = DB::table('chart_of_accounts as coa')
            ->join('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->where('coa.is_cash_account', 1)
            ->where('lt.status', 'posted')
            ->where('lt.date', '<', $startDate)
            ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
            ->first();
        $beginningCash = (float)($openingCashQuery->balance ?? 0);

        // 2. Calculate Ending Cash Balance (posted ledger entries up to end date)
        $closingCashQuery = DB::table('chart_of_accounts as coa')
            ->join('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->where('coa.is_cash_account', 1)
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $endDate)
            ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
            ->first();
        $endingCash = (float)($closingCashQuery->balance ?? 0);
        $actualNetCashChange = $endingCash - $beginningCash;

        // 3. Calculate Net Profit for the period (Revenues - Expenses)
        $plQuery = DB::table('chart_of_accounts as coa')
            ->join('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$startDate, $endDate])
            ->whereIn('coa.account_type', ['Revenue', 'Expense'])
            ->select(
                'coa.account_type',
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit')
            )
            ->groupBy('coa.account_type')
            ->get();

        $revenueSum = 0;
        $expenseSum = 0;
        foreach ($plQuery as $row) {
            $type = strtolower($row->account_type);
            if ($type === 'revenue') {
                $revenueSum += ((float)$row->total_credit - (float)$row->total_debit);
            } elseif ($type === 'expense') {
                $expenseSum += ((float)$row->total_debit - (float)$row->total_credit);
            }
        }
        $netProfit = $revenueSum - $expenseSum;

        // 4. Retrieve Non-Cash Balance Sheet Accounts with their opening/closing net debit balances
        $nonCashAccounts = DB::table('chart_of_accounts as coa')
            ->where('coa.is_cash_account', 0)
            ->whereIn('coa.account_type', ['Asset', 'Liability', 'Equity'])
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', function ($join) {
                $join->on('le.transaction_id', '=', 'lt.id')
                     ->where('lt.status', '=', 'posted');
            })
            ->select(
                'coa.id',
                'coa.account_code',
                'coa.account_name',
                'coa.account_type',
                'coa.report_group',
                'coa.cashflow_group',
                'coa.normal_balance',
                DB::raw("SUM(CASE WHEN lt.date < '$startDate' THEN (le.base_debit - le.base_credit) ELSE 0 END) as open_balance_debit_base"),
                DB::raw("SUM(CASE WHEN lt.date <= '$endDate' THEN (le.base_debit - le.base_credit) ELSE 0 END) as close_balance_debit_base")
            )
            ->groupBy(
                'coa.id',
                'coa.account_code',
                'coa.account_name',
                'coa.account_type',
                'coa.report_group',
                'coa.cashflow_group',
                'coa.normal_balance'
            )
            ->get();

        $operatingAdjustments = [
            'receivables' => 0.0,
            'inventory' => 0.0,
            'payables' => 0.0,
            'other' => 0.0,
        ];
        $investingAdjustments = [
            'fixed_assets' => 0.0,
            'other' => 0.0,
        ];
        $financingAdjustments = [
            'equity' => 0.0,
            'retained_earnings' => 0.0,
            'other' => 0.0,
        ];

        foreach ($nonCashAccounts as $account) {
            $openBalance = (float)$account->open_balance_debit_base;
            $closeBalance = (float)$account->close_balance_debit_base;
            $changeDebit = $closeBalance - $openBalance;
            $effect = -$changeDebit;

            $group = $this->classifyAccountForCashFlow($account);

            if ($group === 'Operating') {
                if (strpos($account->account_code, '13') === 0) {
                    $operatingAdjustments['receivables'] += $effect;
                } elseif (strpos($account->account_code, '14') === 0) {
                    $operatingAdjustments['inventory'] += $effect;
                } elseif (strpos($account->account_code, '21') === 0) {
                    $operatingAdjustments['payables'] += $effect;
                } else {
                    $operatingAdjustments['other'] += $effect;
                }
            } elseif ($group === 'Investing') {
                if (strpos($account->account_code, '15') === 0) {
                    $investingAdjustments['fixed_assets'] += $effect;
                } else {
                    $investingAdjustments['other'] += $effect;
                }
            } elseif ($group === 'Financing') {
                if ($account->account_code === '3200' || strtolower($account->account_name) === 'retained earnings' || strpos(strtolower($account->account_name), 'retained earnings') !== false) {
                    // Adjust Retained Earnings for Net Profit to avoid double counting
                    $directEquityAdjustment = $effect - $netProfit;
                    $financingAdjustments['retained_earnings'] += $directEquityAdjustment;
                } elseif (strpos($account->account_code, '3') === 0) {
                    $financingAdjustments['equity'] += $effect;
                } else {
                    $financingAdjustments['other'] += $effect;
                }
            }
        }

        $netCashOperating = $netProfit + array_sum($operatingAdjustments);
        $netCashInvesting = array_sum($investingAdjustments);
        $netCashFinancing = array_sum($financingAdjustments);
        $netChangeInCash = $netCashOperating + $netCashInvesting + $netCashFinancing;

        $data = [
            'net_profit' => $netProfit,
            'adjustments' => [
                'receivables' => $operatingAdjustments['receivables'],
                'inventory' => $operatingAdjustments['inventory'],
                'payables' => $operatingAdjustments['payables'],
                'other' => $operatingAdjustments['other'],
            ],
            'net_cash_operating' => $netCashOperating,
            'investing' => [
                'fixed_assets' => $investingAdjustments['fixed_assets'],
                'other' => $investingAdjustments['other'],
            ],
            'net_cash_investing' => $netCashInvesting,
            'financing' => [
                'equity' => $financingAdjustments['equity'],
                'retained_earnings' => $financingAdjustments['retained_earnings'],
                'other' => $financingAdjustments['other'],
            ],
            'net_cash_financing' => $netCashFinancing,
            'net_change_in_cash' => $netChangeInCash,
            'beginning_cash' => $beginningCash,
            'ending_cash' => $endingCash,
            'actual_change_in_cash' => $actualNetCashChange,
            'reconciled' => abs($netChangeInCash - $actualNetCashChange) < 0.01,
        ];

        return view('accounting.reports.cash_flow', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }

    public function accountLedger(Request $request)
    {
        $accountId = $request->account_id;
        $sourceId = $request->source_id;
        $sourceType = $request->source_type;
        if ($sourceType == 'ORD') $sourceType = 'App\CustomerOrderDetails';
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $account = $accountId ? ChartOfAccount::find($accountId) : null;
        $accounts = ChartOfAccount::orderBy('account_code')->get();

        $entries = [];
        $openingBalance = 0;

        // If filtering by source (e.g. from the Order screen), we don't necessarily need an account_id
        if ($accountId || ($sourceId && $sourceType)) {
            $query = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select('lt.id as transaction_id', 'lt.date', 'lt.reference', 'lt.description', 'le.base_debit as debit', 'le.base_credit as credit', 'le.account_id', 'le.currency_code', 'le.original_amount');

            if ($accountId) {
                // 1. Calculate Opening Balance for Account
                $opening = DB::table('ledger_entries as le')
                    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                    ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
                    ->where('le.account_id', $accountId)
                    ->where('lt.date', '<', $startDate)
                    ->where('lt.status', 'posted')
                    ->first();
                
                $openingBalance = $opening->balance ?? 0;
                if ($account && $account->normal_balance == 'credit') {
                    $openingBalance = -$openingBalance;
                }
                
                $query->where('le.account_id', $accountId);
            }

            if ($sourceId && $sourceType) {
                $query->where('lt.source_type', $sourceType)
                      ->where('lt.source_id', $sourceId);
            }

            $entries = $query->whereBetween('lt.date', [$startDate, $endDate])
                ->where('lt.status', 'posted')
                ->orderBy('lt.date')
                ->orderBy('lt.id')
                ->get();
        }

        $currencies = \App\Currency::all()->keyBy('code');
        return view('accounting.reports.account_ledger', compact('entries', 'account', 'accounts', 'openingBalance', 'startDate', 'endDate', 'currencies'));
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
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit'),
                DB::raw('SUM(le.base_debit - le.base_credit) as balance')
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
        $currencies = \App\Currency::where('is_active', 1)->get();
        
        $currencyCode = $request->get('currency', 'USD');
        $rate = 1.0;
        $currObj = $currencies->where('code', $currencyCode)->first();
        if ($currObj) {
            $rate = floatval($currObj->exchange_rate);
        } else {
            $currencyCode = 'USD';
        }

        return view('accounting.reports.profit_loss', compact('revenue', 'expenses', 'netProfit', 'startDate', 'endDate', 'currencies', 'currencyCode', 'rate'));
    }

    public function balanceSheet(Request $request)
    {
        $endDate = $request->end_date ?? date('Y-m-d');

        $assets = $this->getAccountTypeBalance('Asset', null, $endDate);
        $liabilities = $this->getAccountTypeBalance('Liability', null, $endDate);
        $equity = $this->getAccountTypeBalance('Equity', null, $endDate);

        // P&L Lifetime Net Profit up to the end date ensures the Balance Sheet stays balanced
        // (Assets = Liabilities + Equity + Lifetime Profit)
        $revenue = $this->getAccountTypeBalance('Revenue', null, $endDate);
        $expenses = $this->getAccountTypeBalance('Expense', null, $endDate);
        $currentNetProfit = $revenue->sum('balance') - $expenses->sum('balance');

        return view('accounting.reports.balance_sheet', compact('assets', 'liabilities', 'equity', 'currentNetProfit', 'endDate'));
    }

    /**
     * Advanced Reports (Dari Afghanistan)
     */

    public function comparativePL(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-m-d');
        
        $data = $this->analyticsService->getComparativePL($startDate, $endDate);
        
        return view('accounting.reports.comparative_pl', array_merge($data, compact('startDate', 'endDate')));
    }

    public function inventoryValuation(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $report = $this->analyticsService->getInventoryValuation($date);
        
        return view('accounting.reports.inventory_valuation', compact('report', 'date'));
    }

    public function fxExposure(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $report = $this->analyticsService->getFXExposure($date);
        
        return view('accounting.reports.fx_exposure', compact('report', 'date'));
    }

    public function costCenterPerformance(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-m-d');
        $report = $this->analyticsService->getCostCenterPerformance($startDate, $endDate);
        
        return view('accounting.reports.cost_center_performance', compact('report', 'startDate', 'endDate'));
    }

    public function auditCorrections(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-m-d');
        
        $report = \App\LedgerTransaction::with(['entries.account'])
            ->where('reference', 'LIKE', 'REV-%')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        // Eager load original transactions
        $originalIds = $report->pluck('reversed_transaction_id')->filter()->unique();
        $originals = \App\LedgerTransaction::with(['entries.account'])
            ->whereIn('id', $originalIds)
            ->get()
            ->keyBy('id');
            
        foreach ($report as $row) {
            // Attach original transaction
            $row->original_tx = $originals->get($row->reversed_transaction_id);
            
            // Calculate total amount (financial impact)
            $row->total_amount = $row->entries->sum('base_debit');
            
            // Get currency code (default to USD/AFN from first entry)
            $row->currency_code = $row->entries->first()->currency_code ?? 'USD';
            
            // Try to find user via Activity logs within 30 seconds
            $activityUser = DB::table('activities')
                ->join('users', 'activities.user_id', '=', 'users.id')
                ->whereRaw('ABS(TIMESTAMPDIFF(SECOND, activities.created_at, ?)) <= 30', [$row->created_at->toDateTimeString()])
                ->select('users.name')
                ->first();
                
            $row->operator_name = $activityUser ? $activityUser->name : 'System';
            
            if ($row->operator_name === 'System' && $row->source_type === 'App\Sale') {
                $saleUser = DB::table('sales')
                    ->leftJoin('users', 'sales.returned_by', '=', 'users.id')
                    ->where('sales.id', $row->source_id)
                    ->select('users.name')
                    ->first();
                if ($saleUser && $saleUser->name) {
                    $row->operator_name = $saleUser->name;
                }
            }
        }
            
        return view('accounting.reports.audit_corrections', compact('report', 'startDate', 'endDate'));
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
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit'),
                DB::raw('SUM(CASE WHEN coa.normal_balance = "debit" THEN (le.base_debit - le.base_credit) ELSE (le.base_credit - le.base_debit) END) as balance')
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
