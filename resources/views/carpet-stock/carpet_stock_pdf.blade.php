<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Carpet Stock Registry</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 10mm 2mm;
            color: #111827;
        }
        
        @page {
            size: A4 landscape;
            margin: 10mm 2mm; 
        }

        .title-main { font-size: 20pt; font-weight: bold; color: #1e3a8a; margin: 0 0 5px 0; }
        .title-sub { font-size: 11pt; color: #475569; margin: 0; }
        
        .logo { width: 80px; height: auto; }

        .report-title-badge {
            background-color: #eff6ff; 
            border: 1px solid #bfdbfe; 
            padding: 8px 25px; 
            border-radius: 20px; 
            color: #1d4ed8; 
            font-weight: bold; 
            font-size: 14pt;
        }
        
        .meta-table { width: 100%; margin-bottom: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; border-collapse: collapse; }
        .meta-table td { padding: 12px; vertical-align: top; border-left: 1px solid #e2e8f0; }
        .meta-table td:last-child { border-left: none; }
        .meta-label { font-size: 10pt; color: #64748b; font-weight: bold; margin-bottom: 4px; display: block; }
        .meta-val-primary { font-size: 11pt; font-weight: bold; color: #0f172a; margin: 0 0 4px 0; }
        
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 9.5pt;
            padding: 8px 4px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
            font-size: 9pt;
            color: #0f172a;
            vertical-align: middle;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .total-row { background-color: #f1f5f9 !important; font-weight: bold; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; border-top: 2px solid #1e3a8a !important; }

        @media print {
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

    <table style="width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 10px;">
        <tr>
            <td style="width: 15%; text-align: right;">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="Logo">
                @else
                    <div style="width: 80px; height: 80px; background: #eee; text-align: center; line-height: 80px; font-weight: bold;">LOGO</div>
                @endif
            </td>
            <td style="width: 50%; text-align: right; padding-right: 15px;">
                <h2 class="title-main">شرکت صنعتی برادران قاسمی</h2>
                <p class="title-sub">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت افغانستان</p>
            </td>
            <td style="width: 35%; text-align: left;">
                <div class="report-title-badge">
                    گزارش قالین‌های آماده فروش (Ready to Sale)
                </div>
                <div style="margin-top: 10px; font-size: 10pt; color: #475569;">
                    تاریخ گزارش: <span style="direction: ltr; display: inline-block;">{{ date('Y-m-d H:i') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; text-align: center;">
                <span class="meta-label">گدام فیلتر شده</span>
                <p class="meta-val-primary">{{ $filter_wh }}</p>
            </td>
            <td style="width: 25%; text-align: center;">
                <span class="meta-label">نوعیت قالین / جستجو</span>
                <p class="meta-val-primary" style="color: #2563eb;">{{ $filter_type }} <br> <span style="font-size: 9pt; color: #64748b;">( {{ $filter_search }} )</span></p>
            </td>
            <td style="width: 25%; text-align: center;">
                <span class="meta-label">محدوده تاریخ</span>
                <p class="meta-val-primary" style="direction: ltr;">{{ $filter_date }}</p>
            </td>
            <td style="width: 25%; text-align: center;">
                <span class="meta-label">تعداد کل رکوردها</span>
                <p class="meta-val-primary" style="font-size: 14pt;">{{ $carpets->count() }} <span style="font-size: 9pt; font-weight: normal; color: #64748b;">تخته</span></p>
            </td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 4%;">ردیف</th>
                <th style="width: 10%;">شماره قالین</th>
                <th style="width: 12%;">نقشه / کوالتی</th>
                <th style="width: 10%;">نوعیت</th>
                <th style="width: 10%;">ابعاد (m)</th>
                <th style="width: 8%;">مساحت</th>
                <th style="width: 14%;">زمینه / حاشیه</th>
                <th style="width: 14%;">گدام / موقعیت</th>
                <th style="width: 8%;">مدت (روز)</th>
                <th style="width: 10%;">قیمت ($)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalArea = 0;
                $totalPrice = 0;
                $rowIndex = 1;
            @endphp
            @foreach($carpets as $carpet)
                @php
                    $daysInStock = \Carbon\Carbon::parse($carpet->date)->diffInDays(now());
                    $totalArea += $carpet->area;
                    $totalPrice += $carpet->total_price;
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $rowIndex++ }}</td>
                    <td class="text-center font-bold text-primary">{{ $carpet->carpet_no }}</td>
                    <td class="text-center">{{ $carpet->map_number }} <br><span style="font-size: 8pt; color: #64748b;">{{ $carpet->quality->quality ?? '---' }}</span></td>
                    <td class="text-center">{{ $carpet->type->carpet_type ?? '---' }}</td>
                    <td class="text-center" style="direction: ltr;">{{ $carpet->height }} × {{ $carpet->width }}</td>
                    <td class="text-left font-bold" style="direction: ltr;">{{ number_format($carpet->area, 2) }}</td>
                    <td class="text-center">{{ $carpet->field }} / {{ $carpet->margin }}</td>
                    <td class="text-center">{{ $carpet->warehouse->name ?? 'نامشخص' }} <br><span style="font-size: 8pt; color: #64748b;">{{ $carpet->warehouse->location ?? '' }}</span></td>
                    <td class="text-center">{{ $daysInStock }}</td>
                    <td class="text-left font-bold" style="direction: ltr; color: #b91c1c;">${{ number_format($carpet->total_price, 2) }}</td>
                </tr>
            @endforeach
            <!-- Totals (in tbody to only show on last page) -->
            <tr class="total-row">
                <td colspan="5" class="text-right">مجموع کل: {{ $carpets->count() }} تخته</td>
                <td class="text-left font-bold" style="direction: ltr; color: #1d4ed8;">{{ number_format($totalArea, 2) }} m²</td>
                <td colspan="3" class="text-center">-</td>
                <td class="text-left font-bold" style="direction: ltr; color: #1d4ed8;">${{ number_format($totalPrice, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; font-size: 9pt; text-align: right; color: #94a3b8;" dir="ltr">
        Generated by QBCC ERP System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
