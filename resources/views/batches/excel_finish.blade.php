<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Finishing Batch</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
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
        .meta-label { font-weight: bold; background-color: #f1f5f9; color: #1e293b; text-align: right; padding-right: 12px; font-size: 10pt; }
        .meta-value { color: #334155; text-align: right; padding-right: 12px; font-size: 10pt; }
        thead th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11pt; text-align: center; border: 1px solid #1e3a8a; }
        .cat-header { background-color: #2563eb; color: #ffffff; }
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 10px; }
        .text-right { text-align: right; padding-right: 10px; }
        .total-row td { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11.5pt; border-top: 2px solid #0f172a; }
    </style>
</head>
<body>
    @php
        $colspanHeader = 7 + $batchCategories->count();
    @endphp
    <table>
        <tr>
            <td colspan="1" align="center" valign="middle" style="border: none; background-color: #ffffff; height: 70pt;">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" width="65" height="65" alt="Logo">
                @else
                    <span style="font-weight: bold; color: #1e3a8a; font-size: 18pt;">QBIC</span>
                @endif
            </td>
            <td colspan="{{ $colspanHeader - 1 }}" valign="middle" style="border: none; background-color: #ffffff; padding-right: 15px; height: 70pt;">
                <span class="company-name">شرکت صنعتی برادران قاسمی</span><br><br>
                <span class="company-subtitle">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت  افغانستان</span>
            </td>
        </tr>
        <tr style="height: 10pt;"><td colspan="{{ $colspanHeader }}" style="border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <td colspan="{{ $colspanHeader }}" class="report-title" valign="middle">صورتحساب تیاری (Finishing Payment Bill)</td>
        </tr>
        <tr style="height: 12pt;"><td colspan="{{ $colspanHeader }}" style="border: none; background-color: #ffffff;"></td></tr>

        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">نمبر مسلسل:</td>
            <td colspan="2" class="meta-value font-bold" style="height: 24pt;">{{ $batch->reference_number }}</td>
            <td colspan="1" class="meta-label" style="height: 24pt;">مجموع مساحت:</td>
            <td colspan="{{ $colspanHeader - 5 }}" class="meta-value" style="height: 24pt; direction: ltr; text-align: right;">{{ number_format($groupedCarpets->sum(function ($group) {
    return $group->first()->carpet->area ?? 0; }), 2) }} m²</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">تیم کاری / بخش مربوطه:</td>
            <td colspan="2" class="meta-value font-bold" style="height: 24pt;">{{ $team->name ?? '--- ثبت نشده ---' }}</td>
            <td colspan="1" class="meta-label" style="height: 24pt;">تاریخ ایجاد:</td>
            <td colspan="{{ $colspanHeader - 5 }}" class="meta-value" style="height: 24pt; direction: ltr; text-align: right;">{{ $batch->created_at->format('Y-m-d') }}</td>
        </tr>

        <tr style="height: 15pt;"><td colspan="{{ $colspanHeader }}" style="border: none; background-color: #ffffff;"></td></tr>

        <thead>
            <tr>
                <th style="height: 32pt;">ردیف</th>
                <th style="height: 32pt;">نمبر قالین</th>
                <th style="height: 32pt;">شماره نقشه</th>
                <th style="height: 32pt;">نوعیت</th>
                <th style="height: 32pt;">کیفیت</th>
                <th style="height: 32pt;">ابعاد (m)</th>
                <th style="height: 32pt;">مساحت (m²)</th>
                @foreach($batchCategories as $cat)
                    <th class="cat-header" style="height: 32pt;">{{ $cat->category }}</th>
                @endforeach
                <th style="height: 32pt; background-color: #0f172a;">مجموع کل (USD)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                                $grandTotalArea = 0;
                $grandTotalPrice = 0;
                $catTotals = [];
                foreach ($batchCategories as $cat) {
                    $catTotals[$cat->id] = 0;
                }
                $rowIndex = 1;
            @endphp
            @forelse($groupedCarpets as $carpetId => $works)
                @php 
                                    $firstWork = $works->first();
                    $carpet = $firstWork->carpet;
                    $area = $carpet->area ?? 0;
                    $grandTotalArea += $area;

                    $carpetTotal = $works->sum('price');
                    $grandTotalPrice += $carpetTotal;

                    $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff'; 
                @endphp
                <tr style="background-color: {{ $rowBgColor }};">
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $rowIndex++ }}</td>
                    <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->carpet_no ?? 'N/A' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->map_number ?? '---' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->type->carpet_type ?? '---' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $carpet->quality->quality ?? '---' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }}; direction: ltr;">{{ $carpet->height ?? '---' }} × {{ $carpet->width ?? '---' }}</td>
                    <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ number_format($area, 2) }}</td>

                    @foreach($batchCategories as $cat)
                        @php
                            $workForCat = $works->firstWhere('category_id', $cat->id);
                            $workPrice = $workForCat ? $workForCat->price : 0;
                            $catTotals[$cat->id] += $workPrice;
                        @endphp
                        <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }}; direction: ltr;">
                            @if($workPrice > 0)
                                ${{ number_format($workPrice, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    <td class="text-left font-bold" style="direction: ltr; height: 26pt; background-color: #fdf2f2; color: #b91c1c;">${{ number_format($carpetTotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $colspanHeader }}" class="text-center" style="height: 26pt;">هیچ قالینی ثبت نشده است.</td>
                </tr>
            @endforelse
            
            <tr style="height: 10pt;"><td colspan="{{ $colspanHeader }}" style="border: none; background-color: #ffffff;"></td></tr>

            <tr>
                <td colspan="6" class="meta-label" style="height: 26pt;">مجموع کل:</td>
                <td class="text-center font-bold" style="background-color: #f1f5f9; direction: ltr; height: 26pt;">{{ number_format($grandTotalArea, 2) }}</td>
                @foreach($batchCategories as $cat)
                    <td class="text-center font-bold" style="background-color: #f1f5f9; direction: ltr; height: 26pt;">${{ number_format($catTotals[$cat->id], 2) }}</td>
                @endforeach
                <td class="text-left font-bold" style="background-color: #e2e8f0; color: #b91c1c; direction: ltr; height: 26pt;">${{ number_format($grandTotalPrice, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($payments && $payments->count() > 0)
        <br><br>
        <table>
            <tr>
                <td colspan="6" class="report-title" style="height: 25pt; font-size: 13pt;" valign="middle">تاریخچه تادیات و پرداخت‌های مستقیم (Payment History)</td>
            </tr>
            <thead>
                <tr>
                    <th style="height: 25pt; background-color: #475569;">تاریخ پرداخت</th>
                    <th style="height: 25pt; background-color: #475569;">تفصیلات و بابت</th>
                    <th style="height: 25pt; background-color: #475569;">نوعیت</th>
                    <th style="height: 25pt; background-color: #475569;">ارز اصلی</th>
                    <th style="height: 25pt; background-color: #475569;">نرخ تسعیر</th>
                    <th style="height: 25pt; background-color: #475569;">معادل دالر (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $pay)
                    <tr>
                        <td class="text-center" style="height: 22pt;">{{ $pay->date }}</td>
                        <td class="text-right" style="height: 22pt; padding-right: 10px;">{{ $pay->description }}</td>
                        <td class="text-center" style="height: 22pt;">{{ $pay->type == 'گرفت' ? 'گرفت (Outflow)' : 'رسید (Inflow)' }}</td>
                        <td class="text-left" style="height: 22pt; padding-left: 10px; direction: ltr;">{{ number_format($pay->original_amount, 2) }} {{ $pay->currency_code }}</td>
                        <td class="text-center" style="height: 22pt; direction: ltr;">{{ number_format($pay->exchange_rate, 4) }}</td>
                        <td class="text-left font-bold" style="height: 22pt; padding-left: 10px; direction: ltr; color: #16a34a;">
                            ${{ number_format($pay->base_amount ?? ($pay->original_amount * ($pay->exchange_rate > 0 ? $pay->exchange_rate : 1)), 2) }}
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td class="meta-label" style="height: 24pt;">مجموع پرداخت شده:</td>
                    <td class="text-left font-bold" style="background-color: #ecfdf5; color: #16a34a; direction: ltr; height: 24pt;">${{ number_format($totalPaid, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td class="meta-label" style="height: 24pt;">باقیمانده طلب:</td>
                    <td class="text-left font-bold" style="background-color: #fef2f2; color: #dc2626; direction: ltr; height: 24pt;">${{ number_format($remaining, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
