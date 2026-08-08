@extends('dsh.master')
@section('title', 'لیست اجرای معاشات')
@section('content')

<div class="container-fluid px-4 py-4" id="payroll-index">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h3 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-money text-success mr-2"></i> اجرای معاشات ماهوار
            </h3>
            <p class="text-muted small mb-0 mt-1">ثبت، مرور و چاپ راپور معاشات کارمندان — هر ردیف یک دوره پرداخت مستقل است</p>
        </div>
        <div class="col-md-5 text-left">
            @can('run_payroll')
            <a href="{{ route('payroll.create') }}" class="btn btn-success shadow-sm px-4 font-weight-bold">
                <i class="fa fa-plus mr-2"></i> اجرای معاش جدید
            </a>
            @endcan
            @can('view_employees')
            <a href="{{ url('/dashboard/office-employee') }}" class="btn btn-light shadow-sm px-3 ml-2">
                <i class="fa fa-users mr-1"></i> لیست کارمندان
            </a>
            @endcan
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if(session('status'))
        <div class="alert alert-success alert-dismissible border-0 rounded-lg shadow-sm mb-4">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-check-circle mr-2"></i> {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible border-0 rounded-lg shadow-sm mb-4">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-times-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ══════════════ KPI CARDS ══════════════ --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg,#0f172a,#1e3a5f);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 small font-weight-bold" style="color:#94a3b8; letter-spacing:.5px;">دوره‌های فعال</p>
                            <h2 class="mb-0 font-weight-bold text-white">{{ number_format($totalRuns) }}</h2>
                            <small style="color:#64748b;">تعداد اجرا (Active Runs)</small>
                        </div>
                        <div style="background:rgba(99,102,241,.2);border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-calendar-check-o" style="font-size:1.6rem;color:#818cf8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg,#052e16,#14532d);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 small font-weight-bold" style="color:#86efac; letter-spacing:.5px;">معاشات پرداختی (USD)</p>
                            <h2 class="mb-0 font-weight-bold text-white" dir="ltr">${{ number_format($totalPaidUSD, 2) }}</h2>
                            <small style="color:#4ade80;">مجموع خالص دوره‌های فعال</small>
                        </div>
                        <div style="background:rgba(16,185,129,.2);border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-usd" style="font-size:1.6rem;color:#34d399;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg,#450a0a,#7f1d1d);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 small font-weight-bold" style="color:#fca5a5; letter-spacing:.5px;">معاشات لغو شده (USD)</p>
                            <h2 class="mb-0 font-weight-bold text-white" dir="ltr">${{ number_format($cancelledPaidUSD, 2) }}</h2>
                            <small style="color:#f87171;">{{ $cancelledRunsCount }} دوره لغو شده (Reversed)</small>
                        </div>
                        <div style="background:rgba(239,68,68,.2);border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-times-circle" style="font-size:1.6rem;color:#f87171;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg,#3b0764,#581c87);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 small font-weight-bold" style="color:#e9d5ff; letter-spacing:.5px;">ماه جاری: {{ $currentMonth }}</p>
                            @if($thisMonthRun)
                                <h5 class="mb-0 font-weight-bold text-white"><i class="fa fa-check-circle text-green mr-1"></i> ثبت شده</h5>
                                <small style="color:#c4b5fd;">${{ number_format($thisMonthRun->total_amount, 2) }} — {{ $thisMonthRun->run_date }}</small>
                            @else
                                <h5 class="mb-0 font-weight-bold" style="color:#fbbf24;"><i class="fa fa-exclamation-circle mr-1"></i> هنوز اجرا نشده</h5>
                                <small style="color:#c4b5fd;">معاش این ماه باقی است</small>
                            @endif
                        </div>
                        <div style="background:rgba(168,85,247,.2);border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-clock-o" style="font-size:1.6rem;color:#a78bfa;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ TABLE ══════════════ --}}
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
        <div class="card-header border-0 py-3 px-4" style="background:#f8fafc;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa fa-history text-primary mr-2"></i> تاریخچه اجرای معاشات
                    </h5>
                </div>
                <div class="col-md-6 text-left">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-print mr-1"></i> چاپ
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-right">
                <thead style="background:#1e293b; color:#e2e8f0;">
                    <tr>
                        <th class="border-0 px-4 py-3">#</th>
                        <th class="border-0 py-3">ماه / سال</th>
                        <th class="border-0 py-3">تاریخ اجرا</th>
                        <th class="border-0 py-3 text-center">مجموع معاش (USD)</th>
                        <th class="border-0 py-3 text-center">حالت</th>
                        <th class="border-0 px-4 py-3 text-left hideOnPrint">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)
                    <tr class="border-bottom">
                        <td class="px-4 py-3 text-muted small">{{ $run->id }}</td>
                        <td class="py-3 font-weight-bold text-dark">
                            <span style="background:#ede9fe;color:#7c3aed;padding:4px 12px;border-radius:20px;font-size:.82rem;">
                                <i class="fa fa-calendar mr-1"></i> {{ $run->month_year }}
                            </span>
                        </td>
                        <td class="py-3 text-muted small">{{ \Carbon\Carbon::parse($run->run_date)->format('d M Y') }}</td>
                        <td class="py-3 text-center">
                            <span class="font-weight-bold text-success" dir="ltr" style="font-size:1rem;">
                                ${{ number_format($run->total_amount, 2) }}
                            </span>
                        </td>
                        <td class="py-3 text-center">
                            @if(($run->status ?? 'posted') === 'cancelled')
                                <span class="badge badge-danger px-3 py-2" style="font-size:.78rem;">
                                    <i class="fa fa-times mr-1"></i> لغو شده (Cancelled)
                                </span>
                            @else
                                <span class="badge badge-success px-3 py-2" style="font-size:.78rem;">
                                    <i class="fa fa-check mr-1"></i> ثبت شده (Posted)
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-left hideOnPrint" style="white-space:nowrap;">
                            <a href="{{ route('payroll.show', $run->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="fa fa-eye mr-1"></i> جزییات
                            </a>

                            @if(($run->status ?? 'posted') !== 'cancelled')
                                @can('edit_payroll')
                                <a href="{{ route('payroll.edit', $run->id) }}" class="btn btn-sm btn-warning text-white ml-1">
                                    <i class="fa fa-pencil mr-1"></i> ویرایش
                                </a>
                                @endcan

                                @can('delete_payroll')
                                <form action="{{ route('payroll.cancel', $run->id) }}" method="POST" class="d-inline-block ml-1" onsubmit="return confirm('آیا مطمین هستید که میخواهید این دوره معاشاتی را لغو کنید؟ سند معکوس در دفتر کل و صورت حساب کارمندان درج خواهد شد.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-times mr-1"></i> لغو
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa fa-inbox" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                            هیچ معاشی هنوز اجرا نشده است.
                            <br>
                            @can('run_payroll')
                            <a href="{{ route('payroll.create') }}" class="btn btn-sm btn-success mt-3">
                                <i class="fa fa-plus mr-1"></i> اجرای اولین معاش
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $runs->links() }}
        </div>
    </div>

</div>

<style>
    .rounded-lg { border-radius: 1rem !important; }
    @media print {
        .hideOnPrint { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>
@endsection
