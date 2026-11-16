<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WarehouseTransfer;
use App\Warehouse;
use App\Carpet;
use App\MaterialType;
use App\Currency;
use App\Services\AccountSelectionService;
use App\Services\InventoryTransactionManager;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryTransferController extends Controller
{
    protected $txManager;

    public function __construct(
        InventoryTransactionManager $txManager
    ) {
        $this->middleware('auth');
        $this->txManager = $txManager;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $transfers = WarehouseTransfer::with(['sourceWarehouse', 'destinationWarehouse', 'creator'])
            ->orderBy('id', 'desc');

        if ($search) {
            $transfers->where('transfer_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $transfers = $transfers->paginate(20);

        return view('accounting.transfers.index', compact('transfers', 'search'));
    }

    public function create()
    {
        abort_if(!auth()->user()->can('create_inventory_transfer'), 403);
        $lastTransfer = WarehouseTransfer::latest()->first();
        $nextId = $lastTransfer ? ($lastTransfer->id + 1) : 1;
        $transferNo = 'TRF-' . Carbon::today()->format('Ymd') . '-' . sprintf('%03d', $nextId);

        $warehouses = Warehouse::where('is_active', 1)->get();

        return view('accounting.transfers.create', compact(
            'transferNo',
            'warehouses'
        ));
    }

    public function getWarehouseItems(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $itemType = $request->get('item_type');

        if (!$warehouseId || !$itemType) {
            return response()->json(['items' => []]);
        }

        if ($itemType === 'carpet') {
            $carpets = Carpet::where('warehouse_id', $warehouseId)
                ->where('status', '!=', 6)
                ->select('carpet_id', 'carpet_no', 'area', 'total_price', 'status')
                ->get()
                ->map(function($c) {
                    $c->status_txt = $this->getCarpetStatusText($c->status);
                    $c->cost_usd = DB::table('items')
                        ->where('type', 'App\Carpet')
                        ->where('ref_id', $c->carpet_id)
                        ->value('current_cost') ?? (float)$c->total_price;
                    return $c;
                });
            return response()->json(['items' => $carpets]);
        } else {
            $materials = DB::table('inventory_transactions')
                ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
                ->join('material_types', 'items.ref_id', '=', 'material_types.material_type_id')
                ->leftJoin('material_categories', 'inventory_transactions.category_id', '=', 'material_categories.material_category_id')
                ->where('inventory_transactions.warehouse_id', $warehouseId)
                ->where('inventory_transactions.status', 1)
                ->where('items.type', 'App\MaterialType')
                ->where('material_types.subtype', $itemType)
                ->select(
                    'inventory_transactions.category_id',
                    DB::raw("COALESCE(material_categories.material_category, 'عمومی / نامشخص') as category_name"),
                    'material_types.material_type_id',
                    'material_types.material_type as name',
                    DB::raw("MAX(items.current_cost) as cost_usd"),
                    DB::raw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as available_qty")
                )
                ->groupBy('inventory_transactions.category_id', 'material_categories.material_category', 'material_types.material_type_id', 'material_types.material_type')
                ->havingRaw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) > 0.0001")
                ->get()
                ->map(function($m) {
                    return [
                        'category_id' => $m->category_id,
                        'category_name' => $m->category_name,
                        'material_type_id' => $m->material_type_id,
                        'name' => $m->name,
                        'available_qty' => (float)$m->available_qty,
                        'cost_usd' => (float)$m->cost_usd
                    ];
                });

            return response()->json(['items' => $materials]);
        }
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create_inventory_transfer'), 403);
        $request->validate([
            'transfer_number' => 'required|unique:warehouse_transfers',
            'transfer_date' => 'required|date',
            'item_type' => 'required|in:carpet,yarn,dye',
            'source_warehouse_id' => 'required',
            'destination_warehouse_id' => 'required|different:source_warehouse_id',
            'items' => 'required|array'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $transfer = WarehouseTransfer::create([
                    'transfer_number' => $request->transfer_number,
                    'transfer_date' => $request->transfer_date,
                    'item_type' => $request->item_type,
                    'source_warehouse_id' => $request->source_warehouse_id,
                    'destination_warehouse_id' => $request->destination_warehouse_id,
                    'quantity' => 0,
                    'description' => $request->description,
                    'created_by' => auth()->id(),
                    'status' => 'posted'
                ]);

                $itemsData = [];
                $totalQty = 0;

                foreach ($request->items as $row) {
                    // Checkbox items for carpets might be array format or might have quantity for raw materials
                    if ($request->item_type === 'carpet') {
                        // For carpets, checkboxes are submitted with keys like: items[index][id]
                        if (!isset($row['id'])) continue;
                        $model = Carpet::findOrFail($row['id']);
                        if ($model->warehouse_id != $request->source_warehouse_id) {
                            throw new \Exception("قالین نمبر {$model->carpet_no} در گدام مبدا وجود ندارد.");
                        }
                        $itemsData[] = [
                            'model' => $model,
                            'quantity' => 1
                        ];
                        $totalQty += 1;
                    } else {
                        if (!isset($row['id']) || !isset($row['quantity'])) continue;
                        $model = MaterialType::findOrFail($row['id']);
                        $qty = (float)$row['quantity'];
                        if ($qty <= 0) continue;

                        $categoryId = isset($row['category_id']) && $row['category_id'] !== '' ? (int)$row['category_id'] : null;

                        $item = DB::table('items')
                            ->where('type', 'App\MaterialType')
                            ->where('ref_id', $model->material_type_id)
                            ->first();
                        
                        $available = 0;
                        if ($item) {
                            $stockQuery = DB::table('inventory_transactions')
                                ->where('item_id', $item->id)
                                ->where('warehouse_id', $request->source_warehouse_id)
                                ->where('status', 1);

                            if ($categoryId) {
                                $stockQuery->where('category_id', $categoryId);
                            }

                            $available = $stockQuery->selectRaw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as balance")
                                ->value('balance') ?? 0;
                        }

                        if ($qty > $available) {
                            throw new \Exception("موجودی مواد {$model->material_type} در گدام مبدا کافی نیست. موجودی: {$available} کیلوگرام.");
                        }

                        $itemsData[] = [
                            'model' => $model,
                            'category_id' => $categoryId,
                            'quantity' => $qty
                        ];
                        $totalQty += $qty;
                    }
                }

                if (empty($itemsData)) {
                    throw new \Exception("لطفا حداقل یک جنس را برای انتقال انتخاب کنید.");
                }

                $transfer->quantity = $totalQty;
                $transfer->save();

                $this->txManager->processWarehouseTransfer($transfer, $itemsData);

                return redirect()->route('accounting.transfers.index')
                    ->with('status', "مکتوب انتقال {$transfer->transfer_number} با موفقیت ثبت شد.");
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $transfer = WarehouseTransfer::with(['items.itemModel', 'items.category', 'sourceWarehouse', 'destinationWarehouse', 'creator'])
            ->findOrFail($id);

        return view('accounting.transfers.show', compact('transfer'));
    }

    public function reverse($id, Request $request)
    {
        abort_if(!auth()->user()->can('reverse_inventory_transfer'), 403);
        $request->validate([
            'reversal_reason' => 'required|string|max:255'
        ]);

        $transfer = WarehouseTransfer::findOrFail($id);
        if ($transfer->status === 'reversed') {
            return back()->with('error', 'این سند قبلا باطل شده است.');
        }

        try {
            $this->txManager->reverseWarehouseTransfer($transfer, $request->reversal_reason);
            return redirect()->route('accounting.transfers.index')
                ->with('status', "سند انتقال {$transfer->transfer_number} با موفقیت باطل (ریورس) شد.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function getCarpetStatusText($status)
    {
        $statusMap = [
            0 => 'قراردادی',
            1 => 'گدام مرکزی (سالم)',
            2 => 'کچایی (تحت ترمیم)',
            3 => 'شستشو',
            4 => 'تیاری (پرداخت)',
            5 => 'آماده فروش',
            6 => 'فروخته شده',
            12 => 'ترمیم شده (کچایی)',
            13 => 'ارسال به تیاری'
        ];
        return $statusMap[$status] ?? 'نامشخص';
    }
}
