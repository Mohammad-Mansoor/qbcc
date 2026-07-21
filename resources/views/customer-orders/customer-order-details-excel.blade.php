<?php
if (!headers_sent()) {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-type: application/x-msexcel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Customer_Order_Details_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $customer_order->order_name) . "_" . date('Y-m-d') . ".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
}
echo "\xEF\xBB\xBF"; // UTF-8 BOM for Persian text in Excel
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body>

    @php
        $statusTranslation = [
            'graphing' => 'نقشه کشی',
            'dyeing' => 'رنگ ریزی',
            'on_loom' => 'در جریان بافت',
            'off_loom' => 'ختمِ بافت',
            'washing' => 'شستشو',
            'finishing' => 'تیاری',
            'repairing' => 'ترمیم',
            'ready' => 'آماده (تکمیل)',
            'shipped' => 'ارسال شده',
            'paused' => 'متوقف',
            'cancelled' => 'لغو شده',
            'completed' => 'تکمیل شده',
            'in_progress' => 'در حال اجرا',
            'pending' => 'معلق',
        ];

        $totalArea = 0;
        $totalCarpetsCount = count($customer_order_details);
        foreach($customer_order_details as $co) {
            $totalArea += (float)$co->area;
        }
    @endphp

    <!-- Title & Order Info -->
    <table border="0" style="margin-bottom: 20px;">
        <tr>
            <td colspan="12" style="text-align: center; font-size: 16pt; font-weight: bold; padding: 10px; background-color: #1e3a8a; color: #ffffff;">
                گزارش جزئیات و مشخصات قالین‌های فرمایش (Customer Order Details)
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right; font-size: 11pt; padding: 8px;">
                <strong>نام / نمبر فرمایش:</strong> #{{ $customer_order->order_name }}<br>
                <strong>نمبر فرمایش مشتری:</strong> {{ $customer_order->customer_order_number ?: '---' }}<br>
                <strong>نام مشتری:</strong> {{ optional($customer_order->customer)->name ?: 'N/A' }} {{ optional($customer_order->customer)->country ? '('.optional($customer_order->customer)->country.')' : '' }}
            </td>
            <td colspan="6" style="text-align: right; font-size: 11pt; padding: 8px;">
                <strong>تاریخ ثبت:</strong> {{ $customer_order->order_date }}<br>
                <strong>تاریخ ختم (پیش‌بینی):</strong> {{ $customer_order->end_date ?: '---' }}<br>
                <strong>تاریخ استخراج گزارش:</strong> {{ $issueDate }}
            </td>
        </tr>
    </table>

    <!-- Section 1: Grouped Status Statistics Table (11 Statuses) -->
    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; margin-bottom: 25px;">
        <thead>
            <tr>
                <th colspan="4" style="background-color: #312e81; color: #ffffff; font-size: 12pt; font-weight: bold; text-align: center;">
                    آمار قالین‌ها تفکیک‌شده بر اساس مراحل تولید و وضعیت (Status Breakdown Statistics)
                </th>
            </tr>
            <tr style="background-color: #4338ca; color: #ffffff; font-weight: bold; text-align: center;">
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">مرحله / وضعیت بافت</th>
                <th style="width: 25%;">تعداد قالین (تخته)</th>
                <th style="width: 25%;">مجموع مساحت (متر مربع m²)</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @foreach($statusStats as $stat)
                <tr style="{{ $stat['count'] > 0 ? 'background-color: #f0f9ff; font-weight: bold;' : '' }}">
                    <td style="text-align: center;">{{ $idx++ }}</td>
                    <td style="text-align: right; padding-right: 15px;">{{ $stat['label'] }}</td>
                    <td style="text-align: center; color: {{ $stat['count'] > 0 ? '#1e40af' : '#64748b' }};">
                        {{ $stat['count'] }} تخته
                    </td>
                    <td style="text-align: center; color: {{ $stat['total_area'] > 0 ? '#047857' : '#64748b' }};">
                        {{ number_format($stat['total_area'], 2) }} m²
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #dbeafe; font-weight: bold; text-align: center;">
                <td colspan="2" style="text-align: center;">مجموع کل تمامی مراحل</td>
                <td>{{ $totalCarpetsCount }} تخته</td>
                <td>{{ number_format($totalArea, 2) }} m²</td>
            </tr>
        </tfoot>
    </table>

    <!-- Section 2: Detailed Carpets List Table -->
    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="12" style="background-color: #1e3a8a; color: #ffffff; font-size: 12pt; font-weight: bold; text-align: center;">
                    جدول مشخصات تخنیکی قالین‌های سفارش
                </th>
            </tr>
            <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center;">
                <th>ردیف</th>
                <th>کیفیت</th>
                <th>طول (m)</th>
                <th>عرض (m)</th>
                <th>مساحت (m²)</th>
                <th>تار</th>
                <th>پود</th>
                <th>کد بافنده</th>
                <th>نمبر قالین</th>
                <th>تاریخ شروع</th>
                <th>تاریخ ختم</th>
                <th>وضعیت جاری</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer_order_details as $index => $co)
                @php
                    $statusLabel = $statusTranslation[$co->current_status] ?? $co->current_status;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $co->quality ?: '-' }}</td>
                    <td style="text-align: center;">{{ $co->height ?: '-' }}</td>
                    <td style="text-align: center;">{{ $co->width ?: '-' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format((float)$co->area, 2) }}</td>
                    <td style="text-align: center;">{{ $co->warp ?: '-' }}</td>
                    <td style="text-align: center;">{{ $co->weft ?: '-' }}</td>
                    <td style="text-align: center;">{{ $co->weaver_code ?: '-' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $co->carpet_number ?: '-' }}</td>
                    <td style="text-align: center;">{{ $co->start_date }}</td>
                    <td style="text-align: center;">{{ $co->end_date ?: '-' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $statusLabel }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center;">هیچ قالینی برای این فرمایش ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($customer_order_details) > 0)
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold; text-align: center;">
                <td colspan="4" style="text-align: center;">مجموع کل قالین‌ها</td>
                <td>{{ number_format($totalArea, 2) }}</td>
                <td colspan="7">تعداد: {{ $totalCarpetsCount }} تخته</td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
