@extends('dsh.master')
@section('title', 'جزییات معاش — ' . $run->month_year)
@section('content')

<div class="container-fluid px-4 py-4" id="payroll-detail">

    {{-- Header --}}
    <div class="row mb-4 align-items-center hideOnPrint">
        <div class="col-md-7">
            <h3 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-file-text-o text-primary mr-2"></i>
                راپور معاش — {{ $run->month_year }}
            </h3>
            <p class="text-muted small mb-0 mt-1">تاریخ اجرا: {{ \Carbon\Carbon::parse($run->run_date)->format('d M Y') }}</p>
        </div>
        <div class="col-md-5 text-left">
            @if(($run->status ?? 'posted') !== 'cancelled')
                @can('edit_payroll')
                <a href="{{ route('payroll.edit', $run->id) }}" class="btn btn-warning text-white shadow-sm px-3 mr-1">
                    <i class="fa fa-pencil mr-1"></i> ویرایش
                </a>
                @endcan
                @can('delete_payroll')
                <form action="{{ route('payroll.cancel', $run->id) }}" method="POST" class="d-inline-block mr-1" onsubmit="return confirm('آیا مطمین هستید که میخواهید این دوره معاشاتی را لغو کنید؟ سند معکوس در دفتر کل و صورت حساب کارمندان درج خواهد شد.');">
                    @csrf
                    <button type="submit" class="btn btn-danger shadow-sm px-3">
                        <i class="fa fa-times mr-1"></i> لغو
                    </button>
                </form>
                @endcan
            @endif
            <button onclick="window.print()" class="btn btn-outline-secondary shadow-sm px-3">
                <i class="fa fa-print mr-1"></i> چاپ راپور
            </button>
            <a href="{{ route('payroll.index') }}" class="btn btn-light shadow-sm px-3 ml-2">
                <i class="fa fa-arrow-left mr-1"></i> بازگشت
            </a>
        </div>
    </div>

    {{-- ══ Summary KPI Cards ══ --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg text-center py-3">
                <div class="text-muted small text-uppercase font-weight-bold mb-1">تعداد کارمندان</div>
                <div class="h2 font-weight-bold text-dark mb-0">{{ number_format($totals->headcount ?? 0) }}</div>
                <div class="text-muted tiny">نفر</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg text-center py-3">
                <div class="text-muted small text-uppercase font-weight-bold mb-1">مجموع معاش پایه (USD)</div>
                <div class="h4 font-weight-bold text-primary mb-0" dir="ltr">${{ number_format($totals->total_base_usd ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg text-center py-3">
                <div class="text-muted small text-uppercase font-weight-bold mb-1">مجموع کسرات</div>
                <div class="h4 font-weight-bold text-danger mb-0" dir="ltr">-${{ number_format($totals->total_deductions ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg text-center py-3" style="background:linear-gradient(135deg,#052e16,#14532d);">
                <div class="small font-weight-bold mb-1" style="color:#86efac; letter-spacing:.5px;">خالص پرداختی (USD)</div>
                <div class="h3 font-weight-bold text-white mb-0" dir="ltr">${{ number_format($totals->total_net_usd ?? $run->total_amount, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- ══ Table ══ --}}
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
        <div class="card-header border-0 py-3 px-4" style="background:#f8fafc;">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-table text-success mr-2"></i> جدول تفصیل معاشات — {{ $run->month_year }}
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-right" id="payroll-detail-table">
                <thead style="background:#1e293b; color:#e2e8f0;">
                    <tr>
                        <th class="border-0 px-4 py-3">#</th>
                        <th class="border-0 py-3">نام کارمند</th>
                        <th class="border-0 py-3">وظیفه / دیپارتمنت</th>
                        <th class="border-0 py-3 text-center">معاش پایه</th>
                        <th class="border-0 py-3 text-center">ارز / نرخ</th>
                        <th class="border-0 py-3 text-center text-success">پایه (USD)</th>
                        <th class="border-0 py-3 text-center">بونس</th>
                        <th class="border-0 py-3 text-center">کسرات</th>
                        <th class="border-0 py-3 text-center" style="color:#4ade80;">خالص (USD)</th>
                        <th class="border-0 px-4 py-3 text-left hideOnPrint">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="border-bottom">
                        <td class="px-4 py-3 text-muted small">{{ $loop->iteration }}</td>
                        <td class="py-3 font-weight-bold text-dark">{{ $item->name }}</td>
                        <td class="py-3 text-muted small">
                            {{ $item->job_title }}
                            @if($item->department)
                                <br><span class="badge badge-light">{{ $item->department }}</span>
                            @endif
                        </td>

                        {{-- Base Salary in contract currency --}}
                        <td class="py-3 text-center font-weight-bold">
                            {{ number_format($item->base_salary, 2) }}
                        </td>

                        {{-- Currency / Rate --}}
                        <td class="py-3 text-center">
                            <span class="badge badge-info px-2">{{ $item->currency_code ?? 'USD' }}</span>
                            <br>
                            <small class="text-muted" dir="ltr" style="font-size:.72rem;">
                                × {{ number_format($item->exchange_rate ?? 1, 4) }}
                            </small>
                        </td>

                        {{-- Base USD --}}
                        <td class="py-3 text-center">
                            <span style="color:#059669; font-weight:700;" dir="ltr">
                                ${{ number_format($item->base_salary_usd ?? $item->base_salary, 2) }}
                            </span>
                        </td>

                        {{-- Bonus --}}
                        <td class="py-3 text-center text-success font-weight-bold">
                            @if($item->bonus > 0)
                                +{{ number_format($item->bonus, 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Deductions --}}
                        <td class="py-3 text-center text-danger font-weight-bold">
                            @if($item->deductions > 0)
                                -{{ number_format($item->deductions, 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Net USD --}}
                        <td class="py-3 text-center">
                            <div style="background:#ecfdf5;padding:5px 12px;border-radius:8px;border:1px solid #a7f3d0;display:inline-block;" dir="ltr">
                                <strong style="color:#065f46;">${{ number_format($item->net_salary_usd ?? $item->net_salary, 2) }}</strong>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-left hideOnPrint" style="white-space:nowrap;">
                            @can('view_payroll_slip')
                            <a href="{{ route('payroll.slip', [$run->id, $item->id]) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-print mr-1"></i> سلیپ معاش
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">هیچ کارمندی در این اجرا ثبت نشده است.</td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- Totals Footer --}}
                <tfoot style="background:#f1f5f9; font-weight:700; border-top: 2px solid #e2e8f0;">
                    <tr>
                        <td colspan="5" class="py-3 px-4 text-right text-dark">مجموع ({{ $totals->headcount ?? 0 }} نفر):</td>
                        <td class="py-3 text-center" style="color:#059669;">${{ number_format($totals->total_base_usd ?? 0, 2) }}</td>
                        <td class="py-3 text-center text-success">+{{ number_format($totals->total_bonus ?? 0, 2) }}</td>
                        <td class="py-3 text-center text-danger">-{{ number_format($totals->total_deductions ?? 0, 2) }}</td>
                        <td class="py-3 text-center">
                            <div style="background:#ecfdf5;padding:5px 14px;border-radius:8px;border:1px solid #6ee7b7;display:inline-block;" dir="ltr">
                                <strong style="color:#065f46; font-size:1rem;">${{ number_format($totals->total_net_usd ?? $run->total_amount, 2) }}</strong>
                            </div>
                        </td>
                        <td class="hideOnPrint"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3 px-4 hideOnPrint">
            {{ $items->links() }}
        </div>
    </div>

    {{-- GL Notice --}}
    <div class="alert alert-light border mt-4 py-2" style="font-size:.82rem; color:#555;">
        <i class="fa fa-university text-warning mr-1"></i>
        این اجرای معاش به صورت خودکار در دفتر کل با الگوی
        <code>DR Salary Expense / CR Salary Payable</code> ثبت شده است.
        مرجع GL: <strong>PAY-{{ $run->month_year }}</strong>
    </div>

</div>

<style>
    .tiny { font-size: 10px; }
    .rounded-lg { border-radius: 1rem !important; }
    @media print {
        .hideOnPrint { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        body { background: white !important; }
    }
</style>
@endsection
