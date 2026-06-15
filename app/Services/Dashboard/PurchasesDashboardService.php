<?php

namespace App\Services\Dashboard;

use App\PurchaseInvoice;
use App\Carpet;
use App\Agents;
use App\MaterialStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchasesDashboardService
{
    public function getAnalytics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Total Purchases via Carpet's total_price where purchase_invoice_id exists
        $totalPurchases = Carpet::whereNotNull('purchase_invoice_id')->sum('total_price') ?? 0;
        
        $monthlyPurchases = Carpet::join('purchase_invoices', 'carpets.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->whereYear('purchase_invoices.created_at', $thisYear)
            ->whereMonth('purchase_invoices.created_at', $thisMonth)
            ->sum('carpets.total_price') ?? 0;

        $rawMaterialsValue = MaterialStock::selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;

        $topSuppliersData = [
            'labels' => [],
            'data' => []
        ];

        try {
            // Aggregate actual purchases from PurchaseInvoice grouped by agent/supplier
            $suppliersRaw = PurchaseInvoice::with('agent')
                ->select('supplier_id', DB::raw('SUM(total_amount) as total'))
                ->groupBy('supplier_id')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get();
            
            $idx = 0;
            foreach ($suppliersRaw as $sup) {
                $name = $sup->agent ? ($sup->agent->name ?? $sup->agent->company_name) : 'Supplier ' . $sup->supplier_id;
                if ($idx === 0) $topSupplier = $name;
                $topSuppliersData['labels'][] = $name;
                $topSuppliersData['data'][] = round($sup->total, 2);
                $idx++;
            }
            if(count($topSuppliersData['labels']) == 0){
                $topSupplier = "No Data";
            }
        } catch (\Exception $e) {
            $topSupplier = "No Data";
        }

        // Purchase Trends (Last 12 Months)
        $purchaseTrend = [
            'months' => [],
            'data' => []
        ];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $purchaseTrend['months'][] = $date->format('M Y');
            // Aggregate actual purchases from Carpet
            $monthly = Carpet::join('purchase_invoices', 'carpets.purchase_invoice_id', '=', 'purchase_invoices.id')
                ->whereYear('purchase_invoices.created_at', $date->year)
                ->whereMonth('purchase_invoices.created_at', $date->month)
                ->sum('carpets.total_price') ?? 0;
            $purchaseTrend['data'][] = round($monthly, 2);
        }

        // Restock Needs (Items with low quantity)
        $restockNeeds = [];
        $lowStocks = MaterialStock::where('quantity', '<', 50)->take(5)->get();
        foreach ($lowStocks as $ls) {
            $status = $ls->quantity < 20 ? 'Critical' : 'Low';
            $restockNeeds[] = [
                'name' => $ls->category ? ($ls->category->category_name ?? 'Material') : 'Raw Material',
                'quantity' => $ls->quantity,
                'status' => $status
            ];
        }



        // Recent Purchases
        $recentPurchasesRaw = PurchaseInvoice::orderBy('created_at', 'desc')->take(5)->get();
        $recentPurchases = [];
        foreach ($recentPurchasesRaw as $inv) {
            $recentPurchases[] = [
                'number' => $inv->bill_number ?? $inv->invoice_number ?? $inv->id,
                'date' => Carbon::parse($inv->created_at)->format('Y-m-d'),
                'amount' => $inv->total_amount ?? 0,
                'status' => $inv->status ?? 'Completed'
            ];
        }

        return [
            'kpis' => [
                'total_purchases' => $totalPurchases,
                'monthly_purchases' => $monthlyPurchases,
                'raw_materials_value' => $rawMaterialsValue,
                'top_supplier' => $topSupplier
            ],
            'purchase_trend' => $purchaseTrend,
            'supplier_distribution' => $topSuppliersData,
            'restock_needs' => $restockNeeds,
            'recent_purchases' => $recentPurchases
        ];
    }
}
