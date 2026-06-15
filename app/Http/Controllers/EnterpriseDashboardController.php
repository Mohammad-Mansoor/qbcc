<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Dashboard\ExecutiveDashboardService;
use App\Services\Dashboard\ProductionDashboardService;
use App\Services\Dashboard\InventoryDashboardService;
use App\Services\Dashboard\FinanceDashboardService;
use App\Services\Dashboard\SalesDashboardService;
use App\Services\Dashboard\CostAnalyticsService;
use App\Services\Dashboard\PurchasesDashboardService;

class EnterpriseDashboardController extends Controller
{
    protected $executiveService;
    protected $productionService;
    protected $inventoryService;
    protected $financeService;
    protected $salesService;
    protected $costAnalyticsService;
    protected $purchasesService;

    public function __construct(
        ExecutiveDashboardService $executiveService, 
        ProductionDashboardService $productionService,
        InventoryDashboardService $inventoryService,
        FinanceDashboardService $financeService,
        SalesDashboardService $salesService,
        CostAnalyticsService $costAnalyticsService,
        PurchasesDashboardService $purchasesService
    ) {
        $this->executiveService = $executiveService;
        $this->productionService = $productionService;
        $this->inventoryService = $inventoryService;
        $this->financeService = $financeService;
        $this->salesService = $salesService;
        $this->costAnalyticsService = $costAnalyticsService;
        $this->purchasesService = $purchasesService;
    }

    /**
     * Display the Executive Dashboard
     */
    public function index()
    {
        $kpis = $this->executiveService->getKPIs();
        $pipeline = $this->executiveService->getProductionPipeline();
        $revenueExpenseChart = $this->executiveService->getRevenueVsExpense();
        $inventoryDonut = $this->executiveService->getInventorySummary();
        
        return view('dsh.enterprise.executive', compact('kpis', 'pipeline', 'revenueExpenseChart', 'inventoryDonut'));
    }

    // Additional methods for other dashboards will be added here
    public function production()
    {
        $data = $this->productionService->getAnalytics();
        return view('dsh.enterprise.production', compact('data'));
    }

    public function inventory()
    {
        $data = $this->inventoryService->getAnalytics();
        return view('dsh.enterprise.inventory', compact('data'));
    }

    public function finance()
    {
        $data = $this->financeService->getAnalytics();
        return view('dsh.enterprise.finance', compact('data'));
    }

    public function sales()
    {
        $data = $this->salesService->getAnalytics();
        return view('dsh.enterprise.sales', compact('data'));
    }

    public function costAnalytics()
    {
        $data = $this->costAnalyticsService->getAnalytics();
        return view('dsh.enterprise.cost-analytics', compact('data'));
    }

    public function purchases()
    {
        $data = $this->purchasesService->getAnalytics();
        return view('dsh.enterprise.purchases', compact('data'));
    }

    public function hr()
    {
        return view('dsh.enterprise.hr');
    }
}
