<?php

namespace App\Services\Dashboard;

use App\Carpet;
use App\CarpetWash;
use App\FinishingWork;
use App\Kachaee;
use Carbon\Carbon;

class ProductionDashboardService
{
    /**
     * Get all Production Analytics
     */
    public function getAnalytics()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // Pipeline exactly like executive dashboard for consistency
        $pipeline = [
            'raw_carpet' => Carpet::whereIn('status', [0, 1])->count(),
            'kachayee' => Carpet::whereIn('status', [2, 12])->count(),
            'washing' => Carpet::whereIn('status', [3, 13])->count(),
            'tayaari' => Carpet::where('status', 4)->count(),
            'ready_for_sale' => Carpet::where('status', 5)->count(),
        ];

        // Team Performance (Kachayee, Washing, Tayaari) completed today or total completed
        // Let's get completed today for the teams
        $kachayeeToday = Carpet::where('status', 12)->whereDate('updated_at', $today)->count();
        $washingToday = Carpet::where('status', 13)->whereDate('updated_at', $today)->count();
        $tayaariToday = Carpet::where('status', 5)->whereDate('updated_at', $today)->count(); // ready for sale means finished tayaari

        $teamPerformance = [
            'kachayee' => $kachayeeToday,
            'washing' => $washingToday,
            'tayaari' => $tayaariToday
        ];

        // Daily Production Output (Last 7 days for the chart)
        $dailyOutput = [];
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D');
            $dailyOutput[] = Carpet::where('status', 5)->whereDate('updated_at', $date->format('Y-m-d'))->count();
        }

        // Efficiency (example gauge)
        $totalWip = Carpet::whereIn('status', [2, 12, 3, 13, 4])->count();
        $completedThisMonth = Carpet::where('status', 5)->whereMonth('updated_at', Carbon::now()->month)->count();
        $efficiency = $totalWip > 0 ? min(100, round(($completedThisMonth / ($totalWip + $completedThisMonth)) * 100)) : 100;

        return [
            'pipeline' => $pipeline,
            'team_performance' => $teamPerformance,
            'daily_output' => [
                'days' => $days,
                'data' => $dailyOutput
            ],
            'efficiency' => $efficiency,
            'wip_total' => $totalWip,
            'delayed_orders' => 0 // Placeholder until delay logic is defined
        ];
    }
}
