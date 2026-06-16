<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Warehouse Movement Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 10mm;
            color: #111827;
        }
        
        @page {
            size: A4 landscape;
            margin: 10mm; 
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
        
        .meta-table { width: 100%; margin-bottom: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 12px; vertical-align: top; }
        .meta-label { font-size: 10pt; color: #64748b; font-weight: bold; margin-bottom: 4px; display: block; }
        .meta-val-primary { font-size: 13pt; font-weight: bold; color: #0f172a; margin: 0 0 4px 0; }
        
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
            font-size: 9pt;
            padding: 6px 4px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
            font-size: 8.5pt;
            color: #0f172a;
            vertical-align: middle;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        .direction-in { background-color: #e6f4ea !important; color: #137333 !important; font-weight: bold; text-align: center; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .direction-out { background-color: #fce8e6 !important; color: #c5221f !important; font-weight: bold; text-align: center; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .total-row { background-color: #f1f5f9 !important; font-weight: bold; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        @media print {
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

    <table style="width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px;">
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
                    گزارش ورودی و خروجی گدام‌ها
                </div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; text-align: center; border-left: 1px solid #e2e8f0;">
                <span class="meta-label">گدام</span>
                <p class="meta-val-primary">{{ $whName }}</p>
            </td>
            <td style="width: 25%; text-align: center; border-left: 1px solid #e2e8f0;">
                <span class="meta-label">جهت تراکنش / نوعیت جنس</span>
                <p class="meta-val-primary" style="color: #2563eb;">{{ $dirFa }} / {{ $itemTypeFa }}</p>
            </td>
            <td style="width: 25%; text-align: center; border-left: 1px solid #e2e8f0;">
                <span class="meta-label">نوع تراکنش</span>
                <p class="meta-val-primary">{{ $typeFa }}</p>
            </td>
            <td style="width: 25%; text-align: center;">
                <span class="meta-label">محدوده تاریخ / تاریخ چاپ</span>
                <p class="meta-val-primary" style="direction: ltr;">{{ request('start_date') ?: 'Start' }} - {{ request('end_date') ?: 'Now' }} <br> <span style="font-size: 9pt;">{{ date('Y-m-d H:i') }}</span></p>
            </td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 10%;">تاریخ ثبت</th>
                <th style="width: 10%;">شماره سند مرجع</th>
                <th style="width: 10%;">گدام</th>
                <th style="width: 8%;">نوعیت آیتم</th>
                <th style="width: 18%;">شرح آیتم</th>
                <th style="width: 10%;">نوع تراکنش</th>
                <th style="width: 8%;">جهت حرکت</th>
                <th style="width: 6%;">تعداد/مقدار</th>
                <th style="width: 6%;">ابعاد (m)</th>
                <th style="width: 5%;">مساحت</th>
                <th style="width: 5%;">قیمت ($)</th>
                <th style="width: 6%;">مجموع ($)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalInQty = 0;
                $totalOutQty = 0;
                $totalInArea = 0;
                $totalOutArea = 0;
                $totalInCost = 0;
                $totalOutCost = 0;
            @endphp
            @foreach($transactions as $tx)
                @php
                    $isCarpet = $tx->item && $tx->item->type === 'App\Carpet';
                    if ($tx->direction === 'IN') {
                        $totalInQty += $tx->quantity;
                        $totalInArea += $tx->area;
                        $totalInCost += $tx->total_cost;
                    } else {
                        $totalOutQty += $tx->quantity;
                        $totalOutArea += $tx->area;
                        $totalOutCost += $tx->total_cost;
                    }
                    $carpetDims = '-';
                    if ($isCarpet) {
                        $actualCarpet = \App\Carpet::find($tx->item->ref_id);
                        if ($actualCarpet && ($actualCarpet->height || $actualCarpet->width)) {
                            $carpetDims = ($actualCarpet->height ?? '-') . ' x ' . ($actualCarpet->width ?? '-');
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center" style="direction: ltr;">{{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d H:i') }}</td>
                    <td class="font-bold text-center">{{ $tx->reference_code }}</td>
                    <td class="text-center">{{ $tx->warehouse ? $tx->warehouse->name : 'N/A' }}</td>
                    <td class="text-center">{{ $isCarpet ? 'قالین' : 'مواد خام' }}</td>
                    <td class="font-bold">{{ $tx->item_name }}</td>
                    <td class="text-center">{{ $tx->type_fa }}</td>
                    <td class="{{ $tx->direction === 'IN' ? 'direction-in' : 'direction-out' }}">
                        {{ $tx->direction === 'IN' ? 'ورود (IN)' : 'خروجی (OUT)' }}
                    </td>
                    <td class="text-left font-bold" style="direction: ltr;">
                        {{ number_format($tx->quantity, 2) }} {{ $isCarpet ? 'Pcs' : 'KG' }}
                    </td>
                    <td class="text-center" style="direction: ltr;">{{ $carpetDims }}</td>
                    <td class="text-left" style="direction: ltr;">{{ $tx->area > 0 ? number_format($tx->area, 2) : '-' }}</td>
                    <td class="text-left" style="direction: ltr;">${{ number_format($tx->unit_cost, 2) }}</td>
                    <td class="text-left font-bold" style="direction: ltr; color: #b91c1c;">${{ number_format($tx->total_cost, 2) }}</td>
                </tr>
            @endforeach
            <!-- Totals (Moved from tfoot to tbody to only show on last page) -->
            <tr class="total-row" style="border-top: 2px solid #1e3a8a;">
                <td colspan="7" class="text-right">خلاصه کل ورودی‌ها (Total IN)</td>
                <td class="text-left" style="direction: ltr;">{{ number_format($totalInQty, 2) }}</td>
                <td class="text-center">-</td>
                <td class="text-left" style="direction: ltr;">{{ number_format($totalInArea, 2) }}</td>
                <td class="text-center">-</td>
                <td class="text-left font-bold" style="direction: ltr;">${{ number_format($totalInCost, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="7" class="text-right">خلاصه کل خروجی‌ها (Total OUT)</td>
                <td class="text-left" style="direction: ltr;">{{ number_format($totalOutQty, 2) }}</td>
                <td class="text-center">-</td>
                <td class="text-left" style="direction: ltr;">{{ number_format($totalOutArea, 2) }}</td>
                <td class="text-center">-</td>
                <td class="text-left font-bold" style="direction: ltr;">${{ number_format($totalOutCost, 2) }}</td>
            </tr>
            <tr class="total-row" style="background-color: #cbd5e1 !important;">
                <td colspan="7" class="text-right">صافی کل دوره (Net Balance)</td>
                <td class="text-left" style="direction: ltr;">{{ number_format($totalInQty - $totalOutQty, 2) }}</td>
                <td class="text-center">-</td>
                <td class="text-left" style="direction: ltr;">{{ number_format($totalInArea - $totalOutArea, 2) }}</td>
                <td class="text-center">-</td>
                <td class="text-left font-bold" style="direction: ltr;">${{ number_format($totalInCost - $totalOutCost, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; font-size: 9pt; text-align: right; color: #94a3b8;" dir="ltr">
        Generated by QBCC ERP System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
