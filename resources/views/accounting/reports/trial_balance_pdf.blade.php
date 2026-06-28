<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Trial Balance</title>
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
        
        @page { size: A4 portrait; margin: 0; }
        
        .fixed-header { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .fixed-header img { width: 100%; display: block; }
        .fixed-footer { position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1000; }
        .fixed-footer img { width: 100%; display: block; }

        .header-space { height: 95px; }
        .footer-space { height: 160px; }

        .content-wrapper { padding-left: 5mm; padding-right: 5mm; }
        
        .title-block { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 2px; margin-top: 1px; margin-bottom: 5px; }
        .title-main { font-size: 12pt; font-weight: bold; color: #0f172a; margin: 0 0 1px 0; }
        .title-sub { font-size: 9pt; color: #475569; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 8px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 4px 8px; vertical-align: top; }
        .meta-label { font-size: 7.5pt; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 1px; display: block; }
        .meta-val-secondary { font-size: 8pt; color: #475569; margin: 0 0 2px 0; }
        
        table.tb-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: auto;
        }
        table.tb-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 8.5pt;
            padding: 4px 6px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.tb-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 6px;
            font-size: 8pt;
            color: #0f172a;
            line-height: 1.2;
        }
        table.tb-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        table.tb-table .total-row td {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 9pt;
            border-top: 2px solid #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

<div class="fixed-header">
    @if($topHeaderBase64) <img src="{{ $topHeaderBase64 }}" alt="Header"> @endif
</div>

<div class="fixed-footer">
    @if($bottomFooterBase64) <img src="{{ $bottomFooterBase64 }}" alt="Footer"> @endif
</div>

<table style="width: 100%; border: none; border-collapse: collapse;">
    <thead><tr><td style="border: none; padding: 0;"><div class="header-space"></div></td></tr></thead>
    <tbody>
        <tr>
            <td style="border: none; padding: 0;">
                <div class="content-wrapper">
                    <div class="title-block">
                        <h2 class="title-main">بیلانس آزمایشی</h2>
                        <p class="title-sub">Trial Balance Statement</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="meta-label">واحد پولی گزارش (Currency):</span>
                                <p class="meta-val-secondary font-weight-bold" style="font-size: 10pt; color: #0f172a;">USD</p>
                            </td>
                            <td style="width: 50%; text-align: left; direction: ltr;">
                                <span class="meta-label" style="text-align: right;">دوره گزارش (Period):</span>
                                <p class="meta-val-secondary" style="text-align: right;">تا تاریخ (As Of): <strong>از {{ $startDate }} الی {{ $endDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تاریخ صدور (Issue Date): <strong>{{ date('Y-m-d H:i') }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    <table class="tb-table">
                        <thead>
                            <tr>
                                <th>کد حساب (Code)</th>
                                <th>نام حساب (Account Name)</th>
                                <th>دیبت (Debit)</th>
                                <th>کریدت (Credit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp

                            @foreach($report as $row)
                                @php
                                    $isDebit = $row->balance > 0;
                                    $isCredit = $row->balance < 0;
                                    
                                    if ($isDebit) $totalDebit += abs($row->balance);
                                    if ($isCredit) $totalCredit += abs($row->balance);
                                @endphp
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $row->account_code }}</td>
                                    <td>{{ $row->account_name }}</td>
                                    <td class="text-left" style="direction: ltr;">
                                        {{ $isDebit ? number_format(abs($row->balance), 2) : '-' }}
                                    </td>
                                    <td class="text-left" style="direction: ltr;">
                                        {{ $isCredit ? number_format(abs($row->balance), 2) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" class="text-center">مجموع (Total):</td>
                                <td class="text-left" style="direction: ltr;">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-left" style="direction: ltr;">{{ number_format($totalCredit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot><tr><td style="border: none; padding: 0;"><div class="footer-space"></div></td></tr></tfoot>
</table>

</body>
</html>
