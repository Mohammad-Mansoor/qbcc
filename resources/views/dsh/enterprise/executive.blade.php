@extends('dsh.master')

@section('title', 'داشبورد اجرایی')

@section('content')
<!-- FontAwesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --navy-blue: #0A192F;
        --emerald-green: #10B981;
        --gold-accent: #F59E0B;
    }
    /* Glassmorphism Classes for Dashboard */
    .glass-card {
        background: #ffffff; /* Fallback for missing glass bg */
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    .kpi-title { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; font-family: Tahoma, Arial, sans-serif; }
    .kpi-value { font-size: 1.8rem; font-weight: 700; color: #0A192F; font-family: Tahoma, Arial, sans-serif; }
    .kpi-icon { font-size: 2.5rem; opacity: 0.8; }
    .pipeline-step { flex: 1; }
    .pipeline-divider { align-self: center; padding: 0 10px; }
    .row-gap { margin-bottom: 20px; }
    .col-gap { padding-bottom: 20px; }
</style>

<!-- Top KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Today Production -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">تولیدات امروز</div>
                    <div class="kpi-value">{{ $kpis['today_production'] ?? 0 }} <small class="fs-6 text-muted">Carpets</small></div>
                </div>
                <div class="kpi-icon">
                    <i class="fa-solid fa-layer-group"></i>
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
                    <div class="kpi-value">${{ number_format($kpis['monthly_revenue'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-success">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Value -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">ارزش موجودی</div>
                    <div class="kpi-value">${{ number_format($kpis['total_inventory_value'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-warning">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash & Bank -->
    <div class="col-md-3 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">موجودی نقد و بانک</div>
                    <div class="kpi-value">${{ number_format($kpis['cash_and_bank'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-info">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-gap" style="margin: 10px;">
    <!-- Accounts Receivable -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">رسیدات (طلبات)</div>
                    <div class="kpi-value text-success">${{ number_format($kpis['accounts_receivable'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-success">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Payable -->
    <div class="col-md-6 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">پرداختنی‌ها (دیونات)</div>
                    <div class="kpi-value text-danger">${{ number_format($kpis['accounts_payable'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-danger">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Production Pipeline -->
<div class="row row-gap" style="margin: 10px;">
    <div class="col-12 col-gap">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4 text-dark">وضعیت تولیدات جاری</h5>
            <div class="d-flex justify-content-between text-center mt-4">
                <div class="pipeline-step">
                    <h3 class="text-secondary fw-bold">{{ $pipeline['raw_carpet'] ?? 0 }}</h3>
                    <p class="text-muted small text-uppercase">خام</p>
                </div>
                <div class="pipeline-divider"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                <div class="pipeline-step">
                    <h3 class="text-primary fw-bold">{{ $pipeline['kachayee'] ?? 0 }}</h3>
                    <p class="text-muted small text-uppercase">کچایی</p>
                </div>
                <div class="pipeline-divider"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                <div class="pipeline-step">
                    <h3 class="text-info fw-bold">{{ $pipeline['washing'] ?? 0 }}</h3>
                    <p class="text-muted small text-uppercase">شست</p>
                </div>
                <div class="pipeline-divider"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                <div class="pipeline-step">
                    <h3 class="text-warning fw-bold">{{ $pipeline['tayaari'] ?? 0 }}</h3>
                    <p class="text-muted small text-uppercase">تیاری</p>
                </div>
                <div class="pipeline-divider"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                <div class="pipeline-step">
                    <h3 class="text-success fw-bold">{{ $pipeline['ready_for_sale'] ?? 0 }}</h3>
                    <p class="text-muted small text-uppercase">آماده فروش</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Revenue vs Expense Chart -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">عواید در مقابل مصارف (۷ ماه گذشته)</h5>
            <div id="revenueExpenseChart" style="min-height: 300px;"></div>
        </div>
    </div>
    <!-- Inventory Distribution -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">توزیع موجودی گدام</h5>
            <div id="inventoryDonutChart" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Real Data for Revenue vs Expense Area Chart
        var revExpOptions = {
            series: [{
                name: 'عواید',
                data: {!! json_encode($revenueExpenseChart['revenue'] ?? []) !!}
            }, {
                name: 'مصارف',
                data: {!! json_encode($revenueExpenseChart['expenses'] ?? []) !!}
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10B981', '#F59E0B'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' },
            xaxis: {
                categories: {!! json_encode($revenueExpenseChart['months'] ?? []) !!}
            },
        };
        var areaChart = new ApexCharts(document.querySelector("#revenueExpenseChart"), revExpOptions);
        areaChart.render();

        // Real Data for Inventory Donut Chart
        var invOptions = {
            series: [
                {{ $inventoryDonut['carpet'] ?? 0 }},
                {{ $inventoryDonut['yarn'] ?? 0 }},
                {{ $inventoryDonut['dye'] ?? 0 }}
            ],
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            labels: ['قالین', 'تار', 'رنگ'],
            colors: ['#0A192F', '#10B981', '#F59E0B'],
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
                                formatter: function (val) {
                                    return "$" + parseInt(val).toLocaleString()
                                }
                            }
                        }
                    }
                }
            }
        };
        var donutChart = new ApexCharts(document.querySelector("#inventoryDonutChart"), donutOptions);
        donutChart.render();
    });
</script>
@endsection
