<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\ChartOfAccount;
use Illuminate\Http\Request;

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
        return view('accounting.coa.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounting.coa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts',
            'account_name' => 'required',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:debit,credit',
        ]);

        ChartOfAccount::create($request->all());

        return redirect()->route('accounting.coa.index')->with('success', 'Account created successfully.');
    }

    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        return view('accounting.coa.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts,account_code,' . $id,
            'account_name' => 'required',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:debit,credit',
        ]);

        $account->update($request->all());

        return redirect()->route('accounting.coa.index')->with('success', 'Account updated successfully.');
    }
}
