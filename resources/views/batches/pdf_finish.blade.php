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
            padding: 4mm;
            color: #000;
        }
        
        @page {
            size: A4 landscape;
            margin: 2mm; 
        }

        .title-main { font-size: 14pt; font-weight: bold; color: #1e3a8a; margin: 0; }
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
        }
        
        .meta-table { width: 100%; margin-bottom: 5px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .meta-table td { padding: 2px; vertical-align: middle; }
        .meta-label { font-size: 8pt; color: #475569; font-weight: bold; }
        .meta-val-primary { font-size: 9pt; font-weight: bold; color: #000; margin: 0; display: inline-block; margin-right: 5px;}
        
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
            font-size: 7.5pt;
            padding: 6px 4px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table.ledger-table td {
            border: 1px solid #94a3b8;
            padding: 5px 4px;
            font-size: 8pt;
            color: #000;
            vertical-align: middle;
            line-height: 1.35;
        }
        table.ledger-table tbody tr:nth-child(even) { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        .cat-header {
            background-color: #2563eb !important;
            font-size: 7pt !important;
        }

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
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print();">

    <table style="width: 100%; border-bottom: 1px solid #1e3a8a; padding-bottom: 2px; margin-bottom: 5px;">
        <tr>
            <td style="width: 10%; text-align: right;">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
                @endif
            </td>
            <td style="width: 30%; text-align: right; padding-right: 5px; vertical-align: middle;">
                <h2 class="title-main">{{ config('company.name') }}</h2>
                <div style="font-size: 8pt; color: #475569;">{{ config('company.description') }}</div>
            </td>
            <td style="width: 60%; text-align: left; vertical-align: middle;">
                <div class="report-title-badge">صورتحساب تیاری (Finishing Payment Bill)</div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; text-align: right;">
                <span class="meta-label">تیم کاری:</span>
                <span class="meta-val-primary">{{ $team->name ?? '---' }}</span>
            </td>
            <td style="width: 25%; text-align: right;">
                <span class="meta-label">نمبر مسلسل:</span>
                <span class="meta-val-primary" style="font-family: monospace; color: #2563eb; direction: ltr;">{{ $batch->reference_number }}</span>
            </td>
            <td style="width: 25%; text-align: right;">
                <span class="meta-label">مجموع مساحت:</span>
                <span class="meta-val-primary" style="direction: ltr;">{{ number_format($groupedCarpets->sum(function($group) { return $group->first()->carpet->area ?? 0; }), 2) }} m²</span>
            </td>
            <td style="width: 25%; text-align: left;">
                <span class="meta-label">تاریخ ایجاد:</span>
                <span class="meta-val-primary" style="direction: ltr;">{{ $batch->created_at->format('Y-m-d') }}</span>
            </td>
        </tr>
        @if(!empty($startDate) && !empty($endDate))
        <tr>
            <td colspan="4" style="text-align: center; padding-top: 4px; border-top: 1px dashed #cbd5e1;">
                <span class="meta-label">فیلتر محدوده تاریخ (Date Filter):</span>
                <span class="meta-val-primary" style="direction: ltr; font-weight: bold; color: #1e3a8a;">از {{ $startDate }} الی {{ $endDate }}</span>
            </td>
        </tr>
        @endif
    </table>

    <h4 style="font-size: 12pt; color: #0f172a; margin-bottom: 10px;">لیست قالین‌ها و مصارف تیاری به تفکیک</h4>
    
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 4%">ردیف</th>
                <th style="width: 9%">نمبر قالین</th>
                <th style="width: 7%">نقشه</th>
                <th style="width: 8%">نوعیت</th>
                <th style="width: 6%">کیفیت</th>
                <th style="width: 5%">طول (m)</th>
                <th style="width: 5%">عرض (m)</th>
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
                    <td class="text-center" style="direction: ltr;">{{ $carpet->height ?? '---' }}</td>
                    <td class="text-center" style="direction: ltr;">{{ $carpet->width ?? '---' }}</td>
                    <td class="text-center font-bold" style="direction: ltr;">{{ number_format($area, 2) }}</td>
                    
                    @foreach($batchCategories as $cat)
                        @php
                            $workForCat = $works->firstWhere('category_id', $cat->id);
                            $workPrice = $workForCat ? $workForCat->price : 0;
                            $catTotals[$cat->id] += $workPrice;
                            
                            $unitPriceStr = '';
                            if ($workForCat && $workPrice > 0) {
                                $totalOrig = ($workForCat->currency_code == 'AFN') ? $workForCat->price_af : $workForCat->price;
                                $uPrice = 0;
                                if (in_array($cat->id, [1, 3, 5, 6, 7, 9])) {
                                    $uPrice = $area > 0 ? ($totalOrig / $area) : 0;
                                } elseif (in_array($cat->id, [4, 8])) {
                                    $uPrice = ($carpet->height > 0) ? ($totalOrig / ($carpet->height * 2)) : 0;
                                } elseif ($cat->id == 2) {
                                    $uPrice = $totalOrig;
                                }
                                $curSym = $workForCat->currency_code == 'AFN' ? 'AFN' : '$';
                                $unitPriceStr = $curSym . round($uPrice, 2);
                            }
                        @endphp
                        <td class="text-center" style="direction: ltr;">
                            @if($workPrice > 0)
                                <span style="display: block; font-weight: bold;">${{ number_format($workPrice, 2) }}</span>
                                <span style="color: #475569; font-size: 6pt;">({{ $unitPriceStr }}{{ $cat->id != 2 ? '/m' : '' }})</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="text-center font-bold" style="color: #b91c1c; direction: ltr; background-color: #fef2f2 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">${{ number_format($carpetTotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 9 + $batchCategories->count() }}" class="text-center" style="color: #64748b;">هیچ قالینی ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot style="background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
            <tr>
                <td colspan="7" class="text-left font-bold">مجموع کل:</td>
                <td class="text-center font-bold text-primary" style="direction: ltr;">{{ number_format($grandTotalArea, 2) }} m²</td>
                @foreach($batchCategories as $cat)
                    <td class="text-center font-bold" style="direction: ltr; color: #0f172a;">${{ number_format($catTotals[$cat->id], 2) }}</td>
                @endforeach
                <td class="text-center font-bold" style="color: #b91c1c; direction: ltr; background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">${{ number_format($grandTotalPrice, 2) }}</td>
            </tr>
        </tfoot>
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
            <div class="signature-line">امضا و تایید نماینده تیم</div>
        </div>
    </div>
    
    <div style="margin-top: 5px; font-size: 6pt; text-align: right; color: #94a3b8;" dir="ltr">
        Generated by QBIC ERP System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
