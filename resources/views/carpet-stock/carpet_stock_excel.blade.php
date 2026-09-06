<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 10pt;
            text-align: right;
        }
        th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            text-align: center;
        }
        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a8a;
            text-align: center;
            border: none;
            padding-bottom: 5px;
        }
        .header-subtitle {
            font-size: 11pt;
            color: #475569;
            text-align: center;
            border: none;
            padding-bottom: 20px;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px double #1e3a8a;
        }
    </style>
</head>
<body>
    <table>
        <!-- Company / Report Header -->
        <tr>
            <td colspan="9" class="header-title">{{ config('company.name') }}</td>
        </tr>
        <tr>
            <td colspan="9" class="header-subtitle">گزارش قالین‌های آماده فروش (Ready to Sale Carpets Report)</td>
        </tr>

        <!-- Filter Details Metadata -->
        <tr>
            <td class="meta-label">گدام:</td>
            <td colspan="2" class="meta-value" style="font-weight: bold; background-color: #f1f5f9;">{{ $filter_wh }}</td>
            <td class="meta-label">نوعیت قالین:</td>
            <td colspan="2" class="meta-value" style="font-weight: bold; background-color: #f1f5f9;">{{ $filter_type }}</td>
            <td class="meta-label">محدوده تاریخ:</td>
            <td colspan="2" class="meta-value" style="font-weight: bold; background-color: #f1f5f9; direction: ltr; text-align: right;">{{ $filter_date }}</td>
        </tr>
        <tr>
            <td class="meta-label">جستجوی آزاد:</td>
            <td colspan="2" class="meta-value" style="font-weight: bold; background-color: #f1f5f9;">{{ $filter_search }}</td>
            <td class="meta-label">تاریخ گزارش:</td>
            <td colspan="2" class="meta-value" style="direction: ltr; text-align: right;">{{ date('Y-m-d H:i') }}</td>
            <td class="meta-label">تعداد کل رکوردها:</td>
            <td colspan="2" class="meta-value" style="font-weight: bold;">{{ $carpets->count() }}</td>
        </tr>

        <!-- Empty Row Spacer -->
        <tr><td colspan="9" style="border: none; height: 15px;"></td></tr>

        <!-- Data Headers -->
        <thead>
            <tr>
                <th style="width: 10%;">شماره قالین</th>
                <th style="width: 12%;">نقشه / کوالتی</th>
                <th style="width: 10%;">نوعیت</th>
                <th style="width: 10%;">ابعاد (m)</th>
                <th style="width: 10%;">مساحت (m²)</th>
                <th style="width: 14%;">زمینه / حاشیه</th>
                <th style="width: 14%;">گدام / موقعیت</th>
                <th style="width: 10%;">مدت در گدام</th>
                <th style="width: 10%;">قیمت تمام شد ($)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalArea = 0;
                $totalPrice = 0;
            @endphp
            @foreach($carpets as $carpet)
                @php
                    $daysInStock = \Carbon\Carbon::parse($carpet->date)->diffInDays(now());
                    $totalArea += $carpet->area;
                    $totalPrice += $carpet->total_price;
                @endphp
                <tr>
                    <td class="font-bold text-center">{{ $carpet->carpet_no }}</td>
                    <td class="text-center">{{ $carpet->map_number }} - {{ $carpet->quality->quality ?? '---' }}</td>
                    <td class="text-center">{{ $carpet->type->carpet_type ?? '---' }}</td>
                    <td class="text-left" style="direction: ltr;">{{ $carpet->height }} x {{ $carpet->width }}</td>
                    <td class="text-left font-bold" style="direction: ltr;">{{ $carpet->area }}</td>
                    <td class="text-center">{{ $carpet->field }} / {{ $carpet->margin }}</td>
                    <td>{{ $carpet->warehouse->name ?? 'نامشخص' }} {{ $carpet->warehouse->location ? '('.$carpet->warehouse->location.')' : '' }}</td>
                    <td class="text-center">{{ $daysInStock }} روز</td>
                    <td class="text-left font-bold" style="direction: ltr;">${{ number_format($carpet->total_price, 2) }}</td>
                </tr>
            @endforeach

            <!-- Total Spacer -->
            <tr><td colspan="9" style="border: none; height: 10px;"></td></tr>

            <!-- Totals Rows -->
            <tr class="total-row">
                <td colspan="4" class="text-right">مجموع کل: {{ $carpets->count() }} تخته</td>
                <td class="text-left font-bold" style="direction: ltr;">{{ number_format($totalArea, 2) }}</td>
                <td colspan="3">-</td>
                <td class="text-left font-bold" style="direction: ltr;">${{ number_format($totalPrice, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
