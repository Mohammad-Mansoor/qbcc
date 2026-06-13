<?php

namespace App\Http\Controllers;

use App\RawMaterialPurchaseBill;
use App\StringSeller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RawMaterialPurchaseBillController extends Controller
{
    /**
     * Generate next serial bill number: RM-PB-YYYY-NNNN
     */
    private function generateBillNumber(): string
    {
        $year = Carbon::now()->year;
        $prefix = "RM-PB-{$year}-";
        $last = RawMaterialPurchaseBill::where('bill_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->bill_number);
            $seq = (int) end($parts) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bills   = RawMaterialPurchaseBill::with('seller')->latest()->paginate(30);
        $sellers = StringSeller::all();
        return view('raw-material-purchase-bills.index', compact('bills', 'sellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sellers    = StringSeller::all();
        $nextNumber = $this->generateBillNumber();
        return view('raw-material-purchase-bills.create', compact('sellers', 'nextNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'seller_id' => 'required|exists:string_sellers,id',
            'date'      => 'required|date',
        ]);

        $data['bill_number'] = $this->generateBillNumber();
        $data['status']      = 'open';

        RawMaterialPurchaseBill::create($data);

        return redirect()->route('raw-material-purchase-bills.index')
            ->with('status', 'بل خرید مواد خام با شماره ' . $data['bill_number'] . ' ثبت شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bill = RawMaterialPurchaseBill::with(['seller', 'purchases.materialType', 'purchases.warehouse', 'allocations.seller_payment'])->findOrFail($id);
        $purchases = $bill->purchases;
        return view('raw-material-purchase-bills.show', compact('bill', 'purchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bill    = RawMaterialPurchaseBill::findOrFail($id);
        $sellers = StringSeller::all();
        return view('raw-material-purchase-bills.edit', compact('bill', 'sellers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bill = RawMaterialPurchaseBill::findOrFail($id);

        $data = $request->validate([
            'seller_id' => 'required|exists:string_sellers,id',
            'date'      => 'required|date',
            'status'    => 'required|in:open,closed',
        ]);

        $bill->update($data);

        return redirect()->route('raw-material-purchase-bills.index')
            ->with('status', 'بل خرید ' . $bill->bill_number . ' بروزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $bill = RawMaterialPurchaseBill::findOrFail($id);

            // Detach linked purchases
            $bill->purchases()->update(['raw_material_purchase_bill_id' => null]);
            $bill->delete();

            return response()->json(['status' => 'success']);
        });
    }

    /**
     * AJAX: get open bills for a given seller
     */
    public function getBySeller($seller_id)
    {
        $bills = RawMaterialPurchaseBill::where('seller_id', $seller_id)
            ->orderBy('date', 'desc')
            ->get(['id', 'bill_number', 'date', 'status']);

        return response()->json($bills);
    }

    /**
     * Close the purchase bill.
     */
    public function closeBill($id)
    {
        $bill = RawMaterialPurchaseBill::findOrFail($id);
        $bill->status = 'closed';
        $bill->save();

        return redirect()->back()->with('status', 'بل خرید ' . $bill->bill_number . ' با موفقیت بسته شد!');
    }
}
