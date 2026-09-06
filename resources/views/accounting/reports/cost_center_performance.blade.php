@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Header -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="font-weight-bold mb-1">گزارش عملکرد مراکز هزینه (Cost Center Performance)</h3>
                            <p class="text-muted mb-0">تحلیل سودآوری و هزینه‌ها به تفکیک بخش‌های مختلف شرکت</p>
                        </div>
                        <div class="col-md-5">
                            <form action="{{ route('accounting.reports.cost_center_performance') }}" method="GET">
                                <div class="row no-gutters align-items-end justify-content-end">
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">تا تاریخ:</label>
                                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <button type="submit" class="btn btn-purple btn-block rounded-pill shadow-sm"><i class="feather icon-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Cards -->
    <div class="row mb-4 no-print">
        @foreach($report as $row)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-purple border-0 py-3">
                    <h5 class="text-white mb-0 text-center font-weight-bold">{{ $row->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">عواید بخش:</span>
                        <span class="font-weight-bold text-success">{{ number_format($row->revenue, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">مصارف بخش:</span>
                        <span class="font-weight-bold text-danger">{{ number_format($row->expenses, 2) }}</span>
                    </div>
                    <div class="text-center pt-2">
                        <span class="text-muted small d-block mb-1">مفاد خالص بخش (Net Profit)</span>
                        <h3 class="font-weight-bold {{ $row->profit >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ number_format($row->profit, 2) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Detailed Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px;">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-5 d-none d-print-block">
                        <h2 class="font-weight-bold">{{ config('company.name') }}</h2>
                        <h4>گزارش عملکرد مالی مراکز هزینه و دیپارتمنت‌ها</h4>
                        <p>دوره: {{ $startDate }} الی {{ $endDate }}</p>
                    </div>

                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">نام مرکز هزینه (Cost Center Name)</th>
                                <th class="py-3 text-right px-4 text-success">عواید (Revenue)</th>
                                <th class="py-3 text-right px-4 text-danger">مصارف (Expenses)</th>
                                <th class="py-3 text-right px-4">مفاد خالص (Profit)</th>
                                <th class="py-3 text-center px-4">کارایی (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report as $row)
                            <tr>
                                <td class="py-3 px-4 font-weight-bold">{{ $row->name }}</td>
                                <td class="py-3 text-right px-4 text-success">{{ number_format($row->revenue, 2) }}</td>
                                <td class="py-3 text-right px-4 text-danger">{{ number_format($row->expenses, 2) }}</td>
                                <td class="py-3 text-right px-4 font-weight-bold {{ $row->profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                    {{ number_format($row->profit, 2) }}
                                </td>
                                <td class="py-3 text-center px-4">
                                    @php $margin = $row->revenue != 0 ? ($row->profit / $row->revenue) * 100 : 0; @endphp
                                    <div class="progress rounded-pill" style="height: 8px;">
                                        <div class="progress-bar bg-purple" style="width: {{ min(100, max(0, $margin)) }}%"></div>
                                    </div>
                                    <span class="small font-weight-bold">{{ round($margin, 1) }}% Margin</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-right no-print mt-5">
                        <button onclick="window.print()" class="btn btn-purple btn-lg px-5 rounded-pill shadow-lg text-white"><i class="feather icon-printer mr-2"></i> چاپ عملکرد مراکز هزینه</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-purple { background: #6f42c1; border-color: #6f42c1; color: white; }
    .bg-purple { background: #6f42c1; }
    @media print {
        .no-print { display: none !important; }
        .printable-document { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection
