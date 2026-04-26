<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\LedgerTransaction;
use App\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        $query = LedgerTransaction::with('entries.account')->orderBy('date', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('reference', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->journal_type) {
            $query->where('journal_type', $request->journal_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->paginate(30);
        return view('accounting.journals.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::orderBy('account_code')->get();
        return view('accounting.journals.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        try {
            $this->accountingService->postTransaction([
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
                'journal_type' => $request->journal_type ?? 'journal',
                'entries' => $request->entries,
            ]);

            return redirect()->route('accounting.journals.index')->with('success', 'Journal entry posted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaction = LedgerTransaction::with('entries.account')->findOrFail($id);
        return view('accounting.journals.show', compact('transaction'));
    }

    public function print($id)
    {
        $transaction = LedgerTransaction::with('entries.account')->findOrFail($id);
        return view('accounting.journals.print', compact('transaction'));
    }

    public function reverse(Request $request, $id)
    {
        try {
            $this->accountingService->reverseTransaction($id, $request->reason);
            return redirect()->route('accounting.journals.index')->with('success', 'Transaction reversed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
