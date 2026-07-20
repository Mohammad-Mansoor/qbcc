@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br>
    
    <!-- Header Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #0f172a, #1e293b);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white font-weight-bold mb-1">
                                <i class="feather icon-file-text mr-2"></i>صورت حساب مالی: {{ $entityName }}
                            </h3>
                            <p class="mb-0 opacity-80">{{ $config['title'] }} | کد حساب: {{ $entity->account_no ?? $entity->id }}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route($config['parent_route']) }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                                <i class="feather icon-arrow-right mr-1"></i> بازگشت به {{ $config['parent_title'] }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; border-right: 5px solid #0f172a;">
                <div class="card-body p-4">
                    <form action="{{ route('accounting.statements.show', ['entity' => $entityKey, 'id' => $entity->id]) }}" method="GET" id="filterForm">
                        <div class="row">
                            <!-- Search -->
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">جستجو در توضیحات / شماره سند:</label>
                                <input type="text" name="search" value="{{ $search }}" class="form-control rounded-pill bg-light border-0" placeholder="توضیحات، سند مرجع، شماره دفتر...">
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">از تاریخ:</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control rounded-pill bg-light border-0">
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">الی تاریخ:</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control rounded-pill bg-light border-0">
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary rounded-pill btn-block shadow-sm" style="height: 38px;">
                                    <i class="feather icon-filter mr-1"></i> اعمال فیلتر
                                </button>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 d-flex justify-content-end">
                                <a href="{{ route('accounting.statements.show', ['entity' => $entityKey, 'id' => $entity->id]) }}" class="btn btn-light rounded-pill px-4 mr-2" style="height: 38px; display: inline-flex; align-items: center;">
                                    پاک کردن فیلتر
                                </a>
                                @php
                                    $excelPermission = null;
                                    $pdfPermission = null;
                                    if ($entityKey === 'customer') {
                                        $excelPermission = 'export_customer_statement_excel';
                                        $pdfPermission = 'export_customer_statement_pdf';
                                    } elseif ($entityKey === 'agents') {
                                        $excelPermission = 'export_agent_statement_excel';
                                        $pdfPermission = 'export_agent_statement_pdf';
                                    } elseif ($entityKey === 'different-account') {
                                        $excelPermission = 'export_different_account_statement_excel';
                                        $pdfPermission = 'export_different_account_statement_pdf';
                                    } elseif ($entityKey === 'kachayee-team') {
                                        $excelPermission = 'export_kachaee_statement_excel';
                                        $pdfPermission = 'export_kachaee_statement_pdf';
                                    } elseif ($entityKey === 'washing-team') {
                                        $excelPermission = 'export_washing_statement_excel';
                                        $pdfPermission = 'export_washing_statement_pdf';
                                    } elseif ($entityKey === 'tayaari-team') {
                                        $excelPermission = 'export_finishing_statement_excel';
                                        $pdfPermission = 'export_finishing_statement_pdf';
                                    }
                                @endphp

                                @if(!$pdfPermission || auth()->user()->can($pdfPermission))
                                <button type="button" class="btn btn-info rounded-pill px-4 mr-2 shadow-sm" onclick="window.print();" style="height: 38px;">
                                    <i class="fa fa-print mr-1"></i> چاپ صورت حساب
                                </button>
                                @endif

                                @if(!$excelPermission || auth()->user()->can($excelPermission))
                                <button type="submit" name="export" value="excel" class="btn btn-success rounded-pill px-4 shadow-sm" style="height: 38px;">
                                    <i class="fa fa-file-excel-o mr-1"></i> خروجی اکسل
                                </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius: 12px; background-color: #f8fafc; border-top: 4px solid #3b82f6;">
                <span class="d-block text-muted small font-weight-bold">موجودی قبلی (Opening Balance)</span>
                <h4 class="mb-0 mt-2 font-weight-bold text-primary" dir="ltr">
                    ${{ number_format($openingBalance, 2) }}
                </h4>
            </div>
        </div>
        @php
            // Calculate sums of current page
            $pageDebit = 0;
            $pageCredit = 0;
            foreach ($entries as $e) {
                $pageDebit += $e->base_debit;
                $pageCredit += $e->base_credit;
            }
        @endphp
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius: 12px; background-color: #f8fafc; border-top: 4px solid #10b981;">
                <span class="d-block text-muted small font-weight-bold">مجموع کریدیت صفحه (Page Credit)</span>
                <h4 class="mb-0 mt-2 font-weight-bold text-success" dir="ltr">
                    ${{ number_format($pageCredit, 2) }}
                </h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius: 12px; background-color: #f8fafc; border-top: 4px solid #ef4444;">
                <span class="d-block text-muted small font-weight-bold">مجموع دبییت صفحه (Page Debit)</span>
                <h4 class="mb-0 mt-2 font-weight-bold text-danger" dir="ltr">
                    ${{ number_format($pageDebit, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Print-Only Header -->
    <div class="print-only mb-4" style="display: none;">
        <table class="w-100" style="border: none;">
            <tr>
                <td style="text-align: right; border: none; width: 33%;">
                    <h5 class="font-weight-bold">شرکت تولیدی قالین برادران قاسمی</h5>
                    <p class="text-muted small mb-0">سیستم تفتیش مالی و اداری</p>
                </td>
                <td style="text-align: center; border: none; width: 33%;">
                    <h3 class="font-weight-bold mb-1">صورت حساب مالی تفصیلی</h3>
                    <p class="mb-0 font-weight-bold">{{ $config['title'] }}</p>
                </td>
                <td style="text-align: left; border: none; width: 33%;">
                    <p class="small mb-1"><b>تاریخ گزارش:</b> {{ date('Y-m-d H:i') }}</p>
                    <p class="small mb-0"><b>دوره مالی:</b> {{ request('from_date') ?: 'آغاز دوره' }} الی {{ request('to_date') ?: date('Y-m-d') }}</p>
                </td>
            </tr>
        </table>
        <hr style="border-top: 2px solid #000; margin-top: 15px;">
        <div class="row my-3">
            <div class="col-6">
                <p class="mb-1"><b>نام شخص/حساب:</b> {{ $entityName }}</p>
                <p class="mb-0"><b>کد حساب:</b> {{ $entity->account_no ?? $entity->id }}</p>
            </div>
            <div class="col-6 text-left">
                <p class="mb-1"><b>موجودی ابتدایی دوره:</b> ${{ number_format($openingBalance, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Statement Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                            <thead class="bg-dark text-white">
                                <tr class="text-right">
                                    <th class="py-3 px-4" style="width: 12%;">تاریخ سند</th>
                                    <th style="width: 13%;">شماره دفتر روزنامه</th>
                                    <th style="width: 15%;">سند مرجع</th>
                                    <th style="width: 25%;">تفصیلات / شرح تراکنش</th>
                                    <th class="text-left" style="width: 11%;">دبیت (بردگی)</th>
                                    <th class="text-left" style="width: 11%;">کریدیت (رسیدگی)</th>
                                    <th class="text-left" style="width: 13%;">باقی‌مانده (Running Balance)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentPage = $entries->currentPage();
                                    $perPage = $entries->perPage();
                                    $offset = ($currentPage - 1) * $perPage;
                                    
                                    // Calculate starting running balance for this page
                                    $prevQuery = DB::table('ledger_entries')
                                        ->where('party_type', $config['party_type'])
                                        ->where('party_id', $entity->id)
                                        ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                                        ->where('ledger_transactions.status', 'posted');
                                        
                                    if (request('from_date')) {
                                        $prevQuery->where('ledger_transactions.date', '>=', request('from_date'));
                                    }
                                    if (request('to_date')) {
                                        $prevQuery->where('ledger_transactions.date', '<=', request('to_date'));
                                    }
                                    if (request('search')) {
                                        $prevQuery->where(function($q) {
                                            $search = request('search');
                                            $q->where('ledger_transactions.description', 'like', "%{$search}%")
                                              ->orWhere('ledger_transactions.reference', 'like', "%{$search}%")
                                              ->orWhere('ledger_transactions.journal_id', 'like', "%{$search}%");
                                        });
                                    }
                                    
                                    $prevItems = $prevQuery->select('ledger_entries.base_debit', 'ledger_entries.base_credit')
                                        ->orderBy('ledger_transactions.date', 'ASC')
                                        ->orderBy('ledger_transactions.id', 'ASC')
                                        ->limit($offset)
                                        ->get();
                                        
                                    $prevSum = 0;
                                    foreach ($prevItems as $pi) {
                                        if ($entityKey === 'customer') {
                                            $prevSum += ($pi->base_debit - $pi->base_credit);
                                        } else {
                                            $prevSum += ($pi->base_credit - $pi->base_debit);
                                        }
                                    }
                                    
                                    $runningBalance = $openingBalance + $prevSum;
                                @endphp
                                
                                <tr class="bg-light font-weight-bold text-right">
                                    <td class="py-2 px-4 text-muted small">-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>موجودی ابتدایی صفحه (Page Opening Balance)</td>
                                    <td class="text-left font-mono">-</td>
                                    <td class="text-left font-mono">-</td>
                                    <td class="text-left font-mono text-primary" dir="ltr">
                                        ${{ number_format($runningBalance, 2) }}
                                    </td>
                                </tr>

                                @forelse($entries as $tx)
                                    @php
                                        if ($entityKey === 'customer') {
                                            $runningBalance += ($tx->base_debit - $tx->base_credit);
                                        } else {
                                            $runningBalance += ($tx->base_credit - $tx->base_debit);
                                        }
                                    @endphp
                                    <tr class="text-right">
                                        <td class="py-3 px-4 text-muted small" style="direction: ltr; font-family: monospace;">
                                            {{ $tx->date }}
                                        </td>
                                        <td class="font-weight-bold text-secondary">{{ $tx->journal_id }}</td>
                                        <td>
                                            @if($tx->reference)
                                                <span class="badge badge-light-secondary font-weight-bold rounded px-2 py-1" style="font-size: 0.8rem;">
                                                    {{ $tx->reference }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-dark">{{ $tx->description ?: 'بدون توضیحات' }}</td>
                                        <td class="text-left text-danger font-weight-bold font-mono" dir="ltr">
                                            {{ $tx->base_debit > 0 ? '$' . number_format($tx->base_debit, 2) : '-' }}
                                        </td>
                                        <td class="text-left text-success font-weight-bold font-mono" dir="ltr">
                                            {{ $tx->base_credit > 0 ? '$' . number_format($tx->base_credit, 2) : '-' }}
                                        </td>
                                        <td class="text-left font-weight-bold font-mono {{ $runningBalance >= 0 ? 'text-dark' : 'text-danger' }}" dir="ltr">
                                            ${{ number_format($runningBalance, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="feather icon-info f-30 d-block mb-3"></i>
                                            هیچ تراکنشی در این دوره یافت نشد.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($entries->hasPages())
            <div class="d-flex justify-content-center mt-4 no-print">
                {{ $entries->appends(request()->except('page'))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .font-mono {
        font-family: monospace;
    }
    .badge-light-secondary {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        body {
            background-color: white !important;
            color: black !important;
            font-size: 10pt;
        }
        .table th {
            background-color: #343a40 !important;
            color: white !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
