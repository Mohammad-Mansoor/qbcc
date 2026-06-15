@extends('dsh.master')

@section('title', 'تحلیل مصارف تولید')

@section('styles')
<style>
    .profit-leaderboard {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(255,255,255,1) 100%);
        border-left: 4px solid var(--emerald-green);
    }
    .loss-leaderboard {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(255,255,255,1) 100%);
        border-left: 4px solid #ef4444;
    }
</style>
@endsection

@section('content')

<!-- KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Average Cost Per SQM -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">متوسط مصرف فی متر مربع</div>
                    <div class="kpi-value">${{ number_format($data['avg_sqm_cost'] ?? 0, 2) }}</div>
                </div>
                <div class="kpi-icon text-primary"><i class="fa-solid fa-calculator"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Profitability Leaderboards Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Most Profitable Carpet Types -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-arrow-trend-up text-success me-2"></i> پرمفاد ترین نوعیت قالین</h5>
            @if($data['most_profitable'])
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-dark mb-1">{{ $data['most_profitable']['type'] }}</h3>
                    <span class="badge bg-success bg-opacity-25 text-success fs-6 border border-success">
                        {{ $data['most_profitable']['margin'] }}% حاشیه مفاد
                    </span>
                </div>
                <div class="text-end">
                    <div class="text-muted small">مفاد مجموعی</div>
                    <h4 class="fw-bold text-success m-0">${{ number_format($data['most_profitable']['profit']) }}</h4>
                </div>
            </div>
            @else
            <p class="text-muted">دیتا کافی جهت محاسبه موجود نیست.</p>
            @endif
        </div>
    </div>

    <!-- Least Profitable -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h6 class="text-uppercase text-danger fw-bold mb-3"><i class="fa-solid fa-triangle-exclamation me-2"></i> ضرورت به اقدام: کمترین حاشیه مفاد</h6>
            @if($data['least_profitable'])
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-dark mb-1">{{ $data['least_profitable']['type'] }}</h3>
                    <span class="badge bg-danger bg-opacity-25 text-danger fs-6 border border-danger">
                        {{ $data['least_profitable']['margin'] }}% حاشیه مفاد
                    </span>
                </div>
                <div class="text-end">
                    <div class="text-muted small">مفاد مجموعی</div>
                    <h4 class="fw-bold text-danger m-0">${{ number_format($data['least_profitable']['profit']) }}</h4>
                </div>
            </div>
            @else
            <p class="text-muted">دیتا کافی جهت محاسبه موجود نیست.</p>
            @endif
        </div>
    </div>
</div>

<!-- Main Cost Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Cost By Phase -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">مصارف نظر به مرحله تولید</h5>
            <div id="costBreakdownChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Cost Trend -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">روند مصارف تولید (۱۲ ماه)</h5>
            <div id="costTrendChart" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

<!-- Bottom Row: Table & Sqm Chart -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Sqm Cost Chart -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">مصرف فی متر مربع</h5>
            <div id="sqmCostChart" style="min-height: 400px;"></div>
        </div>
    </div>

    <!-- Profitability Table -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0">تحلیل مفاد نظر به نوعیت قالین</h5>
                <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-download"></i> خروجی</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>نوع قالین</th>
                            <th>عواید تخمینی</th>
                            <th>مصارف تخمینی</th>
                            <th>حاشیه مفاد (ROI)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['profitability_table'] as $row)
                        <tr>
                            <td class="fw-bold text-dark">{{ $row['type'] }}</td>
                            <td>${{ number_format($row['revenue']) }}</td>
                            <td class="text-danger">-${{ number_format($row['cost']) }}</td>
                            <td class="fw-bold text-success">${{ number_format($row['profit']) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $row['margin'] > 20 ? 'bg-success' : 'bg-warning text-dark' }} me-2">{{ $row['margin'] > 20 ? 'بالا' : 'متوسط' }}</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar {{ $row['margin'] > 20 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $row['margin'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">دیتا موجود نیست.</td>
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

        // 1. Cost Breakdown Donut
        var breakdownKeys = {!! json_encode(array_keys($data['overall_breakdown'] ?? [])) !!};
        var breakdownVals = {!! json_encode(array_values($data['overall_breakdown'] ?? [])) !!};

        var donutOptions = {
            series: breakdownVals,
            chart: {
                type: 'donut',
                height: 300,
                fontFamily: 'Inter, sans-serif'
            },
            labels: breakdownKeys,
            colors: ['#0A192F', '#10B981', '#F59E0B', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            name: { show: true },
                            value: { 
                                show: true,
                                formatter: function (val) { return val + "%" }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
        };
        var donutChart = new ApexCharts(document.querySelector("#costBreakdownChart"), donutOptions);
        donutChart.render();

        // 2. Cost Trend Line Chart
        var trendOptions = {
            series: [{
                name: 'مصارف تولید',
                data: {!! json_encode($data['cost_trend']['cost'] ?? []) !!}
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#F59E0B'],
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            xaxis: {
                categories: {!! json_encode($data['cost_trend']['months'] ?? []) !!},
            },
            tooltip: {
                y: { formatter: function (val) { return "$" + val.toLocaleString() } }
            }
        };
        var trendChart = new ApexCharts(document.querySelector("#costTrendChart"), trendOptions);
        trendChart.render();

        // 3. SQM Cost Bar Chart
        var sqmKeys = {!! json_encode(array_keys($data['cost_per_sqm'] ?? [])) !!};
        var sqmVals = {!! json_encode(array_values($data['cost_per_sqm'] ?? [])) !!};

        var sqmOptions = {
            series: [{
                name: 'مصرف فی متر مربع',
                data: sqmVals
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: { position: 'top' }
                }
            },
            colors: ['#3b82f6'],
            dataLabels: { 
                enabled: true, 
                offsetX: 20, 
                style: { fontSize: '12px', colors: ['#333'] },
                formatter: function (val) { return "$" + val }
            },
            xaxis: {
                categories: sqmKeys,
                labels: { formatter: function (val) { return "$" + val } }
            }
        };
        var sqmChart = new ApexCharts(document.querySelector("#sqmCostChart"), sqmOptions);
        sqmChart.render();

    });
</script>
@endsection
