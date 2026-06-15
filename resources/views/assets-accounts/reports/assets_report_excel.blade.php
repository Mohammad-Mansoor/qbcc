<?php
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=Assets_Report_" . date('Y-m-d') . ".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel to read Persian characters correctly
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body>

    <table border="0" style="margin-bottom: 20px;">
        <tr>
            <td colspan="8" style="text-align: center; font-size: 16pt; font-weight: bold; padding: 10px;">گزارش جامع اجناس ثابت (Fixed Assets Report)</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">
                تاریخ صدور گزارش: {{ $issueDate }}<br>
                از تاریخ خرید: {{ request('from_date') ?: 'همه' }} | تا تاریخ خرید: {{ request('to_date') ?: 'همه' }}<br>
                نمبر جنس: {{ request('asset_number') ?: 'همه' }}
            </td>
            <td colspan="4" style="text-align: right;">
                مجموع اقلام: {{ $assets->count() }} قلم<br>
                کتگوری/حساب: {{ request('account_id') ? ($accounts->where('aa_id', request('account_id'))->first()->aa_name ?? 'همه') : 'همه' }}<br>
                کلاس جنس: {{ request('asset_class') ?: 'همه' }}
            </td>
        </tr>
    </table>

    <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
        <thead>
            <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold;">
                <th>ردیف</th>
                <th>اسم جنس</th>
                <th>تفصیلات</th>
                <th>نمبر جنس</th>
                <th>سریال نمبر</th>
                <th>حساب / کتگوری</th>
                <th>کلاس</th>
                <th>موقعیت فزیکی</th>
                <th>تاریخ خرید</th>
                <th>قیمت خرید</th>
                <th>اسعار</th>
                <th>ارزش اسقاط</th>
                <th>عمر مفید (سال)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $asset->asset_name }}</td>
                <td>{{ $asset->asset_description }}</td>
                <td>{{ $asset->asset_number }}</td>
                <td>{{ $asset->asset_serial_number }}</td>
                <td>{{ $asset->aa_name }}</td>
                <td>{{ $asset->asset_class }}</td>
                <td>{{ $asset->physical_location }}</td>
                <td>{{ \Carbon\Carbon::parse($asset->acquisition_date)->format('Y-m-d') }}</td>
                <td>{{ number_format($asset->acquisition_cost, 2) }}</td>
                <td>{{ $asset->currency_code ?? 'USD' }}</td>
                <td>{{ number_format($asset->estimated_salvage_value, 2) }}</td>
                <td>{{ $asset->estimated_useful_life }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" style="text-align: center;">هیچ جنسی یافت نشد.</td>
            </tr>
            @endforelse
        </tbody>
        @if($assets->count() > 0)
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="9" style="text-align: center;">مجموع کلی</td>
                <td>{{ number_format($assets->sum('acquisition_cost'), 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
