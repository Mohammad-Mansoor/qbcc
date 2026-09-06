<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش مصارف ماهانه - {{ $month_obj->month_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Arabic', Tahoma, Arial, sans-serif; background-color: #fff; color: #1e293b; font-size: 9pt; line-height: 1.4; margin: 0; padding: 10mm; direction: rtl; }
        @page { size: A4 portrait; margin: 0; }
        .report-header { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; display: table; }
        .header-left, .header-center, .header-right { display: table-cell; vertical-align: middle; }
        .header-left { width: 30%; }
        .header-center { width: 40%; text-align: center; }
        .header-right { width: 30%; text-align: left; }
        .company-name { font-size: 13pt; font-weight: bold; color: #1e3a8a; margin: 0; }
        .report-title-badge { background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 6px 16px; border-radius: 12px; color: #1d4ed8; font-weight: bold; font-size: 11pt; display: inline-block; }
        
        table.meta-card { width: 100%; margin-bottom: 20px; background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%); border-radius: 8px; color: white; border-collapse: collapse; }
        table.meta-card td { padding: 10px 15px; vertical-align: middle; border-left: 1px solid rgba(255,255,255,0.2); }
        table.meta-card td:last-child { border-left: none; }
        .meta-label { font-size: 8pt; color: #cbd5e1; display: block; margin-bottom: 4px; }
        .meta-val { font-size: 11pt; color: #ffffff; font-weight: bold; margin: 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #f1f5f9; color: #1e3a8a; font-weight: bold; font-size: 9pt; border: 1px solid #cbd5e1; padding: 8px; text-align: right; }
        table.data-table td { border: 1px solid #e2e8f0; padding: 8px; font-size: 9pt; text-align: right; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        
        .totals-row td { background-color: #1e3a8a !important; color: white !important; font-weight: bold; font-size: 10pt; }
        
        @media print {
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header -->
    <div class="report-header">
        <div class="header-left">
            <table style="border:none; width:auto;">
                <tr>
                    <td style="border:none; padding:0; vertical-align:middle; width:50px;">
                        @if(isset($logoBase64) && $logoBase64)
                            <img src="{{ $logoBase64 }}" style="height:45px; width:auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="border:none; padding:0 10px 0 0; vertical-align:middle;">
                        <p class="company-name">{{ config('company.name') }}</p>
                        <div style="font-size: 8pt; color: #475569;">{{ config('company.description') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-center">
            <div class="report-title-badge">گزارش خلاصه مصارف ماهانه</div>
        </div>
        <div class="header-right">
            <span style="font-size:8pt; color:#475569; direction:ltr; display:block;">تاریخ گزارش: {{ date('Y-m-d H:i') }}</span>
        </div>
    </div>

    <!-- Meta card -->
    <table class="meta-card">
        <tr>
            <td style="width:33%;">
                <span class="meta-label">نام ماه</span>
                <span class="meta-val">{{ $month_obj->month_name }}</span>
            </td>
            <td style="width:33%;">
                <span class="meta-label">شماره ماه</span>
                <span class="meta-val">{{ $month_obj->month_number }}</span>
            </td>
            <td style="width:34%;">
                <span class="meta-label">سال</span>
                <span class="meta-val" style="direction: ltr;">{{ $month_obj->year_name }}</span>
            </td>
        </tr>
    </table>

    <h3 style="color: #1e3a8a; font-size: 12pt; margin-bottom: 10px;">خلاصه مصارف بر اساس دسته‌بندی</h3>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">ردیف</th>
                <th style="width: 30%;">دسته‌بندی (Category)</th>
                <th style="width: 15%;">ارز (Currency)</th>
                <th style="width: 25%; text-align: left;">مبلغ اصلی (Original Amount)</th>
                <th style="width: 25%; text-align: left;">معادل دالر (Base USD)</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 1; $grandTotalBase = 0; @endphp
            @foreach($categoryTotals as $categoryName => $groupedItems)
                @foreach($groupedItems as $item)
                    @php $grandTotalBase += $item->total_base; @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index++ }}</td>
                        <td style="font-weight: bold; color: #1e40af;">{{ $categoryName }}</td>
                        <td style="text-align: center; direction: ltr;">{{ $item->currency_code }}</td>
                        <td style="text-align: left; font-weight: bold; direction: ltr;">{{ number_format($item->total_original, 2) }}</td>
                        <td style="text-align: left; font-weight: bold; direction: ltr; color: #166534;">$ {{ number_format($item->total_base, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="4" style="text-align: right;">مجموع کل مصارف (معادل دالر USD)</td>
                <td style="text-align: left; direction: ltr;">$ {{ number_format($grandTotalBase, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 40px; text-align: center; color: #64748b; font-size: 8pt;">
        ایجاد شده توسط سیستم یکپارچه مدیریت مالی QBIC
    </div>

</body>
</html>
