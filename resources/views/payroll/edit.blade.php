@extends('dsh.master')
@section('title', 'ویرایش اجرای معاشات')
@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h3 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-pencil text-warning mr-2"></i> ویرایش اجرای معاش ماهوار ({{ $run->month_year }})
            </h3>
            <p class="text-muted small mb-0 mt-1">اصلاح مقادیر معاش، پاداش و کسرها — با ثبت مجدد، سند قبلی معکوس و سند جدید ثبت می‌گردد</p>
        </div>
        <div class="col-md-5 text-left">
            <a href="{{ route('payroll.index') }}" class="btn btn-light shadow-sm px-4">
                <i class="fa fa-arrow-left mr-1"></i> بازگشت به لیست
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-lg shadow-sm mb-4">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <ul class="mb-0 mt-1 pr-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payroll.update', $run->id) }}" method="POST" id="payroll-edit-form">
        @csrf
        @method('PUT')

        {{-- ── Run Info Card ── --}}
        <div class="card border-0 shadow-sm rounded-lg mb-4">
            <div class="card-header border-0 py-3 px-4" style="background:#f8fafc;">
                <h5 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-calendar text-primary mr-2"></i> مشخصات دوره معاشاتی
                </h5>
            </div>
            <div class="card-body px-4 py-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted small text-uppercase">ماه و سال (Month/Year)</label>
                            <input type="text" class="form-control bg-light" value="{{ $run->month_year }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted small text-uppercase">تاریخ اجرا (Run Date)</label>
                            <input type="date" name="run_date" class="form-control"
                                   value="{{ old('run_date', $run->run_date) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted small text-uppercase">مجموع پرداختی فعلی (USD)</label>
                            <div class="h4 font-weight-bold text-success mt-1" dir="ltr" id="header-grand-total">
                                ${{ number_format($run->total_amount, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Employee List Table Card ── --}}
        <div class="card border-0 shadow-lg rounded-lg mb-4 overflow-hidden">
            <div class="card-header border-0 py-3 px-4" style="background:#1e293b; color:#fff;">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fa fa-users text-info mr-2"></i> لیست کارمندان در این دوره
                    </h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right" id="employees-table">
                    <thead style="background:#0f172a; color:#cbd5e1;">
                        <tr>
                            <th class="border-0 px-4 py-3" style="width:40px;">
                                <input type="checkbox" id="select-all" checked>
                            </th>
                            <th class="border-0 py-3">کارمند</th>
                            <th class="border-0 py-3">وظیفه</th>
                            <th class="border-0 py-3">معاش پایه</th>
                            <th class="border-0 py-3">پاداش (+)</th>
                            <th class="border-0 py-3">کسری (-)</th>
                            <th class="border-0 py-3">خالص پرداختی</th>
                            <th class="border-0 py-3">ارز</th>
                            <th class="border-0 py-3">معادل (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="emp-row" data-emp-id="{{ $item->employee_id }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="employees[{{ $item->employee_id }}][include]" value="1"
                                       class="emp-check" checked>
                                <input type="hidden" name="employees[{{ $item->employee_id }}][currency_id]" value="{{ $item->currency_id }}">
                                <input type="hidden" name="employees[{{ $item->employee_id }}][currency_code]" value="{{ $item->currency_code }}">
                                <input type="hidden" name="employees[{{ $item->employee_id }}][exchange_rate]" value="{{ $item->exchange_rate }}" class="emp-rate">
                            </td>
                            <td class="py-3 font-weight-bold text-dark">
                                {{ $item->name }}
                                <div class="small text-muted">{{ $item->department ?? 'بدون بخش' }}</div>
                            </td>
                            <td class="py-3 text-muted small">{{ $item->job_title }}</td>
                            <td class="py-3" style="width:140px;">
                                <input type="number" step="any" name="employees[{{ $item->employee_id }}][base_salary]"
                                       class="form-control form-control-sm emp-base text-left" dir="ltr"
                                       value="{{ $item->base_salary }}" required>
                            </td>
                            <td class="py-3" style="width:120px;">
                                <input type="number" step="any" name="employees[{{ $item->employee_id }}][bonus]"
                                       class="form-control form-control-sm emp-bonus text-left" dir="ltr"
                                       value="{{ $item->bonus }}">
                            </td>
                            <td class="py-3" style="width:120px;">
                                <input type="number" step="any" name="employees[{{ $item->employee_id }}][deductions]"
                                       class="form-control form-control-sm emp-deductions text-left" dir="ltr"
                                       value="{{ $item->deductions }}">
                            </td>
                            <td class="py-3 font-weight-bold text-primary">
                                <span class="emp-net-display">{{ number_format($item->net_salary, 2) }}</span>
                            </td>
                            <td class="py-3 small">
                                <span class="badge badge-light border">{{ $item->currency_code }}</span>
                            </td>
                            <td class="py-3 font-weight-bold text-success" dir="ltr">
                                $<span class="emp-usd-display">{{ number_format($item->net_salary_usd, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#f8fafc; font-weight:bold;">
                        <tr>
                            <td colspan="6" class="text-left px-4 py-3">مجموع کلی اصلاح شده (USD):</td>
                            <td colspan="3" class="text-success text-left px-4 py-3" dir="ltr" style="font-size:1.2rem;">
                                $<span id="footer-grand-total">{{ number_format($run->total_amount, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4 text-left">
                <button type="submit" class="btn btn-warning btn-lg shadow-sm px-5 font-weight-bold text-white">
                    <i class="fa fa-save mr-2"></i> ذخیره تغییرات و به‌روزرسانی دفتر کل
                </button>
            </div>
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const empChecks = document.querySelectorAll('.emp-check');

    selectAll.addEventListener('change', function () {
        empChecks.forEach(ch => {
            ch.checked = selectAll.checked;
        });
        recalculateTotals();
    });

    document.querySelectorAll('.emp-base, .emp-bonus, .emp-deductions, .emp-check').forEach(input => {
        input.addEventListener('input', recalculateTotals);
        input.addEventListener('change', recalculateTotals);
    });

    function recalculateTotals() {
        let grandTotalUSD = 0;

        document.querySelectorAll('.emp-row').forEach(row => {
            const isChecked = row.querySelector('.emp-check').checked;
            const base = parseFloat(row.querySelector('.emp-base').value) || 0;
            const bonus = parseFloat(row.querySelector('.emp-bonus').value) || 0;
            const ded = parseFloat(row.querySelector('.emp-deductions').value) || 0;
            const rate = parseFloat(row.querySelector('.emp-rate').value) || 1;

            const net = base + bonus - ded;
            const netUSD = net * rate;

            row.querySelector('.emp-net-display').textContent = net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            row.querySelector('.emp-usd-display').textContent = netUSD.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if (isChecked) {
                grandTotalUSD += netUSD;
            }
        });

        const formattedGrand = '$' + grandTotalUSD.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('header-grand-total').textContent = formattedGrand;
        document.getElementById('footer-grand-total').textContent = grandTotalUSD.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
});
</script>

<style>
    .rounded-lg { border-radius: 1rem !important; }
</style>
@endsection
