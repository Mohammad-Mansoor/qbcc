<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\ChartOfAccount;
use App\LedgerEntry;
use App\LedgerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingAnalyticsService;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AccountingAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $data = $this->analyticsService->getDashboardData($startDate, $endDate);

        // Map service KPIs to the view's 'metrics' variable to fix the error
        $metrics = [
            'cash_on_hand' => $data['kpis']['cash']['value'] ?? 0,
            'total_receivables' => $data['kpis']['receivables']['value'] ?? 0,
            'inventory_value' => $data['kpis']['inventory']['value'] ?? 0,
            'mtd_revenue' => $data['kpis']['revenue']['value'] ?? 0,
            'total_payables' => $data['kpis']['payables']['value'] ?? 0,
        ];

        // Recent Transactions for the list
        $recentTransactions = LedgerTransaction::with('entries.account')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Inventory Distribution for Donut Chart
        $inventoryData = $this->analyticsService->getInventoryValuation($endDate);
        $rawVal = 0;
        $finishedVal = 0;

        foreach ($inventoryData as $inv) {
            $type = strtolower($inv->item_type ?? '');
            if ($type == 'raw_material' || $type == 'material' || strpos($type, 'raw') !== false || $type == 'تار' || $type == 'خامه') {
                $rawVal += $inv->total_value;
            } else {
                // Default everything else (like carpets) to finished
                $finishedVal += $inv->total_value;
            }
        }

        $inventoryDist = [
            'raw' => $rawVal,
            'finished' => $finishedVal,
            'labels' => ['قالین آماده (Finished)', 'مواد خام (Raw)']
        ];

        // Cash Accounts Breakdown
        $cashAccountsData = ChartOfAccount::where('is_cash_account', 1)->get();
        $currencyRates = DB::table('currencies')->pluck('exchange_rate', 'code')->toArray();
        $cashAccounts = [];
        foreach ($cashAccountsData as $acc) {
            $base_balance = DB::table('ledger_entries')
                ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                ->where('ledger_entries.account_id', $acc->id)
                ->where('ledger_transactions.status', 'posted')
                ->sum(DB::raw('base_debit - base_credit'));

            $rate = floatval($currencyRates[$acc->currency ?? 'USD'] ?? 1.0);
            if ($rate <= 0) $rate = 1.0; // Prevent division by zero
            $balance = $base_balance / $rate;

            $cashAccounts[] = [
                'name' => $acc->account_name,
                'balance' => $balance,
                'currency' => $acc->currency ?? 'USD',
                'base_balance' => $base_balance
            ];
        }

        // Get Lock Date
        $lockDate = DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value');

        // NEW: Top 5 Expenses for the current period
        $topExpenses = DB::table('ledger_entries')
            ->join('chart_of_accounts', 'ledger_entries.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.account_type', 'Expense')
            ->whereBetween('ledger_entries.created_at', [$startDate, $endDate])
            ->select('chart_of_accounts.account_name', DB::raw('SUM(debit - credit) as total'))
            ->groupBy('chart_of_accounts.account_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // NEW: Profitability by Carpet Type (Simplified)
        $profitability = DB::table('sales')
            ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
            ->select('carpets.field as type', DB::raw('SUM(sales.profit) as total_profit'))
            ->groupBy('carpets.field')
            ->orderBy('total_profit', 'desc')
            ->limit(5)
            ->get();

        // Get Latest Exchange Rate (for Currency Toggle)
        $exchangeRate = DB::table('financial_settings')->where('key', 'default_usd_rate')->value('value') ?? 80;

        return view('accounting.dashboard', array_merge($data, [
            'metrics' => $metrics,
            'recentTransactions' => $recentTransactions,
            'inventoryDist' => $inventoryDist,
            'cashAccounts' => $cashAccounts,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'lockDate' => $lockDate,
            'topExpenses' => $topExpenses,
            'profitability' => $profitability,
            'exchangeRate' => $exchangeRate,
            'currencies' => \App\Currency::where('is_active', 1)->get()
        ]));
    }
}
