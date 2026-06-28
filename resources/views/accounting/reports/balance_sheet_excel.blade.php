<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Balance_Sheet_" . date('Y_m_d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$totalAssets = $assets->sum('balance');
$totalLiabEquity = $liabilities->sum('balance') + $equity->sum('balance') + $currentNetProfit;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Tahoma, Arial, sans-serif; direction: rtl; }
        th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #000000; padding: 5px; }
        td { border: 1px solid #000000; padding: 5px; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .bg-light { background-color: #f1f5f9; }
        .title-row { font-size: 16pt; font-weight: bold; text-align: center; }
        .header-cell { background-color: #f8fafc; font-weight: bold; color: #64748b; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="2" class="title-row">ترازنامه (Balance Sheet)</th>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td><strong>به تاریخ (As Of):</strong> {{ $endDate }}</td>
            <td><strong>تاریخ استخراج:</strong> {{ date('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        
        <!-- ASSETS -->
        <tr>
            <th colspan="2" style="background-color: #1d4ed8;">دارایی‌ها (Assets)</th>
        </tr>
        <tr>
            <td class="header-cell">شرح حساب (Account Name)</td>
            <td class="header-cell">مبلغ (Amount USD)</td>
        </tr>
        @foreach($assets as $row)
        <tr>
            <td>{{ $row->account_name }}</td>
            <td class="text-left" style="direction: ltr;">{{ $row->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td class="font-bold text-center">مجموع دارایی‌ها (Total Assets)</td>
            <td class="font-bold text-left" style="direction: ltr;">{{ $totalAssets }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>

        <!-- LIABILITIES & EQUITY -->
        <tr>
            <th colspan="2" style="background-color: #6b21a8;">بدهی و سرمایه (Liabilities & Equity)</th>
        </tr>
        <tr>
            <td class="header-cell">شرح حساب (Account Name)</td>
            <td class="header-cell">مبلغ (Amount USD)</td>
        </tr>
        
        <tr><td colspan="2" class="bg-light font-bold">بدهی‌ها (Liabilities)</td></tr>
        @foreach($liabilities as $row)
        <tr>
            <td>{{ $row->account_name }}</td>
            <td class="text-left" style="direction: ltr;">{{ $row->balance }}</td>
        </tr>
        @endforeach
        
        <tr><td colspan="2" class="bg-light font-bold">سرمایه و سود (Equity & Profit)</td></tr>
        @foreach($equity as $row)
        <tr>
            <td>{{ $row->account_name }}</td>
            <td class="text-left" style="direction: ltr;">{{ $row->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td>سود/ضرر دوره جاری (Current P&L)</td>
            <td class="text-left" style="direction: ltr;">{{ $currentNetProfit }}</td>
        </tr>
        <tr>
            <td class="font-bold text-center">مجموع بدهی و سرمایه (Total Liab & Equity)</td>
            <td class="font-bold text-left" style="direction: ltr;">{{ $totalLiabEquity }}</td>
        </tr>
    </table>
</body>
</html>
