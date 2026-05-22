@extends('dsh.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary font-weight-bold">
                    <i class="feather icon-plus-circle mr-2"></i>افزودن اسعار جدید
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('accounting.currencies.store') }}" method="POST" id="currency-form">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">کد اسعار (مثلاً USD)</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required maxlength="3" style="text-transform: uppercase;">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">نام اسعار</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">سمبول</label>
                            <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" value="{{ old('symbol') }}" required>
                            @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">تعداد اعشار (Precision)</label>
                            <select name="decimal_precision" class="form-control" id="decimal_precision">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
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
                                <span class="input-group-text bg-white" id="left_unit">1 [اسعار جدید] =</span>
                            </div>
                            <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', '1.00000000') }}" required oninput="calculateInverse()">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white font-weight-bold" id="base_label">{{ \App\Currency::getBase()->code }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-muted small">
                            <i class="feather icon-help-circle mr-1"></i>
                            <strong>راهنما:</strong> در این فیلد وارد کنید که هر 1 واحد از این اسعار چقدر ارزش در برابر اسعار پایه دارد.
                        </div>

                        <div class="mt-3 p-3 bg-white rounded border border-info" id="inverse_container" style="display:none;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="feather icon-repeat mr-2 text-info"></i>
                                <strong class="text-info">محاسبه نهایی و نرخ معکوس:</strong>
                            </div>
                            <div id="inverse_text" class="h6 mb-0 font-weight-bold">...</div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <!-- Base Currency is FIXED to USD. Option removed for stability. -->
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" checked>
                                <label class="custom-control-label font-weight-bold" for="is_active">فعال باشد</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('accounting.currencies.index') }}" class="btn btn-light px-4 shadow-sm border">
                            <i class="feather icon-arrow-right mr-1"></i>انصراف
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="feather icon-check mr-1"></i>ذخیره اسعار
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
        const code = document.getElementsByName('code')[0].value || 'New Currency';
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
        const currentCode = document.getElementsByName('code')[0].value || 'Currency';
        
        if (checkbox.checked) {
            rateInput.readOnly = false;
            activeSwitch.checked = true;
            activeSwitch.disabled = true;
            baseLabel.innerText = currentCode + " (Base)";
        } else {
            rateInput.readOnly = false;
            activeSwitch.disabled = false;
            // Fetch actual base from server side hidden variable or just generic 'Base'
            baseLabel.innerText = "Base Currency"; 
        }
        calculateInverse();
    }

    // Initialize
    window.onload = function() {
        calculateInverse();
    };
</script>
@endsection
