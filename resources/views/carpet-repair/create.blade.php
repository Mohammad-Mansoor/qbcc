@extends('dsh.master')
@section('title' , 'Carpets')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4>ثبت ترمیم قالین</h4>
        </div>
        <div class="card-body">
          <form action="/dashboard/carpet-repair" method="post">
            @csrf
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">نمبر قالین</label>
                  <input type="text" value="{{$id->carpet_no}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">تیم ترمیم کننده</label>
                  <input type="text" value="{{$id->kachaee->name}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">کچایی نمبر</label>
                  <input type="text" name="kachaee_number" placeholder="نمبر کچایی را وارد کنید" class="form-control"
                         value="{{$KachaeeNo}}">
                  @error('kachaee_number') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت فی متر</label>
                  <input type="text" name="price" placeholder="مصرف ترمیم به افغانی" class="form-control" id="price"
                         value="{{old('price')}}">
                  <input type="hidden" value="{{$currency}}" id="currency">
                  <input type="hidden" value="{{$id->carpet_id}}" name="carpetId">
                  <input type="hidden" value="{{$id->kachaee_id}}" name="team_id">
                  @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">مساحت قالین</label>
                  <input type="text" id="area" name="area" value="{{$id->area}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label>تاریخ</label>
                  <input type="date" id="repair-date" name="date" placeholder="تاریخ را وارد کنید" class="form-control"
                         value="{{old('date')}}">
                  @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
             
            </div>
            <div class="row mb-4 p-3" style="background: #fdfdfe; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 25px;">
                <div class="col-lg-12">
                    <h6 class="text-primary mb-3"><i class="fa fa-money"></i> تنظیمات عمومی مالی (Global Financial Settings)</h6>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="pull-right">واحد پولی (Currency)</label>
                        <select name="currency_code" id="currency_code" class="form-control" required>
                            <option value="AFN">AFN (؋)</option>
                            <option value="USD">USD ($)</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="pull-right">نرخ تبادله (به دالر)</label>
                        <input type="text" name="exchange_rate" id="exchange_rate" value="{{ $currency }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="text-info pull-right">حساب بدهکار (Debit Account)</label>
                        <select name="account_id" id="account_id" class="form-control select2">
                            @foreach($allowedDebitAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $acc->id == $defaultAccount ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="text-info pull-right">حساب بستانکار (Credit Account)</label>
                        <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2">
                            @foreach($allowedCreditAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموع به افغانی</label>
                  <input type="text" id="af_total_price" name="af_total_price" readonly class="form-control"
                         value="{{old('af_total_price')}}" placeholder="قیمت مجموعی به افغانی">
                </div>
              </div>
  
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموع به دالر</label>
                  <input type="text" id="total_price" name="total_price" readonly class="form-control"
                         value="{{old('total_price')}}" placeholder="قیمت مجموعی به دالر">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group fill">
                  <label>توضیحات</label>
                  <textarea name="description" id="description" rows="2" class="form-control"
                            placeholder="توضیحات ترمیم قالین">{{old('description')}}</textarea>
                  @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <a href="/dashboard/carpet-repair" class="btn btn-warning btn-sm" type="reset">انصراف</a>
                  <button class="btn btn-primary marginx btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                </div>
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
    $(document).ready(function() {
        function calculatePrices() {
            let price = parseFloat($('#price').val()) || 0;
            let area = parseFloat($('#area').val()) || 0;
            let rate = parseFloat($('#exchange_rate').val()) || 1;
            let currency = $('#currency_code').val();
            
            let total = price * area;
            
            if (currency === 'USD') {
                $('#total_price').val(total.toFixed(2));
                $('#af_total_price').val((total * rate).toFixed(2));
            } else {
                $('#af_total_price').val(total.toFixed(2));
                $('#total_price').val((total / rate).toFixed(2));
            }
        }

        $('#price, #exchange_rate, #currency_code').on('input change', calculatePrices);
        $('.select2').select2();
        $('#account_id').select2();
        $('#override_credit_account_id').select2();
        $('#currency_code').select2();
    });
</script>
@endsection