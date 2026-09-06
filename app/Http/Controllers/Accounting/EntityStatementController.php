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
            'class' => \App\DifferentAccount::class,
            'party_type' => 'App\DifferentAccount',
            'title' => 'حساب متفرقه (Different Account)',
            'name_field' => 'name',
            'parent_route' => 'different-account.index',
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
            'title' => 'فروشنده مواد خام (Raw Material Seller)',
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
            'title' => 'تیم کچایی (Repair Team)',
            'name_field' => 'name',
            'parent_route' => 'kachaee-team.index',
            'parent_title' => 'تیم‌های کچایی',
        ],
        'tayaari-team' => [
            'class' => FinishingTeam::class,
            'party_type' => 'App\FinishingTeam',
            'title' => 'تیم تیاری (Finishing Team)',
            'name_field' => 'name',
            'parent_route' => 'finish-team.index',
            'parent_title' => 'تیم‌های پرداخت قالین',
        ],
    ];

    protected function authorizeEntity($entityKey)
    {
        $permissions = [
            'customer' => 'view_customer_statement',
            'agents' => 'view_agent_statement',
            'different-account' => 'view_different_account_statement',
            'employee' => 'view_employee_statement',
            'string-seller' => 'view_seller_statement',
            'washing-team' => 'view_washing_team_statement',
            'kachayee-team' => 'view_kachaee_team_statement',
            'tayaari-team' => 'view_finishing_team_statement',
        ];

        if (isset($permissions[$entityKey])) {
            if (!auth()->user()->can($permissions[$entityKey])) {
                abort(403, 'Unauthorized action.');
            }
        }
    }

    public function show(Request $request, $entityKey, $id)
    {
        if (!array_key_exists($entityKey, $this->models)) {
            abort(404, 'Entity type not found');
        }

        $this->authorizeEntity($entityKey);

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

        // 1. Calculate Opening Balance before $from_date (Posted/Reversed ledger transactions)
        $openingBalanceQuery = DB::table('ledger_entries')
            ->where('party_type', $config['party_type'])
            ->where('party_id', $id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereIn('ledger_transactions.status', ['posted', 'reversed'])
            ->where('ledger_transactions.date', '<', $from_date);

        if ($entityKey === 'customer') {
            $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(base_debit - base_credit) as balance'))->value('balance') ?? 0;
        } else {
            $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(base_credit - base_debit) as balance'))->value('balance') ?? 0;
        }

        // 2. Query Ledger Transactions
        $query = DB::table('ledger_entries')
            ->where('party_type', $config['party_type'])
            ->where('party_id', $id)
            ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
            ->whereIn('ledger_transactions.status', ['posted', 'reversed']);

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
            'ledger_entries.base_debit',
            'ledger_entries.base_credit',
            'ledger_entries.currency_code',
            'ledger_entries.exchange_rate',
            'ledger_entries.base_currency_amount'
        )->orderBy('ledger_transactions.date', 'ASC')
         ->orderBy('ledger_transactions.id', 'ASC');

        // Export Excel bypasses pagination
        if ($request->get('export') === 'excel') {
            $excelPermissions = [
                'customer' => 'export_customer_statement_excel',
                'agents' => 'export_agent_statement_excel',
                'different-account' => 'export_different_account_statement_excel',
                'kachayee-team' => 'export_kachaee_statement_excel',
                'washing-team' => 'export_washing_statement_excel',
                'tayaari-team' => 'export_finishing_statement_excel',
            ];
            if (isset($excelPermissions[$entityKey])) {
                abort_if(!auth()->user()->can($excelPermissions[$entityKey]), 403, 'Unauthorized.');
            }
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
        $filename = \Illuminate\Support\Str::slug($entityKey) . '_statement_' . date('Y_m_d_His') . '.xls';
        
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

        $this->authorizeEntity($entityKey);

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
        $isSummary = $request->get('type') === 'summary';

        if ($selectedId && $selectedEntity) {
            if ($entityKey === 'agents') {
                $user = \App\User::find($selectedEntity->user_id);
                $selectedEntity->display_name = $user ? ($user->name . ' ' . $user->last_name) : 'Agent ID: ' . $selectedEntity->id;
            } else {
                $selectedEntity->display_name = $selectedEntity->{$config['name_field']};
            }

            if ($request->get('export') === 'pdf') {
                $reversedTxIds = DB::table('ledger_transactions as lt')
                    ->join('ledger_entries as le', 'le.transaction_id', '=', 'lt.id')
                    ->where('le.party_type', $config['party_type'])
                    ->where('le.party_id', $selectedId)
                    ->whereNotNull('lt.reversed_transaction_id')
                    ->pluck('lt.reversed_transaction_id')
                    ->toArray();

                $openingBalanceQuery = DB::table('ledger_entries as le')
                    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
                    ->where('le.party_type', $config['party_type'])
                    ->where('le.party_id', $selectedId)
                    ->where('lt.date', '<', $startDate)
                    ->where('lt.status', 'posted')
                    ->whereNull('lt.reversed_transaction_id')
                    ->whereNotIn('lt.id', $reversedTxIds);
            } else {
                $openingBalanceQuery = DB::table('ledger_entries')
                    ->where('party_type', $config['party_type'])
                    ->where('party_id', $selectedId)
                    ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                    ->whereIn('ledger_transactions.status', ['posted', 'reversed'])
                    ->where('ledger_transactions.date', '<', $startDate);
            }

            if ($entityKey === 'customer') {
                $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(base_debit - base_credit) as balance'))->value('balance') ?? 0;
            } else {
                $openingBalance = $openingBalanceQuery->select(DB::raw('SUM(base_credit - base_debit) as balance'))->value('balance') ?? 0;
            }

            $query = DB::table('ledger_entries')
                ->where('party_type', $config['party_type'])
                ->where('party_id', $selectedId)
                ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                ->whereIn('ledger_transactions.status', ['posted', 'reversed'])
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
                'ledger_transactions.source_type',
                'ledger_transactions.source_id',
                'ledger_entries.debit',
                'ledger_entries.credit',
                'ledger_entries.base_debit',
                'ledger_entries.base_credit',
                'ledger_entries.currency_code',
                'ledger_entries.exchange_rate',
                'ledger_entries.base_currency_amount',
                'ledger_entries.original_amount'
            )->orderBy('ledger_transactions.date', 'ASC')
             ->orderBy('ledger_transactions.id', 'ASC');

            $logoPath = public_path(config('company.logo_path', 'images/logos/qasimi_logo.png'));
            $topHeaderPath = public_path(config('company.header_path', 'images/logos/qasimi_header.png'));
            $bottomFooterPath = public_path(config('company.footer_path', 'images/logos/qasimi_footer.png'));

            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            $topHeaderBase64 = '';
            if (file_exists($topHeaderPath)) {
                $topHeaderBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($topHeaderPath));
            }

            $bottomFooterBase64 = '';
            if (file_exists($bottomFooterPath)) {
                $bottomFooterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bottomFooterPath));
            }

            $entries = $query->get();

            if ($request->get('export') === 'pdf') {
                $pdfFilter = new \App\Services\Accounting\PdfStatementFilter();
                $entries = $pdfFilter->collapse(collect($entries));
            }

            if ($isSummary) {
                // Batch pre-fetch relationships to avoid N+1 queries
                $allocationIds = [];
                $paymentIds = [];
                $purchaseIds = [];
                foreach ($entries as $item) {
                    $srcType = strtolower($item->source_type);
                    if ($srcType === 'app\sellerpaymentallocation' || $srcType === 'vendor_advance_settlement') {
                        $allocationIds[] = $item->source_id;
                    } elseif ($srcType === 'app\sellerpayment' || $srcType === 'seller_payment') {
                        $paymentIds[] = $item->source_id;
                    } elseif ($srcType === 'app\purchasematerial' || $srcType === 'material_purchase') {
                        $purchaseIds[] = $item->source_id;
                    }
                }

                $allocations = \App\SellerPaymentAllocation::whereIn('id', array_unique($allocationIds))->with('purchase_bill')->get()->keyBy('id');
                $payments = \App\SellerPayment::whereIn('id', array_unique($paymentIds))->with('allocations.purchase_bill')->get()->keyBy('id');
                $purchases = \App\PurchaseMaterial::whereIn('id', array_unique($purchaseIds))->with('purchaseBill')->get()->keyBy('id');

                $entries = collect($entries)->groupBy(function($item) use ($allocations, $payments, $purchases) {
                    $groupRef = trim($item->reference);
                    $srcType = strtolower($item->source_type);
                    if ($srcType === 'app\sellerpaymentallocation' || $srcType === 'vendor_advance_settlement') {
                        $alloc = $allocations->get($item->source_id);
                        if ($alloc && $alloc->purchase_bill) {
                            $groupRef = $alloc->purchase_bill->bill_number;
                        }
                    } elseif ($srcType === 'app\sellerpayment' || $srcType === 'seller_payment') {
                        $pay = $payments->get($item->source_id);
                        if ($pay) {
                            $alloc = $pay->allocations->first();
                            if ($alloc && $alloc->purchase_bill) {
                                $groupRef = $alloc->purchase_bill->bill_number;
                            }
                        }
                    } elseif ($srcType === 'app\purchasematerial' || $srcType === 'material_purchase') {
                        $purch = $purchases->get($item->source_id);
                        if ($purch && $purch->purchaseBill) {
                            $groupRef = $purch->purchaseBill->bill_number;
                        }
                    }
                    return (!empty($groupRef) && $groupRef !== '-') ? $groupRef : 'tx_' . $item->transaction_id;
                })->map(function($group, $key) {
                    $sorted = $group->sortBy('date');
                    $earliest = $sorted->first();
                    return (object)[
                        'transaction_id' => $earliest->transaction_id,
                        'journal_id' => $earliest->journal_id,
                        'date' => $earliest->date,
                        'reference' => $key,
                        'description' => $earliest->description,
                        'debit' => $group->sum('debit'),
                        'credit' => $group->sum('credit'),
                        'base_debit' => $group->sum('base_debit'),
                        'base_credit' => $group->sum('base_credit'),
                        'currency_code' => $earliest->currency_code,
                        'exchange_rate' => $earliest->exchange_rate,
                        'base_currency_amount' => $group->sum('base_currency_amount'),
                        'original_amount' => $group->sum('original_amount'),
                        'source_type' => $earliest->source_type,
                        'source_id' => $earliest->source_id
                    ];
                })->values();
            }

            if ($request->get('export') === 'excel') {
                $excelPermissions = [
                    'customer' => 'export_customer_statement_excel',
                    'agents' => 'export_agent_statement_excel',
                    'different-account' => 'export_different_account_statement_excel',
                    'kachayee-team' => 'export_kachaee_statement_excel',
                    'washing-team' => 'export_washing_statement_excel',
                    'tayaari-team' => 'export_finishing_statement_excel',
                ];
                if (isset($excelPermissions[$entityKey])) {
                    abort_if(!auth()->user()->can($excelPermissions[$entityKey]), 403, 'Unauthorized.');
                }
                $filename = \Illuminate\Support\Str::slug($entityKey) . '_statement_' . date('Y_m_d_His') . '.xls';
                
                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');

                echo view('accounting.reports.entity_excel', compact(
                    'entries',
                    'selectedEntity',
                    'openingBalance',
                    'config',
                    'entityKey',
                    'startDate',
                    'endDate',
                    'logoBase64',
                    'topHeaderBase64'
                ))->render();
                exit;
            }

            if ($request->get('export') === 'pdf') {
                $pdfPermissions = [
                    'customer' => 'export_customer_statement_pdf',
                    'agents' => 'export_agent_statement_pdf',
                    'different-account' => 'export_different_account_statement_pdf',
                    'kachayee-team' => 'export_kachaee_statement_pdf',
                    'washing-team' => 'export_washing_statement_pdf',
                    'tayaari-team' => 'export_finishing_statement_pdf',
                ];
                if (isset($pdfPermissions[$entityKey])) {
                    abort_if(!auth()->user()->can($pdfPermissions[$entityKey]), 403, 'Unauthorized.');
                }
                return view('accounting.reports.entity_pdf', compact(
                    'entries',
                    'selectedEntity',
                    'openingBalance',
                    'config',
                    'entityKey',
                    'startDate',
                    'endDate',
                    'logoBase64',
                    'topHeaderBase64',
                    'bottomFooterBase64'
                ));
            }
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
            'search',
            'isSummary'
        ));
    }
}
