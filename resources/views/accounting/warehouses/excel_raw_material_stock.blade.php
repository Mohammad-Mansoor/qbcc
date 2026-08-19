<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Tahoma, Arial, sans-serif; direction: rtl; }
        .header-title { font-size: 14pt; font-weight: bold; color: #3730a3; text-align: center; }
        .filter-header { background-color: #f1f5f9; font-weight: bold; }
        .table-header { background-color: #3730a3; color: #ffffff; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .grand-total { background-color: #e2e8f0; font-weight: bold; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td colspan="9" class="header-title">گزارش موجودی مواد خام (Raw Material Available Stock Report)</td>
        </tr>
        <tr>
            <td colspan="4" class="text-right">تاریخ گزارش: {{ date('Y-m-d H:i') }}</td>
            <td colspan="5" class="text-left">توسط: {{ auth()->user()->name }}</td>
        </tr>
        <tr><td colspan="9"></td></tr>
        
        <!-- Applied Filters -->
        <tr class="filter-header">
            <td colspan="9">
                فیلترهای اعمال شده: دسته مواد: {{ $selectedCategoryName }} | نوع مواد: {{ $selectedTypeName }}
            </td>
        </tr>
        <tr><td colspan="9"></td></tr>

        <!-- Summary Metrics -->
        <tr>
            <td colspan="3" style="background-color: #ecfeff; font-weight: bold;">موجودی رنگ:</td>
            <td colspan="2" style="background-color: #ecfeff;">{{ number_format($totalAvailableDyeKg, 2) }} KG ($ {{ number_format($totalAvailableDyeCost, 2) }})</td>
            <td colspan="2" style="background-color: #e0e7ff; font-weight: bold;">موجودی تار/الیاف:</td>
            <td colspan="2" style="background-color: #e0e7ff;">{{ number_format($totalAvailableYarnKg, 2) }} KG ($ {{ number_format($totalAvailableYarnCost, 2) }})</td>
        </tr>
        <tr>
            <td colspan="3" style="background-color: #fffbeb; font-weight: bold;">رنگ فروخته شده:</td>
            <td colspan="2" style="background-color: #fffbeb;">{{ number_format($totalSoldDyeKg, 2) }} KG ($ {{ number_format($totalSoldDyeCost, 2) }})</td>
            <td colspan="2" style="background-color: #fff1f2; font-weight: bold;">تار فروخته شده:</td>
            <td colspan="2" style="background-color: #fff1f2;">{{ number_format($totalSoldYarnKg, 2) }} KG ($ {{ number_format($totalSoldYarnCost, 2) }})</td>
        </tr>
        <tr><td colspan="9"></td></tr>

        <!-- Data Table -->
        <thead>
            <tr class="table-header">
                <th>#</th>
                <th>دسته مواد (Category)</th>
                <th>نوع مواد (Material Type)</th>
                <th>موجودی فعال (Avail. KG)</th>
                <th>ارزش موجودی ($ USD)</th>
                <th>فروخته شده (Sold KG)</th>
                <th>ارزش فروش ($ USD)</th>
                <th>کل خرید (Purchased KG)</th>
                <th>ارزش کل خرید ($ USD)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandAvailKg = 0; $grandAvailCost = 0;
                $grandSoldKg = 0; $grandSoldCost = 0;
                $grandPurchasedKg = 0; $grandPurchasedCost = 0;
            @endphp
            @foreach($groupedMaterials as $index => $mat)
                @php
                    $grandAvailKg += $mat['avail_kg'];
                    $grandAvailCost += $mat['avail_cost'];
                    $grandSoldKg += $mat['sold_kg'];
                    $grandSoldCost += $mat['sold_cost'];
                    $grandPurchasedKg += $mat['purchased_kg'];
                    $grandPurchasedCost += $mat['purchased_cost'];
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-right">{{ $mat['category_name'] }}</td>
                    <td class="text-right">{{ $mat['type_name'] }}</td>
                    <td class="text-center">{{ number_format($mat['avail_kg'], 2) }} KG</td>
                    <td class="text-center">$ {{ number_format($mat['avail_cost'], 2) }}</td>
                    <td class="text-center">{{ number_format($mat['sold_kg'], 2) }} KG</td>
                    <td class="text-center">$ {{ number_format($mat['sold_cost'], 2) }}</td>
                    <td class="text-center">{{ number_format($mat['purchased_kg'], 2) }} KG</td>
                    <td class="text-center">$ {{ number_format($mat['purchased_cost'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-right">مجموع کل (Grand Total):</td>
                <td class="text-center">{{ number_format($grandAvailKg, 2) }} KG</td>
                <td class="text-center">$ {{ number_format($grandAvailCost, 2) }}</td>
                <td class="text-center">{{ number_format($grandSoldKg, 2) }} KG</td>
                <td class="text-center">$ {{ number_format($grandSoldCost, 2) }}</td>
                <td class="text-center">{{ number_format($grandPurchasedKg, 2) }} KG</td>
                <td class="text-center">$ {{ number_format($grandPurchasedCost, 2) }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
