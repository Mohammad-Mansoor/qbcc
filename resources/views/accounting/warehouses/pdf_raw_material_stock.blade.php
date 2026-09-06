<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش موجودی مواد خام - Raw Material Stock Report</title>
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
            border-bottom: 2px solid #3730a3;
            padding-bottom: 8px;
        }

        .company-title {
            margin: 0 0 4px 0;
            color: #3730a3;
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
            width: 25%;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
            text-align: center;
        }

        .card-stat-title {
            font-size: 7.5pt;
            color: #475569;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .card-stat-value {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
        }

        table.stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.stock-table th {
            background-color: #3730a3 !important;
            color: #ffffff !important;
            font-weight: bold;
            border: 1px solid #4338ca;
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
            border-top: 2px solid #3730a3;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .text-indigo {
            color: #4338ca;
            font-weight: bold;
        }

        .text-cyan {
            color: #0891b2;
            font-weight: bold;
        }

        .text-amber {
            color: #b45309;
            font-weight: bold;
        }

        .text-rose {
            color: #be123c;
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
            <div class="report-subtitle">گزارش موجودی مواد خام (Raw Material Available Stock Report)</div>
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
            <span class="filter-tag">دسته مواد: {{ $selectedCategoryName }}</span>
            <span class="filter-tag">نوع مواد: {{ $selectedTypeName }}</span>
        </div>

        <!-- Top 4 Summary Cards Table -->
        <table class="summary-cards-table">
            <tr>
                <td style="background-color: #ecfeff;">
                    <div class="card-stat-title">موجودی رنگ (Available Dye)</div>
                    <div class="card-stat-value text-cyan">
                        {{ number_format($totalAvailableDyeKg, 2) }} KG | $ {{ number_format($totalAvailableDyeCost, 2) }}
                    </div>
                </td>
                <td style="background-color: #e0e7ff;">
                    <div class="card-stat-title">موجودی تار/الیاف (Available Yarn)</div>
                    <div class="card-stat-value text-indigo">
                        {{ number_format($totalAvailableYarnKg, 2) }} KG | $ {{ number_format($totalAvailableYarnCost, 2) }}
                    </div>
                </td>
                <td style="background-color: #fffbeb;">
                    <div class="card-stat-title">رنگ فروخته شده (Sold Dye)</div>
                    <div class="card-stat-value text-amber">
                        {{ number_format($totalSoldDyeKg, 2) }} KG | $ {{ number_format($totalSoldDyeCost, 2) }}
                    </div>
                </td>
                <td style="background-color: #fff1f2;">
                    <div class="card-stat-title">تار فروخته شده (Sold Yarn)</div>
                    <div class="card-stat-value text-rose">
                        {{ number_format($totalSoldYarnKg, 2) }} KG | $ {{ number_format($totalSoldYarnCost, 2) }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- High Density Categorized Stock Table -->
        <table class="stock-table">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 17%;">دسته مواد (Category)</th>
                    <th style="width: 18%;">نوع مواد (Material Type)</th>
                    <th style="width: 11%;">موجودی فعال (Avail. KG)</th>
                    <th style="width: 11%;">ارزش موجودی ($ USD)</th>
                    <th style="width: 11%;">فروخته شده (Sold KG)</th>
                    <th style="width: 11%;">ارزش فروش ($ USD)</th>
                    <th style="width: 9%;">کل خرید (Purchased KG)</th>
                    <th style="width: 9%;">ارزش کل خرید ($ USD)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandAvailKg = 0; $grandAvailCost = 0;
                    $grandSoldKg = 0; $grandSoldCost = 0;
                    $grandPurchasedKg = 0; $grandPurchasedCost = 0;
                @endphp
                @forelse($groupedMaterials as $index => $mat)
                    @php
                        $grandAvailKg += $mat['avail_kg'];
                        $grandAvailCost += $mat['avail_cost'];
                        $grandSoldKg += $mat['sold_kg'];
                        $grandSoldCost += $mat['sold_cost'];
                        $grandPurchasedKg += $mat['purchased_kg'];
                        $grandPurchasedCost += $mat['purchased_cost'];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ $mat['category_name'] }}</td>
                        <td style="text-align: right;">{{ $mat['type_name'] }}</td>
                        <td dir="ltr" class="text-indigo">{{ number_format($mat['avail_kg'], 2) }} KG</td>
                        <td dir="ltr" class="text-indigo">$ {{ number_format($mat['avail_cost'], 2) }}</td>
                        <td dir="ltr" class="text-rose">{{ number_format($mat['sold_kg'], 2) }} KG</td>
                        <td dir="ltr" class="text-rose">$ {{ number_format($mat['sold_cost'], 2) }}</td>
                        <td dir="ltr" style="font-weight: bold;">{{ number_format($mat['purchased_kg'], 2) }} KG</td>
                        <td dir="ltr" class="text-indigo" style="font-weight: bold;">$ {{ number_format($mat['purchased_cost'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding: 15px; color: #64748b;">هیچ مواد خامی یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">مجموع کل (Grand Total):</td>
                    <td dir="ltr" class="text-indigo">{{ number_format($grandAvailKg, 2) }} KG</td>
                    <td dir="ltr" class="text-indigo">$ {{ number_format($grandAvailCost, 2) }}</td>
                    <td dir="ltr" class="text-rose">{{ number_format($grandSoldKg, 2) }} KG</td>
                    <td dir="ltr" class="text-rose">$ {{ number_format($grandSoldCost, 2) }}</td>
                    <td dir="ltr">{{ number_format($grandPurchasedKg, 2) }} KG</td>
                    <td dir="ltr" class="text-indigo">$ {{ number_format($grandPurchasedCost, 2) }}</td>
                </tr>
            </tfoot>
        </table>

    </div>

</body>

</html>
