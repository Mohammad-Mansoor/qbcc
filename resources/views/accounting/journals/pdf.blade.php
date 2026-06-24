<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>Voucher - {{ $transaction->reference }}</title>
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

        .header-space {
            height: 120px;
        }

        .footer-space {
            height: 130px;
        }

        .content-wrapper {
            padding-left: 2mm;
            padding-right: 2mm;
        }

        .title-block {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .title-main {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
        }

        .title-sub {
            font-size: 12pt;
            color: #475569;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .meta-table td {
            padding: 12px;
            vertical-align: top;
        }

        .meta-label {
            font-size: 10pt;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
            display: block;
        }

        .meta-val-primary {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
        }

        .description-box {
            border-right: 4px solid #3b82f6;
            background: #f1f5f9;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 11pt;
            line-height: 1.6;
            color: #1e293b;
        }

        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 10pt;
            padding: 10px 8px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            font-size: 10pt;
            color: #0f172a;
        }

        table.ledger-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        table.ledger-table tfoot td {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            border-top: 2px solid #1e3a8a;
            font-size: 11pt;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .signatures {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .signature-box {
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #0f172a;
            padding-top: 8px;
            font-weight: bold;
            font-size: 11pt;
            color: #0f172a;
        }

        .signature-sub {
            font-size: 9pt;
            color: #64748b;
        }

        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            table.ledger-table tfoot {
                display: table-row-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="fixed-header">
        @if(isset($topHeaderBase64) && $topHeaderBase64)
            <img src="{{ $topHeaderBase64 }}" alt="Header">
        @endif
    </div>

    <div class="fixed-footer">
        @if(isset($bottomFooterBase64) && $bottomFooterBase64)
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
                            <h2 class="title-main">شرکت صنعتی برادران قاسمی</h2>
                            <p class="title-sub" style="margin-bottom: 10px;">تولید و صادر کننده انواع مختلف قالین و
                                گیلم های دست بافت افغانستان</p>

                            <div
                                style="display: inline-block; background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 6px 20px; border-radius: 20px; color: #1d4ed8; font-weight: bold; font-size: 14pt; margin-top: 5px;">
                                @php
                                    $titles = [
                                        'payment' => 'سند تادیاتی (Payment Voucher)',
                                        'receipt' => 'سند رسید (Receipt Voucher)',
                                        'journal' => 'سند روزنامچه (Journal Voucher)',
                                        'sales' => 'سند فروشات (Sales Voucher)',
                                        'adjustment' => 'سند تعدیلی (Adjustment Voucher)',
                                    ];
                                    echo $titles[$transaction->journal_type] ?? 'سند حسابداری (Accounting Voucher)';
                                @endphp
                            </div>
                        </div>

                        <table class="meta-table">
                            <tr>
                                <td style="width: 33%; text-align: center; border-left: 1px solid #e2e8f0;">
                                    <span class="meta-label">شناسه سند (GL ID)</span>
                                    <p class="meta-val-primary">{{ $transaction->journal_id ?? $transaction->id }}</p>
                                </td>
                                <td style="width: 33%; text-align: center; border-left: 1px solid #e2e8f0;">
                                    <span class="meta-label">نمبر سند (Ref)</span>
                                    <p class="meta-val-primary" style="color: #0369a1;">
                                        {{ $transaction->reference ?? '-' }}</p>
                                </td>
                                <td style="width: 33%; text-align: center;">
                                    <span class="meta-label">تاریخ صدور (Date)</span>
                                    <p class="meta-val-primary" style="direction: ltr;">{{ $transaction->date }}</p>
                                </td>
                            </tr>
                        </table>

                        <div class="description-box">
                            <span
                                style="font-weight: bold; color: #1e3a8a; display: block; margin-bottom: 5px; font-size: 10pt;">شرح
                                سند (Narrative):</span>
                            {{ $transaction->description }}
                        </div>

                        <table class="ledger-table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">کد حساب</th>
                                    <th style="width: 45%;">نام حساب (Account Name)</th>
                                    <th style="width: 20%;">دیبت (Debit)</th>
                                    <th style="width: 20%;">کریدت (Credit)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->entries as $entry)
                                    <tr>
                                        <td class="text-center font-bold" style="color: #475569;">
                                            {{ $entry->account->account_code }}</td>
                                        <td class="text-right font-bold">{{ $entry->account->account_name }}</td>
                                        <td class="text-left font-bold" style="direction: ltr;">
                                            @if($entry->debit > 0)
                                                {{ number_format($entry->debit, 2) }} <small
                                                    style="color: #64748b;">{{ $entry->currency_code }}</small>
                                                @if($entry->currency_code !== 'USD')
                                                    <br><small
                                                        style="color: #94a3b8; font-size: 8.5pt;">(${{ number_format($entry->base_debit, 2) }})</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-left font-bold" style="direction: ltr;">
                                            @if($entry->credit > 0)
                                                {{ number_format($entry->credit, 2) }} <small
                                                    style="color: #64748b;">{{ $entry->currency_code }}</small>
                                                @if($entry->currency_code !== 'USD')
                                                    <br><small
                                                        style="color: #94a3b8; font-size: 8.5pt;">(${{ number_format($entry->base_credit, 2) }})</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-center">مجموع نهایی (Total Base Amount in USD)</td>
                                    <td class="text-left font-bold" style="direction: ltr; color: #1e3a8a;">
                                        ${{ number_format($transaction->entries->sum('base_debit'), 2) }}</td>
                                    <td class="text-left font-bold" style="direction: ltr; color: #1e3a8a;">
                                        ${{ number_format($transaction->entries->sum('base_credit'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="signatures">
                            <div class="signature-box">
                                <div class="signature-line">ترتیب کننده</div>
                                <div class="signature-sub">(Prepared By)</div>
                            </div>
                            <div class="signature-box">
                                <div class="signature-line">مدیریت مالی</div>
                                <div class="signature-sub">(Checked By)</div>
                            </div>
                            <div class="signature-box">
                                <div class="signature-line">تایید نهایی</div>
                                <div class="signature-sub">(Approved By)</div>
                            </div>
                        </div>

                        <div style="margin-top: 40px; font-size: 9pt; text-align: right; color: #94a3b8;" dir="ltr">
                            Generated by QBIC ERP System on {{ date('Y-m-d H:i:s') }}
                        </div>
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