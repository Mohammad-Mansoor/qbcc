<?php

namespace App\Services\Dashboard;

use App\Carpet;
use App\CarpetType;
use App\Quality;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CostAnalyticsService
{
    public function getAnalytics()
    {
        $totalCarpets = Carpet::count();
        
        // 1. Overall Cost Breakdown
        $officeExpenses = \App\OfficeDebit::all();
        $overallCosts = [
            'Yarn (تار)' => 0,
            'Dye (رنگ)' => 0,
            'Labor (معاش)' => 0,
            'Overhead (سایر)' => 0
        ];
        foreach ($officeExpenses as $exp) {
            $desc = mb_strtolower($exp->description ?? '');
            if (mb_strpos($desc, 'yarn') !== false || mb_strpos($desc, 'تار') !== false) {
                $overallCosts['Yarn (تار)'] += $exp->amount;
            } elseif (mb_strpos($desc, 'dye') !== false || mb_strpos($desc, 'رنگ') !== false) {
                $overallCosts['Dye (رنگ)'] += $exp->amount;
            } elseif (mb_strpos($desc, 'salary') !== false || mb_strpos($desc, 'معاش') !== false || mb_strpos($desc, 'مزد') !== false) {
                $overallCosts['Labor (معاش)'] += $exp->amount;
            } else {
                $overallCosts['Overhead (سایر)'] += $exp->amount;
            }
        }

        // 2. Average Cost by Carpet Type (Since exact SQM per carpet requires parsing size strings)
        $costPerSqm = [];
        $carpetTypesRaw = CarpetType::all();
        foreach ($carpetTypesRaw as $ct) {
            $avgCost = Carpet::where('type_id', $ct->carpet_type_id)->avg('total_price') ?? 0;
            if ($avgCost > 0) {
                $costPerSqm[$ct->carpet_type] = round($avgCost, 2);
            }
        }

        // 3. Profitability By Carpet Type (Table)
        $typeProfitability = [];
        $mostProfitable = null;
        $leastProfitable = null;

        foreach ($carpetTypesRaw as $ct) {
            $carpetsOfType = Carpet::with('sale')->where('type_id', $ct->carpet_type_id)->where('status', 6)->get();
            $rev = 0;
            $cost = 0;
            foreach($carpetsOfType as $carpet) {
                $cost += $carpet->total_price;
                if ($carpet->sale) {
                    $rev += $carpet->sale->sale_cost_total;
                }
            }

            if ($rev > 0) {
                $profit = $rev - $cost;
                $margin = round(($profit / $rev) * 100, 2);

                $record = [
                    'type' => $ct->carpet_type,
                    'cost' => round($cost, 2),
                    'revenue' => round($rev, 2),
                    'profit' => round($profit, 2),
                    'margin' => $margin
                ];

                $typeProfitability[] = $record;

                if (!$mostProfitable || $profit > $mostProfitable['profit']) {
                    $mostProfitable = $record;
                }
                if (!$leastProfitable || $profit < $leastProfitable['profit']) {
                    $leastProfitable = $record;
                }
            }
        }

        usort($typeProfitability, function($a, $b) {
            return $b['margin'] <=> $a['margin'];
        });

        // 4. Production Cost Trend (Last 12 Months)
        $costTrend = [
            'months' => [],
            'cost' => []
        ];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $costTrend['months'][] = $date->format('M Y');
            $monthlyCost = Carpet::whereYear('updated_at', $date->year)
                                ->whereMonth('updated_at', $date->month)
                                ->sum('total_price') ?? 0;
            $costTrend['cost'][] = round($monthlyCost, 2);
        }

        return [
            'overall_breakdown' => $overallCosts,
            'cost_per_sqm' => $costPerSqm,
            'profitability_table' => $typeProfitability,
            'most_profitable' => $mostProfitable,
            'least_profitable' => $leastProfitable,
            'cost_trend' => $costTrend
        ];
    }
}
