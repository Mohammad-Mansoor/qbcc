<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>بل خرید - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 6mm;
        }

        body {
            font-family: 'Tahoma', Arial, sans-serif;
            background-color: #fff;
            color: #1e293b;
            font-size: 8pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        /* Suppress URL links printed by browser */
        @media print {
            a[href]::after {
                content: none !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Header ── */
        .report-header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 5px;
            margin-bottom: 5px;
            display: table;
        }

        .header-left,
        .header-center,
        .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 30%;
        }

        .header-center {
            width: 40%;
            text-align: center;
        }

        .header-right {
            width: 30%;
            text-align: left;
        }

        .company-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }

        .report-title-badge {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 12px;
            color: #1d4ed8;
            font-weight: bold;
            font-size: 9pt;
            display: inline-block;
        }

        /* ── Meta row ── */
        table.meta-table {
            width: 100%;
            margin-bottom: 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        }

        table.meta-table td {
            padding: 4px 8px;
            vertical-align: middle;
            border-left: 1px solid #e2e8f0;
            font-size: 7.5pt;
        }

        table.meta-table td:last-child {
            border-left: none;
        }

        .meta-label {
            font-weight: bold;
            color: #0f172a;
            display: block;
            margin-bottom: 1px;
        }

        .meta-val {
            color: #334155;
        }

        /* ── Data table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
        }

        table.data-table th {
            background-color: #1e3a8a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 7.5pt;
            border: 1px solid #94a3b8;
            padding: 4px 3px;
            text-align: center;
        }

        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 2px 3px;
            font-size: 7pt;
            text-align: right;
            vertical-align: middle;
            line-height: 1.2;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Totals table (separate element — only renders once after data) */
        table.totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-top: 2px solid #1e3a8a;
            page-break-inside: avoid;
        }

        table.totals-table td {
            background-color: #e7edf8 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-weight: bold;
            font-size: 7.5pt;
            border: 1px solid #94a3b8;
            padding: 3px 4px;
            text-align: center;
        }

        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            text-align: center;
            width: 100%;
        }

        .signature-box {
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #0f172a;
            padding-top: 8px;
            font-weight: bold;
            font-size: 9pt;
            color: #0f172a;
            margin-top: 40px;
        }
    </style>
</head>

<body onload="window.print()">

    @php
        $sum_area = $carpets->sum('area');
        $sum_purchase = $carpets->sum('total_price');
    @endphp

    <!-- Header -->
    <div class="report-header">
        <div class="header-left">
            <table style="border:none; width:auto;">
                <tr>
                    <td style="border:none; padding:0; vertical-align:middle; width:45px;">
                        @if(isset($logoBase64) && $logoBase64)
                            <img src="{{ $logoBase64 }}" style="height:38px; width:auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="border:none; padding:0 0 0 7px; vertical-align:middle;">
                        <p class="company-name">شرکت صنعتی برادران قاسمی</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-center">
            <div class="report-title-badge">بل خرید قالین — {{ $invoice->invoice_number }}</div>
        </div>
        <div class="header-right">
            <span style="font-size:7.5pt; color:#475569; direction:ltr; display:block;">{{ $issueDate }}</span>
            <span style="font-size:7.5pt; color:#1d4ed8; font-weight:bold;">{{ number_format($carpets->count()) }} تخته
                &nbsp;|&nbsp; {{ number_format($sum_area, 2) }} m²</span>
        </div>
    </div>

    <!-- Filter meta row -->
    <table class="meta-table">
        <tr>
            <td style="width:25%;">
                <span class="meta-label">شماره بل</span>
                <span class="meta-val"
                    style="direction: ltr; font-family: monospace; color: #2563eb;">{{ $invoice->invoice_number }}</span>
            </td>
            <td style="width:25%;">
                <span class="meta-label">نماینده / فروشنده</span>
                <span class="meta-val">{{ $invoice->agent->user->name ?? '---' }}</span>
            </td>
            <td style="width:25%;">
                <span class="meta-label">تاریخ بل</span>
                <span class="meta-val" style="direction: ltr;">{{ $invoice->date }}</span>
            </td>
            <td style="width:25%;">
                <span class="meta-label">وضعیت</span>
                <span class="meta-val">{{ $invoice->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}</span>
            </td>
        </tr>
    </table>

    <!-- Data table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%;">ردیف</th>
                <th style="width:10%;">شماره پارچه</th>
                <th style="width:15%;">نوعیت</th>
                <th style="width:15%;">کوالیتی</th>
                <th style="width:10%;">نقشه</th>
                <th style="width:8%;">طول</th>
                <th style="width:8%;">عرض</th>
                <th style="width:9%;">مساحت</th>
                <th style="width:10%;">قیمت فی متر ($)</th>
                <th style="width:10%;">مجموع قیمت ($)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carpets as $index => $carpet)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold" style="color:#1d4ed8; text-align: center;">{{ $carpet->carpet_no }}</td>
                    <td class="text-center">{{ $carpet->type->carpet_type ?? '-' }}</td>
                    <td class="text-center">{{ $carpet->quality->quality ?? '-' }}</td>
                    <td class="text-center">{{ $carpet->map_number ?? '-' }}</td>
                    <td class="text-center" style="direction:ltr;">{{ $carpet->height }}</td>
                    <td class="text-center" style="direction:ltr;">{{ $carpet->width }}</td>
                    <td class="text-center font-bold" style="direction:ltr;">{{ number_format($carpet->area, 2) }}</td>
                    <td class="text-center" style="direction:ltr;">
                        ${{ number_format($carpet->area > 0 ? $carpet->total_price / $carpet->area : 0, 2) }}</td>
                    <td class="text-center font-bold" style="direction:ltr; color:#059669;">
                        ${{ number_format($carpet->total_price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding:10px;">هیچ قالینی یافت نشد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals: separate table --}}
    @if($carpets->count() > 0)
        <table class="totals-table">
            <tbody>
                <tr>
                    <td colspan="7" style="text-align:right; width:71%;">مجموع کلی (Grand Total) —
                        {{ number_format($carpets->count()) }} تخته</td>
                    <td style="direction:ltr; width:9%;">{{ number_format($sum_area, 2) }}</td>
                    <td style="width:10%;"></td>
                    <td style="direction:ltr; width:10%; color:#059669;">${{ number_format($sum_purchase, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <table style="width: 100%; margin-top: 15px;">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if($invoice->allocations && $invoice->allocations->count() > 0)
                <div style="padding-left: 10px;">
                    <h5 style="font-size: 8pt; color: #0f172a; margin: 0 0 4px 0;">تاریخچه تادیات و پرداخت‌ها (Payment
                        History)</h5>
                    <table class="data-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="background-color: #475569 !important;">تاریخ</th>
                                <th style="background-color: #475569 !important;">بابت</th>
                                <th style="background-color: #475569 !important;">مبلغ اصلی</th>
                                <th style="background-color: #475569 !important;">معادل ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->allocations as $pay)
                            @php($ap = $pay->agent_payment)
                            @if($ap)
                                <tr>
                                    <td class="text-center">{{ $ap->date }}</td>
                                    <td>سند #: {{ $ap->check_number }} - {{ $ap->description }}</td>
                                    <td class="text-center" style="direction: ltr;">
                                        {{ number_format($pay->allocated_amount, 2) }} {{ $ap->currency_code }}</td>
                                    <td class="text-center font-bold" style="direction: ltr; color: #166534;">
                                        ${{ number_format($pay->base_allocated_amount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="data-table text-right" style="border: none;">
                    <tr>
                        <td class="text-left font-bold"
                            style="direction: ltr; border: none; border-bottom: 1px solid #e2e8f0; background: transparent !important;">
                            {{ $carpets->count() }} تخته</td>
                        <td class="font-bold"
                            style="border: none; border-bottom: 1px solid #e2e8f0; color: #64748b; background: transparent !important;">
                            مجموع تعداد (Quantity):</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold"
                            style="direction: ltr; border: none; border-bottom: 1px solid #e2e8f0; background: transparent !important;">
                            {{ number_format($sum_area, 2) }} m²</td>
                        <td class="font-bold"
                            style="border: none; border-bottom: 1px solid #e2e8f0; color: #64748b; background: transparent !important;">
                            مجموع مساحت (Total Area):</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold"
                            style="direction: ltr; border: none; border-top: 1px solid #1e3a8a; border-bottom: 1px solid #1e3a8a; background: #eff6ff !important; color: #2563eb !important; font-size: 9.5pt;">
                            ${{ number_format($sum_purchase, 2) }}</td>
                        <td class="font-bold"
                            style="border: none; border-top: 1px solid #1e3a8a; border-bottom: 1px solid #1e3a8a; background: #eff6ff !important; color: #2563eb !important; font-size: 9.5pt;">
                            مبلغ کل قابل تادیه (Grand Total USD):</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold"
                            style="direction: ltr; border: none; border-bottom: 1px solid #e2e8f0; background: transparent !important; color: #16a34a;">
                            ${{ number_format($invoice->paid_amount, 2) }}</td>
                        <td class="font-bold"
                            style="border: none; border-bottom: 1px solid #e2e8f0; color: #64748b; background: transparent !important;">
                            مجموع پرداخت شده (Total Paid USD):</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold"
                            style="direction: ltr; border: none; border-bottom: 1px solid #e2e8f0; background: transparent !important; color: #dc2626;">
                            ${{ number_format($invoice->remaining_balance, 2) }}</td>
                        <td class="font-bold"
                            style="border: none; border-bottom: 1px solid #e2e8f0; color: #64748b; background: transparent !important;">
                            باقیمانده (Remaining Balance USD):</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold"
                            style="direction: ltr; border: none; border-bottom: 1px solid #e2e8f0; background: transparent !important;">
                            @if($invoice->payment_status === 'paid')
                                تصفیه شده (Paid)
                            @elseif($invoice->payment_status === 'partially_paid')
                                تادیه قسمتی (Partially Paid)
                            @else
                                پرداخت نشده (Unpaid)
                            @endif
                        </td>
                        <td class="font-bold"
                            style="border: none; border-bottom: 1px solid #e2e8f0; color: #64748b; background: transparent !important;">
                            وضعیت تصفیه مالی (Payment Status):</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">امضا و تایید تحویل‌دهنده (فروشنده)</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">امضا و تایید مدیر گدام (رسیور)</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">امضای نهایی مدیریت مالی / خزانه</div>
        </div>
    </div>

    <div style="margin-top: 10px; font-size: 7pt; text-align: right; color: #94a3b8;" dir="ltr">
        Generated by QBIC ERP System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>

</html>