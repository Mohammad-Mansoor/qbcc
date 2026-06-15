@extends('dsh.master')

@section('title', 'داشبورد تولیدات')

@section('content')

<!-- Top KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- WIP -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">در حال کار (WIP)</div>
                    <div class="kpi-value text-primary">{{ $data['wip_total'] ?? 0 }} <small class="fs-6 text-muted">تعداد</small></div>
                </div>
                <div class="kpi-icon text-primary">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Today -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">تکمیل شده (امروز)</div>
                    <div class="kpi-value text-success">{{ $data['team_performance']['tayaari'] ?? 0 }}</div>
                </div>
                <div class="kpi-icon text-success">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Efficiency -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">موثریت</div>
                    <div class="kpi-value text-info">{{ $data['efficiency'] ?? 100 }}%</div>
                </div>
                <div class="kpi-icon text-info">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Output -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">سفارشات معطل</div>
                    <div class="kpi-value text-danger">{{ $data['delayed_orders'] ?? 0 }}</div>
                </div>
                <div class="kpi-icon text-danger">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Production Output Bar Chart -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">خروجی تولید (۷ روز گذشته)</h5>
            <div id="dailyOutputChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Efficiency Gauge -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
            <h5 class="fw-bold mb-2 text-dark text-start w-100">موثریت تولید</h5>
            <div id="efficiencyGauge" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daily Output Bar Chart
        var outputOptions = {
            series: [{
                name: 'تولیدات (متر مربع)',
                data: {!! json_encode($data['daily_output']['data'] ?? []) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '50%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 2
            },
            colors: ['#0A192F'],
            xaxis: {
                categories: {!! json_encode($data['daily_output']['days']) !!},
            }
        };
        var barChart = new ApexCharts(document.querySelector("#dailyOutputChart"), barOptions);
        barChart.render();

        // Efficiency Gauge Chart
        var gaugeOptions = {
            series: [{{ $data['efficiency'] ?? 100 }}],
            chart: {
                type: 'radialBar',
                height: 350,
                offsetY: -20,
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: {
                        margin: 15,
                        size: '65%',
                        image: undefined,
                        imageOffsetX: 0,
                        imageOffsetY: 0,
                        position: 'front',
                    },
                    track: {
                        background: '#e7e7e7',
                        strokeWidth: '100%',
                        margin: 0, // margin is in pixels
                        dropShadow: {
                            enabled: true,
                            top: 0,
                            left: 0,
                            blur: 3,
                            opacity: 0.5
                        }
                    },
                    dataLabels: {
                        show: true,
                        name: {
                            offsetY: -10,
                            show: true,
                            color: '#888',
                            fontSize: '17px'
                        },
                        value: {
                            formatter: function(val) {
                                return parseInt(val) + "%";
                            },
                            color: '#111',
                            fontSize: '36px',
                            show: true,
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#10B981'],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                }
            },
            stroke: {
                lineCap: 'round'
            },
            labels: ['موثریت'],
        };
        var gaugeChart = new ApexCharts(document.querySelector("#efficiencyGauge"), gaugeOptions);
        gaugeChart.render();
    });
</script>
@endsection
