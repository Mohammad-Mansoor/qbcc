<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <!--[if gte mso 9]>
    <?php $x = 'x:'; echo "<xml>
        <{$x}ExcelWorkbook>
            <{$x}ExcelWorksheets>
                <{$x}ExcelWorksheet>
                    <{$x}Name>Batch Invoice</{$x}Name>
                    <{$x}WorksheetOptions>
                        <{$x}DisplayGridlines/>
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
        .total-row td { background-color: #1e3a8a; color: #ffffff; font-weight: bold; font-size: 11.5pt; border-top: 2px solid #0f172a; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="1" align="center" valign="middle" style="border: none; background-color: #ffffff; height: 70pt;">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" width="65" height="65" alt="Logo">
                @else
                    <span style="font-weight: bold; color: #1e3a8a; font-size: 18pt;">QBIC</span>
                @endif
            </td>
            <td colspan="{{ $batch->type == 'finish' ? '5' : '4' }}" valign="middle" style="border: none; background-color: #ffffff; padding-right: 15px; height: 70pt;">
                <span class="company-name">شرکت صنعتی برادران قاسمی</span><br><br>
                <span class="company-subtitle">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت  افغانستان</span>
            </td>
        </tr>
        <tr style="height: 10pt;"><td colspan="{{ $batch->type == 'finish' ? '6' : '5' }}" style="border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <td colspan="{{ $batch->type == 'finish' ? '6' : '5' }}" class="report-title" valign="middle">
                @if($batch->type == 'kachaee')
                    صورتحساب کچایی (Kachaee Payment Bill)
                @elseif($batch->type == 'wash')
                    صورتحساب شستشو (Washing Payment Bill)
                @elseif($batch->type == 'finish')
                    صورتحساب تیاری (Finishing Payment Bill)
                @else
                    صورتحساب تولید (Production Payment Bill)
                @endif
            </td>
        </tr>
        <tr style="height: 12pt;"><td colspan="{{ $batch->type == 'finish' ? '6' : '5' }}" style="border: none; background-color: #ffffff;"></td></tr>

        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">نمبر مسلسل:</td>
            <td colspan="{{ $batch->type == 'finish' ? '1' : '1' }}" class="meta-value font-bold" style="height: 24pt;">{{ $batch->reference_number }}</td>
            <td colspan="1" class="meta-label" style="height: 24pt;">وضعیت:</td>
            <td colspan="{{ $batch->type == 'finish' ? '2' : '1' }}" class="meta-value" style="height: 24pt;">{{ $batch->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">تیم کاری / بخش مربوطه:</td>
            <td colspan="{{ $batch->type == 'finish' ? '1' : '1' }}" class="meta-value font-bold" style="height: 24pt;">{{ $team->name ?? '--- ثبت نشده ---' }}</td>
            <td colspan="1" class="meta-label" style="height: 24pt;">نوعیت مرحله:</td>
            <td colspan="{{ $batch->type == 'finish' ? '2' : '1' }}" class="meta-value" style="height: 24pt;">
                @if($batch->type == 'kachaee')
                    کچایی (Kachaee)
                @elseif($batch->type == 'wash')
                    شستشو (Washing)
                @elseif($batch->type == 'finish')
                    تیاری (Finishing)
                @endif
            </td>
        </tr>

        <tr style="height: 15pt;"><td colspan="{{ $batch->type == 'finish' ? '6' : '5' }}" style="border: none; background-color: #ffffff;"></td></tr>

        <thead>
            <tr>
                <th style="height: 32pt;">ردیف</th>
                <th style="height: 32pt;">نمبر قالین</th>
                <th style="height: 32pt;">شماره نقشه</th>
                <th style="height: 32pt;">نوعیت</th>
                <th style="height: 32pt;">کیفیت</th>
                <th style="height: 32pt;">ابعاد (m)</th>
                <th style="height: 32pt;">مساحت (m²)</th>
                @if($batch->type == 'finish')
                    <th style="height: 32pt;">کتگوری تیاری</th>
                    <th style="height: 32pt;">هزینه/m² ($)</th>
                    <th style="height: 32pt;">قیمت کل (USD)</th>
                @else
                    <th style="height: 32pt;">هزینه/m² ($)</th>
                    <th style="height: 32pt;">قیمت کل (USD)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($carpets as $index => $item)
                @php 
                                    $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff';
                    $area = isset($item->carpet->area) && $item->carpet->area > 0 ? $item->carpet->area : 1;
                    $price = 0;
                    if ($batch->type == 'finish')
                        $price = $item->price;
                    elseif ($batch->type == 'kachaee')
                        $price = $item->total_price;
                    elseif ($batch->type == 'wash')
                        $price = $item->total_price ?: $item->af_total_price;
                @endphp
                <tr style="background-color: {{ $rowBgColor }};">
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $index + 1 }}</td>
                    <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $item->carpet->carpet_no ?? 'N/A' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $item->carpet->map_number ?? '---' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $item->carpet->type->carpet_type ?? '---' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $item->carpet->quality->quality ?? '---' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }}; direction: ltr;">{{ $item->carpet->height ?? '---' }} × {{ $item->carpet->width ?? '---' }}</td>
                    <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ number_format($item->carpet->area ?? 0, 2) }}</td>
                    @if($batch->type == 'finish')
                        <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $item->category->category ?? '---' }}</td>
                    @endif
                    <td class="text-center font-bold" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">${{ number_format($price / $area, 2) }}</td>
                    <td class="text-left font-bold" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">${{ number_format($price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $batch->type == 'finish' ? 10 : 9 }}" class="text-center" style="height: 26pt;">هیچ قالینی ثبت نشده است.</td>
                </tr>
            @endforelse
            
            <tr style="height: 10pt;"><td colspan="{{ $batch->type == 'finish' ? '10' : '9' }}" style="border: none; background-color: #ffffff;"></td></tr>

            <tr>
                <td colspan="6" class="meta-label" style="height: 26pt;">مجموع کل مساحت:</td>
                <td class="text-center font-bold" style="background-color: #f1f5f9; direction: ltr; height: 26pt;">{{ number_format($carpets->sum(function ($c) {
    return $c->carpet->area ?? 0; }), 2) }}</td>
                @if($batch->type == 'finish')
                    <td class="meta-label" style="height: 26pt;">مبلغ کل (USD):</td>
                    <td class="text-left font-bold" style="background-color: #eff6ff; color: #2563eb; direction: ltr; height: 26pt;">${{ number_format($totalCost, 2) }}</td>
                @else
                    <td class="text-left font-bold" style="background-color: #eff6ff; color: #2563eb; direction: ltr; height: 26pt;">${{ number_format($totalCost, 2) }}</td>
                @endif
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
