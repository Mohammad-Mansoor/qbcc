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
        $metrics = ['revenue', 'expenses', 'profit', 'cash', 'receivables', 'payables'];

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
                DB::raw('SUM(le.debit) as total_debit'),
                DB::raw('SUM(le.credit) as total_credit')
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
                DB::raw('SUM(le.debit) as total_debit'),
                DB::raw('SUM(le.credit) as total_credit')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $end)
            ->whereIn('coa.account_type', ['Asset', 'Liability', 'Equity'])
            ->groupBy('coa.account_code')
            ->get();

        $summary = [
            'revenue' => 0, 'expenses' => 0, 'profit' => 0,
            'cash' => 0, 'receivables' => 0, 'payables' => 0
        ];

        foreach ($plData as $row) {
            if ($row->account_type == 'Revenue') {
                $summary['revenue'] += ($row->total_credit - $row->total_debit);
            } elseif ($row->account_type == 'Expense') {
                $summary['expenses'] += ($row->total_debit - $row->total_credit);
            }
        }
        $summary['profit'] = $summary['revenue'] - $summary['expenses'];

        foreach ($bsData as $row) {
            if (strpos($row->account_code, '11') === 0 || strpos($row->account_code, '12') === 0) {
                $summary['cash'] += ($row->total_debit - $row->total_credit);
            } elseif (strpos($row->account_code, '13') === 0) {
                $summary['receivables'] += ($row->total_debit - $row->total_credit);
            } elseif (strpos($row->account_code, '21') === 0) {
                $summary['payables'] += ($row->total_credit - $row->total_debit);
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
                DB::raw("SUM(CASE WHEN coa.account_type = 'Revenue' THEN (le.credit - le.debit) ELSE 0 END) as revenue"),
                DB::raw("SUM(CASE WHEN coa.account_type = 'Expense' THEN (le.debit - le.credit) ELSE 0 END) as expense")
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
                DB::raw("SUM(CASE WHEN coa.is_cash_account = 1 THEN le.debit ELSE 0 END) as inflow"),
                DB::raw("SUM(CASE WHEN coa.is_cash_account = 1 THEN le.credit ELSE 0 END) as outflow")
            )
            ->where('lt.status', 'posted')
            ->whereBetween('lt.date', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getAgingAnalysis($endDate)
    {
        // Simple Aging by balance per party
        $parties = DB::table('ledger_entries as le')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->join('chart_of_accounts as coa', 'le.account_id', '=', 'coa.id')
            ->select(
                'le.party_type',
                'le.party_id',
                'lt.date',
                DB::raw('SUM(le.debit - le.credit) as balance')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $endDate)
            ->whereNotNull('le.party_id')
            ->where('coa.account_code', 'LIKE', '13%') // Accounts Receivable
            ->groupBy('le.party_type', 'le.party_id', 'lt.date')
            ->get();

        $aging = ['0-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0];
        $now = Carbon::parse($endDate);

        foreach ($parties as $p) {
            $days = Carbon::parse($p->date)->diffInDays($now);
            if ($days <= 30) $aging['0-30'] += $p->balance;
            elseif ($days <= 60) $aging['31-60'] += $p->balance;
            elseif ($days <= 90) $aging['61-90'] += $p->balance;
            else $aging['90+'] += $p->balance;
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
        // We need balances at the exact start and exact end
        $openingBalances = $this->getBalancesAtDate($start->copy()->subDay());
        $closingBalances = $this->getBalancesAtDate($end);

        $arChange = ($closingBalances['receivables'] ?? 0) - ($openingBalances['receivables'] ?? 0);
        $apChange = ($closingBalances['payables'] ?? 0) - ($openingBalances['payables'] ?? 0);
        $invChange = ($closingBalances['inventory'] ?? 0) - ($openingBalances['inventory'] ?? 0);

        return [
            'net_profit' => $netProfit,
            'adjustments' => [
                'receivables' => -$arChange, // Increase in Asset = Cash Outflow
                'payables' => $apChange,     // Increase in Liability = Cash Inflow
                'inventory' => -$invChange,  // Increase in Asset = Cash Outflow
            ],
            'net_cash_operating' => $netProfit - $arChange + $apChange - $invChange
        ];
    }

    private function getBalancesAtDate($date)
    {
        $data = DB::table('chart_of_accounts as coa')
            ->leftJoin('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->leftJoin('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->select(
                'coa.account_code',
                DB::raw('SUM(le.debit - le.credit) as balance_debit_base'),
                DB::raw('SUM(le.credit - le.debit) as balance_credit_base')
            )
            ->where('lt.status', 'posted')
            ->where('lt.date', '<=', $date)
            ->groupBy('coa.account_code')
            ->get();

        $balances = ['receivables' => 0, 'payables' => 0, 'inventory' => 0];

        foreach ($data as $row) {
            if (strpos($row->account_code, '13') === 0) {
                $balances['receivables'] += $row->balance_debit_base;
            } elseif (strpos($row->account_code, '21') === 0) {
                $balances['payables'] += $row->balance_credit_base;
            } elseif (strpos($row->account_code, '14') === 0) {
                $balances['inventory'] += $row->balance_debit_base;
            }
        }

        return $balances;
    }

    private function getFinancialRatios($endDate)
    {
        $total = $this->getPeriodSummary(Carbon::parse($endDate)->startOfYear(), $endDate);
        
        return [
            'liquidity' => [
                'current_ratio' => $total['payables'] != 0 ? round(($total['receivables'] + $total['cash']) / $total['payables'], 2) : 0,
                'quick_ratio' => $total['payables'] != 0 ? round($total['cash'] / $total['payables'], 2) : 0,
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
