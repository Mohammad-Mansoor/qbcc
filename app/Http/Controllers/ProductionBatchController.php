<?php

namespace App\Http\Controllers;

use App\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the batches by type.
     */
    public function index($type)
    {
        if (!in_array($type, ['kachaee', 'wash', 'finish'])) {
            abort(404);
        }
        
        $permissionMap = [
            'kachaee' => 'view_kachaee_batches',
            'wash' => 'view_washing_batches',
            'finish' => 'view_finishing_batches'
        ];
        
        if (!Auth::user()->can($permissionMap[$type])) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        $batches = ProductionBatch::ofType($type)->orderBy('id', 'desc')->get();
        
        // Fetch carpet count and square meters statistics grouped by batch reference number
        $stats = collect();
        if ($type === 'kachaee') {
            $stats = \DB::table('carpet_repairs')
                ->join('carpets', 'carpet_repairs.carpetId', '=', 'carpets.carpet_id')
                ->leftJoin('kachaees', 'carpet_repairs.team_id', '=', 'kachaees.id')
                ->select('carpet_repairs.kachaee_number as ref', \DB::raw('count(*) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'), \DB::raw('MAX(kachaees.name) as team_name'))
                ->groupBy('carpet_repairs.kachaee_number')
                ->get()
                ->keyBy('ref');
        } elseif ($type === 'wash') {
            $stats = \DB::table('carpet_washes')
                ->join('carpets', 'carpet_washes.carpetId', '=', 'carpets.carpet_id')
                ->leftJoin('washing_teams', 'carpet_washes.team_id', '=', 'washing_teams.id')
                ->select('carpet_washes.wash_number as ref', \DB::raw('count(*) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'), \DB::raw('MAX(washing_teams.name) as team_name'))
                ->groupBy('carpet_washes.wash_number')
                ->get()
                ->keyBy('ref');
        } elseif ($type === 'finish') {
            $stats = \DB::table('finishing_works')
                ->join('carpets', 'finishing_works.carpetId', '=', 'carpets.carpet_id')
                ->leftJoin('finishing_teams', 'finishing_works.team_id', '=', 'finishing_teams.id')
                ->select('finishing_works.finish_number as ref', \DB::raw('count(*) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'), \DB::raw('MAX(finishing_teams.name) as team_name'))
                ->groupBy('finishing_works.finish_number')
                ->get()
                ->keyBy('ref');
        }

        // Fetch teams based on type
        $teams = collect();
        if ($type === 'kachaee') {
            $teams = \App\Kachaee::orderBy('name')->get();
        } elseif ($type === 'wash') {
            $teams = \App\WashingTeam::orderBy('name')->get();
        } elseif ($type === 'finish') {
            $teams = \App\FinishingTeam::orderBy('name')->get();
        }
        
        $nextNumber = ProductionBatch::generateNextNumber($type);

        // Define human-readable labels
        $labels = [
            'kachaee' => 'کچایی (Kachaee)',
            'wash' => 'شست (Washing)',
            'finish' => 'تیاری (Tayaari)'
        ];
        
        $title = $labels[$type];
        
        return view('batches.index', compact('batches', 'type', 'title', 'stats', 'teams', 'nextNumber'));
    }

    /**
     * Store a newly generated batch number.
     */
    public function store($type, Request $request)
    {
        if (!in_array($type, ['kachaee', 'wash', 'finish'])) {
            abort(404);
        }

        $permissionMap = [
            'kachaee' => 'create_kachaee_batch',
            'wash' => 'create_washing_batch',
            'finish' => 'create_finishing_batch'
        ];
        
        if (!Auth::user()->can($permissionMap[$type])) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        $nextNumber = ProductionBatch::generateNextNumber($type);

        $request->validate([
            'team_id' => 'required|integer'
        ]);

        $batch = ProductionBatch::create([
            'reference_number' => $nextNumber,
            'team_id' => $request->team_id,
            'type' => $type,
            'status' => 'open',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'batch' => $batch
            ]);
        }

        return redirect()->back()->with('status', 'نمبر جدید با موفقیت ایجاد گردید: ' . $nextNumber);
    }

    /**
     * Toggle the status of a batch (open / closed).
     */
    public function toggleStatus($id)
    {
        $batch = ProductionBatch::findOrFail($id);
        
        $permissionMap = [
            'kachaee' => 'manage_kachaee_batch_status',
            'wash' => 'manage_washing_batch_status',
            'finish' => 'manage_finishing_batch_status'
        ];
        
        if (!Auth::user()->can($permissionMap[$batch->type])) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        $batch->status = $batch->status === 'open' ? 'closed' : 'open';
        $batch->save();

        $message = $batch->status === 'open' ? 'باز گردید' : 'بسته گردید';

        return redirect()->back()->with('status', "وضعیت نمبر {$batch->reference_number} با موفقیت تغییر کرد و {$message}");
    }

    public function getOpenBatches(Request $request, $type)
    {
        if (!in_array($type, ['kachaee', 'wash', 'finish'])) {
            return response()->json(['success' => false, 'message' => 'Invalid type.'], 400);
        }

        $team_id = $request->query('team_id');

        $query = ProductionBatch::where('type', $type)->where('status', 'open');
        
        if ($team_id) {
            $query->where('team_id', $team_id);
        }

        $batches = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'batches' => $batches
        ]);
    }

    public function details(Request $request, $id)
    {
        $batch = ProductionBatch::where('id', $id)->orWhere('reference_number', $id)->firstOrFail();
        
        $type = $batch->type;
        $ref = $batch->reference_number;
        
        $viewPermissionMap = [
            'kachaee' => 'view_kachaee_batches',
            'wash' => 'view_washing_batches',
            'finish' => 'view_finishing_batches'
        ];
        
        if (!Auth::user()->can($viewPermissionMap[$type])) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }
        
        if ($request->get('export') === 'pdf') {
            $pdfPermissionMap = [
                'kachaee' => 'export_kachaee_batches_pdf',
                'wash' => 'export_washing_batches_pdf',
                'finish' => 'export_finishing_batches_pdf'
            ];
            if (!Auth::user()->can($pdfPermissionMap[$type])) {
                abort(403, 'شما اجازه چاپ را ندارید.');
            }
        }
        
        if ($request->get('export') === 'excel') {
            $excelPermissionMap = [
                'kachaee' => 'export_kachaee_batches_excel',
                'wash' => 'export_washing_batches_excel',
                'finish' => 'export_finishing_batches_excel'
            ];
            if (!Auth::user()->can($excelPermissionMap[$type])) {
                abort(403, 'شما اجازه دریافت اکسل را ندارید.');
            }
        }
        
        $carpets = [];
        $payments = [];
        $totalCost = 0.0;
        $totalPaid = 0.0;
        $remaining = 0.0;
        $team = null;

        if ($type === 'kachaee') {
            $carpets = \App\CarpetRepair::where('kachaee_number', $ref)
                ->with(['carpet.type', 'carpet.quality', 'team'])
                ->get();
            
            $payments = \App\KachaeePayment::where('kachaee_number', $ref)
                ->orderBy('date', 'desc')
                ->get();
                
            $totalCost = (float)$carpets->sum('total_price');
            
            $totalSent = (float)$payments->where('type', 'گرفت')->sum('original_amount');
            $totalReceived = (float)$payments->where('type', 'رسید')->sum('original_amount');
            $totalPaid = $totalSent - $totalReceived;
            
            $team = $carpets->first() ? $carpets->first()->team : null;

        } elseif ($type === 'wash') {
            $carpets = \App\CarpetWash::where('wash_number', $ref)
                ->with(['carpet.type', 'carpet.quality', 'washing_team'])
                ->get();
            
            $payments = \App\WashingPayment::where('wash_number', $ref)
                ->orderBy('date', 'desc')
                ->get();
                
            $totalCost = (float)$carpets->sum(function($w) {
                return $w->total_price ?: $w->af_total_price;
            });
            
            $totalSent = (float)$payments->where('type', 'گرفت')->sum('original_amount');
            $totalReceived = (float)$payments->where('type', 'رسید')->sum('original_amount');
            $totalPaid = $totalSent - $totalReceived;
            
            $team = $carpets->first() ? $carpets->first()->washing_team : null;

        } elseif ($type === 'finish') {
            $carpets = \App\FinishingWork::where('finish_number', $ref)
                ->with(['carpet.type', 'carpet.quality', 'team', 'category'])
                ->get();
            
            $payments = \App\FinishingTeamPayment::where('finish_number', $ref)
                ->orderBy('date', 'desc')
                ->get();
                
            $totalCost = (float)$carpets->sum('price');
            
            $totalSent = (float)$payments->where('type', 'گرفت')->sum('original_amount');
            $totalReceived = (float)$payments->where('type', 'رسید')->sum('original_amount');
            $totalPaid = $totalSent - $totalReceived;
            
            $team = $carpets->first() ? $carpets->first()->team : null;
        }
        
        $remaining = max(0.0, $totalCost - $totalPaid);
        
        if ($request->get('export') === 'pdf' || $request->get('export') === 'excel') {
            $topHeaderPath = public_path('images/header.png');
            $bottomFooterPath = public_path('images/footer.png');
            $logoPath = public_path('images/logo.png');
            
            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }
            
            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }

            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            if ($request->get('export') === 'pdf') {
                if ($type === 'finish') {
                    $groupedCarpets = $carpets->groupBy('carpetId');
                    $batchCategories = \App\FinishingTeamCategory::all();
                    return view('batches.pdf_finish', compact('batch', 'groupedCarpets', 'batchCategories', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'logoBase64'));
                } else {
                    return view('batches.pdf', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'topHeaderBase64', 'bottomFooterBase64', 'logoBase64'));
                }
            }

            if ($request->get('export') === 'excel') {
                $filename = 'batch_report_' . $batch->reference_number . '_' . date('Y_m_d_His') . '.xls';
                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');

                if ($type === 'finish') {
                    $groupedCarpets = $carpets->groupBy('carpetId');
                    $batchCategories = \App\FinishingTeamCategory::all();
                    echo view('batches.excel_finish', compact('batch', 'groupedCarpets', 'batchCategories', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'logoBase64'))->render();
                } else {
                    echo view('batches.excel', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'topHeaderBase64', 'logoBase64'))->render();
                }
                exit;
            }
        }
        
        if ($type === 'finish') {
            $groupedCarpets = $carpets->groupBy('carpetId');
            $batchCategories = \App\FinishingTeamCategory::all();
            return view('batches.details_finish', compact('batch', 'groupedCarpets', 'batchCategories', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team'));
        }
        
        return view('batches.details', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team'));
    }
}
