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
                <form action="/dashboard/expenses" method="post" id="expense-form">
                @csrf
                <br>
                <div class="row">
                  <div class="col-md-1"></div>
                  <div class="col-md-10">
                    <!-- MAIN INFO -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-inner">
                                <label class="font-weight-bold">تاریخ (Date)</label>
                                <input id="date" name="date" type="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                                <small class="text-danger">@error('date') {{ $message }} @enderror</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-inner">
                                <label class="font-weight-bold">نوع مصرف (Expense Type)</label>
                                <select name="expense_type" id="expense_type" class="form-control select2">
                                    <option>مصرف دفتر</option>
                                    <option>مصرف خانه</option>
                                    <option>مصرف متفرقه</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group-inner">
                                <label class="font-weight-bold">نام دریافت کننده (Recipient Name)</label>
                                <input id="name" name="name" type="text" class="form-control" placeholder="نام شخص یا شرکت...">
                            </div>
                        </div>
                    </div>

                    <!-- FORENSIC CURRENCY SECTION -->
                    <div class="mt-4 p-4" style="background: #eef2f7; border-radius: 15px; border-left: 5px solid #1a237e;">
                        <h5 class="mb-4 text-primary font-weight-bold"><i class="fa fa-money"></i> جزئیات مالی (Forensic Currency)</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">مقدار (Amount)</label>
                                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" class="form-control form-control-lg border-primary" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">واحد پولی (Currency)</label>
                                    <select name="currency_id" id="currency_id" class="form-control form-control-lg border-primary">
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr->id }}" data-code="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}">
                                                {{ $curr->code }} - {{ $curr->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">نرخ تبدیل (FX Rate to USD)</label>
                                    <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control form-control-lg border-primary">
                                </div>
                            </div>
                        </div>

                        <!-- LIVE USD TRUTH PREVIEW -->
                        <div id="usd-preview-box" class="mt-3 p-3 bg-white shadow-sm d-flex justify-content-between align-items-center" style="border-radius: 10px; display:none !important;">
                            <div>
                                <span class="text-muted small">معادل دالر (USD Truth):</span>
                                <h4 class="mb-0 font-weight-bold text-primary" id="usd-amount-display">0.00 $</h4>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-primary px-3 py-2 rounded-pill">Forensic Normalization Active</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-inner mt-4">
                        <label class="font-weight-bold">توضیحات (Description)</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                        <small class="text-danger">@error('description') {{ $message }} @enderror</small>
                    </div>

                    <!-- ACCOUNT OVERRIDES -->
                    <div class="mt-4 p-3" style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 10px;">
                        <h6 class="mb-3 text-muted"><i class="fa fa-university"></i> تنظیمات حسابی (GL Mapping)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="small font-weight-bold">حساب هزینه (Debit Account)</label>
                                <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2">
                                    @foreach($allowedDebitAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold">حساب پرداخت (Credit Account)</label>
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

                    <div class="login-horizental mt-5 d-flex justify-content-end">
                      <button class="btn btn-light px-5 mr-3 rounded-pill" type="button" onclick="window.history.back()">انصراف</button>
                      <button class="btn btn-primary px-5 rounded-pill shadow-lg" type="submit"><span class="fa fa-save mr-2"></span> ثبت مصرف (Save Expense)</button>
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
        $('.select2').select2();

        function updateForensicPreview() {
            var amount = parseFloat($('#amount').val()) || 0;
            var rate = parseFloat($('#exchange_rate').val()) || 0;
            var usdAmount = amount * rate;

            if (amount > 0) {
                $('#usd-preview-box').attr('style', 'border-radius: 10px; display:flex !important; animation: fadeIn 0.5s;');
                $('#usd-amount-display').text(usdAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 4}) + ' $');
            } else {
                $('#usd-preview-box').attr('style', 'display:none !important;');
            }
        }

        $('#currency_id').change(function() {
            var selected = $(this).find(':selected');
            var rate = selected.data('rate');
            $('#exchange_rate').val(rate);
            updateForensicPreview();
        });

        $('#amount, #exchange_rate').on('keyup change', function() {
            updateForensicPreview();
        });

        // Trigger initial rate
        $('#currency_id').trigger('change');

        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 3000);
    });
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .form-control-lg { height: 50px !important; font-size: 1.1rem !important; border-radius: 10px !important; }
    .select2-container--default .select2-selection--single { height: 45px !important; line-height: 45px !important; border-radius: 10px !important; border: 1px solid #ccc !important; }
</style>
@endsection

