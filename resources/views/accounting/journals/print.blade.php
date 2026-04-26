<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Voucher - {{ $transaction->reference }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            direction: rtl;
            background: #fff;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .wrapper {
            width: 100%;
            max-width: 210mm;
            margin: auto;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 26px;
            font-weight: bold;
            text-align: center;
        }
        .company-subtitle {
            font-size: 14px;
            text-align: center;
            color: #555;
        }
        .voucher-header {
            width: 100%;
            margin-bottom: 20px;
        }
        .voucher-title-box {
            border: 2px solid #000;
            background-color: #eee !important;
            padding: 10px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .info-table td {
            padding: 5px;
        }
        .description-container {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            min-height: 50px;
            font-size: 14px;
        }
        .description-label {
            font-weight: bold;
            text-decoration: underline;
            display: block;
            margin-bottom: 5px;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .ledger-table th, .ledger-table td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 13px;
        }
        .ledger-table th {
            background-color: #333 !important;
            color: #fff !important;
            text-align: center;
            -webkit-print-color-adjust: exact;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; } /* For numbers */
        .text-right { text-align: right; } /* For Dari text */
        
        .total-row td {
            font-weight: bold;
            background-color: #f0f0f0 !important;
            font-size: 15px;
            -webkit-print-color-adjust: exact;
        }
        .footer-table {
            width: 100%;
            margin-top: 60px;
            text-align: center;
        }
        .footer-table td {
            width: 33.33%;
            padding-top: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: auto;
            padding-top: 5px;
            font-weight: bold;
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">چاپ سند (Print)</button>

<div class="wrapper">
    <!-- Main Header -->
    <table class="header-table">
        <tr>
            <td class="company-name">QASIMI BROTHERS CARPET CO.</td>
        </tr>
        <tr>
            <td class="company-subtitle">شرکت تولیدی قـاسمی بـرادران - بخش امور مالی</td>
        </tr>
    </table>

    <!-- Voucher Title -->
    <div class="voucher-header">
        <div class="voucher-title-box">
            @php
                $titles = [
                    'payment' => 'سند تادیاتی (Payment Voucher)',
                    'receipt' => 'سند رسید (Receipt Voucher)',
                    'journal' => 'سند روزنامچه (Journal Voucher)',
                    'sales' => 'سند فروشات (Sales Voucher)',
                    'adjustment' => 'سند تعدیلی (Adjustment Voucher)',
                ];
                echo $titles[$transaction->journal_type] ?? 'سند حسابداری (Accounting Voucher)';
            @endphp
        </div>
    </div>

    <!-- Metadata Info -->
    <table class="info-table">
        <tr>
            <td style="width: 15%;"><strong>نمبر سند:</strong></td>
            <td style="width: 35%;">{{ $transaction->reference }}</td>
            <td style="width: 15%;"><strong>تاریخ:</strong></td>
            <td style="width: 35%;">{{ $transaction->date }}</td>
        </tr>
    </table>

    <!-- Narrative -->
    <div class="description-container">
        <span class="description-label">شرح سند (Narrative):</span>
        {{ $transaction->description }}
    </div>

    <!-- Ledger Entries -->
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 15%;">کد حساب</th>
                <th style="width: 45%;">نام حساب (Account Name)</th>
                <th style="width: 20%;">دیبت (Debit)</th>
                <th style="width: 20%;">کریدت (Credit)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->entries as $entry)
            <tr>
                <td class="text-center">{{ $entry->account->account_code }}</td>
                <td class="text-right">{{ $entry->account->account_name }}</td>
                <td class="text-left font-weight-bold">
                    {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                </td>
                <td class="text-left font-weight-bold">
                    {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-center">مجموع نهایی (Total Amount)</td>
                <td class="text-left font-weight-bold">${{ number_format($transaction->entries->sum('debit'), 2) }}</td>
                <td class="text-left font-weight-bold">${{ number_format($transaction->entries->sum('credit'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <table class="footer-table">
        <tr>
            <td>
                <div class="signature-line">ترتیب کننده</div>
                <small>(Prepared By)</small>
            </td>
            <td>
                <div class="signature-line">کنترل کننده</div>
                <small>(Checked By)</small>
            </td>
            <td>
                <div class="signature-line">مدیریت مالی</div>
                <small>(Approved By)</small>
            </td>
        </tr>
    </table>

    <div style="margin-top: 50px; font-size: 11px; text-align: left; color: #888;">
        Printed on: {{ date('Y-m-d H:i:s') }} | QBCC Finance System
    </div>
</div>

</body>
</html>
