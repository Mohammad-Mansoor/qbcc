<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>General Ledger Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #111827;
            font-size: 8pt;
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
        .fixed-header img {
            width: 100%;
            display: block;
        }
        
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

        .header-space { height: 95px; }
        .footer-space { height: 160px; }

        .content-wrapper {
            padding-left: 3mm;
            padding-right: 3mm;
        }
        
        .title-block {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 2px;
            margin-top: 1px;
            margin-bottom: 3px;
        }
        .title-main { font-size: 10.5pt; font-weight: bold; color: #0f172a; margin: 0 0 1px 0; }
        .title-sub { font-size: 8pt; color: #475569; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 4px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 2px 5px; vertical-align: top; }
        .meta-label { font-size: 6.5pt; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 1px; display: block; }
        .meta-val-primary { font-size: 8pt; font-weight: bold; color: #0f172a; margin: 0; }
        .meta-val-secondary { font-size: 7pt; color: #475569; margin: 0; }
        
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            table-layout: auto;
        }
        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 7.5pt;
            padding: 2px 2px;
            text-align: center;
            white-space: nowrap;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 2px 2px;
            font-size: 7pt;
            color: #0f172a;
            line-height: 1.1;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        table.ledger-table tfoot td { background-color: #f1f5f9 !important; font-weight: bold; border-top: 2px solid #1e3a8a; font-size: 7.5pt; padding: 3px 2px; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-danger { color: #dc2626 !important; }
        .text-success { color: #16a34a !important; }
        .font-bold { font-weight: bold; }
        
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
                        <h2 class="title-main">راپور روزنامچه کل</h2>
                        <p class="title-sub">General Ledger Transactions Report</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="meta-label">پارامترهای جستجو (Filters):</span>
                                <p class="meta-val-secondary">وضعیت: {{ request('status') == 'posted' ? 'تایید شده' : (request('status') == 'draft' ? 'پیش‌نویس' : 'همه') }}</p>
                                <p class="meta-val-secondary">نوعیت سند: {{ request('journal_type') == 'journal' ? 'روزنامچه' : (request('journal_type') == 'payment' ? 'رسید/پرداخت' : 'همه') }}</p>
                                @if(request('account_id'))
                                @php $acc = \App\ChartOfAccount::find(request('account_id')); @endphp
                                <p class="meta-val-secondary">حساب خاص: {{ $acc ? $acc->account_code . ' - ' . ($acc->name_da ?? $acc->name_en) : 'نامشخص' }}</p>
                                @endif
                            </td>
                            <td style="width: 50%; text-align: left; direction: ltr;">
                                <span class="meta-label" style="text-align: right;">دوره گزارش (Period):</span>
                                <p class="meta-val-secondary" style="text-align: right;">از تاریخ (Start): <strong>{{ request('start_date') }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">الی تاریخ (End): <strong>{{ request('end_date') }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تاریخ صدور (Issue Date): <strong>{{ date('Y-m-d H:i') }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تهیه کننده (Generated By): <strong>{{ Auth::user()->name ?? 'System' }} {{ Auth::user()->last_name ?? '' }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    <table class="ledger-table">
                        <thead>
                            <tr>
                                <th>تاریخ (Date)</th>
                                <th>سند (Ref)</th>
                                <th style="width: 35%;">تفصیلات (Description)</th>
                                <th>حساب (Account)</th>
                                <th>دیبت (Debit)</th>
                                <th>کریدت (Credit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp

                            @foreach($transactions as $tx)
                                @foreach($tx->entries as $entry)
                                @php 
                                    $totalDebit += $entry->debit;
                                    $totalCredit += $entry->credit;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $tx->date }}</td>
                                    <td class="text-center font-bold">{{ $tx->journal_id }} <br> <span style="font-size: 6pt; color: #64748b;">{{ $tx->reference }}</span></td>
                                    <td>
                                        {{ $tx->description ?: 'بدون توضیحات' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $entry->account ? $entry->account->account_code . ' - ' . ($entry->account->name_da ?? $entry->account->name_en) : 'N/A' }}
                                    </td>
                                    <td class="text-left text-danger" style="direction: ltr;">
                                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                    </td>
                                    <td class="text-left text-success" style="direction: ltr;">
                                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-center">خلاصه این دوره (Period Totals):</td>
                                <td class="text-left text-danger font-bold" style="direction: ltr;">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-left text-success font-bold" style="direction: ltr;">{{ number_format($totalCredit, 2) }}</td>
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
