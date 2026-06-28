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

        // KPI: Carpet
        $carpetCount = Carpet::whereNotNull('purchase_invoice_id')->where('status', '!=', 6)->count();
        $carpetArea = Carpet::whereNotNull('purchase_invoice_id')->where('status', '!=', 6)->sum('area');
        $carpetValue = Carpet::whereNotNull('purchase_invoice_id')->where('status', '!=', 6)->sum('total_price') ?? 0;

        // KPI: Yarn
        $yarnValue = \App\PurchaseMaterial::whereHas('materialCategory', function($q) {
            $q->where('subtype', 'yarn');
        })->sum('total') ?? 0;

        $yarnQuantity = \App\PurchaseMaterial::whereHas('materialCategory', function($q) {
            $q->where('subtype', 'yarn');
        })->sum('quantity') ?? 0;

        // KPI: Dye
        $dyeValue = \App\PurchaseMaterial::whereHas('materialCategory', function($q) {
            $q->where('subtype', 'dye');
        })->sum('total') ?? 0;

        $dyeQuantity = \App\PurchaseMaterial::whereHas('materialCategory', function($q) {
            $q->where('subtype', 'dye');
        })->sum('quantity') ?? 0;

        // Top Carpet Suppliers
        $carpetSuppliersRaw = PurchaseInvoice::join('carpets', 'purchase_invoices.id', '=', 'carpets.purchase_invoice_id')
            ->with('agent.user')
            ->select('purchase_invoices.agent_id', DB::raw('SUM(carpets.total_price) as total'))
            ->where('carpets.status', '!=', 6)
            ->groupBy('purchase_invoices.agent_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        
        $carpetSuppliers = ['labels' => [], 'data' => []];
        foreach ($carpetSuppliersRaw as $sup) {
            $name = $sup->agent && $sup->agent->user ? $sup->agent->user->name : 'Supplier ' . $sup->agent_id;
            $carpetSuppliers['labels'][] = $name;
            $carpetSuppliers['data'][] = round($sup->total, 2);
        }

        // Top Material Suppliers
        $materialSuppliersRaw = \App\PurchaseMaterial::with('seller')
            ->select('seller_id', DB::raw('SUM(total) as total_sum'))
            ->groupBy('seller_id')
            ->orderBy('total_sum', 'desc')
            ->take(5)
            ->get();
            
        $materialSuppliers = ['labels' => [], 'data' => []];
        foreach ($materialSuppliersRaw as $sup) {
            $name = $sup->seller ? ($sup->seller->name ?? $sup->seller->company_name) : 'Seller ' . $sup->seller_id;
            $materialSuppliers['labels'][] = $name;
            $materialSuppliers['data'][] = round($sup->total_sum, 2);
        }

        // Purchase Trends (Last 12 Months) - Separated by Area (Sqm) and Materials (Kg)
        $purchaseTrend = [
            'months' => [],
            'carpet_area' => [],
            'yarn_kilos' => [],
            'dye_kilos' => []
        ];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $purchaseTrend['months'][] = $date->format('M Y');
            
            // Carpet Area
            $carpetM = Carpet::join('purchase_invoices', 'carpets.purchase_invoice_id', '=', 'purchase_invoices.id')
                ->whereYear('purchase_invoices.date', $date->year)
                ->whereMonth('purchase_invoices.date', $date->month)
                ->sum('carpets.area') ?? 0;
            $purchaseTrend['carpet_area'][] = round($carpetM, 2);

            // Yarn Kilos
            $yarnK = \App\PurchaseMaterial::whereHas('materialCategory', function($q){$q->where('subtype','yarn');})
                ->whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->sum('quantity') ?? 0;
            $purchaseTrend['yarn_kilos'][] = round($yarnK, 2);
            
            // Dye Kilos
            $dyeK = \App\PurchaseMaterial::whereHas('materialCategory', function($q){$q->where('subtype','dye');})
                ->whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->sum('quantity') ?? 0;
            $purchaseTrend['dye_kilos'][] = round($dyeK, 2);
        }

        // Recent Purchases: Carpet
        $recentCarpets = PurchaseInvoice::with('agent.user')->orderBy('date', 'desc')->take(5)->get()->map(function($inv) {
            return [
                'id' => $inv->id,
                'number' => $inv->invoice_number,
                'supplier' => $inv->agent && $inv->agent->user ? $inv->agent->user->name : 'N/A',
                'date' => Carbon::parse($inv->date)->format('Y-m-d'),
                'amount' => $inv->total_amount ?? 0,
                'status' => $inv->status ?? 'open'
            ];
        })->toArray();

        // Recent Purchases: Materials
        $recentMaterials = \App\RawMaterialPurchaseBill::with('seller')->orderBy('date', 'desc')->take(5)->get()->map(function($bill) {
            return [
                'id' => $bill->id,
                'number' => $bill->bill_number,
                'supplier' => $bill->seller ? ($bill->seller->name ?? $bill->seller->company_name) : 'N/A',
                'date' => Carbon::parse($bill->date)->format('Y-m-d'),
                'amount' => $bill->total_amount ?? 0, // Uses accessor
                'status' => $bill->status ?? 'open'
            ];
        })->toArray();

        return [
            'kpis' => [
                'carpet_count' => $carpetCount,
                'carpet_area' => $carpetArea,
                'carpet_value' => $carpetValue,
                'yarn_quantity' => $yarnQuantity,
                'yarn_value' => $yarnValue,
                'dye_quantity' => $dyeQuantity,
                'dye_value' => $dyeValue,
            ],
            'trends' => $purchaseTrend,
            'suppliers' => [
                'carpet' => $carpetSuppliers,
                'material' => $materialSuppliers
            ],
            'recent_purchases' => [
                'carpet' => $recentCarpets,
                'material' => $recentMaterials
            ]
        ];
    }
}
