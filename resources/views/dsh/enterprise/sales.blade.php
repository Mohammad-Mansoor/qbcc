@extends('dsh.master')

@section('title', 'داشبورد فروشات | Executive Sales Intelligence')

@section('content')
<style>
    /* Premium Executive Glassmorphism */
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .dark-mode .glass-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    }

    .primary-text { color: #0f172a !important; }
    .dark-mode .primary-text { color: #f8fafc !important; }

    .secondary-text { color: #64748b !important; font-weight: 500; }
    .dark-mode .secondary-text { color: #94a3b8 !important; }

    .table-glass { color: #0f172a; }
    .dark-mode .table-glass { color: #f8fafc; }
    .table-glass thead th { color: #475569; border-bottom: 2px solid rgba(0,0,0,0.05); }
    .dark-mode .table-glass thead th { color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.1); }

    .kpi-value {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.8rem;
        background: linear-gradient(90deg, #1e293b, #334155);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dark-mode .kpi-value {
        background: linear-gradient(90deg, #ffffff, #c7d2fe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .kpi-title { color: #475569; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .dark-mode .kpi-title { color: #94a3b8; }

    .text-success-gradient { background: linear-gradient(90deg, #059669, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .dark-mode .text-success-gradient { background: linear-gradient(90deg, #10b981, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

    .text-danger-gradient { background: linear-gradient(90deg, #dc2626, #ef4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .dark-mode .text-danger-gradient { background: linear-gradient(90deg, #ef4444, #f87171); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

    .badge-glow-blue { background: rgba(59, 130, 246, 0.1); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.2); }
    .dark-mode .badge-glow-blue { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    
    .badge-glow-orange { background: rgba(249, 115, 22, 0.1); color: #ea580c; border: 1px solid rgba(249, 115, 22, 0.2); }
    .dark-mode .badge-glow-orange { background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3); }

    .data-box { background: rgba(0, 0, 0, 0.03); }
    .dark-mode .data-box { background: rgba(255, 255, 255, 0.05); }

    .timeline-item { border-right: 2px solid rgba(59, 130, 246, 0.3); padding-right: 1.5rem; position: relative; margin-bottom: 1.5rem; }
    .timeline-item::before { content: ''; position: absolute; right: -6px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; box-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
</style>

<div class="container-fluid py-4" dir="rtl">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="primary-text mb-0" style="font-weight: 700;">Executive Sales Intelligence</h2>
            <p class="secondary-text mb-0">داشبورد مدیریت فروشات و عواید</p>
        </div>
        <div>
            <button class="btn btn-outline-primary shadow-sm" onclick="window.print()"><i class="fas fa-print me-2"></i> چاپ راپور</button>
            <button class="btn btn-primary shadow-sm ms-2" onclick="location.reload()"><i class="fas fa-sync-alt me-2"></i> بروزرسانی</button>
        </div>
    </div>

    <!-- ROW 1: Executive KPI Cards -->
    <div class="row g-4 mb-4">
        <!-- Revenue -->
        <div class="col-xl-3 col-lg-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">عواید (Revenue)</div>
                    <div class="icon-box bg-success-subtle text-success rounded-circle p-2"><i class="fas fa-dollar-sign"></i></div>
                </div>
                <div class="kpi-value mt-3 text-success-gradient">${{ number_format($data['carpetAgg']->today_revenue + $data['materialAgg']->today_revenue) }} <small class="fs-6 text-muted">امروز</small></div>
                <div class="mt-3 text-sm">
                    <div class="d-flex justify-content-between mb-1"><span class="secondary-text">این ماه:</span><span class="primary-text fw-bold">${{ number_format($data['carpetAgg']->month_revenue + $data['materialAgg']->month_revenue) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="secondary-text">امسال:</span><span class="primary-text fw-bold">${{ number_format($data['carpetAgg']->year_revenue + $data['materialAgg']->year_revenue) }}</span></div>
                </div>
            </div>
        </div>
        
        <!-- Profit -->
        <div class="col-xl-3 col-lg-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">مفاد خالص (Profit)</div>
                    <div class="icon-box bg-primary-subtle text-primary rounded-circle p-2"><i class="fas fa-chart-line"></i></div>
                </div>
                <div class="kpi-value mt-3 text-success-gradient">+${{ number_format($data['carpetAgg']->today_profit + $data['materialAgg']->today_profit) }} <small class="fs-6 text-muted">امروز</small></div>
                <div class="mt-3 text-sm">
                    <div class="d-flex justify-content-between mb-1"><span class="secondary-text">این ماه:</span><span class="primary-text fw-bold">+${{ number_format($data['carpetAgg']->month_profit + $data['materialAgg']->month_profit) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="secondary-text">امسال:</span><span class="primary-text fw-bold">+${{ number_format($data['carpetAgg']->year_profit + $data['materialAgg']->year_profit) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Volume -->
        <div class="col-xl-3 col-lg-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">حجم فروش (Volume)</div>
                    <div class="icon-box bg-warning-subtle text-warning rounded-circle p-2"><i class="fas fa-boxes"></i></div>
                </div>
                <div class="mt-3">
                    <div class="row g-2 text-sm">
                        <div class="col-6"><div class="data-box p-2 rounded"><div class="secondary-text mb-1">قالین (تخته)</div><div class="primary-text fw-bold fs-5">{{ number_format($data['carpetAgg']->lifetime_count) }}</div></div></div>
                        <div class="col-6"><div class="data-box p-2 rounded"><div class="secondary-text mb-1">مساحت (m²)</div><div class="primary-text fw-bold fs-5">{{ number_format($data['carpetAgg']->lifetime_area, 1) }}</div></div></div>
                        <div class="col-6"><div class="data-box p-2 rounded"><div class="secondary-text mb-1">تار (kg)</div><div class="primary-text fw-bold fs-5">{{ number_format($data['materialAgg']->lifetime_yarn_kg) }}</div></div></div>
                        <div class="col-6"><div class="data-box p-2 rounded"><div class="secondary-text mb-1">رنگ (kg)</div><div class="primary-text fw-bold fs-5">{{ number_format($data['materialAgg']->lifetime_dye_kg) }}</div></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receivables -->
        <div class="col-xl-3 col-lg-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">طلب مالی (AR)</div>
                    <div class="icon-box bg-danger-subtle text-danger rounded-circle p-2"><i class="fas fa-hand-holding-usd"></i></div>
                </div>
                <div class="kpi-value mt-3 text-danger-gradient">${{ number_format($data['arAging']->total_outstanding) }}</div>
                <div class="mt-3 text-sm">
                    <div class="d-flex justify-content-between mb-1"><span class="secondary-text text-danger">معوقه (Overdue 30+):</span><span class="text-danger fw-bold">${{ number_format($data['arAging']->age_60 + $data['arAging']->age_90 + $data['arAging']->age_90_plus) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="secondary-text">انوایس های باقیدار:</span><span class="primary-text fw-bold">{{ number_format($data['arAging']->count_unpaid + $data['arAging']->count_partial) }} عدد</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: Revenue Trends (12 Months) -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="primary-text mb-4">روند ۱۲ ماهه عواید فروشات (12-Month Revenue Trends)</h5>
                <div id="revenueTrendChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- ROW 3: AR Aging & Receivables -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">عمر طلبات مالی (AR Aging Matrix)</h5>
                <div id="arAgingChart" style="height: 280px;"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">خلاصه تحصیلات (Collections Summary)</h5>
                <div class="row mt-4">
                    <div class="col-4 text-center">
                        <div class="secondary-text mb-2">مجموع تحصیل شده</div>
                        <div class="primary-text fs-4 fw-bold text-success">${{ number_format($data['arAging']->total_collected) }}</div>
                    </div>
                    <div class="col-4 text-center border-start border-end">
                        <div class="secondary-text mb-2">باقیداری جاری (۰-۳۰)</div>
                        <div class="primary-text fs-4 fw-bold text-warning">${{ number_format($data['arAging']->age_30) }}</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="secondary-text mb-2">باقیداری معوقه (۳۰+)</div>
                        <div class="primary-text fs-4 fw-bold text-danger">${{ number_format($data['arAging']->age_60 + $data['arAging']->age_90 + $data['arAging']->age_90_plus) }}</div>
                    </div>
                </div>
                <div class="mt-5 text-center">
                    <p class="secondary-text text-sm">کل انوایس ها: {{ $data['arAging']->count_total }} | باقیدار مطلق: {{ $data['arAging']->count_unpaid }} | قسماً پرداخت شده: {{ $data['arAging']->count_partial }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4 & 7: Product Sales & Profitability Intelligence -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="glass-card p-4">
                <h5 class="primary-text mb-4">تحلیل سودآوری محصولات (Product Profitability Intelligence)</h5>
                <div class="row">
                    <!-- Carpets -->
                    <div class="col-md-6 mb-4 mb-md-0 border-end">
                        <h6 class="secondary-text mb-3">قالین ها بر اساس نوعیت (Top Carpet Types)</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless table-glass align-middle table-sm">
                                <thead>
                                    <tr>
                                        <th>نوعیت</th>
                                        <th class="text-center">تعداد / m²</th>
                                        <th class="text-end">عواید</th>
                                        <th class="text-end">مفاد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['topCarpetTypes'] as $top)
                                    <tr>
                                        <td class="primary-text fw-bold">{{ $top->name }}</td>
                                        <td class="text-center text-info">{{ number_format($top->qty) }} <small>({{ number_format($top->area,1) }})</small></td>
                                        <td class="text-end text-primary">${{ number_format($top->revenue) }}</td>
                                        <td class="text-end text-success fw-bold">+${{ number_format($top->profit) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Materials -->
                    <div class="col-md-6">
                        <h6 class="secondary-text mb-3">پر فروش ترین مواد خام (Top Material Categories)</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless table-glass align-middle table-sm">
                                <thead>
                                    <tr>
                                        <th>دسته بندی</th>
                                        <th class="text-center">فروخته شده (kg)</th>
                                        <th class="text-end">عواید</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['topMaterialSales'] as $top)
                                    <tr>
                                        <td class="primary-text fw-bold">
                                            {{ $top->name }}
                                            <br><small class="{{ $top->subtype == 'yarn' ? 'text-primary' : 'text-warning' }}">{{ $top->subtype == 'yarn' ? 'تار' : 'رنگ' }}</small>
                                        </td>
                                        <td class="text-center text-info">{{ number_format($top->qty, 1) }}</td>
                                        <td class="text-end text-success fw-bold">${{ number_format($top->revenue) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 5: Customer Intelligence & Velocity -->
    <div class="row g-4 mb-4">
        <!-- Customers -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">تحلیل مشتریان ممتاز (Top Customers Intelligence)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-glass align-middle">
                        <thead>
                            <tr>
                                <th>مشتری</th>
                                <th class="text-center">تعداد خرید</th>
                                <th class="text-center">آخرین خرید</th>
                                <th class="text-end">مجموع عواید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['topCustomers'] as $cust)
                            <tr>
                                <td class="primary-text fw-bold">{{ $cust->name }}</td>
                                <td class="text-center text-primary">{{ number_format($cust->sales_count) }}</td>
                                <td class="text-center text-muted">{{ \Carbon\Carbon::parse($cust->last_purchase)->format('Y-m-d') }}</td>
                                <td class="text-end text-success fw-bold">${{ number_format($cust->total_revenue) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Velocity -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">سرعت فروش (Inventory Velocity)</h5>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="primary-text fw-bold">قالین (Carpet Turnaround)</span>
                        <span class="badge badge-glow-blue">{{ $data['velocity']['carpet'] }} روز</span>
                    </div>
                    <div class="secondary-text text-xs mt-1">اوسط روزهای ماندگاری در گدام قبل از فروش</div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="primary-text fw-bold">تار (Yarn Burn Rate)</span>
                        <span class="badge badge-glow-orange">{{ $data['velocity']['yarn'] }} روز</span>
                    </div>
                    <div class="secondary-text text-xs mt-1">موجودی گدام برای اوسط فروشات تکمیل است</div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="primary-text fw-bold">رنگ (Dye Burn Rate)</span>
                        <span class="badge badge-glow-purple">{{ $data['velocity']['dye'] }} روز</span>
                    </div>
                    <div class="secondary-text text-xs mt-1">موجودی گدام برای اوسط فروشات تکمیل است</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 6 & 9: Warehouse Intelligence & Activity -->
    <div class="row g-4">
        <!-- Warehouse -->
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">فروشات بر اساس گدام (Sales by Warehouse)</h5>
                
                <h6 class="secondary-text mb-2 text-sm">گدام های قالین</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-borderless table-glass align-middle table-sm">
                        <tbody>
                            @foreach($data['whCarpet'] as $wh)
                            <tr>
                                <td class="primary-text">{{ $wh->name }}</td>
                                <td class="text-center text-muted">{{ number_format($wh->qty) }} <small>تخته</small></td>
                                <td class="text-end text-success fw-bold">${{ number_format($wh->revenue) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h6 class="secondary-text mb-2 text-sm">گدام های مواد خام</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-glass align-middle table-sm">
                        <tbody>
                            @foreach($data['whMaterial'] as $whm)
                            <tr>
                                <td class="primary-text">{{ $whm->name }}</td>
                                <td class="text-center text-muted"><span class="text-primary">{{ number_format($whm->yarn_kg) }}kg تار</span> / <span class="text-warning">{{ number_format($whm->dye_kg) }}kg رنگ</span></td>
                                <td class="text-end text-success fw-bold">${{ number_format($whm->revenue) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">جریان فعالیت های فروش (Sales Activity Feed)</h5>
                <div class="pe-2" style="max-height: 400px; overflow-y: auto;">
                    @foreach($data['activities'] as $act)
                    <div class="timeline-item">
                        <div class="primary-text fs-6">{{ $act->description }}</div>
                        <div class="secondary-text text-xs mt-1">
                            <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }} 
                            <span class="mx-2">|</span>
                            <i class="far fa-user me-1"></i> {{ $act->user_name ?? 'System' }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.body.classList.contains('dark-mode');
    const labelColor = isDarkMode ? '#94a3b8' : '#64748b';
    const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const tooltipTheme = isDarkMode ? 'dark' : 'light';

    // 12-Month Revenue Trend
    var trendOptions = {
        series: [
            { name: 'عواید قالین (Carpet)', data: {!! json_encode($data['trends']['carpet']) !!} },
            { name: 'عواید تار (Yarn)', data: {!! json_encode($data['trends']['yarn']) !!} },
            { name: 'عواید رنگ (Dye)', data: {!! json_encode($data['trends']['dye']) !!} }
        ],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            background: 'transparent',
            stacked: true
        },
        colors: ['#3b82f6', '#f59e0b', '#8b5cf6'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.6, opacityTo: 0.1, stops: [0, 100] } },
        xaxis: { categories: {!! json_encode($data['trends']['months']) !!}, labels: { style: { colors: labelColor } } },
        yaxis: { labels: { style: { colors: labelColor }, formatter: (value) => { return "$" + value.toLocaleString() } } },
        legend: { labels: { colors: labelColor }, position: 'top' },
        tooltip: { theme: tooltipTheme, y: { formatter: function (val) { return "$" + val.toLocaleString() } } },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#revenueTrendChart"), trendOptions).render();

    // AR Aging Chart
    var agingOptions = {
        series: [{
            name: 'باقیداری ($)',
            data: [
                {{ $data['arAging']->age_30 }},
                {{ $data['arAging']->age_60 }},
                {{ $data['arAging']->age_90 }},
                {{ $data['arAging']->age_90_plus }}
            ]
        }],
        chart: { type: 'bar', height: 280, toolbar: { show: false }, background: 'transparent' },
        colors: ['#3b82f6', '#f59e0b', '#f97316', '#dc2626'],
        plotOptions: { bar: { borderRadius: 4, distributed: true, horizontal: false } },
        dataLabels: { enabled: true, formatter: function (val) { return "$" + val.toLocaleString() } },
        xaxis: { categories: ['۰ تا ۳۰ روز', '۳۱ تا ۶۰ روز', '۶۱ تا ۹۰ روز', 'معوقه (۹۰+)'], labels: { style: { colors: labelColor } } },
        yaxis: { labels: { show: false } },
        legend: { show: false },
        tooltip: { theme: tooltipTheme },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#arAgingChart"), agingOptions).render();
});
</script>
@endsection
