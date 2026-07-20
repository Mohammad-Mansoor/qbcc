@extends('dsh.master')

@section('title', 'گزارش قالین های خرید شده')

@section('content')

<style>
    :root {
        --primary-blue: #0A192F;
        --accent-green: #10B981;
        --accent-blue: #00acc1;
        --accent-purple: #8B5CF6;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    body { background-color: #f4f7fa; font-family: 'Inter', sans-serif; direction: rtl; }

    /* Premium Header */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-blue), #1e3c72);
        color: white;
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 25px;
        box-shadow: 0 10px 20px rgba(10, 25, 47, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Filter Card */
    .filter-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--glass-shadow);
        margin-bottom: 25px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
    }

    /* KPI Cards */
    .kpi-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--glass-shadow);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }
    .kpi-details h6 { color: #64748b; font-size: 0.95rem; font-weight: 700; margin-bottom: 5px; }
    .kpi-details h3 { color: var(--primary-blue); font-size: 1.6rem; font-weight: 800; margin: 0; }

    /* Table */
    .table-container {
        background: var(--glass-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--glass-shadow);
    }
    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .premium-table th { color: #64748b; font-weight: 700; padding: 12px 15px; border: none; background: transparent; }
    .premium-table tbody tr { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.2s; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.06); }
    .premium-table td { padding: 15px; border: none; vertical-align: middle; }
    .premium-table td:first-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
    .premium-table td:last-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }

    /* Badges */
    .status-badge { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }
    .btn-export { border-radius: 10px; padding: 10px 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            @php
                $statusFilter = request('status') != '' && isset($statuses[request('status')]) 
                    ? $statuses[request('status')] 
                    : 'تمامی حالت‌ها (All Statuses)';
            @endphp
            <h3 class="mb-1 text-white" style="font-weight: 800;"><i class="fa fa-shopping-bag mr-2"></i> گزارش قالین های خرید شده - {{ $statusFilter }}</h3>
            <p class="mb-0 text-white-50">رهگیری و تحلیل تمام قالین‌های وارد شده به سیستم</p>
        </div>
        <div class="d-flex gap-2">
            @can('export_purchased_carpets_excel')
            <a href="{{ route('purchased.carpets.excel', request()->all()) }}" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> خروجی اکسل (Excel)</a>
            @endcan
            @can('export_purchased_carpets_pdf')
            <a href="{{ route('purchased.carpets.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-export"><i class="fa fa-file-pdf-o"></i> خروجی پی‌دی‌اف (PDF)</a>
            @endcan
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row mb-4">
        <div class="col">
            <div class="kpi-card" style="border-right: 4px solid #00acc1;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #00acc1, #00838f);"><i class="fa fa-cubes"></i></div>
                <div class="kpi-details">
                    <h6 style="font-size: 0.85rem;">کل قالین‌ها (مساحت)</h6>
                    <h3>{{ number_format($kpis['total_qty']) }} <small style="font-size: 0.8rem;">({{ number_format($kpis['total_area'], 2) }} M<sup>2</sup>)</small></h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card" style="border-right: 4px solid #6c757d;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #9ca3af, #4b5563);"><i class="fa fa-home"></i></div>
                <div class="kpi-details">
                    <h6 style="font-size: 0.85rem;">خام / در گدام (Raw)</h6>
                    <h3>{{ number_format($kpis['raw_qty']) }} <small style="font-size: 0.8rem;">تخته</small></h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card" style="border-right: 4px solid #F59E0B;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);"><i class="fa fa-cogs"></i></div>
                <div class="kpi-details">
                    <h6 style="font-size: 0.85rem;">در حال پروسس (WIP)</h6>
                    <h3>{{ number_format($kpis['wip_qty']) }} <small style="font-size: 0.8rem;">تخته</small></h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card" style="border-right: 4px solid #10B981;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #10B981, #059669);"><i class="fa fa-check-circle"></i></div>
                <div class="kpi-details">
                    <h6 style="font-size: 0.85rem;">آماده فروش (Ready)</h6>
                    <h3>{{ number_format($kpis['ready_qty']) }} <small style="font-size: 0.8rem;">تخته</small></h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card" style="border-right: 4px solid #8B5CF6;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9);"><i class="fa fa-shopping-cart"></i></div>
                <div class="kpi-details">
                    <h6 style="font-size: 0.85rem;">فروخته شده (Sold)</h6>
                    <h3>{{ number_format($kpis['sold_qty']) }} <small style="font-size: 0.8rem;">تخته</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('purchased.carpets.index') }}">
            <div class="row align-items-end mb-3">
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">از تاریخ:</label>
                    <input type="date" name="from_date" class="form-control bg-light" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">تا تاریخ:</label>
                    <input type="date" name="to_date" class="form-control bg-light" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">از نمبر پارچه:</label>
                    <input type="text" name="from_id" class="form-control bg-light" placeholder="مثال: QB1000" value="{{ request('from_id') }}">
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">تا نمبر پارچه:</label>
                    <input type="text" name="to_id" class="form-control bg-light" placeholder="مثال: QB1100" value="{{ request('to_id') }}">
                </div>
            </div>
            
            <div class="row align-items-end mb-3">
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">نوعیت قالین:</label>
                    <select name="type_id" class="form-control form-select bg-light">
                        <option value="">همه نوعیت‌ها</option>
                        @foreach($types as $type)
                            <option value="{{ $type->carpet_type_id }}" {{ request('type_id') == $type->carpet_type_id ? 'selected' : '' }}>{{ $type->carpet_type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">کوالیتی قالین:</label>
                    <select name="quality_id" class="form-control form-select bg-light">
                        <option value="">همه کوالیتی‌ها</option>
                        @foreach($qualities as $quality)
                            <option value="{{ $quality->id }}" {{ request('quality_id') == $quality->id ? 'selected' : '' }}>{{ $quality->quality }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">حالت فعلی قالین:</label>
                    <select name="status" class="form-control form-select bg-light">
                        @foreach($statuses as $val => $label)
                            <option value="{{ $val }}" {{ request('status') != null && request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted small mb-1">شماره نقشه (Map No):</label>
                    <input type="text" name="map_number" class="form-control bg-light" placeholder="جستجوی شماره نقشه..." value="{{ request('map_number') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <div class="d-flex" style="gap: 10px; width: 25%;">
                        <button type="submit" class="btn w-100" style="background-color: var(--primary-blue); color: white; border-radius: 10px; padding: 10px;"><i class="fa fa-search"></i> جستجو / فیلتر</button>
                        @php
                            $hasFilters = request()->filled('from_date') || request()->filled('to_date') || request()->filled('from_id') || request()->filled('to_id') || request()->filled('status') || request()->filled('type_id') || request()->filled('quality_id') || request()->filled('map_number');
                        @endphp
                        @if($hasFilters)
                            <a href="{{ route('purchased.carpets.index') }}" class="btn btn-outline-danger" style="border-radius: 10px; padding: 10px; white-space: nowrap;"><i class="fa fa-times"></i> پاکسازی</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="premium-table text-right">
                <thead>
                    <tr>
                        <th>شماره پارچه (ID)</th>
                        <th>تاریخ</th>
                        <th>نماینده فروشنده</th>
                        <th>نوعیت و کوالیتی</th>
                        <th>شماره نقشه (Map No)</th>
                        <th>ابعاد (متر)</th>
                        <th>مساحت (M<sup>2</sup>)</th>
                        <th>قیمت فی متر / کل</th>
                        <th>موقعیت فعلی / حالت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carpets as $carpet)
                    <tr>
                        <td class="font-weight-bold text-dark">{{ $carpet->carpet_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($carpet->date)->format('Y-m-d') }}</td>
                        <td>{{ $carpet->agent->user->name ?? 'نامشخص' }} {{ $carpet->agent->user->last_name ?? '' }}</td>
                        <td>
                            <div><strong style="color: var(--accent-blue);">{{ $carpet->type->carpet_type ?? 'نامشخص' }}</strong></div>
                            <div class="text-muted" style="font-size: 0.85rem;">{{ $carpet->quality->quality ?? 'نامشخص' }}</div>
                        </td>
                        <td class="font-weight-bold text-secondary">{{ $carpet->map_number ?? '---' }}</td>
                        @php
                            $origWidth = $carpet->buying_width > 0 ? $carpet->buying_width : $carpet->width;
                            $origHeight = $carpet->buying_height > 0 ? $carpet->buying_height : $carpet->height;
                            $origArea = $carpet->buying_area > 0 ? $carpet->buying_area : $carpet->area;
                            $origUnitPrice = $carpet->original_price > 0 ? $carpet->original_price : $carpet->price;
                            $origTotalPriceUsd = $carpet->carpet_price_us > 0 ? $carpet->carpet_price_us : $carpet->total_price;
                        @endphp
                        <td dir="ltr" class="text-right">{{ $origWidth }} × {{ $origHeight }}</td>
                        <td class="font-weight-bold text-success">{{ number_format($origArea, 2) }}</td>
                        <td>
                            <div>{{ number_format($origUnitPrice) }} $</div>
                            <div class="text-danger" style="font-weight: 700;">{{ number_format($origTotalPriceUsd) }} $</div>
                        </td>
                        <td>
                            @php
                                $statusLabel = $statuses[$carpet->status] ?? 'نامشخص';
                                $statusColor = 'badge-secondary';
                                if($carpet->status == 5) $statusColor = 'badge-success';
                                elseif($carpet->status == 6) $statusColor = 'badge-primary';
                                elseif(in_array($carpet->status, [2,3,4,12,13])) $statusColor = 'badge-warning text-dark';
                                elseif($carpet->status == 1) $statusColor = 'badge-info';
                            @endphp
                            <span class="status-badge {{ $statusColor }}">{{ $statusLabel }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">هیچ قالینی با این مشخصات یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $carpets->links() }}
        </div>
    </div>
</div>
@endsection
