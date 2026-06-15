<?php
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=Purchased_Carpets_Report_" . date('Y-m-d') . ".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
?>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body { font-family: 'Tahoma', Arial, sans-serif; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #1e3a8a; }
        .issue-date { font-size: 14px; color: #475569; margin-top: 5px; }
        .filters { font-size: 13px; background-color: #f8fafc; padding: 10px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
        
        table { border-collapse: collapse; width: 100%; font-family: 'Tahoma', Arial, sans-serif; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: center; vertical-align: middle; }
        
        /* Table Header */
        th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 14px; height: 40px; }
        
        /* Table Body */
        td { font-size: 13px; color: #1e293b; }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
        
        /* Highlighted Columns */
        .col-id { font-weight: bold; color: #0f172a; }
        .col-area, .col-price { font-weight: bold; color: #059669; }
        
        /* Total Row */
        .total-row td { background-color: #e2e8f0; font-weight: bold; color: #0f172a; font-size: 14px; border-top: 2px solid #94a3b8; height: 35px; }
    </style>
</head>
<body>

    <div class="report-header">
        <div class="title">گزارش جامع قالین های خرید شده</div>
        <div class="issue-date">تاریخ صدور گزارش: {{ $issueDate }}</div>
    </div>
    
    <div class="filters">
        <strong>فیلترهای اعمال شده:</strong> 
        @if(request('from_date') || request('to_date')) تاریخ: {{ request('from_date') ?: 'همه' }} تا {{ request('to_date') ?: 'همه' }} | @endif
        @if(request('from_id') || request('to_id')) نمبر پارچه: {{ request('from_id') ?: 'همه' }} تا {{ request('to_id') ?: 'همه' }} | @endif
        @if(request('type_id')) نوعیت: {{ $types->where('carpet_type_id', request('type_id'))->first()->carpet_type ?? 'همه' }} | @endif
        @if(request('quality_id')) کوالیتی: {{ $qualities->where('id', request('quality_id'))->first()->quality ?? 'همه' }} | @endif
        حالت: {{ request('status') != '' ? ($statuses[request('status')] ?? '') : 'همه حالت‌ها' }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="60">ردیف</th>
                <th width="120">شماره پارچه</th>
                <th width="120">تاریخ خرید</th>
                <th width="180">نماینده فروشنده</th>
                <th width="120">نوعیت قالین</th>
                <th width="120">کوالیتی قالین</th>
                <th width="100">طول (m)</th>
                <th width="100">عرض (m)</th>
                <th width="120">مساحت (M2)</th>
                <th width="120">قیمت فی متر ($)</th>
                <th width="120">قیمت کل ($)</th>
                <th width="150">حالت فعلی</th>
            </tr>
        </thead>
        <tbody>
            @foreach($carpets as $index => $carpet)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="col-id">{{ $carpet->carpet_no }}</td>
                <td>{{ \Carbon\Carbon::parse($carpet->date)->format('Y-m-d') }}</td>
                <td>{{ $carpet->agent->user->name ?? 'نامشخص' }} {{ $carpet->agent->user->last_name ?? '' }}</td>
                <td>{{ $carpet->type->carpet_type ?? 'نامشخص' }}</td>
                <td>{{ $carpet->quality->quality ?? 'نامشخص' }}</td>
                <td>{{ $carpet->height }}</td>
                <td>{{ $carpet->width }}</td>
                <td class="col-area">{{ number_format($carpet->area, 2) }}</td>
                <td>{{ number_format($carpet->price, 2) }}</td>
                <td class="col-price">{{ number_format($carpet->total_price, 2) }}</td>
                <td>{{ $statuses[$carpet->status] ?? 'نامشخص' }}</td>
            </tr>
            @endforeach
        </tbody>
        @if($carpets->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="8" style="text-align: left; padding-left: 20px;">مجموع کلی:</td>
                <td class="col-area">{{ number_format($carpets->sum('area'), 2) }}</td>
                <td></td>
                <td class="col-price">{{ number_format($carpets->sum('total_price'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
