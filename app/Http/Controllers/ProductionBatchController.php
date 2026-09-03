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
            'kachaee' => ['view' => 'view_kachaee_batches', 'create' => 'create_kachaee_batch'],
            'wash' => ['view' => 'view_washing_batches', 'create' => 'create_washing_batch'],
            'finish' => ['view' => 'view_finishing_batches', 'create' => 'create_finishing_batch']
        ];
        
        $viewPerm = $permissionMap[$type]['view'];
        $createPerm = $permissionMap[$type]['create'];

        if (!Auth::user()->can($viewPerm) && !Auth::user()->can($createPerm)) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        $batches = Auth::user()->can($viewPerm) ? ProductionBatch::ofType($type)->orderBy('id', 'desc')->get() : collect();
        
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
            $subquery = \DB::table('finishing_works')
                ->select('finish_number', 'carpetId', \DB::raw('MAX(team_id) as team_id'))
                ->groupBy('finish_number', 'carpetId');

            $stats = \DB::table(\DB::raw("({$subquery->toSql()}) as unique_finishing_works"))
                ->mergeBindings($subquery)
                ->join('carpets', 'unique_finishing_works.carpetId', '=', 'carpets.carpet_id')
                ->leftJoin('finishing_teams', 'unique_finishing_works.team_id', '=', 'finishing_teams.id')
                ->select('unique_finishing_works.finish_number as ref', \DB::raw('count(unique_finishing_works.carpetId) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'), \DB::raw('MAX(finishing_teams.name) as team_name'))
                ->groupBy('unique_finishing_works.finish_number')
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

    /**
     * Update the specified production batch.
     */
    public function update(Request $request, $id)
    {
        $batch = ProductionBatch::findOrFail($id);

        $permissionMap = [
            'kachaee' => 'create_kachaee_batch',
            'wash' => 'create_washing_batch',
            'finish' => 'create_finishing_batch'
        ];

        if (!Auth::user()->can($permissionMap[$batch->type])) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        // PAYMENT CHECK: If active non-reversed payments exist
        if ($batch->has_payments || $batch->paid_amount > 0) {
            if ($request->filled('reference_number') && $request->reference_number != $batch->reference_number) {
                return redirect()->back()->with('error', 'امکان تغییر نمبر مسلسل وجود ندارد زیرا برای این نمبر تادیات ثبت شده است.');
            }
            if ($request->filled('team_id') && $request->team_id != $batch->team_id) {
                return redirect()->back()->with('error', 'امکان تغییر تیم وجود ندارد زیرا برای این نمبر تادیات ثبت شده است.');
            }
        }

        $oldRef = $batch->reference_number;
        $oldTeamId = $batch->team_id;

        $newRef = $request->input('reference_number', $batch->reference_number);
        $newTeamId = $request->input('team_id', $batch->team_id);

        // Update child records if reference_number changed
        if ($newRef !== $oldRef) {
            if ($batch->type === 'kachaee') {
                \DB::table('carpet_repairs')->where('kachaee_number', $oldRef)->update(['kachaee_number' => $newRef]);
            } elseif ($batch->type === 'wash') {
                \DB::table('carpet_washes')->where('wash_number', $oldRef)->update(['wash_number' => $newRef]);
            } elseif ($batch->type === 'finish') {
                \DB::table('finishing_works')->where('finish_number', $oldRef)->update(['finish_number' => $newRef]);
            }
        }

        // Update child records if team_id changed
        if ($newTeamId != $oldTeamId) {
            if ($batch->type === 'kachaee') {
                \DB::table('carpet_repairs')->where('kachaee_number', $newRef)->update(['team_id' => $newTeamId]);
            } elseif ($batch->type === 'wash') {
                \DB::table('carpet_washes')->where('wash_number', $newRef)->update(['team_id' => $newTeamId]);
            } elseif ($batch->type === 'finish') {
                \DB::table('finishing_works')->where('finish_number', $newRef)->update(['team_id' => $newTeamId]);
            }
        }

        $batch->reference_number = $newRef;
        $batch->team_id = $newTeamId;
        $batch->save();

        return redirect()->back()->with('status', 'نمبر با موفقیت بروزرسانی شد: ' . $batch->reference_number);
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

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($type === 'kachaee') {
            $query = \App\CarpetRepair::where('kachaee_number', $ref)
                ->with(['carpet.type', 'carpet.quality', 'carpet.warehouse', 'team']);
            
            if ($startDate && $endDate) {
                $query->whereBetween('repair_date', [$startDate, $endDate]);
            }
            
            $carpets = $query->get();
            
            $paymentsQuery = \App\KachaeePayment::where('kachaee_number', $ref)
                ->where('status', '!=', 2)
                ->orderBy('date', 'desc');
                
            $allocationsQuery = \App\KachaeePaymentAllocation::where('allocatable_type', 'App\ProductionBatch')
                ->where('allocatable_id', $batch->id)
                ->with('payment');

            if ($startDate && $endDate) {
                $paymentsQuery->whereBetween('date', [$startDate, $endDate]);
                $allocationsQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
            
            $payments = $paymentsQuery->get();
            $allocations = $allocationsQuery->get()->map(function($alloc) {
                return (object)[
                    'id' => $alloc->id,
                    'date' => $alloc->created_at->format('Y-m-d'),
                    'description' => 'تخصیص پیش‌پرداخت (Advance Allocation)',
                    'type' => 'گرفت',
                    'original_amount' => $alloc->allocated_amount,
                    'currency_code' => $alloc->payment->currency_code ?? 'USD',
                    'exchange_rate' => $alloc->exchange_rate,
                    'base_amount' => $alloc->base_allocated_amount,
                    'is_allocation' => true
                ];
            });

            $payments = $payments->concat($allocations)->sortByDesc('date')->values();
                
            $totalCost = (float)$carpets->sum('total_price');
            
            $totalSent = (float)$payments->where('type', 'گرفت')->sum('original_amount');
            $totalReceived = (float)$payments->where('type', 'رسید')->sum('original_amount');
            $totalPaid = $totalSent - $totalReceived;
            
            $team = $carpets->first() ? $carpets->first()->team : null;

        } elseif ($type === 'wash') {
            $query = \App\CarpetWash::where('wash_number_sh', $ref)
                ->with(['carpet.type', 'carpet.quality', 'carpet.warehouse', 'washing_team']);
                
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
            
            $carpets = $query->get();
            
            if ($request->get('export') === 'pdf') {
                $carpets = $carpets->filter(function($w) {
                    return $w->total_price > 0 || $w->af_total_price > 0;
                })->values();
            }
            
            $paymentsQuery = \App\WashingPayment::where('wash_number', $ref)
                ->where('status', '!=', 2)
                ->orderBy('date', 'desc');
                
            $allocationsQuery = \App\WashingPaymentAllocation::where('allocatable_type', 'App\ProductionBatch')
                ->where('allocatable_id', $batch->id)
                ->with('payment');

            if ($startDate && $endDate) {
                $paymentsQuery->whereBetween('date', [$startDate, $endDate]);
                $allocationsQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
            
            $payments = $paymentsQuery->get();
            $allocations = $allocationsQuery->get()->map(function($alloc) {
                return (object)[
                    'id' => $alloc->id,
                    'date' => $alloc->created_at->format('Y-m-d'),
                    'description' => 'تخصیص پیش‌پرداخت (Advance Allocation)',
                    'type' => 'گرفت',
                    'original_amount' => $alloc->allocated_amount,
                    'currency_code' => $alloc->payment->currency_code ?? 'USD',
                    'exchange_rate' => $alloc->exchange_rate,
                    'base_amount' => $alloc->base_allocated_amount,
                    'is_allocation' => true
                ];
            });

            $payments = $payments->concat($allocations)->sortByDesc('date')->values();
                
            $totalCost = (float)$carpets->sum('total_price');
            
            $totalSent = (float)$payments->where('type', 'گرفت')->sum('original_amount');
            $totalReceived = (float)$payments->where('type', 'رسید')->sum('original_amount');
            $totalPaid = $totalSent - $totalReceived;
            
            $team = $carpets->first() ? $carpets->first()->washing_team : null;

        } elseif ($type === 'finish') {
            $query = \App\FinishingWork::where('finish_number', $ref)
                ->with(['carpet.type', 'carpet.quality', 'carpet.warehouse', 'team', 'category']);
                
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
            
            $carpets = $query->get();
            
            $paymentsQuery = \App\FinishingTeamPayment::where('finish_number', $ref)
                ->where('status', '!=', 2)
                ->orderBy('date', 'desc');
                
            $allocationsQuery = \App\FinishingPaymentAllocation::where('allocatable_type', 'App\ProductionBatch')
                ->where('allocatable_id', $batch->id)
                ->with('payment');

            if ($startDate && $endDate) {
                $paymentsQuery->whereBetween('date', [$startDate, $endDate]);
                $allocationsQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
            
            $payments = $paymentsQuery->get();
            $allocations = $allocationsQuery->get()->map(function($alloc) {
                return (object)[
                    'id' => $alloc->id,
                    'date' => $alloc->created_at->format('Y-m-d'),
                    'description' => 'تخصیص پیش‌پرداخت (Advance Allocation)',
                    'type' => 'گرفت',
                    'original_amount' => $alloc->allocated_amount,
                    'currency_code' => $alloc->payment->currency_code ?? 'USD',
                    'exchange_rate' => $alloc->exchange_rate,
                    'base_amount' => $alloc->base_allocated_amount,
                    'is_allocation' => true
                ];
            });

            $payments = $payments->concat($allocations)->sortByDesc('date')->values();
                
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
                    return view('batches.pdf_finish', compact('batch', 'groupedCarpets', 'batchCategories', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'logoBase64', 'startDate', 'endDate'));
                } else {
                    return view('batches.pdf', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'topHeaderBase64', 'bottomFooterBase64', 'logoBase64', 'startDate', 'endDate'));
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
                    echo view('batches.excel_finish', compact('batch', 'groupedCarpets', 'batchCategories', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'logoBase64', 'startDate', 'endDate'))->render();
                } else {
                    echo view('batches.excel', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'type', 'topHeaderBase64', 'logoBase64', 'startDate', 'endDate'))->render();
                }
                exit;
            }
        }
        
        if ($type === 'finish') {
            $groupedCarpets = $carpets->groupBy('carpetId');
            $batchCategories = \App\FinishingTeamCategory::all();
            return view('batches.details_finish', compact('batch', 'groupedCarpets', 'batchCategories', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'startDate', 'endDate'));
        }
        
        return view('batches.details', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team', 'startDate', 'endDate'));
    }
}
