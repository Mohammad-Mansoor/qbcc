@extends('dsh.master')
@section('title', 'ویرایش ترمیم قالین')
@section('content')

  <style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
      background: white;
      border: 1px solid var(--QBIC-border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-soft);
      margin-bottom: 30px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .glass-header {
      background: var(--QBIC-surface);
      padding: 20px 25px;
      border-bottom: 1px solid var(--QBIC-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .glass-header h4 {
      margin: 0;
      font-weight: 700;
      color: var(--QBIC-primary);
      font-size: 1.2rem;
    }

    .custom-input {
      border-radius: 8px;
      border: 2px solid #e8f5e9;
      padding: 10px 15px;
      transition: all 0.2s;
    }

    .custom-input:focus {
      border-color: #43a047;
      box-shadow: 0 0 0 0.2rem rgba(67, 160, 71, 0.1);
    }

    .custom-input[readonly] {
      background-color: #f8fafc;
      border-color: #e2e8f0;
      color: #64748b;
    }

    .form-section-title {
      color: #2e7d32;
      font-weight: 700;
      border-bottom: 2px solid #e8f5e9;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .btn-premium {
      border-radius: 8px;
      font-weight: 600;
      padding: 10px 25px;
      transition: all 0.2s;
    }

    .finance-box {
      background: #fdfdfe;
      border: 1px dashed #cfd8dc;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }
  </style>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="glass-card">
        <div class="glass-header">
          <h4><i class="fa fa-pencil text-primary mr-2"></i> ویرایش ترمیم قالین (Edit Repair)</h4>
        </div>
        <div class="card-body p-4">
          <form action="/dashboard/carpet-repair/{{$carpetRepair->id}}" method="post">
            @csrf
            @method('PUT')

            <h6 class="form-section-title"><i class="fa fa-info-circle mr-2"></i> اطلاعات ترمیم (Repair Details)</h6>
            <div class="row mb-4">
              <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">نمبر قالین</label>
                  <input type="text" value="{{$carpetRepair->carpet->carpet_no}}" readonly
                    class="form-control custom-input font-weight-bold">
                  @error('area') <small class="text-danger">{{trans('message.' . $message)}}</small> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">تیم ترمیم کننده</label>
                  <input type="text" value="{{$carpetRepair->team->name}}" readonly
                    class="form-control custom-input font-weight-bold">
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">کچایی نمبر</label>
                  <input type="text" name="kachaee_number" placeholder="نمبر کچایی" class="form-control custom-input"
                    value="{{$carpetRepair->kachaee_number}}">
                  @error('kachaee_number') <small class="text-danger">{{trans('message.' . $message)}}</small> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">مساحت قالین (m²)</label>
                  <input type="text" id="area" name="area" value="{{$carpetRepair->carpet->area}}" readonly
                    class="form-control custom-input font-weight-bold text-primary" style="direction: ltr;">
                  @error('area') <small class="text-danger">{{trans('message.' . $message)}}</small> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">قیمت فی متر</label>
                  <input type="number" step="any" name="price" value="{{$carpetRepair->price}}"
                    class="form-control custom-input" id="price" required>
                  <input type="hidden" value="{{$currency}}" id="currency">
                  <input type="hidden" name="old_price" value="{{$carpetRepair->total_price}}">
                  <input type="hidden" name="af_old_price" value="{{$carpetRepair->af_total_price}}">
                  <input type="hidden" value="{{$carpetRepair->carpetId}}" name="carpetId">
                  <input type="hidden" value="{{$carpetRepair->team_id}}" name="team_id">
                  @error('price') <small class="text-danger">{{trans('message.' . $message)}}</small> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">تاریخ</label>
                  <input type="date" name="date" id="repair_date" value="{{$carpetRepair->date}}"
                    class="form-control custom-input">
                  @error('date') <small class="text-danger">{{trans('message.' . $message)}}</small> @enderror
                </div>
              </div>
            </div>

            <div class="finance-box shadow-sm">
              <div class="row">
                <div class="col-lg-12">
                  <h6 class="text-success mb-4 font-weight-bold"><i class="fa fa-money mr-2"></i> تنظیمات عمومی مالی
                    (Global Financial Settings)</h6>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">واحد پولی (Currency)</label>
                    <select name="currency_code" id="currency_code" class="form-control custom-input" required>
                      @foreach($currencies as $curr)
                        <option value="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}" {{ $carpetRepair->currency_code == $curr->code ? 'selected' : '' }}>
                          {{ $curr->code }} ({{ $curr->symbol }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نرخ تبادله (به دالر)</label>
                    <input type="number" step="any" name="exchange_rate" id="exchange_rate"
                      value="{{ $carpetRepair->exchange_rate ?? $currency }}" class="form-control custom-input bg-light"
                      required>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="form-group">
                    <label class="text-info font-weight-bold small">حساب بدهکار (Debit WIP)</label>
                    <select name="account_id" id="account_id" class="form-control select2">
                      @foreach($allowedDebitAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ $acc->id == ($carpetRepair->override_debit_account_id ?? $defaultAccount) ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="form-group">
                    <label class="text-danger font-weight-bold small">حساب بستانکار (Credit Payable)</label>
                    <select name="override_credit_account_id" id="override_credit_account_id"
                      class="form-control select2">
                      @foreach($allowedCreditAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ $acc->id == ($carpetRepair->override_credit_account_id ?? ($mapping ? $mapping->credit_account_id : null)) ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row mt-3 pt-3 border-top">
                <div class="col-lg-6 col-md-6">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small" id="local_total_label">قیمت مجموع (Local
                      Total)</label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text bg-light"
                          id="local_total_symbol">؋</span></div>
                      <input type="text" id="af_total_price" name="af_total_price"
                        value="{{$carpetRepair->af_total_price}}" readonly
                        class="form-control font-weight-bold text-success" style="direction: ltr; font-size: 1.1rem;">
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">قیمت مجموع به دالر (Total USD)</label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text bg-light">$</span></div>
                      <input type="text" id="total_price" name="total_price" value="{{$carpetRepair->total_price}}"
                        readonly class="form-control font-weight-bold text-success"
                        style="direction: ltr; font-size: 1.1rem;">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small">توضیحات (Description)</label>
                  <textarea name="description" id="description" rows="3" class="form-control custom-input"
                    placeholder="توضیحات مربوط به ترمیم قالین">{{$carpetRepair->description}}</textarea>
                  @error('description') <small class="text-danger">{{trans('message.' . $message)}}</small> @enderror
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-lg-12 text-right">
                <button class="btn btn-primary btn-premium shadow-sm" type="submit"><i class="fa fa-save mr-2"></i>
                  بروزرسانی و اصلاح سند حسابداری</button>
                <a href="/dashboard/carpet-repair" class="btn btn-light btn-premium shadow-sm ml-2">انصراف</a>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-plugins')
  <script>
    $(document).ready(function () {
      function calculatePrices() {
        let price = parseFloat($('#price').val()) || 0;
        let area = parseFloat($('#area').val()) || 0;
        let rate = parseFloat($('#exchange_rate').val()) || 1;
        let currency = $('#currency_code').val();

        let total = price * area;

        // The local total is ALWAYS price * area
        $('#af_total_price').val(total.toFixed(2));

        if (currency === 'USD') {
          $('#total_price').val(total.toFixed(2));
          if (rate !== 1) {
            $('#exchange_rate').val(1);
          }
        } else {
          $('#total_price').val((total / rate).toFixed(2));
        }
      }

      $('#price, #exchange_rate').on('input change', calculatePrices);

      function updateLabels() {
        let selectedOption = $('#currency_code').find('option:selected');
        let code = $('#currency_code').val();
        let textMatch = selectedOption.text().match(/\((.*?)\)/);
        let symbol = textMatch ? textMatch[1] : code;

        $('#local_total_label').text('قیمت مجموع به ' + code + ' (Total ' + code + ')');
        $('#local_total_symbol').text(symbol);
      }

      $('#currency_code').on('change', function () {
        let selectedOption = $(this).find('option:selected');
        let rate = selectedOption.data('rate');
        let code = $(this).val();

        updateLabels();

        // The DB stores rates as "1 Local = X USD". The backend expects "1 USD = X Local" because it divides.
        // Therefore, we must ALWAYS display the inverse rate (1 / rate) in the form for correct division.
        if (rate > 0) {
          $('#exchange_rate').val((1 / rate).toFixed(6));
        } else {
          $('#exchange_rate').val(1);
        }

        calculatePrices();
      });

      $('.select2').select2();
      $('#account_id').select2();
      $('#override_credit_account_id').select2();
      $('#currency_code').select2();

      // On load, update labels and calculate but DO NOT overwrite historical rates
      updateLabels();
      calculatePrices();
    });
  </script>
@endsection