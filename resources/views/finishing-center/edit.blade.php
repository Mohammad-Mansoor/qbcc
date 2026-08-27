@extends('dsh.master')
@section('title' , 'Editing Works')
@section('content')
<!-- form -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h4 class="pull-right">ویرایش تیاری قالین</h4>
            </div>
            <div class="card-body">
                <div class="all-form-element-inner">
                    <form action="/dashboard/finishing-center/{{$finish->id}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3 p-3" style="background: #fdfdfe; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-lg-12">
                                <h6 class="text-primary mb-3"><i class="fa fa-money"></i> تنظیمات پولی و مالی (Financial Settings)</h6>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="pull-right" style="font-weight: 600;">واحد پولی (Currency)</label>
                                    <select name="currency_code" id="currency_code" class="form-control select2" required>
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}" {{ ($finish->currency_code ?? 'USD') == $curr->code ? 'selected' : '' }}>
                                                {{ $curr->code }} ({{ $curr->symbol }}) - {{ $curr->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="pull-right" style="font-weight: 600;">نرخ تبادله (به دالر)</label>
                                    <input type="number" step="any" name="exchange_rate" id="exchange_rate" value="{{ $finish->exchange_rate ?? 1.0 }}" class="form-control bg-light" required>
                                </div>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">نمبر قالین</label>
                                    <input type="text"  value="{{$finish->carpet->carpet_no}}" readonly
                                           class="form-control">

                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">تیاری نمبر</label>
                                    <input type="text" name="finish_number" value="{{$finish->finish_number}}"
                                           class="form-control">
            
                                    <small class="text-danger">@error('finish_number') {{ __('message.'.$message) }} @enderror</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">تیم تیاری</label>
                                    <input type="hidden" value="{{$team->id}}" name="oldTeam">
                                    <select name="team_id" id="team_id" class="form-control">
                                        <option value="{{$team->id}}" selected>{{$team->name}}</option>
                                        @foreach($teams as $team)
                                            <option value="{{$team->id}}">{{$team->name}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">دسته بندی تیم تیاری</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="{{$category->id}}" selected>{{$category->category}}</option>
                                        @foreach($team_categories as $category)
                                            <option value="{{$category->id}}">{{$category->category}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">مصرف فی متر یا متر مربع</label>
                                    <input type="text"
                                           class="form-control" name="price_af" id="fp" value="{{$mainPrice_af}}">
                                    <input type="hidden" value="{{$finish->carpetId}}"
                                           name="carpetId">
                                    <input type="hidden" id="mainP" value="{{$mainPrice}}" name="price">
                                    <input type="hidden" id="currency" value="{{$currency}}">
                                    <input type="hidden" value="{{$finish->price}}" name="old_price">
                                    <input type="hidden" value="{{$finish->price_af}}" name="af_old_price">
                                    @error('price') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">تاریخ تیاری قالین</label>
                                    <input type="text" id="repair-date" name="date"
                                           class="form-control"
                                           value="{{$finish->date}}">
                                    @error('date') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">طول قالین</label>
                                    <input type="text" name="height" readonly value="{{$newCarpet->height}}" placeholder="مصرف تیاری"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">مساحت قالین</label>
                                    <input type="text" name="height" readonly value="{{$newCarpet->area}}" placeholder="مصرف تیاری"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="">شرح</label>
                                    <textarea name="description" id="description" rows="1"
                                              class="form-control">{{$finish->description}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="text-info pull-right">حساب بدهکار (Debit Account)</label>
                                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2">
                                        @foreach($allowedDebitAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ (($finish->debit_account_id ?? ($mapping ? $mapping->debit_account_id : null)) == $acc->id) ? 'selected' : '' }}>
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
                                            <option value="{{ $acc->id }}" {{ (($finish->credit_account_id ?? ($mapping ? $mapping->credit_account_id : null)) == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                              <div class="form-group fill" style="margin-top: 20px;">
                                <label>آیا مراحل تیاری به کلی تمام شده است؟</label>
                                <input type="radio" class="finished" name="finished" value="1"
                                       {{$finish->carpet->status == 5?'checked':''}} style="margin-right: 20px;padding:5px;"><span
                                        style="margin:0 10px">بلی</span>
                                <input type="radio" class="finished" name="finished" value="0"
                                       {{$finish->carpet->status != 5?'checked':''}} style="margin-right: 20px;padding:5px;"><span
                                        style="margin:0 10px">نخیر</span>
                              </div>
                            </div>
                        </div>
                        <div class="row" id="warehouse_transfer_section" style="margin-bottom: 20px;">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                              <div class="form-group fill">
                                <label class="pull-right">انبار نهایی (Target Warehouse)</label>
                                <select name="warehouse_id" id="warehouse_id" class="form-control select2">
                                  @foreach($warehouses as $wh)
                                    <option value="{{$wh->id}}" {{ ($finish->warehouse_id ?? $finish->carpet->warehouse_id) == $wh->id ? 'selected' : '' }}>
                                      {{$wh->name}}
                                    </option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <button class="btn btn-warning btn-sm"><a href="/dashboard/finishing-center">
                                            انصراف </a></button>
                                    <button class="btn btn-primary btn-sm marginx" type="submit"> <span
                                                 class="fa fa-save"></span> ذخیره</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>

@section('scripts')
  <script>
      $(document).ready(function() {
          $('#override_debit_account_id').select2({ width: '100%' });
          $('#override_credit_account_id').select2({ width: '100%' });
          $('#warehouse_id').select2({ width: '100%' });
          $('#currency_code').select2({ width: '100%' });

          function updateCurrencyUI() {
              let selectedOption = $('#currency_code').find('option:selected');
              let code = $('#currency_code').val();
              let rate = parseFloat(selectedOption.data('rate')) || 1.0;
              
              if (code === 'USD') {
                  $('#exchange_rate').val(1.0);
              } else {
                  if (rate > 0) {
                      $('#exchange_rate').val((1 / rate).toFixed(6));
                  } else {
                      $('#exchange_rate').val(1.0);
                  }
              }
              
              $('#fp').attr('placeholder', 'مصرف به ' + code);
          }
          
          $('#currency_code').on('change', updateCurrencyUI);

          function toggleWarehouseSection() {
              $('#warehouse_transfer_section').show();
              $('#warehouse_id').attr('required', 'required');
          }
          
          $('input[name="finished"]').on('change', toggleWarehouseSection);
          toggleWarehouseSection();

          $('form').on('submit', function () {
              var $btn = $(this).find('button[type="submit"]');
              $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> لطفا صبر کنید...');
          });
      });
  </script>
@endsection
@endsection