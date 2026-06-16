<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Finishing Batch Invoice - {{ $batch->reference_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 10mm; /* Standard margin since no fixed headers/footers */
            color: #111827;
        }
        
        @page {
            size: A4 landscape;
            margin: 10mm; 
        }

        .title-block {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .company-info { text-align: right; }
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
            font-size: 9.5pt;
            padding: 6px 4px;
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
        
        .cat-header {
            background-color: #2563eb !important;
            font-size: 8.5pt !important;
        }

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

        @media print {
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

    <!-- Standard Header (No Full Bleed Image) -->
    <table style="width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px;">
        <tr>
            <td style="width: 15%; text-align: right;">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
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
                    صورتحساب تیاری (Finishing Payment Bill)
                </div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; text-align: center; border-left: 1px solid #e2e8f0;">
                <span class="meta-label">تیم کاری / بخش مربوطه</span>
                <p class="meta-val-primary">{{ $team->name ?? '---' }}</p>
            </td>
            <td style="width: 25%; text-align: center; border-left: 1px solid #e2e8f0;">
                <span class="meta-label">نمبر مسلسل</span>
                <p class="meta-val-primary" style="font-family: monospace; color: #2563eb; direction: ltr;">{{ $batch->reference_number }}</p>
            </td>
            <td style="width: 25%; text-align: center; border-left: 1px solid #e2e8f0;">
                <span class="meta-label">مجموع مساحت</span>
                <p class="meta-val-primary" style="direction: ltr;">{{ number_format($groupedCarpets->sum(function($group) { return $group->first()->carpet->area ?? 0; }), 2) }} m²</p>
            </td>
            <td style="width: 25%; text-align: center;">
                <span class="meta-label">تاریخ ایجاد</span>
                <p class="meta-val-primary" style="direction: ltr;">{{ $batch->created_at->format('Y-m-d') }}</p>
            </td>
        </tr>
    </table>

    <h4 style="font-size: 12pt; color: #0f172a; margin-bottom: 10px;">لیست قالین‌ها و مصارف تیاری به تفکیک</h4>
    
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 4%">ردیف</th>
                <th style="width: 9%">نمبر قالین</th>
                <th style="width: 7%">نقشه</th>
                <th style="width: 9%">نوعیت</th>
                <th style="width: 7%">کیفیت</th>
                <th style="width: 8%">ابعاد (m)</th>
                <th style="width: 6%">مساحت (m²)</th>
                @foreach($batchCategories as $cat)
                    <th class="cat-header">{{ $cat->category }}</th>
                @endforeach
                <th style="background-color: #0f172a !important; width: 10%;">مجموع کل ($)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalArea = 0;
                $grandTotalPrice = 0;
                $catTotals = [];
                foreach($batchCategories as $cat) { $catTotals[$cat->id] = 0; }
                $rowIndex = 1;
            @endphp
            @forelse($groupedCarpets as $carpetId => $works)
                @php
                    $firstWork = $works->first();
                    $carpet = $firstWork->carpet;
                    $area = $carpet->area ?? 0;
                    $grandTotalArea += $area;
                    
                    $carpetTotal = $works->sum('price');
                    $grandTotalPrice += $carpetTotal;
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $rowIndex++ }}</td>
                    <td class="text-center font-bold text-primary">{{ $carpet->carpet_no ?? 'N/A' }}</td>
                    <td class="text-center">{{ $carpet->map_number ?? '---' }}</td>
                    <td class="text-center">{{ $carpet->type->carpet_type ?? '---' }}</td>
                    <td class="text-center">{{ $carpet->quality->quality ?? '---' }}</td>
                    <td class="text-center" style="direction: ltr;">{{ $carpet->height ?? '---' }} × {{ $carpet->width ?? '---' }}</td>
                    <td class="text-center font-bold" style="direction: ltr;">{{ number_format($area, 2) }}</td>
                    
                    @foreach($batchCategories as $cat)
                        @php
                            $workForCat = $works->firstWhere('category_id', $cat->id);
                            $workPrice = $workForCat ? $workForCat->price : 0;
                            $catTotals[$cat->id] += $workPrice;
                        @endphp
                        <td class="text-center" style="direction: ltr;">
                            @if($workPrice > 0)
                                <span style="color: #1e293b;">${{ number_format($workPrice, 2) }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="text-center font-bold" style="color: #b91c1c; direction: ltr; background-color: #fef2f2 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">${{ number_format($carpetTotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 8 + $batchCategories->count() }}" class="text-center" style="color: #64748b;">هیچ قالینی ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot style="background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
            <tr>
                <td colspan="6" class="text-left font-bold">مجموع کل:</td>
                <td class="text-center font-bold text-primary" style="direction: ltr;">{{ number_format($grandTotalArea, 2) }} m²</td>
                @foreach($batchCategories as $cat)
                    <td class="text-center font-bold" style="direction: ltr; color: #0f172a;">${{ number_format($catTotals[$cat->id], 2) }}</td>
                @endforeach
                <td class="text-center font-bold" style="color: #b91c1c; direction: ltr; background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">${{ number_format($grandTotalPrice, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%;">
                @if($payments && $payments->count() > 0)
                <div style="padding-left: 20px;">
                    <h5 style="font-size: 10pt; color: #0f172a; margin-bottom: 5px;">تاریخچه تادیات و پرداخت‌ها</h5>
                    <table class="ledger-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="background-color: #475569 !important;">تاریخ</th>
                                <th style="background-color: #475569 !important;">بابت</th>
                                <th style="background-color: #475569 !important;">مبلغ اصلی</th>
                                <th style="background-color: #475569 !important;">معادل ($)</th>
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
            <td style="width: 50%;">
                <table class="totals-table text-right">
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

</body>
</html>
