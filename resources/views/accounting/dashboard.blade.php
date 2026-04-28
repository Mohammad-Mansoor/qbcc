@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    
    <!-- Premium Header & Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #4099ff, #73b4ff);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-7 text-white">
                            <h2 class="font-weight-bold text-white mb-1">داشبورد تحلیلی امور مالی</h2>
                            <p class="mb-0 opacity-80" style="font-size: 1.1rem;">
                                مرور عملکرد و سلامت مالی شرکت در بازه زمانی: 
                                <span class="badge badge-light px-3 py-2 mx-1" style="font-size: 1rem; color: #4099ff;">{{ $startDate }}</span> 
                                الی 
                                <span class="badge badge-light px-3 py-2 mx-1" style="font-size: 1rem; color: #4099ff;">{{ $endDate }}</span>
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="bg-white p-3 shadow-sm" style="border-radius: 12px;">
                                <form action="{{ route('accounting.dashboard') }}" method="GET">
                                    <div class="row no-gutters align-items-end">
                                        <div class="col-5 px-1">
                                            <label class="small font-weight-bold text-muted mb-1">از تاریخ (From)</label>
                                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <div class="col-5 px-1">
                                            <label class="small font-weight-bold text-muted mb-1">الی تاریخ (To)</label>
                                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <div class="col-2 px-1">
                                            <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm" style="border-radius: 8px;">
                                                <i class="feather icon-refresh-cw"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row">
        @php
            $kpiList = [
                ['key' => 'revenue', 'label' => 'مجموع عواید', 'icon' => 'feather icon-trending-up', 'color' => 'bg-c-green', 'detail' => 'Revenue'],
                ['key' => 'expenses', 'label' => 'مجموع هزینه‌ها', 'icon' => 'feather icon-trending-down', 'color' => 'bg-c-yellow', 'detail' => 'Expenses'],
                ['key' => 'profit', 'label' => 'سود خالص', 'icon' => 'feather icon-award', 'color' => 'bg-c-blue', 'detail' => 'Net Profit'],
                ['key' => 'cash', 'label' => 'موجودی نقد', 'icon' => 'feather icon-pocket', 'color' => 'bg-info', 'detail' => 'Cash Flow'],
                ['key' => 'receivables', 'label' => 'طلبات (AR)', 'icon' => 'feather icon-users', 'color' => 'bg-c-purple', 'detail' => 'Receivables'],
                ['key' => 'payables', 'label' => 'بدهی‌ها (AP)', 'icon' => 'feather icon-credit-card', 'color' => 'bg-c-red', 'detail' => 'Payables'],
            ];
        @endphp

        @foreach($kpiList as $item)
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle {{ $item['color'] }} text-white p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="{{ $item['icon'] }}"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">{{ $item['label'] }}</h6>
                            <small class="text-muted opacity-50">{{ $item['detail'] }}</small>
                        </div>
                    </div>
                    <h4 class="font-weight-bold mb-2">{{ number_format($kpis[$item['key']]['value'], 2) }} <small>AFN</small></h4>
                    <div class="d-flex align-items-center">
                        @if($kpis[$item['key']]['change'] >= 0)
                            <span class="text-success small font-weight-bold">
                                <i class="feather icon-arrow-up-right mr-1"></i>{{ abs($kpis[$item['key']]['change']) }}%
                            </span>
                        @else
                            <span class="text-danger small font-weight-bold">
                                <i class="feather icon-arrow-down-right mr-1"></i>{{ abs($kpis[$item['key']]['change']) }}%
                            </span>
                        @endif
                        <span class="text-muted small ml-2">نسبت به قبل</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- P&L Trend -->
        <div class="col-xl-8 col-md-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold text-dark">روند عواید و مصارف (P&L Trend)</h5>
                </div>
                <div class="card-body">
                    <div id="profit-loss-chart"></div>
                </div>
            </div>
        </div>

        <!-- Financial Health -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 text-center">
                    <h5 class="mb-0 font-weight-bold text-dark">شاخص‌های سلامت مالی</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-6 text-center">
                            <div class="p-3 bg-light rounded-lg">
                                <h3 class="text-primary font-weight-bold mb-0">{{ $ratios['liquidity']['current_ratio'] }}</h3>
                                <p class="text-muted small mb-0 font-weight-bold">نسبت جاری</p>
                                <span class="badge {{ $ratios['liquidity']['current_ratio'] >= 1 ? 'badge-success' : 'badge-danger' }} mt-1">
                                    {{ $ratios['liquidity']['current_ratio'] >= 1.5 ? 'عالی' : ($ratios['liquidity']['current_ratio'] >= 1 ? 'متوسط' : 'خطرناک') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="p-3 bg-light rounded-lg">
                                <h3 class="text-success font-weight-bold mb-0">{{ $ratios['profitability']['net_margin'] }}%</h3>
                                <p class="text-muted small mb-0 font-weight-bold">مارجین سود</p>
                                <span class="badge badge-success mt-1">سودآور</span>
                            </div>
                        </div>
                    </div>
                    <hr class="opacity-10">
                    <h6 class="font-weight-bold text-dark mb-3">تحلیل طلبات (Aging Analysis)</h6>
                    <div id="aging-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cash Flow -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold text-dark">جریان وجوه نقد (Inflow vs Outflow)</h5>
                </div>
                <div class="card-body">
                    <div id="cash-flow-chart"></div>
                </div>
            </div>
        </div>

        <!-- Activity -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold text-dark">حجم فعالیت‌های مالی (Transactions)</h5>
                </div>
                <div class="card-body">
                    <div id="activity-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark">آخرین فعالیت‌های دفتر کل</h5>
                    <a href="{{ route('accounting.journals.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">مشاهده همه</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light text-muted small uppercase">
                                <tr>
                                    <th class="border-0 py-3 px-4">تاریخ</th>
                                    <th class="border-0 py-3">نمبر سند</th>
                                    <th class="border-0 py-3">تفصیلات</th>
                                    <th class="border-0 py-3">نوعیت</th>
                                    <th class="border-0 py-3 text-right px-4">مبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $tx)
                                <tr style="cursor: pointer;">
                                    <td class="py-3 px-4">{{ $tx->date }}</td>
                                    <td class="py-3 font-weight-bold">{{ $tx->reference }}</td>
                                    <td class="py-3 text-muted">{{ $tx->description }}</td>
                                    <td class="py-3">
                                        <span class="badge badge-light-primary">{{ strtoupper($tx->journal_type) }}</span>
                                    </td>
                                    <td class="py-3 text-right px-4 font-weight-bold text-dark">
                                        {{ number_format($tx->entries->sum('debit'), 2) }} <small>AFN</small>
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
</div>

<style>
    .card { transition: all 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .opacity-80 { opacity: 0.8; }
    .badge-light-primary { background: #e3f2fd; color: #1976d2; border-radius: 5px; padding: 4px 8px; font-weight: 600; }
</style>
@endsection

@section('footer-plugins')
<script src="/dsh/assets/js/plugins/apexcharts.min.js"></script>
<script>
    $(document).ready(function() {
        // P&L Trend
        var plOptions = {
            series: [{ name: 'عواید (Revenue)', data: {!! json_encode($profit_loss->pluck('revenue')) !!} }, 
                    { name: 'هزینه‌ها (Expenses)', data: {!! json_encode($profit_loss->pluck('expense')) !!} }],
            chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
            colors: ['#2ed8b6', '#ffb64d'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [20, 80, 100] } },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: {!! json_encode($profit_loss->pluck('date')) !!}, axisBorder: { show: false } },
            yaxis: { labels: { formatter: function (val) { return val.toLocaleString() + " AFN"; } } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#f1f1f1' }
        };
        new ApexCharts(document.querySelector("#profit-loss-chart"), plOptions).render();

        // Cash Flow
        var cashOptions = {
            series: [{ name: 'ورودی (In)', data: {!! json_encode($cash_flow->pluck('inflow')) !!} }, 
                    { name: 'خروجی (Out)', data: {!! json_encode($cash_flow->pluck('outflow')) !!} }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            colors: ['#4099ff', '#ff5370'],
            plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
            xaxis: { categories: {!! json_encode($cash_flow->pluck('date')) !!}, axisBorder: { show: false } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#f1f1f1' }
        };
        new ApexCharts(document.querySelector("#cash-flow-chart"), cashOptions).render();

        // Aging
        var agingOptions = {
            series: {!! json_encode(array_values($aging)) !!},
            chart: { type: 'donut', height: 250 },
            labels: ['0-30 روز', '31-60 روز', '61-90 روز', '90+ روز'],
            colors: ['#2ed8b6', '#4099ff', '#ffb64d', '#ff5370'],
            legend: { position: 'bottom' },
            stroke: { width: 0 },
            plotOptions: { pie: { donut: { size: '75%' } } }
        };
        new ApexCharts(document.querySelector("#aging-chart"), agingOptions).render();

        // Activity
        var activityOptions = {
            series: [{ name: 'تعداد تراکنش', data: {!! json_encode($activity->pluck('count')) !!} }],
            chart: { type: 'line', height: 300, toolbar: { show: false } },
            colors: ['#4099ff'],
            stroke: { width: 4, curve: 'smooth' },
            xaxis: { categories: {!! json_encode($activity->pluck('date')) !!} },
            markers: { size: 5, strokeWidth: 3, hover: { size: 8 } }
        };
        new ApexCharts(document.querySelector("#activity-chart"), activityOptions).render();
    });
</script>
@endsection
