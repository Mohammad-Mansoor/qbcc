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

        $batches = ProductionBatch::ofType($type)->orderBy('id', 'desc')->get();
        
        // Fetch carpet count and square meters statistics grouped by batch reference number
        $stats = collect();
        if ($type === 'kachaee') {
            $stats = \DB::table('carpet_repairs')
                ->join('carpets', 'carpet_repairs.carpetId', '=', 'carpets.carpet_id')
                ->select('carpet_repairs.kachaee_number as ref', \DB::raw('count(*) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'))
                ->groupBy('carpet_repairs.kachaee_number')
                ->get()
                ->keyBy('ref');
        } elseif ($type === 'wash') {
            $stats = \DB::table('carpet_washes')
                ->join('carpets', 'carpet_washes.carpetId', '=', 'carpets.carpet_id')
                ->select('carpet_washes.wash_number as ref', \DB::raw('count(*) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'))
                ->groupBy('carpet_washes.wash_number')
                ->get()
                ->keyBy('ref');
        } elseif ($type === 'finish') {
            $stats = \DB::table('finishing_works')
                ->join('carpets', 'finishing_works.carpetId', '=', 'carpets.carpet_id')
                ->select('finishing_works.finish_number as ref', \DB::raw('count(*) as total_carpets'), \DB::raw('sum(carpets.area) as total_area'))
                ->groupBy('finishing_works.finish_number')
                ->get()
                ->keyBy('ref');
        }

        // Define human-readable labels
        $labels = [
            'kachaee' => 'کچایی (Kachaee)',
            'wash' => 'شست (Washing)',
            'finish' => 'تیاری (Tayaari)'
        ];
        
        $title = $labels[$type];
        
        return view('batches.index', compact('batches', 'type', 'title', 'stats'));
    }

    /**
     * Store a newly generated batch number.
     */
    public function store($type, Request $request)
    {
        if (!in_array($type, ['kachaee', 'wash', 'finish'])) {
            abort(404);
        }

        $nextNumber = ProductionBatch::generateNextNumber($type);

        $batch = ProductionBatch::create([
            'reference_number' => $nextNumber,
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
        $batch->status = $batch->status === 'open' ? 'closed' : 'open';
        $batch->save();

        $message = $batch->status === 'open' ? 'باز گردید' : 'بسته گردید';

        return redirect()->back()->with('status', "وضعیت نمبر {$batch->reference_number} با موفقیت تغییر کرد و {$message}");
    }

    public function details($id)
    {
        $batch = ProductionBatch::where('id', $id)->orWhere('reference_number', $id)->firstOrFail();
        
        $type = $batch->type;
        $ref = $batch->reference_number;
        
        $carpets = [];
        $payments = [];
        $totalCost = 0.0;
        $totalPaid = 0.0;
        $remaining = 0.0;
        $team = null;

        if ($type === 'kachaee') {
            $carpets = \App\CarpetRepair::where('kachaee_number', $ref)
                ->with(['carpet', 'team'])
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
                ->with(['carpet', 'washing_team'])
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
                ->with(['carpet', 'team', 'category'])
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
        
        return view('batches.details', compact('batch', 'carpets', 'payments', 'totalCost', 'totalPaid', 'remaining', 'team'));
    }
}
