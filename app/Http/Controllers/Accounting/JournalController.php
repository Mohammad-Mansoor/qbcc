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
        $query = LedgerTransaction::with('entries.account')->orderBy('date', 'desc')->orderBy('id', 'desc');

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

        if ($request->account_id) {
            $query->whereHas('entries', function($q) use ($request) {
                $q->where('account_id', $request->account_id);
            });
        }

        if ($request->min_amount) {
            $query->whereHas('entries', function($q) use ($request) {
                $q->where('base_debit', '>=', $request->min_amount);
            });
        }

        if ($request->max_amount) {
            $query->whereHas('entries', function($q) use ($request) {
                $q->where('base_debit', '<=', $request->max_amount);
            });
        }

        if ($request->mixed_currency) {
            $query->whereHas('entries', function($q) {
                $q->where('currency_code', '!=', 'USD');
            });
        }

        if ($request->party_type) {
            $query->whereHas('entries', function($q) use ($request) {
                $q->where('party_type', $request->party_type);
                if ($request->party_id) {
                    $q->where('party_id', $request->party_id);
                }
            });
        }

        $transactions = $query->paginate(30);
        $accounts = ChartOfAccount::orderBy('account_code')->get();
        return view('accounting.journals.index', compact('transactions', 'accounts'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::orderBy('account_code')->get();
        
        $year = date('Y');
        $prefix = "JV-{$year}-";
        $lastTransactions = LedgerTransaction::where('journal_id', 'LIKE', "{$prefix}%")->get();
        
        $maxNum = 0;
        foreach ($lastTransactions as $t) {
            $numPart = str_replace($prefix, '', $t->journal_id);
            if (is_numeric($numPart)) {
                $maxNum = max($maxNum, intval($numPart));
            }
        }
        
        $nextNum = $maxNum + 1;
        $nextJournalId = $prefix . sprintf('%05d', $nextNum);

        return view('accounting.journals.create', compact('accounts', 'nextJournalId'));
    }

    public function getParties(Request $request)
    {
        $type = $request->input('type');
        $search = $request->input('q');

        $results = [];

        switch ($type) {
            case 'App\Customer':
                $query = \App\Customer::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;

            case 'App\Agents':
                $query = \App\Agents::with('user');
                if ($search) {
                    $query->whereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return [
                        'id' => $item->agent_id,
                        'text' => $item->user ? ($item->user->name . ' (' . $item->user->email . ')') : 'Unnamed Agent'
                    ];
                });
                break;

            case 'App\OfficeEmployee':
                $query = \App\OfficeEmployee::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;

            case 'App\StringSeller':
                $query = \App\StringSeller::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;

            case 'App\WashingTeam':
                $query = \App\WashingTeam::query();
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name . ($item->last_name ? ' ' . $item->last_name : '')];
                });
                break;

            case 'App\FinishingTeam':
                $query = \App\FinishingTeam::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;

            case 'App\Kachaee':
                $query = \App\Kachaee::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;

            case 'App\NewDifferentAccount':
                $query = \App\NewDifferentAccount::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;

            case 'App\DifferentAccount':
                $query = \App\DifferentAccount::query();
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $results = $query->limit(50)->get()->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
                break;
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'journal_id' => 'nullable|string|unique:ledger_transactions,journal_id',
            'date' => 'required|date',
            'description' => 'required',
            'journal_type' => 'required|string',
            'party_type' => 'nullable|string',
            'party_id' => 'nullable|integer',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        try {
            $partyType = $request->input('party_type') ?: null;
            $partyId = $request->input('party_id') ?: null;

            $entries = [];
            foreach ($request->entries as $entry) {
                $entries[] = [
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                    'currency_code' => $entry['currency_code'] ?? 'USD',
                    'party_type' => $partyType,
                    'party_id' => $partyId,
                ];
            }

            $journalId = $request->journal_id;
            if (empty($journalId)) {
                $year = date('Y');
                $prefix = "JV-{$year}-";
                $lastTransactions = LedgerTransaction::where('journal_id', 'LIKE', "{$prefix}%")->get();
                $maxNum = 0;
                foreach ($lastTransactions as $t) {
                    $numPart = str_replace($prefix, '', $t->journal_id);
                    if (is_numeric($numPart)) {
                        $maxNum = max($maxNum, intval($numPart));
                    }
                }
                $nextNum = $maxNum + 1;
                $journalId = $prefix . sprintf('%05d', $nextNum);
            }

            $this->accountingService->postTransaction([
                'journal_id' => $journalId,
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
                'journal_type' => $request->journal_type ?? 'journal',
                'entries' => $entries,
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
