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
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            text-align: center;
        }
        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
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
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px double #0f172a;
        }
    </style>
</head>
<body>
    <table>
        <!-- Company / Report Header -->
        <tr>
            <td colspan="7" class="header-title">شرکت تولیدی قالین برادران قاسمی</td>
        </tr>
        <tr>
            <td colspan="7" class="header-subtitle">صورت حساب مالی تفصیلی (Detailed Statement of Account)</td>
        </tr>

        <!-- Filter Details Metadata -->
        <tr>
            <td class="meta-label">نام شخص/حساب:</td>
            <td class="meta-value">{{ $entityName }}</td>
            <td class="meta-label">نوعیت حساب:</td>
            <td colspan="2" class="meta-value">{{ $config['title'] }}</td>
            <td class="meta-label">تاریخ گزارش:</td>
            <td class="meta-value">{{ date('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">از تاریخ:</td>
            <td class="meta-value">{{ request('from_date') ?: 'آغاز دوره' }}</td>
            <td class="meta-label">الی تاریخ:</td>
            <td colspan="2" class="meta-value">{{ request('to_date') ?: 'تا کنون' }}</td>
            <td class="meta-label">موجودی ابتدایی دوره:</td>
            <td class="meta-value font-bold">${{ number_format($openingBalance, 2) }}</td>
        </tr>
        
        <!-- Empty Row Spacer -->
        <tr><td colspan="7" style="border: none; height: 15px;"></td></tr>

        <!-- Data Headers -->
        <thead>
            <tr>
                <th style="width: 15%;">تاریخ سند</th>
                <th style="width: 15%;">شماره دفتر روزنامه</th>
                <th style="width: 15%;">سند مرجع</th>
                <th style="width: 35%;">تفصیلات / شرح تراکنش</th>
                <th style="width: 13%;">دبیت (بردگی)</th>
                <th style="width: 13%;">کریدیت (رسیدگی)</th>
                <th style="width: 14%;">باقی‌مانده (USD)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $runningBalance = $openingBalance;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            
            <!-- Opening Balance Row -->
            <tr class="total-row" style="background-color: #f1f5f9;">
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td>موجودی ابتدایی دوره (Opening Balance)</td>
                <td class="text-left">-</td>
                <td class="text-left">-</td>
                <td class="text-left">${{ number_format($runningBalance, 2) }}</td>
            </tr>

            @foreach($entries as $tx)
                @php
                    $totalDebit += $tx->base_debit;
                    $totalCredit += $tx->base_credit;
                    if ($entityKey === 'customer') {
                        $runningBalance += ($tx->base_debit - $tx->base_credit);
                    } else {
                        $runningBalance += ($tx->base_credit - $tx->base_debit);
                    }
                @endphp
                <tr>
                    <td class="text-center" style="direction: ltr;">{{ $tx->date }}</td>
                    <td class="font-bold text-center">{{ $tx->journal_id }}</td>
                    <td class="text-center">{{ $tx->reference ?: '-' }}</td>
                    <td>{{ $tx->description ?: 'بدون توضیحات' }}</td>
                    <td class="text-left text-danger">{{ $tx->base_debit > 0 ? '$' . number_format($tx->base_debit, 2) : '-' }}</td>
                    <td class="text-left text-success">{{ $tx->base_credit > 0 ? '$' . number_format($tx->base_credit, 2) : '-' }}</td>
                    <td class="text-left font-bold" style="direction: ltr;">${{ number_format($runningBalance, 2) }}</td>
                </tr>
            @endforeach

            <!-- Total Spacer -->
            <tr><td colspan="7" style="border: none; height: 10px;"></td></tr>

            <!-- Totals Rows -->
            <tr class="total-row">
                <td colspan="4" class="text-center">خلاصه کل دوره (Totals for Selected Period)</td>
                <td class="text-left text-danger">${{ number_format($totalDebit, 2) }}</td>
                <td class="text-left text-success">${{ number_format($totalCredit, 2) }}</td>
                <td class="text-left">${{ number_format($runningBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
