<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\ChartOfAccount;
use App\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\AccountingAnalyticsService;

use App\Customer;
use App\Agents;

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
            if ($request->get('export') === 'pdf') {
                $reversedTxIds = DB::table('ledger_transactions as lt')
                    ->join('ledger_entries as le', 'le.transaction_id', '=', 'lt.id')
                    ->where('le.party_type', 'App\Customer')
                    ->where('le.party_id', $customerId)
                    ->whereNotNull('lt.reversed_transaction_id')
                    ->pluck('lt.reversed_transaction_id')
                    ->toArray();

                $opening = DB::table('ledger_entries as le')
                    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                    ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
                    ->where('le.party_type', 'App\Customer')
                    ->where('le.party_id', $customerId)
                    ->where('lt.date', '<', $startDate)
                    ->where('lt.status', 'posted')
                    ->whereNull('lt.reversed_transaction_id')
                    ->whereNotIn('lt.id', $reversedTxIds)
                    ->first();
            } else {
                $opening = DB::table('ledger_entries as le')
                    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                    ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
                    ->where('le.party_type', 'App\Customer')
                    ->where('le.party_id', $customerId)
                    ->where('lt.date', '<', $startDate)
                    ->whereIn('lt.status', ['posted', 'reversed'])
                    ->first();
            }
            
            $openingBalance = $opening->balance ?? 0;

            // 2. Get Transactions for the period
            $entries = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select('lt.id as transaction_id', 'lt.date', 'lt.reference', 'lt.description', 'le.base_debit as debit', 'le.base_credit as credit', 'le.currency_code', 'le.original_amount', 'lt.source_type', 'lt.source_id')
                ->where('le.party_type', 'App\Customer')
                ->where('le.party_id', $customerId)
                ->whereBetween('lt.date', [$startDate, $endDate])
                ->whereIn('lt.status', ['posted', 'reversed'])
                ->orderBy('lt.date')
                ->orderBy('lt.id')
                ->get();
        }

        $logoPath = public_path(config('company.logo_path', 'images/logos/qasimi_logo.png'));
        $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
        $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));

        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $topHeaderBase64 = '';
        if (file_exists($topHeaderPath)) {
            $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
        }

        $bottomFooterBase64 = '';
        if (file_exists($bottomFooterPath)) {
            $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
        }

        if ($customerId && $request->get('export') === 'pdf' && !empty($entries)) {
            $pdfFilter = new \App\Services\Accounting\PdfStatementFilter();
            $entries = $pdfFilter->collapse(collect($entries));
        }

        $isSummary = $request->get('type') === 'summary';
        if ($customerId && $isSummary) {
            // Batch pre-fetch relationships to avoid N+1 queries
            $paymentIds = [];
            foreach ($entries as $item) {
                $srcType = strtolower($item->source_type);
                if ($srcType === 'app\customerpayment' || $srcType === 'customer_payment') {
                    $paymentIds[] = $item->source_id;
                }
            }

            $payments = \App\CustomerPayment::whereIn('id', array_unique($paymentIds))->with(['allocations.invoice'])->get()->keyBy('id');

            $entries = collect($entries)->groupBy(function($item) use ($payments) {
                $groupRef = trim($item->reference);
                $srcType = strtolower($item->source_type);
                if ($srcType === 'app\customerpayment' || $srcType === 'customer_payment') {
                    $pay = $payments->get($item->source_id);
                    if ($pay) {
                        $alloc = $pay->allocations->first();
                        if ($alloc && $alloc->invoice) {
                            $groupRef = $alloc->invoice->invoice_no;
                        }
                    }
                }
                return (!empty($groupRef) && $groupRef !== '-') ? $groupRef : 'tx_' . $item->transaction_id;
            })->map(function($group, $key) {
                $sorted = $group->sortBy('date');
                $earliest = $sorted->first();
                return (object)[
                    'transaction_id' => $earliest->transaction_id,
                    'date' => $earliest->date,
                    'reference' => $key,
                    'description' => ($group->sum('debit') > 0) ? 'بابت خرید قالین' : $earliest->description,
                    'debit' => $group->sum('debit'),
                    'credit' => $group->sum('credit'),
                    'currency_code' => $earliest->currency_code,
                    'original_amount' => $group->sum('original_amount')
                ];
            })->sortBy('date')->values();
        }

        if ($customerId && $request->get('export') === 'excel') {
            abort_if(!auth()->user()->can('export_customer_statement_excel'), 403, 'Unauthorized.');
            return $this->exportCustomerExcel($entries, $customer, $openingBalance, $startDate, $endDate, $logoBase64, $topHeaderBase64);
        }

        if ($customerId && $request->get('export') === 'pdf') {
            abort_if(!auth()->user()->can('export_customer_statement_pdf'), 403, 'Unauthorized.');
            return view('accounting.reports.customer_pdf', compact('entries', 'customer', 'openingBalance', 'startDate', 'endDate', 'logoBase64', 'topHeaderBase64', 'bottomFooterBase64', 'isSummary'));
        }

        return view('accounting.reports.customer_statement', compact('entries', 'customer', 'customers', 'openingBalance', 'startDate', 'endDate', 'logoBase64', 'isSummary'));
    }

    protected function exportCustomerExcel($entries, $customer, $openingBalance, $startDate, $endDate, $logoBase64, $headerBase64)
    {
        $filename = 'customer_statement_' . date('Y_m_d_His') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo view('accounting.reports.customer_excel', compact(
            'entries',
            'customer',
            'openingBalance',
            'startDate',
            'endDate',
            'logoBase64',
            'headerBase64'
        ))->render();
        exit;
    }

    public function agentStatement(Request $request)
    {
        $agentId = $request->agent_id;
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $agent = Agents::find($agentId);
        if ($agent) {
            $user = \App\User::find($agent->user_id);
            $agent->display_name = $user ? ($user->name . ' ' . $user->last_name) : 'Agent ID: ' . $agent->agent_id;
        }

        $agents = Agents::all()->map(function($a) {
            $user = \App\User::find($a->user_id);
            $a->display_name = $user ? ($user->name . ' ' . $user->last_name) : 'Agent ID: ' . $a->agent_id;
            return $a;
        })->sortBy('display_name');

        $entries = [];
        $openingBalance = 0;

        if ($agentId) {
            if ($request->get('export') === 'pdf') {
                $reversedTxIds = DB::table('ledger_transactions as lt')
                    ->join('ledger_entries as le', 'le.transaction_id', '=', 'lt.id')
                    ->where('le.party_type', 'App\Agents')
                    ->where('le.party_id', $agentId)
                    ->whereNotNull('lt.reversed_transaction_id')
                    ->pluck('lt.reversed_transaction_id')
                    ->toArray();

                $opening = DB::table('ledger_entries as le')
                    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                    ->select(DB::raw('SUM(le.base_credit - le.base_debit) as balance'))
                    ->where('le.party_type', 'App\Agents')
                    ->where('le.party_id', $agentId)
                    ->where('lt.date', '<', $startDate)
                    ->where('lt.status', 'posted')
                    ->whereNull('lt.reversed_transaction_id')
                    ->whereNotIn('lt.id', $reversedTxIds)
                    ->first();
            } else {
                $opening = DB::table('ledger_entries as le')
                    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                    ->select(DB::raw('SUM(le.base_credit - le.base_debit) as balance'))
                    ->where('le.party_type', 'App\Agents')
                    ->where('le.party_id', $agentId)
                    ->where('lt.date', '<', $startDate)
                    ->whereIn('lt.status', ['posted', 'reversed'])
                    ->first();
            }
            
            $openingBalance = $opening->balance ?? 0;

            // 2. Get Transactions for the period
            $entries = DB::table('ledger_entries as le')
                ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                ->select('lt.id as transaction_id', 'lt.date', 'lt.reference', 'lt.description', 'le.base_debit as debit', 'le.base_credit as credit', 'le.currency_code', 'le.original_amount', 'lt.source_type', 'lt.source_id')
                ->where('le.party_type', 'App\Agents')
                ->where('le.party_id', $agentId)
                ->whereBetween('lt.date', [$startDate, $endDate])
                ->whereIn('lt.status', ['posted', 'reversed'])
                ->orderBy('lt.date')
                ->orderBy('lt.id')
                ->get();
        }

        $logoPath = public_path(config('company.logo_path', 'images/logos/qasimi_logo.png'));
        $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
        $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));

        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $topHeaderBase64 = '';
        if (file_exists($topHeaderPath)) {
            $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
        }

        $bottomFooterBase64 = '';
        if (file_exists($bottomFooterPath)) {
            $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
        }

        if ($request->get('export') === 'pdf' && !empty($entries)) {
            $pdfFilter = new \App\Services\Accounting\PdfStatementFilter();
            $entries = $pdfFilter->collapse(collect($entries));
        }

        $isSummary = $request->get('type') === 'summary';
        if ($agentId && $isSummary) {
            // Batch pre-fetch relationships to avoid N+1 queries
            $allocationIds = [];
            $paymentIds = [];
            $carpetIds = [];
            foreach ($entries as $item) {
                $srcType = strtolower($item->source_type);
                if ($srcType === 'app\agentpaymentallocation' || $srcType === 'agent_advance_settlement') {
                    $allocationIds[] = $item->source_id;
                } elseif ($srcType === 'app\agentpayment' || $srcType === 'agent_payment') {
                    $paymentIds[] = $item->source_id;
                } elseif ($srcType === 'app\carpet' || $srcType === 'carpet') {
                    $carpetIds[] = $item->source_id;
                }
            }

            $allocations = \App\AgentPaymentAllocation::whereIn('id', array_unique($allocationIds))->get()->map(function($alloc) {
                $alloc->allocatable = $alloc->allocatable; // Eager load polymorphic relation
                return $alloc;
            })->keyBy('id');

            $payments = \App\AgentPayment::whereIn('id', array_unique($paymentIds))->with('allocations')->get()->map(function($pay) {
                foreach ($pay->allocations as $alloc) {
                    $alloc->allocatable = $alloc->allocatable;
                }
                return $pay;
            })->keyBy('id');

            $carpets = DB::table('carpets')
                ->leftJoin('purchase_invoices', 'carpets.purchase_invoice_id', '=', 'purchase_invoices.id')
                ->whereIn('carpets.carpet_id', array_unique($carpetIds))
                ->select('carpets.carpet_id', 'purchase_invoices.invoice_number')
                ->get()
                ->keyBy('carpet_id');

            // Group entries by resolved reference
            $entries = collect($entries)->groupBy(function($item) use ($allocations, $payments, $carpets) {
                $groupRef = trim($item->reference);
                $srcType = strtolower($item->source_type);
                if ($srcType === 'app\agentpaymentallocation' || $srcType === 'agent_advance_settlement') {
                    $alloc = $allocations->get($item->source_id);
                    if ($alloc && $alloc->allocatable) {
                        $groupRef = $alloc->allocatable->invoice_no ?? $alloc->allocatable->bill_number;
                    }
                } elseif ($srcType === 'app\agentpayment' || $srcType === 'agent_payment') {
                    $pay = $payments->get($item->source_id);
                    if ($pay) {
                        $alloc = $pay->allocations->first();
                        if ($alloc && $alloc->allocatable) {
                            $groupRef = $alloc->allocatable->invoice_no ?? $alloc->allocatable->bill_number;
                        }
                    }
                } elseif ($srcType === 'app\carpet' || $srcType === 'carpet') {
                    $c = $carpets->get($item->source_id);
                    if ($c && !empty($c->invoice_number)) {
                        $groupRef = $c->invoice_number;
                    }
                }
                return (!empty($groupRef) && $groupRef !== '-') ? $groupRef : 'tx_' . $item->transaction_id;
            })->map(function($group, $key) {
                $sorted = $group->sortBy('date');
                $earliest = $sorted->first();
                return (object)[
                    'transaction_id' => $earliest->transaction_id,
                    'date' => $earliest->date,
                    'reference' => $key,
                    'description' => $earliest->description,
                    'debit' => $group->sum('debit'),
                    'credit' => $group->sum('credit'),
                    'currency_code' => $earliest->currency_code,
                    'original_amount' => $group->sum('original_amount'),
                    'source_type' => $earliest->source_type,
                    'source_id' => $earliest->source_id
                ];
            })->sortBy('date')->values();
        }

        if ($agentId && $request->get('export') === 'excel') {
            return $this->exportAgentExcel($entries, $agent, $openingBalance, $startDate, $endDate, $logoBase64, $topHeaderBase64);
        }

        if ($agentId && $request->get('export') === 'pdf') {
            return view('accounting.reports.agent_pdf', compact('entries', 'agent', 'openingBalance', 'startDate', 'endDate', 'logoBase64', 'topHeaderBase64', 'bottomFooterBase64', 'isSummary'));
        }

        return view('accounting.reports.agent_statement', compact('entries', 'agent', 'agents', 'openingBalance', 'startDate', 'endDate', 'logoBase64', 'isSummary'));
    }

    protected function exportAgentExcel($entries, $agent, $openingBalance, $startDate, $endDate, $logoBase64, $headerBase64)
    {
        $filename = 'agent_statement_' . date('Y_m_d_His') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo view('accounting.reports.agent_excel', compact(
            'entries',
            'agent',
            'openingBalance',
            'startDate',
            'endDate',
            'logoBase64',
            'headerBase64'
        ))->render();
        exit;
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
            ->whereIn('lt.status', ['posted', 'reversed'])
            ->where('lt.date', '<', $startDate)
            ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
            ->first();
        $beginningCash = (float)($openingCashQuery->balance ?? 0);

        // 2. Calculate Ending Cash Balance (posted ledger entries up to end date)
        $closingCashQuery = DB::table('chart_of_accounts as coa')
            ->join('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->where('coa.is_cash_account', 1)
            ->whereIn('lt.status', ['posted', 'reversed'])
            ->where('lt.date', '<=', $endDate)
            ->select(DB::raw('SUM(le.base_debit - le.base_credit) as balance'))
            ->first();
        $endingCash = (float)($closingCashQuery->balance ?? 0);
        $actualNetCashChange = $endingCash - $beginningCash;

        // 3. Calculate Net Profit for the period (Revenues - Expenses)
        $plQuery = DB::table('chart_of_accounts as coa')
            ->join('ledger_entries as le', 'coa.id', '=', 'le.account_id')
            ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
            ->whereIn('lt.status', ['posted', 'reversed'])
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
                     ->whereIn('lt.status', ['posted', 'reversed']);
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

        if ($request->export === 'pdf') {
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));
            
            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }
            
            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }

            return view('accounting.reports.cash_flow_pdf', array_merge($data, [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'topHeaderBase64' => $topHeaderBase64,
                'bottomFooterBase64' => $bottomFooterBase64
            ]));
        }

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
                    ->whereIn('lt.status', ['posted', 'reversed'])
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
                ->whereIn('lt.status', ['posted', 'reversed'])
                ->orderBy('lt.date')
                ->orderBy('lt.id')
                ->get();
        }

        $currencies = \App\Currency::all()->keyBy('code');

        if ($request->get('export') === 'excel') {
            $logoPath = public_path(config('company.logo_path', 'images/logos/qasimi_logo.png'));
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
            $topHeaderBase64 = file_exists($topHeaderPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath)) : '';

            $filename = 'account_ledger_' . ($account ? $account->account_code : 'all') . '_' . date('Y_m_d_His') . '.xls';
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');

            echo view('accounting.reports.account_ledger_excel', compact(
                'entries', 'account', 'openingBalance', 'startDate', 'endDate', 'currencies', 'logoBase64', 'topHeaderBase64'
            ))->render();
            exit;
        }

        if ($request->get('export') === 'pdf') {
            $logoPath = public_path(config('company.logo_path', 'images/logos/qasimi_logo.png'));
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));
            
            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
            $topHeaderBase64 = file_exists($topHeaderPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath)) : '';
            $bottomFooterBase64 = file_exists($bottomFooterPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath)) : '';

            return view('accounting.reports.account_ledger_pdf', compact(
                'entries', 'account', 'openingBalance', 'startDate', 'endDate', 'currencies', 'logoBase64', 'topHeaderBase64', 'bottomFooterBase64'
            ));
        }

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
            ->whereIn('lt.status', ['posted', 'reversed'])
            ->groupBy('coa.id', 'coa.account_code', 'coa.account_name')
            ->get();

        if ($request->export === 'pdf') {
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));
            
            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }
            
            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }

            return view('accounting.reports.trial_balance_pdf', compact('report', 'startDate', 'endDate', 'topHeaderBase64', 'bottomFooterBase64'));
        } elseif ($request->export === 'excel') {
            return view('accounting.reports.trial_balance_excel', compact('report', 'startDate', 'endDate'));
        }
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

        if ($request->get('export') === 'pdf') {
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));
            
            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }
            
            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }
            
            return view('accounting.reports.profit_loss_pdf', compact('revenue', 'expenses', 'netProfit', 'startDate', 'endDate', 'currencies', 'currencyCode', 'rate', 'topHeaderBase64', 'bottomFooterBase64'));
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

        if ($request->export === 'pdf') {
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));
            
            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }
            
            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }

            return view('accounting.reports.balance_sheet_pdf', compact('assets', 'liabilities', 'equity', 'currentNetProfit', 'endDate', 'topHeaderBase64', 'bottomFooterBase64'));
        } elseif ($request->export === 'excel') {
            return view('accounting.reports.balance_sheet_excel', compact('assets', 'liabilities', 'equity', 'currentNetProfit', 'endDate'));
        }
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
                'coa.id as account_id',
                'coa.account_code',
                'coa.account_name',
                'coa.normal_balance',
                DB::raw('SUM(le.base_debit) as total_debit'),
                DB::raw('SUM(le.base_credit) as total_credit'),
                DB::raw('SUM(CASE WHEN coa.normal_balance = "debit" THEN (le.base_debit - le.base_credit) ELSE (le.base_credit - le.base_debit) END) as balance')
            )
            ->where('coa.account_type', $type)
            ->whereIn('lt.status', ['posted', 'reversed']);

        if ($startDate) {
            $query->where('lt.date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('lt.date', '<=', $endDate);
        }

        return $query->groupBy('coa.id', 'coa.account_code', 'coa.account_name', 'coa.normal_balance')->get();
    }

    public function differentAccountStatement(Request $request)
    {
        return app(EntityStatementController::class)->reportStatement($request, 'different-account');
    }

    public function repairTeamStatement(Request $request)
    {
        return app(EntityStatementController::class)->reportStatement($request, 'kachayee-team');
    }

    public function washingTeamStatement(Request $request)
    {
        return app(EntityStatementController::class)->reportStatement($request, 'washing-team');
    }

    public function finishingTeamStatement(Request $request)
    {
        return app(EntityStatementController::class)->reportStatement($request, 'tayaari-team');
    }

    public function stringSellerStatement(Request $request)
    {
        return app(EntityStatementController::class)->reportStatement($request, 'string-seller');
    }

    public function employeeStatement(Request $request)
    {
        return app(EntityStatementController::class)->reportStatement($request, 'employee');
    }
}
