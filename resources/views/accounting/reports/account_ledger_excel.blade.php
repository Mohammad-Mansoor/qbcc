@php
    $totalDebit = 0;
    $totalCredit = 0;
    foreach($entries as $tx) {
        $totalDebit += $tx->debit;
        $totalCredit += $tx->credit;
    }
    $finalBalance = $openingBalance;
    foreach($entries as $entry) {
        $normal = (isset($account) && $account->normal_balance == 'credit') ? 'credit' : 'debit';
        if ($normal == 'debit') { $finalBalance += ($entry->debit - $entry->credit); } 
        else { $finalBalance += ($entry->credit - $entry->debit); }
    }
    $accCurrency = isset($account) ? $account->currency : 'USD';
    $accRate = (isset($currencies) && isset($currencies[$accCurrency]) && $currencies[$accCurrency]->exchange_rate > 0) ? $currencies[$accCurrency]->exchange_rate : 1.0;
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <!--[if gte mso 9]>
    <?php $x = 'x:'; echo "<xml>
        <{$x}ExcelWorkbook>
            <{$x}ExcelWorksheets>
                <{$x}ExcelWorksheet>
                    <{$x}Name>Account Ledger</{$x}Name>
                    <{$x}WorksheetOptions>
                        <{$x}DisplayGridlines/>
                        <{$x}FreezePanes/>
                        <{$x}SplitHorizontal>10</{$x}SplitHorizontal>
                        <{$x}TopRowBottomPane>10</{$x}TopRowBottomPane>
                        <{$x}ActivePane>2</{$x}ActivePane>
                    </{$x}WorksheetOptions>
                </{$x}ExcelWorksheet>
            </{$x}ExcelWorksheets>
        </{$x}ExcelWorkbook>
    </xml>"; ?>
    <![endif]-->
    <style>
        body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; direction: rtl; background-color: #ffffff; }
        table { border-collapse: collapse; width: 100%; }
        th, td { font-family: 'Segoe UI', Calibri, Arial, sans-serif; border: 1px solid #cbd5e1; vertical-align: middle; }
        .company-name { font-size: 22pt; font-weight: bold; color: #1e3a8a; text-align: right; }
        .company-subtitle { font-size: 10.5pt; color: #475569; text-align: right; }
        .report-title { font-size: 16pt; font-weight: bold; color: #0f172a; text-align: center; height: 35pt; background-color: #f8fafc; border-bottom: 2px solid #3b82f6; }
        .card-label-debit { font-size: 9.5pt; color: #1e3a8a; background-color: #dbeafe; text-align: center; font-weight: bold; }
        .card-value-debit { font-size: 15pt; color: #2563eb; background-color: #dbeafe; text-align: center; font-weight: bold; }
        .card-label-credit { font-size: 9.5pt; color: #991b1b; background-color: #fee2e2; text-align: center; font-weight: bold; }
        .card-value-credit { font-size: 15pt; color: #dc2626; background-color: #fee2e2; text-align: center; font-weight: bold; }
        .card-label-balance { font-size: 9.5pt; color: #1e3a8a; background-color: #dbeafe; text-align: center; font-weight: bold; }
        .card-value-balance { font-size: 15pt; color: #1e3a8a; background-color: #dbeafe; text-align: center; font-weight: bold; }
        .meta-label { font-weight: bold; background-color: #f1f5f9; color: #1e293b; text-align: right; padding-right: 12px; font-size: 10pt; }
        .meta-value { color: #334155; text-align: right; padding-right: 12px; font-size: 10pt; }
        thead th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11pt; text-align: center; border: 1px solid #1e3a8a; }
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 10px; }
        .text-right { text-align: right; padding-right: 10px; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #2563eb; }
        .font-bold { font-weight: bold; }
        .opening-balance-row td { background-color: #fef9c3; font-weight: bold; color: #1e293b; }
        .total-row td { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11.5pt; border-top: 2px solid #0f172a; border-bottom: 3px double #0f172a; }
    </style>
</head>
<body>
    <table>
        <colgroup>
            <col width="130" style="width: 130px;" />
            <col width="130" style="width: 130px;" />
            <col width="360" style="width: 360px;" />
            <col width="135" style="width: 135px;" />
            <col width="135" style="width: 135px;" />
            <col width="155" style="width: 155px;" />
        </colgroup>
        <tr>
            <td colspan="1" align="center" valign="middle" style="border: none; background-color: #ffffff; height: 70pt;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" width="65" height="65" alt="Logo">
                @else
                    <span style="font-weight: bold; color: #1e3a8a; font-size: 18pt;">QB</span>
                @endif
            </td>
            <td colspan="5" valign="middle" style="border: none; background-color: #ffffff; padding-right: 15px; height: 70pt;">
                <span class="company-name">شرکت صنعتی برادران قاسمی</span><br><br>
                <span class="company-subtitle">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت  افغانستان</span>
            </td>
        </tr>
        <tr style="height: 10pt;"><td colspan="6" style="border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <td colspan="6" class="report-title" valign="middle">دفتر تفصیلی حساب (Account Ledger)</td>
        </tr>
        <tr style="height: 12pt;"><td colspan="6" style="border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <td colspan="2" class="card-label-debit" style="height: 18pt;" valign="middle">مجموع دیبت (Total Debit)</td>
            <td colspan="2" class="card-label-credit" style="height: 18pt;" valign="middle">مجموع کریدت (Total Credit)</td>
            <td colspan="2" class="card-label-balance" style="height: 18pt;" valign="middle">بیلانس نهایی (Closing Balance)</td>
        </tr>
        <tr>
            <td colspan="2" class="card-value-debit" style="height: 32pt;" valign="middle">
                ${{ number_format($totalDebit, 2) }}
                @if($accCurrency !== 'USD')
                    <br><span style="font-size: 9.5pt; font-weight: normal; color: #1e3a8a;">+ {{ number_format($totalDebit / $accRate, 2) }} {{ $accCurrency }}</span>
                @endif
            </td>
            <td colspan="2" class="card-value-credit" style="height: 32pt;" valign="middle">
                ${{ number_format($totalCredit, 2) }}
                @if($accCurrency !== 'USD')
                    <br><span style="font-size: 9.5pt; font-weight: normal; color: #991b1b;">- {{ number_format($totalCredit / $accRate, 2) }} {{ $accCurrency }}</span>
                @endif
            </td>
            <td colspan="2" class="card-value-balance" style="height: 32pt;" valign="middle">
                ${{ number_format($finalBalance, 2) }}
                @if($accCurrency !== 'USD')
                    <br><span style="font-size: 9.5pt; font-weight: normal; color: #1e3a8a;">{{ number_format($finalBalance / $accRate, 2) }} {{ $accCurrency }}</span>
                @endif
            </td>
        </tr>
        <tr style="height: 15pt;"><td colspan="6" style="border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <td class="meta-label" style="height: 24pt;">حساب (Account):</td>
            <td class="meta-value font-bold" style="height: 24pt;">{{ isset($account) ? $account->account_name : 'گزارش تفتیش' }}</td>
            <td class="meta-label" style="height: 24pt;">کد حساب:</td>
            <td class="meta-value" style="height: 24pt;">{{ isset($account) ? $account->account_code : '-' }}</td>
            <td class="meta-label" style="height: 24pt;">تاریخ گزارش:</td>
            <td class="meta-value" style="height: 24pt;">{{ date('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label" style="height: 24pt;">از تاریخ (Start Date):</td>
            <td class="meta-value" style="height: 24pt;">{{ $startDate }}</td>
            <td class="meta-label" style="height: 24pt;">الی تاریخ (End Date):</td>
            <td class="meta-value" style="height: 24pt;">{{ $endDate }}</td>
            <td class="meta-label" style="height: 24pt;">نوعیت حساب:</td>
            <td class="meta-value" style="height: 24pt;">{{ isset($account) ? $account->account_type : '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label" style="height: 24pt;">تهیه کننده (Generated By):</td>
            <td class="meta-value" style="height: 24pt;" colspan="5">{{ Auth::user()->name ?? 'System' }} {{ Auth::user()->last_name ?? '' }}</td>
        </tr>
        <tr style="height: 15pt;"><td colspan="6" style="border: none; background-color: #ffffff;"></td></tr>
        <thead>
            <tr>
                <th width="130" style="height: 32pt;">تاریخ سند (Date)</th>
                <th width="130" style="height: 32pt;">سند مرجع (Ref)</th>
                <th width="360" style="height: 32pt;">تفصیلات / شرح تراکنش (Description)</th>
                <th width="135" style="height: 32pt;">دیبت (Debit)</th>
                <th width="135" style="height: 32pt;">کریدیت (Credit)</th>
                <th width="155" style="height: 32pt;">بیلانس (Balance)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $runningBalance = $openingBalance;
            @endphp
            <tr class="opening-balance-row">
                <td class="text-center" style="height: 26pt;">-</td>
                <td class="text-center" style="height: 26pt;">-</td>
                <td class="text-right font-bold" style="height: 26pt; padding-right: 12px;">بیلانس انتقالی (Opening Balance Forwarded)</td>
                <td class="text-left" style="height: 26pt;">-</td>
                <td class="text-left" style="height: 26pt;">-</td>
                <td class="text-left font-bold" style="direction: ltr; height: 26pt; padding-left: 12px;">
                    ${{ number_format($runningBalance, 2) }}
                    @if($accCurrency !== 'USD')
                        <br><span style="font-size: 8.5pt; font-weight: normal; color: #475569;">{{ number_format($runningBalance / $accRate, 2) }} {{ $accCurrency }}</span>
                    @endif
                </td>
            </tr>
            @foreach($entries as $tx)
                @php
                    $normal = (isset($account) && $account->normal_balance == 'credit') ? 'credit' : 'debit';
                    if ($normal == 'debit') { $runningBalance += ($tx->debit - $tx->credit); } 
                    else { $runningBalance += ($tx->credit - $tx->debit); }
                    $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff';
                @endphp
                <tr style="background-color: {{ $rowBgColor }};">
                    <td class="text-center" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $tx->date }}</td>
                    <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $tx->reference ?: '-' }}</td>
                    <td class="text-right" style="height: 26pt; padding-right: 12px; background-color: {{ $rowBgColor }};">{{ $tx->description ?: 'بدون توضیحات' }}</td>
                    <td class="text-left text-primary" style="height: 26pt; padding-left: 12px; background-color: {{ $rowBgColor }};">
                        {{ $tx->debit > 0 ? '$' . number_format($tx->debit, 2) : '-' }}
                    </td>
                    <td class="text-left text-danger" style="height: 26pt; padding-left: 12px; background-color: {{ $rowBgColor }};">
                        {{ $tx->credit > 0 ? '$' . number_format($tx->credit, 2) : '-' }}
                    </td>
                    <td class="text-left font-bold" style="direction: ltr; height: 26pt; padding-left: 12px; background-color: {{ $rowBgColor }};">
                        ${{ number_format($runningBalance, 2) }}
                        @if($accCurrency !== 'USD')
                            <br><span style="font-size: 8.5pt; font-weight: normal; color: #475569;">{{ number_format($runningBalance / $accRate, 2) }} {{ $accCurrency }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr style="height: 10pt;"><td colspan="6" style="border: none; background-color: #ffffff;"></td></tr>
            <tr class="total-row">
                <td colspan="3" class="text-center" style="height: 32pt;">خلاصه کل دوره (Totals for Selected Period)</td>
                <td class="text-left" style="height: 32pt; padding-left: 12px;">
                    ${{ number_format($totalDebit, 2) }}
                    @if($accCurrency !== 'USD')
                        <br><span style="font-size: 9.5pt; font-weight: normal; color: #e2e8f0;">+ {{ number_format($totalDebit / $accRate, 2) }} {{ $accCurrency }}</span>
                    @endif
                </td>
                <td class="text-left" style="height: 32pt; padding-left: 12px;">
                    ${{ number_format($totalCredit, 2) }}
                    @if($accCurrency !== 'USD')
                        <br><span style="font-size: 9.5pt; font-weight: normal; color: #fca5a5;">- {{ number_format($totalCredit / $accRate, 2) }} {{ $accCurrency }}</span>
                    @endif
                </td>
                <td class="text-left font-bold" style="direction: ltr; height: 32pt; padding-left: 12px;">
                    ${{ number_format($runningBalance, 2) }}
                    @if($accCurrency !== 'USD')
                        <br><span style="font-size: 9.5pt; font-weight: normal; color: #e2e8f0;">{{ number_format($runningBalance / $accRate, 2) }} {{ $accCurrency }}</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
