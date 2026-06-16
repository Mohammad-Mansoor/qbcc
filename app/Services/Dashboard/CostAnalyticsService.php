<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CostAnalyticsService
{
    public function getAnalytics()
    {
        return Cache::remember('cost_analytics_executive_data', 1800, function () {
            
            $now = Carbon::now();
            $today = $now->format('Y-m-d');
            $monthStart = $now->copy()->startOfMonth()->format('Y-m-d');
            $yearStart = $now->copy()->startOfYear()->format('Y-m-d');
            
            // -------------------------------------------------------------
            // 1. REVENUE & SALES COSTS (from sales and material_sales)
            // -------------------------------------------------------------
            
            $carpetSales = DB::table('sales')
                ->where('is_returned', 0)
                ->selectRaw("
                    SUM(sale_cost_total) as lifetime_rev,
                    SUM(sale_cost_total - profit) as lifetime_cost,
                    SUM(profit) as lifetime_profit,
                    SUM(CASE WHEN sale_date = ? THEN sale_cost_total ELSE 0 END) as today_rev,
                    SUM(CASE WHEN sale_date = ? THEN sale_cost_total - profit ELSE 0 END) as today_cost,
                    SUM(CASE WHEN sale_date = ? THEN profit ELSE 0 END) as today_profit,
                    SUM(CASE WHEN sale_date >= ? THEN sale_cost_total ELSE 0 END) as month_rev,
                    SUM(CASE WHEN sale_date >= ? THEN sale_cost_total - profit ELSE 0 END) as month_cost,
                    SUM(CASE WHEN sale_date >= ? THEN profit ELSE 0 END) as month_profit,
                    SUM(CASE WHEN sale_date >= ? THEN sale_cost_total ELSE 0 END) as year_rev,
                    SUM(CASE WHEN sale_date >= ? THEN sale_cost_total - profit ELSE 0 END) as year_cost,
                    SUM(CASE WHEN sale_date >= ? THEN profit ELSE 0 END) as year_profit
                ", [$today, $today, $today, $monthStart, $monthStart, $monthStart, $yearStart, $yearStart, $yearStart])
                ->first();

            $materialSales = DB::table('material_sales')
                ->join('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
                ->leftJoin('items', function($join) {
                    $join->on('material_sales.type_id', '=', 'items.ref_id')
                         ->where('items.type', '=', 'App\\MaterialType');
                })
                ->where('material_sales.status', 1)
                ->selectRaw("
                    material_categories.subtype,
                    SUM(material_sales.base_currency_amount) as lifetime_rev,
                    SUM(material_sales.amount * COALESCE(items.current_cost, 0)) as lifetime_cost,
                    
                    SUM(CASE WHEN material_sales.date = ? THEN material_sales.base_currency_amount ELSE 0 END) as today_rev,
                    SUM(CASE WHEN material_sales.date = ? THEN material_sales.amount * COALESCE(items.current_cost, 0) ELSE 0 END) as today_cost,
                    
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.base_currency_amount ELSE 0 END) as month_rev,
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.amount * COALESCE(items.current_cost, 0) ELSE 0 END) as month_cost,
                    
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.base_currency_amount ELSE 0 END) as year_rev,
                    SUM(CASE WHEN material_sales.date >= ? THEN material_sales.amount * COALESCE(items.current_cost, 0) ELSE 0 END) as year_cost
                ", [$today, $today, $monthStart, $monthStart, $yearStart, $yearStart])
                ->groupBy('material_categories.subtype')
                ->get();
                
            $yarnSales = $materialSales->firstWhere('subtype', 'yarn');
            $dyeSales = $materialSales->firstWhere('subtype', 'dye');

            // -------------------------------------------------------------
            // 2. OPERATIONAL COSTS (from inventory_transactions)
            // -------------------------------------------------------------
            // We use inventory_transactions as the ultimate truth for Cost Build-Up.
            
            $costLedger = DB::table('inventory_transactions')
                ->selectRaw("
                    type, 
                    reference_type,
                    SUM(total_cost) as lifetime_cost,
                    SUM(CASE WHEN DATE(created_at) = ? THEN total_cost ELSE 0 END) as today_cost,
                    SUM(CASE WHEN DATE(created_at) >= ? THEN total_cost ELSE 0 END) as month_cost,
                    SUM(CASE WHEN DATE(created_at) >= ? THEN total_cost ELSE 0 END) as year_cost
                ", [$today, $monthStart, $yearStart])
                ->whereIn('type', ['PURCHASE', 'KACHAEE', 'WASHING', 'FINISHING'])
                ->groupBy('type', 'reference_type')
                ->get();

            $carpetPurchaseCost = $costLedger->where('type', 'PURCHASE')->where('reference_type', 'App\Carpet')->sum('lifetime_cost');
            $materialPurchaseCost = $costLedger->where('type', 'PURCHASE')->where('reference_type', 'App\PurchaseMaterial')->sum('lifetime_cost');
            $repairCost = $costLedger->where('type', 'KACHAEE')->sum('lifetime_cost');
            $washCost = $costLedger->where('type', 'WASHING')->sum('lifetime_cost');
            $finishCost = $costLedger->where('type', 'FINISHING')->sum('lifetime_cost');

            // -------------------------------------------------------------
            // 3. INVENTORY VALUATION (from items and inventory_transactions)
            // -------------------------------------------------------------
            
            $carpetInv = DB::query()->fromSub(function($query) {
                $query->from('inventory_transactions')
                      ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                      ->leftJoin('carpets', 'items.ref_id', '=', 'carpets.carpet_id')
                      ->where('items.type', 'App\\Carpet')
                      ->selectRaw("items.id, items.current_cost, MAX(carpets.area) as carpet_area, SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
                      ->groupBy('items.id', 'items.current_cost');
            }, 'balances')
            ->selectRaw('SUM(balance) as total_qty, SUM(balance * carpet_area) as total_area, SUM(balance * current_cost) as total_value')
            ->where('balance', '>', 0)
            ->first();

            $materialInv = DB::query()->fromSub(function($query) {
                $query->from('inventory_transactions')
                      ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                      ->leftJoin('material_categories', 'items.ref_id', '=', 'material_categories.material_category_id')
                      ->where('items.type', 'App\\MaterialType')
                      ->selectRaw("material_categories.subtype, items.current_cost, SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
                      ->groupBy('items.id', 'material_categories.subtype', 'items.current_cost');
            }, 'balances')
            ->selectRaw('subtype, SUM(balance) as total_qty, SUM(balance * current_cost) as total_value')
            ->where('balance', '>', 0)
            ->groupBy('subtype')
            ->get();
            
            $yarnInv = $materialInv->firstWhere('subtype', 'yarn');
            $dyeInv = $materialInv->firstWhere('subtype', 'dye');

            // -------------------------------------------------------------
            // 4. COST TRENDS (Last 12 Months)
            // -------------------------------------------------------------
            
            $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();
            $trendLedger = DB::table('inventory_transactions')
                ->where('created_at', '>=', $twelveMonthsAgo)
                ->whereIn('type', ['PURCHASE', 'KACHAEE', 'WASHING', 'FINISHING'])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, type, SUM(total_cost) as cost")
                ->groupBy('month', 'type')
                ->get();

            $monthsLabel = [];
            $trendPurchase = [];
            $trendRepair = [];
            $trendWash = [];
            $trendFinish = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $dt = Carbon::now()->subMonths($i);
                $m = $dt->format('Y-m');
                $monthsLabel[] = $dt->format('M Y');
                
                $trendPurchase[] = (float)$trendLedger->where('month', $m)->where('type', 'PURCHASE')->sum('cost');
                $trendRepair[]   = (float)$trendLedger->where('month', $m)->where('type', 'KACHAEE')->sum('cost');
                $trendWash[]     = (float)$trendLedger->where('month', $m)->where('type', 'WASHING')->sum('cost');
                $trendFinish[]   = (float)$trendLedger->where('month', $m)->where('type', 'FINISHING')->sum('cost');
            }

            // -------------------------------------------------------------
            // 5. CARPET LIFECYCLE PROFITABILITY
            // -------------------------------------------------------------
            
            $carpetTypeProfitability = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->join('carpet_types', 'carpets.type_id', '=', 'carpet_types.carpet_type_id')
                ->where('sales.is_returned', 0)
                ->selectRaw("
                    carpet_types.carpet_type as name,
                    SUM(sales.sale_cost_total) as revenue,
                    SUM(sales.sale_cost_total - sales.profit) as cost,
                    SUM(sales.profit) as profit
                ")
                ->groupBy('carpet_types.carpet_type_id', 'carpet_types.carpet_type')
                ->get();

            $qualityProfitability = DB::table('sales')
                ->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')
                ->join('qualities', 'carpets.quality_id', '=', 'qualities.id')
                ->where('sales.is_returned', 0)
                ->selectRaw("
                    qualities.quality as name,
                    SUM(sales.sale_cost_total) as revenue,
                    SUM(sales.sale_cost_total - sales.profit) as cost,
                    SUM(sales.profit) as profit
                ")
                ->groupBy('qualities.id', 'qualities.quality')
                ->get();

            // -------------------------------------------------------------
            // 6. SUPPLIER COST INTELLIGENCE
            // -------------------------------------------------------------
            
            $topCarpetSuppliers = DB::table('carpets')
                ->join('agents', 'carpets.agent_id', '=', 'agents.agent_id')
                ->join('users', 'agents.user_id', '=', 'users.id')
                ->selectRaw("
                    users.name,
                    COUNT(carpets.carpet_id) as qty,
                    SUM(carpets.area) as total_area,
                    SUM(carpets.total_price) as total_value
                ")
                ->groupBy('agents.agent_id', 'users.name')
                ->orderByDesc('total_value')
                ->limit(5)
                ->get();

            $topMaterialSuppliers = DB::table('purchase_materials')
                ->join('string_sellers', 'purchase_materials.seller_id', '=', 'string_sellers.id')
                ->join('material_categories', 'purchase_materials.material_category', '=', 'material_categories.material_category_id')
                ->selectRaw("
                    string_sellers.name,
                    material_categories.subtype,
                    SUM(purchase_materials.quantity) as qty,
                    SUM(purchase_materials.base_currency_amount) as total_value
                ")
                ->groupBy('string_sellers.id', 'string_sellers.name', 'material_categories.subtype')
                ->orderByDesc('total_value')
                ->get();

            // -------------------------------------------------------------
            // 7. FINANCIAL ACTIVITY FEED
            // -------------------------------------------------------------
            
            $activities = DB::table('inventory_transactions')
                ->leftJoin('users', 'inventory_transactions.created_by', '=', 'users.id')
                ->selectRaw("
                    inventory_transactions.type, 
                    inventory_transactions.reference_type, 
                    inventory_transactions.total_cost, 
                    inventory_transactions.created_at, 
                    users.name as user_name
                ")
                ->orderByDesc('inventory_transactions.created_at')
                ->limit(10)
                ->get();

            // -------------------------------------------------------------
            // 8. DATA COMPILATION & VALIDATION
            // -------------------------------------------------------------
            
            // Grand Totals Calculation
            $yRevLif = $yarnSales->lifetime_rev ?? 0;
            $yCostLif = $yarnSales->lifetime_cost ?? 0;
            $dRevLif = $dyeSales->lifetime_rev ?? 0;
            $dCostLif = $dyeSales->lifetime_cost ?? 0;

            $revLif = ($carpetSales->lifetime_rev ?? 0) + $yRevLif + $dRevLif;
            $costLif = ($carpetSales->lifetime_cost ?? 0) + $yCostLif + $dCostLif;
            $profLif = ($carpetSales->lifetime_profit ?? 0) + ($yRevLif - $yCostLif) + ($dRevLif - $dCostLif);

            $yRevTod = $yarnSales->today_rev ?? 0;
            $yCostTod = $yarnSales->today_cost ?? 0;
            $dRevTod = $dyeSales->today_rev ?? 0;
            $dCostTod = $dyeSales->today_cost ?? 0;

            $revTod = ($carpetSales->today_rev ?? 0) + $yRevTod + $dRevTod;
            $costTod = ($carpetSales->today_cost ?? 0) + $yCostTod + $dCostTod;
            $profTod = ($carpetSales->today_profit ?? 0) + ($yRevTod - $yCostTod) + ($dRevTod - $dCostTod);

            $yRevMon = $yarnSales->month_rev ?? 0;
            $yCostMon = $yarnSales->month_cost ?? 0;
            $dRevMon = $dyeSales->month_rev ?? 0;
            $dCostMon = $dyeSales->month_cost ?? 0;

            $revMon = ($carpetSales->month_rev ?? 0) + $yRevMon + $dRevMon;
            $costMon = ($carpetSales->month_cost ?? 0) + $yCostMon + $dCostMon;
            $profMon = ($carpetSales->month_profit ?? 0) + ($yRevMon - $yCostMon) + ($dRevMon - $dCostMon);

            $yRevYr = $yarnSales->year_rev ?? 0;
            $yCostYr = $yarnSales->year_cost ?? 0;
            $dRevYr = $dyeSales->year_rev ?? 0;
            $dCostYr = $dyeSales->year_cost ?? 0;

            $revYr = ($carpetSales->year_rev ?? 0) + $yRevYr + $dRevYr;
            $costYr = ($carpetSales->year_cost ?? 0) + $yCostYr + $dCostYr;
            $profYr = ($carpetSales->year_profit ?? 0) + ($yRevYr - $yCostYr) + ($dRevYr - $dCostYr);

            $carpetInvVal = $carpetInv->total_value ?? 0;
            $yarnInvVal = $yarnInv->total_value ?? 0;
            $dyeInvVal = $dyeInv->total_value ?? 0;
            $totalInvVal = $carpetInvVal + $yarnInvVal + $dyeInvVal;

            // Integrity Validation
            if (round($profLif, 2) != round($revLif - $costLif, 2)) {
                Log::warning('Cost Analytics Validation Error: Lifetime Profit mismatch.', [
                    'computed_profit' => $profLif,
                    'revenue_minus_cost' => $revLif - $costLif
                ]);
            }

            return [
                'executive' => [
                    'lifetime' => ['rev' => $revLif, 'cost' => $costLif, 'profit' => $profLif],
                    'today' => ['rev' => $revTod, 'cost' => $costTod, 'profit' => $profTod],
                    'month' => ['rev' => $revMon, 'cost' => $costMon, 'profit' => $profMon],
                    'year' => ['rev' => $revYr, 'cost' => $costYr, 'profit' => $profYr],
                    'inventory_total' => $totalInvVal
                ],
                'sectors' => [
                    'carpet' => [
                        'rev' => $carpetSales->lifetime_rev ?? 0,
                        'cost' => $carpetSales->lifetime_cost ?? 0,
                        'profit' => $carpetSales->lifetime_profit ?? 0
                    ],
                    'yarn' => [
                        'rev' => $yRevLif,
                        'cost' => $yCostLif,
                        'profit' => $yRevLif - $yCostLif
                    ],
                    'dye' => [
                        'rev' => $dRevLif,
                        'cost' => $dCostLif,
                        'profit' => $dRevLif - $dCostLif
                    ]
                ],
                'inventory' => [
                    'carpet' => ['qty' => $carpetInv->total_qty ?? 0, 'area' => $carpetInv->total_area ?? 0, 'value' => $carpetInvVal],
                    'yarn' => ['qty' => $yarnInv->total_qty ?? 0, 'value' => $yarnInvVal],
                    'dye' => ['qty' => $dyeInv->total_qty ?? 0, 'value' => $dyeInvVal]
                ],
                'build_up' => [
                    'purchase' => $carpetPurchaseCost,
                    'repair' => $repairCost,
                    'wash' => $washCost,
                    'finish' => $finishCost,
                    'total_investment' => $carpetPurchaseCost + $repairCost + $washCost + $finishCost
                ],
                'trends' => [
                    'months' => $monthsLabel,
                    'purchase' => $trendPurchase,
                    'repair' => $trendRepair,
                    'wash' => $trendWash,
                    'finish' => $trendFinish,
                    'total' => array_map(function($p, $r, $w, $f) { return $p + $r + $w + $f; }, $trendPurchase, $trendRepair, $trendWash, $trendFinish)
                ],
                'profitability' => [
                    'types' => $carpetTypeProfitability,
                    'qualities' => $qualityProfitability
                ],
                'suppliers' => [
                    'carpet' => $topCarpetSuppliers,
                    'material' => $topMaterialSuppliers
                ],
                'activities' => $activities
            ];
        });
    }
}
