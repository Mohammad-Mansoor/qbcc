<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Account Ledger - {{ isset($account) ? $account->account_name : 'Audit Filter' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #111827;
        }
        
        @page {
            size: A4 portrait;
            margin: 0; 
        }
        
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .fixed-header img { width: 100%; display: block; }
        
        .fixed-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .fixed-footer img { width: 100%; display: block; }

        .header-space { height: 85px; }
        .footer-space { height: 149px; }

        .content-wrapper {
            padding-left: 2mm;
            padding-right: 2mm;
        }
        
        .title-block {
            text-align: center;
            border-bottom: 1px solid #1e3a8a;
            padding-bottom: 5px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .title-main { font-size: 12pt; font-weight: bold; color: #0f172a; margin: 0 0 2px 0; }
        .title-sub { font-size: 9pt; color: #475569; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 5px; background: #f8fafc; border-radius: 5px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 4px; vertical-align: top; }
        .meta-label { font-size: 7.5pt; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; display: block; }
        .meta-val-primary { font-size: 9pt; font-weight: bold; color: #0f172a; margin: 0 0 2px 0; }
        .meta-val-secondary { font-size: 8pt; color: #475569; margin: 0 0 1px 0; }

        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            margin: 5px 0;
            gap: 5px;
        }
        .dashboard-col {
            flex: 1;
            min-width: 0;
            padding: 4px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: auto;
        }
        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 7.5pt;
            padding: 3px 2px;
            text-align: center;
            white-space: nowrap;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 2px;
            font-size: 7.5pt;
            color: #0f172a;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        table.ledger-table .opening-row td { background-color: #fffbeb !important; font-weight: bold; color: #1e293b; font-size: 7.5pt; }
        table.ledger-table tfoot td { background-color: #f1f5f9 !important; font-weight: bold; border-top: 1px solid #1e3a8a; font-size: 8pt; padding: 3px 2px; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-danger { color: #dc2626 !important; }
        .text-primary { color: #2563eb !important; }
        .font-bold { font-weight: bold; }
        
        .card-yellow { background-color: #fffbeb !important; border-color: #fde68a !important; }
        .card-red { background-color: #fef2f2 !important; border-color: #fca5a5 !important; }
        .card-blue { background-color: #eff6ff !important; border-color: #bfdbfe !important; }
        
        .small-title { font-size: 7pt; font-weight: bold; margin-bottom: 2px; }
        .card-val { font-size: 9pt; font-weight: bold; margin: 0; }
        
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            table.ledger-table tfoot { display: table-row-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

<div class="fixed-header">
    @if($topHeaderBase64)
        <img src="{{ $topHeaderBase64 }}" alt="Header">
    @endif
</div>

<div class="fixed-footer">
    @if($bottomFooterBase64)
        <img src="{{ $bottomFooterBase64 }}" alt="Footer">
    @endif
</div>

<table style="width: 100%; border: none; border-collapse: collapse;">
    <thead>
        <tr>
            <td style="border: none; padding: 0;">
                <div class="header-space"></div>
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: none; padding: 0;">
                
                <div class="content-wrapper">
                    <div class="title-block">
                        <h2 class="title-main">دفتر تفصیلی حساب</h2>
                        <p class="title-sub">Account Ledger</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="meta-label">حساب (Account):</span>
                                <p class="meta-val-primary">{{ isset($account) ? $account->account_name : 'گزارش تفتیش (Audit Filter)' }}</p>
                                @if(isset($account))
                                    <p class="meta-val-secondary">کد حساب (Account Code): {{ $account->account_code }}</p>
                                    <p class="meta-val-secondary">نوعیت (Type): {{ $account->account_type }}</p>
                                    <p class="meta-val-secondary">ارز حساب (Currency): {{ $account->currency }}</p>
                                @endif
                            </td>
                            <td style="width: 50%; text-align: left; direction: ltr;">
                                <span class="meta-label" style="text-align: right;">دوره گزارش (Period):</span>
                                <p class="meta-val-secondary" style="text-align: right;">از تاریخ (Start): <strong>{{ $startDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">الی تاریخ (End): <strong>{{ $endDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تاریخ صدور (Issue Date): <strong>{{ date('Y-m-d H:i') }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تهیه کننده (Generated By): <strong>{{ Auth::user()->name ?? 'System' }} {{ Auth::user()->last_name ?? '' }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    @php
                        $accCurrency = isset($account) ? $account->currency : 'USD';
                        $accRate = (isset($currencies) && isset($currencies[$accCurrency]) && $currencies[$accCurrency]->exchange_rate > 0) ? $currencies[$accCurrency]->exchange_rate : 1.0;
                    @endphp

                    <div class="dashboard-row">
                        <div class="dashboard-col card-yellow">
                            <div class="small-title text-muted">بیلانس قبلی (Opening)</div>
                            <p class="card-val">${{ number_format($openingBalance, 2) }}</p>
                            @if($accCurrency !== 'USD')
                                <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">
                                    {{ number_format($openingBalance / $accRate, 2) }} {{ $accCurrency }}
                                </div>
                            @endif
                        </div>
                        <div class="dashboard-col card-blue">
                            <div class="small-title text-primary">مجموع دیبت (Total Debit)</div>
                            <p class="card-val text-primary">+ ${{ number_format($entries->sum('debit'), 2) }}</p>
                            @if($accCurrency !== 'USD')
                                <div style="font-size: 7pt; color: #2563eb; margin-top: 2px;">
                                    + {{ number_format($entries->sum('debit') / $accRate, 2) }} {{ $accCurrency }}
                                </div>
                            @endif
                        </div>
                        <div class="dashboard-col card-red">
                            <div class="small-title text-danger">مجموع کریدت (Total Credit)</div>
                            <p class="card-val text-danger">- ${{ number_format($entries->sum('credit'), 2) }}</p>
                            @if($accCurrency !== 'USD')
                                <div style="font-size: 7pt; color: #dc2626; margin-top: 2px;">
                                    - {{ number_format($entries->sum('credit') / $accRate, 2) }} {{ $accCurrency }}
                                </div>
                            @endif
                        </div>
                        @php 
                            $runningBalance = $openingBalance;
                            foreach($entries as $entry) {
                                $normal = (isset($account) && $account->normal_balance == 'credit') ? 'credit' : 'debit';
                                if ($normal == 'debit') { $runningBalance += ($entry->debit - $entry->credit); } 
                                else { $runningBalance += ($entry->credit - $entry->debit); }
                            }
                        @endphp
                        <div class="dashboard-col card-blue">
                            <div class="small-title" style="color: #1e3a8a;">بیلانس نهایی (Closing)</div>
                            <p class="card-val" style="color: #1e3a8a;">${{ number_format($runningBalance, 2) }}</p>
                            @if($accCurrency !== 'USD')
                                <div style="font-size: 7pt; color: #1e3a8a; margin-top: 2px;">
                                    {{ number_format($runningBalance / $accRate, 2) }} {{ $accCurrency }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <table class="ledger-table">
                        <thead>
                            <tr>
                                <th>تاریخ (Date)</th>
                                <th>سند (Ref)</th>
                                <th style="width: 40%;">تفصیلات (Description)</th>
                                <th>دیبت (Debit)</th>
                                <th>کریدت (Credit)</th>
                                <th>بیلانس (Balance)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentRunning = $openingBalance; @endphp
                            <tr class="opening-row">
                                <td colspan="5" class="text-right">بیلانس انتقالی (Opening Balance Forwarded)</td>
                                <td class="text-left font-bold" style="direction: ltr;">
                                    ${{ number_format($openingBalance, 2) }}
                                    @if($accCurrency !== 'USD')
                                        <div style="font-size: 6.5pt; color: #64748b; font-weight: normal;">
                                            {{ number_format($openingBalance / $accRate, 2) }} {{ $accCurrency }}
                                        </div>
                                    @endif
                                </td>
                            </tr>

                            @foreach($entries as $entry)
                                @php 
                                    $normal = (isset($account) && $account->normal_balance == 'credit') ? 'credit' : 'debit';
                                    if ($normal == 'debit') { $currentRunning += ($entry->debit - $entry->credit); } 
                                    else { $currentRunning += ($entry->credit - $entry->debit); }
                                @endphp
                            <tr>
                                <td class="text-center">{{ $entry->date }}</td>
                                <td class="text-center font-bold">{{ $entry->reference ?: '-' }}</td>
                                <td>
                                    {{ $entry->description ?: 'بدون توضیحات' }}
                                    @if($entry->currency_code != \App\Currency::getBase()->code && ($entry->debit > 0 || $entry->credit > 0))
                                        <div style="font-size: 6.5pt; color: #64748b; margin-top: 1px;" dir="ltr">
                                            Original: {{ number_format($entry->original_amount, 2) }} {{ $entry->currency_code }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-left text-primary" style="direction: ltr;">
                                    {{ $entry->debit > 0 ? '$' . number_format($entry->debit, 2) : '-' }}
                                </td>
                                <td class="text-left text-danger" style="direction: ltr;">
                                    {{ $entry->credit > 0 ? '$' . number_format($entry->credit, 2) : '-' }}
                                </td>
                                <td class="text-left font-bold" style="direction: ltr;">
                                    ${{ number_format($currentRunning, 2) }}
                                    @if($accCurrency !== 'USD')
                                        <div style="font-size: 6.5pt; color: #64748b; font-weight: normal;">
                                            {{ number_format($currentRunning / $accRate, 2) }} {{ $accCurrency }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-center">خلاصه این دوره (Period Totals):</td>
                                <td class="text-left text-primary" style="direction: ltr;">
                                    ${{ number_format($entries->sum('debit'), 2) }}
                                    @if($accCurrency !== 'USD')
                                        <div style="font-size: 6.5pt; color: #2563eb; font-weight: normal;">
                                            {{ number_format($entries->sum('debit') / $accRate, 2) }} {{ $accCurrency }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-left text-danger" style="direction: ltr;">
                                    ${{ number_format($entries->sum('credit'), 2) }}
                                    @if($accCurrency !== 'USD')
                                        <div style="font-size: 6.5pt; color: #dc2626; font-weight: normal;">
                                            {{ number_format($entries->sum('credit') / $accRate, 2) }} {{ $accCurrency }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-left font-bold" style="direction: ltr; font-size: 8.5pt; color: #1e3a8a;">
                                    ${{ number_format($currentRunning, 2) }}
                                    @if($accCurrency !== 'USD')
                                        <div style="font-size: 6.5pt; color: #1e3a8a; font-weight: normal;">
                                            {{ number_format($currentRunning / $accRate, 2) }} {{ $accCurrency }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="border: none; padding: 0;">
                <div class="footer-space">&nbsp;</div>
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>
