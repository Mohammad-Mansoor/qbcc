@extends('dsh.master')
@section('title', 'اجرای معاش جدید')
@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h3 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-money text-success mr-2"></i> اجرای معاش ماهوار
            </h3>
            <p class="text-muted small mb-0 mt-1">معاش هر کارمند بر اساس قرارداد فعال و ارز قرارداد محاسبه و در دفتر کل ثبت می‌شود</p>
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

    <form action="{{ route('payroll.store') }}" method="POST" id="payroll-form">
        @csrf

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
                            <input type="text" name="month_year" class="form-control"
                                   placeholder="مثلاً: 06-2026 یا ثور ۱۴۰۳"
                                   value="{{ old('month_year', \Carbon\Carbon::now()->format('m-Y')) }}" required>
                            <small class="text-muted">این مقدار یکتا خواهد بود — دو بار برای یک ماه قابل اجرا نیست</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted small text-uppercase">تاریخ اجرا (Run Date)</label>
                            <input type="date" name="run_date" class="form-control"
                                   value="{{ old('run_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted small text-uppercase">مجموع خالص معاش (USD)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold text-success">$</span>
                                </div>
                                <input type="text" id="grand_total_usd" class="form-control font-weight-bold text-success" readonly value="0.00">
                            </div>
                            <small class="text-muted">محاسبه خودکار بر اساس انتخاب ها</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Employees Table ── --}}
        <div class="card border-0 shadow-sm rounded-lg mb-4">
            <div class="card-header border-0 py-3 px-4" style="background:#f8fafc;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa fa-users text-primary mr-2"></i> لیست کارمندان و معاشات
                    </h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="select-all-btn">
                            <i class="fa fa-check-square-o mr-1"></i> انتخاب همه
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselect-all-btn">
                            <i class="fa fa-square-o mr-1"></i> حذف انتخاب
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right" id="payroll-table">
                    <thead style="background:#1e293b; color:#e2e8f0;">
                        <tr>
                            <th class="border-0 py-3 px-3 text-center" style="width:50px;">انتخاب</th>
                            <th class="border-0 py-3">نام کارمند</th>
                            <th class="border-0 py-3">وظیفه</th>
                            <th class="border-0 py-3 text-center">معاش پایه</th>
                            <th class="border-0 py-3 text-center">ارز</th>
                            <th class="border-0 py-3 text-center">نرخ دالر</th>
                            <th class="border-0 py-3 text-center">بونس / اضافه کاری</th>
                            <th class="border-0 py-3 text-center">کسرات / جریمه</th>
                            <th class="border-0 py-3 text-center">خالص پرداختی (ارز)</th>
                            <th class="border-0 py-3 text-center" style="color:#4ade80;">معادل USD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                        @php
                            $hasContract  = $emp->base_salary !== null;
                            $isExpired    = $emp->contract_expired ?? 0;
                            $isNoContract = $emp->no_contract ?? !$hasContract;
                            $rowStyle     = $isExpired ? 'background:#fff7ed;' : ($isNoContract ? 'background:#fef2f2;' : '');
                        @endphp
                        <tr style="{{ $rowStyle }}" class="emp-row border-bottom">
                            <td class="py-3 px-3 text-center">
                                @if($hasContract && !$isExpired)
                                    <input type="checkbox" name="employees[{{ $emp->id }}][include]"
                                           class="emp-checkbox" value="1" checked>
                                @else
                                    <input type="checkbox" disabled title="{{ $isExpired ? 'قرارداد منقضی' : 'بدون قرارداد' }}">
                                @endif
                            </td>
                            <td class="py-3 font-weight-bold text-dark">
                                {{ $emp->name }}
                                @if($isExpired)
                                    <span class="badge badge-warning ml-1" style="font-size:.7rem;">قرارداد منقضی</span>
                                @elseif($isNoContract)
                                    <span class="badge badge-danger ml-1" style="font-size:.7rem;">بدون قرارداد</span>
                                @endif
                            </td>
                            <td class="py-3 text-muted small">{{ $emp->job_title }}</td>

                            {{-- Base Salary --}}
                            <td class="py-3 text-center">
                                <input type="hidden" name="employees[{{ $emp->id }}][base_salary]"
                                       value="{{ $emp->base_salary ?? 0 }}">
                                <span class="font-weight-bold">
                                    {{ number_format($emp->base_salary ?? 0, 2) }}
                                </span>
                            </td>

                            {{-- Currency --}}
                            <td class="py-3 text-center">
                                <input type="hidden" name="employees[{{ $emp->id }}][currency_id]"
                                       value="{{ $emp->currency_id }}">
                                <span class="badge badge-info px-2">{{ $emp->currency_code ?? 'USD' }}</span>
                            </td>

                            {{-- Exchange Rate --}}
                            <td class="py-3 text-center">
                                <input type="number" step="0.00000001" min="0.00000001"
                                       name="employees[{{ $emp->id }}][exchange_rate]"
                                       class="form-control form-control-sm rate-input text-center"
                                       style="width:100px;"
                                       value="{{ number_format($emp->exchange_rate ?? 1, 8, '.', '') }}"
                                       {{ $hasContract && !$isExpired ? '' : 'disabled' }}>
                            </td>

                            {{-- Bonus --}}
                            <td class="py-3 text-center">
                                <input type="number" step="0.01" min="0"
                                       name="employees[{{ $emp->id }}][bonus]"
                                       class="form-control form-control-sm bonus-input text-center text-success"
                                       style="width:110px;" value="0"
                                       {{ $hasContract && !$isExpired ? '' : 'disabled' }}>
                            </td>

                            {{-- Deductions --}}
                            <td class="py-3 text-center">
                                <input type="number" step="0.01" min="0"
                                       name="employees[{{ $emp->id }}][deductions]"
                                       class="form-control form-control-sm ded-input text-center text-danger"
                                       style="width:110px;" value="0"
                                       {{ $hasContract && !$isExpired ? '' : 'disabled' }}>
                            </td>

                            {{-- Net in contract currency --}}
                            <td class="py-3 text-center">
                                <input type="text" readonly
                                       class="form-control form-control-sm net-local text-center font-weight-bold"
                                       style="width:120px; background:#f0fdf4;"
                                       value="{{ number_format($emp->base_salary ?? 0, 2) }}">
                                <small class="text-muted" style="font-size:.7rem;">{{ $emp->currency_code ?? 'USD' }}</small>
                            </td>

                            {{-- Net USD --}}
                            <td class="py-3 text-center">
                                <div class="d-inline-flex align-items-center gap-1"
                                     style="background:#ecfdf5;padding:4px 10px;border-radius:8px;border:1px solid #a7f3d0;gap:4px;">
                                    <span style="color:#059669;font-weight:600;">$</span>
                                    <span class="net-usd font-weight-bold" style="color:#065f46;">
                                        {{ number_format(($emp->base_salary ?? 0) * ($emp->exchange_rate ?? 1), 2) }}
                                    </span>
                                </div>
                            </td>

                            {{-- Hidden base_salary_usd (calculated on submit) --}}
                            <input type="hidden" name="employees[{{ $emp->id }}][base_salary_usd]"
                                   value="{{ $emp->base_salary_usd ?? 0 }}">
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa fa-users" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                                هیچ کارمندی با قرارداد فعال یافت نشد.
                                <br>
                                <a href="/dashboard/employee-salary" class="btn btn-sm btn-warning mt-3">ثبت قرارداد</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    {{-- Grand Totals Footer --}}
                    <tfoot style="background:#f1f5f9; font-weight:700;">
                        <tr>
                            <td colspan="8" class="py-3 px-4 text-right text-dark">مجموع خالص معاشات:</td>
                            <td class="py-3 text-center">
                                <span id="footer-net-local" class="text-success">—</span>
                            </td>
                            <td class="py-3 text-center">
                                <div style="background:#ecfdf5;padding:4px 12px;border-radius:8px;border:1px solid #6ee7b7;display:inline-flex;gap:4px;align-items:center;">
                                    <span style="color:#059669;">$</span>
                                    <span id="footer-net-usd" style="color:#065f46;">0.00</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Accounting Override Card ── --}}
        <div class="card border-0 shadow-sm rounded-lg mb-4">
            <div class="card-header border-0 py-3 px-4" style="background:#f8fafc;">
                <h5 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-university text-warning mr-2"></i> تنظیمات حسابداری — دفتر کل (General Ledger)
                </h5>
            </div>
            <div class="card-body px-4 py-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-info small text-uppercase">
                                <i class="fa fa-arrow-up mr-1"></i> حساب مصرف معاش (Debit — Salary Expense)
                            </label>
                            <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2" required>
                                @foreach($allowedDebitAccounts as $acc)
                                    <option value="{{ $acc->id }}"
                                        {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} — {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-info small text-uppercase">
                                <i class="fa fa-arrow-down mr-1"></i> حساب معاشات واجب‌الاداء (Credit — Salary Payable)
                            </label>
                            <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2" required>
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}"
                                        {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} — {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light border rounded mb-0 py-2" style="font-size:.82rem; color:#555;">
                    <i class="fa fa-info-circle text-primary mr-1"></i>
                    این تراکنش به صورت <strong>تعهدی (Accrual)</strong> ثبت می‌شود:
                    <code>DR Salary Expense &nbsp;/&nbsp; CR Salary Payable</code> —
                    مبلغ USD نرمالایز شده بر اساس نرخ تبدیل ارز هر قرارداد محاسبه می‌شود.
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="text-left mb-4">
            <button type="submit" class="btn btn-success btn-lg shadow-sm font-weight-bold px-5">
                <i class="fa fa-save mr-2"></i> ثبت نهایی معاشات و ارسال به دفتر کل
            </button>
            <a href="{{ route('payroll.index') }}" class="btn btn-light btn-lg ml-2">انصراف</a>
        </div>

    </form>
</div>

@endsection

@section('scripts')
@if(session('swal_error'))
<script>
    $(document).ready(function() {
        swal({
            title: "خطا!",
            text: "{{ session('swal_error') }}",
            icon: "error",
            button: "بستن",
        });
    });
</script>
@endif
<script>
$(document).ready(function() {
    $('#override_debit_account_id, #override_credit_account_id').select2();

    function recalcRow(row) {
        var base = parseFloat(row.find('input[name*="[base_salary]"]').val()) || 0;
        var rate = parseFloat(row.find('.rate-input').val()) || 1;
        var bonus = parseFloat(row.find('.bonus-input').val()) || 0;
        var ded   = parseFloat(row.find('.ded-input').val()) || 0;
        var net   = base + bonus - ded;
        var netUSD = net * rate;

        row.find('.net-local').val(net.toFixed(2));
        row.find('.net-usd').text(netUSD.toFixed(2));
        recalcGrandTotal();
    }

    function recalcGrandTotal() {
        var totalUSD = 0;
        $('#payroll-table tbody tr.emp-row').each(function() {
            var checked = $(this).find('.emp-checkbox').is(':checked');
            if (!checked) return;
            var usdText = $(this).find('.net-usd').text();
            totalUSD += parseFloat(usdText) || 0;
        });
        $('#grand_total_usd').val(totalUSD.toFixed(2));
        $('#footer-net-usd').text(totalUSD.toFixed(2));
    }

    // Live recalc on any input change
    $('#payroll-table').on('input change', '.bonus-input, .ded-input, .rate-input', function() {
        recalcRow($(this).closest('tr'));
    });

    // Checkbox toggle
    $('#payroll-table').on('change', '.emp-checkbox', recalcGrandTotal);

    // Select all / deselect all
    $('#select-all-btn').on('click', function() {
        $('#payroll-table .emp-checkbox:not([disabled])').prop('checked', true);
        recalcGrandTotal();
    });
    $('#deselect-all-btn').on('click', function() {
        $('#payroll-table .emp-checkbox:not([disabled])').prop('checked', false);
        recalcGrandTotal();
    });

    // Initial calculation
    recalcGrandTotal();
});
</script>
@endsection
