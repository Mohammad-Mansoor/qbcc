<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Trial_Balance_" . date('Y_m_d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
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
    </style>
</head>
<body>
    <table style="width: 100%;">
        <tr>
            <th colspan="4" style="text-align: center; font-size: 16pt;">بیلانس آزمایشی (Trial Balance)</th>
        </tr>
        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="2"><strong>تا تاریخ (As Of):</strong> از {{ $startDate }} الی {{ $endDate }}</td>
            <td colspan="2"><strong>تاریخ استخراج:</strong> {{ date('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th>کد حساب (Code)</th>
            <th>نام حساب (Account Name)</th>
            <th>دیبت (Debit)</th>
            <th>کریدت (Credit)</th>
        </tr>
        
        @php 
            $totalDebit = 0;
            $totalCredit = 0;
        @endphp

        @foreach($report as $row)
            @php
                $isDebit = $row->balance > 0;
                $isCredit = $row->balance < 0;
                
                if ($isDebit) $totalDebit += abs($row->balance);
                if ($isCredit) $totalCredit += abs($row->balance);
            @endphp
            <tr>
                <td class="text-center">{{ $row->account_code }}</td>
                <td>{{ $row->account_name }}</td>
                <td class="text-left" style="direction: ltr;">{{ $isDebit ? abs($row->balance) : '-' }}</td>
                <td class="text-left" style="direction: ltr;">{{ $isCredit ? abs($row->balance) : '-' }}</td>
            </tr>
        @endforeach
        
        <tr>
            <td colspan="2" class="text-center font-bold">مجموع (Total):</td>
            <td class="text-left font-bold" style="direction: ltr;">{{ $totalDebit }}</td>
            <td class="text-left font-bold" style="direction: ltr;">{{ $totalCredit }}</td>
        </tr>
    </table>
</body>
</html>
