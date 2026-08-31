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
                     ->where('items.type', '=', 'App\\Carpet');
            })
            ->selectRaw('COALESCE(SUM(COALESCE(items.current_cost, carpets.total_price)), 0) as total')
            ->value('total') ?? 0;
        $carpetCount = Carpet::where('status', '!=', 6)->count();
        $carpetArea = Carpet::where('status', '!=', 6)->sum('area') ?? 0;
        
        $yarnValue = DB::table('material_stocks')
            ->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
            ->leftJoin('items', function($join) {
                $join->on('material_stocks.material_type', '=', 'items.ref_id')
                     ->where('items.type', '=', 'App\MaterialType');
            })
            ->where('material_categories.subtype', 'yarn')
            ->selectRaw('SUM(material_stocks.quantity * COALESCE(items.current_cost, material_stocks.price_per_kilo, 0)) as total')
            ->value('total') ?? 0;
        
        $yarnQuantity = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'yarn');
        })->sum('quantity') ?? 0;

        $dyeValue = DB::table('material_stocks')
            ->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
            ->leftJoin('items', function($join) {
                $join->on('material_stocks.material_type', '=', 'items.ref_id')
                     ->where('items.type', '=', 'App\MaterialType');
            })
            ->where('material_categories.subtype', 'dye')
            ->selectRaw('SUM(material_stocks.quantity * COALESCE(items.current_cost, material_stocks.price_per_kilo, 0)) as total')
            ->value('total') ?? 0;

        $dyeQuantity = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'dye');
        })->sum('quantity') ?? 0;

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
        $yarnByCategoryRaw = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'yarn');
        })->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
          ->select('material_categories.material_category as name', DB::raw('SUM(material_stocks.quantity) as count'))
          ->groupBy('material_categories.material_category_id', 'material_categories.material_category')
          ->get();
          
        $yarnCategories = [];
        $yarnCategoryCounts = [];
        foreach ($yarnByCategoryRaw as $c) {
            $yarnCategories[] = $c->name;
            $yarnCategoryCounts[] = $c->count;
        }

        // Dye by Category
        $dyeByCategoryRaw = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'dye');
        })->join('material_categories', 'material_stocks.material_category', '=', 'material_categories.material_category_id')
          ->select('material_categories.material_category as name', DB::raw('SUM(material_stocks.quantity) as count'))
          ->groupBy('material_categories.material_category_id', 'material_categories.material_category')
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
        
        $yarnDeadStock = [
            '30_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','yarn');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(30))->where('updated_at', '>=', $now->copy()->subDays(60))->sum('quantity'),
            '60_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','yarn');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(60))->where('updated_at', '>=', $now->copy()->subDays(90))->sum('quantity'),
            '90_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','yarn');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(90))->where('updated_at', '>=', $now->copy()->subDays(180))->sum('quantity'),
            '180_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','yarn');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(180))->sum('quantity'),
        ];
        
        $dyeDeadStock = [
            '30_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','dye');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(30))->where('updated_at', '>=', $now->copy()->subDays(60))->sum('quantity'),
            '60_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','dye');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(60))->where('updated_at', '>=', $now->copy()->subDays(90))->sum('quantity'),
            '90_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','dye');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(90))->where('updated_at', '>=', $now->copy()->subDays(180))->sum('quantity'),
            '180_days' => MaterialStock::whereHas('category', function($q){$q->where('subtype','dye');})->where('quantity','>',0)->where('updated_at', '<', $now->copy()->subDays(180))->sum('quantity'),
        ];

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
