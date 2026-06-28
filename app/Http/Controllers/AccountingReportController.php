<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\LedgerEntry;
use App\Invoice;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingReportController extends Controller
{
    /**
     * Trial Balance Report
     */
    public function trialBalance(Request $request)
    {
        $asOfDate = $request->get('as_of', Carbon::today()->format('Y-m-d'));

        $accounts = ChartOfAccount::orderBy('account_code')->get()->map(function ($account) use ($asOfDate) {
            $totals = LedgerEntry::whereHas('transaction', function ($q) use ($asOfDate) {
                $q->where('date', '<=', $asOfDate)
                  ->where('status', 'posted');
            })
            ->where('account_id', $account->id)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

            $account->total_debit = $totals->total_debit ?? 0;
            $account->total_credit = $totals->total_credit ?? 0;
            $account->balance = $account->total_debit - $account->total_credit;
            
            // Adjust balance sign based on normal account type
            if ($account->account_type == 'liability' || $account->account_type == 'equity' || $account->account_type == 'revenue') {
                $account->display_balance = $account->total_credit - $account->total_debit;
            } else {
                $account->display_balance = $account->total_debit - $account->total_credit;
            }

            return $account;
        })->filter(function($account) {
            return abs($account->total_debit) > 0 || abs($account->total_credit) > 0;
        });

        if ($request->export === 'pdf') {
            $topHeaderPath = public_path('images/header.png');
            $bottomFooterPath = public_path('images/footer.png');
            
            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }
            
            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }

            return view('accounting.reports.trial-balance-pdf', compact('accounts', 'asOfDate', 'topHeaderBase64', 'bottomFooterBase64'));
        } elseif ($request->export === 'excel') {
            return view('accounting.reports.trial-balance-excel', compact('accounts', 'asOfDate'));
        }
        return view('accounting.reports.trial-balance', compact('accounts', 'asOfDate'));
    }

    /**
     * Income Statement (P&L)
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $revenueAccounts = ChartOfAccount::where('account_type', 'revenue')->orderBy('account_code')->get();
        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')->orderBy('account_code')->get();

        $fetchBalances = function ($accounts) use ($startDate, $endDate) {
            return $accounts->map(function ($account) use ($startDate, $endDate) {
                $totals = LedgerEntry::whereHas('transaction', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->where('status', 'posted');
                })
                ->where('account_id', $account->id)
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

                $account->balance = ($account->account_type == 'revenue') 
                    ? ($totals->total_credit - $totals->total_debit)
                    : ($totals->total_debit - $totals->total_credit);
                
                return $account;
            })->filter(fn($a) => abs($a->balance) > 0.01);
        };

        $revenue = $fetchBalances($revenueAccounts);
        $expenses = $fetchBalances($expenseAccounts);

        $totalRevenue = $revenue->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;

        return view('accounting.reports.income-statement', compact('revenue', 'expenses', 'totalRevenue', 'totalExpenses', 'netIncome', 'startDate', 'endDate'));
    }

    /**
     * Accounts Receivable Aging Report
     */
    public function arAging(Request $request)
    {
        $asOfDate = $request->get('as_of', Carbon::today()->format('Y-m-d'));
        $asOfCarbon = Carbon::parse($asOfDate);

        $customers = Customer::all()->map(function ($customer) use ($asOfDate, $asOfCarbon) {
            $customerInvoices = Invoice::where('customer_id', $customer->id)
                ->where('invoice_date', '<=', $asOfDate)
                ->get();

            $aging = [
                'current' => 0,
                '31_60'   => 0,
                '61_90'   => 0,
                '90_plus' => 0,
            ];

            $totalOutstanding = 0;

            foreach ($customerInvoices as $invoice) {
                $invoiceTotal = DB::table('sales')->where('invoice_id', $invoice->id)->where('is_returned', 0)->sum('sale_cost_total');
                
                $payments = DB::table('invoice_payments')
                    ->where('invoice_id', $invoice->id)
                    ->join('customer_payments', 'invoice_payments.payment_id', '=', 'customer_payments.id')
                    ->where('customer_payments.date', '<=', $asOfDate)
                    ->sum('invoice_payments.amount_applied');
                
                $balance = $invoiceTotal - $payments;

                if ($balance <= 0.01) continue;

                $days = Carbon::parse($invoice->invoice_date)->diffInDays($asOfCarbon);

                if ($days <= 30) {
                    $aging['current'] += $balance;
                } elseif ($days <= 60) {
                    $aging['31_60'] += $balance;
                } elseif ($days <= 90) {
                    $aging['61_90'] += $balance;
                } else {
                    $aging['90_plus'] += $balance;
                }
                
                $totalOutstanding += $balance;
            }

            $customer->total_outstanding = $totalOutstanding;
            $customer->aging = $aging;

            return $customer;
        })->filter(function ($customer) {
            return $customer->total_outstanding > 0.01;
        });

        return view('accounting.reports.ar-aging', compact('customers', 'asOfDate'));
    }

    /**
     * Financial Dashboard
     */
    public function dashboard()
    {
        $fetchBalance = function($code) {
            $account = ChartOfAccount::where('account_code', $code)->first();
            if (!$account) return 0;
            
            $totals = LedgerEntry::whereHas('transaction', fn($q) => $q->where('status', 'posted'))
                ->where('account_id', $account->id)
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();
                
            $bal = $totals->total_debit - $totals->total_credit;
            return ($account->account_type == 'liability' || $account->account_type == 'equity' || $account->account_type == 'revenue') 
                ? -$bal : $bal;
        };

        // 1. Core Metrics
        $metrics = [
            'cash_on_hand' => $fetchBalance('1000'),
            'total_receivables' => $fetchBalance('1200'),
            'total_payables' => $fetchBalance('2100'),
            'inventory_value' => $fetchBalance('1510') + $fetchBalance('1520'),
        ];

        // 2. KPIs & Comparison
        $startOfMonth = now()->startOfMonth();
        $endOfToday = now();
        $startOfPrevMonth = now()->subMonth()->startOfMonth();
        $endOfPrevMonth = now()->subMonth()->endOfMonth();

        $fetchRevenue = function($start, $end) {
            return LedgerEntry::whereHas('transaction', fn($q) => $q->whereBetween('date', [$start, $end])->where('status', 'posted'))
                ->whereHas('account', fn($q) => $q->where('account_type', 'revenue'))
                ->selectRaw('SUM(credit) - SUM(debit) as balance')
                ->first()->balance ?? 0;
        };

        $currRevenue = $fetchRevenue($startOfMonth, $endOfToday);
        $prevRevenue = $fetchRevenue($startOfPrevMonth, $endOfPrevMonth);
        $revChange = ($prevRevenue > 0) ? (($currRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        $kpis = [
            'cash' => ['change' => 5.2], // Mocked for now
            'receivables' => ['change' => -2.1],
            'profit' => ['value' => $currRevenue * 0.25], // Estimated
        ];

        // 3. Revenue vs Expenses (Last 30 Days)
        $profit_loss = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $profit_loss[] = [
                'date' => $date,
                'revenue' => LedgerEntry::whereHas('transaction', fn($q) => $q->where('date', $date)->where('status', 'posted'))
                    ->whereHas('account', fn($q) => $q->where('account_type', 'revenue'))
                    ->selectRaw('SUM(credit) - SUM(debit) as balance')
                    ->first()->balance ?? 0,
                'expense' => LedgerEntry::whereHas('transaction', fn($q) => $q->where('date', $date)->where('status', 'posted'))
                    ->whereHas('account', fn($q) => $q->where('account_type', 'expense'))
                    ->selectRaw('SUM(debit) - SUM(credit) as balance')
                    ->first()->balance ?? 0,
            ];
        }

        // 4. Cash Flow (Weekly)
        $cash_flow = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subWeeks($i)->format('Y-m-d');
            $cash_flow[] = [
                'date' => 'Week ' . (7-$i),
                'inflow' => LedgerEntry::whereHas('transaction', fn($q) => $q->whereBetween('date', [now()->subWeeks($i)->startOfWeek(), now()->subWeeks($i)->endOfWeek()])->where('status', 'posted'))
                    ->whereHas('account', fn($q) => $q->where('account_code', '1000'))
                    ->sum('debit'),
                'outflow' => LedgerEntry::whereHas('transaction', fn($q) => $q->whereBetween('date', [now()->subWeeks($i)->startOfWeek(), now()->subWeeks($i)->endOfWeek()])->where('status', 'posted'))
                    ->whereHas('account', fn($q) => $q->where('account_code', '1000'))
                    ->sum('credit'),
            ];
        }

        // 5. Inventory Distribution
        $rawInv = $fetchBalance('1510');
        $finishedInv = $fetchBalance('1520');
        $inventoryDist = [
            'raw' => $rawInv,
            'finished' => $finishedInv,
            'labels' => ['مواد خام (Raw Materials)', 'قالین آماده (Finished)']
        ];

        // 6. AR Aging Summary
        $aging = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0
        ];
        $invoices = Invoice::all();
        foreach($invoices as $inv) {
            $days = Carbon::parse($inv->invoice_date)->diffInDays(now());
            $invoiceTotal = DB::table('sales')->where('invoice_id', $inv->id)->where('is_returned', 0)->sum('sale_cost_total');
            $balance = $invoiceTotal - ($inv->payments->sum('amount_applied') ?? 0);
            if ($balance <= 0.01) continue;
            
            if ($days <= 30) $aging['0-30'] += $balance;
            elseif ($days <= 60) $aging['31-60'] += $balance;
            elseif ($days <= 90) $aging['61-90'] += $balance;
            else $aging['90+'] += $balance;
        }

        // 7. Cash & Bank Breakdown
        $cashAccounts = ChartOfAccount::where('is_cash_account', 1)->get()->map(function($acc) use ($fetchBalance) {
            return [
                'name' => $acc->account_name,
                'balance' => $fetchBalance($acc->account_code)
            ];
        })->filter(fn($a) => abs($a['balance']) > 0.01);

        $recentTransactions = \App\Transaction::with('entries.account')
            ->where('status', 'posted')
            ->orderBy('date', 'DESC')
            ->limit(5)
            ->get();

        $lockDate = DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value');

        return view('accounting.dashboard', compact('metrics', 'kpis', 'profit_loss', 'cash_flow', 'recentTransactions', 'lockDate', 'inventoryDist', 'aging', 'cashAccounts'));
    }

    /**
     * Close a financial period (Locking)
     */
    public function closePeriod(Request $request)
    {
        $date = $request->validate(['lock_date' => 'required|date'])['lock_date'];
        
        DB::table('financial_settings')
            ->updateOrInsert(
                ['key' => 'financial_lock_date'],
                ['value' => $date, 'updated_at' => now()]
            );

        return redirect()->back()->with('status', 'دوره مالی تا تاریخ مورد نظر با موفقیت بسته شد.');
    }
}
