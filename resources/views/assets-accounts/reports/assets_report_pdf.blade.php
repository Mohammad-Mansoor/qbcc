<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش اجناس ثابت شرکت</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        body { font-family: 'Tahoma', Arial, sans-serif; background-color: #fff; color: #1e293b; font-size: 10pt; line-height: 1.5; margin: 0; padding: 0; }
        
        .fixed-header { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .fixed-header img { width: 100%; display: block; }
        
        .fixed-footer { position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1000; }
        .fixed-footer img { width: 100%; display: block; }

        .header-space { height: 100px; }
        .footer-space { height: 120px; }

        .content-wrapper { padding-left: 3mm; padding-right: 3mm; }
        
        .header { text-align: center; border-bottom: 2px solid #00acc1; padding-bottom: 10px; margin-top: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 18pt; color: #0A192F; margin: 0 0 5px 0; }
        .header p { font-size: 10pt; color: #64748b; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .meta-table td { padding: 5px; vertical-align: top; }
        .meta-label { font-weight: bold; color: #0f172a; font-size: 9pt; display: block; margin-bottom: 3px; }
        .meta-val { color: #334155; font-size: 9pt; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #cbd5e1; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 10px 8px; text-align: right; font-size: 8.5pt; }
        table.data-table th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 9pt; }
        table.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.data-table tbody tr:nth-child(odd) { background-color: #ffffff; }
        
        .total-row td { background-color: #e2e8f0; font-weight: bold; color: #0f172a; font-size: 10pt; border-top: 2px solid #94a3b8; }
        
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        
        .footer { text-align: center; font-size: 8pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 20px; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    @if($headerBase64)
    <div class="fixed-header">
        <img src="{{ $headerBase64 }}" alt="Header">
    </div>
    @endif
    
    @if($footerBase64)
    <div class="fixed-footer">
        <img src="{{ $footerBase64 }}" alt="Footer">
    </div>
    @endif

    <table style="width: 100%; border: none;">
        <thead>
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="header-space"></div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="content-wrapper">
                        
                        <div class="header">
                            <h1>گزارش جامع اجناس ثابت (Fixed Assets Report)</h1>
                            <p>سیستم مدیریت یکپارچه - بخش مدیریت دارایی‌ها</p>
                        </div>

                        <table class="meta-table">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="meta-label">پارامترهای فیلتر (Filters):</span>
                                    <div class="meta-val">
                                        از تاریخ: <strong>{{ request('from_date') ?: 'همه' }}</strong> | تا تاریخ: <strong>{{ request('to_date') ?: 'همه' }}</strong><br>
                                        کتگوری/حساب: <strong>{{ request('account_id') ? ($accounts->where('aa_id', request('account_id'))->first()->aa_name ?? 'همه') : 'همه' }}</strong><br>
                                        کلاس جنس: <strong>{{ request('asset_class') ?: 'همه' }}</strong> | نمبر جنس: <strong>{{ request('asset_number') ?: 'همه' }}</strong>
                                    </div>
                                </td>
                                <td style="width: 50%; text-align: left; direction: ltr;">
                                    <span class="meta-label" style="text-align: right;">جزئیات گزارش (Report Details):</span>
                                    <div class="meta-val" style="text-align: right;">
                                        تاریخ صدور (Issue Date): <strong>{{ $issueDate }}</strong><br>
                                        مجموع اقلام (Total Items): <strong>{{ number_format($assets->count()) }} قلم</strong><br>
                                        مجموع ارزش اولیه (Total Base Cost): <strong>${{ number_format($assets->sum('acquisition_cost'), 2) }}</strong>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">ردیف</th>
                                    <th style="width: 15%;">اسم جنس</th>
                                    <th style="width: 10%;">نمبر جنس</th>
                                    <th style="width: 15%;">حساب / کتگوری</th>
                                    <th style="width: 12%;">کلاس</th>
                                    <th style="width: 12%;">تاریخ خرید</th>
                                    <th class="text-center" style="width: 15%;">قیمت خرید</th>
                                    <th class="text-center" style="width: 16%;">اسقاط / عمر مفید</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $index => $asset)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $asset->asset_name }}</strong></td>
                                    <td>{{ $asset->asset_number }}</td>
                                    <td>{{ $asset->aa_name }}</td>
                                    <td>{{ $asset->asset_class }}</td>
                                    <td>{{ \Carbon\Carbon::parse($asset->acquisition_date)->format('Y-m-d') }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">{{ number_format($asset->acquisition_cost, 2) }} {{ $asset->currency_code ?? '$' }}</td>
                                    <td class="text-center">
                                        {{ number_format($asset->estimated_salvage_value, 2) }} / {{ $asset->estimated_useful_life }} سال
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center" style="padding: 20px;">هیچ جنسی مطابق با فیلترهای اعمال شده یافت نشد.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($assets->count() > 0)
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="6" class="text-center">مجموع کلی (Grand Total)</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($assets->sum('acquisition_cost'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>

                        <div class="footer">
                            این سند به صورت خودکار توسط سیستم صادر شده است و بدون امضا و مهر فاقد اعتبار فیزیکی می‌باشد. <br>
                            Generated on {{ $issueDate }} by ERP System
                        </div>

                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="footer-space"></div>
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
