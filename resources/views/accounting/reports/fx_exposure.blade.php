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
                            <h3 class="font-weight-bold mb-1">گزارش مواجهه با اسعار (FX Exposure)</h3>
                            <p class="text-muted mb-0">تحلیل توزیع نقدینگی و دارایی‌ها بر اساس اسعار مختلف (دالر و افغانی)</p>
                        </div>
                        <div class="col-md-5 text-right">
                            <form action="{{ route('accounting.reports.fx_exposure') }}" method="GET" class="form-inline justify-content-end">
                                <label class="mr-2 font-weight-bold">تا تاریخ:</label>
                                <input type="date" name="date" value="{{ $date }}" class="form-control bg-light border-0 rounded-pill mr-2">
                                <button type="submit" class="btn btn-dark rounded-pill shadow-sm px-4">بروزرسانی</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Cards -->
    <div class="row mb-4 no-print">
        @foreach($report as $row)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; border-right: 10px solid {{ $row->currency_code == 'USD' ? '#1a237e' : '#00c853' }} !important;">
                <div class="row align-items-center">
                    <div class="col-7">
                        <span class="text-muted small font-weight-bold d-block">مجموع موجودی به اسعار ({{ $row->currency_code }})</span>
                        <h2 class="font-weight-bold mt-1">{{ number_format($row->net_balance_original, 2) }}</h2>
                        <span class="badge {{ $row->currency_code == 'USD' ? 'badge-primary' : 'badge-success' }} px-3 py-1">
                            {{ $row->currency_code == 'USD' ? 'دالر امریکایی' : 'افغانی' }}
                        </span>
                    </div>
                    <div class="col-5 text-right border-left">
                        <span class="text-muted small d-block">معادل به دالر (Base)</span>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ number_format($row->net_balance_base, 2) }}</h3>
                        <small class="text-muted">نرخ تخمینی: {{ $row->net_balance_original != 0 ? round($row->net_balance_base / $row->net_balance_original, 4) : '1.00' }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px;">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-5 d-none d-print-block">
                        <h2 class="font-weight-bold">{{ config('company.name') }}</h2>
                        <h4>گزارش تفکیکی اسعار و مواجهه مالی</h4>
                        <p>به تاریخ: {{ $date }}</p>
                    </div>

                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">واحد پولی (Currency)</th>
                                <th class="py-3 text-right px-4">بیلانس به اسعار اصلی (Original)</th>
                                <th class="py-3 text-right px-4">بیلانس به دالر (USD Equivalent)</th>
                                <th class="py-3 text-center px-4">فیصدی از کل (Share %)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalBase = $report->sum('net_balance_base'); @endphp
                            @foreach($report as $row)
                            <tr>
                                <td class="py-3 px-4 font-weight-bold text-dark">{{ $row->currency_code }}</td>
                                <td class="py-3 text-right px-4">{{ number_format($row->net_balance_original, 2) }}</td>
                                <td class="py-3 text-right px-4 font-weight-bold text-primary">{{ number_format($row->net_balance_base, 2) }}</td>
                                <td class="py-3 text-center px-4">
                                    <div class="progress rounded-pill shadow-sm" style="height: 10px; width: 100px; display: inline-flex;">
                                        @php $pct = $totalBase != 0 ? ($row->net_balance_base / $totalBase) * 100 : 0; @endphp
                                        <div class="progress-bar {{ $row->currency_code == 'USD' ? 'bg-primary' : 'bg-success' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="ml-2 small font-weight-bold">{{ round($pct, 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-dark text-white">
                            <tr>
                                <td colspan="2" class="py-3 px-4 f-18">مجموع ارزش مالی (Base USD)</td>
                                <td class="py-3 text-right px-4 f-18 font-weight-bold text-white">{{ number_format($totalBase, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="mt-5 no-print text-right">
                        <button onclick="window.print()" class="btn btn-outline-dark btn-lg px-5 rounded-pill shadow-sm"><i class="feather icon-printer mr-2"></i> چاپ گزارش اسعار</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .f-18 { font-size: 18px; }
    @media print {
        .no-print { display: none !important; }
        .printable-document { border: none !important; box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
