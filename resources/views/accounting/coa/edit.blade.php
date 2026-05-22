@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <!-- Header with Gradient -->
                <div class="card-header border-0 py-4" style="background: linear-gradient(45deg, #fb8c00, #ff9800);">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-edit text-warning f-24"></i>
                        </div>
                        <div>
                            <h4 class="text-white mb-0 font-weight-bold">ویرایش حساب: {{ $account->account_name }}</h4>
                            <p class="text-white opacity-75 mb-0">در حال ویرایش تنظیمات حساب شماره {{ $account->account_code }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5 bg-white">
                    <form action="{{ route('accounting.coa.update', $account->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if($hasEntries)
                        <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 10px;">
                            <i class="feather icon-alert-triangle mr-2"></i>
                            <strong>توجه:</strong> این حساب دارای معامله در دفتر کل می‌باشد. جهت حفظ انسجام تاریخی، نمبر حساب، نوعیت، بیلانس نارمل و ارز قفل شده است.
                        </div>
                        @endif
                        
                        <div class="row">
                            <!-- Account Code -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">نمبر حساب (Account Code) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="feather icon-hash"></i></span>
                                    </div>
                                    <input type="text" name="account_code" value="{{ $account->account_code }}" class="form-control border-left-0 bg-light" {{ $hasEntries ? 'readonly' : '' }} required>
                                </div>
                                <small class="form-text text-info mt-2">
                                    این کد برای شناسایی و ترتیب‌بندی حساب‌ها استفاده می‌شود. تغییر این کد ممکن است روی گزارشات قبلی تاثیر بگذارد.
                                </small>
                            </div>

                            <!-- Account Name -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">نام حساب (Account Name) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="feather icon-tag"></i></span>
                                    </div>
                                    <input type="text" name="account_name" value="{{ $account->account_name }}" class="form-control border-left-0 bg-light" required>
                                </div>
                                <small class="form-text text-info mt-2">
                                    نامی که در گزارشات مالی و ترازنامه نمایش داده خواهد شد.
                                </small>
                            </div>

                            <!-- Account Type -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">نوعیت حساب (Account Type) <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-control select2 bg-light" {{ $hasEntries ? 'disabled' : '' }} required>
                                    @foreach(['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'] as $type)
                                        <option value="{{ $type }}" {{ $account->account_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-info mt-2">
                                    تغییر نوعیت حساب، نحوه محاسبه آن در بیلانس شیت و سود و ضرر را تغییر می‌دهد.
                                </small>
                            </div>

                            <!-- Normal Balance -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">بیلانس نارمل (Normal Balance) <span class="text-danger">*</span></label>
                                <select name="normal_balance" class="form-control bg-light" {{ $hasEntries ? 'disabled' : '' }} required>
                                    <option value="debit" {{ $account->normal_balance == 'debit' ? 'selected' : '' }}>دیبت (Debit)</option>
                                    <option value="credit" {{ $account->normal_balance == 'credit' ? 'selected' : '' }}>کریدت (Credit)</option>
                                </select>
                                <small class="form-text text-info mt-2">
                                    تعیین می‌کند که افزایش در این حساب به صورت دیبت (Debit) باشد یا کریدت (Credit).
                                </small>
                            </div>

                            <!-- Report Group -->
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">گروه گزارش (Report Group) <span class="text-danger">*</span></label>
                                <select name="report_group" class="form-control select2 bg-light" required>
                                    @php
                                        $groups = [
                                            'Assets' => ['Current Asset' => 'Current Asset (دارایی‌های جاری)', 'Fixed Asset' => 'Fixed Asset (دارایی‌های ثابت)', 'Inventory' => 'Inventory (موجودی کالا)'],
                                            'Liabilities' => ['Current Liability' => 'Current Liability (بدهی‌های جاری)', 'Long-term Liability' => 'Long-term Liability (بدهی‌های بلندمدت)'],
                                            'Equity' => ['Equity' => 'Equity (سرمایه)'],
                                            'Revenue' => ['Operating Revenue' => 'Operating Revenue (عواید عملیاتی)', 'Other Revenue' => 'Other Revenue (سایر عواید)'],
                                            'Expenses' => ['Direct Expense' => 'Direct Expense / COGS (هزینه‌های مستقیم)', 'Operating Expense' => 'Operating Expense (هزینه‌های عملیاتی)', 'Other Expense' => 'Other Expense (سایر هزینه‌ها)']
                                        ];
                                    @endphp
                                    @foreach($groups as $label => $options)
                                        <optgroup label="{{ $label }}">
                                            @foreach($options as $val => $lbl)
                                                <option value="{{ $val }}" {{ $account->report_group == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Cashflow Group -->
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">گروه جریان وجوه نقد (Cashflow Group)</label>
                                <select name="cashflow_group" class="form-control select2 bg-light">
                                    <option value="">-- انتخاب (اختیاری) --</option>
                                    <option value="Operating" {{ $account->cashflow_group == 'Operating' ? 'selected' : '' }}>Operating Activities (فعالیت‌های عملیاتی)</option>
                                    <option value="Investing" {{ $account->cashflow_group == 'Investing' ? 'selected' : '' }}>Investing Activities (فعالیت‌های سرمایه‌گذاری)</option>
                                    <option value="Financing" {{ $account->cashflow_group == 'Financing' ? 'selected' : '' }}>Financing Activities (فعالیت‌های تأمین مالی)</option>
                                </select>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    برای تهیه صورت جریان وجوه نقد (Indirect Method) ضروری است.
                                </small>
                            </div>

                            <!-- Currency -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">ارز (Currency)</label>
                                <select name="currency" class="form-control select2 bg-light" {{ $hasEntries ? 'disabled' : '' }} required>
                                    @foreach($currencies as $c)
                                        <option value="{{ $c->code }}" {{ $account->currency == $c->code ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Is Cash Account -->
                            <div class="col-md-6 mb-4 d-flex align-items-center pt-4">
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" name="is_cash_account" value="1" class="custom-control-input" id="isCash" {{ $account->is_cash_account ? 'checked' : '' }} {{ $hasEntries ? 'disabled' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="isCash">آیا حساب نقدی است؟ (Is Cash Account)</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-5">
                            <a href="{{ route('accounting.coa.index') }}" class="btn btn-light px-4 mr-2" style="border-radius: 10px;">انصراف</a>
                            <button type="submit" class="btn btn-warning px-5 shadow text-white" style="border-radius: 10px; background: #fb8c00;">بروزرسانی حساب (Update Account)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-plugins')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
