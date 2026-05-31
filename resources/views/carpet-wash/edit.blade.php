@extends('dsh.master')
@section('title' , 'Carpet Wash')
@section('content')

<style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .glass-header {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid var(--qbcc-border, #e2e8f0);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .glass-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 1.2rem;
    }

    .form-group label {
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 8px;
        display: inline-block;
    }

    .form-control {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        box-shadow: none;
        padding: 10px 15px;
        height: auto;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: var(--qbcc-primary, #3b82f6);
        background: #fff;
    }
    .form-control[readonly] {
        background: #f8fafc;
        border-color: #f1f5f9;
        color: #64748b;
    }

    .btn-submit {
        background: var(--qbcc-primary, #3b82f6);
        color: white;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: #2563eb;
        color: white;
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
        text-decoration: none;
    }

    .section-divider {
        border-top: 1px solid #f1f5f9;
        margin: 25px 0;
        position: relative;
    }
    .section-divider span {
        position: absolute;
        top: -12px;
        right: 30px;
        background: white;
        padding: 0 10px;
        font-weight: 700;
        color: #94a3b8;
        font-size: 0.8rem;
    }
</style>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="glass-card">
      
      <!-- Header -->
      <div class="glass-header">
        <h5><i class="fa fa-pencil-alt mr-2 text-primary"></i> ویرایش شست قالین (Edit Carpet Wash)</h5>
      </div>

      <!-- Body -->
      <div class="card-body p-4">
        <div class="all-form-element-inner">
          <form action="/dashboard/carpet-wash/{{$wash->id}}" method="post">
            @csrf
            @method('PUT')
            <input type="hidden" value="{{$wash->carpetId}}" name="carpetId">
            <input type="hidden" value="{{$currency}}" id="currency">

            <!-- Section 1: Carpet & Team Info -->
            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نمبر قالین (Carpet No)</label>
                  <input type="text" value="{{$wash->carpet->carpet_no}}" required class="form-control" readonly>
                </div>
              </div>
              
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">تیم شوینده (Washing Team)</label>
                  <select name="team_id" class="form-control" required>
                    @foreach ($washing_team as $team)
                      <option {{($team->id == $wash->team_id ? 'selected' : '')}} value="{{$team->id}}">{{$team->name}}</option>
                    @endforeach
                  </select>
                  @error('team_id') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نمبر شست مرکزی (Central Wash#)</label>
                  <input type="text" name="wash_number" value="{{$wash->wash_number}}" class="form-control">
                  @error('wash_number') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نمبر شست فروشات (Sales Wash#)</label>
                  <input type="text" name="wash_number_sh" value="{{$wash->wash_number_sh}}" class="form-control">
                  @error('wash_number_sh') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
            </div>

            <!-- Section 2: Post-Wash Metrics -->
            <div class="section-divider">
              <span>مشخصات بعد از شست (Post-Wash Metrics)</span>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">طول بعد از شست (Height)</label>
                  <input type="text" id="wheight" name="height" value="{{$wash->height}}" class="form-control">
                  @error('height') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">عرض بعد از شست (Width)</label>
                  <input type="text" id="wwidth" name="width" value="{{$wash->width}}" class="form-control">
                  @error('width') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">مساحت قالین (Area)</label>
                  <input type="text" id="warea" name="area" value="{{$wash->area}}" readonly class="form-control">
                  @error('area') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
            </div>

            <!-- Section 3: Costing & Accounting -->
            <div class="section-divider">
              <span>قیمت گذاری و حسابداری (Costing & Ledger Configuration)</span>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">مصرف شست فی متر مربع دالر (Price per m²)</label>
                  <input type="text" name="price" value="{{$wash->price}}" class="form-control" id="wprice">
                  @error('price') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">واحد پولی (Currency)</label>
                  <select name="currency_code" id="currency_code" class="form-control" required>
                    @foreach($currencies as $curr)
                      <option value="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}" {{ $wash->currency_code == $curr->code ? 'selected' : '' }}>
                        {{ $curr->code }} ({{ $curr->symbol }})
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نرخ تبادله به دالر (FX Rate)</label>
                  <input type="text" name="exchange_rate" id="exchange_rate" value="{{ $wash->exchange_rate ?? $currency }}" class="form-control" required>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right text-info">حساب بدهکار (Debit WIP)</label>
                  <select name="account_id" id="account_id" class="form-control select2" required>
                    @foreach($allowedDebitAccounts as $acc)
                      <option value="{{$acc->id}}" {{ ($existingDebitAccount ?? $defaultAccount) == $acc->id ? 'selected' : '' }}>{{$acc->account_code}} - {{$acc->account_name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right text-danger">حساب بستانکار (Credit Payable)</label>
                  <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2" required>
                    @foreach($allowedCreditAccounts as $acc)
                      <option value="{{$acc->id}}" {{ ($existingCreditAccount ?? ($mapping ? $mapping->credit_account_id : null)) == $acc->id ? 'selected' : '' }}>{{$acc->account_code}} - {{$acc->account_name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">تاریخ شست قالین (Date)</label>
                  <input type="date" name="date" id="repair_date" value="{{$wash->date}}" class="form-control">
                  @error('date') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">قیمت مجموعی به ارز انتخابی (Total Selected)</label>
                  <input type="text" id="af_total_price" name="af_total_price" value="{{$wash->af_total_price}}" readonly class="form-control font-weight-bold text-success">
                  @error('af_total_price') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">معادل به دالر (Base USD)</label>
                  <input type="text" id="total_price" name="total_price" value="{{$wash->total_price}}" readonly class="form-control font-weight-bold text-primary">
                  @error('total_price') <p class="text-danger mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 mb-4">
                <div class="form-group">
                  <label class="">توضیحات (Description)</label>
                  <textarea name="description" id="description" rows="2" class="form-control" placeholder="توضیحات شست قالین...">{{$wash->description}}</textarea>
                </div>
              </div>
            </div>

            <!-- Buttons -->
            <div class="row">
              <div class="col-12">
                <button class="btn btn-submit" type="submit"><i class="fa fa-save mr-1"></i> ذخیره (Save)</button>
                <a href="/dashboard/carpet-wash" class="btn btn-cancel ml-2">انصراف (Cancel)</a>
              </div>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize select2
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }

        // Dynamic exchange rate updates
        $('#currency_code').on('change', function () {
            var selectedOption = $(this).find('option:selected');
            var rawRate = parseFloat(selectedOption.data('rate')) || 1.0;
            var invertedRate = 1.0 / rawRate;
            // Only update rate if it is different, to prevent wiping custom entered rate during initialization
            var currentRate = parseFloat($('#exchange_rate').val()) || 0;
            if (Math.abs(currentRate - invertedRate) > 0.01 || currentRate === 0) {
                $('#exchange_rate').val(invertedRate.toFixed(4)).trigger('change');
            }
        });

        // Recalculate correctly for any currency
        $("#wprice, #wheight, #wwidth, #exchange_rate, #currency_code").on('blur change keyup', function () {
            var unitPrice = parseFloat($('#wprice').val()) || 0;
            var area = parseFloat($('#warea').val()) || 0;
            var exchangeRate = parseFloat($('#exchange_rate').val()) || 1;
            var currencyCode = $('#currency_code').val();

            if (unitPrice > 0 && area > 0) {
                var totalPriceInSelectedCurrency = unitPrice * area;
                var baseAmountUSD = totalPriceInSelectedCurrency;

                if (currencyCode !== 'USD') {
                    baseAmountUSD = totalPriceInSelectedCurrency / exchangeRate;
                }

                $("#af_total_price").val(totalPriceInSelectedCurrency.toFixed(2));
                $("#total_price").val(baseAmountUSD.toFixed(2));
            }
        });

        // Trigger change calculation on load
        $('#wprice').trigger('change');
    });
</script>
@endsection
