<?php

namespace App\Services\Dashboard;

use App\Carpet;
use App\Sale;
use App\OfficeDebit;
use App\OfficeCashBook;
use App\AgentPayment;
use App\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceDashboardService
{
    protected $executiveService;

    public function __construct(ExecutiveDashboardService $executiveService)
    {
        $this->executiveService = $executiveService;
    }

    public function getAnalytics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Base Financial KPIs
        $revenue = Sale::whereYear('sale_date', $thisYear)->sum('sale_cost_total') ?? 0;
        $expenses = OfficeDebit::whereYear('date', $thisYear)->sum('amount') ?? 0;
        $netProfit = $revenue - $expenses;

        // Reusing logic from Executive Service for basic totals
        $executiveKpis = $this->executiveService->getKPIs();
        
        $receivables = $executiveKpis['accounts_receivable'] ?? 0;
        $payables = $executiveKpis['accounts_payable'] ?? 0;
        $inventoryValue = $executiveKpis['total_inventory_value'] ?? 0;
        $cashAndBank = $executiveKpis['cash_and_bank'] ?? 0;

        $assets = $cashAndBank + $inventoryValue + $receivables;
        $liabilities = $payables;
        $equity = $assets - $liabilities;

        // Cash Flow Analytics (Last 6 Months)
        $cashFlow = [
            'months' => [],
            'inflow' => [],
            'outflow' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $cashFlow['months'][] = $date->format('M');
            
            $monthlyRev = Sale::whereYear('sale_date', $date->year)->whereMonth('sale_date', $date->month)->sum('sale_cost_total') ?? 0;
            $monthlyExp = OfficeDebit::whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount') ?? 0;
            
            $cashFlow['inflow'][] = $monthlyRev;
            $cashFlow['outflow'][] = $monthlyExp;
        }

        // Expense Breakdown by actual OfficeDebit descriptions
        $expenseCategories = [
            'Yarn (تار)' => 0,
            'Dye (رنگ)' => 0,
            'Salaries (معاش)' => 0,
            'Utilities (برق/آب)' => 0,
            'Rent (کرایه)' => 0,
            'Misc (سایر)' => 0
        ];
        
        $allExpenses = OfficeDebit::whereYear('date', $thisYear)->get();
        foreach ($allExpenses as $exp) {
            $desc = mb_strtolower($exp->description ?? '');
            if (mb_strpos($desc, 'yarn') !== false || mb_strpos($desc, 'تار') !== false) {
                $expenseCategories['Yarn (تار)'] += $exp->amount;
            } elseif (mb_strpos($desc, 'dye') !== false || mb_strpos($desc, 'رنگ') !== false) {
                $expenseCategories['Dye (رنگ)'] += $exp->amount;
            } elseif (mb_strpos($desc, 'salary') !== false || mb_strpos($desc, 'معاش') !== false || mb_strpos($desc, 'مزد') !== false) {
                $expenseCategories['Salaries (معاش)'] += $exp->amount;
            } elseif (mb_strpos($desc, 'electric') !== false || mb_strpos($desc, 'برق') !== false || mb_strpos($desc, 'آب') !== false) {
                $expenseCategories['Utilities (برق/آب)'] += $exp->amount;
            } elseif (mb_strpos($desc, 'rent') !== false || mb_strpos($desc, 'کرایه') !== false) {
                $expenseCategories['Rent (کرایه)'] += $exp->amount;
            } else {
                $expenseCategories['Misc (سایر)'] += $exp->amount;
            }
        }

        // Aging Reports - Unpaid invoices tracking does not have a native aging table,
        // so we place the entire balance in the current period to avoid fake data.
        $arAging = [
            '0-30' => $receivables,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0
        ];

        $apAging = [
            '0-30' => $payables,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0
        ];

        return [
            'kpis' => [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'net_profit' => $netProfit,
            ],
            'cash_flow' => $cashFlow,
            'expense_breakdown' => $expenseCategories,
            'aging' => [
                'ar' => $arAging,
                'ap' => $apAging
            ]
        ];
    }
}
