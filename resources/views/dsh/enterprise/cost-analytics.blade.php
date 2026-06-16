@extends('dsh.master')

@section('title', 'Cost Analytics & Financial Intelligence')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .dark-mode .glass-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    }

    .primary-text { color: #0f172a !important; font-weight: 700; }
    .dark-mode .primary-text { color: #f8fafc !important; }

    .secondary-text { color: #64748b !important; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px; text-transform: uppercase;}
    .dark-mode .secondary-text { color: #94a3b8 !important; }

    .kpi-amount {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        font-size: 1.8rem;
        background: linear-gradient(90deg, #1e293b, #334155);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dark-mode .kpi-amount {
        background: linear-gradient(90deg, #ffffff, #e2e8f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .text-success-gradient { background: linear-gradient(90deg, #059669, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .text-danger-gradient { background: linear-gradient(90deg, #dc2626, #ef4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .text-warning-gradient { background: linear-gradient(90deg, #d97706, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

    .dark-mode .text-success-gradient { background: linear-gradient(90deg, #10b981, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .dark-mode .text-danger-gradient { background: linear-gradient(90deg, #ef4444, #f87171); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .timeline-node { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-left: 8px; }
    .node-purchase { background: #3b82f6; box-shadow: 0 0 8px rgba(59, 130, 246, 0.6); }
    .node-kachaee { background: #f59e0b; box-shadow: 0 0 8px rgba(245, 158, 11, 0.6); }
    .node-washing { background: #0ea5e9; box-shadow: 0 0 8px rgba(14, 165, 233, 0.6); }
    .node-finishing { background: #8b5cf6; box-shadow: 0 0 8px rgba(139, 92, 246, 0.6); }

    .table-glass th { border-bottom: 2px solid rgba(0,0,0,0.05); color: #64748b; font-weight: 600; }
    .dark-mode .table-glass th { border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; }
    .table-glass td { color: #334155; vertical-align: middle; }
    .dark-mode .table-glass td { color: #cbd5e1; }
</style>

<div class="container-fluid py-4" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="primary-text mb-1"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Financial & Cost Intelligence</h2>
            <p class="secondary-text mb-0">داشبورد هوش مالی و تحلیل مصارف</p>
        </div>
        <button class="btn btn-primary shadow-sm" onclick="location.reload()"><i class="fas fa-sync-alt me-2"></i> بروزرسانی</button>
    </div>

    <!-- 10. Executive Financial Snapshot -->
    <div class="row g-4 mb-4">
        <!-- Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100 border-top border-4 border-success">
                <div class="secondary-text mb-2">عواید (Revenue)</div>
                <div class="kpi-amount text-success-gradient">${{ number_format($data['executive']['today']['rev']) }} <span class="fs-6 text-muted fw-normal">امروز</span></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted text-sm">این ماه:</span><span class="fw-bold">${{ number_format($data['executive']['month']['rev']) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted text-sm">امسال:</span><span class="fw-bold">${{ number_format($data['executive']['year']['rev']) }}</span></div>
                </div>
            </div>
        </div>
        <!-- Cost -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100 border-top border-4 border-danger">
                <div class="secondary-text mb-2">مصارف (Cost of Goods)</div>
                <div class="kpi-amount text-danger-gradient">${{ number_format($data['executive']['today']['cost']) }} <span class="fs-6 text-muted fw-normal">امروز</span></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted text-sm">این ماه:</span><span class="fw-bold">${{ number_format($data['executive']['month']['cost']) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted text-sm">امسال:</span><span class="fw-bold">${{ number_format($data['executive']['year']['cost']) }}</span></div>
                </div>
            </div>
        </div>
        <!-- Profit -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100 border-top border-4 border-primary">
                <div class="secondary-text mb-2">مفاد خالص (Profit)</div>
                <div class="kpi-amount text-primary">${{ number_format($data['executive']['today']['profit']) }} <span class="fs-6 text-muted fw-normal">امروز</span></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted text-sm">این ماه:</span><span class="fw-bold">${{ number_format($data['executive']['month']['profit']) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted text-sm">امسال:</span><span class="fw-bold">${{ number_format($data['executive']['year']['profit']) }}</span></div>
                </div>
            </div>
        </div>
        <!-- Inventory Value -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100 border-top border-4 border-warning">
                <div class="secondary-text mb-2">ارزش موجودی (Inventory Value)</div>
                <div class="kpi-amount text-warning-gradient">${{ number_format($data['executive']['inventory_total']) }}</div>
                <div class="mt-3">
                    @php 
                        $margin = $data['executive']['lifetime']['rev'] > 0 
                            ? ($data['executive']['lifetime']['profit'] / $data['executive']['lifetime']['rev']) * 100 
                            : 0;
                    @endphp
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted text-sm">حاشیه سود (Margin):</span><span class="fw-bold text-success">{{ number_format($margin, 1) }}%</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted text-sm">ارزش قالین:</span><span class="fw-bold">${{ number_format($data['inventory']['carpet']['value']) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1 & 2. Gross Profit Dashboard by Sector -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="primary-text border-bottom pb-3 mb-4">تحلیل سودآوری بر اساس بخش (Cost vs Revenue Sector Intelligence)</h5>
                <div class="row text-center">
                    <!-- Carpet -->
                    <div class="col-md-4 border-end">
                        <div class="secondary-text mb-3"><i class="fas fa-layer-group me-1"></i> بخش قالین (Carpet Business)</div>
                        <div class="d-flex justify-content-around">
                            <div>
                                <div class="text-muted text-sm mb-1">مصرف (Cost)</div>
                                <div class="fw-bold fs-5 text-danger">${{ number_format($data['sectors']['carpet']['cost']) }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-sm mb-1">عواید (Rev)</div>
                                <div class="fw-bold fs-5 text-success">${{ number_format($data['sectors']['carpet']['rev']) }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-sm mb-1">مفاد (Profit)</div>
                                <div class="fw-bold fs-5 text-primary">${{ number_format($data['sectors']['carpet']['profit']) }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Yarn -->
                    <div class="col-md-4 border-end">
                        <div class="secondary-text mb-3"><i class="fas fa-drum-steelpan me-1"></i> بخش تار (Yarn Business)</div>
                        <div class="d-flex justify-content-around">
                            <div>
                                <div class="text-muted text-sm mb-1">مصرف (Cost)</div>
                                <div class="fw-bold fs-5 text-danger">${{ number_format($data['sectors']['yarn']['cost']) }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-sm mb-1">عواید (Rev)</div>
                                <div class="fw-bold fs-5 text-success">${{ number_format($data['sectors']['yarn']['rev']) }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-sm mb-1">مفاد (Profit)</div>
                                <div class="fw-bold fs-5 text-primary">${{ number_format($data['sectors']['yarn']['profit']) }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Dye -->
                    <div class="col-md-4">
                        <div class="secondary-text mb-3"><i class="fas fa-fill-drip me-1"></i> بخش رنگ (Dye Business)</div>
                        <div class="d-flex justify-content-around">
                            <div>
                                <div class="text-muted text-sm mb-1">مصرف (Cost)</div>
                                <div class="fw-bold fs-5 text-danger">${{ number_format($data['sectors']['dye']['cost']) }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-sm mb-1">عواید (Rev)</div>
                                <div class="fw-bold fs-5 text-success">${{ number_format($data['sectors']['dye']['rev']) }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-sm mb-1">مفاد (Profit)</div>
                                <div class="fw-bold fs-5 text-primary">${{ number_format($data['sectors']['dye']['profit']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 3 & 5. Cost Build-Up Waterfall -->
        <div class="col-xl-8 col-lg-7">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">آبشار تحلیل مصارف قالین (Cost Build-Up Waterfall)</h5>
                <div id="waterfallChart" style="height: 350px;"></div>
                <div class="row text-center mt-3 border-top pt-3">
                    <div class="col-3"><div class="text-muted text-xs">خرید</div><div class="fw-bold text-primary">${{ number_format($data['build_up']['purchase']) }}</div></div>
                    <div class="col-3"><div class="text-muted text-xs">ترمیم</div><div class="fw-bold text-warning">${{ number_format($data['build_up']['repair']) }}</div></div>
                    <div class="col-3"><div class="text-muted text-xs">شستشو</div><div class="fw-bold text-info">${{ number_format($data['build_up']['wash']) }}</div></div>
                    <div class="col-3"><div class="text-muted text-xs">فینشنگ</div><div class="fw-bold text-purple" style="color: #8b5cf6;">${{ number_format($data['build_up']['finish']) }}</div></div>
                </div>
            </div>
        </div>

        <!-- 4. Inventory Valuation Intelligence -->
        <div class="col-xl-4 col-lg-5">
            <div class="glass-card p-4 h-100 bg-light-subtle">
                <h5 class="primary-text mb-4">ارزیابی موجودی (Inventory Valuation)</h5>
                
                <div class="bg-white dark:bg-slate-800 rounded p-3 mb-3 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold"><i class="fas fa-layer-group text-primary me-2"></i> قالین (Carpet)</div>
                        <div class="fw-bold text-success fs-5">${{ number_format($data['inventory']['carpet']['value']) }}</div>
                    </div>
                    <div class="d-flex justify-content-between text-muted text-sm">
                        <span>موجود: {{ number_format($data['inventory']['carpet']['qty']) }} تخته</span>
                        <span>مساحت: {{ number_format($data['inventory']['carpet']['area'], 1) }} m²</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded p-3 mb-3 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold"><i class="fas fa-drum-steelpan text-warning me-2"></i> تار (Yarn)</div>
                        <div class="fw-bold text-success fs-5">${{ number_format($data['inventory']['yarn']['value']) }}</div>
                    </div>
                    <div class="text-muted text-sm">موجود: {{ number_format($data['inventory']['yarn']['qty'], 1) }} kg</div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded p-3 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold"><i class="fas fa-fill-drip text-info me-2"></i> رنگ (Dye)</div>
                        <div class="fw-bold text-success fs-5">${{ number_format($data['inventory']['dye']['value']) }}</div>
                    </div>
                    <div class="text-muted text-sm">موجود: {{ number_format($data['inventory']['dye']['qty'], 1) }} kg</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Monthly Cost Escalation Analysis -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="primary-text mb-4">روند مصارف عملیاتی ۱۲ ماهه (Monthly Cost Escalation Trend)</h5>
                <div id="costTrendChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- 7 & 9. Carpet Profitability & Supplier Intelligence -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">سودآوری بر اساس نوعیت قالین (Carpet Lifecycle Profitability)</h5>
                <div class="table-responsive">
                    <table class="table table-borderless table-glass align-middle table-sm">
                        <thead>
                            <tr>
                                <th>نوعیت (Type)</th>
                                <th class="text-end">عواید (Rev)</th>
                                <th class="text-end">مصرف (Cost)</th>
                                <th class="text-end">مفاد (Profit)</th>
                                <th class="text-end">حاشیه (Margin)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($data['profitability']['types'])->sortByDesc('profit')->take(8) as $type)
                            @php $typeMargin = $type->revenue > 0 ? ($type->profit / $type->revenue) * 100 : 0; @endphp
                            <tr>
                                <td class="fw-bold">{{ $type->name }}</td>
                                <td class="text-end text-success">${{ number_format($type->revenue) }}</td>
                                <td class="text-end text-danger">${{ number_format($type->cost) }}</td>
                                <td class="text-end text-primary fw-bold">${{ number_format($type->profit) }}</td>
                                <td class="text-end text-info">{{ number_format($typeMargin, 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="glass-card p-4 h-100">
                <h5 class="primary-text mb-4">تامین کنندگان برتر قالین (Top Carpet Suppliers)</h5>
                <div class="table-responsive">
                    <table class="table table-borderless table-glass align-middle table-sm">
                        <thead>
                            <tr>
                                <th>تامین کننده (Supplier)</th>
                                <th class="text-center">تعداد (Qty)</th>
                                <th class="text-end">ارزش خرید (Purchase Val)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['suppliers']['carpet'] as $sup)
                            <tr>
                                <td class="fw-bold">{{ $sup->name }}</td>
                                <td class="text-center text-muted">{{ number_format($sup->qty) }} <small class="text-xs">({{ number_format($sup->total_area, 1) }}m²)</small></td>
                                <td class="text-end text-danger fw-bold">${{ number_format($sup->total_value) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <h5 class="primary-text mt-4 mb-3">تامین کنندگان مواد (Top Material Suppliers)</h5>
                <div class="table-responsive">
                    <table class="table table-borderless table-glass align-middle table-sm">
                        <tbody>
                            @foreach(collect($data['suppliers']['material'])->take(4) as $supM)
                            <tr>
                                <td class="fw-bold">{{ $supM->name }} <span class="badge bg-light text-dark ms-1">{{ $supM->subtype }}</span></td>
                                <td class="text-center text-muted">{{ number_format($supM->qty) }}kg</td>
                                <td class="text-end text-danger fw-bold">${{ number_format($supM->total_value) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 11. Cost Activity Timeline -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="primary-text mb-4">جریان فعالیت های مالی (Financial Activity Ledger Feed)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-glass align-middle">
                        <thead>
                            <tr>
                                <th>زمان (Time)</th>
                                <th>نوعیت هزینه (Cost Type)</th>
                                <th>مربوط (Reference)</th>
                                <th>کاربر (User)</th>
                                <th class="text-end">ارزش مالی (Value Impact)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['activities'] as $act)
                            <tr>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }}</td>
                                <td>
                                    @if($act->type == 'PURCHASE') <span class="badge bg-primary text-white">خرید (Purchase)</span>
                                    @elseif($act->type == 'KACHAEE') <span class="badge bg-warning text-dark">ترمیم (Repair)</span>
                                    @elseif($act->type == 'WASHING') <span class="badge bg-info text-white">شستشو (Wash)</span>
                                    @elseif($act->type == 'FINISHING') <span class="badge" style="background: #8b5cf6;">فینشنگ (Finish)</span>
                                    @else <span class="badge bg-secondary">{{ $act->type }}</span> @endif
                                </td>
                                <td class="text-muted text-sm">{{ class_basename($act->reference_type) }}</td>
                                <td>{{ $act->user_name ?? 'System' }}</td>
                                <td class="text-end fw-bold {{ $act->type == 'SALE' ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($act->total_cost, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    // 3. Waterfall-Style Bar Chart for Cost Build-Up
    var waterfallOptions = {
        series: [{
            name: 'ارزش مالی',
            data: [
                { x: 'خرید قالین', y: {{ $data['build_up']['purchase'] ?? 0 }}, fillColor: '#3b82f6' },
                { x: 'ارزش ترمیم', y: {{ $data['build_up']['repair'] ?? 0 }}, fillColor: '#f59e0b' },
                { x: 'ارزش شستشو', y: {{ $data['build_up']['wash'] ?? 0 }}, fillColor: '#0ea5e9' },
                { x: 'ارزش فینشنگ', y: {{ $data['build_up']['finish'] ?? 0 }}, fillColor: '#8b5cf6' },
                { x: 'مجموع سرمایه گذاری', y: {{ $data['build_up']['total_investment'] ?? 0 }}, fillColor: '#1e293b' }
            ]
        }],
        chart: { type: 'bar', height: 350, toolbar: { show: false }, background: 'transparent' },
        plotOptions: {
            bar: { distributed: true, borderRadius: 4, columnWidth: '60%' }
        },
        dataLabels: { 
            enabled: true, 
            formatter: function (val) { return "$" + val.toLocaleString(); },
            style: { colors: ['#fff'] }
        },
        stroke: { width: 1, colors: ['transparent'] },
        xaxis: { type: 'category', labels: { style: { colors: labelColor, fontWeight: 600 } } },
        yaxis: { labels: { style: { colors: labelColor }, formatter: (value) => "$" + value.toLocaleString() } },
        tooltip: { theme: isDarkMode ? 'dark' : 'light', y: { formatter: function (val) { return "$" + val.toLocaleString() } } },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#waterfallChart"), waterfallOptions).render();

    // 6. Cost Trend Stacked Chart
    var trendOptions = {
        series: [
            { name: 'خرید (Purchase)', data: {!! json_encode($data['trends']['purchase']) !!} },
            { name: 'ترمیم (Repair)', data: {!! json_encode($data['trends']['repair']) !!} },
            { name: 'شستشو (Wash)', data: {!! json_encode($data['trends']['wash']) !!} },
            { name: 'فینشنگ (Finish)', data: {!! json_encode($data['trends']['finish']) !!} }
        ],
        chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false }, background: 'transparent' },
        colors: ['#3b82f6', '#f59e0b', '#0ea5e9', '#8b5cf6'],
        plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: {!! json_encode($data['trends']['months']) !!}, labels: { style: { colors: labelColor } } },
        yaxis: { labels: { style: { colors: labelColor }, formatter: (value) => "$" + value.toLocaleString() } },
        legend: { position: 'top', labels: { colors: labelColor } },
        fill: { opacity: 1 },
        tooltip: { theme: isDarkMode ? 'dark' : 'light', y: { formatter: function (val) { return "$" + val.toLocaleString() } } },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#costTrendChart"), trendOptions).render();
});
</script>
@endsection
