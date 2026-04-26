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

        // Recent Transactions for the list
        $recentTransactions = LedgerTransaction::with('entries.account')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('accounting.dashboard', array_merge($data, [
            'recentTransactions' => $recentTransactions,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }
}
