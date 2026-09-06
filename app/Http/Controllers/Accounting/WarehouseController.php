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
                $query->from('carpets')
                    ->whereColumn('carpets.warehouse_id', 'warehouses.id')
                    ->where('carpets.status', '!=', 6)
                    ->selectRaw("COALESCE(SUM(carpets.area), 0)");
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
        abort_if(!auth()->user()->can('create_warehouse'), 403);
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
        abort_if(!auth()->user()->can('edit_warehouse'), 403);
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
        abort_if(!auth()->user()->can('delete_warehouse'), 403);
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
        abort_if(! (auth()->user()->can('view_warehouse_inventory_report') || auth()->user()->can('view_warehouse_available_stock')), 403);
        return $this->generateStockReport($request, $id, 'view');
    }

    public function stockReportPdf(Request $request, $id)
    {
        abort_if(! (auth()->user()->can('view_warehouse_inventory_report') || auth()->user()->can('view_warehouse_available_stock')), 403);
        return $this->generateStockReport($request, $id, 'pdf');
    }

    public function stockReportExcel(Request $request, $id)
    {
        abort_if(! (auth()->user()->can('view_warehouse_inventory_report') || auth()->user()->can('view_warehouse_available_stock')), 403);
        return $this->generateStockReport($request, $id, 'excel');
    }

    private function generateStockReport(Request $request, $id, $exportType)
    {
        $warehouse = Warehouse::findOrFail($id);
        $issueDate = Carbon::now()->format('Y-m-d H:i');

        $carpetTypes = [];
        $qualities = [];
        $agents = [];
        $materialCategories = [];
        $materialTypes = [];

        if ($warehouse->subtype === 'carpet') {
            $carpetTypes = \App\CarpetType::all();
            $qualities = \App\Quality::all();
            $agents = \App\Agents::with('user')->get();

            $query = \App\Carpet::with(['type', 'quality', 'agent.user'])
                ->where('warehouse_id', $id)
                ->where('status', '!=', 6);

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('carpet_no', 'like', "%{$search}%")
                      ->orWhere('map_number', 'like', "%{$search}%");
                });
            }
            if ($request->filled('type_id')) {
                $query->where('type_id', $request->get('type_id'));
            }
            if ($request->filled('quality_id')) {
                $query->where('quality_id', $request->get('quality_id'));
            }
            if ($request->filled('status') && $request->get('status') !== 'all') {
                $query->where('status', $request->get('status'));
            }
            if ($request->filled('agent_id')) {
                $query->where('agent_id', $request->get('agent_id'));
            }
            
            $items = $query->get();
        } else {
            $materialCategories = \App\MaterialCategory::where('subtype', $warehouse->subtype)->get();
            $materialTypes = \App\MaterialType::where('subtype', $warehouse->subtype)->get();

            $query = \DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
                ->leftJoin('material_categories', 'inventory_transactions.category_id', '=', 'material_categories.material_category_id')
                ->where('inventory_transactions.warehouse_id', $id)
                ->where('inventory_transactions.status', 1)
                ->where('items.type', 'App\MaterialType')
                ->where('material_types.subtype', $warehouse->subtype);

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where('material_types.material_type', 'like', "%{$search}%");
            }
            if ($request->filled('category_id')) {
                $query->where('inventory_transactions.category_id', $request->get('category_id'));
            }
            if ($request->filled('material_type_id')) {
                $query->where('material_types.material_type_id', $request->get('material_type_id'));
            }

            $query->select(
                    'material_types.material_type_id',
                    'material_types.material_type',
                    \DB::raw("MAX(material_categories.material_category) as material_category"),
                    \DB::raw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) as available_qty"),
                    \DB::raw("MAX(items.current_cost) as current_cost")
                )
                ->groupBy('material_types.material_type_id', 'material_types.material_type');
            
            if ($request->filled('min_qty')) {
                $minQty = (float) $request->get('min_qty');
                $query->havingRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) >= ?", [$minQty]);
            }
            if ($request->filled('max_qty')) {
                $maxQty = (float) $request->get('max_qty');
                $query->havingRaw("SUM(CASE WHEN direction = 'IN' THEN inventory_transactions.quantity ELSE -inventory_transactions.quantity END) <= ?", [$maxQty]);
            }

            $items = $query->get();

            $latestPrices = \DB::table('purchase_materials as pm')
                ->select('pm.material_type', 'pm.price_per_kilo')
                ->whereIn('pm.id', function($q) {
                    $q->select(\DB::raw('MAX(id)'))
                      ->from('purchase_materials')
                      ->groupBy('material_type');
                })
                ->get()
                ->keyBy('material_type');

            foreach ($items as $item) {
                $item->last_purchase_price = isset($latestPrices[$item->material_type_id])
                    ? (float) $latestPrices[$item->material_type_id]->price_per_kilo
                    : (float) $item->current_cost;
            }
        }

        $headerPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
        $footerPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));
        $logoPath = public_path(config('company.logo_path', 'images/logos/qasimi_logo.png'));
        
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

        $data = compact('warehouse', 'items', 'issueDate', 'headerBase64', 'footerBase64', 'logoBase64', 'statuses', 'carpetTypes', 'qualities', 'agents', 'materialCategories', 'materialTypes', 'request');

        if ($exportType === 'pdf') {
            return view('accounting.warehouses.stock_report_pdf', $data);
        } elseif ($exportType === 'excel') {
            return view('accounting.warehouses.stock_report_excel', $data);
        }

        return view('accounting.warehouses.stock_report', $data);
    }
}
