<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\InventoryTransaction;

class InventoryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Unified Inventory Stock Report
     */
    public function index(Request $request)
    {
        // Fetch ALL positive stock grouped by item and warehouse.
        $balances = DB::table('inventory_transactions')
            ->join('items', 'items.id', '=', 'inventory_transactions.item_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'inventory_transactions.warehouse_id')
            ->where('inventory_transactions.status', 1)
            ->select(
                'items.id as item_id',
                'items.type as item_model',
                'items.ref_id',
                'inventory_transactions.category_id',
                'warehouses.id as warehouse_id',
                'warehouses.name as warehouse_name',
                DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.quantity WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.quantity ELSE 0 END) as qty"),
                DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.area WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.area ELSE 0 END) as area"),
                DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' THEN inventory_transactions.total_cost WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.total_cost ELSE 0 END) as total_value")
            )
            ->groupBy('items.id', 'items.type', 'items.ref_id', 'inventory_transactions.category_id', 'warehouses.id', 'warehouses.name')
            ->havingRaw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.quantity WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.quantity ELSE 0 END) > 0 OR SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.area WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.area ELSE 0 END) > 0")
            ->get();

        $materialTypeIds = [];
        $materialCategoryIds = [];
        $carpetIds = [];
        foreach($balances as $b) {
            if ($b->item_model == 'App\MaterialType') {
                $materialTypeIds[] = $b->ref_id;
                if ($b->category_id) {
                    $materialCategoryIds[] = $b->category_id;
                }
            } elseif ($b->item_model == 'App\Carpet') {
                $carpetIds[] = $b->ref_id;
            }
        }

        $materialTypes = [];
        if (count($materialTypeIds) > 0) {
            $materialTypes = DB::table('material_types')
                ->whereIn('material_type_id', array_unique($materialTypeIds))
                ->get()
                ->keyBy('material_type_id');
        }

        $materialCategories = [];
        if (count($materialCategoryIds) > 0) {
            $materialCategories = DB::table('material_categories')
                ->whereIn('material_category_id', array_unique($materialCategoryIds))
                ->get()
                ->keyBy('material_category_id');
        }

        $carpets = [];
        if (count($carpetIds) > 0) {
            $carpets = DB::table('carpets')
                ->whereIn('carpet_id', array_unique($carpetIds))
                ->leftJoin('carpet_types', 'carpet_types.carpet_type_id', '=', 'carpets.type_id')
                ->leftJoin('qualities', 'qualities.id', '=', 'carpets.quality_id')
                ->select(
                    'carpets.carpet_id',
                    'carpets.status',
                    'carpet_types.carpet_type as type_name',
                    'qualities.quality as quality_name'
                )
                ->get()
                ->keyBy('carpet_id');
        }

        $dashboardData = [
            'yarn' => ['by_cat_type' => [], 'by_cat' => [], 'by_type' => []],
            'dye' => ['by_cat_type' => [], 'by_cat' => [], 'by_type' => []],
            'carpet' => ['by_qual_type' => [], 'by_qual' => [], 'by_type' => []],
            'ready_carpet' => ['by_qual_type' => [], 'by_qual' => [], 'by_type' => []]
        ];

        $totals = [
            'yarn_qty' => 0,
            'dye_qty' => 0,
            'carpet_qty' => 0,
            'carpet_area' => 0,
            'ready_carpet_qty' => 0,
            'ready_carpet_area' => 0,
            'total_value' => 0
        ];

        foreach($balances as $b) {
            $totals['total_value'] += $b->total_value;
            $whName = $b->warehouse_name ?: 'بدون گدام';

            if ($b->item_model == 'App\MaterialType' && isset($materialTypes[$b->ref_id])) {
                $mt = $materialTypes[$b->ref_id];
                $mcName = isset($materialCategories[$b->category_id]) ? $materialCategories[$b->category_id]->material_category : 'بدون کتگوری';
                $sub = $mt->subtype == 'dye' ? 'dye' : 'yarn'; 
                $typeName = $mt->material_type ?: 'نامشخص';
                $groupKey = $typeName . ' - ' . $mcName;
                
                // Initialize combined Cat & Type
                if (!isset($dashboardData[$sub]['by_cat_type'][$groupKey])) {
                    $dashboardData[$sub]['by_cat_type'][$groupKey] = ['total' => 0, 'warehouses' => []];
                }
                if (!isset($dashboardData[$sub]['by_cat_type'][$groupKey]['warehouses'][$whName])) {
                    $dashboardData[$sub]['by_cat_type'][$groupKey]['warehouses'][$whName] = 0;
                }
                
                $dashboardData[$sub]['by_cat_type'][$groupKey]['total'] += $b->qty;
                $dashboardData[$sub]['by_cat_type'][$groupKey]['warehouses'][$whName] += $b->qty;

                // Initialize by_cat
                if (!isset($dashboardData[$sub]['by_cat'][$mcName])) {
                    $dashboardData[$sub]['by_cat'][$mcName] = 0;
                }
                $dashboardData[$sub]['by_cat'][$mcName] += $b->qty;

                // Initialize by_type
                if (!isset($dashboardData[$sub]['by_type'][$typeName])) {
                    $dashboardData[$sub]['by_type'][$typeName] = 0;
                }
                $dashboardData[$sub]['by_type'][$typeName] += $b->qty;

                if ($sub == 'yarn') $totals['yarn_qty'] += $b->qty;
                else $totals['dye_qty'] += $b->qty;

            } elseif ($b->item_model == 'App\Carpet' && isset($carpets[$b->ref_id])) {
                $c = $carpets[$b->ref_id];
                $isReady = ($c->status == 5);
                
                $typeName = $c->type_name ?: 'نامشخص';
                $qualName = $c->quality_name ?: 'نامشخص';
                $groupKey = $typeName . ' - ' . $qualName;
                $sub = $isReady ? 'ready_carpet' : 'carpet';

                // Initialize combined Qual & Type
                if (!isset($dashboardData[$sub]['by_qual_type'][$groupKey])) {
                    $dashboardData[$sub]['by_qual_type'][$groupKey] = ['total_qty' => 0, 'total_area' => 0, 'warehouses' => []];
                }
                if (!isset($dashboardData[$sub]['by_qual_type'][$groupKey]['warehouses'][$whName])) {
                    $dashboardData[$sub]['by_qual_type'][$groupKey]['warehouses'][$whName] = ['qty' => 0, 'area' => 0];
                }

                $dashboardData[$sub]['by_qual_type'][$groupKey]['total_qty'] += $b->qty;
                $dashboardData[$sub]['by_qual_type'][$groupKey]['total_area'] += $b->area;
                $dashboardData[$sub]['by_qual_type'][$groupKey]['warehouses'][$whName]['qty'] += $b->qty;
                $dashboardData[$sub]['by_qual_type'][$groupKey]['warehouses'][$whName]['area'] += $b->area;

                // Initialize by_qual
                if (!isset($dashboardData[$sub]['by_qual'][$qualName])) {
                    $dashboardData[$sub]['by_qual'][$qualName] = ['qty' => 0, 'area' => 0];
                }
                $dashboardData[$sub]['by_qual'][$qualName]['qty'] += $b->qty;
                $dashboardData[$sub]['by_qual'][$qualName]['area'] += $b->area;

                // Initialize by_type
                if (!isset($dashboardData[$sub]['by_type'][$typeName])) {
                    $dashboardData[$sub]['by_type'][$typeName] = ['qty' => 0, 'area' => 0];
                }
                $dashboardData[$sub]['by_type'][$typeName]['qty'] += $b->qty;
                $dashboardData[$sub]['by_type'][$typeName]['area'] += $b->area;

                if ($isReady) {
                    $totals['ready_carpet_qty'] += $b->qty;
                    $totals['ready_carpet_area'] += $b->area;
                } else {
                    $totals['carpet_qty'] += $b->qty;
                    $totals['carpet_area'] += $b->area;
                }
            }
        }

        return view('inventory.reports.index', compact('dashboardData', 'totals'));
    }

    /**
     * Detailed Audit Trail for a specific Item
     */
    public function detail($id)
    {
        $item = DB::table('items')->where('id', $id)->first();
        if (!$item) abort(404);

        $transactions = DB::table('inventory_transactions')
            ->where('item_id', $id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get legacy details for header
        $legacyData = DB::table(str_replace('App\\', '', $item->type) == 'Carpet' ? 'carpets' : 'material_stocks')
            ->where($item->type == 'App\\Carpet' ? 'carpet_id' : 'id', $item->ref_id)
            ->first();

        return view('inventory.reports.detail', compact('item', 'transactions', 'legacyData'));
    }

    /**
     * Work In Progress (WIP) Valuation Report
     */
    public function wipReport()
    {
        // Items that have been "Issued to Production" but not yet "Finished"
        // In our simple model, we can track by transactions of type PROD_ISSUE vs PROD_FINISH
        // Or simply items that are currently in 'WIP' status in legacy tables.
        
        $wipItems = DB::table('inventory_transactions')
            ->join('items', 'items.id', '=', 'inventory_transactions.item_id')
            ->where('inventory_transactions.status', 1)
            ->select(
                'items.id',
                'items.type',
                'items.ref_id',
                DB::raw("SUM(total_cost) as total_investment")
            )
            ->groupBy('items.id', 'items.type', 'items.ref_id')
            ->havingRaw("SUM(CASE WHEN inventory_transactions.type = 'PROD_FINISH' THEN 1 ELSE 0 END) = 0")
            ->whereIn('inventory_transactions.type', ['PROD_ISSUE', 'PROD_SERVICE', 'PURCHASE']) // Items still in flow
            ->get();

        return view('inventory.reports.wip', compact('wipItems'));
    }
}
