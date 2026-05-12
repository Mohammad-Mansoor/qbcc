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
                        <label class="font-weight-bold text-primary">نرخ تبادله (Exchange Rate)</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">1 [این اسعار] =</span>
                            </div>
                            <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', '1.00000000') }}" required oninput="calculateInverse()">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white font-weight-bold" id="base_label">{{ \App\Currency::getBase()->code }} (Base)</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-white rounded border border-info" id="inverse_container">
                            <p class="mb-0 text-info">
                                <i class="feather icon-repeat mr-2"></i>
                                <strong>نرخ معکوس (Inverse Rate):</strong> 
                                <span id="inverse_text">1 {{ \App\Currency::getBase()->code }} = ...</span>
                            </p>
                        </div>

                        <small class="form-text text-muted mt-2">
                            <i class="feather icon-info mr-1"></i>
                            مقدار این اسعار را بر اساس اسعار پایه سیستم وارد کنید.
                        </small>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" name="is_base_currency" class="custom-control-input" id="is_base_currency" onchange="toggleBaseCurrency(this)">
                                <label class="custom-control-label font-weight-bold" for="is_base_currency">به عنوان اسعار پایه (Base) تنظیم شود</label>
                            </div>
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
        const rate = document.getElementById('exchange_rate').value;
        const code = document.getElementsByName('code')[0].value || 'Currency';
        const baseCode = document.getElementById('base_label').innerText.split(' ')[0];
        const inverseText = document.getElementById('inverse_text');
        const inverseContainer = document.getElementById('inverse_container');
        const isBase = document.getElementById('is_base_currency').checked;

        if (isBase) {
            inverseContainer.style.display = 'none';
            return;
        }

        inverseContainer.style.display = 'block';
        if (rate > 0) {
            const inverse = (1 / rate).toFixed(8);
            inverseText.innerHTML = `1 ${baseCode} = <strong>${inverse}</strong> ${code}`;
        } else {
            inverseText.innerHTML = 'لطفاً نرخ معتبر وارد کنید';
        }
    }

    function toggleBaseCurrency(checkbox) {
        const rateInput = document.getElementById('exchange_rate');
        const activeSwitch = document.getElementById('is_active');
        const baseLabel = document.getElementById('base_label');
        const currentCode = document.getElementsByName('code')[0].value || 'Currency';
        
        if (checkbox.checked) {
            rateInput.value = "1.00000000";
            rateInput.readOnly = true;
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
