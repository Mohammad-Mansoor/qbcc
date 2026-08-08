<?php
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=Warehouse_Stock_Report_" . date('Y-m-d') . ".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <!--[if gte mso 9]>
    <?php $x = 'x:'; echo "<xml>
        <{$x}ExcelWorkbook>
            <{$x}ExcelWorksheets>
                <{$x}ExcelWorksheet>
                    <{$x}Name>Stock Report</{$x}Name>
                    <{$x}WorksheetOptions>
                        <{$x}DisplayGridlines/>
                        <{$x}FreezePanes/>
                        <{$x}SplitHorizontal>7</{$x}SplitHorizontal>
                        <{$x}TopRowBottomPane>7</{$x}TopRowBottomPane>
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
        @if($warehouse->subtype === 'carpet')
        <colgroup>
            <col width="60" style="width: 60px;" />
            <col width="120" style="width: 120px;" />
            <col width="150" style="width: 150px;" />
            <col width="120" style="width: 120px;" />
            <col width="120" style="width: 120px;" />
            <col width="100" style="width: 100px;" />
            <col width="100" style="width: 100px;" />
            <col width="120" style="width: 120px;" />
            <col width="150" style="width: 150px;" />
            <col width="150" style="width: 150px;" />
        </colgroup>
        @else
        <colgroup>
            <col width="60" style="width: 60px;" />
            <col width="150" style="width: 150px;" />
            <col width="150" style="width: 150px;" />
            <col width="150" style="width: 150px;" />
            <col width="150" style="width: 150px;" />
            <col width="150" style="width: 150px;" />
        </colgroup>
        @endif

        <!-- Company Header Section -->
        <tr>
            <td colspan="1" align="center" valign="middle" style="border: none; background-color: #ffffff; height: 70pt;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" width="65" height="65" alt="Logo">
                @else
                    <span style="font-weight: bold; color: #1e3a8a; font-size: 18pt;">QB</span>
                @endif
            </td>
            <td colspan="{{ $warehouse->subtype === 'carpet' ? 9 : 5 }}" valign="middle" style="border: none; background-color: #ffffff; padding-right: 15px; height: 70pt;">
                <span class="company-name">شرکت صنعتی برادران قاسمی</span><br><br>
                <span class="company-subtitle">گزارش موجودی گدام</span>
            </td>
        </tr>

        <tr style="height: 10pt;"><td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" style="border: none; background-color: #ffffff;"></td></tr>

        <!-- Report Title Header -->
        <tr>
            <td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" class="report-title" valign="middle">گزارش موجودی - {{ $warehouse->name }}</td>
        </tr>

        <tr style="height: 12pt;"><td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" style="border: none; background-color: #ffffff;"></td></tr>

        <!-- Metadata Section -->
        <tr>
            <td colspan="1" class="meta-label" style="height: 24pt;">نام گدام:</td>
            <td colspan="2" class="meta-value font-bold" style="height: 24pt;">{{ $warehouse->name }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">تاریخ گزارش:</td>
            <td colspan="{{ $warehouse->subtype === 'carpet' ? 5 : 0 }}" class="meta-value font-bold" style="height: 24pt;">{{ $issueDate }}</td>
        </tr>
        <tr>
            <td colspan="1" class="meta-label" style="height: 24pt;">موقعیت:</td>
            <td colspan="2" class="meta-value font-bold" style="height: 24pt;">{{ $warehouse->location ?: 'نامشخص' }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">مجموع اقلام:</td>
            <td colspan="{{ $warehouse->subtype === 'carpet' ? 5 : 0 }}" class="meta-value font-bold" style="height: 24pt;">{{ number_format(count($items)) }}</td>
        </tr>
        
        <tr style="height: 15pt;"><td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" style="border: none; background-color: #ffffff;"></td></tr>

        @if(count(array_filter($request->except(['id', 'export']))) > 0)
        <tr>
            <td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" class="meta-label" style="text-align: right; background-color: #e2e8f0; height: 20pt;">فیلترهای اعمال شده (Applied Filters):</td>
        </tr>
        <tr>
            <td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" class="meta-value text-right" style="height: 40pt; vertical-align: top;">
                @if($request->filled('search')) جستجو: {{ $request->search }} | @endif
                @if($request->filled('type_id')) 
                    @php $typeName = collect($carpetTypes ?? [])->firstWhere('carpet_type_id', $request->type_id)->carpet_type ?? $request->type_id; @endphp
                    نوعیت: {{ $typeName }} | 
                @endif
                @if($request->filled('quality_id')) 
                    @php $qualName = collect($qualities ?? [])->firstWhere('quality_id', $request->quality_id)->quality ?? $request->quality_id; @endphp
                    کوالیتی: {{ $qualName }} | 
                @endif
                @if($request->filled('status') && $request->status !== 'all') 
                    وضعیت: {{ $statuses[$request->status] ?? $request->status }} | 
                @endif
                @if($request->filled('agent_id')) 
                    @php 
                        $ag = collect($agents ?? [])->firstWhere('id', $request->agent_id);
                        $agName = $ag ? ($ag->user->name . ' ' . $ag->user->last_name) : $request->agent_id;
                    @endphp
                    عاملیت: {{ $agName }} | 
                @endif
                @if($request->filled('category_id')) 
                    @php $catName = collect($materialCategories ?? [])->firstWhere('material_category_id', $request->category_id)->material_category ?? $request->category_id; @endphp
                    کتگوری: {{ $catName }} | 
                @endif
                @if($request->filled('material_type_id')) 
                    @php $matTypeName = collect($materialTypes ?? [])->firstWhere('material_type_id', $request->material_type_id)->material_type ?? $request->material_type_id; @endphp
                    نوعیت مواد: {{ $matTypeName }} | 
                @endif
                @if($request->filled('min_qty')) حداقل موجودی: {{ $request->min_qty }} | @endif
                @if($request->filled('max_qty')) حداکثر موجودی: {{ $request->max_qty }} | @endif
            </td>
        </tr>
        <tr style="height: 15pt;"><td colspan="{{ $warehouse->subtype === 'carpet' ? 10 : 6 }}" style="border: none; background-color: #ffffff;"></td></tr>
        @endif

        <!-- Main Table -->
        @if($warehouse->subtype === 'carpet')
        <thead>
            <tr>
                <th style="height: 32pt;">ردیف</th>
                <th style="height: 32pt;">شماره پارچه</th>
                <th style="height: 32pt;">نماینده فروشنده</th>
                <th style="height: 32pt;">نوعیت</th>
                <th style="height: 32pt;">کوالیتی</th>
                <th style="height: 32pt;">طول</th>
                <th style="height: 32pt;">عرض</th>
                <th style="height: 32pt;">مساحت (M2)</th>
                <th style="height: 32pt;">قیمت کل ($)</th>
                <th style="height: 32pt;">حالت فعلی</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            @php $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff'; @endphp
            <tr style="background-color: {{ $rowBgColor }};">
                <td class="text-center" style="height: 26pt;">{{ $index + 1 }}</td>
                <td class="text-center font-bold" style="height: 26pt;">{{ $item->carpet_no }}</td>
                <td class="text-center" style="height: 26pt;">{{ $item->agent->user->name ?? 'نامشخص' }} {{ $item->agent->user->last_name ?? '' }}</td>
                <td class="text-center" style="height: 26pt;">{{ $item->type->carpet_type ?? '-' }}</td>
                <td class="text-center" style="height: 26pt;">{{ $item->quality->quality ?? '-' }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt;">{{ $item->height }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt;">{{ $item->width }}</td>
                <td class="text-center font-bold text-success" style="direction: ltr; height: 26pt;">{{ number_format($item->area, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 26pt;">${{ number_format($item->total_price, 2) }}</td>
                <td class="text-center" style="height: 26pt;">{{ $statuses[$item->status] ?? 'نامشخص' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px;">هیچ قالینی در این گدام یافت نشد.</td>
            </tr>
            @endforelse

            @if(count($items) > 0)
            <tr class="total-row">
                <td colspan="7" class="text-center" style="height: 32pt;">مجموع کلی (Grand Total)</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">{{ number_format($items->sum('area'), 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">${{ number_format($items->sum('total_price'), 2) }}</td>
                <td style="height: 32pt;"></td>
            </tr>
            @endif
        </tbody>
        @else
        <thead>
            <tr>
                <th style="height: 32pt;">ردیف</th>
                <th style="height: 32pt;">کتگوری</th>
                <th style="height: 32pt;">نوعیت مواد</th>
                <th style="height: 32pt;">موجودی در دسترس (KG)</th>
                <th style="height: 32pt;">آخرین فیت قیمت ($)</th>
                <th style="height: 32pt;">ارزش تخمینی ($)</th>
            </tr>
        </thead>
        <tbody>
            @php $sum_qty = 0; $sum_val = 0; @endphp
            @forelse($items as $index => $item)
            @php 
                $val = $item->available_qty * $item->current_cost;
                $sum_qty += $item->available_qty;
                $sum_val += $val;
                $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff';
            @endphp
            <tr style="background-color: {{ $rowBgColor }};">
                <td class="text-center" style="height: 26pt;">{{ $index + 1 }}</td>
                <td class="text-center font-bold" style="height: 26pt;">{{ $item->material_category ?? '-' }}</td>
                <td class="text-center font-bold" style="height: 26pt;">{{ $item->material_type ?? '-' }}</td>
                <td class="text-center font-bold text-success" style="direction: ltr; height: 26pt;">{{ number_format($item->available_qty, 2) }}</td>
                <td class="text-center" style="direction: ltr; height: 26pt;">${{ number_format($item->current_cost, 2) }}</td>
                <td class="text-center font-bold" style="direction: ltr; height: 26pt;">${{ number_format($val, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">هیچ موادی در این گدام یافت نشد.</td>
            </tr>
            @endforelse

            @if(count($items) > 0)
            <tr class="total-row">
                <td colspan="3" class="text-center" style="height: 32pt;">مجموع کلی (Grand Total)</td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">{{ number_format($sum_qty, 2) }}</td>
                <td class="text-center" style="height: 32pt;"></td>
                <td class="text-center font-bold" style="direction: ltr; height: 32pt;">${{ number_format($sum_val, 2) }}</td>
            </tr>
            @endif
        </tbody>
        @endif
    </table>
</body>
</html>
