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

    public function stockReport(Request $request, $id)
    {
        return $this->generateStockReport($request, $id, 'view');
    }

    public function stockReportPdf(Request $request, $id)
    {
        return $this->generateStockReport($request, $id, 'pdf');
    }

    public function stockReportExcel(Request $request, $id)
    {
        return $this->generateStockReport($request, $id, 'excel');
    }

    private function generateStockReport(Request $request, $id, $exportType)
    {
        $warehouse = Warehouse::findOrFail($id);
        $issueDate = Carbon::now()->format('Y-m-d H:i');

        if ($warehouse->subtype === 'carpet') {
            $items = \App\Carpet::with(['type', 'quality', 'agent.user'])
                ->where('warehouse_id', $id)
                ->where('status', '!=', 6)
                ->get();
        } else {
            $items = \DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
                ->leftJoin('material_categories', 'inventory_transactions.category_id', '=', 'material_categories.material_category_id')
                ->where('inventory_transactions.warehouse_id', $id)
                ->where('inventory_transactions.status', 1)
                ->where('items.type', 'App\MaterialType')
                ->where('material_types.subtype', $warehouse->subtype)
                ->select(
                    'material_types.material_type_id',
                    'material_types.material_type',
                    'material_categories.material_category',
                    \DB::raw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as available_qty"),
                    \DB::raw("MAX(items.current_cost) as current_cost")
                )
                ->groupBy('material_types.material_type_id', 'material_types.material_type', 'material_categories.material_category_id', 'material_categories.material_category')
                ->havingRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) > 0")
                ->get();
        }

        $headerPath = public_path('images/header.png');
        $footerPath = public_path('images/footer.png');
        $logoPath = public_path('images/logo.png');
        
        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : '';
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : '';
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $statuses = [
            '0' => 'شست و شو (Washing)',
            '1' => 'ترمیم (Repairing)',
            '2' => 'تکمیلی (Finishing)',
            '3' => 'آماده فروش (Ready)',
            '4' => 'کچه‌ای (Raw)',
            '5' => 'انتقال شده (Transfer)',
            '6' => 'فروخته شده (Sold)'
        ];

        $data = compact('warehouse', 'items', 'issueDate', 'headerBase64', 'footerBase64', 'logoBase64', 'statuses');

        if ($exportType === 'pdf') {
            return view('accounting.warehouses.stock_report_pdf', $data);
        } elseif ($exportType === 'excel') {
            return view('accounting.warehouses.stock_report_excel', $data);
        }

        return view('accounting.warehouses.stock_report', $data);
    }
}
