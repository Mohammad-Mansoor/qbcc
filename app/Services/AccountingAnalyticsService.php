<?php

namespace App\Services;

use App\ChartOfAccount;
use App\LedgerEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingAnalyticsService
{
    /**
     * Get all dashboard data for a specific date range
     */
    public function getDashboardData($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        
        // Calculate previous period for comparison (same duration)
        $diff = $startDate->diffInDays($endDate);
        $prevStartDate = $startDate->copy()->subDays($diff + 1);
        $prevEndDate = $startDate->copy()->subDay();

        return [
            'period' => [
                'current' => ['start' => $startDate->toDateString(), 'end' => $endDate->toDateString()],
                'previous' => ['start' => $prevStartDate->toDateString(), 'end' => $prevEndDate->toDateString()],
            ],
            'kpis' => $this->getKpiSummary($startDate, $endDate, $prevStartDate, $prevEndDate),
            'profit_loss' => $this->getProfitLossTrends($startDate, $endDate),
            'cash_flow' => $this->getCashFlowTrends($startDate, $endDate),
            'aging' => $this->getAgingAnalysis($endDate),
            'ratios' => $this->getFinancialRatios($endDate),
            'activity' => $this->getTransactionActivity($startDate, $endDate),
        ];
    }

    /**
     * Calculates Revenue, Expenses, Profit, Cash, AR, AP and comparisons
     */
    private function getKpiSummary($start, $end, $pStart, $pEnd)
    {
        $current = $this->getPeriodSummary($start, $end);
        $previous = $this->getPeriodSummary($pStart, $pEnd);

        $kpis = [];
        $metrics = ['revenue', 'expenses', 'profit', 'cash', 'receivables', 'payables', 'inventory'];

        foreach ($metrics as $metric) {
            $curVal = $current[$metric] ?? 0;
            $prevVal = $previous[$metric] ?? 0;
            $change = $prevVal != 0 ? (($curVal - $prevVal) / abs($prevVal)) * 100 : 0;
            
            $kpis[$metric] = [
                'value' => $curVal,
                'previous' => $prevVal,
                'change' => round($change, 2),
                'status' => $change >= 0 ? 'increase' : 'decrease'
            ];
        }

        // Working Capital (Current Assets - Current Liabilities)
        $kpis['working_capital'] = $kpis['receivables']['value'] + $kpis['cash']['value'] - $kpis['payables']['value'];

        return $kpis;
    }

    private function getPeriodSummary($start, $end)
    {
        // 1. P&L Items (Movement within the period)
        $plData = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_type',
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit')
            )
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$start, $end])
            ->whereIn('coa.account_type', ['Revenue', 'Expense'])
            ->groupBy('coa.account_type')
            ->get();

        // 2. Balance Sheet Items (Snapshot at the end of the period)
        $bsData = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_code',
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $end)
            ->whereIn('coa.account_type', ['Asset', 'Liability', 'Equity'])
            ->groupBy('coa.account_code')
            ->get();

        $summary = [
            'revenue' => 0, 'expenses' => 0, 'profit' => 0,
            'cash' => 0, 'receivables' => 0, 'payables' => 0, 'inventory' => 0
        ];

        foreach ($plData as $row) {
            $type = strtolower($row->account_type);
            if ($type == 'revenue' || $type == 'income') {
                $summary['revenue'] += ($row->total_credit - $row->total_debit);
            } elseif ($type == 'expense') {
                $summary['expenses'] += ($row->total_debit - $row->total_credit);
            }
        }
        $summary['profit'] = $summary['revenue'] - $summary['expenses'];

        // BS data uses account codes and report groups
        $bsDataFull = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_code',
                'coa.report_group',
                'coa.is_cash_account',
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $end)
            ->groupBy('coa.id', 'coa.account_code', 'coa.report_group', 'coa.is_cash_account')
            ->get();

        foreach ($bsDataFull as $row) {
            if ($row->is_cash_account) {
                $summary['cash'] += ($row->total_debit - $row->total_credit);
            }
            
            if ($row->report_group == 'Current Asset' && strpos($row->account_code, '13') === 0) {
                $summary['receivables'] += ($row->total_debit - $row->total_credit);
            } elseif ($row->report_group == 'Current Liability' && strpos($row->account_code, '21') === 0) {
                $summary['payables'] += ($row->total_credit - $row->total_debit);
            } elseif ($row->report_group == 'Inventory' || strpos($row->account_code, '14') === 0) {
                $summary['inventory'] += ($row->total_debit - $row->total_credit);
            }
        }

        return $summary;
    }

    private function getProfitLossTrends($start, $end)
    {
        return DB::table('ledger_transactions as lt')
            ->join('ledger_entries as le', 'lt.id', '=', 'le.transaction_id')
            ->join('chart_of_accounts as coa', 'le.account_id', '=', 'coa.id')
            ->select(
                DB::raw("DATE_FORMAT(lt.date, '%Y-%m-%d') as date"),
                DB::raw("SUM(CASE WHEN coa.account_type = 'Revenue' THEN (le.base_credit - le.base_debit) ELSE 0 END) as revenue"),
                DB::raw("SUM(CASE WHEN coa.account_type = 'Expense' THEN (le.base_debit - le.base_credit) ELSE 0 END) as expense")
            )
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getCashFlowTrends($start, $end)
    {
        return DB::table('ledger_transactions as lt')
            ->join('ledger_entries as le', 'lt.id', '=', 'le.transaction_id')
            ->join('chart_of_accounts as coa', 'le.account_id', '=', 'coa.id')
            ->select(
                DB::raw("DATE_FORMAT(lt.date, '%Y-%m-%d') as date"),
                DB::raw("SUM(CASE WHEN coa.is_cash_account = 1 THEN le.base_debit ELSE 0 END) as inflow"),
                DB::raw("SUM(CASE WHEN coa.is_cash_account = 1 THEN le.base_credit ELSE 0 END) as outflow")
            )
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getAgingAnalysis($endDate)
    {
        $transactions = DB::table('ledger_entries as le')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->join('chart_of_accounts as coa', 'le.account_id', '=', 'coa.id')
            ->select(
                'le.party_id',
                'lt.date',
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $endDate)
            ->whereNotNull('le.party_id')
            ->where('coa.account_code', 'LIKE', '13%') // Accounts Receivable
            ->groupBy('le.party_id', 'lt.date')
            ->orderBy('le.party_id')
            ->orderBy('lt.date', 'asc')
            ->get();

        $parties = [];
        foreach ($transactions as $t) {
            if (!isset($parties[$t->party_id])) {
                $parties[$t->party_id] = ['invoices' => [], 'unallocated_payments' => 0];
            }
            if ($t->total_debit > 0) {
                $parties[$t->party_id]['invoices'][] = ['date' => $t->date, 'amount' => $t->total_debit];
            }
            if ($t->total_credit > 0) {
                $parties[$t->party_id]['unallocated_payments'] += $t->total_credit;
            }
        }

        $aging = ['0-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0];
        $now = Carbon::parse($endDate);

        // Apply FIFO allocation
        foreach ($parties as $partyId => $data) {
            $paymentRemaining = $data['unallocated_payments'];
            
            // First, offset oldest invoices
            foreach ($data['invoices'] as &$inv) {
                if ($paymentRemaining <= 0) break;
                
                if ($paymentRemaining >= $inv['amount']) {
                    $paymentRemaining -= $inv['amount'];
                    $inv['amount'] = 0;
                } else {
                    $inv['amount'] -= $paymentRemaining;
                    $paymentRemaining = 0;
                }
            }

            // Bucket remaining open invoice amounts
            foreach ($data['invoices'] as $inv) {
                if ($inv['amount'] > 0) {
                    $days = Carbon::parse($inv['date'])->diffInDays($now);
                    if ($days <= 30) $aging['0-30'] += $inv['amount'];
                    elseif ($days <= 60) $aging['31-60'] += $inv['amount'];
                    elseif ($days <= 90) $aging['61-90'] += $inv['amount'];
                    else $aging['90+'] += $inv['amount'];
                }
            }
            
            // If there's still payment remaining (overpayment/advance), it technically reduces the 0-30 bucket
            if ($paymentRemaining > 0) {
                $aging['0-30'] -= $paymentRemaining;
            }
        }

        return $aging;
    }

    public function getIndirectCashFlow($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // 1. Get Net Profit for the period
        $summary = $this->getPeriodSummary($start, $end);
        $netProfit = $summary['profit'];

        // 2. Calculate Working Capital Changes
        $openingBalances = $this->getBalancesAtDate($start->copy()->subDay());
        $closingBalances = $this->getBalancesAtDate($end);

        // Calculate Depreciation Expense (non-cash)
        $depreciationData = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$start, $end])
            ->where('coa.account_type', 'Expense')
            ->where('coa.account_name', 'LIKE', '%Depreciation%')
            ->select(DB::raw('SUM(le.base_debit - le.base_credit) as total'))
            ->first();
            
        $depreciation = $depreciationData->total ?? 0;

        $arChange = ($closingBalances['receivables'] ?? 0) - ($openingBalances['receivables'] ?? 0);
        $apChange = ($closingBalances['payables'] ?? 0) - ($openingBalances['payables'] ?? 0);
        $invChange = ($closingBalances['inventory'] ?? 0) - ($openingBalances['inventory'] ?? 0);
        $equityChange = ($closingBalances['equity'] ?? 0) - ($openingBalances['equity'] ?? 0);
        $fixedAssetsChange = ($closingBalances['investing'] ?? 0) - ($openingBalances['investing'] ?? 0);

        return [
            'net_profit' => $netProfit,
            'adjustments' => [
                'depreciation' => $depreciation,
                'receivables' => -$arChange,
                'payables' => $apChange,
                'inventory' => -$invChange,
            ],
            'net_cash_operating' => $netProfit + $depreciation - $arChange + $apChange - $invChange,
            'investing' => [
                'fixed_assets' => -$fixedAssetsChange,
            ],
            'net_cash_investing' => -$fixedAssetsChange,
            'financing' => [
                'equity' => $equityChange,
            ],
            'net_cash_financing' => $equityChange,
            'net_change_in_cash' => ($netProfit + $depreciation - $arChange + $apChange - $invChange) - $fixedAssetsChange + $equityChange
        ];
    }

    /**
     * Get Historical Inventory Valuation
     */
    public function getInventoryValuation($date)
    {
        return DB::table('inventory_transactions as it')
            ->join('items', 'it.item_id', '=', 'items.id')
            ->select(
                'items.type as item_type',
                DB::raw("SUM(CASE WHEN direction = 'IN' THEN it.total_cost ELSE -it.total_cost END) as total_value"),
                DB::raw("SUM(CASE WHEN it.is_value_adjustment = 0 AND direction = 'IN' THEN it.quantity WHEN direction = 'OUT' THEN -it.quantity ELSE 0 END) as on_hand_qty")
            )
            ->where('it.status', 1)
            ->where('it.created_at', '<=', Carbon::parse($date)->endOfDay())
            ->groupBy('items.type')
            ->get();
    }

    /**
     * Get Comparative P&L (Current vs Previous)
     */
    public function getComparativePL($start, $end)
    {
        $current = $this->getPeriodSummary($start, $end);
        
        $startP = Carbon::parse($start)->subYear();
        $endP = Carbon::parse($end)->subYear();
        $previous = $this->getPeriodSummary($startP, $endP);

        return [
            'current' => $current,
            'previous' => $previous,
            'variance' => [
                'revenue' => $current['revenue'] - $previous['revenue'],
                'expenses' => $current['expenses'] - $previous['expenses'],
                'profit' => $current['profit'] - $previous['profit'],
            ]
        ];
    }

    /**
     * Get FX Exposure Analysis
     */
    public function getFXExposure($date)
    {
        return DB::table('ledger_entries as le')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'le.currency_code',
                DB::raw("SUM(CASE WHEN le.debit > 0 THEN le.original_amount ELSE -le.original_amount END) as net_balance_original"),
                DB::raw("SUM(le.base_currency_amount) as net_balance_base")
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $date)
            ->groupBy('le.currency_code')
            ->get();
    }

    /**
     * Get Cost Center Performance
     */
    public function getCostCenterPerformance($start, $end)
    {
        return DB::table('cost_centers as cc')
            ->leftJoin('ledger_entries as le', 'cc.id', '=', 'le.cost_center_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->join('chart_of_accounts as coa', 'le.account_id', '=', 'coa.id')
            ->select(
                'cc.name',
                DB::raw("SUM(CASE WHEN coa.account_type = 'Revenue' THEN (le.base_credit - le.base_debit) ELSE 0 END) as revenue"),
                DB::raw("SUM(CASE WHEN coa.account_type = 'Expense' THEN (le.base_debit - le.base_credit) ELSE 0 END) as expenses")
            )
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$start, $end])
            ->groupBy('cc.id', 'cc.name')
            ->get()
            ->map(function($item) {
                $item->profit = $item->revenue - $item->expenses;
                return $item;
            });
    }

    private function getBalancesAtDate($date)
    {
        $data = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_code',
                'coa.report_group',
                'coa.cashflow_group',
                'coa.account_type',
                DB::raw('SUM(le.base_debit - le.base_credit) as balance_debit_base'),
                DB::raw('SUM(le.base_credit - le.base_debit) as balance_credit_base')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $date)
            ->groupBy('coa.id', 'coa.account_code', 'coa.report_group', 'coa.cashflow_group', 'coa.account_type')
            ->get();

        $balances = ['receivables' => 0, 'payables' => 0, 'inventory' => 0, 'equity' => 0, 'investing' => 0];

        foreach ($data as $row) {
            $type = strtolower($row->account_type);
            
            // Logic based on Tags first, then fallback to code ranges
            if ($row->report_group == 'Current Asset' && strpos($row->account_code, '13') === 0) {
                $balances['receivables'] += $row->balance_debit_base;
            } elseif ($row->report_group == 'Current Liability' && strpos($row->account_code, '21') === 0) {
                $balances['payables'] += $row->balance_credit_base;
            } elseif ($row->report_group == 'Inventory' || strpos($row->account_code, '14') === 0) {
                $balances['inventory'] += $row->balance_debit_base;
            } elseif ($type == 'equity' || strpos($row->account_code, '3') === 0) {
                $balances['equity'] += $row->balance_credit_base;
            } elseif ($row->cashflow_group == 'Investing' || strpos($row->account_code, '15') === 0) {
                $balances['investing'] += $row->balance_debit_base;
            }
        }

        return $balances;
    }

    private function getFinancialRatios($endDate)
    {
        $total = $this->getPeriodSummary(Carbon::parse($endDate)->startOfYear(), $endDate);
        
        return [
            'liquidity' => [
                'current_ratio' => $total['payables'] != 0 ? round(($total['receivables'] + $total['cash'] + $total['inventory']) / $total['payables'], 2) : 0,
                'quick_ratio' => $total['payables'] != 0 ? round(($total['receivables'] + $total['cash']) / $total['payables'], 2) : 0,
            ],
            'profitability' => [
                'net_margin' => $total['revenue'] != 0 ? round(($total['profit'] / $total['revenue']) * 100, 2) : 0,
            ]
        ];
    }

    private function getTransactionActivity($start, $end)
    {
        return DB::table('ledger_transactions')
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date"),
                DB::raw("COUNT(*) as count")
            )
            ->whereBetween('date', [$start, $end])
            ->where('status', 'posted')
            ->groupBy('date')
            ->get();
    }
}
