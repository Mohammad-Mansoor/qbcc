<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>انوایس فروش - {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
            /* Let the header/footer span the entire width */
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 11px;
            direction: rtl;
        }

        .header-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .footer-img {
            width: 100%;
            height: auto;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            padding-bottom: 120px;
            /* Space for footer */
        }

        .report-title {
            text-align: left;
            direction: ltr;
        }

        .report-title h2 {
            font-size: 28px;
            color: #0056b3;
            margin: 0;
            letter-spacing: 2px;
        }

        .report-title p {
            font-size: 14px;
            margin: 5px 0 0 0;
            color: #555;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            margin: 5px;
            min-height: 100px;
        }

        .info-box h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #0056b3;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 12px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            text-align: center;
        }

        .data-table th {
            background-color: #0056b3 !important;
            color: #fff !important;
            font-size: 12px;
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .summary-box {
            float: left;
            width: 40%;
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            text-align: right;
            font-size: 12px;
            color: #555;
        }

        .summary-value {
            display: table-cell;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
            direction: ltr;
        }

        .summary-total {
            border-top: 1px solid #ccc;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 15px !important;
            color: #0056b3;
        }

        .signatures {
            width: 100%;
            margin-top: 80px;
            table-layout: fixed;
        }

        .signatures td {
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            width: 60%;
            border-top: 1px solid #000;
            margin: 0 auto;
            padding-top: 5px;
            font-weight: bold;
            color: #555;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body onload="window.print();">
    @if(isset($headerBase64) && $headerBase64)
        <img src="data:image/png;base64,{{ $headerBase64 }}" class="header-img" alt="Header">
    @endif

    <div
        style="text-align: center; padding: 10px 20px 20px 20px; border-bottom: 2px solid #0056b3; margin-bottom: 20px;">
        <h1 style="color: #0056b3; margin: 0; font-size: 24px;">شرکت صنعتی برادران قاسمی</h1>
        <p style="color: #666; margin: 5px 0 0 0; font-size: 12px; font-weight: bold;">تولید و صادر کننده انواع مختلف
            قالین و گیلم های دست بافت افغانستان</p>
    </div>

    <div class="container">
        <!-- Header Section -->
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 60%; text-align: right;">
                    <h3 style="margin: 0; color: #333;">رسید فروش / انوایس</h3>
                </td>
                <td style="width: 40%;" class="report-title">
                    <h2>INVOICE</h2>
                    <p>No: #{{ $invoice->invoice_no }}</p>
                    <p>Date: {{ $invoice->invoice_date }}</p>
                </td>
            </tr>
        </table>

        <!-- Info Boxes -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-box">
                        <h4>مشخصات خریدار / مشتری (Billed To)</h4>
                        @if($invoice->type === 'carpet')
                            <p class="font-weight-bold" style="font-size: 14px;">{{ $invoice->customer->name ?? '---' }}</p>
                            <p>شرکت: {{ $invoice->customer->company_name ?? '---' }}</p>
                            <p>آدرس: {{ $invoice->customer->company_address ?? '---' }}</p>
                            <p>شماره تماس: {{ $invoice->customer->phone ?? '---' }}</p>
                        @else
                            <p class="font-weight-bold" style="font-size: 14px;">
                                {{ $invoice->agent->user->name ?? $invoice->agent->name ?? '---' }}</p>
                            <p>شماره حساب: {{ $invoice->agent->account_no ?? '---' }}</p>
                            @php
                                $agentPhone = '---';
                                if(isset($invoice->agent->phone) && is_iterable($invoice->agent->phone) && count($invoice->agent->phone) > 0) {
                                    $firstPhone = collect($invoice->agent->phone)->first();
                                    $agentPhone = $firstPhone['phone_no'] ?? $firstPhone->phone_no ?? '---';
                                } elseif (is_string($invoice->agent->phone)) {
                                    // Fallback if it's stored as a simple string
                                    $agentPhone = $invoice->agent->phone;
                                }
                            @endphp
                            <p>شماره تماس: {{ $agentPhone }}</p>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="info-box">
                        <h4>جزئیات سیستم (System Details)</h4>
                        <p>نوعیت فروش:
                            @if($invoice->type === 'carpet') قالین (Carpet)
                            @elseif($invoice->type === 'dye') رنگ (Dye)
                            @else نخ (Yarn)
                            @endif
                        </p>
                        <p>حالت انوایس:
                            @if($invoice->status === 'closed') <span style="color: #dc3545;">بسته شده (Closed)</span>
                            @else <span style="color: #28a745;">باز (Open)</span>
                            @endif
                        </p>
                        <p>توضیحات: {{ $invoice->invoice_description ?: '---' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="data-table">
            <thead>
                @if($invoice->type === 'carpet')
                    <tr>
                        <th>ردیف</th>
                        <th>نمبر قالین</th>
                        <th>نقشه و مشخصات</th>
                        <th>ابعاد (m)</th>
                        <th>مساحت (m²)</th>
                        <th>قیمت واحد ($)</th>
                        <th>قیمت کل ($)</th>
                    </tr>
                @else
                    <tr>
                        <th>ردیف</th>
                        <th>دسته بندی</th>
                        <th>نوعیت مواد</th>
                        <th>مقدار (Kg)</th>
                        <th>قیمت فی واحد</th>
                        <th>قیمت کل</th>
                        <th>معادل (USD)</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @if($invoice->type === 'carpet')
                    @foreach($sales as $sale)
                        @if(!$sale->is_returned)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td class="font-weight-bold">{{ $sale->carpet->carpet_no ?? $sale->carpet_no }}</td>
                                <td>
                                    {{ $sale->type }} - {{ $sale->carpet->map_number ?? '---' }}<br>
                                    <span style="font-size: 10px; color: #666;">
                                        کیفیت: {{ $sale->quality }} | رنگ:
                                        {{ $sale->carpet->field ?? '---' }}/{{ $sale->carpet->margin ?? '---' }}
                                    </span>
                                </td>
                                <td dir="ltr">{{ $sale->carpet_height ?? ($sale->carpet->height ?? '---') }} &times;
                                    {{ $sale->carpet_width ?? ($sale->carpet->width ?? '---') }}</td>
                                <td>{{ round($sale->carpet_area ?? ($sale->carpet->area ?? 0), 2) }}</td>
                                <td>${{ number_format($sale->sale_cost_per_meter, 2) }}</td>
                                <td class="font-weight-bold">${{ number_format($sale->sale_cost_total, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    @foreach($sales as $material)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td class="font-weight-bold">{{ optional($material->category)->material_category }}</td>
                            <td>{{ optional($material->type)->material_type }}</td>
                            <td>{{ $material->amount }}</td>
                            <td>{{ number_format($material->price, 2) }} {{ $material->currency_code }}</td>
                            <td>{{ number_format($material->original_amount, 2) }} {{ $material->currency_code }}</td>
                            <td class="font-weight-bold">${{ number_format($material->base_currency_amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Summary & Totals -->
        <div class="clearfix">
            <div style="float: right; width: 55%; margin-top: 20px;">
                <p style="font-size: 11px; color: #555; line-height: 1.6;">
                    <strong>شرایط و ضوابط:</strong><br>
                    ۱. این انوایس به صورت سیستمی تولید شده و معتبر می‌باشد.<br>
                    ۲. خریدار موظف است در صورت هرگونه مغایرت ظرف مدت ۲۴ ساعت به بخش مالی اطلاع دهد.<br>
                    ۳. در صورت بروزرسانی یا تغییرات، نسخه جدید جایگزین این انوایس خواهد شد.
                </p>
            </div>

            <div class="summary-box">
                @php
                    if ($invoice->type === 'carpet') {
                        $totalPaid = $invoice->payments->sum('amount_applied');
                        $totalDue = $invoice->sale->where('is_returned', 0)->sum('sale_cost_total');
                    } else {
                        $totalPaid = $invoice->paid_amount;
                        $totalDue = $invoice->material_sales->sum('base_currency_amount');
                    }
                    $balance = $totalDue - $totalPaid;
                @endphp

                <div class="summary-row">
                    <div class="summary-label">مجموع کل (Total Due):</div>
                    <div class="summary-value">${{ number_format($totalDue, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">مبلغ پرداختی (Total Paid):</div>
                    <div class="summary-value" style="color: #28a745;">${{ number_format($totalPaid, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label summary-total">باقیمانده (Balance):</div>
                    <div class="summary-value summary-total"
                        style="{{ $balance > 0 ? 'color: #dc3545;' : 'color: #0056b3;' }}">
                        ${{ number_format($balance, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <table class="signatures">
            <tr>
                <td>
                    <div class="signature-line">امضای تحویل دهنده (Issued By)</div>
                </td>
                <td>
                    <div class="signature-line">مهر و امضای فروشگاه (Stamp)</div>
                </td>
                <td>
                    <div class="signature-line">امضای مشتری (Received By)</div>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #888; margin-bottom: 20px;">
            چاپ شده توسط سیستم QBCC ERP - {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}
        </div>
    </div>

    @if(isset($footerBase64) && $footerBase64)
        <img src="data:image/png;base64,{{ $footerBase64 }}" class="footer-img" alt="Footer">
    @endif
</body>

</html>