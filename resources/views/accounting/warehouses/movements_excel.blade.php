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
        .meta-label {
            font-weight: bold;
            background-color: #f1f5f9;
            width: 15%;
        }
        .meta-value {
            width: 35%;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .font-bold {
            font-weight: bold;
        }
        .direction-in {
            background-color: #e6f4ea;
            color: #137333;
            font-weight: bold;
            text-align: center;
        }
        .direction-out {
            background-color: #fce8e6;
            color: #c5221f;
            font-weight: bold;
            text-align: center;
        }
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
            <td colspan="12" class="header-title">شرکت تولیدی قالین برادران قاسمی</td>
        </tr>
        <tr>
            <td colspan="12" class="header-subtitle">گزارش ورودی و خروجی گدام‌ها (Warehouse Movement Ledger)</td>
        </tr>

        <!-- Filter Details Metadata -->
        <tr>
            <td class="meta-label">گدام:</td>
            <td class="meta-value">{{ $whName }}</td>
            <td class="meta-label">جهت تراکنش:</td>
            <td colspan="2" class="meta-value">{{ $dirFa }}</td>
            <td class="meta-label">نوعیت جنس:</td>
            <td colspan="2" class="meta-value">{{ $itemTypeFa }}</td>
            <td class="meta-label">نوع تراکنش:</td>
            <td colspan="3" class="meta-value">{{ $typeFa }}</td>
        </tr>
        <tr>
            <td class="meta-label">از تاریخ:</td>
            <td class="meta-value">{{ request('start_date') ?: 'آغاز دوره' }}</td>
            <td class="meta-label">الی تاریخ:</td>
            <td colspan="2" class="meta-value">{{ request('end_date') ?: 'تا کنون' }}</td>
            <td class="meta-label">تاریخ خروجی:</td>
            <td colspan="2" class="meta-value">{{ date('Y-m-d H:i') }}</td>
            <td class="meta-label">تعداد کل رکوردها:</td>
            <td colspan="3" class="meta-value font-bold">{{ $transactions->count() }}</td>
        </tr>
        
        <!-- Empty Row Spacer -->
        <tr><td colspan="12" style="border: none; height: 15px;"></td></tr>

        <!-- Data Headers -->
        <thead>
            <tr>
                <th style="width: 12%;">تاریخ ثبت</th>
                <th style="width: 12%;">شماره سند مرجع</th>
                <th style="width: 12%;">گدام</th>
                <th style="width: 8%;">نوعیت آیتم</th>
                <th style="width: 16%;">شرح آیتم</th>
                <th style="width: 12%;">نوع تراکنش</th>
                <th style="width: 8%;">جهت حرکت</th>
                <th style="width: 8%;">مقدار/تعداد</th>
                <th style="width: 8%;">ابعاد (m)</th>
                <th style="width: 8%;">مساحت (م²)</th>
                <th style="width: 8%;">قیمت واحد</th>
                <th style="width: 10%;">مجموع هزینه</th>
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
                    <td>{{ $tx->warehouse ? $tx->warehouse->name : 'N/A' }}</td>
                    <td class="text-center">{{ $isCarpet ? 'قالین' : 'مواد خام' }}</td>
                    <td class="font-bold">{{ $tx->item_name }}</td>
                    <td>{{ $tx->type_fa }}</td>
                    <td class="{{ $tx->direction === 'IN' ? 'direction-in' : 'direction-out' }}">
                        {{ $tx->direction === 'IN' ? 'ورود (IN)' : 'خروجی (OUT)' }}
                    </td>
                    <td class="text-left font-bold">
                        {{ number_format($tx->quantity, 2) }} {{ $isCarpet ? 'تخته' : 'KG' }}
                    </td>
                    <td class="text-left" style="direction: ltr;">{{ $carpetDims }}</td>
                    <td class="text-left">{{ $tx->area > 0 ? number_format($tx->area, 2) : '-' }}</td>
                    <td class="text-left">${{ number_format($tx->unit_cost, 2) }}</td>
                    <td class="text-left font-bold">${{ number_format($tx->total_cost, 2) }}</td>
                </tr>
            @endforeach

            <!-- Total Spacer -->
            <tr><td colspan="11" style="border: none; height: 10px;"></td></tr>

            <!-- Totals Rows -->
            <tr class="total-row">
                <td colspan="7" class="text-center">خلاصه کل ورودی‌ها (Total IN)</td>
                <td class="text-left">{{ number_format($totalInQty, 2) }}</td>
                <td>-</td>
                <td class="text-left">{{ number_format($totalInArea, 2) }}</td>
                <td>-</td>
                <td class="text-left">${{ number_format($totalInCost, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="7" class="text-center">خلاصه کل خروجی‌ها (Total OUT)</td>
                <td class="text-left">{{ number_format($totalOutQty, 2) }}</td>
                <td>-</td>
                <td class="text-left">{{ number_format($totalOutArea, 2) }}</td>
                <td>-</td>
                <td class="text-left">${{ number_format($totalOutCost, 2) }}</td>
            </tr>
            <tr class="total-row" style="background-color: #cbd5e1;">
                <td colspan="7" class="text-center">صافی کل دوره (Net Balance)</td>
                <td class="text-left">{{ number_format($totalInQty - $totalOutQty, 2) }}</td>
                <td>-</td>
                <td class="text-left">{{ number_format($totalInArea - $totalOutArea, 2) }}</td>
                <td>-</td>
                <td class="text-left">${{ number_format($totalInCost - $totalOutCost, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
