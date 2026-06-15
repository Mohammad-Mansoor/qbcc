@extends('dsh.master')

@section('title', 'داشبورد گدام (Inventory Dashboard)')

@section('content')

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        --primary-blue: #0A192F;
        --accent-blue: #00acc1;
        --accent-green: #10B981;
        --accent-purple: #8B5CF6;
        --accent-orange: #F59E0B;
        --card-radius: 20px;
    }

    body {
        background-color: #f4f7fa;
        font-family: 'Inter', sans-serif;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-blue), #1e3c72);
        color: white;
        padding: 30px;
        border-radius: var(--card-radius);
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(10, 25, 47, 0.15);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        transform: rotate(30deg);
        pointer-events: none;
    }

    .kpi-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 25px 20px;
        box-shadow: var(--glass-shadow);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(31, 38, 135, 0.12);
    }

    .kpi-icon {
        position: absolute;
        bottom: -20px;
        left: -20px;
        font-size: 7rem;
        opacity: 0.04;
        z-index: 0;
        transform: rotate(-15deg);
        transition: all 0.4s ease;
    }

    .kpi-card:hover .kpi-icon {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.08;
    }

    .kpi-title {
        color: #64748b;
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }

    .kpi-value {
        color: var(--primary-blue);
        font-size: 2rem;
        font-weight: 900;
        position: relative;
        z-index: 1;
        letter-spacing: -0.5px;
    }

    .section-title {
        color: var(--primary-blue);
        font-weight: 800;
        font-size: 1.4rem;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: white;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .bg-gradient-info { background: linear-gradient(135deg, #00acc1, #00838f); }
    .bg-gradient-warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .bg-gradient-success { background: linear-gradient(135deg, #10B981, #059669); }
    .bg-gradient-purple { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }

    .data-table-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: var(--card-radius);
        padding: 30px;
        box-shadow: var(--glass-shadow);
        backdrop-filter: blur(10px);
        margin-bottom: 40px;
    }

    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
        height: 350px;
    }

    .premium-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .premium-table th {
        background-color: transparent;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 12px 20px;
        border: none;
    }

    .premium-table tbody tr {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .premium-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
    }

    .premium-table td {
        padding: 18px 20px;
        border: none;
        vertical-align: middle;
    }

    .premium-table td:first-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    .premium-table td:last-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }

    .badge-soft {
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .badge-primary-soft { background: rgba(10, 25, 47, 0.08); color: var(--primary-blue); }
    .badge-success-soft { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
    .badge-warning-soft { background: rgba(245, 158, 11, 0.1); color: var(--accent-orange); }
    .badge-purple-soft { background: rgba(139, 92, 246, 0.1); color: var(--accent-purple); }

    .warehouse-breakdown {
        background: #f8fafc;
        border-radius: 10px;
        padding: 12px 15px;
        margin-top: 5px;
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
    }

    .wh-item {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px dashed #cbd5e1;
    }
    .wh-item:last-child { border-bottom: none; padding-bottom: 0; }
    
    .print-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 12px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .print-btn:hover { background: white; color: var(--primary-blue); transform: translateY(-2px); }

    /* Animated Sub-Cards */
    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.06);
    }
    .metric-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-left: 15px;
    }
    .metric-info h5 { font-size: 1.1rem; font-weight: 800; margin: 0; }
    .metric-info span { font-size: 0.8rem; color: #64748b; font-weight: 600; }

    @media print {
        .pcoded-navbar, .pcoded-header, .print-btn { display: none !important; }
        .pcoded-main-container { margin: 0 !important; }
        .kpi-card, .data-table-card, .chart-container { box-shadow: none !important; border: 1px solid #ddd !important; }
        body { background: white !important; }
    }
</style>

<div class="container-fluid" style="direction: rtl;">
    <!-- Dashboard Header -->
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="font-weight-bold mb-2" style="color: white;"><i class="fa fa-cubes mr-2"></i> داشبورد تحلیلی گدام‌ها (ERP Stock Dashboard)</h2>
            <p class="mb-0 text-white-50" style="font-size: 1.1rem;">گزارش جامع و تفکیک شده از فزیک گدام‌ها (تار، رنگ، قالین)</p>
        </div>
        <div>
            <button onclick="window.print()" class="print-btn"><i class="fa fa-print mr-2"></i> چاپ گزارش</button>
        </div>
    </div>

    <!-- Main KPI Summary Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="kpi-card">
                <i class="fa fa-shopping-bag kpi-icon text-info"></i>
                <div class="kpi-title">کل موجودی تار (Yarn)</div>
                <div class="kpi-value text-info">{{ number_format($totals['yarn_qty'], 2) }} <span style="font-size: 1.1rem; color: #64748b;">کیلو</span></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="kpi-card">
                <i class="fa fa-paint-brush kpi-icon text-warning"></i>
                <div class="kpi-title">کل موجودی رنگ (Dye)</div>
                <div class="kpi-value text-warning">{{ number_format($totals['dye_qty'], 2) }} <span style="font-size: 1.1rem; color: #64748b;">کیلو</span></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="kpi-card">
                <i class="fa fa-industry kpi-icon" style="color: #8B5CF6;"></i>
                <div class="kpi-title">قالین پروسس/خام (WIP)</div>
                <div class="kpi-value" style="color: #8B5CF6;">
                    {{ number_format($totals['carpet_qty']) }} <span style="font-size: 1.1rem; color: #64748b;">تخته</span>
                    <div style="font-size: 0.95rem; color: #94a3b8; font-weight: 600; margin-top: 5px;">{{ number_format($totals['carpet_area'], 2) }} متر مربع</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="kpi-card">
                <i class="fa fa-check-circle kpi-icon text-success"></i>
                <div class="kpi-title">قالین آماده فروش (Ready)</div>
                <div class="kpi-value text-success">
                    {{ number_format($totals['ready_carpet_qty']) }} <span style="font-size: 1.1rem; color: #64748b;">تخته</span>
                    <div style="font-size: 0.95rem; color: #94a3b8; font-weight: 600; margin-top: 5px;">{{ number_format($totals['ready_carpet_area'], 2) }} متر مربع</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yarn Stock Details -->
    <div class="data-table-card">
        <h4 class="section-title"><i class="fa fa-shopping-bag bg-gradient-info"></i> تحلیل موجودی تار (Yarn Analytics)</h4>
        @if(empty($dashboardData['yarn']['by_cat_type']))
            <div class="alert alert-light text-center">موجودی تار در گدام صفر است.</div>
        @else
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس کتگوری:</h6>
                    <div class="row">
                        @foreach($dashboardData['yarn']['by_cat'] as $cat => $qty)
                        <div class="col-md-6">
                            <div class="metric-card border-info" style="border-left: 4px solid #00acc1;">
                                <div class="metric-icon bg-gradient-info text-white"><i class="fa fa-tags"></i></div>
                                <div class="metric-info">
                                    <span>کتگوری: {{ $cat }}</span>
                                    <h5 class="text-info">{{ number_format($qty, 2) }} کیلو</h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس نوعیت تار:</h6>
                    <div class="row">
                        @foreach($dashboardData['yarn']['by_type'] as $type => $qty)
                        <div class="col-md-6">
                            <div class="metric-card border-info" style="border-right: 4px solid #00acc1;">
                                <div class="metric-icon bg-gradient-info text-white"><i class="fa fa-cubes"></i></div>
                                <div class="metric-info">
                                    <span>نوعیت: {{ $type }}</span>
                                    <h5 class="text-info">{{ number_format($qty, 2) }} کیلو</h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <h6 class="text-muted font-weight-bold mt-4 mb-3">تفکیک جدول کلی با موقعیت گدام‌ها:</h6>
            <div class="table-responsive">
                <table class="premium-table text-right">
                    <thead>
                        <tr>
                            <th>نوعیت و کتگوری</th>
                            <th class="text-center">مجموع موجودی</th>
                            <th>موقعیت گدام‌ها</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboardData['yarn']['by_cat_type'] as $name => $data)
                            <tr>
                                <td class="font-weight-bold text-dark">{{ $name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-primary-soft">{{ number_format($data['total'], 2) }} کیلو</span>
                                </td>
                                <td>
                                    <div class="warehouse-breakdown">
                                        @foreach($data['warehouses'] as $wh => $qty)
                                            @if($qty > 0)
                                            <div class="wh-item">
                                                <span class="text-muted"><i class="fa fa-map-marker text-info mr-1"></i> {{ $wh }}:</span>
                                                <span class="font-weight-bold">{{ number_format($qty, 2) }} کیلو</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Dye Stock Details -->
    <div class="data-table-card">
        <h4 class="section-title"><i class="fa fa-paint-brush bg-gradient-warning"></i> تحلیل موجودی رنگ (Dye Analytics)</h4>
        @if(empty($dashboardData['dye']['by_cat_type']))
            <div class="alert alert-light text-center">موجودی رنگ در گدام صفر است.</div>
        @else
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس کتگوری:</h6>
                    <div class="row">
                        @foreach($dashboardData['dye']['by_cat'] as $cat => $qty)
                        <div class="col-md-6">
                            <div class="metric-card border-warning" style="border-left: 4px solid #F59E0B;">
                                <div class="metric-icon bg-gradient-warning text-white"><i class="fa fa-tags"></i></div>
                                <div class="metric-info">
                                    <span>کتگوری: {{ $cat }}</span>
                                    <h5 class="text-warning">{{ number_format($qty, 2) }} کیلو</h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس نوعیت رنگ:</h6>
                    <div class="row">
                        @foreach($dashboardData['dye']['by_type'] as $type => $qty)
                        <div class="col-md-6">
                            <div class="metric-card border-warning" style="border-right: 4px solid #F59E0B;">
                                <div class="metric-icon bg-gradient-warning text-white"><i class="fa fa-tint"></i></div>
                                <div class="metric-info">
                                    <span>نوعیت: {{ $type }}</span>
                                    <h5 class="text-warning">{{ number_format($qty, 2) }} کیلو</h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <h6 class="text-muted font-weight-bold mt-4 mb-3">تفکیک جدول کلی با موقعیت گدام‌ها:</h6>
            <div class="table-responsive">
                <table class="premium-table text-right">
                    <thead>
                        <tr>
                            <th>نوعیت و کتگوری</th>
                            <th class="text-center">مجموع موجودی</th>
                            <th>موقعیت گدام‌ها</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboardData['dye']['by_cat_type'] as $name => $data)
                            <tr>
                                <td class="font-weight-bold text-dark">{{ $name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-warning-soft">{{ number_format($data['total'], 2) }} کیلو</span>
                                </td>
                                <td>
                                    <div class="warehouse-breakdown">
                                        @foreach($data['warehouses'] as $wh => $qty)
                                            @if($qty > 0)
                                            <div class="wh-item">
                                                <span class="text-muted"><i class="fa fa-map-marker text-warning mr-1"></i> {{ $wh }}:</span>
                                                <span class="font-weight-bold">{{ number_format($qty, 2) }} کیلو</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Ready for Sale Carpets Details -->
    <div class="data-table-card">
        <h4 class="section-title"><i class="fa fa-check-circle bg-gradient-success"></i> تحلیل قالین‌های آماده فروش (Ready Carpets Analytics)</h4>
        @if(empty($dashboardData['ready_carpet']['by_qual_type']))
            <div class="alert alert-light text-center">هیچ قالین آماده فروش موجود نیست.</div>
        @else
            <div class="row mb-4">
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس کوالیتی (تخته/مساحت):</h6>
                    <div class="row">
                        @foreach($dashboardData['ready_carpet']['by_qual'] as $qual => $vals)
                        <div class="col-md-6">
                            <div class="metric-card border-success" style="border-left: 4px solid #10B981;">
                                <div class="metric-icon bg-gradient-success text-white"><i class="fa fa-star"></i></div>
                                <div class="metric-info">
                                    <span>کوالیتی: {{ $qual }}</span>
                                    <h5 class="text-success">{{ number_format($vals['qty']) }} تخته</h5>
                                    <span style="font-size: 0.8rem;">{{ number_format($vals['area'], 2) }} M<sup>2</sup></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس نوعیت قالین (تخته/مساحت):</h6>
                    <div class="row">
                        @foreach($dashboardData['ready_carpet']['by_type'] as $type => $vals)
                        <div class="col-md-6">
                            <div class="metric-card border-success" style="border-right: 4px solid #10B981;">
                                <div class="metric-icon bg-gradient-success text-white"><i class="fa fa-th-large"></i></div>
                                <div class="metric-info">
                                    <span>نوعیت: {{ $type }}</span>
                                    <h5 class="text-success">{{ number_format($vals['qty']) }} تخته</h5>
                                    <span style="font-size: 0.8rem;">{{ number_format($vals['area'], 2) }} M<sup>2</sup></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <h6 class="text-muted font-weight-bold mt-4 mb-3">تفکیک جدول کلی با موقعیت گدام‌ها:</h6>
            <div class="table-responsive">
                <table class="premium-table text-right">
                    <thead>
                        <tr>
                            <th>نوع و کوالیتی قالین</th>
                            <th class="text-center">مجموع تخته</th>
                            <th class="text-center">مجموع مساحت</th>
                            <th>موقعیت گدام‌ها (تفکیک)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboardData['ready_carpet']['by_qual_type'] as $name => $data)
                            <tr>
                                <td class="font-weight-bold text-dark" style="font-size: 1.05rem;">{{ $name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-success-soft">{{ number_format($data['total_qty']) }} تخته</span>
                                </td>
                                <td class="text-center font-weight-bold text-success">
                                    {{ number_format($data['total_area'], 2) }} M<sup>2</sup>
                                </td>
                                <td>
                                    <div class="warehouse-breakdown">
                                        @foreach($data['warehouses'] as $wh => $vals)
                                            @if($vals['qty'] > 0)
                                            <div class="wh-item">
                                                <span class="text-muted"><i class="fa fa-building text-success mr-1"></i> {{ $wh }}:</span>
                                                <span class="font-weight-bold">{{ number_format($vals['qty']) }} تخته <small class="text-muted">({{ number_format($vals['area'], 2) }} متر)</small></span>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- WIP Carpets Details -->
    <div class="data-table-card">
        <h4 class="section-title"><i class="fa fa-industry bg-gradient-purple"></i> تحلیل قالین‌های خام و تحت پروسس (WIP Carpets Analytics)</h4>
        @if(empty($dashboardData['carpet']['by_qual_type']))
            <div class="alert alert-light text-center">هیچ قالین تحت پروسس موجود نیست.</div>
        @else
            <div class="row mb-4">
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس کوالیتی (تخته/مساحت):</h6>
                    <div class="row">
                        @foreach($dashboardData['carpet']['by_qual'] as $qual => $vals)
                        <div class="col-md-6">
                            <div class="metric-card" style="border-left: 4px solid #8B5CF6;">
                                <div class="metric-icon bg-gradient-purple text-white"><i class="fa fa-star"></i></div>
                                <div class="metric-info">
                                    <span>کوالیتی: {{ $qual }}</span>
                                    <h5 style="color: #8B5CF6;">{{ number_format($vals['qty']) }} تخته</h5>
                                    <span style="font-size: 0.8rem;">{{ number_format($vals['area'], 2) }} M<sup>2</sup></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-muted font-weight-bold mb-3">موجودی بر اساس نوعیت قالین (تخته/مساحت):</h6>
                    <div class="row">
                        @foreach($dashboardData['carpet']['by_type'] as $type => $vals)
                        <div class="col-md-6">
                            <div class="metric-card" style="border-right: 4px solid #8B5CF6;">
                                <div class="metric-icon bg-gradient-purple text-white"><i class="fa fa-th-large"></i></div>
                                <div class="metric-info">
                                    <span>نوعیت: {{ $type }}</span>
                                    <h5 style="color: #8B5CF6;">{{ number_format($vals['qty']) }} تخته</h5>
                                    <span style="font-size: 0.8rem;">{{ number_format($vals['area'], 2) }} M<sup>2</sup></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <h6 class="text-muted font-weight-bold mt-4 mb-3">تفکیک جدول کلی با موقعیت گدام‌ها:</h6>
            <div class="table-responsive">
                <table class="premium-table text-right">
                    <thead>
                        <tr>
                            <th>نوع و کوالیتی قالین</th>
                            <th class="text-center">مجموع تخته</th>
                            <th class="text-center">مجموع مساحت</th>
                            <th>موقعیت گدام‌ها (تفکیک)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboardData['carpet']['by_qual_type'] as $name => $data)
                            <tr>
                                <td class="font-weight-bold text-dark" style="font-size: 1.05rem;">{{ $name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-purple-soft">{{ number_format($data['total_qty']) }} تخته</span>
                                </td>
                                <td class="text-center font-weight-bold" style="color: #8B5CF6;">
                                    {{ number_format($data['total_area'], 2) }} M<sup>2</sup>
                                </td>
                                <td>
                                    <div class="warehouse-breakdown">
                                        @foreach($data['warehouses'] as $wh => $vals)
                                            @if($vals['qty'] > 0)
                                            <div class="wh-item">
                                                <span class="text-muted"><i class="fa fa-cogs text-purple mr-1" style="color: #8B5CF6 !important;"></i> {{ $wh }}:</span>
                                                <span class="font-weight-bold">{{ number_format($vals['qty']) }} تخته <small class="text-muted">({{ number_format($vals['area'], 2) }} متر)</small></span>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>



@endsection
