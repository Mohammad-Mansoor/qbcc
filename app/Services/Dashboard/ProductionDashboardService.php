<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductionDashboardService
{
    public function getAnalytics()
    {
        // 1. Executive KPIs (Database direct aggregations)
        
        $carpetInventory = DB::table('carpets')
            ->where('carpets.status', '!=', 6)
            ->leftJoin('items', function ($join) {
                $join->on('carpets.carpet_id', '=', 'items.ref_id')
                     ->where('items.type', '=', 'App\\Carpet');
            })
            ->selectRaw('COUNT(carpets.carpet_id) as total_qty, COALESCE(SUM(carpets.area), 0) as total_area, COALESCE(SUM(COALESCE(items.current_cost, carpets.total_price)), 0) as total_value')
            ->first();

        // Raw Material Stock
        $rawMaterials = DB::table('material_stocks')
            ->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
            ->selectRaw("
                SUM(CASE WHEN material_categories.subtype = 'yarn' THEN material_stocks.quantity ELSE 0 END) as yarn_kg,
                SUM(CASE WHEN material_categories.subtype = 'dye' THEN material_stocks.quantity ELSE 0 END) as dye_kg
            ")
            ->first();

        // 2. Financial Costs & Profitability (From Inventory Ledger)
        $costs = DB::table('inventory_transactions')
            ->selectRaw("
                SUM(CASE WHEN type = 'PURCHASE' AND direction = 'IN' AND reference_type = 'App\\Carpet' THEN total_cost ELSE 0 END) as carpet_purchase_cost,
                SUM(CASE WHEN type = 'KACHAEE' AND is_value_adjustment = 1 THEN (CASE WHEN direction = 'IN' THEN total_cost ELSE -total_cost END) ELSE 0 END) as repair_cost,
                SUM(CASE WHEN type = 'WASHING' AND is_value_adjustment = 1 THEN (CASE WHEN direction = 'IN' THEN total_cost ELSE -total_cost END) ELSE 0 END) as washing_cost,
                SUM(CASE WHEN type = 'FINISHING' AND is_value_adjustment = 1 THEN (CASE WHEN direction = 'IN' THEN total_cost ELSE -total_cost END) ELSE 0 END) as finishing_cost
            ")
            ->whereIn('type', ['PURCHASE', 'KACHAEE', 'WASHING', 'FINISHING'])
            ->where('status', 1)
            ->first();

        $revenueData = DB::table('sales')
            ->where('is_returned', '!=', 1)
            ->selectRaw('COALESCE(SUM(CASE WHEN currency_code = "USD" THEN sale_cost_total ELSE sale_cost_total * COALESCE(exchange_rate, 1) END), 0) as total_revenue, COALESCE(SUM(profit), 0) as net_profit')
            ->first();

        // 3. Work In Progress (WIP) Queues
        $wipRepair = DB::table('carpets')->where('status', 2)->selectRaw('COUNT(*) as count, SUM(area) as area')->first();
        $wipWash = DB::table('carpets')->where('status', 13)->selectRaw('COUNT(*) as count, SUM(area) as area')->first();
        $wipFinish = DB::table('carpets')->where('status', 4)->selectRaw('COUNT(*) as count, SUM(area) as area')->first();
        
        // 4. Warehouse Carpet Intelligence (Live physical ledger calculation)
        $warehouseIntelligence = DB::table('carpets')
            ->join('warehouses', 'carpets.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('items', function ($join) {
                $join->on('carpets.carpet_id', '=', 'items.ref_id')
                     ->where('items.type', '=', 'App\\Carpet');
            })
            ->selectRaw("
                warehouses.name as warehouse_name,
                COUNT(carpets.carpet_id) as qty,
                COALESCE(SUM(carpets.area), 0) as area,
                COALESCE(SUM(COALESCE(items.current_cost, carpets.total_price)), 0) as value
            ")
            ->where('carpets.status', '!=', 6)
            ->groupBy('warehouses.id', 'warehouses.name')
            ->having('qty', '>', 0)
            ->get();

        // Warehouse Material Intelligence (Yarn & Dye)
        $warehouseMaterials = DB::table('inventory_transactions')
            ->join('warehouses', 'inventory_transactions.warehouse_id', '=', 'warehouses.id')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_categories', 'items.ref_id', '=', 'material_categories.material_category_id')
            ->selectRaw("
                warehouses.name as warehouse_name,
                material_categories.subtype,
                SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as qty,
                SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.total_cost ELSE -inventory_transactions.total_cost END) as value
            ")
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\\MaterialType')
            ->groupBy('warehouses.id', 'warehouses.name', 'material_categories.subtype')
            ->having('qty', '>', 0)
            ->get();

        // 5. Lifecycle Funnel (All Time)
        $funnel = [
            'Purchased' => DB::table('carpets')->count(),
            'Repaired' => DB::table('carpet_repairs')->distinct('carpetId')->count('carpetId'),
            'Washed' => DB::table('carpet_washes')->distinct('carpetId')->count('carpetId'),
            'Finished' => DB::table('finishing_works')->distinct('carpetId')->count('carpetId'),
            'Sold' => DB::table('sales')->where('is_returned', '!=', 1)->count()
        ];

        // 6. Trend Analytics (Last 12 Months)
        $months = [];
        $purchasedTrend = [];
        $repairedTrend = [];
        $washedTrend = [];
        $finishedTrend = [];
        $soldTrend = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $months[] = $date->format('M Y');
            
            $purchasedTrend[] = DB::table('carpets')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $repairedTrend[] = DB::table('carpet_repairs')->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->distinct('carpetId')->count('carpetId');
            $washedTrend[] = DB::table('carpet_washes')->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->distinct('carpetId')->count('carpetId');
            $finishedTrend[] = DB::table('finishing_works')->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->distinct('carpetId')->count('carpetId');
            $soldTrend[] = DB::table('sales')->where('is_returned', '!=', 1)->whereBetween('sale_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->count();
        }

        // 7. Throughput Analytics
        $now = Carbon::now();
        $last7Days = $now->copy()->subDays(7);
        $last30Days = $now->copy()->subDays(30);

        $throughput = [
            '7_days' => DB::table('inventory_transactions')
                ->whereIn('type', ['KACHAEE', 'WASHING', 'FINISHING'])
                ->where('is_value_adjustment', 1)
                ->where('created_at', '>=', $last7Days)
                ->count(),
            '30_days' => DB::table('inventory_transactions')
                ->whereIn('type', ['KACHAEE', 'WASHING', 'FINISHING'])
                ->where('is_value_adjustment', 1)
                ->where('created_at', '>=', $last30Days)
                ->count(),
        ];

        // 8. Profitability Top Performers
        $topCarpetTypes = DB::table('sales')
            ->where('sales.is_returned', '!=', 1)
            ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
            ->join('carpet_types', 'carpets.type_id', '=', 'carpet_types.carpet_type_id')
            ->selectRaw('carpet_types.carpet_type as name, COUNT(sales.id) as sold_qty, SUM(CASE WHEN sales.currency_code = "USD" THEN sales.sale_cost_total ELSE sales.sale_cost_total * COALESCE(sales.exchange_rate, 1) END) as revenue, SUM(sales.profit) as profit')
            ->groupBy('carpet_types.carpet_type_id', 'carpet_types.carpet_type')
            ->orderByDesc('profit')
            ->limit(5)
            ->get();

        // Top Qualities
        $topQualities = DB::table('sales')
            ->where('sales.is_returned', '!=', 1)
            ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
            ->join('qualities', 'carpets.quality_id', '=', 'qualities.id')
            ->selectRaw('qualities.quality as name, COUNT(sales.id) as sold_qty, SUM(CASE WHEN sales.currency_code = "USD" THEN sales.sale_cost_total ELSE sales.sale_cost_total * COALESCE(sales.exchange_rate, 1) END) as revenue, SUM(sales.profit) as profit')
            ->groupBy('qualities.id', 'qualities.quality')
            ->orderByDesc('profit')
            ->limit(5)
            ->get();

        // Top Raw Materials (Yarn and Dye Revenue)
        $topMaterials = DB::table('material_sales')
            ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
            ->selectRaw("
                material_categories.material_category as name,
                material_categories.subtype,
                SUM(material_sales.amount) as sold_qty,
                SUM(COALESCE(material_sales.base_currency_amount, material_sales.total_price)) as revenue
            ")
            ->groupBy('material_categories.material_category_id', 'material_categories.material_category', 'material_categories.subtype')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // 9. Operational Activity Feed (Unified Timeline)
        $activities = DB::table('activities')
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->select('activities.description', 'activities.created_at', 'users.name as user_name')
            ->orderByDesc('activities.created_at')
            ->limit(10)
            ->get();

        return [
            'carpetInventory' => $carpetInventory,
            'rawMaterials' => $rawMaterials,
            'costs' => $costs,
            'revenueData' => $revenueData,
            'warehouseIntelligence' => $warehouseIntelligence,
            'funnel' => $funnel,
            'trends' => [
                'months' => $months,
                'purchased' => $purchasedTrend,
                'repaired' => $repairedTrend,
                'washed' => $washedTrend,
                'finished' => $finishedTrend,
                'sold' => $soldTrend
            ],
            'throughput' => $throughput,
            'topCarpetTypes' => $topCarpetTypes,
            'topQualities' => $topQualities,
            'topMaterials' => $topMaterials,
            'activities' => $activities,
            'wipRepair' => $wipRepair,
            'wipWash' => $wipWash,
            'wipFinish' => $wipFinish,
            'warehouseMaterials' => $warehouseMaterials,
        ];
    }
}
