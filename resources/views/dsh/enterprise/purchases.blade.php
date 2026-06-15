@extends('dsh.master')

@section('title', 'داشبورد خریدات')

@section('content')

<!-- KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Total Purchases -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">مجموع خرید (کل)</div>
                    <div class="kpi-value text-primary">${{ number_format($data['kpis']['total_purchases'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-primary">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Purchases -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">خرید ماهانه</div>
                    <div class="kpi-value text-warning">${{ number_format($data['kpis']['monthly_purchases'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-warning">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Materials Value -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">موجودی مواد خام</div>
                    <div class="kpi-value text-success">${{ number_format($data['kpis']['raw_materials_value'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-success">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Supplier -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">بهترین تامین کننده</div>
                    <div class="kpi-value text-info fs-5">{{ $data['kpis']['top_supplier'] ?? 'N/A' }}</div>
                </div>
                <div class="kpi-icon text-info">
                    <i class="fa-solid fa-handshake"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Purchase Trends -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">روند خرید (۱۲ ماه گذشته)</h5>
            <div id="purchaseTrendChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Supplier Distribution -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">توزیع تامین کنندگان</h5>
            <div id="supplierDistributionChart" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

<!-- Bottom Data Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Restock Needs -->
    <div class="col-md-5 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-boxes-stacked me-2"></i> اقدام ضروری: کمبود موجودی</h5>
            <ul class="list-group list-group-flush">
                @forelse($data['restock_needs'] as $item)
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-3">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $item['name'] }}</h6>
                            <small class="text-muted">Current Quantity: {{ $item['quantity'] }} units</small>
                        </div>
                        <span class="badge {{ strtolower($item['status']) === 'critical' ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill">
                            {{ strtolower($item['status']) === 'critical' ? 'بحرانی' : 'اخطار' }}
                        </span>
                    </li>
                @empty
                    <li class="list-group-item bg-transparent text-center text-muted py-4">No urgent restock needed.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Recent Purchases Table -->
    <div class="col-md-7 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-file-invoice me-2"></i> بل های اخیر خرید</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>شماره بل</th>
                            <th>تاریخ</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_purchases'] as $inv)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $inv['number'] }}</td>
                            <td>{{ $inv['date'] }}</td>
                            <td class="fw-bold">${{ number_format($inv['amount']) }}</td>
                            <td>
                                @if(strtolower($inv['status']) === 'open')
                                    <span class="badge bg-warning text-dark">Open</span>
                                @else
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No recent purchases found.</td>
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
        
        // Purchase Trend Line Chart
        var trendOptions = {
            series: [{
                name: 'Purchases',
                data: {!! json_encode($data['purchase_trend']['data'] ?? []) !!}
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#3b82f6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: {!! json_encode($data['purchase_trend']['months'] ?? []) !!},
            },
            tooltip: {
                y: { formatter: function (val) { return "$" + val.toLocaleString() } }
            }
        };
        var trendChart = new ApexCharts(document.querySelector("#purchaseTrendChart"), trendOptions);
        trendChart.render();


        // Supplier Distribution Donut Chart
        var supplierLabels = {!! json_encode($data['supplier_distribution']['labels'] ?? []) !!};
        var supplierData = {!! json_encode($data['supplier_distribution']['data'] ?? []) !!};

        if (supplierLabels.length === 0) {
            supplierLabels = ['No Data'];
            supplierData = [1];
        }

        var donutOptions = {
            series: supplierData,
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            labels: supplierLabels,
            colors: ['#0A192F', '#10B981', '#F59E0B', '#3b82f6', '#8b5cf6', '#ec4899'],
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { show: true },
                            value: { 
                                show: true,
                                formatter: function (val) { return "$" + parseInt(val).toLocaleString() }
                            }
                        }
                    }
                }
            }
        };
        var donutChart = new ApexCharts(document.querySelector("#supplierDistributionChart"), donutOptions);
        donutChart.render();

    });
</script>
@endsection
