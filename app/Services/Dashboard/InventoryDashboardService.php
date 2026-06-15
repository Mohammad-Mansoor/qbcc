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
        // Total Values
        $carpetValue = Carpet::where('status', '!=', 6)->sum('total_price') ?? 0;
        
        $yarnValue = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'yarn');
        })->selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;

        $dyeValue = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'dye');
        })->selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;

        // Carpet by Type (Chart Data)
        // Group by type name if possible. Assume Carpet belongs to CarpetType via `type_id` or `type` string.
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

        // Dead Stock Analysis (Carpets sitting for long without update)
        $now = Carbon::now();
        $deadStock = [
            '30_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(30))->where('updated_at', '>=', $now->copy()->subDays(60))->count(),
            '60_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(60))->where('updated_at', '>=', $now->copy()->subDays(90))->count(),
            '90_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(90))->where('updated_at', '>=', $now->copy()->subDays(180))->count(),
            '180_days' => Carpet::where('status', '!=', 6)->where('updated_at', '<', $now->copy()->subDays(180))->count(),
        ];

        // Warehouse Data
        $warehouses = Warehouse::all();
        $warehouseStats = [];
        foreach ($warehouses as $w) {
            // Assume capacity logic or mock it for display
            $warehouseStats[] = [
                'name' => $w->name,
                'type' => $w->type ?? 'General',
                'items_count' => Carpet::where('status', '!=', 6)->where('warehouse_id', $w->id)->count()
            ];
        }

        return [
            'values' => [
                'carpet' => $carpetValue,
                'yarn' => $yarnValue,
                'dye' => $dyeValue,
            ],
            'carpet_by_type' => [
                'labels' => $carpetTypes,
                'data' => $carpetTypeCounts
            ],
            'dead_stock' => $deadStock,
            'warehouses' => $warehouseStats
        ];
    }
}
