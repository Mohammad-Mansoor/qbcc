<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Carpet;
use App\CarpetType;
use App\Quality;
use Carbon\Carbon;
use DB;

class PurchasedCarpetReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_purchased_carpets_report')->only('index');
        $this->middleware('permission:export_purchased_carpets_excel')->only('exportExcel');
        $this->middleware('permission:export_purchased_carpets_pdf')->only('exportPdf');
    }
    private $statuses = [
        '' => 'همه حالت‌ها (All)',
        0 => 'در نزد نماینده (With agent)',
        1 => 'در گدام مرکزی (Central Warehouse)',
        2 => 'کچایی نشده (Not Kachayee)',
        12 => 'کچایی شده (Kachayee Done)',
        3 => 'شست نشده (Not Washed)',
        13 => 'شست شده (Washed)',
        4 => 'بخش تیاری (Finishing)',
        5 => 'آماده فروش (Ready for Sale)',
        6 => 'فروخته شده (Sold)',
    ];

    public function index(Request $request)
    {
        return $this->generateReport($request, 'view');
    }

    public function exportExcel(Request $request)
    {
        return $this->generateReport($request, 'excel');
    }

    public function exportPdf(Request $request)
    {
        return $this->generateReport($request, 'pdf');
    }

    private function generateReport(Request $request, $type)
    {
        $query = Carpet::with(['agent.user', 'type', 'quality', 'warehouse', 'sale', 'repair', 'carpet_wash', 'finishing_works'])
            ->whereHas('agent', function($q) {
                $q->where('contract_type', 'carpet seller');
            });

        // Date Filter
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // System ID Filter
        if ($request->filled('from_id') && $request->filled('to_id')) {
            $query->whereBetween('carpet_no', [$request->from_id, $request->to_id]);
        } elseif ($request->filled('from_id')) {
            $query->where('carpet_no', 'like', '%' . $request->from_id . '%');
        }

        // Map Number Filter
        if ($request->filled('map_number')) {
            $query->where('map_number', 'like', '%' . $request->map_number . '%');
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type Filter
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        // Quality Filter
        if ($request->filled('quality_id')) {
            $query->where('quality_id', $request->quality_id);
        }

        $types = CarpetType::all();
        $qualities = Quality::all();

        // Generate KPIs based on filtered data (without pagination)
        $cloneQuery = clone $query;
        $kpiData = $cloneQuery->select(
            DB::raw('COUNT(*) as total_qty'),
            DB::raw('SUM(COALESCE(NULLIF(buying_area, 0), area)) as total_area'),
            DB::raw('SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as ready_qty'),
            DB::raw('SUM(CASE WHEN status = 6 THEN 1 ELSE 0 END) as sold_qty'),
            DB::raw('SUM(CASE WHEN status IN (2,12,3,13,4) THEN 1 ELSE 0 END) as wip_qty'),
            DB::raw('SUM(CASE WHEN status IN (0,1) THEN 1 ELSE 0 END) as raw_qty')
        )->first();

        $kpis = [
            'total_qty' => $kpiData->total_qty ?? 0,
            'total_area' => $kpiData->total_area ?? 0,
            'ready_qty' => $kpiData->ready_qty ?? 0,
            'sold_qty' => $kpiData->sold_qty ?? 0,
            'wip_qty' => $kpiData->wip_qty ?? 0,
            'raw_qty' => $kpiData->raw_qty ?? 0,
        ];

        // Execute query
        if ($type === 'view') {
            $carpets = $query->orderBy('carpet_id', 'desc')->paginate(30);
            $carpets->appends($request->all());
            
            return view('carpets.reports.purchased_report', [
                'carpets' => $carpets,
                'kpis' => $kpis,
                'statuses' => $this->statuses,
                'types' => $types,
                'qualities' => $qualities,
                'request' => $request
            ]);
        } else {
            $carpets = $query->orderBy('carpet_id', 'desc')->get();
            $issueDate = Carbon::now()->format('Y-m-d H:i');

            if ($type === 'excel') {
                return view('carpets.reports.purchased_report_excel', [
                    'carpets' => $carpets,
                    'statuses' => $this->statuses,
                    'types' => $types,
                    'qualities' => $qualities,
                    'issueDate' => $issueDate,
                    'request' => $request
                ]);
            } else {
                $logoPath = public_path('images/logo.png');
                
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                }

                return view('carpets.reports.purchased_report_pdf', [
                    'carpets' => $carpets,
                    'statuses' => $this->statuses,
                    'types' => $types,
                    'qualities' => $qualities,
                    'issueDate' => $issueDate,
                    'request' => $request,
                    'logoBase64' => $logoBase64
                ]);
            }
        }
    }
}
