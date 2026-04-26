@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <!-- Header with Gradient -->
                <div class="card-header border-0 py-4" style="background: linear-gradient(45deg, #1565c0, #1976d2);">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-plus text-primary f-24"></i>
                        </div>
                        <div>
                            <h4 class="text-white mb-0 font-weight-bold">ایجاد حساب جدید در لیست حسابات</h4>
                            <p class="text-white opacity-75 mb-0">لطفاً مشخصات حساب را با دقت وارد نمایید</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5 bg-white">
                    <form action="{{ route('accounting.coa.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Account Code -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">نمبر حساب (Account Code) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="feather icon-hash"></i></span>
                                    </div>
                                    <input type="text" name="account_code" class="form-control border-left-0 bg-light" placeholder="مثلاً: 1100" required>
                                </div>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    این کد برای شناسایی و ترتیب‌بندی حساب‌ها استفاده می‌شود. دارایی‌ها معمولاً با 1، بدهی‌ها با 2 و سرمایه با 3 شروع می‌شوند.
                                </small>
                            </div>

                            <!-- Account Name -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">نام حساب (Account Name) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="feather icon-tag"></i></span>
                                    </div>
                                    <input type="text" name="account_name" class="form-control border-left-0 bg-light" placeholder="مثلاً: صندوق مرکزی" required>
                                </div>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    نامی که در گزارشات مالی و ترازنامه نمایش داده خواهد شد.
                                </small>
                            </div>

                            <!-- Account Type -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">نوعیت حساب (Account Type) <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-control select2 bg-light" required>
                                    <option value="Asset">Asset (دارایی)</option>
                                    <option value="Liability">Liability (بدهی)</option>
                                    <option value="Equity">Equity (سرمایه)</option>
                                    <option value="Revenue">Revenue (عاید)</option>
                                    <option value="Expense">Expense (هزینه)</option>
                                </select>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    این بخش ماهیت حساب را تعیین می‌کند و مشخص می‌کند که این حساب در بیلانس شیت (Asset/Liability) یا صورت حساب سود و ضرر (Revenue/Expense) قرار گیرد.
                                </small>
                            </div>

                            <!-- Normal Balance -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">بیلانس نارمل (Normal Balance) <span class="text-danger">*</span></label>
                                <select name="normal_balance" class="form-control bg-light" required>
                                    <option value="debit">دیبت (Debit)</option>
                                    <option value="credit">کریدت (Credit)</option>
                                </select>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    تعیین می‌کند که افزایش در این حساب به صورت دیبت (Debit) باشد یا کریدت (Credit). معمولاً دارایی‌ها دیبت و بدهی‌ها کریدت هستند.
                                </small>
                            </div>

                            <!-- Report Group -->
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">گروه گزارش (Report Group) <span class="text-danger">*</span></label>
                                <select name="report_group" class="form-control select2 bg-light" required>
                                    <optgroup label="دارایی‌ها (Assets)">
                                        <option value="Current Asset">Current Asset (دارایی‌های جاری)</option>
                                        <option value="Fixed Asset">Fixed Asset (دارایی‌های ثابت)</option>
                                        <option value="Inventory">Inventory (موجودی کالا)</option>
                                    </optgroup>
                                    <optgroup label="بدهی‌ها (Liabilities)">
                                        <option value="Current Liability">Current Liability (بدهی‌های جاری)</option>
                                        <option value="Long-term Liability">Long-term Liability (بدهی‌های بلندمدت)</option>
                                    </optgroup>
                                    <optgroup label="سرمایه (Equity)">
                                        <option value="Equity">Equity (سرمایه)</option>
                                    </optgroup>
                                    <optgroup label="عواید (Revenue)">
                                        <option value="Operating Revenue">Operating Revenue (عواید عملیاتی)</option>
                                        <option value="Other Revenue">Other Revenue (سایر عواید)</option>
                                    </optgroup>
                                    <optgroup label="هزینه‌ها (Expenses)">
                                        <option value="Direct Expense">Direct Expense / COGS (هزینه‌های مستقیم)</option>
                                        <option value="Operating Expense">Operating Expense (هزینه‌های عملیاتی)</option>
                                        <option value="Other Expense">Other Expense (سایر هزینه‌ها)</option>
                                    </optgroup>
                                </select>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    این بخش برای دسته‌بندی دقیق‌تر در بیلانس شیت و صورت حساب سود و ضرر استفاده می‌شود.
                                </small>
                            </div>

                            <!-- Currency -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">ارز (Currency)</label>
                                <select name="currency" class="form-control bg-light">
                                    <option value="USD">USD - دالر</option>
                                    <option value="AFN">AFN - افغانی</option>
                                </select>
                                <small class="form-text text-info mt-2">
                                    <i class="feather icon-info mr-1"></i>
                                    واحد پولی پیش‌فرض برای این حساب.
                                </small>
                            </div>

                            <!-- Is Cash Account -->
                            <div class="col-md-6 mb-4 d-flex align-items-center pt-4">
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" name="is_cash_account" value="1" class="custom-control-input" id="isCash">
                                    <label class="custom-control-label font-weight-bold" for="isCash">آیا حساب نقدی است؟ (Is Cash Account)</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-5">
                            <a href="{{ route('accounting.coa.index') }}" class="btn btn-light px-4 mr-2" style="border-radius: 10px;">انصراف</a>
                            <button type="submit" class="btn btn-primary px-5 shadow" style="border-radius: 10px; background: #1565c0;">ذخیره حساب (Save Account)</button>
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
