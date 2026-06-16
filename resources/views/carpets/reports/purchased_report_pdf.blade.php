<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش قالین های خرید شده</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        body { font-family: 'Tahoma', Arial, sans-serif; background-color: #fff; color: #1e293b; font-size: 10pt; line-height: 1.5; margin: 0; padding: 0; }
        
        .content-wrapper { padding-left: 5mm; padding-right: 5mm; padding-top: 5mm; }
        
        .header { text-align: center; border-bottom: 2px solid #00acc1; padding-bottom: 15px; margin-bottom: 25px; }
        .header img { width: 80px; height: 80px; object-fit: contain; margin-bottom: 5px; }
        .header h1.company-name { font-size: 20pt; color: #1e3a8a; margin: 0 0 5px 0; font-weight: bold; }
        .header p.company-slogan { font-size: 10.5pt; color: #475569; margin: 0 0 15px 0; }
        .header h2.report-title { font-size: 15pt; color: #0f172a; margin: 0; background-color: #f8fafc; padding: 8px 20px; border-radius: 8px; display: inline-block; border: 1px solid #e2e8f0; }
        
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

    <div class="content-wrapper">
        
        <div class="header">
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo">
            @endif
            <h1 class="company-name">شرکت صنعتی برادران قاسمی</h1>
            <p class="company-slogan">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت افغانستان</p>

            @php
                $statusFilter = request('status') != '' && isset($statuses[request('status')]) 
                    ? $statuses[request('status')] 
                    : 'تمامی حالت‌ها (All Statuses)';
            @endphp
            <h2 class="report-title">گزارش قالین های خرید شده - {{ $statusFilter }}</h2>
        </div>

                        <table class="meta-table">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="meta-label">پارامترهای فیلتر (Filters):</span>
                                    <div class="meta-val">
                                        از تاریخ: <strong>{{ request('from_date') ?: 'همه' }}</strong> | تا تاریخ: <strong>{{ request('to_date') ?: 'همه' }}</strong><br>
                                        از نمبر پارچه: <strong>{{ request('from_id') ?: 'همه' }}</strong> | تا نمبر پارچه: <strong>{{ request('to_id') ?: 'همه' }}</strong><br>
                                        نوعیت: <strong>{{ request('type_id') ? ($types->where('carpet_type_id', request('type_id'))->first()->carpet_type ?? 'همه') : 'همه' }}</strong> | 
                                        کوالیتی: <strong>{{ request('quality_id') ? ($qualities->where('id', request('quality_id'))->first()->quality ?? 'همه') : 'همه' }}</strong><br>
                                        حالت فعلی: <strong>{{ request('status') != '' ? ($statuses[request('status')] ?? 'همه') : 'همه حالت‌ها' }}</strong>
                                    </div>
                                </td>
                                <td style="width: 50%; text-align: left; direction: ltr;">
                                    <span class="meta-label" style="text-align: right;">جزئیات گزارش (Report Details):</span>
                                    <div class="meta-val" style="text-align: right;">
                                        تاریخ صدور (Issue Date): <strong>{{ $issueDate }}</strong><br>
                                        مجموع قالین‌ها (Total Count): <strong>{{ number_format($carpets->count()) }} تخته</strong><br>
                                        مجموع مساحت (Total Area): <strong>{{ number_format($carpets->sum('area'), 2) }} M<sup>2</sup></strong>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 4%;">ردیف</th>
                                    <th style="width: 8%;">شماره پارچه</th>
                                    <th style="width: 8%;">نوعیت</th>
                                    <th style="width: 8%;">کوالیتی</th>
                                    <th style="width: 8%;">شماره نقشه</th>
                                    <th class="text-center" style="width: 5%;">طول</th>
                                    <th class="text-center" style="width: 5%;">عرض</th>
                                    <th class="text-center" style="width: 6%;">مساحت</th>
                                    <th class="text-center" style="width: 8%;">قیمت خرید</th>
                                    <th class="text-center" style="width: 8%;">مصارف ترمیم</th>
                                    <th class="text-center" style="width: 8%;">مصارف شست</th>
                                    <th class="text-center" style="width: 8%;">مصارف تکمیلی</th>
                                    <th class="text-center" style="width: 8%;">مبلغ فروش</th>
                                    <th style="width: 8%;">حالت فعلی</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sum_purchase = 0;
                                    $sum_repair = 0;
                                    $sum_wash = 0;
                                    $sum_finish = 0;
                                    $sum_sold = 0;
                                @endphp
                                @forelse($carpets as $index => $carpet)
                                @php
                                    $repair_cost = $carpet->repair ? $carpet->repair->sum('total_price') : 0;
                                    $wash_cost = $carpet->carpet_wash ? $carpet->carpet_wash->total_price : 0;
                                    $finish_cost = $carpet->finishing_works ? $carpet->finishing_works->sum('price') : 0;
                                    $sold_amount = $carpet->sale ? $carpet->sale->sale_cost_total : 0;

                                    $sum_purchase += $carpet->total_price;
                                    $sum_repair += $repair_cost;
                                    $sum_wash += $wash_cost;
                                    $sum_finish += $finish_cost;
                                    $sum_sold += $sold_amount;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $carpet->carpet_no }}</strong></td>
                                    <td>{{ $carpet->type->carpet_type ?? '-' }}</td>
                                    <td>{{ $carpet->quality->quality ?? '-' }}</td>
                                    <td>{{ $carpet->map_number ?? '-' }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $carpet->height }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $carpet->width }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">{{ number_format($carpet->area, 2) }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">${{ number_format($carpet->total_price, 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $repair_cost > 0 ? '$'.number_format($repair_cost, 2) : '-' }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $wash_cost > 0 ? '$'.number_format($wash_cost, 2) : '-' }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $finish_cost > 0 ? '$'.number_format($finish_cost, 2) : '-' }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold; color: #059669;">{{ $sold_amount > 0 ? '$'.number_format($sold_amount, 2) : '-' }}</td>
                                    <td>{{ $statuses[$carpet->status] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center" style="padding: 20px;">هیچ قالینی مطابق با فیلترهای اعمال شده یافت نشد.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($carpets->count() > 0)
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="7" class="text-center">مجموع کلی (Grand Total)</td>
                                    <td class="text-center" style="direction: ltr;">{{ number_format($carpets->sum('area'), 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($sum_purchase, 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($sum_repair, 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($sum_wash, 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($sum_finish, 2) }}</td>
                                    <td class="text-center" style="direction: ltr; color: #059669;">${{ number_format($sum_sold, 2) }}</td>
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

</body>
</html>
