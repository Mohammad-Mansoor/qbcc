<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Journal_Report_" . date('Y_m_d') . ".xls");
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
            <th colspan="6" style="text-align: center; font-size: 16pt;">راپور روزنامچه کل (General Ledger)</th>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td colspan="3"><strong>از تاریخ:</strong> {{ request('start_date') }}</td>
            <td colspan="3"><strong>الی تاریخ:</strong> {{ request('end_date') }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>تاریخ استخراج:</strong> {{ date('Y-m-d H:i') }}</td>
            <td colspan="3"><strong>تهیه کننده:</strong> {{ Auth::user()->name ?? 'System' }} {{ Auth::user()->last_name ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
        <tr>
            <th>تاریخ</th>
            <th>شناسه سند</th>
            <th>تفصیلات</th>
            <th>حساب</th>
            <th>دیبت</th>
            <th>کریدت</th>
        </tr>
        
        @php 
            $totalDebit = 0;
            $totalCredit = 0;
        @endphp

        @foreach($transactions as $tx)
            @foreach($tx->entries as $entry)
            @php 
                $totalDebit += $entry->debit;
                $totalCredit += $entry->credit;
            @endphp
            <tr>
                <td class="text-center">{{ $tx->date }}</td>
                <td class="text-center">{{ $tx->journal_id }} @if($tx->reference) ({{ $tx->reference }}) @endif</td>
                <td>{{ $tx->description ?: 'بدون توضیحات' }}</td>
                <td class="text-center">{{ $entry->account ? $entry->account->account_code . ' - ' . ($entry->account->name_da ?? $entry->account->name_en) : 'N/A' }}</td>
                <td class="text-left" style="direction: ltr;">{{ $entry->debit > 0 ? $entry->debit : '-' }}</td>
                <td class="text-left" style="direction: ltr;">{{ $entry->credit > 0 ? $entry->credit : '-' }}</td>
            </tr>
            @endforeach
        @endforeach
        
        <tr>
            <td colspan="4" class="text-center font-bold">مجموع (Totals)</td>
            <td class="text-left font-bold" style="direction: ltr;">{{ $totalDebit }}</td>
            <td class="text-left font-bold" style="direction: ltr;">{{ $totalCredit }}</td>
        </tr>
    </table>
</body>
</html>
