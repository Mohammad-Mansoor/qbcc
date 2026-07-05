@extends('dsh.master')
@section('title', 'Carpets Wash')
@section('content')

  <style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
      background: white;
      border: 1px solid var(--QBIC-border, #e2e8f0);
      border-radius: 16px;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
      margin-bottom: 30px;
      overflow: hidden;
    }

    .glass-header {
      background: #f8fafc;
      padding: 20px 25px;
      border-bottom: 1px solid var(--QBIC-border, #e2e8f0);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .glass-header h4 {
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
      border-color: var(--QBIC-primary, #3b82f6);
      background: #fff;
    }

    .form-control[readonly] {
      background: #f8fafc;
      border-color: #f1f5f9;
      color: #64748b;
    }

    .btn-submit {
      background: var(--QBIC-primary, #3b82f6);
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
          <h4><i class="fa fa-save mr-2 text-primary"></i> ثبت شست قالین (Record Carpet Wash)</h4>
          <div id="system_back_button"></div>
        </div>

        <!-- Body -->
        <div class="card-body p-4">

          <form action="/dashboard/carpet-wash" method="post">
            @csrf
            <input type="hidden" name="wash_id" value="{{$carpet_wash->id}}">
            <input type="hidden" name="carpetId" value="{{$carpet_wash->carpet->carpet_id}}">
            <input type="hidden" name="dollar_rate" value="{{$currency}}">
            <input type="hidden" value="{{$currency}}" id="currency">

            <!-- Section 1: Carpet & Team Info (Read-only) -->
            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نمبر قالین (Carpet No)</label>
                  <input type="text" value="{{$carpet_wash->carpet->carpet_no}}" required class="form-control" readonly>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">طول قبلی (Original Height)</label>
                  <input type="text" value="{{$carpet_wash->carpet->height}} m" required class="form-control" readonly>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">عرض قبلی (Original Width)</label>
                  <input type="text" value="{{$carpet_wash->carpet->width}} m" required class="form-control" readonly>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">تیم شوینده (Washing Team)</label>
                  <input type="text" name="team_id" required readonly class="form-control"
                    value="{{$carpet_wash->washing_team->name}}">
                  @error('team_id') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نمبر شست مرکزی (Central Wash#)</label>
                  <input type="text" name="wash_number" required readonly class="form-control"
                    value="{{$carpet_wash->wash_number}}">
                  @error('wash_number') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>
            </div>

            <!-- Section 2: Post-Wash Metrics (Writable) -->
            <div class="section-divider">
              <span>مشخصات بعد از شست (Post-Wash Metrics)</span>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نمبر شست جدید فروشات (New Sales Wash#)</label>
                  <div class="input-group">
                    <select name="wash_number_sh" id="wash_number_sh" required class="form-control select2">
                      <option value="">-- انتخاب نمبر شست --</option>
                      @foreach($openBatches as $batch)
                        <option value="{{ $batch->reference_number }}">{{ $batch->reference_number }}</option>
                      @endforeach
                    </select>
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success" id="btn_generate_wash_number" title="ایجاد نمبر جدید">
                        <i class="fa fa-plus"></i> ایجاد
                      </button>
                    </div>
                  </div>
                  @error('wash_number_sh') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">طول بعد از شست (Height)</label>
                  <input type="text" id="wheight" name="height" required class="form-control" placeholder="طول قالین">
                  @error('height') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">عرض بعد از شست (Width)</label>
                  <input type="text" id="wwidth" name="width" required class="form-control" placeholder="عرض قالین">
                  @error('width') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">مساحت قالین (Area)</label>
                  <input type="hidden" id="original_area" value="{{$carpet_wash->carpet->area}}">
                  <input type="text" id="warea" name="area" required readonly class="form-control mb-1"
                    placeholder="مساحت محاسبه شده">
                  <small id="area_difference_msg" class="font-weight-bold d-block"></small>
                  @error('area') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>
            </div>

            <!-- Section 3: Costing & Accounting (Writable) -->
            <div class="section-divider">
              <span>قیمت گذاری و حسابداری (Costing & Ledger Configuration)</span>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">مصرف شست فی متر مربع دالر (Price per m²)</label>
                  <input type="text" name="price" placeholder="مصرف شست فی متر" required class="form-control" id="wprice"
                    value="{{old('price')}}">
                  @error('price') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">واحد پولی (Currency)</label>
                  <select name="currency_code" id="currency_code" class="form-control" required>
                    @foreach($currencies as $curr)
                      <option value="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}" {{ $curr->code == 'AFN' ? 'selected' : '' }}>
                        {{ $curr->code }} ({{ $curr->symbol }})
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">نرخ تبادله به دالر (FX Rate)</label>
                  <input type="text" name="exchange_rate" id="exchange_rate" value="{{ $currency }}" class="form-control"
                    required>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right text-info">حساب بدهکار (Debit WIP)</label>
                  <select name="account_id" id="account_id" class="form-control select2" required>
                    @foreach($allowedDebitAccounts as $acc)
                      <option value="{{$acc->id}}" {{ $acc->id == $defaultAccount ? 'selected' : '' }}>{{$acc->account_code}}
                        - {{$acc->account_name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right text-danger">حساب بستانکار (Credit Payable)</label>
                  <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2"
                    required>
                    @foreach($allowedCreditAccounts as $acc)
                      <option value="{{$acc->id}}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>{{$acc->account_code}} - {{$acc->account_name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">انبار نهایی (Target Warehouse)</label>
                  <select name="warehouse_id" id="warehouse_id" class="form-control select2" required>
                    @foreach($warehouses as $wh)
                      <option value="{{$wh->id}}" {{ (old('warehouse_id', $carpet_wash->carpet->warehouse_id) == $wh->id) ? 'selected' : '' }}>
                        {{$wh->name}}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">تاریخ شست قالین (Date)</label>
                  <input type="date" name="date" required class="form-control" value="{{old('date', date('Y-m-d'))}}">
                  @error('date') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">قیمت مجموعی به ارز انتخابی (Total Selected)</label>
                  <input type="text" id="af_total_price" name="af_total_price" required readonly
                    class="form-control font-weight-bold text-success">
                  @error('af_total_price') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>

              <div class="col-md-3 col-sm-6 mb-3">
                <div class="form-group">
                  <label class="pull-right">معادل به دالر (Base USD)</label>
                  <input type="text" id="total_price" name="total_price" required readonly
                    class="form-control font-weight-bold text-primary">
                  @error('total_price') <p class="text-danger mt-1">{{trans('message.' . $message)}}</p> @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 mb-4">
                <div class="form-group">
                  <label class="">توضیحات (Description)</label>
                  <textarea name="description" id="description" rows="2" required class="form-control"
                    placeholder="توضیحات شست قالین..."></textarea>
                </div>
              </div>
            </div>

            <!-- Buttons -->
            <div class="row">
              <div class="col-12">
                <button class="btn btn-submit" type="submit"><i class="fa fa-save mr-1"></i> ذخیره (Save)</button>
                <a href="/dashboard/carpet-wash/wash-numbers/{{$carpet_wash->team_id}}" class="btn btn-cancel ml-2">انصراف
                  (Cancel)</a>
              </div>
            </div>

          </form>

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

      // Prevent double submission
      $('form').on('submit', function () {
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> لطفا صبر کنید...');
      });

      // Dynamic exchange rate updates
      $('#currency_code').on('change', function () {
        var selectedOption = $(this).find('option:selected');
        var rawRate = parseFloat(selectedOption.data('rate')) || 1.0;
        var invertedRate = 1.0 / rawRate;
        $('#exchange_rate').val(invertedRate.toFixed(4)).trigger('change');
      });

      // Recalculate correctly for any currency
      $("#wprice, #wheight, #wwidth, #exchange_rate, #currency_code").on('blur change keyup', function () {
        var h = parseFloat($('#wheight').val()) || 0;
        var w = parseFloat($('#wwidth').val()) || 0;
        var newArea = h * w;
        if (newArea > 0) {
            $('#warea').val(newArea.toFixed(4));
        }

        var area = parseFloat($('#warea').val()) || 0;
        var originalArea = parseFloat($('#original_area').val()) || 0;

        if (area > 0 && originalArea > 0) {
            var diff = area - originalArea;
            if (diff < -0.001) {
                $('#area_difference_msg').html('<i class="fa fa-arrow-down"></i> مساحت از دست رفته: ' + Math.abs(diff).toFixed(3) + ' متر مربع (Lost)').removeClass('text-success text-muted').addClass('text-danger');
            } else if (diff > 0.001) {
                $('#area_difference_msg').html('<i class="fa fa-arrow-up"></i> مساحت اضافه شده: ' + diff.toFixed(3) + ' متر مربع (Gained)').removeClass('text-danger text-muted').addClass('text-success');
            } else {
                $('#area_difference_msg').html('<i class="fa fa-minus"></i> بدون تغییر مساحت (No Lost)').removeClass('text-danger text-success').addClass('text-muted');
            }
        } else {
            $('#area_difference_msg').html('');
        }

        var unitPrice = parseFloat($('#wprice').val()) || 0;
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

      // Trigger change once on load to populate initial exchange rate
      $('#currency_code').trigger('change');

      // AJAX Generate Wash Number
      $('#btn_generate_wash_number').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
          url: '/dashboard/batches/wash',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            team_id: '{{ $carpet_wash->team_id }}'
          },
          success: function (response) {
            if (response.success && response.batch) {
              var newRef = response.batch.reference_number;
              if ($('#wash_number_sh option[value="' + newRef + '"]').length === 0) {
                var newOption = new Option(newRef, newRef, true, true);
                $('#wash_number_sh').append(newOption).trigger('change');
              } else {
                $('#wash_number_sh').val(newRef).trigger('change');
              }
              swal("موفقیت", "نمبر شست جدید با موفقیت ایجاد و انتخاب گردید: " + newRef, "success");
            } else {
              swal("خطا", "ایجاد نمبر با خطا مواجه شد.", "error");
            }
          },
          error: function () {
            swal("خطا", "ارتباط با سرور برقرار نشد.", "error");
          },
          complete: function () {
            $btn.prop('disabled', false).html('<i class="fa fa-plus"></i> ایجاد');
          }
        });
      });
    });
  </script>
@endsection