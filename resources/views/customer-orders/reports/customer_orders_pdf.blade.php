<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش فرمایشات مشتری - {{ $customer->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            font-family: 'Noto Sans Arabic', Tahoma, Arial, sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            font-size: 8.5pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        @media print {
            a[href]::after {
                content: none !important;
            }
            thead {
                display: table-header-group;
            }
            tr {
                page-break-inside: avoid;
            }
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Header Bar */
        .report-header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 12px;
            display: table;
        }

        .header-left, .header-center, .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 35%;
        }

        .header-center {
            width: 30%;
            text-align: center;
        }

        .header-right {
            width: 35%;
            text-align: left;
        }

        .company-name {
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }

        .report-title-badge {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 5px 14px;
            border-radius: 20px;
            color: #1d4ed8;
            font-weight: bold;
            font-size: 10.5pt;
            display: inline-block;
        }

        /* Summary Meta Cards */
        .meta-container {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .meta-card-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            text-align: center;
        }

        .meta-card-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
            color: #ffffff;
            border: none;
        }

        .meta-label {
            font-size: 7.5pt;
            color: #64748b;
            display: block;
            margin-bottom: 3px;
        }

        .meta-card-primary .meta-label {
            color: #cbd5e1;
        }

        .meta-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }

        .meta-card-primary .meta-value {
            color: #ffffff;
        }

        /* Table Design */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-bottom: 12px;
        }

        table.data-table th {
            background-color: #1e3a8a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 8.5pt;
            border: 1px solid #64748b;
            padding: 7px 6px;
            text-align: center;
        }

        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 7px;
            font-size: 8.5pt;
            text-align: center;
            vertical-align: middle;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7.5pt;
            font-weight: bold;
            display: inline-block;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .badge-pending { background: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
        .badge-in_progress { background: #dbeafe !important; color: #1e40af !important; border: 1px solid #bfdbfe; }
        .badge-completed { background: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
        .badge-cancel { background: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fca5a5; }

        /* Progress Mini Bar */
        .progress-bar-bg {
            background-color: #e2e8f0;
            border-radius: 4px;
            height: 8px;
            width: 80px;
            display: inline-block;
            vertical-align: middle;
            overflow: hidden;
        }

        .progress-bar-fill {
            background-color: #2563eb;
            height: 100%;
            border-radius: 4px;
        }

        .progress-bar-complete {
            background-color: #059669;
        }

        /* Totals Footer */
        table.totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-top: 2px solid #1e3a8a;
            page-break-inside: avoid;
        }

        table.totals-table td {
            background-color: #eff6ff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #94a3b8;
            padding: 8px 12px;
        }
    </style>
</head>
<body onload="window.print()">

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

    <!-- Top Header -->
    <div class="report-header">
        <div class="header-left">
            <table style="border:none; width:auto;">
                <tr>
                    <td style="border:none; padding:0; vertical-align:middle; width:45px;">
                        @if(isset($logoBase64) && $logoBase64)
                            <img src="{{ $logoBase64 }}" style="height:40px; width:auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="border:none; padding:0 0 0 8px; vertical-align:middle;">
                        <p class="company-name">شرکت صنعتی برادران قاسمی</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-center">
            <div class="report-title-badge">گزارش فرمایشات مشتری</div>
        </div>
        <div class="header-right">
            <span style="font-size:8pt; color:#475569; direction:ltr; display:block;">تاریخ صدور: {{ $issueDate }}</span>
            <span style="font-size:8.5pt; color:#1d4ed8; font-weight:bold;">مشتری: {{ $customer->name }}{{ $customer->country ? " ($customer->country)" : '' }}</span>
        </div>
    </div>

    <!-- Meta Summary Bar -->
    <table class="meta-container">
        <tr>
            <td class="meta-card-item meta-card-primary" style="width: 20%;">
                <span class="meta-label">نام / کد مشتری</span>
                <span class="meta-value">{{ $customer->name }}</span>
            </td>
            <td class="meta-card-item" style="width: 15%;">
                <span class="meta-label">مجموع فرمایشات</span>
                <span class="meta-value">{{ $kpis['total_orders'] }}</span>
            </td>
            <td class="meta-card-item" style="width: 15%;">
                <span class="meta-label">معلق / در حال اجرا</span>
                <span class="meta-value">{{ $kpis['pending_orders'] }} / {{ $kpis['in_progress_orders'] }}</span>
            </td>
            <td class="meta-card-item" style="width: 15%;">
                <span class="meta-label">تکمیل شده</span>
                <span class="meta-value" style="color: #059669;">{{ $kpis['completed_orders'] }}</span>
            </td>
            <td class="meta-card-item" style="width: 15%;">
                <span class="meta-label">تعداد کل قالین‌ها</span>
                <span class="meta-value">{{ $kpis['total_carpets'] }} تخته</span>
            </td>
            <td class="meta-card-item" style="width: 20%;">
                <span class="meta-label">مجموع مساحت سفارشات</span>
                <span class="meta-value" style="direction: ltr;">{{ number_format($kpis['total_area'], 2) }} m²</span>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 18%; text-align: right; padding-right: 10px;">نام / نمبر فرمایش</th>
                <th style="width: 15%;">نمبر فرمایش مشتری</th>
                <th style="width: 11%;">تاریخ ثبت</th>
                <th style="width: 11%;">تاریخ پایان (پیش‌بینی)</th>
                <th style="width: 13%;">تعداد قالین‌ها</th>
                <th style="width: 13%;">میزان پیشرفت %</th>
                <th style="width: 15%;">وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer_orders as $index => $co)
                @php
                    $totalOrderCarpets = $co->details->count();
                    $completedOrderCarpets = $co->details->whereIn('current_status', ['ready', 'shipped'])->count();

                    $totalProgressScore = 0;
                    $validCarpetsCount = 0;
                    foreach($co->details as $carpet) {
                        if ($carpet->current_status != 'cancelled') {
                            $totalProgressScore += $statusProgress[$carpet->current_status] ?? 0;
                            $validCarpetsCount++;
                        }
                    }
                    $progressPercentage = $validCarpetsCount > 0 ? round($totalProgressScore / $validCarpetsCount) : 0;
                    $statusLabel = $statusLabels[$co->status] ?? $co->status;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right; padding-right: 10px; font-weight: bold; color: #1e3a8a;">
                        {{ $co->order_name }}
                    </td>
                    <td style="direction: ltr; font-family: monospace;">{{ $co->customer_order_number ?: '---' }}</td>
                    <td style="direction: ltr;">{{ $co->order_date }}</td>
                    <td style="direction: ltr; color: #64748b;">{{ $co->end_date ?: '---' }}</td>
                    <td>
                        <span style="font-weight: bold; color: #0f172a;">{{ $totalOrderCarpets }}</span> تخته
                        <small style="color: #64748b;">({{ $completedOrderCarpets }} تکمیل)</small>
                    </td>
                    <td>
                        <span style="font-weight: bold; margin-left: 4px;">{{ $progressPercentage }}%</span>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill {{ $progressPercentage == 100 ? 'progress-bar-complete' : '' }}" style="width: {{ $progressPercentage }}%;"></div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $co->status }}">{{ $statusLabel }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 15px; color: #64748b;">هیچ فرمایشی برای این مشتری ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totals Bar -->
    @if(count($customer_orders) > 0)
        <table class="totals-table">
            <tr>
                <td style="width: 33%; text-align: right;">
                    <span style="color: #475569; font-weight: normal;">تعداد کل فرمایشات:</span>
                    <span style="color: #1e3a8a; font-size: 10.5pt; margin-right: 5px;">{{ $kpis['total_orders'] }} سفارش</span>
                </td>
                <td style="width: 33%; text-align: center;">
                    <span style="color: #475569; font-weight: normal;">مجموع قالین‌های فرمایش داده شده:</span>
                    <span style="color: #1e3a8a; font-size: 10.5pt; margin-right: 5px;">{{ $kpis['total_carpets'] }} تخته</span>
                </td>
                <td style="width: 34%; text-align: left; direction: ltr;">
                    <span style="color: #475569; font-weight: normal;">مجموع مساحت:</span>
                    <span style="color: #1e3a8a; font-size: 10.5pt; margin-left: 5px;">{{ number_format($kpis['total_area'], 2) }} m²</span>
                </td>
            </tr>
        </table>
    @endif

</body>
</html>
