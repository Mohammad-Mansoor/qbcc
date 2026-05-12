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
        $warehouses = Warehouse::all();

        foreach ($warehouses as $w) {
            // Calculate Material Stock
            $materialStats = \DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->where('inventory_transactions.warehouse_id', $w->id)
                ->where('inventory_transactions.status', 1)
                ->where('items.type', 'App\PurchaseMaterial')
                ->select(
                    \DB::raw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as total_qty"),
                    \DB::raw("SUM((CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) * items.current_cost) as total_val")
                )
                ->first();

            // Calculate Carpet Stock (Quantity only usually, but let's try value if available)
            $carpetStats = \DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->where('inventory_transactions.warehouse_id', $w->id)
                ->where('inventory_transactions.status', 1)
                ->where('items.type', 'App\Carpet')
                ->select(
                    \DB::raw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as total_qty"),
                    \DB::raw("SUM((CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) * items.current_cost) as total_val")
                )
                ->first();

            $w->material_qty = $materialStats->total_qty ?? 0;
            $w->carpet_qty = $carpetStats->total_qty ?? 0;
            $w->total_asset_value = ($materialStats->total_val ?? 0) + ($carpetStats->total_val ?? 0);
        }

        return view('accounting.warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:warehouses,name',
            'location' => 'nullable',
            'type' => 'nullable|string'
        ]);

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'type' => $request->type,
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
