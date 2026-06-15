@extends('dsh.master')

@section('title', 'داشبورد فروشات')

@section('content')

<!-- Top KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Total Revenue -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">مجموع عواید (امسال)</div>
                    <div class="kpi-value text-success">${{ number_format($data['kpis']['sales_today'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-success">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">عواید ماهانه</div>
                    <div class="kpi-value text-primary">${{ number_format($data['kpis']['monthly_sales'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-primary">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Order Value -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">متوسط ارزش سفارش</div>
                    <div class="kpi-value text-warning">${{ number_format($data['kpis']['average_order_value'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-warning">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">مجموع سفارشات (کل)</div>
                    <div class="kpi-value text-info">{{ number_format($data['kpis']['total_orders'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-info">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Sales Trend Line Chart -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">روند فروشات (۱۲ ماه گذشته)</h5>
            <div id="salesTrendChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Sales by Carpet Type -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">فروشات به تفکیک نوع قالین</h5>
            <div id="salesByTypeChart" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Top Customers -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-users me-2"></i> مشتریان برتر</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>نام مشتری</th>
                            <th>مجموع خرید</th>
                            <th>باقی‌مانده حساب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_customers'] as $c)
                        <tr>
                            <td class="fw-bold text-dark">{{ $c['name'] }}</td>
                            <td class="text-success">${{ number_format($c['total_purchases']) }}</td>
                            <td class="text-danger">${{ number_format($c['balance']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">موردی یافت نشد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-file-invoice me-2"></i> فاکتورهای اخیر</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>شماره فاکتور</th>
                            <th>مشتری</th>
                            <th>تاریخ</th>
                            <th>مبلغ</th>
                            <th>حالت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_invoices'] as $inv)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $inv['number'] }}</td>
                            <td>{{ $inv['customer'] }}</td>
                            <td>{{ $inv['date'] }}</td>
                            <td class="fw-bold">${{ number_format($inv['amount']) }}</td>
                            <td>
                                @if(strtolower($inv['status']) === 'open')
                                    <span class="badge bg-warning text-dark">باز</span>
                                @else
                                    <span class="badge bg-success">بسته</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">موردی یافت نشد.</td>
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
        
        // Sales Trend Area Chart
        var trendOptions = {
            series: [{
                name: 'عواید ($)',
                data: {!! json_encode($data['sales_trend']['data'] ?? []) !!}
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10B981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: {!! json_encode($data['sales_trend']['days'] ?? []) !!},
            },
            tooltip: {
                y: { formatter: function (val) { return "$" + val.toLocaleString() } }
            }
        };
        var trendChart = new ApexCharts(document.querySelector("#salesTrendChart"), trendOptions);
        trendChart.render();


        // Sales by Type Donut Chart
        var typeLabels = {!! json_encode($data['sales_by_type']['labels'] ?? []) !!};
        var typeData = {!! json_encode($data['sales_by_type']['data'] ?? []) !!};

        if (typeLabels.length === 0) {
            typeLabels = ['No Data'];
            typeData = [1];
        }

        var donutOptions = {
            series: typeData,
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            labels: typeLabels,
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
                                formatter: function (val) { return val + " Sold" }
                            }
                        }
                    }
                }
            }
        };
        var donutChart = new ApexCharts(document.querySelector("#salesByTypeChart"), donutOptions);
        donutChart.render();

    });
</script>
@endsection
