@extends('dsh.master')
@section('title' , 'فورم ایجاد مصارف')
@section('content')
  <!-- navbar -->
  
  <div class="sparkline12-list">
    <div class="sparkline12-hd">
      <div class="main-sparkline12-hd tx-xs-center">
        <h1>فورم مصرف جدید</h1>
        @if(session("status"))
          <div class="alert alert-success status" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center">{{session('status')}}</p>
          </div>
        
        @endif
        @if(session("error"))
          
          <div class="alert alert-danger status" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center">{{session('error')}}</p>
          </div>
        
        @endif
      </div>
    </div>
  <!-- @if ($errors->any())
    @foreach ($errors->all() as $error)
      <div>{{$error}}</div>
            @endforeach
  @endif -->
    <div class="sparkline12-graph">
      <div class="basic-login-form-ad">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="all-form-element-inner">
              <form action="/dashboard/expenses" method="post">
                @csrf
                <br>
                <div class="row">
                  <div class="col-md-4"></div>
                  <div class="col-md-8">
                    <div class="form-group-inner">
                      <div class="row">
                        <div class="col-lg- col-md-9 col-sm-9 col-xs-12">
                          <input id="name" name="name" type="text" class="form-control quantity">
                          <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                          <label class="">نام دریافت کننده</label>
                        </div>
                      </div>
                    </div>
                    <div class="form-group-inner">
                      <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                          <select name="expense_type" id="expense_type" class="form-control">
                            
                            <option>مصرف دفتر</option>
                            <option>مصرف خانه</option>
                          
                          </select>
                          <small class="text-danger">@error('expense_type') {{ __('message.'.$message) }}@enderror
                          </small>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                          <label class="">نوع مصرف</label>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group-inner">
                      <div class="row">
                        <div class="col-lg- col-md-9 col-sm-9 col-xs-12">
                          <input id="date" name="date" type="date" class="form-control quantity">
                          <small class="text-danger">@error('date') {{ __('message.'.$message) }} @enderror</small>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                          <label class="">تاریخ</label>
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="form-group-inner">
                      <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <input value="AF" dir="ltr" type="text" class="form-control" readonly>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                          <input type="text" dir="ltr" name="amount_af" value="{{ old('amount') }}" id="fp"
                                 class="form-control">
                          <input type="text" name="amount" dir="ltr" id="mainP" value="{{ old('amount') }}"
                                 class="form-control">
                          <small class="text-danger">@error('amount') {{ __('message.'.$message) }} @enderror</small>
                        </div>
                        <input type="hidden" value="{{$currency}}" id="currency">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                          <label class="">مقدار</label>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group-inner">
                      <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                          <textarea id="description" name="description"
                                    class="form-control">{{ old('description') }}</textarea>
                          <small class="text-danger">@error('description') {{ __('message.'.$message) }}@enderror
                          </small>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                          <label class="">توضیحات</label>
                        </div>
                      </div>
                    </div>

                    <!-- ACCOUNT OVERRIDES -->
                    <div class="mt-4 p-3" style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
                        <h6 class="mb-3 text-muted"><i class="fa fa-university"></i> تنظیمات حسابی (Accounting)</h6>
                        <div class="form-group-inner">
                            <div class="row">
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control">
                                        @foreach($allowedDebitAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label class="">حساب هزینه (Debit)</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-inner">
                            <div class="row">
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                    <select name="override_credit_account_id" id="override_credit_account_id" class="form-control">
                                        @foreach($allowedCreditAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label class="">حساب پرداخت (Credit)</label>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info py-1 mt-2" style="font-size: 0.8rem;">
                            <i class="fa fa-info-circle"></i> به صورت خودکار حساب‌های پیش‌فرض انتخاب شده‌اند.
                        </div>
                    </div>
                    <br>
                    
                    <div class="login-horizental cancel-wp ">
                      <button class="btn btn-white" type="button">انصراف</button>
                      <button class="btn btn-primary " type="submit"><span class="fa fa-save"></span> ذخیره</button>
                    </div>
                  </div>
                
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#override_debit_account_id').select2();
        $('#override_credit_account_id').select2();
        $('#expense_type').select2();

        // Calculation logic
        $("#fp").keyup(function () {
            var fp = $('#fp').val();
            var c = $('#currency').val();
            var mainP = fp / c;
            var total = parseFloat(mainP).toFixed(2);
            if (isNaN(total)) {
                $("#mainP").val();
            } else {
                $("#mainP").val(total);
            }
        });

        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 2000);
    });
</script>
@endsection

