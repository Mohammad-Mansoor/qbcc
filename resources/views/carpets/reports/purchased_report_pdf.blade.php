<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش قالین های خرید شده</title>
    <style>
        * { box-sizing: border-box; }

        @page { size: A4 landscape; margin: 6mm; }

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
            a[href]::after { content: none !important; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
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
        .header-left, .header-center, .header-right {
            display: table-cell;
            vertical-align: middle;
        }
        .header-left  { width: 30%; }
        .header-center { width: 40%; text-align: center; }
        .header-right  { width: 30%; text-align: left; }

        .company-name { font-size: 11pt; font-weight: bold; color: #1e3a8a; margin: 0; }

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
        table.meta-table td:last-child { border-left: none; }
        .meta-label { font-weight: bold; color: #0f172a; display: block; margin-bottom: 1px; }
        .meta-val   { color: #334155; }

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

        .text-center { text-align: center !important; }
        .text-left   { text-align: left !important; }
        .font-bold   { font-weight: bold; }

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
    </style>
</head>
<body onload="window.print()">

    @php
        $statusFilter = request('status') != '' && isset($statuses[request('status')])
            ? $statuses[request('status')]
            : 'تمامی حالت‌ها';

        // Pre-calculate all totals
        $sum_area     = $carpets->sum('area');
        $sum_purchase = $carpets->sum('total_price');
        $sum_repair   = $carpets->sum(fn($c) => $c->repair ? $c->repair->sum('total_price') : 0);
        $sum_wash     = $carpets->sum(fn($c) => $c->carpet_wash ? $c->carpet_wash->total_price : 0);
        $sum_finish   = $carpets->sum(fn($c) => $c->finishing_works ? $c->finishing_works->sum('price') : 0);
        $sum_sold     = $carpets->sum(fn($c) => $c->sale ? $c->sale->sale_cost_total : 0);
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
            <div class="report-title-badge">گزارش قالین‌های خرید شده — {{ $statusFilter }}</div>
        </div>
        <div class="header-right">
            <span style="font-size:7.5pt; color:#475569; direction:ltr; display:block;">{{ $issueDate }}</span>
            <span style="font-size:7.5pt; color:#1d4ed8; font-weight:bold;">{{ number_format($carpets->count()) }} تخته &nbsp;|&nbsp; {{ number_format($sum_area, 2) }} m²</span>
        </div>
    </div>

    <!-- Filter meta row -->
    <table class="meta-table">
        <tr>
            <td style="width:20%;">
                <span class="meta-label">از / تا تاریخ</span>
                <span class="meta-val">{{ request('from_date') ?: '—' }} / {{ request('to_date') ?: '—' }}</span>
            </td>
            <td style="width:20%;">
                <span class="meta-label">شماره پارچه</span>
                <span class="meta-val">{{ request('from_id') ?: '—' }} / {{ request('to_id') ?: '—' }}</span>
            </td>
            <td style="width:20%;">
                <span class="meta-label">نوعیت</span>
                <span class="meta-val">{{ request('type_id') ? ($types->where('carpet_type_id', request('type_id'))->first()->carpet_type ?? 'همه') : 'همه' }}</span>
            </td>
            <td style="width:20%;">
                <span class="meta-label">کوالیتی</span>
                <span class="meta-val">{{ request('quality_id') ? ($qualities->where('id', request('quality_id'))->first()->quality ?? 'همه') : 'همه' }}</span>
            </td>
            <td style="width:20%;">
                <span class="meta-label">حالت فعلی</span>
                <span class="meta-val">{{ $statusFilter }}</span>
            </td>
        </tr>
    </table>

    <!-- Data table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%;">ردیف</th>
                <th style="width:8%;">شماره پارچه</th>
                <th style="width:8%;">نوعیت</th>
                <th style="width:8%;">کوالیتی</th>
                <th style="width:8%;">نقشه</th>
                <th style="width:5%;">طول</th>
                <th style="width:5%;">عرض</th>
                <th style="width:6%;">مساحت</th>
                <th style="width:8%;">قیمت خرید</th>
                <th style="width:8%;">مصارف ترمیم</th>
                <th style="width:8%;">مصارف شست</th>
                <th style="width:8%;">مصارف تکمیلی</th>
                <th style="width:8%;">مبلغ فروش</th>
                <th style="width:8%;">حالت فعلی</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carpets as $index => $carpet)
            @php
                $repair_cost  = $carpet->repair ? $carpet->repair->sum('total_price') : 0;
                $wash_cost    = $carpet->carpet_wash ? $carpet->carpet_wash->total_price : 0;
                $finish_cost  = $carpet->finishing_works ? $carpet->finishing_works->sum('price') : 0;
                $sold_amount  = $carpet->sale ? $carpet->sale->sale_cost_total : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold" style="color:#1d4ed8;">{{ $carpet->carpet_no }}</td>
                <td>{{ $carpet->type->carpet_type ?? '-' }}</td>
                <td>{{ $carpet->quality->quality ?? '-' }}</td>
                <td>{{ $carpet->map_number ?? '-' }}</td>
                <td class="text-center" style="direction:ltr;">{{ $carpet->height }}</td>
                <td class="text-center" style="direction:ltr;">{{ $carpet->width }}</td>
                <td class="text-center font-bold" style="direction:ltr;">{{ number_format($carpet->area, 2) }}</td>
                <td class="text-center font-bold" style="direction:ltr;">${{ number_format($carpet->total_price, 2) }}</td>
                <td class="text-center" style="direction:ltr;">{{ $repair_cost > 0 ? '$'.number_format($repair_cost, 2) : '-' }}</td>
                <td class="text-center" style="direction:ltr;">{{ $wash_cost > 0 ? '$'.number_format($wash_cost, 2) : '-' }}</td>
                <td class="text-center" style="direction:ltr;">{{ $finish_cost > 0 ? '$'.number_format($finish_cost, 2) : '-' }}</td>
                <td class="text-center font-bold" style="direction:ltr; color:#059669;">{{ $sold_amount > 0 ? '$'.number_format($sold_amount, 2) : '-' }}</td>
                <td style="font-size:6.5pt;">{{ $statuses[$carpet->status] ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center" style="padding:10px;">هیچ قالینی یافت نشد.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals: separate table — renders only ONCE at end of content, never repeats on each page --}}
    @if($carpets->count() > 0)
    <table class="totals-table">
        <tbody>
            <tr>
                <td colspan="7" style="text-align:right;">مجموع کلی (Grand Total) — {{ number_format($carpets->count()) }} تخته</td>
                <td style="direction:ltr;">{{ number_format($sum_area, 2) }}</td>
                <td style="direction:ltr;">${{ number_format($sum_purchase, 2) }}</td>
                <td style="direction:ltr;">${{ number_format($sum_repair, 2) }}</td>
                <td style="direction:ltr;">${{ number_format($sum_wash, 2) }}</td>
                <td style="direction:ltr;">${{ number_format($sum_finish, 2) }}</td>
                <td style="direction:ltr; color:#059669;">${{ number_format($sum_sold, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    @endif

</body>
</html>
