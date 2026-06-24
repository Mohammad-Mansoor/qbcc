@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #1a237e, #283593);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white font-weight-bold mb-1">تنظیمات هوشمند نگاشت حسابات</h3>
                            <p class="mb-0 opacity-80">در این بخش، قوانین ثبت خودکار سند در دفتر کل (General Ledger) را برای هر فعالیت تجاری مدیریت کنید.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow" style="width: 60px; height: 60px;">
                                <i class="feather icon-cpu text-primary f-30"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="feather icon-check-circle mr-2"></i> {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('accounting.mappings.update') }}" method="POST">
        @csrf
        
        <!-- Column Explanations Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: #f8f9fa;">
            <div class="card-body p-4">
                <h6 class="font-weight-bold text-dark mb-3"><i class="feather icon-help-circle text-primary mr-2"></i> راهنمای ستون‌های تنظیمات:</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="p-2">
                            <span class="d-block font-weight-bold text-primary small">نوع تراکنش (Event)</span>
                            <small class="text-muted">رویداد تجاری که باعث ثبت خودکار سند می‌شود.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2">
                            <span class="d-block font-weight-bold text-primary small">حساب دیبت (Debit)</span>
                            <small class="text-muted">حسابی که در این تراکنش دارایی آن افزایش می‌یابد.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2">
                            <span class="d-block font-weight-bold text-primary small">حساب کریدت (Credit)</span>
                            <small class="text-muted">حسابی که منبع پول است یا درآمد در آن ثبت می‌شود.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2">
                            <span class="d-block font-weight-bold text-primary small">توضیحات (Narrative)</span>
                            <small class="text-muted">متنی که به طور خودکار در روزنامچه نوشته می‌شود.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rules Grouped by Type -->
        @php
            $types = [
                'sale' => [
                    'title' => 'طی مراحل فروشات (Sales Cycle)', 
                    'color' => '#4099ff', 
                    'icon' => 'shopping-cart', 
                    'sub' => 'مدیریت حساب‌های درآمد و نقدینگی فروش',
                    'db_types' => ['sale']
                ],
                'customer_payment' => [
                    'title' => 'رسیدات از مشتریان (Customer Receipts)', 
                    'color' => '#ffb64d', 
                    'icon' => 'user-check', 
                    'sub' => 'مدیریت دریافت پول و تسویه حساب مشتریان',
                    'db_types' => ['customer_payment']
                ],
                'agent_payment' => [
                    'title' => 'حسابات نماینده ها (Agent Transactions)', 
                    'color' => '#9575cd', 
                    'icon' => 'users', 
                    'sub' => 'مدیریت دریافت و پرداخت‌های مالی نماینده‌ها',
                    'db_types' => ['agent_payment']
                ],
                'material_purchase' => [
                    'title' => 'خریداری مواد اولیه (Material Purchases)', 
                    'color' => '#2ed8b6', 
                    'icon' => 'package', 
                    'sub' => 'مدیریت حساب‌های موجودی کالا و بدهی به فروشندگان مواد',
                    'db_types' => ['material_purchase', 'material_payment_in', 'material_payment_out']
                ],
                'washing' => [
                    'title' => 'طی مراحل شست (Washing Cycle)', 
                    'color' => '#00bcd4', 
                    'icon' => 'droplet', 
                    'sub' => 'مدیریت حساب‌های هزینه شست و سرمایه‌گذاری در موجودی (WIP)',
                    'db_types' => ['washing', 'washing_payment']
                ],
                'finishing' => [
                    'title' => 'طی مراحل تیاری (Finishing Cycle)', 
                    'color' => '#e91e63', 
                    'icon' => 'check-circle', 
                    'sub' => 'مدیریت حساب‌های هزینه تیاری و سرمایه‌گذاری در موجودی (WIP)',
                    'db_types' => ['finishing', 'finishing_payment']
                ],
                'kachaee' => [
                    'title' => 'طی مراحل کچایی و ترمیم (Kachaee/Repair)', 
                    'color' => '#ff5722', 
                    'icon' => 'tool', 
                    'sub' => 'مدیریت حساب‌های هزینه ترمیم و انتقال کار',
                    'db_types' => ['kachaee_repair_cost', 'kachaee_transfer', 'kachaee_payment']
                ],
                'payments' => [
                    'title' => 'پرداخت‌های فروشندگان (Supplier Payments)', 
                    'color' => '#009688', 
                    'icon' => 'credit-card', 
                    'sub' => 'مدیریت پرداخت‌های نقدی به تامین‌کنندگان',
                    'db_types' => ['seller_payment']
                ],
                'employee_payment' => [
                    'title' => 'معاشات و کارمندان (Employee Salaries)', 
                    'color' => '#795548', 
                    'icon' => 'briefcase', 
                    'sub' => 'مدیریت هزینه‌ها و پرداخت‌های معاشات کارمندان',
                    'db_types' => ['employee_payment']
                ],
                'office_cash' => [
                    'title' => 'دخل و نقدینگی دفتر (Office Cash Transactions)', 
                    'color' => '#607d8b', 
                    'icon' => 'dollar-sign', 
                    'sub' => 'مدیریت تزریق یا برداشت نقدینگی دفتر',
                    'db_types' => ['office_credit', 'office_debit']
                ],
                'expenses' => [
                    'title' => 'مصارف عملیاتی (Operational Expenses)', 
                    'color' => '#ff5252', 
                    'icon' => 'activity', 
                    'sub' => 'مدیریت دسته‌بندی مصارف روزمره و خوراکه دفتر',
                    'db_types' => ['expense']
                ],
                'fixed_assets' => [
                    'title' => 'اجناس و دارایی‌های ثابت (Fixed Assets)', 
                    'color' => '#3f51b5', 
                    'icon' => 'home', 
                    'sub' => 'مدیریت دارایی‌های ثابت و جایدادهای اداری',
                    'db_types' => ['ajnas_account', 'asset']
                ],
                'different_account' => [
                    'title' => 'حساب‌های متفرقه (Miscellaneous Accounts)', 
                    'color' => '#ab47bc', 
                    'icon' => 'layers', 
                    'sub' => 'مدیریت ثبت‌های موقت و متفرقه متفرقه حساب‌ها',
                    'db_types' => ['different_account']
                ],
            ];
        @endphp

        @foreach($types as $typeKey => $typeInfo)
        @php
            $subRules = $rules->whereIn('transaction_type', $typeInfo['db_types']);
        @endphp
        @if($subRules->count() > 0)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header border-0 py-3" style="background: {{ $typeInfo['color'] }}; color: #fff;">
                <h5 class="mb-0 font-weight-bold"><i class="feather icon-{{ $typeInfo['icon'] }} mr-2"></i> {{ $typeInfo['title'] }}</h5>
                <small class="opacity-80">{{ $typeInfo['sub'] }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light small text-muted uppercase">
                            <tr>
                                <th style="width: 20%" class="px-4 py-3">شرایط (Condition)</th>
                                <th style="width: 25%" class="py-3">حساب دیبت (Debit)</th>
                                <th style="width: 25%" class="py-3">حساب کریدت (Credit)</th>
                                <th style="width: 30%" class="py-3 px-4">متن توضیحات خودکار (Auto Narration)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subRules as $rule)
                            <tr>
                                <td class="px-4 py-4 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 mr-2 text-center shadow-sm" style="min-width: 100px; border: 1px solid #ddd;">
                                            <span class="font-weight-bold text-dark small">
                                                @if($rule->condition == 'cash' || $rule->condition == 'نقدی') نقدی (Cash)
                                                @elseif($rule->condition == 'credit' || $rule->condition == 'نسیه') نسیه (Credit)
                                                @elseif($rule->condition == 'receipt' || $rule->condition == 'رسید') رسید (Receipt)
                                                @elseif($rule->condition == 'withdrawal' || $rule->condition == 'گرفت') گرفت (Withdrawal/Payment)
                                                @elseif($rule->condition == 'transfer') انتقال (Transfer)
                                                @else {{ strtoupper($rule->condition) }} @endif
                                            </span>
                                            @if($rule->mapping_key)
                                                <div class="small text-muted mt-1 border-top pt-1" dir="ltr" style="font-size: 0.65rem;">
                                                    <code>{{ $rule->mapping_key }}</code>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 align-middle">
                                    <select name="rules[{{ $rule->id }}][debit_account_id]" class="form-control select2">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ $rule->debit_account_id == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-4 align-middle">
                                    <select name="rules[{{ $rule->id }}][credit_account_id]" class="form-control select2">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ $rule->credit_account_id == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-4 px-4 align-middle">
                                    <input type="text" name="rules[{{ $rule->id }}][description_template]" value="{{ $rule->description_template }}" class="form-control bg-light" placeholder="مثلاً: فروش نقد نمبر {ref}">
                                    <small class="text-muted mt-1 d-block"><i class="feather icon-edit-2"></i> این متن در روزنامچه ظاهر می‌شود.</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        @endforeach

        @php
            $allConfiguredDbTypes = [];
            foreach($types as $typeInfo) {
                $allConfiguredDbTypes = array_merge($allConfiguredDbTypes, $typeInfo['db_types']);
            }
            $unmatchedRules = $rules->whereNotIn('transaction_type', $allConfiguredDbTypes);
        @endphp

        @if($unmatchedRules->count() > 0)
        <!-- Unmatched / Miscellaneous Rules -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden; border: 1px dashed #ff5252;">
            <div class="card-header border-0 py-3 bg-dark text-white">
                <h5 class="mb-0 font-weight-bold"><i class="feather icon-alert-circle mr-2"></i> نگاشت‌های متفرقه سیستم (Other System Rules)</h5>
                <small class="opacity-80">سایر قوانین نگاشت خودکار تراکنش‌ها</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light small text-muted uppercase">
                            <tr>
                                <th style="width: 20%" class="px-4 py-3">رویداد (Event)</th>
                                <th style="width: 25%" class="py-3">حساب دیبت (Debit)</th>
                                <th style="width: 25%" class="py-3">حساب کریدت (Credit)</th>
                                <th style="width: 30%" class="py-3 px-4">متن توضیحات خودکار (Auto Narration)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unmatchedRules as $rule)
                            <tr>
                                <td class="px-4 py-4 align-middle">
                                    <div class="bg-light rounded p-2 mr-2 text-center shadow-sm" style="min-width: 100px; border: 1px solid #ddd;">
                                        <span class="badge badge-secondary px-2 py-1">{{ strtoupper($rule->transaction_type) }}</span>
                                        <div class="small text-muted mt-1" dir="ltr" style="font-size: 0.65rem;">
                                            <code>{{ $rule->mapping_key }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 align-middle">
                                    <select name="rules[{{ $rule->id }}][debit_account_id]" class="form-control select2">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ $rule->debit_account_id == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-4 align-middle">
                                    <select name="rules[{{ $rule->id }}][credit_account_id]" class="form-control select2">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ $rule->credit_account_id == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-4 px-4 align-middle">
                                    <input type="text" name="rules[{{ $rule->id }}][description_template]" value="{{ $rule->description_template }}" class="form-control bg-light">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Bar -->
        <div class="card border-0 shadow-lg sticky-bottom mb-5" style="border-radius: 15px; background: #fff; z-index: 1000;">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="feather icon-info mr-1 text-primary"></i>
                    تغییرات شما بلافاصله پس از ذخیره روی تمام تراکنش‌های آینده اعمال خواهد شد.
                </div>
                @can('edit_mapping_rules')
                <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm" style="border-radius: 10px;">
                    <i class="feather icon-save mr-2"></i> ذخیره تنظیمات هوشمند
                </button>
                @endcan
            </div>
        </div>
    </form>
</div>

<style>
    .sticky-bottom { position: sticky; bottom: 20px; border: 1px solid #eee; }
    .select2-container--default .select2-selection--single { background-color: #f8f9fa; border: none; height: 40px; line-height: 40px; }
    .select2-container { width: 100% !important; }
    tr:hover { background-color: #fcfcfc; }
    .table td, .table th { border-top: 1px solid #f1f1f1; }
</style>
@endsection

@section('footer-plugins')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            dir: "rtl"
        });
    });
</script>
@endsection
