<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = ChartOfAccount::orderBy('account_code');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('account_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('account_code', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->account_type) {
            $query->where('account_type', $request->account_type);
        }

        $accounts = $query->get();

        // Enrich with Balances and Protection Status
        $protectedIds = DB::table('mapping_rules')
            ->select('debit_account_id', 'credit_account_id')
            ->get()
            ->flatMap(function($row) {
                return [$row->debit_account_id, $row->credit_account_id];
            })
            ->unique()
            ->toArray();

        // Fetch all currency rates indexed by currency code
        $currencyRates = DB::table('currencies')->pluck('exchange_rate', 'code')->toArray();

        foreach ($accounts as $acc) {
            // 1. Calculate USD-Normalized Balance (Base Balance)
            $acc->base_balance = DB::table('ledger_entries')
                ->where('account_id', $acc->id)
                ->sum(DB::raw('base_debit - base_credit'));

            // 2. Calculate Original Currency Balance using the universal formula
            $rate = floatval($currencyRates[$acc->currency] ?? 1.0);
            if ($rate <= 0) $rate = 1.0; // Prevent division by zero
            $acc->balance = $acc->base_balance / $rate;
            
            // 3. Protection Status
            $acc->is_protected = in_array($acc->id, $protectedIds);

            // 4. Has Entries Check
            $acc->has_entries = DB::table('ledger_entries')
                ->where('account_id', $acc->id)
                ->exists();
        }

        return view('accounting.coa.index', compact('accounts'));
    }

    public function create()
    {
        $currencies = \App\Currency::where('is_active', 1)->get();
        return view('accounting.coa.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts',
            'account_name' => 'required',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:debit,credit',
            'currency' => 'required|exists:currencies,code',
        ]);

        ChartOfAccount::create($request->all());

        return redirect()->route('accounting.coa.index')->with('success', 'Account created successfully.');
    }

    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $currencies = \App\Currency::where('is_active', 1)->get();
        $hasEntries = DB::table('ledger_entries')->where('account_id', $id)->exists();
        return view('accounting.coa.edit', compact('account', 'currencies', 'hasEntries'));
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $hasEntries = DB::table('ledger_entries')->where('account_id', $id)->exists();
        
        $rules = [
            'account_name' => 'required',
        ];

        if (!$hasEntries) {
            $rules['account_code'] = 'required|unique:chart_of_accounts,account_code,' . $id;
            $rules['account_type'] = 'required|in:Asset,Liability,Equity,Revenue,Expense';
            $rules['normal_balance'] = 'required|in:debit,credit';
            $rules['currency'] = 'required|exists:currencies,code';
        } else {
            // Force core settings to remain unchanged if the account already has postings
            $request->merge([
                'account_code' => $account->account_code,
                'account_type' => $account->account_type,
                'normal_balance' => $account->normal_balance,
                'currency' => $account->currency,
            ]);
        }

        $request->validate($rules);
        $account->update($request->all());

        return redirect()->route('accounting.coa.index')->with('success', 'Account updated successfully.');
    }
}
