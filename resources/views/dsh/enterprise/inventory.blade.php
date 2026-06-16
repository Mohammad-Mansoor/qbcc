@extends('dsh.master')

@section('title', 'داشبورد گدام (موجودی)')

@section('content')

<!-- Styling overrides for a premium feel -->
<style>
    .kpi-title { font-size: 0.95rem; color: #64748b; font-weight: 600; margin-bottom: 0.5rem; }
    .kpi-value-main { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem; }
    .kpi-value-sub { font-size: 0.85rem; color: #475569; font-weight: 500; }
    .icon-box { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; }
    .icon-box-primary { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .icon-box-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .icon-box-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    
    .dead-stock-item { background: rgba(255, 255, 255, 0.6); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 12px; padding: 12px 15px; margin-bottom: 8px; transition: all 0.2s ease-in-out; }
    .dead-stock-item:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .dead-stock-count { font-size: 1.15rem; font-weight: 700; }
    
    .wh-card { border-left: 4px solid #3b82f6; transition: all 0.2s; }
    .wh-card:hover { transform: scale(1.02); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    
    /* Vibrant gradients for cards */
    .bg-gradient-blue { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); }
    .bg-gradient-red { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
    .bg-gradient-green { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); }
    
    .chart-container { min-height: 320px; }
</style>

<!-- 1. Top KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Carpet Stats -->
    <div class="col-md-4 col-gap">
        <div class="glass-card bg-gradient-blue p-4 h-100 position-relative overflow-hidden" style="border: 1px solid #bfdbfe;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-title text-primary">موجودی قالین</div>
                    <div class="kpi-value-main text-blue-900">
                        {{ number_format($data['quantities']['carpet_count'] ?? 0) }} <span style="font-size: 1rem; color: #3b82f6; font-weight: 600;">تخته</span>
                    </div>
                    <div class="kpi-value-sub mt-2">
                        <span class="d-block mb-1"><i class="fa-solid fa-ruler-combined text-primary me-1"></i> مساحت: <span class="fw-bold">{{ number_format($data['quantities']['carpet_area'] ?? 0, 2) }}</span> م.م</span>
                        <span class="d-block"><i class="fa-solid fa-dollar-sign text-success me-1"></i> ارزش: <span class="fw-bold">${{ number_format($data['values']['carpet'] ?? 0, 2) }}</span></span>
                    </div>
                </div>
                <div class="icon-box icon-box-primary bg-white"><i class="fa-solid fa-layer-group"></i></div>
            </div>
        </div>
    </div>

    <!-- Yarn Stats -->
    <div class="col-md-4 col-gap">
        <div class="glass-card bg-gradient-orange p-4 h-100" style="border: 1px solid #fed7aa;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-title text-warning">موجودی تار (خامچه)</div>
                    <div class="kpi-value-main text-orange-900">
                        {{ number_format($data['quantities']['yarn_kilos'] ?? 0, 2) }} <span style="font-size: 1rem; color: #f59e0b; font-weight: 600;">کیلوگرام</span>
                    </div>
                    <div class="kpi-value-sub mt-2">
                        <span class="d-block"><i class="fa-solid fa-dollar-sign text-success me-1"></i> ارزش: <span class="fw-bold">${{ number_format($data['values']['yarn'] ?? 0, 2) }}</span></span>
                    </div>
                </div>
                <div class="icon-box icon-box-warning bg-white"><i class="fa-solid fa-lines-leaning"></i></div>
            </div>
        </div>
    </div>

    <!-- Dye Stats -->
    <div class="col-md-4 col-gap">
        <div class="glass-card bg-gradient-red p-4 h-100" style="border: 1px solid #fecaca;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-title text-danger">موجودی رنگ</div>
                    <div class="kpi-value-main text-red-900">
                        {{ number_format($data['quantities']['dye_kilos'] ?? 0, 2) }} <span style="font-size: 1rem; color: #ef4444; font-weight: 600;">کیلوگرام</span>
                    </div>
                    <div class="kpi-value-sub mt-2">
                        <span class="d-block"><i class="fa-solid fa-dollar-sign text-success me-1"></i> ارزش: <span class="fw-bold">${{ number_format($data['values']['dye'] ?? 0, 2) }}</span></span>
                    </div>
                </div>
                <div class="icon-box icon-box-danger bg-white"><i class="fa-solid fa-fill-drip"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Charts Row (Carpet Breakdowns) -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Carpet By Type -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-pie text-primary me-2"></i> موجودی قالین نظر به نوعیت</h5>
            <div id="carpetByTypeChart" class="chart-container"></div>
        </div>
    </div>

    <!-- Carpet By Quality -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-award text-success me-2"></i> موجودی قالین نظر به کیفیت</h5>
            <div id="carpetByQualityChart" class="chart-container"></div>
        </div>
    </div>
</div>

<!-- 3. Charts Row (Materials Breakdowns) -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Yarn By Category -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-column text-warning me-2"></i> تار نظر به کتگوری (کیلوگرام)</h5>
            <div id="yarnByCategoryChart" class="chart-container"></div>
        </div>
    </div>

    <!-- Dye By Category -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-column text-danger me-2"></i> رنگ نظر به کتگوری (کیلوگرام)</h5>
            <div id="dyeByCategoryChart" class="chart-container"></div>
        </div>
    </div>
</div>

<!-- 4. Dead Stock Analysis Row -->
<div class="row row-gap" style="margin: 10px;">
    <div class="col-12 col-gap">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-box-archive text-secondary me-2"></i> اجناس راکد (Dead Stock) - عدم تحرک در گدام</h5>
            
            <div class="row">
                <!-- Carpet Dead Stock -->
                <div class="col-md-4">
                    <div class="p-3 bg-gradient-blue rounded-3 border border-primary border-opacity-25 h-100">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-layer-group me-1"></i> قالین های راکد (تخته)</h6>
                        @foreach(['30_days' => ['label' => '۳۰ روز', 'color' => 'text-secondary'], '60_days' => ['label' => '۶۰ روز', 'color' => 'text-warning'], '90_days' => ['label' => '۹۰ روز', 'color' => 'text-danger'], '180_days' => ['label' => '+۱۸۰ روز', 'color' => 'text-dark']] as $key => $config)
                        <div class="dead-stock-item d-flex justify-content-between align-items-center">
                            <span class="badge bg-white {{ $config['color'] }} border shadow-sm">{{ $config['label'] }}</span>
                            <div class="dead-stock-count {{ $config['color'] }}">{{ number_format($data['dead_stock']['carpet'][$key] ?? 0) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Yarn Dead Stock -->
                <div class="col-md-4">
                    <div class="p-3 bg-gradient-orange rounded-3 border border-warning border-opacity-25 h-100">
                        <h6 class="fw-bold text-warning mb-3" style="color: #d97706 !important;"><i class="fa-solid fa-lines-leaning me-1"></i> تار های راکد (کیلوگرام)</h6>
                        @foreach(['30_days' => ['label' => '۳۰ روز', 'color' => 'text-secondary'], '60_days' => ['label' => '۶۰ روز', 'color' => 'text-warning'], '90_days' => ['label' => '۹۰ روز', 'color' => 'text-danger'], '180_days' => ['label' => '+۱۸۰ روز', 'color' => 'text-dark']] as $key => $config)
                        <div class="dead-stock-item d-flex justify-content-between align-items-center">
                            <span class="badge bg-white {{ $config['color'] }} border shadow-sm">{{ $config['label'] }}</span>
                            <div class="dead-stock-count {{ $config['color'] }}">{{ number_format($data['dead_stock']['yarn'][$key] ?? 0, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dye Dead Stock -->
                <div class="col-md-4">
                    <div class="p-3 bg-gradient-red rounded-3 border border-danger border-opacity-25 h-100">
                        <h6 class="fw-bold text-danger mb-3"><i class="fa-solid fa-fill-drip me-1"></i> رنگ های راکد (کیلوگرام)</h6>
                        @foreach(['30_days' => ['label' => '۳۰ روز', 'color' => 'text-secondary'], '60_days' => ['label' => '۶۰ روز', 'color' => 'text-warning'], '90_days' => ['label' => '۹۰ روز', 'color' => 'text-danger'], '180_days' => ['label' => '+۱۸۰ روز', 'color' => 'text-dark']] as $key => $config)
                        <div class="dead-stock-item d-flex justify-content-between align-items-center">
                            <span class="badge bg-white {{ $config['color'] }} border shadow-sm">{{ $config['label'] }}</span>
                            <div class="dead-stock-count {{ $config['color'] }}">{{ number_format($data['dead_stock']['dye'][$key] ?? 0, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5. Warehouse Summaries -->
<div class="row row-gap" style="margin: 10px;">
    <div class="col-12 col-gap">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-warehouse text-success me-2"></i> توزیع موجودی در گدام ها</h5>
            
            <div class="row">
                @forelse($data['warehouses'] as $w)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100 border-0 bg-white shadow-sm wh-card" style="border-radius: 12px; @if(($w['subtype'] ?? '') === 'yarn') border-left-color: #f59e0b; @elseif(($w['subtype'] ?? '') === 'dye') border-left-color: #ef4444; @endif">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark mb-1">{{ $w['name'] }}</h6>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary mb-3">{{ $w['type'] }}</span>
                            
                            @if(($w['subtype'] ?? 'carpet') === 'carpet')
                            <div class="d-flex justify-content-between align-items-end mt-2">
                                <div class="text-muted small">تعداد قالین:</div>
                                <div class="fs-4 fw-bold text-primary">{{ number_format($w['items_count']) }} <span class="fs-6 fw-normal text-muted">تخته</span></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mt-1">
                                <div class="text-muted small">مساحت کل:</div>
                                <div class="fs-6 fw-bold text-dark">{{ number_format($w['area'] ?? 0, 2) }} <span class="small fw-normal text-muted">متر مربع</span></div>
                            </div>
                            @else
                            <div class="d-flex justify-content-between align-items-end mt-2">
                                <div class="text-muted small">موجودی مواد:</div>
                                <div class="fs-4 fw-bold @if($w['subtype'] === 'yarn') text-warning @else text-danger @endif">{{ number_format($w['items_count'], 2) }} <span class="fs-6 fw-normal text-muted">کیلوگرام</span></div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <div class="text-muted">معلومات گدام موجود نیست.</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        const commonOptions = {
            chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, Vazirmatn, sans-serif' },
            plotOptions: { bar: { borderRadius: 6, distributed: true, dataLabels: { position: 'top' } } },
            legend: { show: false },
            xaxis: { labels: { style: { fontSize: '13px', fontWeight: 600, fontFamily: 'Inter, Vazirmatn, sans-serif' } } },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 4 }
        };

        // 1. Carpet by Type
        var typeLabels = {!! json_encode($data['carpet_by_type']['labels'] ?? []) !!};
        var typeData = {!! json_encode($data['carpet_by_type']['data'] ?? []) !!};
        if(typeLabels.length === 0) { typeLabels = ['Empty']; typeData = [0]; }
        
        var typeOptions = { ...commonOptions,
            series: [{ name: 'تعداد (تخته)', data: typeData }],
            colors: ['#3b82f6', '#0ea5e9', '#6366f1', '#8b5cf6', '#a855f7'],
            xaxis: { categories: typeLabels, ...commonOptions.xaxis },
            dataLabels: { enabled: true, formatter: function (val) { return val + " تخته"; }, offsetY: -20, style: { fontSize: '12px', colors: ["#304758"] } }
        };
        new ApexCharts(document.querySelector("#carpetByTypeChart"), typeOptions).render();

        // 2. Carpet by Quality
        var qualLabels = {!! json_encode($data['carpet_by_quality']['labels'] ?? []) !!};
        var qualData = {!! json_encode($data['carpet_by_quality']['data'] ?? []) !!};
        if(qualLabels.length === 0) { qualLabels = ['Empty']; qualData = [0]; }

        var qualOptions = { ...commonOptions,
            chart: { type: 'pie', height: 340, fontFamily: 'Inter, Vazirmatn, sans-serif' },
            series: qualData,
            labels: qualLabels,
            plotOptions: { pie: { donut: { size: '65%' } } },
            colors: ['#10b981', '#34d399', '#059669', '#047857'],
            legend: { show: true, position: 'bottom' },
            dataLabels: { enabled: true, formatter: function (val, opts) { return opts.w.globals.seriesTotals[opts.seriesIndex] + " تخته"; } }
        };
        new ApexCharts(document.querySelector("#carpetByQualityChart"), qualOptions).render();

        // 3. Yarn by Category
        var yarnLabels = {!! json_encode($data['yarn_by_category']['labels'] ?? []) !!};
        var yarnData = {!! json_encode($data['yarn_by_category']['data'] ?? []) !!};
        if(yarnLabels.length === 0) { yarnLabels = ['Empty']; yarnData = [0]; }

        var yarnOptions = { ...commonOptions,
            series: [{ name: 'تار (کیلو)', data: yarnData }],
            colors: ['#f59e0b', '#fbbf24', '#d97706', '#b45309'],
            xaxis: { categories: yarnLabels, ...commonOptions.xaxis },
            dataLabels: { enabled: true, formatter: function (val) { return val + " kg"; }, offsetY: -20, style: { fontSize: '12px', colors: ["#304758"] } }
        };
        new ApexCharts(document.querySelector("#yarnByCategoryChart"), yarnOptions).render();

        // 4. Dye by Category
        var dyeLabels = {!! json_encode($data['dye_by_category']['labels'] ?? []) !!};
        var dyeData = {!! json_encode($data['dye_by_category']['data'] ?? []) !!};
        if(dyeLabels.length === 0) { dyeLabels = ['Empty']; dyeData = [0]; }

        var dyeOptions = { ...commonOptions,
            series: [{ name: 'رنگ (کیلو)', data: dyeData }],
            colors: ['#ef4444', '#f87171', '#dc2626', '#b91c1c'],
            xaxis: { categories: dyeLabels, ...commonOptions.xaxis },
            dataLabels: { enabled: true, formatter: function (val) { return val + " kg"; }, offsetY: -20, style: { fontSize: '12px', colors: ["#304758"] } }
        };
        new ApexCharts(document.querySelector("#dyeByCategoryChart"), dyeOptions).render();
    });
</script>
@endsection
