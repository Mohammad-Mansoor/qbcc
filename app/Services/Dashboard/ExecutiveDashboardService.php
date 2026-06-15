<?php

namespace App\Services\Dashboard;

use App\Carpet;
use App\Sale;
use App\OfficeCashBook;
use App\MaterialStock;
use App\Currency;
use App\AgentPayment;
use App\CustomerPayment;
use App\DifferentAccountPayment;
use App\EmployeePayment;
use App\FinishingTeamPayment;
use App\KachaeePayment;
use App\SellerPayment;
use App\WashingPayment;
use App\OfficeDebit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardService
{
    /**
     * Get all KPIs for the Executive Dashboard
     */
    public function getKPIs()
    {
        $today = Carbon::today()->format('Y-m-d');
        $thisMonth = Carbon::now()->format('m');
        $thisYear = Carbon::now()->format('Y');

        // Today's Production (assuming carpets added today)
        $todayProduction = Carpet::whereDate('updated_at', $today)->count();

        // Today's Sales
        $todaySales = Sale::whereDate('sale_date', $today)->sum('sale_cost_total') ?? 0;

        // Monthly Revenue
        $monthlyRevenue = Sale::whereMonth('sale_date', $thisMonth)
            ->whereYear('sale_date', $thisYear)
            ->sum('sale_cost_total') ?? 0;

        // Monthly Gross Profit (Revenue - Cost of Goods Sold)
        // Cost of goods sold = total_price of carpets that were sold this month
        $monthlyCogs = Carpet::whereHas('sale', function($q) use ($thisMonth, $thisYear) {
            $q->whereMonth('sale_date', $thisMonth)
              ->whereYear('sale_date', $thisYear);
        })->sum('total_price') ?? 0;
        
        $monthlyGrossProfit = $monthlyRevenue - $monthlyCogs;

        // Cash & Bank Balance
        $cashAndBank = OfficeCashBook::sum('balance') ?? 0;

        // Total Inventory Value (Carpets + Material Stock)
        $carpetValue = Carpet::where('status', '!=', 6)->sum('total_price') ?? 0;
        
        $yarnValue = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'yarn');
        })->selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;

        $dyeValue = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'dye');
        })->selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;
        
        $totalInventoryValue = $carpetValue + $yarnValue + $dyeValue;

        // Receivables and Payables
        $financials = $this->calculateReceivablesAndPayables();

        return [
            'today_production' => $todayProduction,
            'today_sales' => $todaySales,
            'monthly_revenue' => $monthlyRevenue,
            'monthly_gross_profit' => $monthlyGrossProfit,
            'total_inventory_value' => $totalInventoryValue,
            'cash_and_bank' => $cashAndBank,
            'accounts_receivable' => $financials['receivable'], // Rasidat
            'accounts_payable' => $financials['payable'] // Gerft Ha
        ];
    }

    /**
     * Get Production Pipeline stats
     */
    public function getProductionPipeline()
    {
        // Carpet Statuses: 0=Agent, 1=Central, 2=Not Kachaee, 12=Kachaee done, 3=Not Washed, 13=Washed, 4=Tayaari, 5=Ready, 6=Sold
        return [
            'raw_carpet' => Carpet::whereIn('status', [0, 1])->count(),
            'kachayee' => Carpet::whereIn('status', [2, 12])->count(),
            'washing' => Carpet::whereIn('status', [3, 13])->count(),
            'tayaari' => Carpet::where('status', 4)->count(),
            'ready_for_sale' => Carpet::where('status', 5)->count(),
        ];
    }

    /**
     * Get Revenue vs Expense Data (Last 7 Months)
     */
    public function getRevenueVsExpense()
    {
        $months = [];
        $revenue = [];
        $expenses = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $rev = Sale::whereYear('sale_date', $date->year)->whereMonth('sale_date', $date->month)->sum('sale_cost_total') ?? 0;
            // Assuming OfficeDebit holds expenses
            $exp = OfficeDebit::whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount') ?? 0;

            $revenue[] = $rev;
            $expenses[] = $exp;
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'expenses' => $expenses
        ];
    }

    /**
     * Get Inventory Summary for Donut Chart
     */
    public function getInventorySummary()
    {
        $carpetValue = Carpet::where('status', '!=', 6)->sum('total_price') ?? 0;
        
        $yarnValue = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'yarn');
        })->selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;

        $dyeValue = MaterialStock::whereHas('category', function($q) {
            $q->where('subtype', 'dye');
        })->selectRaw('SUM(quantity * price_per_kilo) as total')->value('total') ?? 0;

        return [
            'carpet' => round($carpetValue, 2),
            'yarn' => round($yarnValue, 2),
            'dye' => round($dyeValue, 2)
        ];
    }

    /**
     * Calculate Receivables (Talab / Rasidat) and Payables (Qarz / Gerft ha)
     */
    private function calculateReceivablesAndPayables()
    {
        $currency = Currency::getLegacyAFNRate() ?: 1;

        // Receivables (Rasidat)
        $rasidat = 0;
        $rasidat += AgentPayment::where('type', 'رسید')->sum('amount') + (AgentPayment::where('type', 'رسید')->sum('amount_af') / $currency);
        $rasidat += CustomerPayment::where('type', 'رسید')->sum('amount') + (CustomerPayment::where('type', 'رسید')->sum('amount_af') / $currency);
        $rasidat += DifferentAccountPayment::where('type', 'رسید')->sum('amount');
        $rasidat += EmployeePayment::where('type', 'رسید')->sum('amount') + (EmployeePayment::where('type', 'رسید')->sum('amount_af') / $currency);
        $rasidat += FinishingTeamPayment::where('type', 'رسید')->sum('amount') + (FinishingTeamPayment::where('type', 'رسید')->sum('amount_af') / $currency);
        $rasidat += KachaeePayment::where('type', 'رسید')->sum('amount') + (KachaeePayment::where('type', 'رسید')->sum('amount_af') / $currency);
        $rasidat += SellerPayment::where('type', 'رسید')->sum('amount') + (SellerPayment::where('type', 'رسید')->sum('amount_af') / $currency);
        $rasidat += WashingPayment::where('type', 'رسید')->sum('amount') + (WashingPayment::where('type', 'رسید')->sum('amount_af') / $currency);

        // Payables (Gerft Ha)
        $gerftha = 0;
        $gerftha += AgentPayment::where('type', 'گرفت')->sum('amount') + (AgentPayment::where('type', 'گرفت')->sum('amount_af') / $currency);
        $gerftha += CustomerPayment::where('type', 'گرفت')->sum('amount') + (CustomerPayment::where('type', 'گرفت')->sum('amount_af') / $currency);
        $gerftha += DifferentAccountPayment::where('type', 'گرفت')->sum('amount');
        $gerftha += EmployeePayment::where('type', 'گرفت')->sum('amount') + (EmployeePayment::where('type', 'گرفت')->sum('amount_af') / $currency);
        $gerftha += FinishingTeamPayment::where('type', 'گرفت')->sum('amount') + (FinishingTeamPayment::where('type', 'گرفت')->sum('amount_af') / $currency);
        $gerftha += KachaeePayment::where('type', 'گرفت')->sum('amount') + (KachaeePayment::where('type', 'گرفت')->sum('amount_af') / $currency);
        $gerftha += SellerPayment::where('type', 'گرفت')->sum('amount') + (SellerPayment::where('type', 'گرفت')->sum('amount_af') / $currency);
        $gerftha += WashingPayment::where('type', 'گرفت')->sum('amount') + (WashingPayment::where('type', 'گرفت')->sum('amount_af') / $currency);

        return [
            'receivable' => $rasidat,
            'payable' => $gerftha
        ];
    }
}
