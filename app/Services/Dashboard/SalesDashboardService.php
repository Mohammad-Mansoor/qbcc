<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SalesDashboardService
{
    public function getAnalytics()
    {
        return Cache::remember('sales_dashboard_executive_data', 1800, function () {
            
            $now = Carbon::now();
            $today = $now->format('Y-m-d');
            $thisMonthStart = $now->copy()->startOfMonth()->format('Y-m-d');
            $thisYearStart = $now->copy()->startOfYear()->format('Y-m-d');

            // 1. Executive Snapshot (Carpet)
            $carpetAgg = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->where('sales.is_returned', 0)
                ->selectRaw("
                    SUM(sales.sale_cost_total) as lifetime_revenue,
                    SUM(sales.profit) as lifetime_profit,
                    SUM(carpets.area) as lifetime_area,
                    COUNT(sales.id) as lifetime_count,
                    SUM(CASE WHEN sales.sale_date = ? THEN sales.sale_cost_total ELSE 0 END) as today_revenue,
                    SUM(CASE WHEN sales.sale_date = ? THEN sales.profit ELSE 0 END) as today_profit,
                    SUM(CASE WHEN sales.sale_date = ? THEN carpets.area ELSE 0 END) as today_area,
                    SUM(CASE WHEN sales.sale_date = ? THEN 1 ELSE 0 END) as today_count,
                    SUM(CASE WHEN sales.sale_date >= ? THEN sales.sale_cost_total ELSE 0 END) as month_revenue,
                    SUM(CASE WHEN sales.sale_date >= ? THEN sales.profit ELSE 0 END) as month_profit,
                    SUM(CASE WHEN sales.sale_date >= ? THEN carpets.area ELSE 0 END) as month_area,
                    SUM(CASE WHEN sales.sale_date >= ? THEN 1 ELSE 0 END) as month_count,
                    SUM(CASE WHEN sales.sale_date >= ? THEN sales.sale_cost_total ELSE 0 END) as year_revenue,
                    SUM(CASE WHEN sales.sale_date >= ? THEN sales.profit ELSE 0 END) as year_profit
                ", [
                    $today, $today, $today, $today, 
                    $thisMonthStart, $thisMonthStart, $thisMonthStart, $thisMonthStart, 
                    $thisYearStart, $thisYearStart
                ])
                ->first();

            // 2. Executive Snapshot (Raw Materials)
            $materialAgg = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->leftJoin('items', function($join) {
                    $join->on('material_sales.type_id', '=', 'items.ref_id')
                         ->where('items.type', '=', 'App\\MaterialType');
                })
                ->where('material_sales.status', 1)
                ->selectRaw("
                    SUM(material_sales.base_currency_amount) as lifetime_revenue,
                    SUM(material_sales.base_currency_amount - (material_sales.amount * COALESCE(items.current_cost, 0))) as lifetime_profit,
                    
                    SUM(CASE WHEN material_categories.subtype = 'yarn' THEN material_sales.amount ELSE 0 END) as lifetime_yarn_kg,
                    SUM(CASE WHEN material_categories.subtype = 'dye' THEN material_sales.amount ELSE 0 END) as lifetime_dye_kg,
                    
                    SUM(CASE WHEN material_sales.date = ? THEN material_sales.base_currency_amount ELSE 0 END) as today_revenue,
                    SUM(CASE WHEN material_sales.date = ? THEN material_sales.base_currency_amount - (material_sales.amount * COALESCE(items.current_cost, 0)) ELSE 0 END) as today_profit,
                    SUM(CASE WHEN material_sales.date = ? AND material_categories.subtype = 'yarn' THEN material_sales.amount ELSE 0 END) as today_yarn_kg,
                    SUM(CASE WHEN material_sales.date = ? AND material_categories.subtype = 'dye' THEN material_sales.amount ELSE 0 END) as today_dye_kg,
                    
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.base_currency_amount ELSE 0 END) as month_revenue,
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.base_currency_amount - (material_sales.amount * COALESCE(items.current_cost, 0)) ELSE 0 END) as month_profit,
                    SUM(CASE WHEN material_sales.date >= ? AND material_categories.subtype = 'yarn' THEN material_sales.amount ELSE 0 END) as month_yarn_kg,
                    SUM(CASE WHEN material_sales.date >= ? AND material_categories.subtype = 'dye' THEN material_sales.amount ELSE 0 END) as month_dye_kg,
                    
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.base_currency_amount ELSE 0 END) as year_revenue,
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.base_currency_amount - (material_sales.amount * COALESCE(items.current_cost, 0)) ELSE 0 END) as year_profit
                ", [
                    $today, $today, $today, $today,
                    $thisMonthStart, $thisMonthStart, $thisMonthStart, $thisMonthStart,
                    $thisYearStart, $thisYearStart
                ])
                ->first();

            // 3. Accounts Receivable (AR) Aging
            $arAging = DB::query()->fromSub(function ($query) {
                $query->from('invoices')
                    ->select('id', 'payment_status')
                    ->selectRaw('DATEDIFF(CURDATE(), invoice_date) as age')
                    ->selectRaw('COALESCE((SELECT SUM(sale_cost_total) FROM sales WHERE invoice_id = invoices.id AND is_returned = 0), 0) + COALESCE((SELECT SUM(base_currency_amount) FROM material_sales WHERE invoice_id = invoices.id AND status = 1), 0) as total')
                    ->selectRaw('COALESCE((SELECT SUM(amount_applied) FROM invoice_payments WHERE invoice_id = invoices.id), 0) as paid')
                    ->selectRaw('(COALESCE((SELECT SUM(sale_cost_total) FROM sales WHERE invoice_id = invoices.id AND is_returned = 0), 0) + COALESCE((SELECT SUM(base_currency_amount) FROM material_sales WHERE invoice_id = invoices.id AND status = 1), 0)) - COALESCE((SELECT SUM(amount_applied) FROM invoice_payments WHERE invoice_id = invoices.id), 0) as outstanding')
                    ->where('status', 'open');
            }, 'invoice_totals')
            ->selectRaw("
                SUM(CASE WHEN age <= 30 AND payment_status != 'paid' THEN outstanding ELSE 0 END) as age_30,
                SUM(CASE WHEN age BETWEEN 31 AND 60 AND payment_status != 'paid' THEN outstanding ELSE 0 END) as age_60,
                SUM(CASE WHEN age BETWEEN 61 AND 90 AND payment_status != 'paid' THEN outstanding ELSE 0 END) as age_90,
                SUM(CASE WHEN age > 90 AND payment_status != 'paid' THEN outstanding ELSE 0 END) as age_90_plus,
                SUM(CASE WHEN payment_status != 'paid' THEN outstanding ELSE 0 END) as total_outstanding,
                SUM(paid) as total_collected,
                SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) as count_unpaid,
                SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as count_partial,
                COUNT(id) as count_total
            ")
            ->first();

            // 4. Revenue Trends (12-Month Array)
            $monthsLabel = [];
            $carpetTrendArr = [];
            $yarnTrendArr = [];
            $dyeTrendArr = [];

            $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();
            
            $carpetTrendData = DB::table('sales')
                ->where('sale_date', '>=', $twelveMonthsAgo)
                ->where('is_returned', 0)
                ->selectRaw("DATE_FORMAT(sale_date, '%Y-%m') as month, SUM(sale_cost_total) as rev")
                ->groupBy('month')
                ->pluck('rev', 'month')->toArray();

            $materialTrendData = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->where('material_sales.date', '>=', $twelveMonthsAgo)
                ->where('material_sales.status', 1)
                ->selectRaw("DATE_FORMAT(material_sales.date, '%Y-%m') as month, material_categories.subtype, SUM(material_sales.base_currency_amount) as rev")
                ->groupBy('month', 'subtype')
                ->get();

            for ($i = 11; $i >= 0; $i--) {
                $dt = Carbon::now()->subMonths($i);
                $m = $dt->format('Y-m');
                $monthsLabel[] = $dt->format('M Y');
                
                $carpetTrendArr[] = (float)($carpetTrendData[$m] ?? 0);
                
                $yRev = 0; $dRev = 0;
                foreach($materialTrendData as $mtd) {
                    if ($mtd->month === $m) {
                        if ($mtd->subtype === 'yarn') $yRev += $mtd->rev;
                        if ($mtd->subtype === 'dye') $dRev += $mtd->rev;
                    }
                }
                $yarnTrendArr[] = (float)$yRev;
                $dyeTrendArr[] = (float)$dRev;
            }

            // 5. Product Performance & Profitability
            $topCarpetTypes = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->join('carpet_types', 'carpets.type_id', '=', 'carpet_types.carpet_type_id')
                ->where('sales.is_returned', 0)
                ->selectRaw('carpet_types.carpet_type as name, SUM(sales.sale_cost_total) as revenue, SUM(sales.profit) as profit, SUM(carpets.area) as area, COUNT(*) as qty')
                ->groupBy('carpet_types.carpet_type_id', 'carpet_types.carpet_type')
                ->orderByDesc('profit')
                ->limit(5)
                ->get();

            $topCarpetQualities = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->join('qualities', 'carpets.quality_id', '=', 'qualities.id')
                ->where('sales.is_returned', 0)
                ->selectRaw('qualities.quality as name, SUM(sales.sale_cost_total) as revenue, SUM(sales.profit) as profit, COUNT(*) as qty')
                ->groupBy('qualities.id', 'qualities.quality')
                ->orderByDesc('profit')
                ->limit(5)
                ->get();

            $topMaterialSales = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->where('material_sales.status', 1)
                ->selectRaw("
                    material_categories.material_category as name, 
                    material_categories.subtype,
                    SUM(material_sales.amount) as qty, 
                    SUM(material_sales.base_currency_amount) as revenue
                ")
                ->groupBy('material_categories.material_category_id', 'material_categories.material_category', 'material_categories.subtype')
                ->orderByDesc('revenue')
                ->get();

            // 6. Top Customers Deep Analytics
            $topCustomers = DB::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('sales.is_returned', 0)
                ->selectRaw('customers.name, customers.id, SUM(sales.sale_cost_total) as total_revenue, COUNT(sales.id) as sales_count, MAX(sales.sale_date) as last_purchase')
                ->groupBy('customers.id', 'customers.name')
                ->orderByDesc('total_revenue')
                ->limit(5)
                ->get();
                
            // (We omit balances directly here as balances per customer need AccountingService which is expensive in a tight loop. We use AR Aging for global balance).

            // 7. Warehouse Intelligence
            $whCarpet = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->join('warehouses', 'carpets.warehouse_id', '=', 'warehouses.id')
                ->where('sales.is_returned', 0)
                ->selectRaw('warehouses.name, COUNT(*) as qty, SUM(carpets.area) as area, SUM(sales.sale_cost_total) as revenue')
                ->groupBy('warehouses.id', 'warehouses.name')
                ->get();

            $whMaterial = DB::table('material_sales')
                ->leftJoin('warehouses', 'material_sales.warehouse_id', '=', 'warehouses.id')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->where('material_sales.status', 1)
                ->selectRaw("
                    COALESCE(warehouses.name, 'نامشخص') as name, 
                    SUM(CASE WHEN material_categories.subtype = 'yarn' THEN material_sales.amount ELSE 0 END) as yarn_kg,
                    SUM(CASE WHEN material_categories.subtype = 'dye' THEN material_sales.amount ELSE 0 END) as dye_kg,
                    SUM(material_sales.base_currency_amount) as revenue
                ")
                ->groupBy('warehouses.id', 'warehouses.name')
                ->get();

            // 8. Velocity Analytics
            $carpetVelocity = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->selectRaw('AVG(DATEDIFF(sales.sale_date, carpets.created_at)) as avg_days')
                ->where('sales.is_returned', 0)
                ->value('avg_days') ?? 0;

            // Yarn Velocity = Total Stock / 30-day burn rate
            $yarnStock = DB::table('material_stocks')
                ->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
                ->where('material_categories.subtype', 'yarn')
                ->sum('quantity');

            $yarnSales30 = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->where('material_categories.subtype', 'yarn')
                ->where('material_sales.date', '>=', Carbon::now()->subDays(30))
                ->sum('amount');
            
            $yarnDailyBurn = $yarnSales30 / 30;
            $yarnVelocity = $yarnDailyBurn > 0 ? ($yarnStock / $yarnDailyBurn) : 0;

            // Dye Velocity
            $dyeStock = DB::table('material_stocks')
                ->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
                ->where('material_categories.subtype', 'dye')
                ->sum('quantity');

            $dyeSales30 = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->where('material_categories.subtype', 'dye')
                ->where('material_sales.date', '>=', Carbon::now()->subDays(30))
                ->sum('amount');
            
            $dyeDailyBurn = $dyeSales30 / 30;
            $dyeVelocity = $dyeDailyBurn > 0 ? ($dyeStock / $dyeDailyBurn) : 0;

            // 9. Unified Activity Feed
            $activities = DB::table('activities')
                ->leftJoin('users', 'activities.user_id', '=', 'users.id')
                ->where(function($q) {
                    $q->where('activities.description', 'like', '%فروش%')
                      ->orWhere('activities.description', 'like', '%انوایس%')
                      ->orWhere('activities.description', 'like', '%payment%');
                })
                ->select('activities.description', 'activities.created_at', 'users.name as user_name')
                ->orderByDesc('activities.created_at')
                ->limit(10)
                ->get();

            // 10. Financial Validation
            $carpetRev = $carpetAgg->lifetime_revenue ?? 0;
            $materialRev = $materialAgg->lifetime_revenue ?? 0;
            $carpetProfit = $carpetAgg->lifetime_profit ?? 0;
            $materialProfit = $materialAgg->lifetime_profit ?? 0;

            // Since Total Revenue and Profit are dynamically calculated on the frontend by summing these components,
            // we log the foundational metrics to ensure they exist and don't produce unexpected NULLs.
            if ($carpetRev < 0 || $materialRev < 0) {
                \Log::warning('Dashboard Sales Validation: Negative lifetime revenue detected.', [
                    'carpet_revenue' => $carpetRev,
                    'material_revenue' => $materialRev
                ]);
            }

            return [
                'carpetAgg' => $carpetAgg,
                'materialAgg' => $materialAgg,
                'arAging' => $arAging,
                'trends' => [
                    'months' => $monthsLabel,
                    'carpet' => $carpetTrendArr,
                    'yarn' => $yarnTrendArr,
                    'dye' => $dyeTrendArr
                ],
                'topCarpetTypes' => $topCarpetTypes,
                'topCarpetQualities' => $topCarpetQualities,
                'topMaterialSales' => $topMaterialSales,
                'topCustomers' => $topCustomers,
                'whCarpet' => $whCarpet,
                'whMaterial' => $whMaterial,
                'velocity' => [
                    'carpet' => round($carpetVelocity),
                    'yarn' => round($yarnVelocity),
                    'dye' => round($dyeVelocity)
                ],
                'activities' => $activities
            ];
        });
    }
}
