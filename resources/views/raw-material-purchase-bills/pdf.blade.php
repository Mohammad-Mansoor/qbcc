<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>Raw Material Bill - {{ $bill->bill_number }}</title>
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
            height: 90px;
        }

        .footer-space {
            height: 90px;
        }

        .content-wrapper {
            padding-left: 2mm;
            padding-right: 2mm;
        }

        .title-block {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 5px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .title-main {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
        }

        .title-sub {
            font-size: 10pt;
            color: #475569;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 5px;
            background: #f8fafc;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .meta-table td {
            padding: 4px;
            vertical-align: top;
        }

        .meta-label {
            font-size: 8pt;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 2px;
            display: block;
        }

        .meta-val-primary {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }

        .billing-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px;
            margin-bottom: 5px;
        }

        .billing-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .billing-details {
            font-size: 8pt;
            color: #334155;
            line-height: 1.4;
        }

        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 8pt;
            padding: 4px 2px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 2px;
            font-size: 8pt;
            color: #0f172a;
        }

        table.ledger-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .totals-table td {
            padding: 4px 5px;
            font-size: 9pt;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
        }

        .totals-table tr.grand-total td {
            font-size: 11pt;
            font-weight: bold;
            color: #2563eb !important;
            background-color: #eff6ff !important;
            border-top: 2px solid #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
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
            margin-top: 50px;
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
            font-size: 10pt;
            color: #0f172a;
        }

        .signature-sub {
            font-size: 8.5pt;
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
                            <h2 class="title-main">{{ config('company.name') }}</h2>
                            <div style="font-size: 8pt; color: #475569; margin-bottom: 2px;">{{ config('company.description') }}</div>
                            <div
                                style="display: inline-block; background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 15px; border-radius: 10px; color: #1d4ed8; font-weight: bold; font-size: 12pt; margin-top: 2px;">
                                بل خرید مواد خام
                            </div>
                        </div>

                        <table class="meta-table">
                            <tr>
                                <td style="width: 33%; text-align: center; border-left: 1px solid #e2e8f0;">
                                    <span class="meta-label">نمبر فاکتور / بل</span>
                                    <p class="meta-val-primary"
                                        style="font-family: monospace; color: #2563eb; direction: ltr;">
                                        {{ $bill->bill_number }}</p>
                                </td>
                                <td style="width: 33%; text-align: center; border-left: 1px solid #e2e8f0;">
                                    <span class="meta-label">وضعیت بل</span>
                                    <p class="meta-val-primary"
                                        style="color: {{ $bill->status == 'open' ? '#16a34a' : '#dc2626' }};">
                                        {{ $bill->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
                                    </p>
                                </td>
                                <td style="width: 33%; text-align: center;">
                                    <span class="meta-label">تاریخ صدور (Date)</span>
                                    <p class="meta-val-primary" style="direction: ltr;">
                                        {{ \Carbon\Carbon::parse($bill->date)->format('d M Y') }}</p>
                                </td>
                            </tr>
                        </table>

                        <table style="width: 100%; margin-bottom: 20px;">
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                                    <div class="billing-section">
                                        <div class="billing-title">مشخصات فروشنده مواد خام</div>
                                        <div class="billing-details">
                                            <strong>نام تامین کننده:</strong> {{ $bill->seller->name ?? 'N/A' }}<br>
                                            <strong>شماره تماس:</strong> {{ $bill->seller->phone ?? 'N/A' }}<br>
                                            <strong>آدرس:</strong> {{ $bill->seller->address ?? 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                                    <div class="billing-section">
                                        <div class="billing-title">مشخصات تحویل‌گیرنده</div>
                                        <div class="billing-details">
                                            <strong>نام سازمان:</strong> دفتر مرکزی QBIC<br>
                                            <strong>بخش تحویل‌گیرنده:</strong> مدیریت انبار الیاف و رنگ<br>
                                            <strong>سیستم مالی:</strong> حسابداری دوبانده (Double Entry Ledger)
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table class="ledger-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%">ردیف</th>
                                    <th style="width: 15%">تاریخ</th>
                                    <th style="width: 20%">نوعیت مواد</th>
                                    <th style="width: 15%">دسته‌بندی</th>
                                    <th style="width: 15%">گدام</th>
                                    <th style="width: 10%">مقدار (KG)</th>
                                    <th style="width: 10%">نرخ فی کیلو (ارز خرید)</th>
                                    <th style="width: 10%">مجموع (Base USD)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $index => $purchase)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $purchase->purchase_date }}</td>
                                        <td class="font-bold">{{ $purchase->materialType->material_type ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $purchase->materialCategory->material_category ?? '—' }}
                                        </td>
                                        <td class="text-center">{{ $purchase->warehouse->name ?? 'N/A' }}</td>
                                        <td class="text-center font-bold" style="direction: ltr;">
                                            {{ number_format($purchase->quantity, 2) }} kg</td>
                                        <td class="text-center" style="direction: ltr;">
                                            {{ number_format($purchase->price_per_kilo, 2) }} {{ $purchase->currency_code ?? 'USD' }}</td>
                                        <td class="text-center font-bold text-dark" style="direction: ltr;">
                                            ${{ number_format($purchase->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center" style="color: #64748b;">هیچ خرید مواد خامی به
                                            این بل متصل نشده است.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;"></td>
                                <td style="width: 50%;">
                                    <table class="totals-table text-right">
                                        <tr>
                                            <td class="text-left font-bold" style="direction: ltr;">
                                                {{ number_format($purchases->sum('quantity'), 2) }} kg</td>
                                            <td class="font-bold" style="color: #64748b;">مجموع کل وزن:</td>
                                        </tr>
                                        <tr class="grand-total">
                                            <td class="text-left font-bold" style="direction: ltr;">
                                                ${{ number_format($bill->total_amount, 2) }}</td>
                                            <td class="font-bold">مبلغ کل قابل تادیه (USD):</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left font-bold" style="direction: ltr; color: #16a34a;">
                                                ${{ number_format($bill->paid_amount, 2) }}</td>
                                            <td class="font-bold" style="color: #64748b;">مجموع پرداخت شده:</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left font-bold" style="direction: ltr; color: #dc2626;">
                                                ${{ number_format($bill->remaining_balance, 2) }}</td>
                                            <td class="font-bold" style="color: #64748b;">باقیمانده:</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        @if($bill->allocations && $bill->allocations->count() > 0)
                        <div style="margin-top: 30px; border-top: 2px solid #e2e8f0; padding-top: 20px;">
                            <h4 style="font-size: 12pt; color: #0f172a; margin-bottom: 10px;">تاریخچه تادیات و
                                پرداخت‌های بل خرید (Payment History)</h4>
                            <table class="ledger-table">
                                <thead>
                                    <tr>
                                        <th>تاریخ پرداخت</th>
                                        <th>توضیحات تراکنش</th>
                                        <th>نوعیت پرداخت</th>
                                        <th>مقدار پرداختی ارز اصلی</th>
                                        <th>نرخ تسعیر</th>
                                        <th>معادل دالر (USD Amount)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bill->allocations as $pay)
                                    @php($sp = $pay->seller_payment)
                                    @if($sp)
                                        <tr>
                                            <td class="text-center">{{ $sp->date }}</td>
                                            <td>{{ $sp->description }}</td>
                                            <td class="text-center">
                                                {{ $sp->type == 'رسید' ? 'رسید (Inflow)' : 'گرفت (Outflow)' }}
                                            </td>
                                            <td class="text-center" style="direction: ltr;">
                                                {{ number_format($pay->allocated_amount, 2) }} {{ $sp->currency_code }}</td>
                                            <td class="text-center" style="direction: ltr;">
                                                {{ number_format($pay->exchange_rate, 8) }}</td>
                                            <td class="text-center font-bold" style="direction: ltr; color: #166534;">
                                                ${{ number_format($pay->base_allocated_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

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