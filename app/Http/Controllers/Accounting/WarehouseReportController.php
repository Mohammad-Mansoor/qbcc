<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Carpet;
use App\Quality;
use App\CarpetType;
use App\PurchaseMaterial;
use App\MaterialSale;
use App\MaterialCategory;
use App\MaterialType;

class WarehouseReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Helper to load Base64 header image
     */
    private function getHeaderBase64()
    {
        $headerPath = public_path('images/header.png');
        if (file_exists($headerPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath));
        }
        return '';
    }

    /**
     * Helper to load Base64 logo image
     */
    private function getLogoBase64()
    {
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        return '';
    }

    /**
     * Display Carpet Available Stock Report
     */
    public function carpetStock(Request $request)
    {
        $this->authorize('view_carpet_stock_report');

        // Dropdown option lists
        $qualities = Quality::orderBy('quality', 'asc')->get();
        $carpetTypes = CarpetType::orderBy('carpet_type', 'asc')->get();

        // Extract Filter Inputs (Quality & Type only)
        $qualityId = $request->input('quality_id');
        $typeId = $request->input('type_id');

        $isFiltered = $request->filled('quality_id') || $request->filled('type_id');

        // Human readable filter names for PDF/Excel reports
        $selectedQualityName = 'همه کیفیت‌ها';
        if ($qualityId) {
            $qObj = $qualities->firstWhere('id', $qualityId);
            if ($qObj) $selectedQualityName = $qObj->quality;
        }

        $selectedTypeName = 'همه نوعیت‌ها';
        if ($typeId) {
            $tObj = $carpetTypes->firstWhere('carpet_type_id', $typeId);
            if ($tObj) $selectedTypeName = $tObj->carpet_type;
        }

        // Sold carpet IDs subquery
        $soldCarpetIds = DB::table('sales')
            ->where('is_returned', 0)
            ->pluck('carpet_id')
            ->toArray();

        // Base Query for Summary Cards
        $baseCardQuery = DB::table('carpets')->where('carpets.status', '!=', 6);
        if ($qualityId) $baseCardQuery->where('carpets.quality_id', $qualityId);
        if ($typeId) $baseCardQuery->where('carpets.type_id', $typeId);

        // Top Statistics Card 1: Total Available Inventory (Not Sold)
        $availQuery = (clone $baseCardQuery)->whereNotIn('carpets.carpet_id', $soldCarpetIds);
        $totalAvailableQty = $availQuery->count();
        $totalAvailableSqm = $availQuery->sum(DB::raw('COALESCE(area, buying_area, 0)'));

        // Top Statistics Card 2: Sold Carpets
        $soldQuery = (clone $baseCardQuery)->whereIn('carpets.carpet_id', $soldCarpetIds);
        $totalSoldQty = $soldQuery->count();
        $totalSoldSqm = $soldQuery->sum(DB::raw('COALESCE(area, buying_area, 0)'));

        // Top Statistics Card 3: Ready for Sale Carpets
        $readyQuery = (clone $baseCardQuery)
            ->whereNotIn('carpets.carpet_id', $soldCarpetIds)
            ->whereIn('carpets.status', [1, 5]);
        $totalReadyQty = $readyQuery->count();
        $totalReadySqm = $readyQuery->sum(DB::raw('COALESCE(area, buying_area, 0)'));

        // High-Performance SQL GROUP BY query for bottom card grid
        $gridQuery = DB::table('carpets')
            ->where('carpets.status', '!=', 6)
            ->leftJoin('sales', function($join) {
                $join->on('carpets.carpet_id', '=', 'sales.carpet_id')
                     ->where('sales.is_returned', '=', 0);
            })
            ->leftJoin('qualities', 'carpets.quality_id', '=', 'qualities.id')
            ->leftJoin('carpet_types', 'carpets.type_id', '=', 'carpet_types.carpet_type_id')
            ->select(
                'carpets.quality_id',
                'carpets.type_id',
                DB::raw("COALESCE(qualities.quality, 'عمومی / عمومی') as quality_name"),
                DB::raw("COALESCE(carpet_types.carpet_type, 'سایر / عمومی') as type_name"),
                DB::raw("SUM(CASE WHEN sales.id IS NULL AND carpets.status NOT IN (1, 5) THEN 1 ELSE 0 END) as wip_qty"),
                DB::raw("SUM(CASE WHEN sales.id IS NULL AND carpets.status NOT IN (1, 5) THEN COALESCE(carpets.area, carpets.buying_area, 0) ELSE 0 END) as wip_sqm"),
                DB::raw("SUM(CASE WHEN sales.id IS NULL AND carpets.status IN (1, 5) THEN 1 ELSE 0 END) as ready_qty"),
                DB::raw("SUM(CASE WHEN sales.id IS NULL AND carpets.status IN (1, 5) THEN COALESCE(carpets.area, carpets.buying_area, 0) ELSE 0 END) as ready_sqm"),
                DB::raw("SUM(CASE WHEN sales.id IS NOT NULL THEN 1 ELSE 0 END) as sold_qty"),
                DB::raw("SUM(CASE WHEN sales.id IS NOT NULL THEN COALESCE(carpets.area, carpets.buying_area, 0) ELSE 0 END) as sold_sqm"),
                DB::raw("SUM(COALESCE(carpets.area, carpets.buying_area, 0)) as total_sqm"),
                DB::raw("SUM(COALESCE(carpets.total_price, 0)) as total_cost"),
                DB::raw("COUNT(carpets.carpet_id) as total_count")
            );

        if ($qualityId) $gridQuery->where('carpets.quality_id', $qualityId);
        if ($typeId) $gridQuery->where('carpets.type_id', $typeId);

        $groupedCategories = $gridQuery
            ->groupBy('carpets.quality_id', 'carpets.type_id', 'qualities.quality', 'carpet_types.carpet_type')
            ->orderBy('quality_name', 'asc')
            ->orderBy('type_name', 'asc')
            ->get();

        // Handle PDF Export
        if ($request->get('export') === 'pdf') {
            $topHeaderBase64 = $this->getHeaderBase64();
            $logoBase64 = $this->getLogoBase64();

            return view('accounting.warehouses.pdf_carpet_stock', compact(
                'totalAvailableQty',
                'totalAvailableSqm',
                'totalSoldQty',
                'totalSoldSqm',
                'totalReadyQty',
                'totalReadySqm',
                'groupedCategories',
                'selectedQualityName',
                'selectedTypeName',
                'isFiltered',
                'topHeaderBase64',
                'logoBase64'
            ));
        }

        // Handle Excel Export
        if ($request->get('export') === 'excel') {
            $filename = 'carpet_stock_report_' . date('Y_m_d_His') . '.xls';
            if (!headers_sent()) {
                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
            }

            return view('accounting.warehouses.excel_carpet_stock', compact(
                'totalAvailableQty',
                'totalAvailableSqm',
                'totalSoldQty',
                'totalSoldSqm',
                'totalReadyQty',
                'totalReadySqm',
                'groupedCategories',
                'selectedQualityName',
                'selectedTypeName',
                'isFiltered'
            ));
        }

        return view('accounting.warehouses.carpet_stock', compact(
            'totalAvailableQty',
            'totalAvailableSqm',
            'totalSoldQty',
            'totalSoldSqm',
            'totalReadyQty',
            'totalReadySqm',
            'groupedCategories',
            'qualities',
            'carpetTypes',
            'isFiltered'
        ));
    }

    /**
     * Display Raw Material Available Stock Report
     */
    public function rawMaterialStock(Request $request)
    {
        $this->authorize('view_raw_material_stock_report');

        // Dropdown option lists
        $categories = MaterialCategory::orderBy('material_category', 'asc')->get();
        $types = MaterialType::orderBy('material_type', 'asc')->get();

        // Extract Filter Inputs (Category & Type only)
        $categoryId = $request->input('category_id');
        $typeId = $request->input('type_id');

        $isFiltered = $request->filled('category_id') || $request->filled('type_id');

        // Human readable filter names for PDF/Excel reports
        $selectedCategoryName = 'همه دسته‌ها';
        if ($categoryId) {
            $cObj = $categories->firstWhere('material_category_id', $categoryId);
            if ($cObj) $selectedCategoryName = $cObj->material_category;
        }

        $selectedTypeName = 'همه نوعیت‌ها';
        if ($typeId) {
            $tObj = $types->firstWhere('material_type_id', $typeId);
            if ($tObj) $selectedTypeName = $tObj->material_type;
        }

        // 1. Purchases Grouped Query
        $purchasesQuery = DB::table('purchase_materials')
            ->leftJoin('material_categories', 'purchase_materials.material_category', '=', 'material_categories.material_category_id')
            ->leftJoin('material_types', 'purchase_materials.material_type', '=', 'material_types.material_type_id')
            ->select(
                'purchase_materials.material_category as cat_id',
                'purchase_materials.material_type as type_id',
                DB::raw("COALESCE(material_categories.material_category, 'سایر / عمومی') as category_name"),
                DB::raw("COALESCE(material_types.material_type, 'سایر / عمومی') as type_name"),
                DB::raw('SUM(COALESCE(purchase_materials.quantity, 0)) as total_purchased_kg'),
                DB::raw('SUM(COALESCE(purchase_materials.base_currency_amount, 0)) as total_purchased_cost')
            );

        if ($categoryId) $purchasesQuery->where('purchase_materials.material_category', $categoryId);
        if ($typeId) $purchasesQuery->where('purchase_materials.material_type', $typeId);

        $purchases = $purchasesQuery
            ->groupBy('purchase_materials.material_category', 'purchase_materials.material_type', 'material_categories.material_category', 'material_types.material_type')
            ->get();

        // 2. Sales Grouped Query
        $salesQuery = DB::table('material_sales')
            ->leftJoin('material_categories', 'material_sales.category_id', '=', 'material_categories.material_category_id')
            ->leftJoin('material_types', 'material_sales.type_id', '=', 'material_types.material_type_id')
            ->select(
                'material_sales.category_id as cat_id',
                'material_sales.type_id as type_id',
                DB::raw("COALESCE(material_categories.material_category, 'سایر / عمومی') as category_name"),
                DB::raw("COALESCE(material_types.material_type, 'سایر / عمومی') as type_name"),
                DB::raw('SUM(COALESCE(material_sales.amount, 0)) as total_sold_kg'),
                DB::raw('SUM(COALESCE(material_sales.base_currency_amount, 0)) as total_sold_cost')
            );

        if ($categoryId) $salesQuery->where('material_sales.category_id', $categoryId);
        if ($typeId) $salesQuery->where('material_sales.type_id', $typeId);

        $sales = $salesQuery
            ->groupBy('material_sales.category_id', 'material_sales.type_id', 'material_categories.material_category', 'material_types.material_type')
            ->get();

        // 3. Combine Purchases and Sales into Grouped Structure
        $groupedMaterials = [];

        foreach ($purchases as $p) {
            $key = ($p->cat_id ?: '0') . '_' . ($p->type_id ?: '0');
            $groupedMaterials[$key] = [
                'category_name' => $p->category_name,
                'type_name' => $p->type_name,
                'purchased_kg' => (float) $p->total_purchased_kg,
                'purchased_cost' => (float) $p->total_purchased_cost,
                'sold_kg' => 0.0,
                'sold_cost' => 0.0,
            ];
        }

        foreach ($sales as $s) {
            $key = ($s->cat_id ?: '0') . '_' . ($s->type_id ?: '0');
            if (!isset($groupedMaterials[$key])) {
                $groupedMaterials[$key] = [
                    'category_name' => $s->category_name,
                    'type_name' => $s->type_name,
                    'purchased_kg' => 0.0,
                    'purchased_cost' => 0.0,
                    'sold_kg' => 0.0,
                    'sold_cost' => 0.0,
                ];
            }
            $groupedMaterials[$key]['sold_kg'] += (float) $s->total_sold_kg;
            $groupedMaterials[$key]['sold_cost'] += (float) $s->total_sold_cost;
        }

        // Calculate available stock per group & compute top 4 summary cards
        $totalAvailableDyeKg = 0.0;
        $totalAvailableDyeCost = 0.0;

        $totalAvailableYarnKg = 0.0;
        $totalAvailableYarnCost = 0.0;

        $totalSoldDyeKg = 0.0;
        $totalSoldDyeCost = 0.0;

        $totalSoldYarnKg = 0.0;
        $totalSoldYarnCost = 0.0;

        foreach ($groupedMaterials as $key => &$item) {
            $item['avail_kg'] = max(0, $item['purchased_kg'] - $item['sold_kg']);
            $item['avail_cost'] = max(0, $item['purchased_cost'] - $item['sold_cost']);

            $isDye = (mb_strpos($item['category_name'], 'رنگ') !== false);

            if ($isDye) {
                $totalAvailableDyeKg += $item['avail_kg'];
                $totalAvailableDyeCost += $item['avail_cost'];
                $totalSoldDyeKg += $item['sold_kg'];
                $totalSoldDyeCost += $item['sold_cost'];
            } else {
                $totalAvailableYarnKg += $item['avail_kg'];
                $totalAvailableYarnCost += $item['avail_cost'];
                $totalSoldYarnKg += $item['sold_kg'];
                $totalSoldYarnCost += $item['sold_cost'];
            }
        }
        unset($item);

        // Sort grouped materials by category name then type name
        usort($groupedMaterials, function ($a, $b) {
            return strcmp($a['category_name'] . $a['type_name'], $b['category_name'] . $b['type_name']);
        });

        // Handle PDF Export
        if ($request->get('export') === 'pdf') {
            $topHeaderBase64 = $this->getHeaderBase64();
            $logoBase64 = $this->getLogoBase64();

            return view('accounting.warehouses.pdf_raw_material_stock', compact(
                'totalAvailableDyeKg',
                'totalAvailableDyeCost',
                'totalAvailableYarnKg',
                'totalAvailableYarnCost',
                'totalSoldDyeKg',
                'totalSoldDyeCost',
                'totalSoldYarnKg',
                'totalSoldYarnCost',
                'groupedMaterials',
                'selectedCategoryName',
                'selectedTypeName',
                'isFiltered',
                'topHeaderBase64',
                'logoBase64'
            ));
        }

        // Handle Excel Export
        if ($request->get('export') === 'excel') {
            $filename = 'raw_material_stock_report_' . date('Y_m_d_His') . '.xls';
            if (!headers_sent()) {
                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
            }

            return view('accounting.warehouses.excel_raw_material_stock', compact(
                'totalAvailableDyeKg',
                'totalAvailableDyeCost',
                'totalAvailableYarnKg',
                'totalAvailableYarnCost',
                'totalSoldDyeKg',
                'totalSoldDyeCost',
                'totalSoldYarnKg',
                'totalSoldYarnCost',
                'groupedMaterials',
                'selectedCategoryName',
                'selectedTypeName',
                'isFiltered'
            ));
        }

        return view('accounting.warehouses.raw_material_stock', compact(
            'totalAvailableDyeKg',
            'totalAvailableDyeCost',
            'totalAvailableYarnKg',
            'totalAvailableYarnCost',
            'totalSoldDyeKg',
            'totalSoldDyeCost',
            'totalSoldYarnKg',
            'totalSoldYarnCost',
            'groupedMaterials',
            'categories',
            'types',
            'isFiltered'
        ));
    }
}
