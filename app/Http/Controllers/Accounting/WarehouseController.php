<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Activity;
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::select('*')
            ->selectSub(function($query) {
                $query->from('inventory_transactions')
                    ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                    ->whereColumn('inventory_transactions.warehouse_id', 'warehouses.id')
                    ->where('inventory_transactions.status', 1)
                    ->where('items.type', 'App\MaterialType')
                    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END), 0)");
            }, 'material_qty')
            ->selectSub(function($query) {
                $query->from('inventory_transactions')
                    ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                    ->whereColumn('inventory_transactions.warehouse_id', 'warehouses.id')
                    ->where('inventory_transactions.status', 1)
                    ->where('items.type', 'App\Carpet')
                    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END), 0)");
            }, 'carpet_qty')
            ->selectSub(function($query) {
                $query->from('inventory_transactions')
                    ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                    ->whereColumn('inventory_transactions.warehouse_id', 'warehouses.id')
                    ->where('inventory_transactions.status', 1)
                    ->where('inventory_transactions.is_value_adjustment', 0)
                    ->where('items.type', 'App\Carpet')
                    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.area ELSE -inventory_transactions.area END), 0)");
            }, 'carpet_area')
            ->selectSub(function($query) {
                $query->from('inventory_transactions')
                    ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                    ->whereColumn('inventory_transactions.warehouse_id', 'warehouses.id')
                    ->where('inventory_transactions.status', 1)
                    ->selectRaw("COALESCE(SUM((CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) * items.current_cost), 0)");
            }, 'total_asset_value')
            ->get();

        $currencies = \App\Currency::where('is_active', 1)->get();
        return view('accounting.warehouses.index', compact('warehouses', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:warehouses,name',
            'location' => 'nullable',
            'type' => 'nullable|string',
            'subtype' => 'required|in:carpet,yarn,dye'
        ]);

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'type' => $request->type,
            'subtype' => $request->subtype,
            'is_active' => 1
        ]);

        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = " گدام جدید بنام  " . $warehouse->name . " توسط " . Auth::user()->name . " ایجاد شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect()->back()->with('status', 'گدام جدید با موفقیت اضافه شد.');
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:warehouses,name,' . $id,
            'location' => 'nullable',
            'type' => 'nullable|string',
            'subtype' => 'required|in:carpet,yarn,dye',
            'is_active' => 'required|boolean'
        ]);

        $warehouse->update($request->all());


        return redirect()->back()->with('status', 'اطلاعات گدام بروزرسانی شد.');
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        
        // Check if warehouse has transactions before deleting (optional but safer)
        $hasTransactions = \DB::table('inventory_transactions')->where('warehouse_id', $id)->exists();
        
        if ($hasTransactions) {
            return redirect()->back()->with('error', 'این گدام دارای تراکنش است و قابل حذف نمی‌باشد. بجای آن می‌توانید آن را غیرفعال کنید.');
        }

        $warehouse->delete();
        return redirect()->back()->with('status', 'گدام حذف شد.');
    }
}
