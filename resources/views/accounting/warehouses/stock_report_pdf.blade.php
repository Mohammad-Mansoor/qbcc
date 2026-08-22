<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش موجودی گدام - {{ $warehouse->name }}</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        body { font-family: 'Tahoma', Arial, sans-serif; background-color: #fff; color: #1e293b; font-size: 10pt; line-height: 1.5; margin: 0; padding: 0; }
        
        .fixed-header { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .fixed-header img { width: 100%; height: 110px; object-fit: cover; object-position: center top; display: block; }
        
        .fixed-footer { position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1000; }
        .fixed-footer img { width: 100%; height: 110px; object-fit: cover; object-position: center bottom; display: block; }

        .header-space { height: 120px; }
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
                            <h1>گزارش موجودی - {{ $warehouse->name }}</h1>
                            <p>سیستم مدیریت یکپارچه - بخش گزارشات گدام</p>
                        </div>

                        <table class="meta-table">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="meta-label">مشخصات گدام:</span>
                                    <div class="meta-val">
                                        نام گدام: <strong>{{ $warehouse->name }}</strong><br>
                                        موقعیت: <strong>{{ $warehouse->location ?: 'نامشخص' }}</strong><br>
                                        نوعیت ذخیره: <strong>{{ $warehouse->subtype_fa ?? $warehouse->subtype }}</strong>
                                    </div>
                                </td>
                                <td style="width: 50%; text-align: left; direction: ltr;">
                                    <span class="meta-label" style="text-align: right;">جزئیات گزارش (Report Details):</span>
                                    <div class="meta-val" style="text-align: right;">
                                        تاریخ صدور (Issue Date): <strong>{{ $issueDate }}</strong><br>
                                        @if($warehouse->subtype === 'carpet')
                                        مجموع قالین‌ها (Total Count): <strong>{{ number_format(count($items)) }} تخته</strong><br>
                                        مجموع مساحت (Total Area): <strong>{{ number_format($items->sum('area'), 2) }} M<sup>2</sup></strong>
                                        @else
                                        مجموع اقلام: <strong>{{ number_format(count($items)) }} قلم</strong>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>

                        @if(count(array_filter($request->except(['id', 'export']))) > 0)
                        <div style="background-color: #f1f5f9; padding: 10px; border: 1px solid #cbd5e1; margin-bottom: 20px; font-size: 8.5pt;">
                            <strong style="color: #0f172a;">فیلترهای اعمال شده (Applied Filters):</strong>
                            <ul style="margin: 5px 0 0 0; padding-right: 20px; color: #334155;">
                                @if($request->filled('search')) <li>جستجو: {{ $request->search }}</li> @endif
                                @if($request->filled('type_id')) 
                                    @php $typeName = collect($carpetTypes ?? [])->firstWhere('carpet_type_id', $request->type_id)->carpet_type ?? $request->type_id; @endphp
                                    <li>نوعیت: {{ $typeName }}</li> 
                                @endif
                                @if($request->filled('quality_id')) 
                                    @php $qualName = collect($qualities ?? [])->firstWhere('quality_id', $request->quality_id)->quality ?? $request->quality_id; @endphp
                                    <li>کوالیتی: {{ $qualName }}</li> 
                                @endif
                                @if($request->filled('status') && $request->status !== 'all') 
                                    <li>وضعیت: {{ $statuses[$request->status] ?? $request->status }}</li> 
                                @endif
                                @if($request->filled('agent_id')) 
                                    @php 
                                        $ag = collect($agents ?? [])->firstWhere('id', $request->agent_id);
                                        $agName = $ag ? ($ag->user->name . ' ' . $ag->user->last_name) : $request->agent_id;
                                    @endphp
                                    <li>عاملیت: {{ $agName }}</li> 
                                @endif
                                @if($request->filled('category_id')) 
                                    @php $catName = collect($materialCategories ?? [])->firstWhere('material_category_id', $request->category_id)->material_category ?? $request->category_id; @endphp
                                    <li>کتگوری: {{ $catName }}</li> 
                                @endif
                                @if($request->filled('material_type_id')) 
                                    @php $matTypeName = collect($materialTypes ?? [])->firstWhere('material_type_id', $request->material_type_id)->material_type ?? $request->material_type_id; @endphp
                                    <li>نوعیت مواد: {{ $matTypeName }}</li> 
                                @endif
                                @if($request->filled('min_qty')) <li>حداقل موجودی: {{ $request->min_qty }}</li> @endif
                                @if($request->filled('max_qty')) <li>حداکثر موجودی: {{ $request->max_qty }}</li> @endif
                            </ul>
                        </div>
                        @endif

                        <table class="data-table">
                            @if($warehouse->subtype === 'carpet')
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">ردیف</th>
                                    <th style="width: 10%;">شماره پارچه</th>
                                    <th style="width: 15%;">نماینده فروشنده</th>
                                    <th style="width: 10%;">نوعیت</th>
                                    <th style="width: 10%;">کوالیتی</th>
                                    <th class="text-center" style="width: 8%;">طول</th>
                                    <th class="text-center" style="width: 8%;">عرض</th>
                                    <th class="text-center" style="width: 10%;">مساحت (m2)</th>
                                    <th class="text-center" style="width: 12%;">قیمت کل ($)</th>
                                    <th class="text-center" style="width: 12%;">حالت فعلی</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->carpet_no }}</strong></td>
                                    <td>{{ $item->agent->user->name ?? 'نامشخص' }} {{ $item->agent->user->last_name ?? '' }}</td>
                                    <td>{{ $item->type->carpet_type ?? '-' }}</td>
                                    <td>{{ $item->quality->quality ?? '-' }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $item->height }}</td>
                                    <td class="text-center" style="direction: ltr;">{{ $item->width }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">{{ number_format($item->area, 2) }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">${{ number_format($item->total_price, 2) }}</td>
                                    <td class="text-center">{{ $statuses[$item->status] ?? 'نامشخص' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center" style="padding: 20px;">هیچ قالینی در این گدام یافت نشد.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($items) > 0)
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="7" class="text-center">مجموع کلی (Grand Total)</td>
                                    <td class="text-center" style="direction: ltr;">{{ number_format($items->sum('area'), 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($items->sum('total_price'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                            @else
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 8%;">ردیف</th>
                                    <th style="width: 22%;">کتگوری</th>
                                    <th style="width: 22%;">نوعیت مواد</th>
                                    <th class="text-center" style="width: 14%;">موجودی در دسترس (KG)</th>
                                    <th class="text-center" style="width: 11%;">آخرین خرید ($)</th>
                                    <th class="text-center" style="width: 11%;">میانگین WAC ($)</th>
                                    <th class="text-center" style="width: 12%;">ارزش تخمینی ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sum_qty = 0; $sum_val = 0; @endphp
                                @forelse($items as $index => $item)
                                @php 
                                    $val = $item->available_qty * $item->current_cost;
                                    $sum_qty += $item->available_qty;
                                    $sum_val += $val;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->material_category ?? '-' }}</strong></td>
                                    <td><strong>{{ $item->material_type ?? '-' }}</strong></td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">{{ number_format($item->available_qty, 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($item->last_purchase_price ?? $item->current_cost, 2) }}</td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($item->current_cost, 2) }}</td>
                                    <td class="text-center" style="direction: ltr; font-weight: bold;">${{ number_format($val, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 20px;">هیچ موادی در این گدام یافت نشد.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($items) > 0)
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="3" class="text-center">مجموع کلی (Grand Total)</td>
                                    <td class="text-center" style="direction: ltr;">{{ number_format($sum_qty, 2) }}</td>
                                    <td colspan="2" class="text-center"></td>
                                    <td class="text-center" style="direction: ltr;">${{ number_format($sum_val, 2) }}</td>
                                </tr>
                            </tfoot>
                            @endif
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
