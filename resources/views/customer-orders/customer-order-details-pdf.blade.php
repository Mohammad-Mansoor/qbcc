<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>Customer Order Details - {{ $customer_order->order_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 6mm;
        }

        body {
            font-family: 'Noto Sans Arabic', Tahoma, Arial, sans-serif;
            background-color: #fff;
            color: #1e293b;
            font-size: 8pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        /* Suppress URL links printed by browser */
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

        /* ── Header ── */
        .report-header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 5px;
            margin-bottom: 10px;
            display: table;
        }

        .header-left,
        .header-center,
        .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 30%;
        }

        .header-center {
            width: 40%;
            text-align: center;
        }

        .header-right {
            width: 30%;
            text-align: left;
        }

        .company-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }

        .report-title-badge {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 12px;
            color: #1d4ed8;
            font-weight: bold;
            font-size: 10pt;
            display: inline-block;
        }

        /* ── Meta card ── */
        table.meta-card {
            width: 100%;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
            border-radius: 8px;
            color: white;
            border-collapse: collapse;
        }
        
        table.meta-card td {
            padding: 8px 12px;
            vertical-align: middle;
            border-left: 1px solid rgba(255,255,255,0.2);
        }
        table.meta-card td:last-child { border-left: none; }
        .meta-label { font-size: 7.5pt; color: #cbd5e1; display: block; margin-bottom: 2px; }
        .meta-val { font-size: 9.5pt; color: #ffffff; font-weight: bold; margin: 0; }

        /* ── Data table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
        }

        table.data-table th {
            background-color: #1e3a8a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 8pt;
            border: 1px solid #94a3b8;
            padding: 5px;
            text-align: center;
        }

        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            font-size: 8pt;
            text-align: center;
            vertical-align: middle;
            line-height: 1.2;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        table.data-table td:first-child, table.data-table th:first-child { text-align: right; padding-right: 10px; }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }
        
        .badge {
            padding: 3px 6px; border-radius: 6px; font-size: 7pt; font-weight: bold;
            display: inline-block; -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .badge-info { background: #dbeafe !important; color: #1e40af !important; border: 1px solid #bfdbfe; }
        .badge-success { background: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
        .badge-warning { background: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }

        /* Totals table */
        table.totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-top: 2px solid #1e3a8a;
            page-break-inside: avoid;
        }

        table.totals-table td {
            background-color: #e7edf8 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-weight: bold;
            font-size: 8.5pt;
            border: 1px solid #94a3b8;
            padding: 6px 10px;
        }
    </style>
</head>

<body onload="window.print()">

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
            'in_progress' => 'در جریان',
            'in progress' => 'در جریان',
            'completed' => 'تکمیل شده',
            'pending' => 'معلق',
            'On loom' => 'در جریان بافت'
        ];
        
        $totalArea = 0;
        foreach($customer_order_details as $co) {
            $totalArea += (float)$co->area;
        }
    @endphp

    <!-- Header -->
    <div class="report-header">
        <div class="header-left">
            <table style="border:none; width:auto;">
                <tr>
                    <td style="border:none; padding:0; vertical-align:middle; width:45px;">
                        @if(isset($logoBase64) && $logoBase64)
                            <img src="{{ $logoBase64 }}" style="height:38px; width:auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="border:none; padding:0 0 0 7px; vertical-align:middle;">
                        <p class="company-name">{{ config('company.name') }}</p>
                        <div style="font-size: 8pt; color: #475569;">{{ config('company.description') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-center">
            <div class="report-title-badge">گزارش جامع فرمایش مشتری</div>
        </div>
        <div class="header-right">
            <span style="font-size:7.5pt; color:#475569; direction:ltr; display:block;">تاریخ صدور: {{ date('Y-m-d H:i') }}</span>
            <span style="font-size:7.5pt; color:#1d4ed8; font-weight:bold;">{{ count($customer_order_details) }} تخته &nbsp;|&nbsp; {{ number_format($totalArea, 2) }} m²</span>
        </div>
    </div>

    <!-- Meta card -->
    <table class="meta-card">
        <tr>
            <td style="width:25%;">
                <span class="meta-label">نمبر / نام سفارش</span>
                <span class="meta-val" style="direction: ltr; font-family: monospace; color: #fde68a;">#{{ $customer_order->order_name }}</span>
                @if($customer_order->customer_order_number)
                <span class="meta-label" style="margin-top: 4px;">نمبر فرمایش مشتری</span>
                <span class="meta-val" style="direction: ltr; font-family: monospace; color: #fde68a;">{{ $customer_order->customer_order_number }}</span>
                @endif
            </td>
            <td style="width:30%;">
                <span class="meta-label">مشتری</span>
                <span class="meta-val">{{ optional($customer_order->customer)->name ?? '-' }} {{ optional($customer_order->customer)->country ? '('.optional($customer_order->customer)->country.')' : '' }}</span>
            </td>
            <td style="width:15%;">
                <span class="meta-label">تاریخ ثبت</span>
                <span class="meta-val" style="direction: ltr;">{{ $customer_order->order_date }}</span>
            </td>
            <td style="width:15%;">
                <span class="meta-label">تاریخ تحویل</span>
                <span class="meta-val" style="direction: ltr; color: #fca5a5;">{{ $customer_order->end_date ?? '-' }}</span>
            </td>
            <td style="width:15%;">
                <span class="meta-label">وضعیت کلی</span>
                @php $overallStatus = $statusTranslation[$customer_order->status] ?? $customer_order->status; @endphp
                <span class="meta-val" style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px;">{{ $overallStatus }}</span>
            </td>
        </tr>
    </table>

    <!-- Grouped Status Statistics Table (11 Statuses) -->
    @if(isset($statusStats) && count($statusStats) > 0)
    <div style="margin-bottom: 12px;">
        <div style="font-size: 8pt; font-weight: bold; color: #1e3a8a; margin-bottom: 4px;">
            آمار قالین‌ها تفکیک‌شده بر اساس ۱۱ مرحله بافت و تولید:
        </div>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1; font-size: 7.5pt;">
            <thead>
                <tr style="background-color: #312e81; color: #ffffff;">
                    @foreach($statusStats as $stat)
                        <th style="border: 1px solid #475569; padding: 4px 2px; text-align: center; font-size: 7pt; width: 9.09%;">
                            {{ $stat['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($statusStats as $stat)
                        <td style="border: 1px solid #cbd5e1; padding: 5px 2px; text-align: center; background-color: {{ $stat['count'] > 0 ? '#eff6ff' : '#ffffff' }};">
                            <span style="font-weight: bold; color: {{ $stat['count'] > 0 ? '#1d4ed8' : '#64748b' }}; display: block;">
                                {{ $stat['count'] }} تخته
                            </span>
                            <span style="font-size: 6.8pt; color: {{ $stat['total_area'] > 0 ? '#047857' : '#94a3b8' }}; display: block; margin-top: 2px;">
                                {{ number_format($stat['total_area'], 2) }} m²
                            </span>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Data table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:12%; text-align:right;">کیفیت (Quality)</th>
                <th style="width:6%;">طول</th>
                <th style="width:6%;">عرض</th>
                <th style="width:7%;">مساحت</th>
                <th style="width:8%;">تار (Warp)</th>
                <th style="width:8%;">پود (Weft)</th>
                <th style="width:8%;">نوع شست</th>
                <th style="width:9%;">کد بافنده</th>
                <th style="width:10%;">نمبر قالین</th>
                <th style="width:8%;">شروع</th>
                <th style="width:8%;">ختم</th>
                <th style="width:6%;">وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer_order_details as $index => $co)
                @php 
                    $translatedStatus = $statusTranslation[$co->current_status] ?? $co->current_status;
                    $badgeStyle = 'badge-info';
                    if (in_array($co->current_status, ['ready', 'shipped', 'completed'])) {
                        $badgeStyle = 'badge-success';
                    } elseif (in_array($co->current_status, ['paused', 'cancelled', 'repairing'])) {
                        $badgeStyle = 'badge-warning';
                    }
                @endphp
                <tr>
                    <td class="text-right">{{ $index + 1 }}</td>
                    <td class="font-bold text-right" style="color:#1d4ed8;">{{ $co->quality ?: '-' }}</td>
                    <td style="direction:ltr;">{{ $co->height ?: '-' }}</td>
                    <td style="direction:ltr;">{{ $co->width ?: '-' }}</td>
                    <td class="font-bold" style="direction:ltr;">{{ number_format((float)$co->area, 2) }}</td>
                    <td>{{ $co->warp ?: '-' }}</td>
                    <td>{{ $co->weft ?: '-' }}</td>
                    <td>{{ $co->wash_type ?: '-' }}</td>
                    <td>{{ $co->weaver_code ?: '-' }}</td>
                    <td class="font-bold">{{ $co->carpet_number ?: '-' }}</td>
                    <td style="direction:ltr;">{{ $co->start_date ?: '-' }}</td>
                    <td style="direction:ltr;">{{ $co->end_date ?: '-' }}</td>
                    <td><span class="badge {{ $badgeStyle }}">{{ $translatedStatus ?: '-' }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding:15px;">هیچ قالینی در این فرمایش ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals: separate table --}}
    @if(count($customer_order_details) > 0)
        <table class="totals-table">
            <tbody>
                <tr>
                    <td style="text-align:right; width: 32%; border-left: none;">
                        <span style="color:#475569; font-weight:normal;">تعداد کل قالین‌ها:</span>
                        <span style="color:#1e3a8a; font-size: 11pt; margin-right:5px;">{{ count($customer_order_details) }} تخته</span>
                    </td>
                    <td style="text-align:right; width: 68%; border-right: none;">
                        <span style="color:#475569; font-weight:normal;">مجموع مساحت سفارش:</span>
                        <span style="color:#1e3a8a; font-size: 11pt; margin-right:5px; direction:ltr; display:inline-block;">{{ number_format($totalArea, 2) }} m²</span>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

</body>

</html>
