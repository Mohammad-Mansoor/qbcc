<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Profit & Loss - {{ $currencyCode }}</title>
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
        
        table.pl-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: auto;
        }
        table.pl-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 8.5pt;
            color: #0f172a;
            line-height: 1.2;
        }
        table.pl-table .section-header td {
            background-color: #f1f5f9 !important;
            color: #1e3a8a !important;
            font-weight: bold;
            font-size: 10pt;
            border-top: 2px solid #1e3a8a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.pl-table .row-main td {
            background-color: #ffffff !important;
            font-weight: bold;
        }
        table.pl-table .sub-row td {
            background-color: #ffffff !important;
            color: #64748b !important;
            font-size: 7.5pt;
            border-top: none;
            padding-top: 1px;
            padding-bottom: 1px;
        }
        table.pl-table .sub-row td.indent {
            padding-right: 25px;
        }
        table.pl-table .total-row td {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            font-size: 9.5pt;
            border-top: 2px solid #94a3b8;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.pl-table .net-profit td {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 12pt;
            border-top: 3px solid #0f172a;
            padding: 8px 6px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

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
if (!function_exists('formatAccounting')) {
    function formatAccounting($val, $rate, $currencyCode = '') {
        $amount = floatval($rate) > 0 ? (floatval($val) / floatval($rate)) : 0;
        if ($amount < 0) {
            $formatted = '(' . number_format(abs($amount), 2) . ')';
        } else {
            $formatted = number_format($amount, 2);
        }
        return $formatted . ($currencyCode ? ' ' . $currencyCode : '');
    }
}
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
                        <h2 class="title-main">صورت سود و ضرر (<span dir="ltr">{{ $currencyCode }}</span>)</h2>
                        <p class="title-sub">Profit & Loss Statement</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="meta-label">واحد پولی گزارش (Currency):</span>
                                <p class="meta-val-secondary font-weight-bold" style="font-size: 10pt; color: #0f172a;">{{ $currencyCode }}</p>
                                @if($currencyCode != 'USD')
                                <p class="meta-val-secondary">نرخ تبدیل (Rate): {{ $rate }}</p>
                                @endif
                            </td>
                            <td style="width: 50%; text-align: left; direction: ltr;">
                                <span class="meta-label" style="text-align: right;">دوره گزارش (Period):</span>
                                <p class="meta-val-secondary" style="text-align: right;">از تاریخ (Start): <strong>{{ $startDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">الی تاریخ (End): <strong>{{ $endDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تاریخ صدور (Issue Date): <strong>{{ date('Y-m-d H:i') }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    <table class="pl-table">
                        <tbody>
                            <!-- REVENUE SECTION -->
                            <tr class="section-header">
                                <td colspan="2">عواید عملیاتی (Operating Revenue)</td>
                            </tr>
                            @foreach($revenue as $row)
                            <tr class="row-main">
                                <td>{{ $row->account_code }} - {{ $row->account_name }}</td>
                                <td class="text-left {{ $row->balance >= 0 ? 'text-success' : 'text-danger' }}" style="direction: ltr;">
                                    {{ formatAccounting($row->balance, $rate) }}
                                </td>
                            </tr>
                            <tr class="sub-row">
                                <td class="indent">└─ فروش ناخالص (Gross Sales)</td>
                                <td class="text-left" style="direction: ltr;">{{ formatAccounting($row->total_credit, $rate) }}</td>
                            </tr>
                            <tr class="sub-row">
                                <td class="indent" style="border-bottom: 1px solid #cbd5e1;">└─ منهای اصلاحات (Less Reversals)</td>
                                <td class="text-left text-danger" style="direction: ltr; border-bottom: 1px solid #cbd5e1;">{{ formatAccounting($row->total_debit, $rate) }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td>مجموع عواید (Total Revenue)</td>
                                <td class="text-left text-success" style="direction: ltr;">{{ formatAccounting($revenue->sum('balance'), $rate) }}</td>
                            </tr>

                            <!-- SPACER -->
                            <tr><td colspan="2" style="border: none; height: 10px;"></td></tr>

                            <!-- EXPENSE SECTION -->
                            <tr class="section-header">
                                <td colspan="2">مصارف عملیاتی (Operating Expenses)</td>
                            </tr>
                            @foreach($expenses as $row)
                            <tr class="row-main">
                                <td>{{ $row->account_code }} - {{ $row->account_name }}</td>
                                <td class="text-left {{ $row->balance >= 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ formatAccounting($row->balance, $rate) }}
                                </td>
                            </tr>
                            <tr class="sub-row">
                                <td class="indent">└─ مصارف ثبت شده (Recorded Expenses)</td>
                                <td class="text-left" style="direction: ltr;">{{ formatAccounting($row->total_debit, $rate) }}</td>
                            </tr>
                            <tr class="sub-row">
                                <td class="indent" style="border-bottom: 1px solid #cbd5e1;">└─ منهای اصلاحات (Less Reversals)</td>
                                <td class="text-left text-success" style="direction: ltr; border-bottom: 1px solid #cbd5e1;">{{ formatAccounting($row->total_credit, $rate) }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td>مجموع مصارف (Total Expenses)</td>
                                <td class="text-left text-danger" style="direction: ltr;">{{ formatAccounting($expenses->sum('balance'), $rate) }}</td>
                            </tr>

                            <!-- SPACER -->
                            <tr><td colspan="2" style="border: none; height: 15px;"></td></tr>

                            <!-- NET PROFIT -->
                            <tr class="net-profit">
                                <td>سود / زیان خالص (Net Profit / Loss)</td>
                                <td class="text-left" style="direction: ltr;">{{ formatAccounting($netProfit, $rate, $currencyCode) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot><tr><td style="border: none; padding: 0;"><div class="footer-space"></div></td></tr></tfoot>
</table>

</body>
</html>
