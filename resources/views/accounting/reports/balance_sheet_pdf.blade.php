<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
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
        
        table.layout-table { width: 100%; table-layout: fixed; }
        table.layout-table td.col-half { width: 50%; vertical-align: top; padding: 2px 5px; }

        .section-title { font-size: 10pt; font-weight: bold; margin-bottom: 4px; padding-bottom: 2px; border-bottom: 2px solid #cbd5e1; }
        .title-assets { color: #1d4ed8; border-bottom-color: #1d4ed8; }
        .title-liab { color: #6b21a8; border-bottom-color: #6b21a8; }

        table.bs-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.bs-table th { background-color: #f1f5f9 !important; border: 1px solid #cbd5e1; font-size: 8pt; padding: 3px 5px; text-align: right; }
        table.bs-table td { border: 1px solid #cbd5e1; padding: 3px 5px; font-size: 8pt; color: #0f172a; line-height: 1.2; }
        
        table.bs-table .subheader td { background-color: #f8fafc !important; font-size: 7.5pt; font-weight: bold; color: #64748b; }
        
        table.bs-table .total-row td { font-weight: bold; font-size: 9pt; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        table.bs-table .total-assets td { background-color: #1d4ed8 !important; }
        table.bs-table .total-liab td { background-color: #6b21a8 !important; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-danger { color: #dc2626 !important; }
        .text-success { color: #16a34a !important; }
        
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

@php 
    $totalAssets = $assets->sum('balance');
    $totalLiabEquity = $liabilities->sum('balance') + $equity->sum('balance') + $currentNetProfit;
    $isBalanced = abs($totalAssets - $totalLiabEquity) < 0.01;
@endphp

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
                        <h2 class="title-main">ترازنامه (<span dir="ltr">USD</span>)</h2>
                        <p class="title-sub">Balance Sheet Statement</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 33%;">
                                <span class="meta-label">وضعیت توازن (Balance Status):</span>
                                @if($isBalanced)
                                    <p class="meta-val-secondary font-weight-bold" style="color: #16a34a;">متوازن است (Balanced)</p>
                                @else
                                    <p class="meta-val-secondary font-weight-bold" style="color: #dc2626;">نامتوازن (Unbalanced: {{ number_format($totalAssets - $totalLiabEquity, 2) }})</p>
                                @endif
                            </td>
                            <td style="width: 33%;">
                                <span class="meta-label">واحد پولی گزارش (Currency):</span>
                                <p class="meta-val-secondary font-weight-bold" style="font-size: 10pt; color: #0f172a;">USD</p>
                            </td>
                            <td style="width: 34%; text-align: left; direction: ltr;">
                                <span class="meta-label" style="text-align: right;">تاریخ (Date):</span>
                                <p class="meta-val-secondary" style="text-align: right;">به تاریخ (As Of): <strong>{{ $endDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تاریخ صدور (Issue): <strong>{{ date('Y-m-d H:i') }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    <table class="layout-table">
                        <tr>
                            <!-- ASSETS SIDE -->
                            <td class="col-half">
                                <div class="section-title title-assets">دارایی‌ها (Assets)</div>
                                <table class="bs-table">
                                    <thead>
                                        <tr>
                                            <th>شرح حساب (Account Name)</th>
                                            <th class="text-left">مبلغ (Amount)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $row)
                                        <tr>
                                            <td>{{ $row->account_name }}</td>
                                            <td class="text-left font-weight-bold" style="direction: ltr;">{{ number_format($row->balance, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row total-assets">
                                            <td>مجموع دارایی‌ها (Total Assets)</td>
                                            <td class="text-left" style="direction: ltr;">{{ number_format($totalAssets, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>

                            <!-- LIABILITIES & EQUITY SIDE -->
                            <td class="col-half">
                                <div class="section-title title-liab">بدهی و سرمایه (Liabilities & Equity)</div>
                                <table class="bs-table">
                                    <thead>
                                        <tr>
                                            <th>شرح حساب (Account Name)</th>
                                            <th class="text-left">مبلغ (Amount)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="subheader"><td colspan="2">بدهی‌ها (Liabilities)</td></tr>
                                        @foreach($liabilities as $row)
                                        <tr>
                                            <td>{{ $row->account_name }}</td>
                                            <td class="text-left font-weight-bold" style="direction: ltr;">{{ number_format($row->balance, 2) }}</td>
                                        </tr>
                                        @endforeach
                                        
                                        <tr class="subheader"><td colspan="2">سرمایه و سود (Equity & Profit)</td></tr>
                                        @foreach($equity as $row)
                                        <tr>
                                            <td>{{ $row->account_name }}</td>
                                            <td class="text-left font-weight-bold" style="direction: ltr;">{{ number_format($row->balance, 2) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td>سود/ضرر دوره جاری (Current P&L)</td>
                                            <td class="text-left font-weight-bold {{ $currentNetProfit >= 0 ? 'text-success' : 'text-danger' }}" style="direction: ltr;">
                                                {{ number_format($currentNetProfit, 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row total-liab">
                                            <td>مجموع بدهی و سرمایه (Total Liab & Equity)</td>
                                            <td class="text-left" style="direction: ltr;">{{ number_format($totalLiabEquity, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                    </table>

                </div>
            </td>
        </tr>
    </tbody>
    <tfoot><tr><td style="border: none; padding: 0;"><div class="footer-space"></div></td></tr></tfoot>
</table>

</body>
</html>
