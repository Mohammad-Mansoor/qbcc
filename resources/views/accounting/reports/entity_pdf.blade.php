<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $config['title'] }} - {{ $selectedEntity->display_name }}</title>
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
        
        /* Remove ALL browser and PDF margins to allow images to touch edges */
        @page {
            size: A4 portrait;
            margin: 0; 
        }
        
        /* Full-bleed fixed header */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .fixed-header img {
            width: 100%;
            display: block;
        }
        
        /* Full-bleed fixed footer */
        .fixed-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .fixed-footer img {
            width: 100%;
            display: block;
        }

        /* Spacers to prevent content from hiding behind fixed header/footer */
        .header-space { height: 100px; }
        .footer-space { height: 120px; }

        /* Container for actual text to give it safe padding from page edges */
        .content-wrapper {
            padding-left: 3mm;
            padding-right: 3mm;
        }
        
        .title-block {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .title-main { font-size: 18pt; font-weight: bold; color: #0f172a; margin: 0 0 5px 0; }
        .title-sub { font-size: 12pt; color: #475569; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 15px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 12px; vertical-align: top; }
        .meta-label { font-size: 9pt; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .meta-val-primary { font-size: 12pt; font-weight: bold; color: #0f172a; margin: 0 0 4px 0; }
        .meta-val-secondary { font-size: 10pt; color: #475569; margin: 0 0 2px 0; }

        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            margin: 15px 0;
            gap: 10px;
        }
        .dashboard-col {
            flex: 1;
            min-width: 0;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: auto;
        }
        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 9.5pt;
            padding: 8px 5px;
            text-align: center;
            white-space: nowrap;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 5px;
            font-size: 9pt;
            color: #0f172a;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        table.ledger-table .opening-row td { background-color: #fffbeb !important; font-weight: bold; color: #1e293b; }
        table.ledger-table tfoot td { background-color: #f1f5f9 !important; font-weight: bold; border-top: 2px solid #1e3a8a; font-size: 9.5pt; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-danger { color: #dc2626 !important; }
        .text-success { color: #16a34a !important; }
        .font-bold { font-weight: bold; }
        
        .card-yellow { background-color: #fffbeb !important; border-color: #fde68a !important; }
        .card-red { background-color: #fef2f2 !important; border-color: #fca5a5 !important; }
        .card-green { background-color: #ecfdf5 !important; border-color: #a7f3d0 !important; }
        .card-blue { background-color: #eff6ff !important; border-color: #bfdbfe !important; }
        
        .small-title { font-size: 8.5pt; font-weight: bold; margin-bottom: 5px; }
        .card-val { font-size: 12pt; font-weight: bold; margin: 0; }
        
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            table.ledger-table tfoot { display: table-row-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

<!-- Fixed Header Image -->
<div class="fixed-header">
    @if($topHeaderBase64)
        <img src="{{ $topHeaderBase64 }}" alt="Header">
    @endif
</div>

<!-- Fixed Footer Image -->
<div class="fixed-footer">
    @if($bottomFooterBase64)
        <img src="{{ $bottomFooterBase64 }}" alt="Footer">
    @endif
</div>

<!-- Main Table wrapper to provide repeating header/footer whitespace gaps -->
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
                        <h2 class="title-main">صورت حساب تفصیلی {{ trim(explode('(', $config['title'])[0]) }}</h2>
                        <p class="title-sub">Detailed {{ isset(explode('(', $config['title'])[1]) ? trim(str_replace(')', '', explode('(', $config['title'])[1])) . ' ' : '' }}Statement</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="meta-label">حساب (Account Party):</span>
                                <p class="meta-val-primary">{{ $selectedEntity->display_name }}</p>
                                <p class="meta-val-secondary">کد حساب (Account Code): {{ $selectedEntity->id }}</p>
                                <p class="meta-val-secondary">آدرس (Address): {{ $selectedEntity->address ?? 'ثبت نشده' }}</p>
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

                    <div class="dashboard-row">
                        <div class="dashboard-col card-yellow">
                            <div class="small-title text-muted">بیلانس قبلی (Opening)</div>
                            <p class="card-val">${{ number_format($openingBalance, 2) }}</p>
                        </div>
                        <div class="dashboard-col card-red">
                            <div class="small-title text-danger">دیبت / فروش (Debit)</div>
                            <p class="card-val text-danger">${{ number_format($entries->sum('debit'), 2) }}</p>
                        </div>
                        <div class="dashboard-col card-green">
                            <div class="small-title text-success">کریدیت / رسید (Credit)</div>
                            <p class="card-val text-success">${{ number_format($entries->sum('credit'), 2) }}</p>
                        </div>
                        @php 
                            $isCustomer = ($entityKey === 'customer');
                            if ($isCustomer) {
                                $closing = $openingBalance + $entries->sum('debit') - $entries->sum('credit'); 
                            } else {
                                $closing = $openingBalance + $entries->sum('credit') - $entries->sum('debit'); 
                            }
                        @endphp
                        <div class="dashboard-col card-blue">
                            <div class="small-title" style="color: #2563eb;">بیلانس نهایی (Closing)</div>
                            <p class="card-val" style="color: #2563eb;">${{ number_format($closing, 2) }}</p>
                        </div>
                    </div>

                    <table class="ledger-table">
                        <thead>
                            <tr>
                                <th>تاریخ (Date)</th>
                                <th>سند (Ref)</th>
                                <th style="width: 40%;">تفصیلات (Description)</th>
                                <th>دیبت / فروش</th>
                                <th>کریدت / رسید</th>
                                <th>بیلانس نهایی</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentRunning = $openingBalance; @endphp
                            <tr class="opening-row">
                                <td colspan="5" class="text-right">بیلانس قبلی (Opening Balance Forwarded)</td>
                                <td class="text-left font-bold" style="direction: ltr;">${{ number_format($openingBalance, 2) }}</td>
                            </tr>

                            @php $isCustomer = ($entityKey === 'customer'); @endphp
                            @foreach($entries as $entry)
                                @php 
                                    if ($isCustomer) {
                                        $currentRunning += ($entry->debit - $entry->credit);
                                    } else {
                                        $currentRunning += ($entry->credit - $entry->debit);
                                    }
                                @endphp
                            <tr>
                                <td class="text-center">{{ $entry->date }}</td>
                                <td class="text-center font-bold">{{ $entry->reference ?: '-' }}</td>
                                <td>
                                    {{ $entry->description ?: 'بدون توضیحات' }}
                                    @if($entry->currency_code != \App\Currency::getBase()->code && ($entry->debit > 0 || $entry->credit > 0))
                                        <div style="font-size: 8pt; color: #64748b; margin-top: 3px;" dir="ltr">
                                            Original: {{ number_format($entry->original_amount, 2) }} {{ $entry->currency_code }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-left text-danger" style="direction: ltr;">
                                    {{ $entry->debit > 0 ? '$' . number_format($entry->debit, 2) : '-' }}
                                </td>
                                <td class="text-left text-success" style="direction: ltr;">
                                    {{ $entry->credit > 0 ? '$' . number_format($entry->credit, 2) : '-' }}
                                </td>
                                <td class="text-center font-bold" dir="ltr" style="{{ $currentRunning < 0 ? 'color: #e53935;' : '' }}">
                                    {{ number_format(abs($currentRunning), 2) }} {{ $currentRunning >= 0 ? ($isCustomer ? '(Dr)' : '(Cr)') : ($isCustomer ? '(Cr)' : '(Dr)') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-center">خلاصه این دوره (Period Totals):</td>
                                <td class="text-left text-danger" style="direction: ltr;">${{ number_format($entries->sum('debit'), 2) }}</td>
                                <td class="text-left text-success" style="direction: ltr;">${{ number_format($entries->sum('credit'), 2) }}</td>
                                <td class="text-left font-bold" style="direction: ltr; font-size: 11pt; color: #1e3a8a;">
                                    {{ number_format(abs($currentRunning), 2) }} {{ $currentRunning >= 0 ? ($isCustomer ? '(Dr)' : '(Cr)') : ($isCustomer ? '(Cr)' : '(Dr)') }}
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
                <div class="footer-space"></div>
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>
