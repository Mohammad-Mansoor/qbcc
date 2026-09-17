<?php

namespace App\Services\Dashboard;

use App\Carpet;
use App\CarpetType;
use App\MaterialStock;
use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryDashboardService
{
    public function getAnalytics()
    {
        // Total Values & Quantities
        $carpetValue = DB::table('carpets')
            ->where('carpets.status', '!=', 6)
            ->leftJoin('items', function ($join) {
                $join->on('carpets.carpet_id', '=', 'items.ref_id')
                     ->where('items.type', '=', 'App\Carpet');
            })
            ->selectRaw('COALESCE(SUM(COALESCE(items.current_cost, carpets.total_price)), 0) as total')
            ->value('total') ?? 0;
        $carpetCount = Carpet::where('status', '!=', 6)->count();
        $carpetArea = Carpet::where('status', '!=', 6)->sum('area') ?? 0;
        
        $yarnValue = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'yarn')
            ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN total_cost * COALESCE(exchange_rate, 1) ELSE -total_cost * COALESCE(exchange_rate, 1) END) as total")
            ->value('total') ?? 0;
        
        $yarnQuantity = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->where('inventory_transactions.status', 1)
            ->where('inventory_transactions.is_value_adjustment', 0)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'yarn')
            ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as total")
            ->value('total') ?? 0;

        $dyeValue = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'dye')
            ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN total_cost * COALESCE(exchange_rate, 1) ELSE -total_cost * COALESCE(exchange_rate, 1) END) as total")
            ->value('total') ?? 0;

        $dyeQuantity = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->where('inventory_transactions.status', 1)
            ->where('inventory_transactions.is_value_adjustment', 0)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'dye')
            ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as total")
            ->value('total') ?? 0;

        // Carpet by Type
        $carpetByTypeRaw = Carpet::where('status', '!=', 6)
            ->join('carpet_types', 'carpets.type_id', '=', 'carpet_types.carpet_type_id')
            ->select('carpet_types.carpet_type as name', DB::raw('count(carpets.carpet_id) as count'))
            ->groupBy('carpet_types.carpet_type_id', 'carpet_types.carpet_type')
            ->get();

        $carpetTypes = [];
        $carpetTypeCounts = [];
        foreach ($carpetByTypeRaw as $c) {
            $carpetTypes[] = $c->name;
            $carpetTypeCounts[] = $c->count;
        }

        // Carpet by Quality
        $carpetByQualityRaw = Carpet::where('status', '!=', 6)
            ->join('qualities', 'carpets.quality_id', '=', 'qualities.id')
            ->select('qualities.quality as name', DB::raw('count(carpets.carpet_id) as count'))
            ->groupBy('qualities.id', 'qualities.quality')
            ->get();
            
        $carpetQualities = [];
        $carpetQualityCounts = [];
        foreach ($carpetByQualityRaw as $c) {
            $carpetQualities[] = $c->name;
            $carpetQualityCounts[] = $c->count;
        }

        // Yarn by Category
        $yarnByCategoryRaw = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->join('material_categories', 'inventory_transactions.category_id', '=', 'material_categories.material_category_id')
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'yarn')
            ->where('inventory_transactions.is_value_adjustment', 0)
            ->select('material_categories.material_category as name', DB::raw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as count"))
            ->groupBy('material_categories.material_category_id', 'material_categories.material_category')
            ->having('count', '>', 0)
            ->get();
          
        $yarnCategories = [];
        $yarnCategoryCounts = [];
        foreach ($yarnByCategoryRaw as $c) {
            $yarnCategories[] = $c->name;
            $yarnCategoryCounts[] = $c->count;
        }

        // Dye by Category
        $dyeByCategoryRaw = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->join('material_categories', 'inventory_transactions.category_id', '=', 'material_categories.material_category_id')
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'dye')
            ->where('inventory_transactions.is_value_adjustment', 0)
            ->select('material_categories.material_category as name', DB::raw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as count"))
            ->groupBy('material_categories.material_category_id', 'material_categories.material_category')
            ->having('count', '>', 0)
            ->get();
          
        $dyeCategories = [];
        $dyeCategoryCounts = [];
        foreach ($dyeByCategoryRaw as $c) {
            $dyeCategories[] = $c->name;
            $dyeCategoryCounts[] = $c->count;
        }

        // Dead Stock Analysis
        $now = Carbon::now();
        
        $carpetDeadStock = [
            '30_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(30))->where('updated_at', '>=', $now->copy()->subDays(60))->count(),
            '60_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(60))->where('updated_at', '>=', $now->copy()->subDays(90))->count(),
            '90_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(90))->where('updated_at', '>=', $now->copy()->subDays(180))->count(),
            '180_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(180))->count(),
        ];
        
        $yarnDeadStockItems = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'yarn')
            ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
            ->selectRaw("MAX(inventory_transactions.created_at) as last_activity")
            ->groupBy('items.id')
            ->having('balance', '>', 0)
            ->get();
            
        $yarnDeadStock = ['30_days' => 0, '60_days' => 0, '90_days' => 0, '180_days' => 0];
        foreach($yarnDeadStockItems as $item) {
            $days = $now->diffInDays(Carbon::parse($item->last_activity));
            if ($days >= 180) { $yarnDeadStock['180_days'] += $item->balance; }
            elseif ($days >= 90) { $yarnDeadStock['90_days'] += $item->balance; }
            elseif ($days >= 60) { $yarnDeadStock['60_days'] += $item->balance; }
            elseif ($days >= 30) { $yarnDeadStock['30_days'] += $item->balance; }
        }

        $dyeDeadStockItems = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
            ->where('inventory_transactions.status', 1)
            ->where('items.type', 'App\MaterialType')
            ->where('material_types.subtype', 'dye')
            ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity ELSE -quantity END) as balance")
            ->selectRaw("MAX(inventory_transactions.created_at) as last_activity")
            ->groupBy('items.id')
            ->having('balance', '>', 0)
            ->get();
            
        $dyeDeadStock = ['30_days' => 0, '60_days' => 0, '90_days' => 0, '180_days' => 0];
        foreach($dyeDeadStockItems as $item) {
            $days = $now->diffInDays(Carbon::parse($item->last_activity));
            if ($days >= 180) { $dyeDeadStock['180_days'] += $item->balance; }
            elseif ($days >= 90) { $dyeDeadStock['90_days'] += $item->balance; }
            elseif ($days >= 60) { $dyeDeadStock['60_days'] += $item->balance; }
            elseif ($days >= 30) { $dyeDeadStock['30_days'] += $item->balance; }
        }

        // Warehouse Data
        $warehouses = Warehouse::all();
        $warehouseStats = [];
        foreach ($warehouses as $w) {
            $subtype = $w->subtype ?? 'carpet';
            
            if ($subtype === 'carpet') {
                $count = Carpet::where('status', '!=', 6)->where('warehouse_id', $w->id)->count();
                $area = Carpet::where('status', '!=', 6)->where('warehouse_id', $w->id)->sum('area');
                $warehouseStats[] = [
                    'name' => $w->name,
                    'type' => 'قالین',
                    'subtype' => $subtype,
                    'items_count' => $count,
                    'area' => $area
                ];
            } else {
                $balance = DB::table('inventory_transactions')
                    ->where('warehouse_id', $w->id)
                    ->where('status', 1)
                    ->where('is_value_adjustment', 0)
                    ->selectRaw("SUM(CASE WHEN direction = 'IN' THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as balance")
                    ->value('balance') ?? 0;
                    
                $warehouseStats[] = [
                    'name' => $w->name,
                    'type' => $subtype === 'yarn' ? 'تار (خامچه)' : 'رنگ',
                    'subtype' => $subtype,
                    'items_count' => round((float)$balance, 2),
                    'area' => 0
                ];
            }
        }

        return [
            'values' => [
                'carpet' => $carpetValue,
                'yarn' => $yarnValue,
                'dye' => $dyeValue,
            ],
            'quantities' => [
                'carpet_count' => $carpetCount,
                'carpet_area' => $carpetArea,
                'yarn_kilos' => $yarnQuantity,
                'dye_kilos' => $dyeQuantity,
            ],
            'carpet_by_type' => [
                'labels' => $carpetTypes,
                'data' => $carpetTypeCounts
            ],
            'carpet_by_quality' => [
                'labels' => $carpetQualities,
                'data' => $carpetQualityCounts
            ],
            'yarn_by_category' => [
                'labels' => $yarnCategories,
                'data' => $yarnCategoryCounts
            ],
            'dye_by_category' => [
                'labels' => $dyeCategories,
                'data' => $dyeCategoryCounts
            ],
            'dead_stock' => [
                'carpet' => $carpetDeadStock,
                'yarn' => $yarnDeadStock,
                'dye' => $dyeDeadStock,
            ],
            'warehouses' => $warehouseStats
        ];
    }
}
