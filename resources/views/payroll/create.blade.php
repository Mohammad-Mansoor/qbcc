@extends('dsh.master')
@section('title' , 'ثبت معاشات')
@section('content')
<div class="sparkline12-list">
    <div class="sparkline12-hd">
        <div class="main-sparkline12-hd">
            <h1>ثبت معاشات کارمندان</h1>
        </div>
    </div>
    <div class="sparkline12-graph">
        <form action="{{ route('payroll.store') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>ماه و سال</label>
                        <input type="text" name="month_year" class="form-control" placeholder="مثلاً: ثور ۱۴۰۳" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>تاریخ اجرا</label>
                        <input type="date" name="run_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>انتخاب</th>
                            <th>نام کارمند</th>
                            <th>معاش اصلی</th>
                            <th>بونس / اضافه کاری</th>
                            <th>تخفیفات / جریمه</th>
                            <th>خالص پرداختی</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td>
                                <input type="checkbox" name="employees[{{ $emp->id }}][include]" checked>
                                <input type="hidden" name="employees[{{ $emp->id }}][base_salary]" value="{{ $emp->base_salary }}">
                            </td>
                            <td>{{ $emp->name }}</td>
                            <td>{{ number_format($emp->base_salary) }}</td>
                            <td><input type="number" name="employees[{{ $emp->id }}][bonus]" class="form-control calc" value="0"></td>
                            <td><input type="number" name="employees[{{ $emp->id }}][deductions]" class="form-control calc" value="0"></td>
                            <td><input type="text" readonly class="form-control net-salary" value="{{ $emp->base_salary }}"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- ACCOUNT OVERRIDES -->
            <div class="row mt-4 p-3" style="background: #f1f3f5; border-radius: 8px; border: 1px solid #dee2e6;">
                <div class="col-lg-12">
                    <h6 class="text-muted mb-3"><i class="fa fa-university"></i> تنظیمات حسابی (Payroll Accounting)</h6>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="text-info">حساب مصرف معاش (Debit)</label>
                        <select name="override_debit_account_id" id="override_debit_account_id" class="form-control">
                            @foreach($allowedDebitAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="text-info">حساب معاشات واجب‌الپرداخت (Credit)</label>
                        <select name="override_credit_account_id" id="override_credit_account_id" class="form-control">
                            @foreach($allowedCreditAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="alert alert-light border mt-2 py-1" style="font-size: 0.8rem; color: #666;">
                        <i class="fa fa-info-circle"></i> این تراکنش به صورت تعهدی (Accrual) ثبت می‌شود.
                    </div>
                </div>
            </div>

            <div class="mt-4 text-left">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> ثبت و تایید نهایی</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#override_debit_account_id').select2();
        $('#override_credit_account_id').select2();

        $('.calc').on('input', function() {
            var row = $(this).closest('tr');
            var base = parseFloat(row.find('input[name*="base_salary"]').val()) || 0;
            var bonus = parseFloat(row.find('input[name*="bonus"]').val()) || 0;
            var ded = parseFloat(row.find('input[name*="deductions"]').val()) || 0;
            var net = base + bonus - ded;
            row.find('.net-salary').val(net.toLocaleString());
        });
    });
</script>
@endsection
