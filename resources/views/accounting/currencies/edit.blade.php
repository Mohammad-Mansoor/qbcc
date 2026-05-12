@extends('dsh.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary font-weight-bold">
                    <i class="feather icon-edit mr-2"></i>ویرایش اسعار: {{ $currency->code }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('accounting.currencies.update', $currency->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">کد اسعار</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $currency->code) }}" required maxlength="3" style="text-transform: uppercase;">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">نام اسعار</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $currency->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">سمبول</label>
                            <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" value="{{ old('symbol', $currency->symbol) }}" required>
                            @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">تعداد اعشار (Precision)</label>
                            <select name="decimal_precision" class="form-control">
                                <option value="0" {{ $currency->decimal_precision == 0 ? 'selected' : '' }}>0</option>
                                <option value="1" {{ $currency->decimal_precision == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $currency->decimal_precision == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $currency->decimal_precision == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $currency->decimal_precision == 4 ? 'selected' : '' }}>4</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group bg-light p-4 rounded border shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold text-primary mb-0">نرخ تبادله (Exchange Rate)</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="rate_direction" onchange="calculateInverse()">
                                <label class="custom-control-label small text-muted" for="rate_direction">ورود نرخ بر اساس 1 {{ \App\Currency::getBase()->code }}</label>
                            </div>
                        </div>

                        <div class="input-group input-group-lg" id="direct_input_group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white" id="left_unit">1 {{ $currency->code }} =</span>
                            </div>
                            <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $currency->exchange_rate) }}" required {{ $currency->is_base_currency ? 'readonly' : '' }} oninput="calculateInverse()">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white font-weight-bold" id="base_label">{{ \App\Currency::getBase()->code }}</span>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-white rounded border border-info" id="inverse_container" style="{{ $currency->is_base_currency ? 'display:none;' : '' }}">
                            <p class="mb-0 text-info">
                                <i class="feather icon-repeat mr-2"></i>
                                <strong>محاسبه نهایی:</strong> 
                                <span id="inverse_text">...</span>
                            </p>
                        </div>

                        @if($currency->is_base_currency)
                            <small class="form-text text-success mt-2 font-weight-bold">
                                <i class="feather icon-lock mr-1"></i>
                                این اسعار پایه سیستم است. نرخ آن همیشه 1.00 است.
                            </small>
                        @endif
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" name="is_base_currency" class="custom-control-input" id="is_base_currency" {{ $currency->is_base_currency ? 'checked' : '' }} onchange="toggleBaseCurrency(this)">
                                <label class="custom-control-label font-weight-bold" for="is_base_currency">به عنوان اسعار پایه (Base) تنظیم شود</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" {{ $currency->is_active ? 'checked' : '' }} {{ $currency->is_base_currency ? 'disabled' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="is_active">فعال باشد</label>
                            </div>
                        </div>
                    </div>

                    <div id="base_change_warning" class="alert alert-warning shadow-sm border-warning" style="display: none;">
                        <div class="d-flex">
                            <i class="feather icon-alert-triangle mr-3 h3 mb-0"></i>
                            <div>
                                <h6 class="alert-heading font-weight-bold">هشدار: تغییر اسعار پایه سیستم</h6>
                                <p class="mb-0 small text-dark">
                                    با انتخاب این گزینه به عنوان اسعار پایه، تمام نرخ‌های تبادله اسعار دیگر به صورت خودکار بر اساس نرخ فعلی این اسعار محاسبه مجدد خواهند شد. 
                                    <br>
                                    <strong>مثلاً:</strong> اگر قبلاً نرخ‌ها بر اساس دلار بود و اکنون افغانی را پایه قرار دهید، نرخ دلار به ۶۳ افغانی (معکوس) تبدیل خواهد شد.
                                </p>
                                <p class="mt-2 mb-0 font-weight-bold text-danger small">
                                    * لطفاً پس از ذخیره، تمامی نرخ‌ها را در جدول اصلی چک و تایید کنید.
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('accounting.currencies.index') }}" class="btn btn-light px-4 shadow-sm border">
                            <i class="feather icon-arrow-right mr-1"></i>انصراف
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="feather icon-save mr-1"></i>بروزرسانی اسعار
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateInverse() {
        const rateInput = document.getElementById('exchange_rate');
        const rate = rateInput.value;
        const code = document.getElementsByName('code')[0].value || 'Currency';
        const baseCode = "{{ \App\Currency::getBase()->code }}";
        const inverseText = document.getElementById('inverse_text');
        const inverseContainer = document.getElementById('inverse_container');
        const isBase = document.getElementById('is_base_currency').checked;
        const isInverseMode = document.getElementById('rate_direction').checked;
        const leftUnit = document.getElementById('left_unit');
        const baseLabel = document.getElementById('base_label');

        if (isBase) {
            inverseContainer.style.display = 'none';
            return;
        }

        inverseContainer.style.display = 'block';
        
        if (isInverseMode) {
            // Mode: 1 USD = X AFN
            leftUnit.innerText = `1 ${baseCode} =`;
            baseLabel.innerText = code;
            if (rate > 0) {
                const actualRate = (1 / rate).toFixed(12);
                inverseText.innerHTML = `نرخ ذخیره شده در سیستم: 1 ${code} = <strong>${actualRate}</strong> ${baseCode}`;
                // Note: We don't change the hidden input value here, the controller must handle the inversion
                // or we use a hidden field. To keep it simple, let's just use the display.
            }
        } else {
            // Mode: 1 AFN = X USD
            leftUnit.innerText = `1 ${code} =`;
            baseLabel.innerText = baseCode;
            if (rate > 0) {
                const inverse = (1 / rate).toFixed(4);
                inverseText.innerHTML = `معادل: 1 ${baseCode} = <strong>${inverse}</strong> ${code}`;
            }
        }
    }

    function toggleBaseCurrency(checkbox) {
        const rateInput = document.getElementById('exchange_rate');
        const activeSwitch = document.getElementById('is_active');
        const baseLabel = document.getElementById('base_label');
        const warningDiv = document.getElementById('base_change_warning');
        const currentCode = document.getElementsByName('code')[0].value || 'Currency';
        const isOriginallyBase = {{ $currency->is_base_currency ? 'true' : 'false' }};
        
        if (checkbox.checked) {
            rateInput.value = "1.00000000";
            rateInput.readOnly = true;
            activeSwitch.checked = true;
            activeSwitch.disabled = true;
            baseLabel.innerText = currentCode + " (Base)";
            
            if (!isOriginallyBase) {
                warningDiv.style.display = 'block';
            }
        } else {
            rateInput.readOnly = false;
            activeSwitch.disabled = false;
            baseLabel.innerText = "{{ \App\Currency::getBase()->code }} (Base)";
            warningDiv.style.display = 'none';
        }
        calculateInverse();
    }

    // Initialize
    window.onload = function() {
        calculateInverse();
    };
</script>
@endsection
