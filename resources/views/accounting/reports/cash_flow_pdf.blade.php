<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Cash Flow Statement</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #111827;
            font-size: 8.5pt;
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

        .content-wrapper { padding-left: 10mm; padding-right: 10mm; }
        
        .title-block { text-align: center; border-bottom: 2px solid #00838f; padding-bottom: 5px; margin-top: 5px; margin-bottom: 10px; }
        .title-main { font-size: 14pt; font-weight: bold; color: #006064; margin: 0 0 2px 0; }
        .title-sub { font-size: 10pt; color: #475569; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 6px 10px; vertical-align: top; }
        .meta-label { font-size: 8pt; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; display: block; }
        .meta-val-secondary { font-size: 8.5pt; color: #0f172a; margin: 0 0 2px 0; font-weight: bold;}
        
        table.cf-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: auto;
        }
        table.cf-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 9pt;
            color: #0f172a;
            line-height: 1.3;
        }
        table.cf-table .section-header td {
            background-color: #e0f7fa !important;
            color: #00838f !important;
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #00838f;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.cf-table .section-header-warning td {
            background-color: #fffde7 !important;
            color: #fbc02d !important;
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #fbc02d;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.cf-table .section-header-success td {
            background-color: #e8f5e9 !important;
            color: #43a047 !important;
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #43a047;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.cf-table .row-main td {
            background-color: #ffffff !important;
        }
        table.cf-table .total-row td {
            background-color: #f1f5f9 !important;
            font-weight: bold;
            font-size: 10pt;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.cf-table .net-change td {
            background-color: #1e293b !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 12pt;
            border-top: 3px solid #0f172a;
            padding: 10px 8px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .reconciliation-table { width: 100%; border-collapse: collapse; margin-top: 15px; border: 2px solid #cbd5e1; }
        .reconciliation-table td { padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9pt;}
        .reconciliation-header { background: #f8fafc; font-weight: bold; text-align: center; font-size: 11pt; border-bottom: 2px solid #cbd5e1; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-danger { color: #dc2626 !important; }
        .text-success { color: #16a34a !important; }
        .text-info { color: #0284c7 !important; }
        .font-weight-bold { font-weight: bold; }
        
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
                        <h2 class="title-main">گزارش جریان وجوه نقد (روش غیر مستقیم)</h2>
                        <p class="title-sub">Cash Flow Statement</p>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="meta-label">واحد پولی گزارش (Currency):</span>
                                <p class="meta-val-secondary font-weight-bold" style="font-size: 10pt; color: #0f172a;">USD (دالر)</p>
                            </td>
                            <td style="width: 50%; text-align: left; direction: ltr;">
                                <span class="meta-label" style="text-align: right;">دوره گزارش (Period):</span>
                                <p class="meta-val-secondary" style="text-align: right;">از تاریخ (Start): <strong>{{ $startDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">الی تاریخ (End): <strong>{{ $endDate }}</strong></p>
                                <p class="meta-val-secondary" style="text-align: right;">تاریخ صدور (Issue Date): <strong>{{ date('Y-m-d H:i') }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    <table class="cf-table">
                        <tbody>
                            <!-- OPERATING ACTIVITIES -->
                            <tr class="section-header">
                                <td colspan="2">۱. فعالیت‌های عملیاتی (Operating Activities)</td>
                            </tr>
                            <tr class="row-main font-weight-bold bg-light">
                                <td>سود خالص دوره (Net Profit)</td>
                                <td class="text-left" style="direction: ltr;">{{ number_format($net_profit, 2) }}</td>
                            </tr>
                            <tr class="row-main">
                                <td colspan="2" style="background:#f8fafc; color:#64748b; font-size:8pt; border-bottom: none;">تعدیلات مربوط به سرمایه در گردش:</td>
                            </tr>
                            <tr class="row-main">
                                <td style="padding-right: 20px;">تغییر در حساب‌های دریافتنی (Account Receivables)</td>
                                <td class="text-left {{ $adjustments['receivables'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $adjustments['receivables'] > 0 ? '+' : '' }}{{ number_format($adjustments['receivables'], 2) }}
                                </td>
                            </tr>
                            <tr class="row-main">
                                <td style="padding-right: 20px;">تغییر در موجودی کالا (Inventory)</td>
                                <td class="text-left {{ $adjustments['inventory'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $adjustments['inventory'] > 0 ? '+' : '' }}{{ number_format($adjustments['inventory'], 2) }}
                                </td>
                            </tr>
                            <tr class="row-main">
                                <td style="padding-right: 20px;">تغییر در حساب‌های پرداختنی (Account Payables)</td>
                                <td class="text-left {{ $adjustments['payables'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $adjustments['payables'] > 0 ? '+' : '' }}{{ number_format($adjustments['payables'], 2) }}
                                </td>
                            </tr>
                            @if(isset($adjustments['other']) && abs($adjustments['other']) > 0.001)
                            <tr class="row-main">
                                <td style="padding-right: 20px;">سایر دارایی‌ها و بدهی‌های عملیاتی (Other Operating Items)</td>
                                <td class="text-left {{ $adjustments['other'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $adjustments['other'] > 0 ? '+' : '' }}{{ number_format($adjustments['other'], 2) }}
                                </td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td>خالص جریان نقد از فعالیت‌های عملیاتی</td>
                                <td class="text-left text-info" style="direction: ltr;">{{ number_format($net_cash_operating, 2) }}</td>
                            </tr>

                            <!-- SPACER -->
                            <tr><td colspan="2" style="border: none; height: 10px;"></td></tr>

                            <!-- INVESTING ACTIVITIES -->
                            <tr class="section-header-warning">
                                <td colspan="2" style="color: #c28d05 !important;">۲. فعالیت‌های سرمایه‌گذاری (Investing Activities)</td>
                            </tr>
                            <tr class="row-main">
                                <td>تغییر در دارایی‌های ثابت (Fixed Assets)</td>
                                <td class="text-left {{ $investing['fixed_assets'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $investing['fixed_assets'] > 0 ? '+' : '' }}{{ number_format($investing['fixed_assets'], 2) }}
                                </td>
                            </tr>
                            @if(isset($investing['other']) && abs($investing['other']) > 0.001)
                            <tr class="row-main">
                                <td>سایر فعالیت‌های سرمایه‌گذاری (Other Investing Items)</td>
                                <td class="text-left {{ $investing['other'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $investing['other'] > 0 ? '+' : '' }}{{ number_format($investing['other'], 2) }}
                                </td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td>خالص جریان نقد از فعالیت‌های سرمایه‌گذاری</td>
                                <td class="text-left" style="color: #c28d05; direction: ltr;">{{ number_format($net_cash_investing, 2) }}</td>
                            </tr>

                            <!-- SPACER -->
                            <tr><td colspan="2" style="border: none; height: 10px;"></td></tr>

                            <!-- FINANCING ACTIVITIES -->
                            <tr class="section-header-success">
                                <td colspan="2" style="color: #2e7d32 !important;">۳. فعالیت‌های تأمین مالی (Financing Activities)</td>
                            </tr>
                            <tr class="row-main">
                                <td>تغییر در حقوق مالکانه (Equity / Investment)</td>
                                <td class="text-left {{ $financing['equity'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $financing['equity'] > 0 ? '+' : '' }}{{ number_format($financing['equity'], 2) }}
                                </td>
                            </tr>
                            @if(isset($financing['retained_earnings']) && abs($financing['retained_earnings']) > 0.001)
                            <tr class="row-main">
                                <td>تعدیلات مستقیم حقوق مالکان / سود انباشته</td>
                                <td class="text-left {{ $financing['retained_earnings'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $financing['retained_earnings'] > 0 ? '+' : '' }}{{ number_format($financing['retained_earnings'], 2) }}
                                </td>
                            </tr>
                            @endif
                            @if(isset($financing['other']) && abs($financing['other']) > 0.001)
                            <tr class="row-main">
                                <td>سایر فعالیت‌های تأمین مالی (Other Financing Items)</td>
                                <td class="text-left {{ $financing['other'] < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                    {{ $financing['other'] > 0 ? '+' : '' }}{{ number_format($financing['other'], 2) }}
                                </td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td>خالص جریان نقد از فعالیت‌های تأمین مالی</td>
                                <td class="text-left text-success" style="direction: ltr;">{{ number_format($net_cash_financing, 2) }}</td>
                            </tr>

                            <!-- SPACER -->
                            <tr><td colspan="2" style="border: none; height: 15px;"></td></tr>

                            <!-- NET CHANGE IN CASH -->
                            <tr class="net-change">
                                <td>خالص تغییر در نقدینگی (Net Change in Cash and Equivalents)</td>
                                <td class="text-left" style="direction: ltr;">{{ number_format($net_change_in_cash, 2) }} USD</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="reconciliation-table">
                        <tr>
                            <td colspan="2" class="reconciliation-header">تطبیق نقدینگی با دفتر کل (Cash Reconciliation)</td>
                        </tr>
                        <tr>
                            <td>موجودی نقد اول دوره (Beginning Cash)</td>
                            <td class="text-left font-weight-bold" style="direction: ltr;">{{ number_format($beginning_cash, 2) }}</td>
                        </tr>
                        <tr>
                            <td>خالص تغییر در نقدینگی محاسباتی (Calculated Net Cash Flow)</td>
                            <td class="text-left font-weight-bold {{ $net_change_in_cash < 0 ? 'text-danger' : 'text-success' }}" style="direction: ltr;">
                                {{ $net_change_in_cash > 0 ? '+' : '' }}{{ number_format($net_change_in_cash, 2) }}
                            </td>
                        </tr>
                        <tr style="background: #e1f5fe;">
                            <td class="font-weight-bold">مانده نقد پایانی محاسباتی (Calculated Ending Cash)</td>
                            <td class="text-left font-weight-bold text-info" style="direction: ltr;">{{ number_format($beginning_cash + $net_change_in_cash, 2) }}</td>
                        </tr>
                        <tr style="background: #f1f8e9;">
                            <td class="font-weight-bold">مانده نقد پایانی واقعی در دفتر کل (Actual Ledger Ending Cash)</td>
                            <td class="text-left font-weight-bold text-success" style="direction: ltr;">{{ number_format($ending_cash, 2) }}</td>
                        </tr>
                    </table>

                    @if(!$reconciled)
                    <p class="text-danger font-weight-bold text-center" style="margin-top: 10px; font-size: 9pt;">
                        مغایرت محاسباتی: اختلاف {{ number_format(abs(($beginning_cash + $net_change_in_cash) - $ending_cash), 2) }} در تطبیق نقدینگی مشاهده شده است.
                    </p>
                    @endif

                </div>
            </td>
        </tr>
    </tbody>
    <tfoot><tr><td style="border: none; padding: 0;"><div class="footer-space"></div></td></tr></tfoot>
</table>

</body>
</html>
