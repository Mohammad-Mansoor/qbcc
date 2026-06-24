<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\InventoryTransaction;
use App\Warehouse;
use Illuminate\Http\Request;

class WarehouseMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['warehouse', 'category', 'creator', 'item'])
            ->where('status', 1);

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by direction
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        // Filter by item type (carpet, material)
        if ($request->filled('item_type')) {
            $itemType = $request->item_type;
            $query->whereHas('item', function ($q) use ($itemType) {
                if ($itemType === 'carpet') {
                    $q->where('type', 'App\Carpet');
                } else {
                    $q->where('type', 'App\MaterialType');
                }
            });
        }

        // Filter by transaction type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by start date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter by end date
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // General search by reference id or item details
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('reference_type', 'like', "%{$search}%")
                  ->orWhereHas('warehouse', function ($wh) use ($search) {
                      $wh->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderBy('created_at', 'desc');

        // Check if PDF export is requested
        if ($request->get('export') === 'pdf') {
            abort_if(!auth()->user()->can('export_warehouse_movements_pdf'), 403);
            $transactions = $query->get();
            return $this->exportToPdf($transactions, $request);
        }

        // Check if Excel export is requested
        if ($request->get('export') === 'excel') {
            abort_if(!auth()->user()->can('export_warehouse_movements_excel'), 403);
            $transactions = $query->get();
            return $this->exportToExcel($transactions, $request);
        }

        // Paginate for web view
        $transactions = $query->paginate(30);

        // Fetch options for filters
        $warehouses = Warehouse::where('is_active', 1)->get();
        
        // Fetch unique transaction types
        $transactionTypes = InventoryTransaction::where('status', 1)
            ->distinct()
            ->pluck('type')
            ->toArray();

        return view('accounting.warehouses.movements', compact(
            'transactions',
            'warehouses',
            'transactionTypes'
        ));
    }

    /**
     * Export movements to PDF view.
     */
    protected function exportToPdf($transactions, Request $request)
    {
        $whName = 'همه گدام‌ها';
        if ($request->filled('warehouse_id')) {
            $wh = Warehouse::find($request->warehouse_id);
            if ($wh) $whName = $wh->name;
        }

        $dirFa = 'همه جهت‌ها';
        if ($request->filled('direction')) {
            $dirFa = $request->direction === 'IN' ? 'ورودی (IN)' : 'خروجی (OUT)';
        }

        $itemTypeFa = 'همه نوعیت‌ها';
        if ($request->filled('item_type')) {
            $itemTypeFa = $request->item_type === 'carpet' ? 'قالین' : 'مواد خام';
        }

        $typeFa = $request->type ?? 'همه تراکنش‌ها';

        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        return view('accounting.warehouses.movements_pdf', compact(
            'transactions',
            'whName',
            'dirFa',
            'itemTypeFa',
            'typeFa',
            'request',
            'logoBase64'
        ));
    }

    /**
     * Export movements to a beautifully formatted Excel/HTML file.
     */
    protected function exportToExcel($transactions, Request $request)
    {
        $filename = 'warehouse_movements_' . date('Y_m_d_His') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // Fetch filter descriptions for the header
        $whName = 'همه گدام‌ها';
        if ($request->filled('warehouse_id')) {
            $wh = Warehouse::find($request->warehouse_id);
            if ($wh) $whName = $wh->name;
        }

        $dirFa = 'همه جهت‌ها';
        if ($request->filled('direction')) {
            $dirFa = $request->direction === 'IN' ? 'ورودی (IN)' : 'خروجی (OUT)';
        }

        $itemTypeFa = 'همه نوعیت‌ها';
        if ($request->filled('item_type')) {
            $itemTypeFa = $request->item_type === 'carpet' ? 'قالین' : 'مواد خام';
        }

        $typeFa = $request->type ?? 'همه تراکنش‌ها';

        echo view('accounting.warehouses.movements_excel', compact(
            'transactions',
            'whName',
            'dirFa',
            'itemTypeFa',
            'typeFa',
            'request'
        ))->render();
        exit;
    }
}
