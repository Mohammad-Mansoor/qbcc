<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Sales Report (PDF) {{ $search ? ' - '.$search : '' }}</title>
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

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

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
                <h2 class="title-main">شرکت برادران قاسمی</h2>
            </td>
            <td style="width: 60%; text-align: left; vertical-align: middle;">
                <div class="report-title-badge">گزارش فروشات (Sales Report)</div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 33%; text-align: right;">
                <span class="meta-label">تاریخ گزارش:</span>
                <span class="meta-val-primary" style="direction: ltr;">{{ date('Y-m-d H:i') }}</span>
            </td>
            <td style="width: 33%; text-align: right;">
                <span class="meta-label">کلمه جستجو:</span>
                <span class="meta-val-primary">{{ $search ?? 'همه موارد' }}</span>
            </td>
            <td style="width: 34%; text-align: left;">
                <span class="meta-label">تعداد فروشات:</span>
                <span class="meta-val-primary">{{ $sales->count() }} ثوب</span>
            </td>
        </tr>
    </table>
    
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 3%">ردیف</th>
                <th style="width: 8%">نمبر انوایس</th>
                <th style="width: 12%">مشتری</th>
                <th style="width: 9%">نمبر قالین</th>
                <th style="width: 8%">نوعیت</th>
                <th style="width: 7%">کوالتی</th>
                <th style="width: 8%">ابعاد (m)</th>
                <th style="width: 6%">مساحت (m²)</th>
                <th style="width: 8%">قیمت خرید ($)</th>
                <th style="width: 8%">قیمت تمام شد ($)</th>
                <th style="width: 8%">قیمت فی متر</th>
                <th style="width: 10%; background-color: #0f172a !important;">مجموع فروش ($)</th>
                @if(auth()->user()->role == 'SP')
                <th style="width: 8%; background-color: #065f46 !important;">مفاد خالص ($)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalArea = 0;
                $grandTotalCost = 0;
                $grandTotalSale = 0;
                $grandTotalProfit = 0;
                $grandTotalPurchase = 0;
                $rowIndex = 1;
            @endphp
            @forelse($sales as $sale)
                @php
                    if($sale->is_returned) continue;

                    $area = $sale->carpet_area ?? ($sale->carpet->area ?? 0);
                    $grandTotalArea += $area;
                    
                    $saleUsdTotal = $sale->sale_cost_total;
                    if ($sale->currency_code !== 'USD' && $sale->exchange_rate > 0) {
                        if ($sale->exchange_rate > 1) {
                            $saleUsdTotal = $sale->sale_cost_total / $sale->exchange_rate;
                        } else {
                            $saleUsdTotal = $sale->sale_cost_total * $sale->exchange_rate;
                        }
                    }

                    $carpetPurchasePrice = $sale->carpet->carpet_price_us ?? ($sale->carpet->original_price ?? 0);
                    $carpetTotalPrice = $sale->carpet->total_price ?? 0;

                    $grandTotalPurchase += $carpetPurchasePrice;
                    $grandTotalCost += $carpetTotalPrice;
                    $grandTotalSale += $saleUsdTotal;
                    $grandTotalProfit += $sale->profit;

                    $saleUsdPerMeter = $sale->sale_cost_per_meter;
                    if ($sale->currency_code !== 'USD' && $sale->exchange_rate > 0) {
                        if ($sale->exchange_rate > 1) {
                            $saleUsdPerMeter = $sale->sale_cost_per_meter / $sale->exchange_rate;
                        } else {
                            $saleUsdPerMeter = $sale->sale_cost_per_meter * $sale->exchange_rate;
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $rowIndex++ }}</td>
                    <td class="text-center font-bold text-primary" style="direction: ltr;">{{ $sale->invoice->invoice_no ?? '---' }}</td>
                    <td class="text-center">{{ $sale->customer->name ?? '---' }} <br><span style="font-size: 6pt; color: #475569;">{{ $sale->customer->customer_code ?? '' }}</span></td>
                    <td class="text-center font-bold">{{ $sale->carpet->carpet_no ?? '---' }}</td>
                    <td class="text-center">{{ $sale->type }}</td>
                    <td class="text-center">{{ $sale->quality }}</td>
                    <td class="text-center" style="direction: ltr;">{{ $sale->carpet_height ?? ($sale->carpet->height ?? '---') }} × {{ $sale->carpet_width ?? ($sale->carpet->width ?? '---') }}</td>
                    <td class="text-center font-bold" style="direction: ltr;">{{ number_format($area, 2) }}</td>
                    <td class="text-center" style="direction: ltr;">${{ number_format($carpetPurchasePrice, 2) }}</td>
                    <td class="text-center" style="color: #b91c1c; direction: ltr;">${{ number_format($carpetTotalPrice, 2) }}</td>
                    <td class="text-center" style="direction: ltr;">
                        ${{ number_format($saleUsdPerMeter, 2) }}
                        @if($sale->currency_code !== 'USD')
                            <br><span style="font-size: 6pt; color: #475569;">({{ $sale->currency_code }} {{ number_format($sale->sale_cost_per_meter, 2) }})</span>
                        @endif
                    </td>
                    <td class="text-center font-bold" style="color: #16a34a; direction: ltr; background-color: #f0fdf4 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                        ${{ number_format($saleUsdTotal, 2) }}
                        @if($sale->currency_code !== 'USD')
                            <br><span style="font-size: 6pt; color: #475569;">({{ $sale->currency_code }} {{ number_format($sale->sale_cost_total, 2) }})</span>
                        @endif
                    </td>
                    @if(auth()->user()->role == 'SP')
                    <td class="text-center font-bold" style="direction: ltr; color: {{ $sale->profit >= 0 ? '#166534' : '#991b1b' }}; background-color: {{ $sale->profit >= 0 ? '#dcfce7' : '#fee2e2' }} !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                        ${{ number_format($sale->profit, 2) }}
                    </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->role == 'SP' ? 13 : 12 }}" class="text-center" style="color: #64748b; padding: 15px;">هیچ فروشاتی یافت نشد.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot style="background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
            <tr>
                <td colspan="7" class="text-left font-bold">مجموع کل:</td>
                <td class="text-center font-bold text-primary" style="direction: ltr;">{{ number_format($grandTotalArea, 2) }} m²</td>
                <td class="text-center font-bold" style="direction: ltr; color: #0f172a;">${{ number_format($grandTotalPurchase, 2) }}</td>
                <td class="text-center font-bold" style="color: #b91c1c; direction: ltr;">${{ number_format($grandTotalCost, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr;">---</td>
                <td class="text-center font-bold" style="color: #16a34a; direction: ltr; background-color: #dcfce7 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">${{ number_format($grandTotalSale, 2) }}</td>
                @if(auth()->user()->role == 'SP')
                <td class="text-center font-bold" style="color: #166534; direction: ltr; background-color: #bbf7d0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">${{ number_format($grandTotalProfit, 2) }}</td>
                @endif
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 6pt; text-align: right; color: #94a3b8;" dir="ltr">
        Generated by QBIC ERP System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
