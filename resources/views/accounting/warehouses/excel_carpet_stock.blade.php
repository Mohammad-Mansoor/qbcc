<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Tahoma, Arial, sans-serif; direction: rtl; }
        .header-title { font-size: 14pt; font-weight: bold; color: #047857; text-align: center; }
        .filter-header { background-color: #f1f5f9; font-weight: bold; }
        .table-header { background-color: #065f46; color: #ffffff; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .grand-total { background-color: #e2e8f0; font-weight: bold; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td colspan="11" class="header-title">گزارش موجودی قالین‌ها (Carpet Available Stock Report)</td>
        </tr>
        <tr>
            <td colspan="5" class="text-right">تاریخ گزارش: {{ date('Y-m-d H:i') }}</td>
            <td colspan="6" class="text-left">توسط: {{ auth()->user()->name }}</td>
        </tr>
        <tr><td colspan="11"></td></tr>
        
        <!-- Applied Filters -->
        <tr class="filter-header">
            <td colspan="11">
                فیلترهای اعمال شده: کیفیت: {{ $selectedQualityName }} | نوعیت / دیزاین: {{ $selectedTypeName }}
            </td>
        </tr>
        <tr><td colspan="11"></td></tr>

        <!-- Summary Metrics -->
        <tr>
            <td colspan="3" style="background-color: #ecfdf5; font-weight: bold;">موجودی کل قالین‌ها:</td>
            <td colspan="3" style="background-color: #ecfdf5;">{{ number_format($totalAvailableQty) }} تخته ({{ number_format($totalAvailableSqm, 2) }} m²)</td>
            <td colspan="3" style="background-color: #eff6ff; font-weight: bold;">آماده برای فروش:</td>
            <td colspan="2" style="background-color: #eff6ff;">{{ number_format($totalReadyQty) }} تخته ({{ number_format($totalReadySqm, 2) }} m²)</td>
        </tr>
        <tr>
            <td colspan="3" style="background-color: #fef2f2; font-weight: bold;">فروخته شده:</td>
            <td colspan="8" style="background-color: #fef2f2;">{{ number_format($totalSoldQty) }} تخته ({{ number_format($totalSoldSqm, 2) }} m²)</td>
        </tr>
        <tr><td colspan="11"></td></tr>

        <!-- Data Table -->
        <thead>
            <tr class="table-header">
                <th>#</th>
                <th>کیفیت (Quality)</th>
                <th>نوعیت / دیزاین (Type)</th>
                <th>تولید (WIP Pcs)</th>
                <th>مساحت تولید (WIP m²)</th>
                <th>آماده (Ready Pcs)</th>
                <th>مساحت آماده (Ready m²)</th>
                <th>فروش (Sold Pcs)</th>
                <th>مساحت فروش (Sold m²)</th>
                <th>کل مساحت (Total m²)</th>
                <th>ارزش کل ($ USD)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandWipQty = 0; $grandWipSqm = 0;
                $grandReadyQty = 0; $grandReadySqm = 0;
                $grandSoldQty = 0; $grandSoldSqm = 0;
                $grandTotalSqm = 0; $grandTotalCost = 0;
            @endphp
            @foreach($groupedCategories as $index => $group)
                @php
                    $grandWipQty += $group->wip_qty;
                    $grandWipSqm += $group->wip_sqm;
                    $grandReadyQty += $group->ready_qty;
                    $grandReadySqm += $group->ready_sqm;
                    $grandSoldQty += $group->sold_qty;
                    $grandSoldSqm += $group->sold_sqm;
                    $grandTotalSqm += $group->total_sqm;
                    $grandTotalCost += $group->total_cost;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-right">{{ $group->quality_name }}</td>
                    <td class="text-right">{{ $group->type_name }}</td>
                    <td class="text-center">{{ number_format($group->wip_qty) }}</td>
                    <td class="text-center">{{ number_format($group->wip_sqm, 2) }} m²</td>
                    <td class="text-center">{{ number_format($group->ready_qty) }}</td>
                    <td class="text-center">{{ number_format($group->ready_sqm, 2) }} m²</td>
                    <td class="text-center">{{ number_format($group->sold_qty) }}</td>
                    <td class="text-center">{{ number_format($group->sold_sqm, 2) }} m²</td>
                    <td class="text-center">{{ number_format($group->total_sqm, 2) }} m²</td>
                    <td class="text-center">$ {{ number_format($group->total_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-right">مجموع کل (Grand Total):</td>
                <td class="text-center">{{ number_format($grandWipQty) }}</td>
                <td class="text-center">{{ number_format($grandWipSqm, 2) }} m²</td>
                <td class="text-center">{{ number_format($grandReadyQty) }}</td>
                <td class="text-center">{{ number_format($grandReadySqm, 2) }} m²</td>
                <td class="text-center">{{ number_format($grandSoldQty) }}</td>
                <td class="text-center">{{ number_format($grandSoldSqm, 2) }} m²</td>
                <td class="text-center">{{ number_format($grandTotalSqm, 2) }} m²</td>
                <td class="text-center">$ {{ number_format($grandTotalCost, 2) }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
