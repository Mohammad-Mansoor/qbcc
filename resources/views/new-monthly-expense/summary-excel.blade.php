<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Expense_Summary_{$month_obj->month_name}_{$month_obj->year}.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo "\xEF\xBB\xBF"; // UTF-8 BOM
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th colspan="3" style="background-color: #1e3a8a; color: #ffffff; font-size: 14pt;">گزارش خلاصه مصارف ماهانه - شرکت صنعتی برادران قاسمی</th>
            </tr>
            <tr>
                <th style="background-color: #e2e8f0; font-weight: bold;">نام ماه</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">شماره ماه</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">سال</th>
            </tr>
            <tr>
                <td>{{ $month_obj->month_name }}</td>
                <td>{{ $month_obj->month_number }}</td>
                <td>{{ $month_obj->year_name }}</td>
            </tr>
            <tr><td colspan="4"></td></tr>
            <tr>
                <th style="background-color: #1e40af; color: #ffffff;">دسته‌بندی (Category)</th>
                <th style="background-color: #1e40af; color: #ffffff;">ارز (Currency)</th>
                <th style="background-color: #1e40af; color: #ffffff;">مبلغ اصلی (Original Amount)</th>
                <th style="background-color: #1e40af; color: #ffffff;">معادل دالر (Base USD)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotalBase = 0; @endphp
            @foreach($categoryTotals as $categoryName => $groupedItems)
                @foreach($groupedItems as $item)
                    @php $grandTotalBase += $item->total_base; @endphp
                    <tr>
                        <td style="font-weight: bold;">{{ $categoryName }}</td>
                        <td style="text-align: center;">{{ $item->currency_code }}</td>
                        <td style="text-align: left;">{{ number_format($item->total_original, 2) }}</td>
                        <td style="text-align: left;">{{ number_format($item->total_base, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: right;">مجموع کل مصارف (معادل دالر USD)</td>
                <td style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: left;">{{ number_format($grandTotalBase, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
