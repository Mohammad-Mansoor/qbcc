@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Header Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="font-weight-bold mb-1">گزارش مقایسوی مفاد و ضرر (Comparative P&L)</h3>
                            <p class="text-muted mb-0">تحلیل عملکرد مالی نسبت به دوره مشابه سال قبل</p>
                        </div>
                        <div class="col-md-5">
                            <form action="{{ route('accounting.reports.comparative_pl') }}" method="GET">
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
                                        <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm"><i class="feather icon-filter"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparative Summary Cards -->
    <div class="row mb-4 no-print">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: linear-gradient(135deg, #00c853, #b9f6ca);">
                <span class="text-dark opacity-75 small font-weight-bold">تغییر در عواید (Revenue Variance)</span>
                <h2 class="text-dark font-weight-bold mt-2 mb-0">
                    {{ number_format($variance['revenue'], 2) }}
                    @if($variance['revenue'] >= 0) <i class="feather icon-trending-up small text-success"></i> @else <i class="feather icon-trending-down small text-danger"></i> @endif
                </h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: linear-gradient(135deg, #d50000, #ff8a80);">
                <span class="text-white opacity-75 small font-weight-bold">تغییر در مصارف (Expense Variance)</span>
                <h2 class="text-white font-weight-bold mt-2 mb-0">{{ number_format($variance['expenses'], 2) }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: linear-gradient(135deg, #1a237e, #3949ab);">
                <span class="text-white opacity-75 small font-weight-bold">تغییر در مفاد خالص (Net Profit Variance)</span>
                <h2 class="text-white font-weight-bold mt-2 mb-0">{{ number_format($variance['profit'], 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Main Report Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px;">
                <div class="card-body p-5">
                    
                    <!-- Print Header -->
                    <div class="text-center mb-5">
                        <h2 class="font-weight-bold">{{ config('company.name') }}</h2>
                        <h4 class="text-muted">گزارش مقایسوی عواید و مصارف</h4>
                        <p class="mb-0">دوره فعلی: {{ $startDate }} الی {{ $endDate }} | دوره قبلی: {{ \Carbon\Carbon::parse($startDate)->subYear()->toDateString() }} الی {{ \Carbon\Carbon::parse($endDate)->subYear()->toDateString() }}</p>
                    </div>

                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">شرح حساب (Description)</th>
                                <th class="py-3 text-right px-4">دوره فعلی (Current)</th>
                                <th class="py-3 text-right px-4">دوره قبلی (Previous)</th>
                                <th class="py-3 text-right px-4">تفاوت (Variance)</th>
                                <th class="py-3 text-center px-4">تغییر %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Revenue Section -->
                            <tr class="bg-lightest font-weight-bold"><td colspan="5" class="py-3 px-4 text-primary f-18">عواید (Revenue)</td></tr>
                            <tr>
                                <td class="py-3 px-4">مجموع عواید عملیاتی</td>
                                <td class="py-3 text-right px-4 font-weight-bold text-success">{{ number_format($current['revenue'], 2) }}</td>
                                <td class="py-3 text-right px-4">{{ number_format($previous['revenue'], 2) }}</td>
                                <td class="py-3 text-right px-4 font-weight-bold {{ $variance['revenue'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($variance['revenue'], 2) }}
                                </td>
                                <td class="py-3 text-center px-4">
                                    @php $revPct = $previous['revenue'] != 0 ? ($variance['revenue'] / $previous['revenue']) * 100 : 0; @endphp
                                    <span class="badge {{ $revPct >= 0 ? 'badge-success' : 'badge-danger' }}">{{ round($revPct, 1) }}%</span>
                                </td>
                            </tr>

                            <!-- Expenses Section -->
                            <tr class="bg-lightest font-weight-bold"><td colspan="5" class="py-3 px-4 text-danger f-18">مصارف (Expenses)</td></tr>
                            <tr>
                                <td class="py-3 px-4">مجموع مصارف عملیاتی و اداری</td>
                                <td class="py-3 text-right px-4 font-weight-bold text-danger">{{ number_format($current['expenses'], 2) }}</td>
                                <td class="py-3 text-right px-4">{{ number_format($previous['expenses'], 2) }}</td>
                                <td class="py-3 text-right px-4 font-weight-bold {{ $variance['expenses'] <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($variance['expenses'], 2) }}
                                </td>
                                <td class="py-3 text-center px-4">
                                    @php $expPct = $previous['expenses'] != 0 ? ($variance['expenses'] / $previous['expenses']) * 100 : 0; @endphp
                                    <span class="badge {{ $expPct <= 0 ? 'badge-success' : 'badge-danger' }}">{{ round($expPct, 1) }}%</span>
                                </td>
                            </tr>

                            <!-- Net Profit Section -->
                            <tr class="bg-dark text-white font-weight-bold">
                                <td class="py-4 px-4 f-20">مفاد خالص (Net Profit)</td>
                                <td class="py-4 text-right px-4 f-20">{{ number_format($current['profit'], 2) }}</td>
                                <td class="py-4 text-right px-4 f-20 text-white-50">{{ number_format($previous['profit'], 2) }}</td>
                                <td class="py-4 text-right px-4 f-20 {{ $variance['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($variance['profit'], 2) }}
                                </td>
                                <td class="py-4 text-center px-4">
                                    @php $profitPct = $previous['profit'] != 0 ? ($variance['profit'] / abs($previous['profit'])) * 100 : 0; @endphp
                                    <span class="badge badge-light text-dark">{{ round($profitPct, 1) }}%</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-5 pt-5 d-none d-print-block">
                        <div class="row text-center mt-5">
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">ترتیب کننده</div></div>
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">مدیر مالی</div></div>
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">تایید نهایی</div></div>
                        </div>
                    </div>

                    <div class="text-right no-print mt-4">
                        <button onclick="window.print()" class="btn btn-dark btn-lg px-5 rounded-pill shadow"><i class="feather icon-printer mr-2"></i> چاپ گزارش</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-lightest { background: #f8f9fa; }
    .f-18 { font-size: 18px; }
    .f-20 { font-size: 20px; }
    @media print {
        .no-print { display: none !important; }
        .printable-document { border: none !important; box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
