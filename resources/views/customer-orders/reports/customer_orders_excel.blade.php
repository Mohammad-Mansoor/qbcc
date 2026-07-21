<?php
if (!headers_sent()) {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-type: application/x-msexcel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Customer_Orders_Report_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $customer->name) . "_" . date('Y-m-d') . ".xls");
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
        $statusLabels = [
            'pending' => 'معلق',
            'in_progress' => 'در حال اجرا',
            'completed' => 'تکمیل شده',
            'cancel' => 'لغو شده',
        ];

        $statusProgress = [
            'graphing' => 10, 'dyeing' => 20, 'on_loom' => 40, 'off_loom' => 50,
            'washing' => 60, 'finishing' => 70, 'repairing' => 80, 'ready' => 100,
            'shipped' => 100, 'paused' => 0, 'cancelled' => 0
        ];
    @endphp

    <table border="0" style="margin-bottom: 20px;">
        <tr>
            <td colspan="8" style="text-align: center; font-size: 16pt; font-weight: bold; padding: 10px;">گزارش جامع فرمایشات مشتری (Customer Orders Report)</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-size: 11pt;">
                <strong>نام مشتری:</strong> {{ $customer->name }}{{ $customer->country ? " ($customer->country)" : '' }}<br>
                <strong>کد / تلفن مشتری:</strong> {{ $customer->phone ?: '---' }}<br>
                <strong>تاریخ صدور گزارش:</strong> {{ $issueDate }}
            </td>
            <td colspan="4" style="text-align: right; font-size: 11pt;">
                <strong>مجموع فرمایشات:</strong> {{ $kpis['total_orders'] }} سفارش<br>
                <strong>فرمایشات معلق / در حال اجرا:</strong> {{ $kpis['pending_orders'] }} معلق | {{ $kpis['in_progress_orders'] }} در حال اجرا<br>
                <strong>تکمیل شده / لغو شده:</strong> {{ $kpis['completed_orders'] }} تکمیل | {{ $kpis['canceled_orders'] }} لغو شده
            </td>
        </tr>
    </table>

    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <thead>
            <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center;">
                <th>ردیف</th>
                <th>نام / نمبر فرمایش</th>
                <th>نمبر فرمایش مشتری</th>
                <th>تاریخ ثبت سفارش</th>
                <th>تاریخ پایان (پیش‌بینی)</th>
                <th>تعداد قالین‌ها</th>
                <th>قالین‌های تکمیل شده</th>
                <th>میزان پیشرفت (%)</th>
                <th>مجموع مساحت (متر مربع)</th>
                <th>وضعیت سفارش</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer_orders as $index => $co)
                @php
                    $totalOrderCarpets = $co->details->count();
                    $completedOrderCarpets = $co->details->whereIn('current_status', ['ready', 'shipped'])->count();

                    $totalProgressScore = 0;
                    $validCarpetsCount = 0;
                    $orderArea = 0;
                    foreach($co->details as $carpet) {
                        if ($carpet->current_status != 'cancelled') {
                            $totalProgressScore += $statusProgress[$carpet->current_status] ?? 0;
                            $validCarpetsCount++;
                        }
                        $orderArea += (float)$carpet->area;
                    }
                    $progressPercentage = $validCarpetsCount > 0 ? round($totalProgressScore / $validCarpetsCount) : 0;
                    $statusLabel = $statusLabels[$co->status] ?? $co->status;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $co->order_name }}</td>
                    <td style="text-align: center;">{{ $co->customer_order_number ?: '---' }}</td>
                    <td style="text-align: center;">{{ $co->order_date }}</td>
                    <td style="text-align: center;">{{ $co->end_date ?: '---' }}</td>
                    <td style="text-align: center;">{{ $totalOrderCarpets }}</td>
                    <td style="text-align: center;">{{ $completedOrderCarpets }}</td>
                    <td style="text-align: center;">{{ $progressPercentage }}%</td>
                    <td style="text-align: center;">{{ number_format($orderArea, 2) }}</td>
                    <td style="text-align: center;">{{ $statusLabel }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">هیچ فرمایشی برای این مشتری یافت نشد.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($customer_orders) > 0)
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold; text-align: center;">
                <td colspan="5" style="text-align: center;">مجموع کلی</td>
                <td>{{ $kpis['total_carpets'] }}</td>
                <td>{{ $kpis['completed_carpets'] }}</td>
                <td>---</td>
                <td>{{ number_format($kpis['total_area'], 2) }}</td>
                <td>---</td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
