<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('is_base_currency', 'desc')
            ->orderBy('code', 'asc')
            ->get();
            
        return view('accounting.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('accounting.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'decimal_precision' => 'required|integer|min:0|max:4',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // BASE CURRENCY IS FIXED TO USD. No new base currencies allowed.
                Currency::create([
                    'code' => strtoupper($request->code),
                    'name' => $request->name,
                    'symbol' => $request->symbol,
                    'exchange_rate' => $request->exchange_rate,
                    'is_base_currency' => false,
                    'is_active' => $request->has('is_active'),
                    'decimal_precision' => $request->decimal_precision,
                ]);
            });

            return redirect()->route('accounting.currencies.index')
                ->with('success', 'Currency created successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error creating currency: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return view('accounting.currencies.edit', compact('currency'));
    }

    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);

        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code,' . $id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'decimal_precision' => 'required|integer|min:0|max:4',
        ]);

        try {
            DB::transaction(function () use ($request, $currency) {
                // BASE CURRENCY IS FIXED TO USD. 
                // Any attempts to change is_base_currency via request are ignored.
                
                $data = [
                    'code' => strtoupper($request->code),
                    'name' => $request->name,
                    'symbol' => $request->symbol,
                    'is_active' => $currency->is_base_currency ? true : $request->has('is_active'),
                    'decimal_precision' => $request->decimal_precision,
                ];

                // If this IS the base currency (USD), force rate to 1.0
                if ($currency->is_base_currency) {
                    $data['exchange_rate'] = 1.00000000;
                } else {
                    $data['exchange_rate'] = $request->exchange_rate;
                }

                $currency->update($data);
            });

            return redirect()->route('accounting.currencies.index')
                ->with('success', 'Currency updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error updating currency: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);

        if ($currency->is_base_currency) {
            return back()->with('error', 'Cannot delete the base currency.');
        }

        // Check if currency is used in ledger entries
        $isUsed = DB::table('ledger_entries')->where('currency_code', $currency->code)->exists();
        if ($isUsed) {
            return back()->with('error', 'Cannot delete currency as it has associated ledger entries. Deactivate it instead.');
        }

        $currency->delete();
        return redirect()->route('accounting.currencies.index')
            ->with('success', 'Currency deleted successfully.');
    }
}
