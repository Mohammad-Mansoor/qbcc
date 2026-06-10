<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Agents;
use App\PurchaseInvoice;
use App\Carpet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetCheckBookController extends Controller
{
    /**
     * Display a listing of the resource (Purchase Bills).
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PurchaseInvoice::with('agent.user')->orderBy('id', 'DESC');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $invoices = $query->get();

        $agents = Agents::where('contract_type', 'carpet seller')->get();
        
        // Statistics Calculations
        $totalInvoicesCount = PurchaseInvoice::count();
        $openInvoicesCount = PurchaseInvoice::where('status', 'open')->count();
        $closedInvoicesCount = PurchaseInvoice::where('status', 'closed')->count();
        $totalCarpetsCount = Carpet::whereNotNull('purchase_invoice_id')->count();
        $totalArea = Carpet::whereNotNull('purchase_invoice_id')->sum('area');
        $totalAmountUsd = Carpet::whereNotNull('purchase_invoice_id')->sum('total_price');

        // Auto-generate next Purchase Bill number: PB-CURRENTYEAR-0001
        $currentYear = date('Y');
        $prefix = "PB-" . $currentYear . "-";
        $lastInvoice = PurchaseInvoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->invoice_number);
            $lastNum = isset($parts[2]) ? intval($parts[2]) : 0;
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }
        $nextBillNumber = $prefix . sprintf('%04d', $nextNum);

        return view('carpet-check-book.index', compact(
            'invoices', 
            'agents',
            'totalInvoicesCount',
            'openInvoicesCount',
            'closedInvoicesCount',
            'totalCarpetsCount',
            'totalArea',
            'totalAmountUsd',
            'nextBillNumber'
        ));
    }

    /**
     * Store a newly created resource (Purchase Bill) in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'agent_id' => 'required|exists:agents,agent_id',
            'date' => 'required|date',
        ]);

        $data['status'] = 'open';

        $invoice = PurchaseInvoice::create($data);

        // Audit Log
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "بل خرید جدید نمبر " . $invoice->invoice_number . " ایجاد شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect()->back()->with('status', 'بل خرید جدید با موفقیت ایجاد شد!');
    }

    /**
     * Display the specified resource (Purchase Bill details).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = PurchaseInvoice::with('agent.user')->findOrFail($id);
        $carpets = Carpet::where('purchase_invoice_id', $id)->with('type', 'quality')->get();

        return view('carpet-check-book.check-number-list', compact('invoice', 'carpets'));
    }

    /**
     * Close the purchase bill.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function closeInvoice($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $invoice->status = 'closed';
        $invoice->save();

        // Audit Log
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = "بل خرید نمبر " . $invoice->invoice_number . " بسته شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();

        return redirect()->back()->with('status', 'بل خرید با موفقیت بسته شد!');
    }
}
