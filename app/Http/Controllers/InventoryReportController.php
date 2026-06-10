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
        $warehouseId = $request->get('warehouse_id');
        $itemType = $request->get('item_type');

        $query = DB::table('items')
            ->leftJoin('inventory_transactions', function($join) use ($warehouseId) {
                $join->on('items.id', '=', 'inventory_transactions.item_id')
                     ->where('inventory_transactions.status', 1);
                if ($warehouseId) {
                    $join->where('inventory_transactions.warehouse_id', $warehouseId);
                }
            })
            ->select(
                'items.id',
                'items.type',
                'items.ref_id',
                'items.current_cost',
                DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.quantity WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.quantity ELSE 0 END) as stock_balance"),
                DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' AND inventory_transactions.is_value_adjustment = 0 THEN inventory_transactions.area WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.area ELSE 0 END) as area_balance"),
                DB::raw("SUM(CASE WHEN inventory_transactions.direction = 'IN' THEN inventory_transactions.total_cost WHEN inventory_transactions.direction = 'OUT' THEN -inventory_transactions.total_cost ELSE 0 END) as total_value")
            )
            ->groupBy('items.id', 'items.type', 'items.ref_id', 'items.current_cost');

        if ($itemType) {
            $query->where('items.type', $itemType);
        }

        $items = $query->paginate(20);
        $warehouses = DB::table('warehouses')->get();

        return view('inventory.reports.index', compact('items', 'warehouses'));
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
