<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\MappingRule;
use App\ChartOfAccount;
use Illuminate\Http\Request;

class MappingRuleController extends Controller
{
    public function index()
    {
        // Auto-heal: If the tables are empty (like on a fresh production server), 
        // seed the default skeleton structure automatically so the UI isn't blank.
        if (\App\ChartOfAccount::count() == 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'ChartOfAccountsSeeder']);
        }

        if (\App\MappingRule::count() == 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'MappingRulesSeeder']);
        }

        $rules = MappingRule::with(['debitAccount', 'creditAccount'])->get();
        $accounts = ChartOfAccount::orderBy('account_code')->get();

        return view('accounting.mappings.index', compact('rules', 'accounts'));
    }

    public function update(Request $request)
    {
        foreach ($request->rules as $id => $data) {
            MappingRule::where('id', $id)->update([
                'debit_account_id' => $data['debit_account_id'],
                'credit_account_id' => $data['credit_account_id'],
                'description_template' => $data['description_template'],
            ]);
        }

        return redirect()->back()->with('status', 'تنظیمات با موفقیت بروزرسانی شد (Mapping updated successfully)');
    }
}
