<?php

namespace App\Services\Dashboard;

use App\Sale;
use App\Invoice;
use App\Carpet;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesDashboardService
{
    public function getAnalytics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Basic KPIs
        $salesToday = Sale::whereDate('sale_date', $today)->sum('sale_cost_total') ?? 0;
        
        $monthlySales = Sale::whereYear('sale_date', $thisYear)
            ->whereMonth('sale_date', $thisMonth)
            ->sum('sale_cost_total') ?? 0;

        $totalOrdersThisMonth = Sale::whereYear('sale_date', $thisYear)
            ->whereMonth('sale_date', $thisMonth)
            ->count();
            
        $averageOrderValue = $totalOrdersThisMonth > 0 ? ($monthlySales / $totalOrdersThisMonth) : 0;
        $totalOrdersAllTime = Sale::count();

        // Sales Trend (Last 7 Days)
        $salesTrend = [
            'days' => [],
            'data' => []
        ];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $salesTrend['days'][] = $date->format('D');
            $salesTrend['data'][] = Sale::whereDate('sale_date', $date->format('Y-m-d'))->sum('sale_cost_total') ?? 0;
        }

        // Sales by Carpet Type (Using Carpets with status 6 = Sold)
        $soldByTypeRaw = Carpet::where('status', 6)
            ->join('carpet_types', 'carpets.type_id', '=', 'carpet_types.carpet_type_id')
            ->select('carpet_types.carpet_type as name', DB::raw('count(carpets.carpet_id) as count'))
            ->groupBy('carpet_types.carpet_type_id', 'carpet_types.carpet_type')
            ->get();

        $salesByType = [
            'labels' => [],
            'data' => []
        ];
        foreach ($soldByTypeRaw as $c) {
            $salesByType['labels'][] = $c->name;
            $salesByType['data'][] = $c->count;
        }

        // Top Customers by Invoice Totals
        $topCustomersRaw = Customer::with('invoice')->get();
        $topCustomersArray = [];
        foreach ($topCustomersRaw as $c) {
            $totalPurchases = $c->invoice ? $c->invoice->sum('total_price') : 0;
            if ($totalPurchases > 0) {
                $topCustomersArray[] = [
                    'name' => $c->name . ' ' . $c->last_name,
                    'total_purchases' => $totalPurchases,
                    'balance' => 0 // Balance requires ledger logic, set to 0 for strict reality
                ];
            }
        }
        
        // Sort descending by total purchases and take top 5
        usort($topCustomersArray, function($a, $b) {
            return $b['total_purchases'] <=> $a['total_purchases'];
        });
        $topCustomers = array_slice($topCustomersArray, 0, 5);

        // Recent Invoices
        $recentInvoicesRaw = Invoice::orderBy('created_at', 'desc')->take(5)->get();
        $recentInvoices = [];
        foreach ($recentInvoicesRaw as $inv) {
            $recentInvoices[] = [
                'number' => $inv->invoice_number,
                'customer' => $inv->customer ? ($inv->customer->name . ' ' . $inv->customer->last_name) : 'Unknown',
                'date' => Carbon::parse($inv->created_at)->format('Y-m-d'),
                'amount' => $inv->total_price ?? $inv->amount ?? 0,
                'status' => $inv->status ?? 'Closed'
            ];
        }

        return [
            'kpis' => [
                'sales_today' => $salesToday,
                'monthly_sales' => $monthlySales,
                'average_order_value' => round($averageOrderValue, 2),
                'total_orders' => $totalOrdersAllTime
            ],
            'sales_trend' => $salesTrend,
            'sales_by_type' => $salesByType,
            'top_customers' => $topCustomers,
            'recent_invoices' => $recentInvoices
        ];
    }
}
