<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Purchase Bill</x:Name>
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
                    <span style="font-weight: bold; color: #1e3a8a; font-size: 18pt;">QBCC</span>
                @endif
            </td>
            <td colspan="7" valign="middle" style="border: none; background-color: #ffffff; padding-right: 15px; height: 70pt;">
                <span class="company-name">شرکت صنعتی برادران قاسمی</span><br><br>
                <span class="company-subtitle">تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت  افغانستان</span>
            </td>
        </tr>
        <tr style="height: 10pt;"><td colspan="8" style="border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <td colspan="8" class="report-title" valign="middle">بل خرید مواد خام (Purchase Bill)</td>
        </tr>
        <tr style="height: 12pt;"><td colspan="8" style="border: none; background-color: #ffffff;"></td></tr>

        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">نمبر فاکتور / بل:</td>
            <td colspan="2" class="meta-value font-bold" style="height: 24pt;">{{ $bill->bill_number }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">تاریخ صدور:</td>
            <td colspan="2" class="meta-value" style="height: 24pt;">{{ \Carbon\Carbon::parse($bill->date)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">نام تامین کننده:</td>
            <td colspan="2" class="meta-value font-bold" style="height: 24pt;">{{ $bill->seller->name ?? 'N/A' }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">شماره تماس فروشنده:</td>
            <td colspan="2" class="meta-value" style="height: 24pt;">{{ $bill->seller->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="height: 24pt;">آدرس تامین کننده:</td>
            <td colspan="2" class="meta-value" style="height: 24pt;">{{ $bill->seller->address ?? 'N/A' }}</td>
            <td colspan="2" class="meta-label" style="height: 24pt;">وضعیت بل:</td>
            <td colspan="2" class="meta-value" style="height: 24pt;">{{ $bill->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}</td>
        </tr>

        <tr style="height: 15pt;"><td colspan="8" style="border: none; background-color: #ffffff;"></td></tr>

        <thead>
            <tr>
                <th style="height: 32pt;">ردیف</th>
                <th style="height: 32pt;">تاریخ خرید</th>
                <th style="height: 32pt;">نوعیت مواد</th>
                <th style="height: 32pt;">دسته‌بندی (Subtype)</th>
                <th style="height: 32pt;">گدام (Warehouse)</th>
                <th style="height: 32pt;">مقدار (KG)</th>
                <th style="height: 32pt;">نرخ فی کیلو (USD)</th>
                <th style="height: 32pt;">مجموع قیمت (USD)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $index => $purchase)
                @php $rowBgColor = $loop->even ? '#f8fafc' : '#ffffff'; @endphp
                <tr style="background-color: {{ $rowBgColor }};">
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $index + 1 }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $purchase->purchase_date }}</td>
                    <td class="text-center font-bold" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $purchase->materialType->material_type ?? 'N/A' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $purchase->materialCategory->material_category ?? '—' }}</td>
                    <td class="text-center" style="height: 26pt; background-color: {{ $rowBgColor }};">{{ $purchase->warehouse->name ?? 'N/A' }}</td>
                    <td class="text-left font-bold" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">{{ number_format($purchase->quantity, 2) }}</td>
                    <td class="text-left" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">${{ number_format($purchase->price_per_kilo, 2) }}</td>
                    <td class="text-left font-bold" style="direction: ltr; height: 26pt; background-color: {{ $rowBgColor }};">${{ number_format($purchase->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="height: 26pt;">هیچ خریدی ثبت نشده است.</td>
                </tr>
            @endforelse
            
            <tr style="height: 10pt;"><td colspan="8" style="border: none; background-color: #ffffff;"></td></tr>

            <tr>
                <td colspan="5" class="meta-label" style="height: 26pt;">مجموع کل وزن:</td>
                <td class="text-left font-bold" style="background-color: #f1f5f9; direction: ltr; height: 26pt;">{{ number_format($purchases->sum('quantity'), 2) }}</td>
                <td class="meta-label" style="height: 26pt;">مبلغ کل (USD):</td>
                <td class="text-left font-bold" style="background-color: #eff6ff; color: #2563eb; direction: ltr; height: 26pt;">${{ number_format($bill->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($bill->allocations && $bill->allocations->count() > 0)
        <br><br>
        <table>
            <tr>
                <td colspan="6" class="report-title" style="height: 25pt; font-size: 13pt;" valign="middle">تاریخچه تادیات و پرداخت‌های بل خرید (Payment History)</td>
            </tr>
            <thead>
                <tr>
                    <th style="height: 25pt; background-color: #475569;">تاریخ پرداخت</th>
                    <th style="height: 25pt; background-color: #475569;">توضیحات تراکنش</th>
                    <th style="height: 25pt; background-color: #475569;">نوعیت پرداخت</th>
                    <th style="height: 25pt; background-color: #475569;">مقدار پرداختی ارز اصلی</th>
                    <th style="height: 25pt; background-color: #475569;">نرخ تسعیر</th>
                    <th style="height: 25pt; background-color: #475569;">معادل دالر (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->allocations as $pay)
                    @php($sp = $pay->seller_payment)
                    @if($sp)
                    <tr>
                        <td class="text-center" style="height: 22pt;">{{ $sp->date }}</td>
                        <td class="text-right" style="height: 22pt; padding-right: 10px;">{{ $sp->description }}</td>
                        <td class="text-center" style="height: 22pt;">{{ $sp->type == 'رسید' ? 'رسید (Inflow)' : 'گرفت (Outflow)' }}</td>
                        <td class="text-left" style="height: 22pt; padding-left: 10px; direction: ltr;">{{ number_format($pay->allocated_amount, 2) }} {{ $sp->currency_code }}</td>
                        <td class="text-center" style="height: 22pt; direction: ltr;">{{ number_format($pay->exchange_rate, 8) }}</td>
                        <td class="text-left font-bold" style="height: 22pt; padding-left: 10px; direction: ltr; color: #16a34a;">${{ number_format($pay->base_allocated_amount, 2) }}</td>
                    </tr>
                    @endif
                @endforeach
                
                <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td class="meta-label" style="height: 24pt;">مجموع پرداخت شده:</td>
                    <td class="text-left font-bold" style="background-color: #ecfdf5; color: #16a34a; direction: ltr; height: 24pt;">${{ number_format($bill->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td class="meta-label" style="height: 24pt;">باقیمانده بل:</td>
                    <td class="text-left font-bold" style="background-color: #fef2f2; color: #dc2626; direction: ltr; height: 24pt;">${{ number_format($bill->remaining_balance, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
