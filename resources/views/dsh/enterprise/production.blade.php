@extends('dsh.master')

@section('title', 'داشبورد تولید و عملیات | Production Intelligence')

@section('content')
<style>
    /* Glassmorphism Dynamic Theme */
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

    /* Text Colors */
    .primary-text {
        color: #0f172a !important;
    }
    .dark-mode .primary-text {
        color: #f8fafc !important;
    }

    .secondary-text {
        color: #64748b !important;
        font-weight: 500;
    }
    .dark-mode .secondary-text {
        color: #94a3b8 !important;
    }

    .table-glass {
        color: #0f172a;
    }
    .dark-mode .table-glass {
        color: #f8fafc;
    }
    .table-glass thead th {
        color: #475569;
        border-bottom: 2px solid rgba(0,0,0,0.05);
    }
    .dark-mode .table-glass thead th {
        color: #94a3b8;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    /* KPI Typographies */
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

    .kpi-title {
        color: #475569;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .dark-mode .kpi-title {
        color: #94a3b8;
    }

    /* Gradients */
    .text-success-gradient {
        background: linear-gradient(90deg, #059669, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dark-mode .text-success-gradient {
        background: linear-gradient(90deg, #10b981, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .text-danger-gradient {
        background: linear-gradient(90deg, #dc2626, #ef4444);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dark-mode .text-danger-gradient {
        background: linear-gradient(90deg, #ef4444, #f87171);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Badges */
    .badge-glow-blue { background: rgba(59, 130, 246, 0.1); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.2); }
    .dark-mode .badge-glow-blue { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    
    .badge-glow-orange { background: rgba(249, 115, 22, 0.1); color: #ea580c; border: 1px solid rgba(249, 115, 22, 0.2); }
    .dark-mode .badge-glow-orange { background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3); }
    
    .badge-glow-purple { background: rgba(139, 92, 246, 0.1); color: #7c3aed; border: 1px solid rgba(139, 92, 246, 0.2); }
    .dark-mode .badge-glow-purple { background: rgba(139, 92, 246, 0.2); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3); }

    /* Timeline */
    .timeline-item {
        border-right: 2px solid rgba(59, 130, 246, 0.3);
        padding-right: 1.5rem;
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        right: -6px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #3b82f6;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    }

    /* Sub-containers */
    .data-box {
        background: rgba(0, 0, 0, 0.03);
    }
    .dark-mode .data-box {
        background: rgba(255, 255, 255, 0.05);
    }
</style>

<div class="container-fluid py-4" dir="rtl">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="primary-text mb-0" style="font-weight: 700;">داشبورد تولید و عملیات</h2>
            <p class="secondary-text mb-0">Production & Executive Intelligence Center</p>
        </div>
        <div>
            <button class="btn btn-outline-primary shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i> چاپ راپور
            </button>
        </div>
    </div>

    <!-- ROW 1: Executive KPIs -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-lg-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">موجودی قالین (Carpet Stock)</div>
                    <div class="icon-box bg-primary-subtle text-primary rounded-circle p-2"><i class="fas fa-layer-group"></i></div>
                </div>
                <div class="kpi-value mt-3">{{ number_format($data['carpetInventory']->total_qty) }} <small class="fs-6 text-muted">تخته</small></div>
                <div class="d-flex justify-content-between mt-2 text-sm">
                    <span class="secondary-text">مساحت:</span>
                    <span class="primary-text fw-bold">{{ number_format($data['carpetInventory']->total_area, 2) }} m²</span>
                </div>
                <div class="d-flex justify-content-between mt-1 text-sm">
                    <span class="secondary-text">ارزش مجموعی:</span>
                    <span class="primary-text fw-bold">${{ number_format($data['carpetInventory']->total_value) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">موجودی مواد خام (Materials)</div>
                    <div class="icon-box bg-warning-subtle text-warning rounded-circle p-2"><i class="fas fa-boxes"></i></div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="secondary-text">تار (Yarn):</span>
                        <span class="primary-text fw-bold fs-5">{{ number_format($data['rawMaterials']->yarn_kg) }} <small class="text-muted fs-6">kg</small></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="secondary-text">رنگ (Dye):</span>
                        <span class="primary-text fw-bold fs-5">{{ number_format($data['rawMaterials']->dye_kg) }} <small class="text-muted fs-6">kg</small></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between">
                    <div class="kpi-title">مصارف عملیاتی (Costs)</div>
                    <div class="icon-box bg-danger-subtle text-danger rounded-circle p-2"><i class="fas fa-money-bill-wave"></i></div>
                </div>
                <div class="kpi-value mt-3 text-danger-gradient">${{ number_format($data['costs']->repair_cost + $data['costs']->washing_cost + $data['costs']->finishing_cost) }}</div>
                <div class="mt-2 text-sm">
                    <div class="d-flex justify-content-between"><span class="secondary-text">ترمیم:</span><span class="primary-text fw-bold">${{ number_format($data['costs']->repair_cost) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="secondary-text">شست:</span><span class="primary-text fw-bold">${{ number_format($data['costs']->washing_cost) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="secondary-text">تیاری:</span><span class="primary-text fw-bold">${{ number_format($data['costs']->finishing_cost) }}</span></div>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2: Funnel & WIP Queues -->
    <div class="row g-4 mb-4">
        <!-- Production Funnel -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">مراحل تولید قالین (Production Lifecycle Funnel)</h5>
                <div id="funnelChart" style="height: 300px;"></div>
            </div>
        </div>

        <!-- WIP Queues -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">جریان کار در حال اجرا (WIP Queues)</h5>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="primary-text fw-bold">کچایی (Repair Queue)</span>
                        <span class="badge badge-glow-orange">{{ number_format($data['wipRepair']->count) }} تخته</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(0,0,0,0.05);">
                        <div class="progress-bar bg-warning" style="width: 70%"></div>
                    </div>
                    <div class="secondary-text text-xs mt-1">{{ number_format($data['wipRepair']->area, 2) }} m² در حال جریان</div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="primary-text fw-bold">شستشو (Wash Queue)</span>
                        <span class="badge badge-glow-info" style="background-color: #0ea5e9; color: white;">{{ number_format($data['wipWash']->count) }} تخته</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(0,0,0,0.05);">
                        <div class="progress-bar bg-info" style="width: 60%; background-color: #0ea5e9;"></div>
                    </div>
                    <div class="secondary-text text-xs mt-1">{{ number_format($data['wipWash']->area, 2) }} m² در حال جریان</div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="primary-text fw-bold">تیاری (Finishing Queue)</span>
                        <span class="badge badge-glow-purple">{{ number_format($data['wipFinish']->count) }} تخته</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(0,0,0,0.05);">
                        <div class="progress-bar bg-purple" style="width: 45%; background-color: #a855f7;"></div>
                    </div>
                    <div class="secondary-text text-xs mt-1">{{ number_format($data['wipFinish']->area, 2) }} m² در حال جریان</div>
                </div>

                <div>
                    <h6 class="secondary-text mt-4 mb-3">سرعت تولید (Throughput)</h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="p-3 rounded data-box">
                                <div class="secondary-text text-xs">۷ روز گذشته</div>
                                <div class="primary-text fw-bold fs-5">{{ number_format($data['throughput']['7_days']) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded data-box">
                                <div class="secondary-text text-xs">۳۰ روز گذشته</div>
                                <div class="primary-text fw-bold fs-5">{{ number_format($data['throughput']['30_days']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: Trend Charts -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="primary-text mb-4">روند ۱۲ ماهه تولید (12-Month Production Trend)</h5>
                <div id="trendChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- ROW 4: Warehouse & Top Performers -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">موجودی بر اساس گدام (Warehouse Intelligence)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-glass align-middle" style="background: transparent;">
                        <thead>
                            <tr>
                                <th>نام گدام</th>
                                <th class="text-center">تعداد (تخته)</th>
                                <th class="text-center">مساحت (m²)</th>
                                <th class="text-end">ارزش (USD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['warehouseIntelligence'] as $wh)
                            <tr>
                                <td class="primary-text fw-bold">{{ $wh->warehouse_name }}</td>
                                <td class="text-center text-primary fw-medium">{{ number_format($wh->qty) }}</td>
                                <td class="text-center text-warning fw-medium">{{ number_format($wh->area, 2) }}</td>
                                <td class="text-end text-success fw-bold">${{ number_format($wh->value) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">پرسود ترین محصولات (Top Profitable Products)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-glass align-middle" style="background: transparent;">
                        <thead>
                            <tr>
                                <th>نوعیت قالین</th>
                                <th class="text-center">فروخته شده</th>
                                <th class="text-end">عواید</th>
                                <th class="text-end">مفاد خالص</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['topCarpetTypes'] as $top)
                            <tr>
                                <td class="primary-text fw-bold">{{ $top->name }}</td>
                                <td class="text-center text-info fw-medium">{{ number_format($top->sold_qty) }}</td>
                                <td class="text-end text-primary fw-medium">${{ number_format($top->revenue) }}</td>
                                <td class="text-end text-success fw-bold">+${{ number_format($top->profit) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 5: Raw Materials Intelligence -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">موجودی مواد خام بر اساس گدام (Material Stocks)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-glass align-middle" style="background: transparent;">
                        <thead>
                            <tr>
                                <th>نام گدام</th>
                                <th>نوعیت</th>
                                <th class="text-center">مقدار (kg)</th>
                                <th class="text-end">ارزش (USD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['warehouseMaterials'] as $wh)
                            <tr>
                                <td class="primary-text fw-bold">{{ $wh->warehouse_name }}</td>
                                <td>
                                    @if($wh->subtype == 'yarn')
                                        <span class="badge badge-glow-blue">تار (Yarn)</span>
                                    @else
                                        <span class="badge badge-glow-orange">رنگ (Dye)</span>
                                    @endif
                                </td>
                                <td class="text-center text-primary fw-medium">{{ number_format($wh->qty, 2) }}</td>
                                <td class="text-end text-success fw-bold">${{ number_format($wh->value) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">پر فروش ترین مواد خام (Top Selling Materials)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-glass align-middle" style="background: transparent;">
                        <thead>
                            <tr>
                                <th>نام جنس</th>
                                <th>نوعیت</th>
                                <th class="text-center">فروخته شده (kg)</th>
                                <th class="text-end">عواید (Revenue)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['topMaterials'] as $top)
                            <tr>
                                <td class="primary-text fw-bold">{{ $top->name }}</td>
                                <td>
                                    @if($top->subtype == 'yarn')
                                        <span class="badge badge-glow-blue">تار (Yarn)</span>
                                    @else
                                        <span class="badge badge-glow-orange">رنگ (Dye)</span>
                                    @endif
                                </td>
                                <td class="text-center text-info fw-medium">{{ number_format($top->sold_qty, 2) }}</td>
                                <td class="text-end text-primary fw-medium">${{ number_format($top->revenue) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 6: Activity Feed -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="primary-text mb-4">جریان فعالیت های اخیر (Recent Operations Activity)</h5>
                <div class="row">
                    @foreach($data['activities']->chunk(5) as $chunk)
                        <div class="col-md-6">
                            @foreach($chunk as $act)
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Automatically detect theme colors for ApexCharts based on the body class
    const isDarkMode = document.body.classList.contains('dark-mode');
    const labelColor = isDarkMode ? '#94a3b8' : '#64748b';
    const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const tooltipTheme = isDarkMode ? 'dark' : 'light';

    // FUNNEL CHART
    var funnelOptions = {
        series: [{
            name: 'تعداد (Count)',
            data: [
                {{ $data['funnel']['Purchased'] }},
                {{ $data['funnel']['Repaired'] }},
                {{ $data['funnel']['Washed'] }},
                {{ $data['funnel']['Finished'] }},
                {{ $data['funnel']['Sold'] }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            background: 'transparent'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true,
                dataLabels: { position: 'bottom' }
            }
        },
        colors: ['#3b82f6', '#f59e0b', '#06b6d4', '#8b5cf6', '#10b981'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: { colors: ['#fff'] },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
            },
            offsetX: 0,
            dropShadow: { enabled: true, color: '#000', opacity: 0.3 }
        },
        stroke: { width: 0 },
        xaxis: { categories: ['خریداری (Purchased)', 'کچایی (Repaired)', 'شست (Washed)', 'تیاری (Finished)', 'فروخته شده (Sold)'], labels: { style: { colors: labelColor } } },
        yaxis: { labels: { show: false } },
        tooltip: { theme: tooltipTheme },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    var funnelChart = new ApexCharts(document.querySelector("#funnelChart"), funnelOptions);
    funnelChart.render();

    // TREND CHART
    var trendOptions = {
        series: [
            { name: 'خریداری (Purchased)', data: {!! json_encode($data['trends']['purchased']) !!} },
            { name: 'کچایی (Repaired)', data: {!! json_encode($data['trends']['repaired']) !!} },
            { name: 'شست (Washed)', data: {!! json_encode($data['trends']['washed']) !!} },
            { name: 'تیاری (Finished)', data: {!! json_encode($data['trends']['finished']) !!} },
            { name: 'فروش (Sold)', data: {!! json_encode($data['trends']['sold']) !!} }
        ],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            background: 'transparent',
            stacked: false
        },
        colors: ['#3b82f6', '#f59e0b', '#06b6d4', '#8b5cf6', '#10b981'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] }
        },
        xaxis: {
            categories: {!! json_encode($data['trends']['months']) !!},
            labels: { style: { colors: labelColor } }
        },
        yaxis: { labels: { style: { colors: labelColor } } },
        legend: { labels: { colors: labelColor }, position: 'top' },
        tooltip: { theme: tooltipTheme },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    var trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
    trendChart.render();
});
</script>
@endsection
