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
            $materials = MaterialType::where('subtype', $itemType)
                ->get()
                ->map(function($m) use ($warehouseId) {
                    $item = DB::table('items')
                        ->where('type', 'App\MaterialType')
                        ->where('ref_id', $m->material_type_id)
                        ->first();
                    
                    $qty = 0;
                    $costUsd = $m->price_per_kilo ?? 0;
                    if ($item) {
                        $qty = DB::table('inventory_transactions')
                            ->where('item_id', $item->id)
                            ->where('warehouse_id', $warehouseId)
                            ->where('status', 1)
                            ->selectRaw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as balance")
                            ->value('balance') ?? 0;
                        $costUsd = $item->current_cost;
                    }

                    return [
                        'material_type_id' => $m->material_type_id,
                        'name' => $m->material_type,
                        'available_qty' => (float)$qty,
                        'cost_usd' => (float)$costUsd
                    ];
                })->filter(function($m) {
                    return $m['available_qty'] > 0;
                })->values();

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

                        $item = DB::table('items')
                            ->where('type', 'App\MaterialType')
                            ->where('ref_id', $model->material_type_id)
                            ->first();
                        
                        $available = 0;
                        if ($item) {
                            $available = DB::table('inventory_transactions')
                                ->where('item_id', $item->id)
                                ->where('warehouse_id', $request->source_warehouse_id)
                                ->where('status', 1)
                                ->selectRaw("SUM(CASE WHEN direction = 'IN' AND is_value_adjustment = 0 THEN quantity WHEN direction = 'OUT' THEN -quantity ELSE 0 END) as balance")
                                ->value('balance') ?? 0;
                        }

                        if ($qty > $available) {
                            throw new \Exception("موجودی مواد {$model->material_type} در گدام مبدا کافی نیست. موجودی: {$available} کیلوگرام.");
                        }

                        $itemsData[] = [
                            'model' => $model,
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
        $transfer = WarehouseTransfer::with(['items.itemModel', 'sourceWarehouse', 'destinationWarehouse', 'creator'])
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
