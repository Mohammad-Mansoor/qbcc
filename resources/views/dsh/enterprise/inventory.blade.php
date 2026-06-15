@extends('dsh.master')

@section('title', 'داشبورد گدام (موجودی)')

@section('content')

<!-- Top KPIs Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Carpet Value -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">ارزش قالین ها</div>
                    <div class="kpi-value">${{ number_format(($data['values']['carpet'] ?? 0) + ($data['values']['yarn'] ?? 0) + ($data['values']['dye'] ?? 0)) }}</div>
                </div>
                <div class="kpi-icon text-primary">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Yarn Value -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">ارزش تار</div>
                    <div class="kpi-value">${{ number_format($data['values']['yarn'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-warning">
                    <i class="fa-solid fa-lines-leaning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dye Value -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-title">ارزش رنگ</div>
                    <div class="kpi-value">${{ number_format($data['values']['dye'] ?? 0) }}</div>
                </div>
                <div class="kpi-icon text-danger">
                    <i class="fa-solid fa-fill-drip"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-gap" style="margin: 10px;">
    <!-- Carpet By Type Chart -->
    <div class="col-md-8 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">موجودی قالین نظر به نوعیت</h5>
            <div id="carpetByTypeChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Dead Stock Visualizer -->
    <div class="col-md-4 col-gap">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">اجناس راکد در گدام (Dead Stock)</h5>
            
            <div class="mb-3">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>30 Days Without Movement</span>
                    <span>{{ $data['dead_stock']['30_days'] ?? 0 }} items</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: 20%"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>60 Days Without Movement</span>
                    <span>{{ $data['dead_stock']['60_days'] ?? 0 }} items</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" style="background-color: #fd7e14; width: 40%"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>90 Days Without Movement</span>
                    <span>{{ $data['dead_stock']['90_days'] ?? 0 }} items</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: 60%"></div>
                </div>
            </div>

            <div class="mb-0">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>180+ Days Without Movement</span>
                    <span class="text-danger fw-bold">{{ $data['dead_stock']['180_days'] ?? 0 }} items</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-dark" style="width: 100%"></div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Warehouse Summaries -->
<div class="row row-gap" style="margin: 10px;">
    <div class="col-12 col-gap">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-warehouse me-2"></i> تحلیل گدام ها</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>نام گدام</th>
                            <th>نوعیت (Type)</th>
                            <th>موجودی قالین</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['warehouses'] as $w)
                        <tr>
                            <td class="fw-bold text-dark">{{ $w['name'] }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $w['type'] }}</span></td>
                            <td><span class="badge bg-primary rounded-pill">{{ $w['items_count'] }} تخته</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">معلومات گدام موجود نیست.</td>
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
        
        var typeLabels = {!! json_encode($data['carpet_by_type']['labels'] ?? []) !!};
        var typeData = {!! json_encode($data['carpet_by_type']['data'] ?? []) !!};

        if(typeLabels.length === 0) {
            typeLabels = ['Type A', 'Type B', 'Type C'];
            typeData = [0, 0, 0];
        }

        var barOptions = {
            series: [{
                name: 'Carpets in Stock',
                data: typeData
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
                    columnWidth: '40%',
                    distributed: true
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            colors: ['#0A192F', '#10B981', '#F59E0B', '#3b82f6', '#8b5cf6', '#ec4899'],
            xaxis: {
                categories: typeLabels,
            }
        };
        var barChart = new ApexCharts(document.querySelector("#carpetByTypeChart"), barOptions);
        barChart.render();
    });
</script>
@endsection
