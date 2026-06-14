<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Agents;
use App\NewDifferentAccount;
use App\Customer;
use App\OfficeEmployee;
use App\StringSeller;
use App\WashingTeam;
use App\Kachaee;
use App\FinishingTeam;

class EntityStatementController extends Controller
{
    protected $models = [
        'agents' => [
            'class' => Agents::class,
            'party_type' => 'App\Agents',
            'title' => 'حساب عاملیت (Agent)',
            'name_field' => 'name',
            'parent_route' => 'agents.index',
            'parent_title' => 'مدیریت عاملیت‌ها',
        ],
        'different-account' => [
            'class' => NewDifferentAccount::class,
            'party_type' => 'App\NewDifferentAccount',
            'title' => 'حساب متفرقه (Different Account)',
            'name_field' => 'name',
            'parent_route' => 'new-different-account.index',
            'parent_title' => 'حساب‌های متفرقه',
        ],
        'customer' => [
            'class' => Customer::class,
            'party_type' => 'App\Customer',
            'title' => 'حساب مشتری (Customer)',
            'name_field' => 'name',
            'parent_route' => 'customers.index',
            'parent_title' => 'مدیریت مشتریان',
        ],
        'employee' => [
            'class' => OfficeEmployee::class,
            'party_type' => 'App\OfficeEmployee',
            'title' => 'حساب کارمند (Employee)',
            'name_field' => 'name',
            'parent_route' => 'office-employee.index',
            'parent_title' => 'کارمندان دفتر',
        ],
        'string-seller' => [
            'class' => StringSeller::class,
            'party_type' => 'App\StringSeller',
            'title' => 'فروشنده مواد خام (String Seller)',
            'name_field' => 'name',
            'parent_route' => 'string-seller.index',
            'parent_title' => 'فروشندگان مواد خام',
        ],
        'washing-team' => [
            'class' => WashingTeam::class,
            'party_type' => 'App\WashingTeam',
            'title' => 'تیم شستشو (Washing Team)',
            'name_field' => 'name',
            'parent_route' => 'washing-team.index',
            'parent_title' => 'تیم‌های شستشو',
        ],
        'kachayee-team' => [
            'class' => Kachaee::class,
            'party_type' => 'App\Kachaee',
            'title' => 'تیم قیچی (Kachaee Team)',
            'name_field' => 'name',
            'parent_route' => 'kachaee-team.index',
            'parent_title' => 'تیم‌های قیچی',
        ],
        'tayaari-team' => [
            'class' => FinishingTeam::class,
            'party_type' => 'App\FinishingTeam',
            'title' => 'تیم پرداخت (Tayaari/Finishing Team)',
            'name_field' => 'name',
            'parent_route' => 'finish-team.index',
            'parent_title' => 'تیم‌های پرداخت قالین',
        ],
    ];

    public function show(Request $request, $entityKey, $id)
    {
        if (!array_key_exists($entityKey, $this->models)) {
            abort(404, 'Entity type not found');
        }

        $config = $this->models[$entityKey];
        $entityClass = $config['class'];
        
        $entity = $entityClass::findOrFail($id);
        
        // Resolve entity name
        $entityName = '';
        if ($entityKey === 'agents') {
            $user = \App\User::find($entity->user_id);
            $entityName = $user ? ($user->name . ' ' . $user->last_name) : 'N/A';
        } else {
            $entityName = $entity->name;
        }

        $from_date = $request->get('from_date') ?: '2000-01-01';
        $to_date = $request->get('to_date') ?: date('Y-m-d');
        $search = $request->get('search');

        // 1. Calculate Opening Balance before $from_date (Posted ledger transactions only)
        $openingBalanceQuery = DB::table('ledger_entries')
            ->where('party_type', $config['party_type'])
            ->where('party_id', $id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted')
            ->where('ledger_transactions.date', '<', $from_date);

        if ($entityKey === 'customer') {
            $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(debit - credit) as balance'))->value('balance') ?? 0;
        } else {
            $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(credit - debit) as balance'))->value('balance') ?? 0;
        }

        // 2. Query Ledger Transactions
        $query = DB::table('ledger_entries')
            ->where('party_type', $config['party_type'])
            ->where('party_id', $id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->where('ledger_transactions.status', 'posted');

        // Apply filters
        if ($request->filled('from_date')) {
            $query->where('ledger_transactions.date', '>=', $from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('ledger_transactions.date', '<=', $to_date);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->where('ledger_transactions.description', 'like', "%{$search}%")
                  ->orWhere('ledger_transactions.reference', 'like', "%{$search}%")
                  ->orWhere('ledger_transactions.journal_id', 'like', "%{$search}%");
            });
        }

        $query->select(
            'ledger_transactions.id as transaction_id',
            'ledger_transactions.journal_id',
            'ledger_transactions.date',
            'ledger_transactions.reference',
            'ledger_transactions.description',
            'ledger_entries.debit',
            'ledger_entries.credit',
            'ledger_entries.currency_code',
            'ledger_entries.exchange_rate',
            'ledger_entries.base_currency_amount'
        )->orderBy('ledger_transactions.date', 'ASC')
         ->orderBy('ledger_transactions.id', 'ASC');

        // Export Excel bypasses pagination
        if ($request->get('export') === 'excel') {
            $entries = $query->get();
            return $this->exportToExcel($entries, $entityName, $openingBalance, $config, $entityKey, $request);
        }

        // Web view paginates (e.g., 30 items)
        $entries = $query->paginate(30);

        return view('accounting.statements.show', compact(
            'entity',
            'entityKey',
            'config',
            'entityName',
            'openingBalance',
            'entries',
            'from_date',
            'to_date',
            'search'
        ));
    }

    protected function exportToExcel($entries, $entityName, $openingBalance, $config, $entityKey, Request $request)
    {
        $filename = str_slug($entityKey) . '_statement_' . date('Y_m_d_His') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo view('accounting.statements.excel', compact(
            'entries',
            'entityName',
            'openingBalance',
            'config',
            'entityKey',
            'request'
        ))->render();
        exit;
    }

    public function reportStatement(Request $request, $entityKey)
    {
        if (!array_key_exists($entityKey, $this->models)) {
            abort(404, 'Entity type not found');
        }

        $config = $this->models[$entityKey];
        $entityClass = $config['class'];

        if ($entityKey === 'agents') {
            $entities = $entityClass::all()->map(function($agent) {
                $user = \App\User::find($agent->user_id);
                $agent->display_name = $user ? ($user->name . ' ' . $user->last_name) : 'Agent ID: ' . $agent->id;
                return $agent;
            })->sortBy('display_name');
        } else {
            $entities = $entityClass::orderBy($config['name_field'])->get();
            foreach ($entities as $e) {
                $e->display_name = $e->{$config['name_field']};
            }
        }

        $selectedId = $request->get('entity_id');
        $startDate = $request->get('start_date') ?: date('Y-m-01');
        $endDate = $request->get('end_date') ?: date('Y-m-d');
        $search = $request->get('search');
        
        $selectedEntity = $selectedId ? $entityClass::find($selectedId) : null;
        $entries = [];
        $openingBalance = 0;

        if ($selectedId && $selectedEntity) {
            $openingBalanceQuery = DB::table('ledger_entries')
                ->where('party_type', $config['party_type'])
                ->where('party_id', $selectedId)
                ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                ->where('ledger_transactions.status', 'posted')
                ->where('ledger_transactions.date', '<', $startDate);

            if ($entityKey === 'customer') {
                $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(debit - credit) as balance'))->value('balance') ?? 0;
            } else {
                $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(credit - debit) as balance'))->value('balance') ?? 0;
            }

            $query = DB::table('ledger_entries')
                ->where('party_type', $config['party_type'])
                ->where('party_id', $selectedId)
                ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                ->where('ledger_transactions.status', 'posted')
                ->whereBetween('ledger_transactions.date', [$startDate, $endDate]);

            if ($request->filled('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('ledger_transactions.description', 'like', "%{$search}%")
                      ->orWhere('ledger_transactions.reference', 'like', "%{$search}%")
                      ->orWhere('ledger_transactions.journal_id', 'like', "%{$search}%");
                });
            }

            $query->select(
                'ledger_transactions.id as transaction_id',
                'ledger_transactions.journal_id',
                'ledger_transactions.date',
                'ledger_transactions.reference',
                'ledger_transactions.description',
                'ledger_entries.debit',
                'ledger_entries.credit',
                'ledger_entries.currency_code',
                'ledger_entries.exchange_rate',
                'ledger_entries.base_currency_amount'
            )->orderBy('ledger_transactions.date', 'ASC')
             ->orderBy('ledger_transactions.id', 'ASC');

            if ($request->get('export') === 'excel') {
                $entries = $query->get();
                $entityName = '';
                if ($entityKey === 'agents') {
                    $user = \App\User::find($selectedEntity->user_id);
                    $entityName = $user ? ($user->name . ' ' . $user->last_name) : 'Agent ID: ' . $selectedEntity->id;
                } else {
                    $entityName = $selectedEntity->display_name ?? $selectedEntity->name;
                }
                return $this->exportToExcel($entries, $entityName, $openingBalance, $config, $entityKey, $request);
            }

            $entries = $query->get();
        }

        return view('accounting.reports.entity_statement', compact(
            'entities',
            'selectedEntity',
            'entityKey',
            'config',
            'openingBalance',
            'entries',
            'startDate',
            'endDate',
            'search'
        ));
    }
}
