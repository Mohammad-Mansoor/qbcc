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
            padding: 4mm;
            color: #000;
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

        .title-main { font-size: 14pt; font-weight: bold; color: #1e3a8a; margin: 0; display: inline-block; }
        .logo { width: 50px; height: auto; }

        .report-title-badge {
            background-color: #eff6ff; 
            border: 1px solid #bfdbfe; 
            padding: 4px 10px; 
            border-radius: 4px; 
            color: #1d4ed8; 
            font-weight: bold; 
            font-size: 10pt;
            display: inline-block;
            margin-right: 10px;
        }
        
        .meta-table { width: 100%; margin-bottom: 5px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .meta-table td { padding: 2px; vertical-align: middle; }
        .meta-label { font-size: 8pt; color: #475569; font-weight: bold; }
        .meta-val-primary { font-size: 9pt; font-weight: bold; color: #000; margin: 0; display: inline-block; margin-right: 5px;}
        
        .billing-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 5px;
            margin-bottom: 5px;
        }
        .billing-title {
            font-size: 8pt;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }
        .billing-details {
            font-size: 8pt;
            color: #000;
        }
        
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #94a3b8;
            font-size: 7pt;
            padding: 3px 2px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #94a3b8;
            padding: 3px 2px;
            font-size: 7.5pt;
            color: #000;
            vertical-align: middle;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 4px;
            font-size: 8pt;
            border-bottom: 1px solid #e2e8f0;
            color: #000;
        }
        .totals-table tr.grand-total td {
            font-size: 10pt;
            font-weight: bold;
            color: #2563eb !important;
            background-color: #eff6ff !important;
            border-top: 1px solid #1e3a8a;
            border-bottom: 1px solid #1e3a8a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .signatures {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .signature-box { width: 30%; }
        .signature-line {
            border-top: 1px dotted #000;
            padding-top: 4px;
            font-weight: bold;
            font-size: 8pt;
            color: #000;
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
                    <table style="width: 100%; border-bottom: 1px solid #1e3a8a; padding-bottom: 2px; margin-bottom: 5px;">
    <tr>
        <td style="width: 10%; text-align: right;">
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
            @endif
        </td>
        <td style="width: 40%; text-align: right; padding-right: 5px; vertical-align: middle;">
            <h2 class="title-main">شرکت برادران قاسمی</h2>
        </td>
        <td style="width: 50%; text-align: left; vertical-align: middle;">
            <div class="report-title-badge">
                @if($batch->type == 'kachaee')
                    صورتحساب کچایی (Kachaee Bill)
                @elseif($batch->type == 'wash')
                    صورتحساب شستشو (Washing Bill)
                @elseif($batch->type == 'finish')
                    صورتحساب تیاری (Finishing Bill)
                @else
                    صورتحساب تولید (Production Bill)
                @endif
            </div>
        </td>
    </tr>
</table>

<table class="meta-table">
    <tr>
        <td style="width: 33%; text-align: right;">
            <span class="meta-label">نمبر مسلسل:</span>
            <span class="meta-val-primary" style="font-family: monospace; color: #2563eb; direction: ltr;">{{ $batch->reference_number }}</span>
        </td>
        <td style="width: 33%; text-align: right;">
            <span class="meta-label">وضعیت:</span>
            <span class="meta-val-primary" style="color: {{ $batch->status == 'open' ? '#16a34a' : '#dc2626' }};">
                {{ $batch->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
            </span>
        </td>
        <td style="width: 33%; text-align: left;">
            <span class="meta-label">تاریخ ایجاد:</span>
            <span class="meta-val-primary" style="direction: ltr;">{{ $batch->created_at->format('Y-m-d') }}</span>
        </td>
    </tr>
</table>

<table style="width: 100%; margin-bottom: 5px;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding-left: 5px;">
            <div class="billing-section">
                <div class="billing-title">تیم کاری / بخش مربوطه</div>
                <div class="billing-details">
                    @if($team)
                        <strong>نام تیم:</strong> {{ $team->name }} | <strong>تلفن:</strong> {{ $team->phone ?? 'N/A' }}<br>
                    @else
                        <strong style="color: #ef4444;">--- ثبت نشده ---</strong><br>
                    @endif
                    <strong>نوعیت:</strong> 
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
        <td style="width: 50%; vertical-align: top; padding-right: 5px;">
            <div class="billing-section">
                <div class="billing-title">خلاصه وضعیت کارکرد</div>
                <div class="billing-details">
                    <strong>تعداد قالین:</strong> <span style="direction: ltr;">{{ $carpets->count() }} Pcs</span> | 
                    <strong>مجموع مساحت:</strong> <span style="direction: ltr;">{{ number_format($carpets->sum(function($c) { return $c->carpet->area ?? 0; }), 2) }} m²</span>
                </div>
            </div>
        </td>
    </tr>
</table>

<h4 style="font-size: 10pt; color: #0f172a; margin-bottom: 2px; margin-top: 0;">لیست قالین‌ها</h4>
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

<table style="width: 100%; margin-top: 5px;">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            @if($payments && $payments->count() > 0)
            <div style="padding-left: 10px;">
                <h5 style="font-size: 8pt; color: #0f172a; margin: 0 0 2px 0;">تادیات</h5>
                <table class="ledger-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th style="background-color: #475569 !important; padding: 2px;">تاریخ</th>
                            <th style="background-color: #475569 !important; padding: 2px;">بابت</th>
                            <th style="background-color: #475569 !important; padding: 2px;">مبلغ اصلی</th>
                            <th style="background-color: #475569 !important; padding: 2px;">معادل ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $pay)
                            <tr>
                                <td class="text-center">{{ $pay->date }}</td>
                                <td>{{ $pay->description }}</td>
                                <td class="text-center" style="direction: ltr;">{{ number_format($pay->original_amount, 2) }} {{ $pay->currency_code }}</td>
                                <td class="text-center font-bold" style="direction: ltr; color: #166534;">
                                    ${{ number_format($pay->base_amount ?? ($pay->original_amount * ($pay->exchange_rate > 0 ? $pay->exchange_rate : 1)), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </td>
        <td style="width: 40%; vertical-align: top;">
            <table class="totals-table text-right">
                <tr>
                    <td class="text-left font-bold" style="direction: ltr;">{{ number_format($carpets->sum(function($c) { return $c->carpet->area ?? 0; }), 2) }} m²</td>
                    <td class="font-bold" style="color: #64748b;">کل مساحت:</td>
                </tr>
                <tr class="grand-total">
                    <td class="text-left font-bold" style="direction: ltr;">${{ number_format($totalCost, 2) }}</td>
                    <td class="font-bold">مبلغ کل (USD):</td>
                </tr>
                <tr>
                    <td class="text-left font-bold" style="direction: ltr; color: #16a34a;">${{ number_format($totalPaid, 2) }}</td>
                    <td class="font-bold" style="color: #64748b;">پرداخت شده:</td>
                </tr>
                <tr>
                    <td class="text-left font-bold" style="direction: ltr; color: #dc2626;">${{ number_format($remaining, 2) }}</td>
                    <td class="font-bold" style="color: #64748b;">باقیمانده:</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="signatures">
    <div class="signature-box">
        <div class="signature-line">تنظیم کننده صورتحساب</div>
    </div>
    <div class="signature-box">
        <div class="signature-line">تایید و مهر مدیریت</div>
    </div>
    <div class="signature-box">
        <div class="signature-line">امضا نماینده تیم</div>
    </div>
</div>

                    <div style="margin-top: 5px; font-size: 6pt; text-align: right; color: #94a3b8;" dir="ltr">
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
