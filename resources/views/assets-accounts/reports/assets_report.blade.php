@extends('dsh.master')

@section('title', 'گزارش اجناس ثابت شرکت')

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
            <h3 class="mb-1 text-white" style="font-weight: 800;"><i class="fa fa-briefcase mr-2"></i> گزارش جامع اجناس ثابت شرکت</h3>
            <p class="mb-0 text-white-50">رهگیری و تحلیل تمام دارایی‌ها و اجناس ثابت</p>
        </div>
        <div class="d-flex gap-2">
            @can('export_assets_report_excel')
            <a href="{{ route('assets.report.excel', request()->all()) }}" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> خروجی اکسل (Excel)</a>
            @endcan
            @can('export_assets_report_pdf')
            <a href="{{ route('assets.report.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-export"><i class="fa fa-file-pdf-o"></i> خروجی پی‌دی‌اف (PDF)</a>
            @endcan
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="kpi-card" style="border-right: 4px solid #00acc1;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #00acc1, #00838f);"><i class="fa fa-cubes"></i></div>
                <div class="kpi-details">
                    <h6>مجموع تعداد اجناس ثبت شده</h6>
                    <h3>{{ number_format($kpis['total_items']) }} <small style="font-size: 1rem;">قلم</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="border-right: 4px solid #F59E0B;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);"><i class="fa fa-dollar"></i></div>
                <div class="kpi-details">
                    <h6>مجموع ارزش اولیه ثبت شده (Base Cost)</h6>
                    <h3>{{ number_format($kpis['total_cost_usd'], 2) }} <small style="font-size: 1rem;">$</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="border-right: 4px solid #10B981;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #10B981, #059669);"><i class="fa fa-line-chart"></i></div>
                <div class="kpi-details">
                    <h6>مجموع ارزش فعلی تخمینی (Book Value)</h6>
                    <h3>{{ number_format($kpis['total_book_value_usd'], 2) }} <small style="font-size: 1rem;">$</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <form action="{{ route('assets.report') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted font-weight-bold">از تاریخ خرید</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted font-weight-bold">تا تاریخ خرید</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label text-muted font-weight-bold">نمبر جنس</label>
                    <input type="text" name="asset_number" value="{{ request('asset_number') }}" class="form-control" placeholder="جستجو نمبر...">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted font-weight-bold">حساب / کتگوری</label>
                    <select name="account_id" class="form-control select2">
                        <option value="">همه حساب‌ها</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->aa_id }}" {{ request('account_id') == $acc->aa_id ? 'selected' : '' }}>{{ $acc->aa_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted font-weight-bold">کلاس جنس</label>
                    <select name="asset_class" class="form-control select2">
                        <option value="">همه کلاس‌ها</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ request('asset_class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12 mt-3 d-flex justify-content-end gap-2">
                    @if(request()->hasAny(['from_date', 'to_date', 'account_id', 'asset_class', 'asset_number']))
                        <a href="{{ route('assets.report') }}" class="btn btn-light" style="border-radius: 10px; font-weight: 600;"><i class="fa fa-times"></i> پاک کردن فیلترها</a>
                    @endif
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-blue); border-color: var(--primary-blue); border-radius: 10px; font-weight: 600; padding: 10px 25px;"><i class="fa fa-search"></i> جستجو</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="premium-table text-right">
                <thead>
                    <tr>
                        <th>شماره ثبت</th>
                        <th>نمبر جنس / سریال نمبر</th>
                        <th>اسم و تفصیلات جنس</th>
                        <th>حساب (کتگوری)</th>
                        <th>موقعیت فزیکی</th>
                        <th>تاریخ خرید</th>
                        <th>قیمت اصلی</th>
                        <th>قیمت خرید (Base)</th>
                        <th>اسقاط / عمر مفید</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr>
                            <td class="font-weight-bold" style="color: var(--accent-blue);">#{{ $asset->aad_id }}</td>
                            <td>
                                <div><span class="badge badge-light" style="border: 1px solid #cbd5e1; color: #475569;">نمبر: {{ $asset->asset_number }}</span></div>
                                <div class="mt-1"><span class="badge badge-light" style="border: 1px solid #cbd5e1; color: #475569;">سریال: {{ $asset->asset_serial_number }}</span></div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $asset->asset_name }}</div>
                                <div class="text-muted small mt-1"><i class="fa fa-tag"></i> {{ $asset->asset_class }}</div>
                                <div class="text-muted small"><i class="fa fa-info-circle"></i> {{ $asset->asset_description }}</div>
                            </td>
                            <td><span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">{{ $asset->aa_name }}</span></td>
                            <td class="text-muted"><i class="fa fa-map-marker text-danger"></i> {{ $asset->physical_location }}</td>
                            <td class="text-muted"><i class="fa fa-calendar"></i> {{ $asset->acquisition_date }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ number_format($asset->original_amount ?? $asset->acquisition_cost, 2) }} {{ $asset->currency_code ?? '$' }}</div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-success">{{ number_format($asset->acquisition_cost, 2) }} USD</div>
                            </td>
                            <td>
                                <div class="text-danger small font-weight-bold">اسقاط: {{ number_format($asset->estimated_salvage_value, 2) }}</div>
                                <div class="text-info small mt-1 font-weight-bold">عمر: {{ $asset->estimated_useful_life }} سال</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted" style="font-size: 1.1rem;">
                                    <i class="fa fa-folder-open-o mb-3" style="font-size: 3rem; color: #cbd5e1;"></i><br>
                                    هیچ جنسی با این مشخصات یافت نشد
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 d-flex justify-content-center" dir="ltr">
            {{ $assets->links() }}
        </div>
    </div>
</div>

@endsection
