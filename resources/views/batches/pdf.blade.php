<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Batch Invoice - {{ $batch->reference_number }}</title>
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

        .header-space { height: 120px; }
        .footer-space { height: 130px; }

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
        .title-main { font-size: 18pt; font-weight: bold; color: #0f172a; margin: 0 0 5px 0; }
        .title-sub { font-size: 12pt; color: #475569; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 12px; vertical-align: top; }
        .meta-label { font-size: 10pt; color: #64748b; font-weight: bold; margin-bottom: 4px; display: block; }
        .meta-val-primary { font-size: 13pt; font-weight: bold; color: #0f172a; margin: 0 0 4px 0; }
        
        .billing-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .billing-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .billing-details {
            font-size: 10pt;
            color: #334155;
            line-height: 1.6;
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
            font-size: 9.5pt;
            padding: 8px 6px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            font-size: 9.5pt;
            color: #0f172a;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .totals-table td {
            padding: 8px 10px;
            font-size: 10.5pt;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
        }
        .totals-table tr.grand-total td {
            font-size: 14pt;
            font-weight: bold;
            color: #2563eb !important;
            background-color: #eff6ff !important;
            border-top: 2px solid #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

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
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            table.ledger-table tfoot { display: table-row-group; }
            tr { page-break-inside: avoid; }
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
                        <p class="title-sub" style="margin-bottom: 10px;">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت افغانستان</p>
                        
                        <div style="display: inline-block; background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 6px 20px; border-radius: 20px; color: #1d4ed8; font-weight: bold; font-size: 14pt; margin-top: 5px;">
                            @if($batch->type == 'kachaee')
                                صورتحساب کچایی (Kachaee Payment Bill)
                            @elseif($batch->type == 'wash')
                                صورتحساب شستشو (Washing Payment Bill)
                            @elseif($batch->type == 'finish')
                                صورتحساب تیاری (Finishing Payment Bill)
                            @else
                                صورتحساب تولید (Production Payment Bill)
                            @endif
                        </div>
                    </div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 33%; text-align: center; border-left: 1px solid #e2e8f0;">
                                <span class="meta-label">نمبر مسلسل</span>
                                <p class="meta-val-primary" style="font-family: monospace; color: #2563eb; direction: ltr;">{{ $batch->reference_number }}</p>
                            </td>
                            <td style="width: 33%; text-align: center; border-left: 1px solid #e2e8f0;">
                                <span class="meta-label">وضعیت</span>
                                <p class="meta-val-primary" style="color: {{ $batch->status == 'open' ? '#16a34a' : '#dc2626' }};">
                                    {{ $batch->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
                                </p>
                            </td>
                            <td style="width: 33%; text-align: center;">
                                <span class="meta-label">تاریخ ایجاد (Date)</span>
                                <p class="meta-val-primary" style="direction: ltr;">{{ $batch->created_at->format('Y-m-d') }}</p>
                            </td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-bottom: 20px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                                <div class="billing-section">
                                    <div class="billing-title">تیم کاری / بخش مربوطه</div>
                                    <div class="billing-details">
                                        @if($team)
                                            <strong>نام تیم:</strong> {{ $team->name }}<br>
                                            <strong>شماره تماس:</strong> {{ $team->phone ?? 'N/A' }}<br>
                                        @else
                                            <strong style="color: #ef4444;">--- ثبت نشده ---</strong><br>
                                        @endif
                                        <strong>نوعیت مرحله:</strong> 
                                        @if($batch->type == 'kachaee')
                                            کچایی (Kachaee)
                                        @elseif($batch->type == 'wash')
                                            شستشو (Washing)
                                        @elseif($batch->type == 'finish')
                                            تیاری (Finishing)
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                                <div class="billing-section">
                                    <div class="billing-title">خلاصه وضعیت کارکرد (Summary)</div>
                                    <div class="billing-details">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>تعداد کل قالین‌ها:</td>
                                                <td class="font-bold text-left" style="direction: ltr;">{{ $carpets->count() }} Pcs</td>
                                            </tr>
                                            <tr>
                                                <td>مجموع مساحت:</td>
                                                <td class="font-bold text-left" style="direction: ltr;">{{ number_format($carpets->sum(function($c) { return $c->carpet->area ?? 0; }), 2) }} m²</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <h4 style="font-size: 12pt; color: #0f172a; margin-bottom: 10px;">لیست قالین‌های شامل این نمبر</h4>
                    <table class="ledger-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">ردیف</th>
                                <th style="width: 15%">نمبر قالین</th>
                                <th style="width: 10%">شماره نقشه</th>
                                <th style="width: 12%">نوعیت</th>
                                <th style="width: 10%">کیفیت</th>
                                <th style="width: 15%">ابعاد (m)</th>
                                <th style="width: 10%">مساحت (m²)</th>
                                @if($batch->type == 'finish')
                                    <th style="width: 10%">کتگوری</th>
                                    <th style="width: 13%">هزینه/m² ($)</th>
                                    <th style="width: 13%">قیمت کل ($)</th>
                                @else
                                    <th style="width: 13%">هزینه/m² ($)</th>
                                    <th style="width: 13%">قیمت کل ($)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carpets as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center font-bold text-primary">{{ $item->carpet->carpet_no ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item->carpet->map_number ?? '---' }}</td>
                                    <td class="text-center">{{ $item->carpet->type->carpet_type ?? '---' }}</td>
                                    <td class="text-center">{{ $item->carpet->quality->quality ?? '---' }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $item->carpet->height ?? '---' }} × {{ $item->carpet->width ?? '---' }}</td>
                                    <td class="text-center font-bold" style="direction: ltr;">{{ number_format($item->carpet->area ?? 0, 2) }}</td>
                                    @php
                                        $area = isset($item->carpet->area) && $item->carpet->area > 0 ? $item->carpet->area : 1;
                                        $price = 0;
                                        if($batch->type == 'finish') $price = $item->price;
                                        elseif($batch->type == 'kachaee') $price = $item->total_price;
                                        elseif($batch->type == 'wash') $price = $item->total_price ?: $item->af_total_price;
                                    @endphp
                                    @if($batch->type == 'finish')
                                        <td class="text-center">{{ $item->category->category ?? '---' }}</td>
                                    @endif
                                    <td class="text-center font-bold" style="direction: ltr;">${{ number_format($price / $area, 2) }}</td>
                                    <td class="text-left font-bold text-dark" style="direction: ltr;">${{ number_format($price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $batch->type == 'finish' ? 10 : 9 }}" class="text-center" style="color: #64748b;">هیچ قالینی ثبت نشده است.</td>
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
                                        <td class="text-left font-bold" style="direction: ltr;">{{ number_format($carpets->sum(function($c) { return $c->carpet->area ?? 0; }), 2) }} m²</td>
                                        <td class="font-bold" style="color: #64748b;">مجموع کل مساحت:</td>
                                    </tr>
                                    <tr class="grand-total">
                                        <td class="text-left font-bold" style="direction: ltr;">${{ number_format($totalCost, 2) }}</td>
                                        <td class="font-bold">مبلغ کل قابل تادیه (USD):</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left font-bold" style="direction: ltr; color: #16a34a;">${{ number_format($totalPaid, 2) }}</td>
                                        <td class="font-bold" style="color: #64748b;">مجموع پرداخت شده:</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left font-bold" style="direction: ltr; color: #dc2626;">${{ number_format($remaining, 2) }}</td>
                                        <td class="font-bold" style="color: #64748b;">باقیمانده طلب:</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    @if($payments && $payments->count() > 0)
                    <div style="margin-top: 30px; border-top: 2px solid #e2e8f0; padding-top: 20px;">
                        <h4 style="font-size: 12pt; color: #0f172a; margin-bottom: 10px;">تاریخچه تادیات و پرداخت‌های مستقیم (Payment History)</h4>
                        <table class="ledger-table">
                            <thead>
                                <tr>
                                    <th>تاریخ پرداخت</th>
                                    <th>تفصیلات و بابت</th>
                                    <th>نوعیت</th>
                                    <th>ارز اصلی</th>
                                    <th>نرخ تسعیر</th>
                                    <th>معادل دالر ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $pay)
                                    <tr>
                                        <td class="text-center">{{ $pay->date }}</td>
                                        <td>{{ $pay->description }}</td>
                                        <td class="text-center">
                                            {{ $pay->type == 'گرفت' ? 'گرفت (Outflow)' : 'رسید (Inflow)' }}
                                        </td>
                                        <td class="text-center" style="direction: ltr;">{{ number_format($pay->original_amount, 2) }} {{ $pay->currency_code }}</td>
                                        <td class="text-center" style="direction: ltr;">{{ number_format($pay->exchange_rate, 4) }}</td>
                                        <td class="text-left font-bold" style="direction: ltr; color: #166534;">
                                            ${{ number_format($pay->base_amount ?? ($pay->original_amount * ($pay->exchange_rate > 0 ? $pay->exchange_rate : 1)), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <div class="signatures">
                        <div class="signature-box">
                            <div class="signature-line">تنظیم کننده صورتحساب</div>
                        </div>
                        <div class="signature-box">
                            <div class="signature-line">تایید و مهر مدیریت</div>
                        </div>
                        <div class="signature-box">
                            <div class="signature-line">امضا و تایید نماینده تیم</div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px; font-size: 9pt; text-align: right; color: #94a3b8;" dir="ltr">
                        Generated by QBCC ERP System on {{ date('Y-m-d H:i:s') }}
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
