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
                $isBase = $request->has('is_base_currency');

                if ($isBase) {
                    // Reset existing base currency
                    Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
                    $request->merge(['exchange_rate' => 1]); // Force rate to 1 for base
                }

                Currency::create([
                    'code' => strtoupper($request->code),
                    'name' => $request->name,
                    'symbol' => $request->symbol,
                    'exchange_rate' => $isBase ? 1.00000000 : $request->exchange_rate,
                    'is_base_currency' => $isBase,
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
                $isBase = $request->has('is_base_currency');
                $oldBaseRate = $currency->exchange_rate;

                if ($isBase && !$currency->is_base_currency) {
                    // CHANGING BASE CURRENCY DETECTED
                    if ($oldBaseRate <= 0) {
                        throw new Exception("Cannot set a currency with zero rate as base.");
                    }

                    // 1. Reset all other currencies to NOT be base
                    Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);

                    // 2. Recalculate ALL currency rates relative to the NEW base
                    // Formula: NewRate = OldRate / OldRateOfNewBase
                    $allCurrencies = Currency::all();
                    foreach ($allCurrencies as $cur) {
                        if ($cur->id == $currency->id) {
                            $newRate = 1.00000000; // The new base is always 1.0
                        } else {
                            // Use high precision division for the new rate
                            $newRate = bcdiv($cur->exchange_rate, $oldBaseRate, 12);
                        }
                        
                        $cur->update([
                            'exchange_rate' => $newRate,
                            'is_base_currency' => ($cur->id == $currency->id)
                        ]);
                    }
                }

                // Normal update for non-base change or other fields
                $currency->update([
                    'code' => strtoupper($request->code),
                    'name' => $request->name,
                    'symbol' => $request->symbol,
                    'is_active' => $isBase ? true : $request->has('is_active'),
                    'decimal_precision' => $request->decimal_precision,
                ]);
                
                // If not changing base, just update its rate normally
                if (!$isBase) {
                    $currency->update(['exchange_rate' => $request->exchange_rate]);
                }
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
