<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Carpet Stock Registry</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

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
            size: A4 landscape;
            margin: 6mm 6mm 6mm 6mm;
        }

        /* Suppress URL links printed by browser */
        @media print {
            a[href]::after { content: none !important; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }

        /* ── Header ── */
        .report-header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 5px;
            margin-bottom: 6px;
            display: table;
        }
        .header-left, .header-center, .header-right {
            display: table-cell;
            vertical-align: middle;
        }
        .header-center { text-align: center; }
        .header-right  { text-align: left; width: 28%; }
        .header-left   { width: 28%; }

        .company-name { font-size: 12pt; font-weight: bold; color: #1e3a8a; margin: 0; }

        .report-title-badge {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 4px 14px;
            border-radius: 14px;
            color: #1d4ed8;
            font-weight: bold;
            font-size: 10pt;
            white-space: nowrap;
            display: inline-block;
        }
        .report-date { font-size: 8pt; color: #475569; margin-top: 4px; }

        /* ── Meta row ── */
        table.meta-table {
            width: 100%;
            margin-bottom: 5px;
            background: #f8fafc;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        }
        table.meta-table td {
            padding: 5px 8px;
            vertical-align: middle;
            border-left: 1px solid #e2e8f0;
            text-align: center;
        }
        table.meta-table td:last-child { border-left: none; }
        .meta-label { font-size: 7.5pt; color: #64748b; font-weight: bold; display: block; margin-bottom: 2px; }
        .meta-val   { font-size: 9pt; font-weight: bold; color: #0f172a; margin: 0; }

        /* ── Ledger table ── */
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.ledger-table th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            font-size: 8pt;
            padding: 5px 3px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 3px;
            font-size: 7.5pt;
            color: #0f172a;
            vertical-align: middle;
            line-height: 1.2;
        }
        table.ledger-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-center { text-align: center; }
        .text-left   { text-align: left; }
        .text-right  { text-align: right; }
        .font-bold   { font-weight: bold; }

        .total-row {
            background-color: #e7edf8 !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border-top: 2px solid #1e3a8a !important;
        }
    </style>
</head>
<body onload="window.print();">

    <!-- Header (table-based for cross-browser compatibility in print) -->
    <div class="report-header">
        <div class="header-left">
            <table style="border:none; width:100%;">
                <tr>
                    <td style="border:none; padding:0; vertical-align:middle; width:55px;">
                        @if(isset($logoBase64) && $logoBase64)
                            <img src="data:image/png;base64,{{ $logoBase64 }}" style="height:40px; width:auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="border:none; padding:0 0 0 8px; vertical-align:middle;">
                        <p class="company-name">{{ config('company.name') }}</p>
                        <div style="font-size: 8pt; color: #475569;">{{ config('company.description') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-center">
            <div class="report-title-badge">گزارش قالین‌های آماده فروش (Ready to Sale)</div>
        </div>
        <div class="header-right">
            <div class="report-date" style="direction:ltr; text-align:left;">تاریخ: {{ date('Y-m-d H:i') }}</div>
        </div>
    </div>

    <!-- Meta summary row -->
    <table class="meta-table">
        <tr>
            <td style="width:25%;">
                <span class="meta-label">گدام فیلتر شده</span>
                <p class="meta-val">{{ $filter_wh }}</p>
            </td>
            <td style="width:25%;">
                <span class="meta-label">نوعیت / جستجو</span>
                <p class="meta-val" style="color:#2563eb;">{{ $filter_type }} &nbsp;<span style="font-size:7.5pt; color:#64748b;">({{ $filter_search }})</span></p>
            </td>
            <td style="width:25%;">
                <span class="meta-label">محدوده تاریخ</span>
                <p class="meta-val" style="direction:ltr;">{{ $filter_date }}</p>
            </td>
            <td style="width:25%;">
                <span class="meta-label">تعداد کل رکوردها</span>
                <p class="meta-val" style="font-size:11pt;">{{ $carpets->count() }} <span style="font-size:8pt; font-weight:normal; color:#64748b;">تخته</span></p>
            </td>
        </tr>
    </table>

    <!-- Data table -->
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width:4%;">ردیف</th>
                <th style="width:10%;">شماره قالین</th>
                <th style="width:12%;">نقشه / کوالتی</th>
                <th style="width:10%;">نوعیت</th>
                <th style="width:10%;">ابعاد (m)</th>
                <th style="width:8%;">مساحت</th>
                <th style="width:14%;">زمینه / حاشیه</th>
                <th style="width:14%;">گدام / موقعیت</th>
                <th style="width:8%;">مدت (روز)</th>
                <th style="width:10%;">قیمت ($)</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Pre-calculate totals so they are available after the table closes
                $totalArea  = $carpets->sum('area');
                $totalPrice = $carpets->sum('total_price');
                $rowIndex   = 1;
            @endphp
            @foreach($carpets as $carpet)
                @php
                    $daysInStock = \Carbon\Carbon::parse($carpet->date)->diffInDays(now());
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $rowIndex++ }}</td>
                    <td class="text-center font-bold" style="color:#1d4ed8;">{{ $carpet->carpet_no }}</td>
                    <td class="text-center">{{ $carpet->map_number }}<br><span style="font-size:7pt; color:#64748b;">{{ $carpet->quality->quality ?? '---' }}</span></td>
                    <td class="text-center">{{ $carpet->type->carpet_type ?? '---' }}</td>
                    <td class="text-center" style="direction:ltr;">{{ $carpet->height }} &times; {{ $carpet->width }}</td>
                    <td class="text-left font-bold" style="direction:ltr;">{{ number_format($carpet->area, 2) }}</td>
                    <td class="text-center">{{ $carpet->field }} / {{ $carpet->margin }}</td>
                    <td class="text-center">{{ $carpet->warehouse->name ?? 'نامشخص' }}<br><span style="font-size:7pt; color:#64748b;">{{ $carpet->warehouse->location ?? '' }}</span></td>
                    <td class="text-center">{{ $daysInStock }}</td>
                    <td class="text-left font-bold" style="direction:ltr; color:#b91c1c;">${{ number_format($carpet->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals: rendered OUTSIDE the main table so it only prints once on the last page --}}
    <table class="ledger-table" style="margin-top: 0; border-top: none; page-break-inside: avoid;">
        <tbody>
            <tr class="total-row">
                <td colspan="5" class="text-right" style="border-top: 2px solid #1e3a8a;">مجموع کل: {{ $carpets->count() }} تخته</td>
                <td class="text-left font-bold" style="direction:ltr; color:#1d4ed8; border-top: 2px solid #1e3a8a;">{{ number_format($totalArea, 2) }} m²</td>
                <td colspan="3" class="text-center" style="border-top: 2px solid #1e3a8a;">—</td>
                <td class="text-left font-bold" style="direction:ltr; color:#1d4ed8; border-top: 2px solid #1e3a8a;">${{ number_format($totalPrice, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
