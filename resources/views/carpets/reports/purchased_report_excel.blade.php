<?php
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=Purchased_Carpets_Report_" . date('Y-m-d') . ".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
?>
@php
    $sum_purchase = 0;
    $sum_repair = 0;
    $sum_wash = 0;
    $sum_finish = 0;
    $sum_sold = 0;
    foreach($carpets as $carpet) {
        $sum_purchase += $carpet->total_price;
        $sum_repair += $carpet->repair ? $carpet->repair->sum('total_price') : 0;
        $sum_wash += $carpet->carpet_wash ? $carpet->carpet_wash->total_price : 0;
        $sum_finish += $carpet->finishing_works ? $carpet->finishing_works->sum('price') : 0;
        $sum_sold += $carpet->sale ? $carpet->sale->sale_cost_total : 0;
    }

    $logoBase64 = '';
    if (file_exists(public_path('images/logo.png'))) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
    }
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Purchased Carpets</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                        <x:FreezePanes/>
                        <x:SplitHorizontal>10</x:SplitHorizontal>
                        <x:TopRowBottomPane>10</x:TopRowBottomPane>
                        <x:ActivePane>2</x:ActivePane>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; direction: rtl; background-color: #ffffff; }
        table { border-collapse: collapse; width: 100%; }
        th, td { font-family: 'Segoe UI', Calibri, Arial, sans-serif; border: 1px solid #cbd5e1; vertical-align: middle; }

        .company-name { font-size: 22pt; font-weight: bold; color: #1e3a8a; text-align: right; }
        .company-subtitle { font-size: 10.5pt; color: #475569; text-align: right; }

        .report-title { font-size: 16pt; font-weight: bold; color: #0f172a; text-align: center; height: 35pt; background-color: #f8fafc; border-bottom: 2px solid #3b82f6; }

        .card-label { font-size: 9.5pt; text-align: center; font-weight: bold; }
        .card-value { font-size: 15pt; text-align: center; font-weight: bold; }

        .bg-blue-light { background-color: #dbeafe; color: #1e3a8a; }
        .text-blue-dark { color: #1e40af; }
        
        .bg-green-light { background-color: #d1fae5; color: #065f46; }
        .text-green-dark { color: #047857; }

        .bg-red-light { background-color: #fee2e2; color: #991b1b; }
        .text-red-dark { color: #b91c1c; }

        .bg-yellow-light { background-color: #fef3c7; color: #92400e; }
        .text-yellow-dark { color: #b45309; }

        .bg-purple-light { background-color: #f3e8ff; color: #6b21a8; }
        .text-purple-dark { color: #7e22ce; }

        .meta-label { font-weight: bold; background-color: #f1f5f9; color: #1e293b; text-align: right; padding-right: 12px; font-size: 10pt; }
        .meta-value { color: #334155; text-align: right; padding-right: 12px; font-size: 10pt; }

        thead th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11pt; text-align: center; border: 1px solid #1e3a8a; }

        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 10px; }
        .text-right { text-align: right; padding-right: 10px; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
        .font-bold { font-weight: bold; }

        .total-row td { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11.5pt; border-top: 2px solid #0f172a; border-bottom: 3px double #0f172a; }
    </style>
</head>
<body>
    <table>
        <colgroup>
            <col width="60" style="width: 60px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="180" style="width: 180px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="80" style="width: 80px;" />
            <col width="80" style="width: 80px;" />
            <col width="100" style="width: 100px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="150" style="width: 150px;" />
        </colgroup>

        <!-- Company Header: Logo & Title Section -->
        <tr>
            <td colspan="2" align="center" valign="middle" style="border: none; background-color: #ffffff; height: 70pt;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" width="65" height="65" alt="Logo">
                @else
                    <span style="font-weight: bold; color: #1e3a8a; font-size: 18pt;">QB</span>
                @endif
            </td>
            <td colspan="13" valign="middle" style="border: none; background-color: #ffffff; padding-right: 15px; height: 70pt;">
                <span class="company-name">شرکت صنعتی برادران قاسمی</span><br><br>
                <span class="company-subtitle">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت افغانستان</span>
            </td>
        </tr>

        <tr style="height: 10pt;"><td colspan="15" style="border: none; background-color: #ffffff;"></td></tr>

        <!-- Report Title Header -->
        @php
            $statusFilter = request('status') != '' && isset($statuses[request('status')]) 
                ? $statuses[request('status')] 
                : 'تمامی حالت‌ها (All Statuses)';
        @endphp
        <tr>
            <td colspan="15" class="report-title" valign="middle">گزارش جامع قالین های خرید شده - {{ $statusFilter }}</td>
        </tr>

        <tr style="height: 12pt;"><td colspan="15" style="border: none; background-color: #ffffff;"></td></tr>

        <!-- Summary Dashboard Card Grid -->
        <tr>
            <td colspan="3" class="card-label bg-blue-light" style="height: 18pt;" valign="middle">مجموع قیمت خرید</td>
            <td colspan="3" class="card-label bg-yellow-light" style="height: 18pt;" valign="middle">مجموع مصارف ترمیم</td>
            <td colspan="3" class="card-label bg-purple-light" style="height: 18pt;" valign="middle">مجموع مصارف شست</td>
            <td colspan="3" class="card-label bg-red-light" style="height: 18pt;" valign="middle">مجموع مصارف تکمیلی</td>
            <td colspan="3" class="card-label bg-green-light" style="height: 18pt;" valign="middle">مجموع مبلغ فروش</td>
        </tr>
        <tr>
            <td colspan="3" class="card-value bg-blue-light text-blue-dark" style="height: 32pt;" valign="middle">${{ number_format($sum_purchase, 2) }}</td>
            <td colspan="3" class="card-value bg-yellow-light text-yellow-dark" style="height: 32pt;" valign="middle">${{ number_format($sum_repair, 2) }}</td>
            <td colspan="3" class="card-value bg-purple-light text-purple-dark" style="height: 32pt;" valign="middle">${{ number_format($sum_wash, 2) }}</td>
            <td colspan="3" class="card-value bg-red-light text-red-dark" style="height: 32pt;" valign="middle">${{ number_format($sum_finish, 2) }}</td>
            <td colspan="3" class="card-value bg-green-light text-green-dark" style="height: 32pt;" valign="middle">${{ number_format($sum_sold, 2) }}</td>
        </tr>

        <tr style="height: 15pt;"><td colspan="15" style="border: none; background-color: #ffffff;"></td></tr>

        <!-- Metadata Information Section -->
        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">از تاریخ:</td>
            <td colspan="3" class="meta-value font-bold" style="height: 24pt;">{{ request('from_date') ?: 'همه' }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">تا تاریخ:</td>
            <td colspan="3" class="meta-value font-bold" style="height: 24pt;">{{ request('to_date') ?: 'همه' }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">تاریخ گزارش:</td>
            <td colspan="3" class="meta-value font-bold" style="height: 24pt;">{{ $issueDate }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">نوعیت:</td>
            <td colspan="3" class="meta-value font-bold" style="height: 24pt;">{{ request('type_id') ? ($types->where('carpet_type_id', request('type_id'))->first()->carpet_type ?? 'همه') : 'همه' }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">کوالیتی:</td>
            <td colspan="3" class="meta-value font-bold" style="height: 24pt;">{{ request('quality_id') ? ($qualities->where('id', request('quality_id'))->first()->quality ?? 'همه') : 'همه' }}</td>
            <td colspan="4" class="meta-label" style="height: 24pt;">تعداد کل قالین‌ها:</td>
            <td colspan="3" class="meta-value font-bold" style="height: 24pt;">{{ number_format($carpets->count()) }} تخته</td>
        </tr>
        
        <tr style="height: 15pt;"><td colspan="17" style="border: none; background-color: #ffffff;"></td></tr>

        <!-- Main Table -->
        <thead>
            <tr>
                <th style="height: 32pt;">ردیف</th>
                <th style="height: 32pt;">شماره پارچه</th>
                <th style="height: 32pt;">نماینده</th>
                <th style="height: 32pt;">نوعیت</th>
                <th style="height: 32pt;">کوالیتی</th>
                <th style="height: 32pt;">شماره نقشه</th>
                <th style="height: 32pt;">طول</th>
                <th style="height: 32pt;">عرض</th>
                <th style="height: 32pt;">مساحت (M2)</th>
                <th style="height: 32pt;">قیمت خرید ($)</th>
                <th style="height: 32pt;">مصارف ترمیم ($)</th>
                <th style="height: 32pt;">مصارف شست ($)</th>
                <th style="height: 32pt;">مصارف تکمیلی ($)</th>
                <th style="height: 32pt;">مبلغ فروش ($)</th>
                <th style="height: 32pt;">حالت فعلی</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carpets as $index => $carpet)
            @php
                $repair_cost = $carpet->repair ? $carpet->repair->sum('total_price') : 0;
                $wash_cost = $carpet->carpet_wash ? $carpet->carpet_wash->total_price : 0;
                $finish_cost = $carpet->finishing_works ? $carpet->finishing_works->sum('price') : 0;
                $sold_amount = $carpet->sale ? $carpet->sale->sale_cost_total : 0;
                $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff';
            @endphp
            <tr style="background-color: {{ $rowBgColor }};">
                <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $index + 1 }}</td>
                <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->carpet_no }}</td>
                <td class="text-right" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->agent->user->name ?? 'نامشخص' }} {{ $carpet->agent->user->last_name ?? '' }}</td>
                <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->type->carpet_type ?? '-' }}</td>
                <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->quality->quality ?? '-' }}</td>
                <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->map_number ?? '-' }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->height }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->width }}</td>
                <td class="text-center font-bold text-success" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ number_format($carpet->area, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">${{ number_format($carpet->total_price, 2) }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $repair_cost > 0 ? '$'.number_format($repair_cost, 2) : '-' }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $wash_cost > 0 ? '$'.number_format($wash_cost, 2) : '-' }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $finish_cost > 0 ? '$'.number_format($finish_cost, 2) : '-' }}</td>
                <td class="text-center font-bold text-success" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ $sold_amount > 0 ? '$'.number_format($sold_amount, 2) : '-' }}</td>
                <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $statuses[$carpet->status] ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center" style="padding: 20px;">هیچ قالینی مطابق با فیلترهای اعمال شده یافت نشد.</td>
            </tr>
            @endforelse

            <tr style="height: 10pt;"><td colspan="14" style="border: none; background-color: #ffffff;"></td></tr>

            @if($carpets->count() > 0)
            <tr class="total-row">
                <td colspan="8" class="text-center" style="height: 32pt;">مجموع کلی (Grand Total)</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">{{ number_format($carpets->sum('area'), 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">${{ number_format($sum_purchase, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">${{ number_format($sum_repair, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">${{ number_format($sum_wash, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">${{ number_format($sum_finish, 2) }}</td>
                <td class="text-center font-bold text-success" style="direction: ltr; height: 32pt; color: #10b981;">${{ number_format($sum_sold, 2) }}</td>
                <td style="height: 32pt;"></td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
