@extends('dsh.master')

@section('title', 'داشبورد خریدات')

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
    .icon-box-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }

    .chart-container { min-height: 320px; }
    
    .bg-gradient-blue { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; }
    .bg-gradient-orange { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 1px solid #fed7aa; }
    .bg-gradient-red { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fecaca; }
    
    .supplier-card { transition: all 0.2s; }
    .supplier-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
</style>

<!-- 1. KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Carpet Purchases -->
    <div class="col-md-4 col-gap">
        <div class="glass-card bg-gradient-blue p-4 h-100 position-relative overflow-hidden">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-title text-primary">خریدات قالین</div>
                    <div class="kpi-value-main text-blue-900">
                        {{ number_format($data['kpis']['carpet_count'] ?? 0) }} <span style="font-size: 1rem; color: #3b82f6; font-weight: 600;">تخته</span>
                    </div>
                    <div class="kpi-value-sub mt-2">
                        <span class="d-block mb-1"><i class="fa-solid fa-ruler-combined text-primary me-1"></i> مساحت کل: <span class="fw-bold">{{ number_format($data['kpis']['carpet_area'] ?? 0, 2) }}</span> م.م</span>
                        <span class="d-block"><i class="fa-solid fa-dollar-sign text-success me-1"></i> ارزش کل: <span class="fw-bold">${{ number_format($data['kpis']['carpet_value'] ?? 0, 2) }}</span></span>
                    </div>
                </div>
                <div class="icon-box icon-box-primary bg-white"><i class="fa-solid fa-layer-group"></i></div>
            </div>
        </div>
    </div>

    <!-- Yarn Purchases -->
    <div class="col-md-4 col-gap">
        <div class="glass-card bg-gradient-orange p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-title text-warning">خریدات تار (خامچه)</div>
                    <div class="kpi-value-main text-orange-900">
                        {{ number_format($data['kpis']['yarn_quantity'] ?? 0, 2) }} <span style="font-size: 1rem; color: #f59e0b; font-weight: 600;">کیلوگرام</span>
                    </div>
                    <div class="kpi-value-sub mt-2">
                        <span class="d-block"><i class="fa-solid fa-dollar-sign text-success me-1"></i> ارزش کل: <span class="fw-bold">${{ number_format($data['kpis']['yarn_value'] ?? 0, 2) }}</span></span>
                    </div>
                </div>
                <div class="icon-box icon-box-warning bg-white"><i class="fa-solid fa-lines-leaning"></i></div>
            </div>
        </div>
    </div>

    <!-- Dye Purchases -->
    <div class="col-md-4 col-gap">
        <div class="glass-card bg-gradient-red p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-title text-danger">خریدات رنگ</div>
                    <div class="kpi-value-main text-red-900">
                        {{ number_format($data['kpis']['dye_quantity'] ?? 0, 2) }} <span style="font-size: 1rem; color: #ef4444; font-weight: 600;">کیلوگرام</span>
                    </div>
                    <div class="kpi-value-sub mt-2">
                        <span class="d-block"><i class="fa-solid fa-dollar-sign text-success me-1"></i> ارزش کل: <span class="fw-bold">${{ number_format($data['kpis']['dye_value'] ?? 0, 2) }}</span></span>
                    </div>
                </div>
                <div class="icon-box icon-box-danger bg-white"><i class="fa-solid fa-fill-drip"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Charts Row (Trends) -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Carpet Trend -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i> روند خرید قالین (مساحت متر مربع)</h5>
            <div id="carpetTrendChart" class="chart-container"></div>
        </div>
    </div>

    <!-- Materials Trend -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-area text-warning me-2"></i> روند خرید مواد خام (تار و رنگ - کیلوگرام)</h5>
            <div id="materialTrendChart" class="chart-container"></div>
        </div>
    </div>
</div>

<!-- 3. Top Suppliers & Recent Purchases -->
<div class="row row-gap" style="margin: 10px;">
    
    <!-- Carpet Suppliers & Invoices -->
    <div class="col-md-6 col-gap">
        <!-- Suppliers -->
        <div class="glass-card p-4 mb-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-handshake text-primary me-2"></i> تامین کنندگان برتر قالین</h5>
            <div class="row">
                @forelse($data['suppliers']['carpet']['labels'] as $index => $label)
                <div class="col-12 mb-2">
                    <div class="supplier-card bg-light border p-3 rounded-3 d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark"><span class="badge bg-primary me-2">{{ $index + 1 }}</span> {{ $label }}</div>
                        <div class="fw-bold text-success">${{ number_format($data['suppliers']['carpet']['data'][$index]) }}</div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted py-3">معلوماتی موجود نیست.</div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Invoices -->
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-file-invoice text-primary me-2"></i> بل های اخیر قالین</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>شماره بل</th>
                            <th>تامین کننده</th>
                            <th>تاریخ</th>
                            <th>مبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_purchases']['carpet'] as $inv)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $inv['number'] }}</td>
                            <td>{{ $inv['supplier'] }}</td>
                            <td>{{ $inv['date'] }}</td>
                            <td class="fw-bold">${{ number_format($inv['amount']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">بل خریدی موجود نیست.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Material Suppliers & Invoices -->
    <div class="col-md-6 col-gap">
        <!-- Suppliers -->
        <div class="glass-card p-4 mb-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-handshake text-warning me-2"></i> تامین کنندگان برتر مواد خام</h5>
            <div class="row">
                @forelse($data['suppliers']['material']['labels'] as $index => $label)
                <div class="col-12 mb-2">
                    <div class="supplier-card bg-light border p-3 rounded-3 d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark"><span class="badge bg-warning text-dark me-2">{{ $index + 1 }}</span> {{ $label }}</div>
                        <div class="fw-bold text-success">${{ number_format($data['suppliers']['material']['data'][$index]) }}</div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted py-3">معلوماتی موجود نیست.</div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Invoices -->
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-file-invoice text-warning me-2"></i> بل های اخیر مواد خام</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>شماره بل</th>
                            <th>تامین کننده</th>
                            <th>تاریخ</th>
                            <th>مبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_purchases']['material'] as $inv)
                        <tr>
                            <td class="fw-bold text-warning text-dark">#{{ $inv['number'] }}</td>
                            <td>{{ $inv['supplier'] }}</td>
                            <td>{{ $inv['date'] }}</td>
                            <td class="fw-bold">${{ number_format($inv['amount']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">بل خریدی موجود نیست.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        const commonOptions = {
            chart: { type: 'area', height: 350, toolbar: { show: false }, fontFamily: 'Inter, Vazirmatn, sans-serif' },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.6, opacityTo: 0.1, stops: [0, 90, 100] } },
            xaxis: { categories: {!! json_encode($data['trends']['months'] ?? []) !!}, labels: { style: { fontSize: '13px', fontWeight: 600, fontFamily: 'Inter, Vazirmatn, sans-serif' } } },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 4 }
        };

        // Carpet Trend Chart
        var carpetTrendOptions = { ...commonOptions,
            series: [{ name: 'مساحت (متر مربع)', data: {!! json_encode($data['trends']['carpet_area'] ?? []) !!} }],
            colors: ['#3b82f6'],
            tooltip: { y: { formatter: function (val) { return val.toLocaleString() + " م.م" } } }
        };
        new ApexCharts(document.querySelector("#carpetTrendChart"), carpetTrendOptions).render();

        // Material Trend Chart
        var materialTrendOptions = { ...commonOptions,
            series: [
                { name: 'تار (کیلوگرام)', data: {!! json_encode($data['trends']['yarn_kilos'] ?? []) !!} },
                { name: 'رنگ (کیلوگرام)', data: {!! json_encode($data['trends']['dye_kilos'] ?? []) !!} }
            ],
            colors: ['#f59e0b', '#ef4444'],
            tooltip: { y: { formatter: function (val) { return val.toLocaleString() + " kg" } } }
        };
        new ApexCharts(document.querySelector("#materialTrendChart"), materialTrendOptions).render();

    });
</script>
@endsection
