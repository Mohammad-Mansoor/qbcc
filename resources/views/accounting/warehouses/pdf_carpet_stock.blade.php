<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش موجودی قالین‌ها - Carpet Stock Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 6mm;
            color: #000;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        .content-wrapper {
            padding: 0;
        }

        .company-header-block {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #047857;
            padding-bottom: 8px;
        }

        .company-title {
            margin: 0 0 4px 0;
            color: #047857;
            font-size: 16pt;
            font-weight: bold;
        }

        .report-subtitle {
            font-size: 10.5pt;
            font-weight: bold;
            color: #334155;
        }

        .filter-badge-section {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 12px;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }

        .filter-tag {
            background: #e2e8f0;
            color: #1e293b;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            margin-left: 8px;
        }

        table.summary-cards-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.summary-cards-table td {
            width: 33.33%;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            vertical-align: middle;
            text-align: center;
        }

        .card-stat-title {
            font-size: 8pt;
            color: #475569;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .card-stat-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }

        table.stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.stock-table th {
            background-color: #065f46 !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #047857;
            font-size: 8pt;
            padding: 5px 4px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        table.stock-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 5px;
            font-size: 8pt;
            color: #000;
            vertical-align: middle;
            text-align: center;
        }

        table.stock-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        table.stock-table tfoot tr td {
            font-weight: bold;
            background-color: #f1f5f9 !important;
            border-top: 2px solid #047857;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-emerald {
            color: #047857;
            font-weight: bold;
        }

        .text-amber {
            color: #b45309;
            font-weight: bold;
        }

        .text-blue {
            color: #1d4ed8;
            font-weight: bold;
        }

        @media print {
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-row-group !important;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="content-wrapper">

        <!-- Company Header Block -->
        <div class="company-header-block">
            <h2 class="company-title">{{ config('company.name') }}</h2>
            <div class="report-subtitle">گزارش موجودی قالین‌ها (Carpet Available Stock Report)</div>
        </div>

        <!-- Report Metadata -->
        <table style="width: 100%; margin-bottom: 8px; border: none;">
            <tr>
                <td style="text-align: right; font-size: 8.5pt; color: #475569;">
                    توسط: <strong>{{ auth()->user()->name }}</strong>
                </td>
                <td style="text-align: left; font-size: 8.5pt; color: #475569;">
                    تاریخ چاپ: <strong>{{ date('Y-m-d H:i') }}</strong>
                </td>
            </tr>
        </table>

        <!-- Applied Filters Display Banner -->
        <div class="filter-badge-section">
            <strong>فیلترهای اعمال شده:</strong>
            <span class="filter-tag">کیفیت: {{ $selectedQualityName }}</span>
            <span class="filter-tag">نوعیت / دیزاین: {{ $selectedTypeName }}</span>
        </div>

        <!-- Top 3 Summary Cards Table -->
        <table class="summary-cards-table">
            <tr>
                <td style="background-color: #ecfdf5;">
                    <div class="card-stat-title">موجودی کل قالین‌ها (Total Inventory)</div>
                    <div class="card-stat-value text-emerald">
                        {{ number_format($totalAvailableQty) }} تخته | {{ number_format($totalAvailableSqm, 2) }} m²
                    </div>
                </td>
                <td style="background-color: #fef2f2;">
                    <div class="card-stat-title">قالین‌های فروخته شده (Total Sold)</div>
                    <div class="card-stat-value" style="color: #dc2626;">
                        {{ number_format($totalSoldQty) }} تخته | {{ number_format($totalSoldSqm, 2) }} m²
                    </div>
                </td>
                <td style="background-color: #eff6ff;">
                    <div class="card-stat-title">قالین‌های آماده فروش (Ready to Sale)</div>
                    <div class="card-stat-value text-blue">
                        {{ number_format($totalReadyQty) }} تخته | {{ number_format($totalReadySqm, 2) }} m²
                    </div>
                </td>
            </tr>
        </table>

                        <!-- High Density Categorized Stock Table -->
                        <table class="stock-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th style="width: 15%;">کیفیت / دسته (Quality)</th>
                                    <th style="width: 15%;">نوعیت / دیزاین (Type)</th>
                                    <th style="width: 7%;">تولید (WIP Pcs)</th>
                                    <th style="width: 9%;">مساحت تولید (WIP m²)</th>
                                    <th style="width: 7%;">آماده (Ready Pcs)</th>
                                    <th style="width: 9%;">مساحت آماده (Ready m²)</th>
                                    <th style="width: 7%;">فروش (Sold Pcs)</th>
                                    <th style="width: 9%;">مساحت فروش (Sold m²)</th>
                                    <th style="width: 9%;">کل مساحت (Total m²)</th>
                                    <th style="width: 10%;">ارزش کل ($ USD)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandWipQty = 0; $grandWipSqm = 0;
                                    $grandReadyQty = 0; $grandReadySqm = 0;
                                    $grandSoldQty = 0; $grandSoldSqm = 0;
                                    $grandTotalSqm = 0; $grandTotalCost = 0;
                                @endphp
                                @forelse($groupedCategories as $index => $group)
                                    @php
                                        $grandWipQty += $group->wip_qty;
                                        $grandWipSqm += $group->wip_sqm;
                                        $grandReadyQty += $group->ready_qty;
                                        $grandReadySqm += $group->ready_sqm;
                                        $grandSoldQty += $group->sold_qty;
                                        $grandSoldSqm += $group->sold_sqm;
                                        $grandTotalSqm += $group->total_sqm;
                                        $grandTotalCost += $group->total_cost;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td style="text-align: right; font-weight: bold;">{{ $group->quality_name }}</td>
                                        <td style="text-align: right;">{{ $group->type_name }}</td>
                                        <td class="text-amber">{{ number_format($group->wip_qty) }}</td>
                                        <td dir="ltr" class="text-amber">{{ number_format($group->wip_sqm, 2) }} m²</td>
                                        <td class="text-emerald">{{ number_format($group->ready_qty) }}</td>
                                        <td dir="ltr" class="text-emerald">{{ number_format($group->ready_sqm, 2) }} m²</td>
                                        <td>{{ number_format($group->sold_qty) }}</td>
                                        <td dir="ltr">{{ number_format($group->sold_sqm, 2) }} m²</td>
                                        <td dir="ltr" style="font-weight: bold;">{{ number_format($group->total_sqm, 2) }} m²</td>
                                        <td dir="ltr" class="text-emerald" style="font-weight: bold;">$ {{ number_format($group->total_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" style="padding: 15px; color: #64748b;">هیچ رکوردی یافت نشد.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right;">مجموع کل (Grand Total):</td>
                                    <td class="text-amber">{{ number_format($grandWipQty) }}</td>
                                    <td dir="ltr" class="text-amber">{{ number_format($grandWipSqm, 2) }} m²</td>
                                    <td class="text-emerald">{{ number_format($grandReadyQty) }}</td>
                                    <td dir="ltr" class="text-emerald">{{ number_format($grandReadySqm, 2) }} m²</td>
                                    <td>{{ number_format($grandSoldQty) }}</td>
                                    <td dir="ltr">{{ number_format($grandSoldSqm, 2) }} m²</td>
                                    <td dir="ltr">{{ number_format($grandTotalSqm, 2) }} m²</td>
                                    <td dir="ltr" class="text-emerald">$ {{ number_format($grandTotalCost, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

    </div>

</body>

</html>
