@extends('dsh.master')

@section('title', 'داشبورد مالی')

@section('content')

<!-- Top KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Revenue -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">مجموع عواید (امسال)</div>
                    <div class="kpi-value text-primary">${{ number_format($data['kpis']['revenue'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-primary opacity-50">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">مجموع مصارف (امسال)</div>
                    <div class="kpi-value text-warning">${{ number_format($data['kpis']['expenses'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-warning opacity-50">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">مفاد خالص</div>
                    <div class="kpi-value {{ ($data['kpis']['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($data['kpis']['net_profit'] ?? 0) }}
                    </div>
                </div>
                <div class="kpi-icon {{ ($data['kpis']['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} opacity-50">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Cash Flow Chart -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">جریان نقدینگی (Cash Flow)</h5>
            <div id="cashFlowChart" style="min-height: 350px;"></div>
        </div>
    </div>

    <!-- Expense Breakdown Chart -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">تجزیه مصارف</h5>
            <div id="expenseDonutChart" style="min-height: 350px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cash Flow Chart
        var cashFlowOptions = {
            series: [{
                name: 'ورودی (Cash In)',
                data: {!! json_encode($data['cash_flow']['inflow'] ?? []) !!}
            }, {
                name: 'خروجی (Cash Out)',
                data: {!! json_encode($data['cash_flow']['outflow'] ?? []) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4
                },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: {!! json_encode($data['cash_flow']['months'] ?? []) !!},
            },
            colors: ['#10B981', '#F59E0B'],
            fill: { opacity: 1 },
            tooltip: {
                y: { formatter: function (val) { return "$" + val.toLocaleString() } }
            }
        };
        var cfChart = new ApexCharts(document.querySelector("#cashFlowChart"), cfOptions);
        cfChart.render();

        // Expense Breakdown Donut
        var expKeys = {!! json_encode(array_keys($data['expense_breakdown'] ?? [])) !!};
        var expVals = {!! json_encode(array_values($data['expense_breakdown'] ?? [])) !!};
        
        var donutOptions = {
            series: expVals,
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            labels: expKeys,
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
        var donutChart = new ApexCharts(document.querySelector("#expenseDonutChart"), donutOptions);
        donutChart.render();

        // AR Aging Bar Chart
        var arData = {!! json_encode(array_values($data['aging']['ar'] ?? [])) !!};
        var arOptions = {
            series: [{
                name: 'Receivables',
                data: arData
            }],
            chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            colors: ['#10B981'],
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            dataLabels: { enabled: true, formatter: function(val) { return "$" + val.toLocaleString(); } },
            xaxis: { categories: ['0-30 Days', '31-60 Days', '61-90 Days', '90+ Days'] }
        };
        var arChart = new ApexCharts(document.querySelector("#arAgingChart"), arOptions);
        arChart.render();

        // AP Aging Bar Chart
        var apData = {!! json_encode(array_values($data['aging']['ap'] ?? [])) !!};
        var apOptions = {
            series: [{
                name: 'Payables',
                data: apData
            }],
            chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            colors: ['#ef4444'], // danger red
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            dataLabels: { enabled: true, formatter: function(val) { return "$" + val.toLocaleString(); } },
            xaxis: { categories: ['0-30 Days', '31-60 Days', '61-90 Days', '90+ Days'] }
        };
        var apChart = new ApexCharts(document.querySelector("#apAgingChart"), apOptions);
        apChart.render();
    });
</script>
@endsection
