@extends('dsh.master')

@section('content')
  @php($lockDate = \DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value'))
  <br>
  <div class="row" id="customer_payment">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(session("status"))
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="table-responsive">
                <table class="table table-sm table-hover text-left">
                  <thead>
                  
                  </thead>
                  <tbody>
                  
                  <tr>
                    <td><b>نام</b></td>
                    <td>{{$customer->name}}</td>
                  </tr>
                  <tr>
                    <td><b>ادرس</b></td>
                    <td> {{$customer->company_address}}</td>
                  </tr>
                  <tr>
                    <td><b>شماره تماس</b></td>
                    
                    <td style="direction: ltr;">
                      {{$customer->phone}}
                      <i class="fa fa-phone"></i>
                    </td>
                  </tr>
                  
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h4>ACCOUNT #: {{$customer->id}}</h4>
            </div>
          </div>
          <div class="all-form-element-inner hideOnPrint">
            @if(!$paymentEdit)
              <style>
                .premium-form-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
                .form-section-title { font-size: 1.1rem; font-weight: 700; color: #2c3e50; border-bottom: 2px solid #f8f9fa; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; }
                .form-section-title i { margin-left: 10px; color: #3498db; }
                .field-label { font-weight: 600; color: #34495e; margin-bottom: 5px; display: block; }
                .field-explanation { font-size: 0.75rem; color: #7f8c8d; display: block; margin-top: 2px; line-height: 1.4; }
                .custom-input { border-radius: 8px; border: 1.5px solid #dee2e6; padding: 10px 15px; transition: all 0.3s; }
                .custom-input:focus { border-color: #3498db; box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.1); }
                .normalization-box { background: linear-gradient(135deg, #e0f7fa 0%, #e1f5fe 100%); border-radius: 12px; border: 1px solid #b3e5fc; transition: all 0.3s; }
              </style>

              <form action="/dashboard/customer-payments" method="post" class="p-4">
                @csrf
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                
                <div class="row">
                  <!-- Column 1: Payment Details -->
                  <div class="col-lg-4 col-md-6">
                    <h6 class="form-section-title"><i class="fa fa-money"></i> جزئیات پرداخت (Payment Details)</h6>
                    
                    <div class="form-group mb-4">
                      <label class="field-label">مقدار پول (Amount)</label>
                      <input type="number" step="0.01" name="amount" value="{{old('amount')}}" class="form-control custom-input h5" placeholder="0.00">
                      <small class="field-explanation text-right">مبلغ پرداختی را وارد کنید. سیستم به صورت خودکار آن را به دالر تبدیل می‌کند.</small>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">نوع پول (Currency)</label>
                      <select name="currency_id" id="currency_id" class="form-control custom-input">
                        @foreach($currencies as $curr)
                          <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}">
                            {{ $curr->code }} - {{ $curr->name }}
                          </option>
                        @endforeach
                      </select>
                      <small class="field-explanation text-right">واحد پولی که مشتری با آن پرداخت کرده است را انتخاب کنید.</small>
                    </div>

                    <div class="form-group mb-4" id="exchange_rate_container">
                      <label class="field-label">نرخ تبادله به دالر (Exchange Rate to USD)</label>
                      <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" value="{{old('exchange_rate')}}" class="form-control custom-input h5 font-weight-bold" placeholder="1.00000000">
                      <small class="field-explanation text-right">ارزش ۱ واحد از این اسعار را به دالر وارد کنید.</small>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">نوع معامله (Transaction Type)</label>
                      <select name="type" id="" class="form-control custom-input font-weight-bold">
                        <option value="رسید" class="text-success">رسید (Payment Received)</option>
                        <option value="گرفت" class="text-danger">گرفت (Payment Sent/Adjustment)</option>
                      </select>
                      <small class="field-explanation text-right">آیا این پول از مشتری دریافت شده (رسید) یا به او پرداخت شده است؟</small>
                    </div>
                  </div>

                  <!-- Column 2: Reference & Dates -->
                  <div class="col-lg-4 col-md-6">
                    <h6 class="form-section-title"><i class="fa fa-file-text-o"></i> اسناد و تاریخ (Reference & Date)</h6>

                    <div class="form-group mb-4">
                      <label class="field-label">انوایس نمبر (Invoice Reference)</label>
                      <select name="invoice_number" id="" class="form-control custom-input">
                        <option value="نقد">نقد (Cash Payment)</option>
                        @foreach($invoice_numbers as $ch)
                          <option value="{{$ch->invoice_no}}">انوایس شماره: {{$ch->invoice_no}}</option>
                        @endforeach
                      </select>
                      <small class="field-explanation text-right">اگر پرداخت مربوط به انوایس خاصی است، آن را انتخاب کنید.</small>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">تاریخ (Transaction Date)</label>
                      <input type="date" name="date" class="form-control custom-input" value="{{ date('Y-m-d') }}">
                      <small class="field-explanation text-right">تاریخ واقعی معامله را وارد کنید.</small>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">توضیحات (Description)</label>
                      <textarea name="description" id="description" rows="1" class="form-control custom-input" placeholder="مثلاً: بابت تسویه حساب ماه حمل"></textarea>
                      <small class="field-explanation text-right">جزئیات بیشتر در مورد این پرداخت را اینجا بنویسید.</small>
                    </div>
                  </div>

                  <!-- Column 3: Live Preview & Action -->
                  <div class="col-lg-4 col-md-12">
                    <h6 class="form-section-title"><i class="fa fa-calculator"></i> محاسبه آنی (Live Truth Preview)</h6>
                    
                    <div id="normalization_preview_container" class="normalization-box p-4 text-center mb-4 shadow-sm" style="display:none;">
                      <div class="text-muted small mb-2">ارزش نهایی در دفتر کل (USD Value)</div>
                      <div class="h2 font-weight-bold text-primary mb-0">$<span id="final_usd_value">0.00</span></div>
                      <div class="mt-2 badge badge-pill badge-primary px-3">سیستم از نرخ رسمی استفاده می‌کند</div>
                    </div>

                    <div class="row p-3 mb-3 mx-1" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px;">
                        <div class="col-lg-12 mb-3">
                            <label class="field-label text-info"><i class="fa fa-university"></i> حسابات مالی (Accounting)</label>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <select name="override_debit_account_id" id="override_debit_account_id" class="form-control form-control-sm">
                                @foreach($allowedDebitAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                        بدهکار: {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <select name="override_credit_account_id" id="override_credit_account_id" class="form-control form-control-sm">
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                        بستانکار: {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                             <button class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold" type="submit"><i class="fa fa-check-circle"></i> ثبت نهایی معامله</button>
                        </div>
                    </div>
                  </div>
                </div>

                <!-- Invoice Allocation Section -->
                <div id="invoice_allocation_section" style="display:none; margin-top: 20px; width: 100%;" class="mt-4">
                    <h6 class="form-section-title text-success"><i class="fa fa-list"></i> تخصیص به انوایس‌های باقی‌مانده (Invoice Match)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>نمبر انوایس</th>
                                    <th>تاریخ</th>
                                    <th>مجموع</th>
                                    <th>باقیمانده</th>
                                    <th width="150">مقدار تادیه</th>
                                </tr>
                            </thead>
                            <tbody id="invoice_list_body"></tbody>
                        </table>
                    </div>
                </div>
              </form>
            @else
              <form action="/dashboard/customer-payments/{{$paymentEdit->id}}" method="post" class="p-4 border border-warning rounded shadow-sm" style="background: #fffcf5;">
                @csrf
                @method('PUT')
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                
                <div class="row">
                  <!-- Column 1: Edit Details -->
                  <div class="col-lg-4 col-md-6 border-left">
                    <h6 class="form-section-title"><i class="fa fa-edit text-warning"></i> ویرایش پرداخت (Edit Payment)</h6>
                    
                    <div class="form-group mb-4">
                      <label class="field-label">مقدار پول (Amount)</label>
                      <input type="number" step="0.01" name="amount" value="{{ $paymentEdit->original_amount ?? ($paymentEdit->amount > 0 ? $paymentEdit->amount : $paymentEdit->amount_af) }}" class="form-control custom-input h5 font-weight-bold">
                      <small class="field-explanation text-right">مبلغ جدید را وارد کنید.</small>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">نوع پول (Currency)</label>
                      <select name="currency_id" id="currency_id_edit" class="form-control custom-input">
                        @foreach($currencies as $curr)
                          @php($isCurrent = ($paymentEdit->currency_code == $curr->code))
                          <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ $isCurrent ? 'selected' : '' }}>
                            {{ $curr->code }} - {{ $curr->name }}
                          </option>
                        @endforeach
                      </select>
                      <small class="field-explanation text-right">واحد پولی معامله.</small>
                    </div>

                    <div class="form-group mb-4" id="exchange_rate_container_edit">
                      <label class="field-label">نرخ تبادله به دالر (Exchange Rate to USD)</label>
                      <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate_edit" value="{{ $paymentEdit->exchange_rate }}" class="form-control custom-input h5 font-weight-bold" placeholder="1.00000000">
                      <small class="field-explanation text-right">ارزش ۱ واحد از این اسعار را به دالر وارد کنید.</small>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">نوع معامله</label>
                      <select name="type" class="form-control custom-input font-weight-bold">
                        <option value="رسید" {{ $paymentEdit->type == 'رسید' ? 'selected' : '' }} class="text-success">رسید (Received)</option>
                        <option value="گرفت" {{ $paymentEdit->type == 'گرفت' ? 'selected' : '' }} class="text-danger">گرفت (Sent)</option>
                      </select>
                    </div>
                  </div>

                  <!-- Column 2: Ref & Date -->
                  <div class="col-lg-4 col-md-6 border-left">
                    <h6 class="form-section-title"><i class="fa fa-calendar"></i> تاریخ و انوایس</h6>
                    
                    <div class="form-group mb-4">
                      <label class="field-label">انوایس نمبر</label>
                      <select name="invoice_number" class="form-control custom-input">
                        <option value="نقد">نقد</option>
                        @foreach($invoice_numbers as $ch)
                          <option value="{{$ch->invoice_no}}" {{ $paymentEdit->invoice_number == $ch->invoice_no ? 'selected' : '' }}>{{$ch->invoice_no}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">تاریخ</label>
                      <input type="date" name="date" class="form-control custom-input" value="{{$paymentEdit->date}}">
                    </div>

                    <div class="form-group mb-4">
                      <label class="field-label">توضیحات</label>
                      <textarea name="description" rows="1" class="form-control custom-input">{{$paymentEdit->description}}</textarea>
                    </div>
                  </div>

                  <!-- Column 3: Update & Calculation -->
                  <div class="col-lg-4 col-md-12">
                    <h6 class="form-section-title"><i class="fa fa-refresh text-primary"></i> محاسبه مجدد (Update Preview)</h6>
                    <div id="normalization_preview_container_edit" class="normalization-box p-4 text-center mb-4 shadow-sm" style="background: linear-gradient(135deg, #fff3e0 0%, #fffde7 100%); border-color: #ffe082;">
                      <div class="text-muted small mb-2">ارزش بروز شده در دفتر کل</div>
                      <div class="h2 font-weight-bold text-primary mb-0">$<span id="final_usd_value_edit">0.00</span></div>
                    </div>

                    <div class="row p-3 mb-3 mx-1 bg-light rounded border">
                        <div class="col-lg-12 mb-3">
                            <label class="field-label text-info"><i class="fa fa-university"></i> حسابات مالی (Accounting)</label>
                            <select name="override_debit_account_id" id="override_debit_account_id_edit" class="form-control form-control-sm mb-2">
                                @foreach($allowedDebitAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ (($paymentEdit->override_debit_account_id ?? $mapping->debit_account_id) == $acc->id) ? 'selected' : '' }}>
                                        بدهکار: {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="override_credit_account_id" id="override_credit_account_id_edit" class="form-control form-control-sm">
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ (($paymentEdit->override_credit_account_id ?? $mapping->credit_account_id) == $acc->id) ? 'selected' : '' }}>
                                        بستانکار: {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <button class="btn btn-warning btn-block btn-lg shadow-sm font-weight-bold" type="submit"><i class="fa fa-save"></i> بروزرسانی نهایی</button>
                            <a href="/dashboard/customer-payments/{{$customer->id}}" class="btn btn-block btn-link text-muted mt-2">انصراف (Cancel)</a>
                        </div>
                    </div>
                  </div>
                </div>
              </form>
            @endif
          </div>
          <div class="row">
            
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('customer_payment')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
              <a href="/dashboard/customer-payments-all/{{$customer->id}}" style="float: left"
                 class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
            </div>
          
          </div>
          
          <div class="table-responsive">
            
            <table class="table table-sm table-hover" id="customer_payments">
              <thead>
              <tr>
                <td><b>نمبر معامله</b></td>
                <td><b>مقدار پرداخت (اسعار اصلی)</b></td>
                <td><b>نرخ تبادله</b></td>
                <td><b>معادل دالر (USD)</b></td>
                <td><b>نوعیت</b></td>
                @if($customer->type == 'مشتری قالین')
                  <td><b>انوایس نمبر</b></td>
                @else
                  <td><b>فاکتور فروش</b></td>
                @endif
                
                <td><b>تفصیلات</b></td>
                <td><b>تاریخ</b></td>
                <td><b>حالت</b></td>
                <td class="hideOnPrint text-center"><b>عملیات</b></td>
              </tr>
              </thead>
              <tbody>
              @foreach($payments as $pa)
                <tr class="ur{{$pa->id}}">
                  <td>PAY-{{ $pa->id }}</td>
                  <td class="font-weight-bold" dir="ltr">
                      {{ number_format($pa->original_amount ?? ($pa->amount > 0 ? $pa->amount : $pa->amount_af), 2) }}
                      <span class="badge badge-light border text-dark font-weight-normal">{{ $pa->currency_code ?? ($pa->amount > 0 ? 'USD' : 'AFN') }}</span>
                  </td>
                  <td dir="ltr">{{ number_format($pa->exchange_rate ?? ($pa->amount > 0 ? 1.0 : (1 / ($pa->dollar_rate > 0 ? $pa->dollar_rate : 1))), 8) }}</td>
                  <td class="font-weight-bold text-primary" dir="ltr">
                      ${{ number_format($pa->base_amount ?? ($pa->amount > 0 ? $pa->amount : ($pa->amount_af * ($pa->exchange_rate ?? 1.0))), 2) }}
                  </td>
                  <td>
                      @if($pa->type == 'رسید')
                          <span class="badge badge-success px-2 py-1">رسید</span>
                      @else
                          <span class="badge badge-danger px-2 py-1">گرفت</span>
                      @endif
                  </td>
                  @if($pa->invoice_number == 'نقد')
                    <td>نقد</td>
                  @else
                    <td>
                      <a href="/dashboard/invoices/search-invoice-number/{{$pa->invoice_number}},{{$pa->customer_id}}"
                      >&nbsp; {{$pa->invoice_number}}</a></td>
                  @endif
                  <td>{{$pa->description}}</td>
                  <td>{{$pa->date}}</td>
                  @if($pa->status == 0)
                    
                    <td class="hideOnPrint">
                      <label class="badge badge-warning">درخواست تایید
                        نشده</label></td>
                  @else
                    
                    <td class="hideOnPrint"><label for="" class="badge-success">درخواست تایید
                        شد</label></td>
                  
                  @endif
                  @if( $pa->status == 0 || auth()->user()->role == 'SP')
                    <td class="hideOnPrint text-center">
                      @if(Carbon\Carbon::parse($pa->date)->gt(Carbon\Carbon::parse($lockDate)))
                        <a href="/dashboard/customer-payments/{{$pa->id}}/edit"
                           class="btn btn-sm btn-info">ویرایش</a>
                        
                        <button onclick="deletePayment( {{$pa->id}}, {{$pa->customer_id}})" class="btn btn-danger btn-sm">
                          <i class="fa fa-tick"></i>حذف
                        </button>
                      @else
                        <span class="badge badge-secondary"><i class="fa fa-lock"></i> قفل شده</span>
                      @endif

                      @if($pa->ledger_transaction_id)
                          <a href="{{ route('accounting.journals.show', $pa->ledger_transaction_id) }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-book"></i>&nbsp; روزنامچه مالی</a>
                      @endif
                    </td>
                  @endif
                
                </tr>
              @endforeach

              @foreach($currencyTotals as $code => $totals)
              <tr class="bg-light">
                <td colspan="3" class="text-right font-weight-bold">خلاصه {{ $code }} ({{ $code }} Summary)</td>
                <td colspan="2"><strong>رسیدات: {{ number_format($totals->total_received, 2) }} {{ $code }}</strong></td>
                <td colspan="2"><strong>گرفت‌ها: {{ number_format($totals->total_sent, 2) }} {{ $code }}</strong></td>
                @php($balance = $totals->total_received - $totals->total_sent)
                <td colspan="3" class="text-left font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                    بیلانس: {{ number_format($balance, 2) }} {{ $code }}
                </td>
              </tr>
              @endforeach

              <tr style="background: #e3f2fd;">
                <td colspan="3" class="text-right font-weight-bold">مجموع کل (دالر)</td>
                <td colspan="2"><strong>$ {{ number_format($totalBaseReceived, 2) }}</strong></td>
                <td colspan="2"><strong>$ {{ number_format($totalBaseSent, 2) }}</strong></td>
                @php($baseBalance = $totalBaseReceived - $totalBaseSent)
                <td colspan="3" class="text-left font-weight-bold {{ $baseBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                    بیلانس نهایی: $ {{ number_format($baseBalance, 2) }}
                </td>
              </tr>
              
              </tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
              @if(!isset($all))
                <p>{{$payments->links()}}</p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
  
  <script>

    $(document).ready(function () {
          $('#override_debit_account_id').select2();
          $('#override_credit_account_id').select2();
          $('#override_debit_account_id_edit').select2();
          $('#override_credit_account_id_edit').select2();

          $("#customer_payments").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: 5,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#customer_payments').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });

      function deletePayment(id, customer_id) {

          swal({
              text: "مطمعین هستید ؟",
              buttons: true,
              dangerMode: true,
              buttons: {
                  confirm: {text: 'بلی', className: 'btn-danger'},
                  cancel: 'نخیر'
              },
          })
              .then((willDelete) => {
                  if (willDelete) {
                      $.ajax({
                          type: 'DELETE',
                          data: {
                              '_token': '{{csrf_token()}}',
                          },
                          url: '/dashboard/customer-payments/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/customer-payments/' + customer_id
                              } else {
                                  $('.alert-danger').show();
                              }


                              window.setTimeout(function () {
                                  $(".alert-success").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);
                          },

                      })
                  }
              });
      }
  
  
      // Invoice Matching Logic
      $(document).ready(function() {
          const customerId = "{{$customer->id}}";
          if (customerId) {
              fetchOutstandingInvoices(customerId);
          }

          function fetchOutstandingInvoices(id) {
              $.ajax({
                  url: "{{ route('dashboard.customer_payments.get_outstanding') }}",
                  data: { customer_id: id },
                  success: function(data) {
                      if (data.invoices.length > 0) {
                          $('#invoice_allocation_section').show();
                          let html = '';
                          data.invoices.forEach(inv => {
                              html += `
                                  <tr>
                                      <td>${inv.invoice_no}</td>
                                      <td>${inv.invoice_date}</td>
                                      <td>$${inv.total_amount}</td>
                                      <td><b class="text-danger">$${inv.remaining_balance}</b></td>
                                      <td>
                                          <input type="number" step="0.01" 
                                              name="allocations[${inv.id}]" 
                                              class="form-control form-control-sm allocation-input" 
                                              max="${inv.remaining_balance}" 
                                              placeholder="0.00">
                                      </td>
                                  </tr>
                              `;
                          });
                          $('#invoice_list_body').html(html);
                      } else {
                          $('#invoice_allocation_section').hide();
                      }
                  }
              });
          }

          // Initialize exchange rate fields and handle live updates
          function initExchangeRateField() {
              const dropdown = $('#currency_id, #currency_id_edit');
              dropdown.each(function() {
                  const selectedOption = $(this).find('option:selected');
                  const code = selectedOption.data('code');
                  const rateInput = $(this).attr('id') === 'currency_id' ? $('#exchange_rate') : $('#exchange_rate_edit');
                  
                  if (!rateInput.val()) {
                      rateInput.val(selectedOption.data('rate'));
                  }
                  
                  if (code === 'USD') {
                      rateInput.prop('readonly', true);
                  } else {
                      rateInput.prop('readonly', false);
                  }
              });
          }

          $('#currency_id, #currency_id_edit').on('change', function() {
              const selectedOption = $(this).find('option:selected');
              const rate = selectedOption.data('rate');
              const code = selectedOption.data('code');
              
              const rateInput = $(this).attr('id') === 'currency_id' ? $('#exchange_rate') : $('#exchange_rate_edit');
              rateInput.val(rate);
              
              if (code === 'USD') {
                  rateInput.prop('readonly', true);
              } else {
                  rateInput.prop('readonly', false);
              }
              
              updateNormalizationPreview();
          });

          // Live Normalization Preview
          const amountInput = $('input[name="amount"]');
          const previewContainer = $('#normalization_preview_container, #normalization_preview_container_edit');
          const finalUsdSpan = $('#final_usd_value, #final_usd_value_edit');

          function updateNormalizationPreview() {
              const amount = parseFloat(amountInput.val()) || 0;
              const selectedOption = $('#currency_id option:selected, #currency_id_edit option:selected');
              const code = selectedOption.data('code');
              
              const rateInput = $('#exchange_rate, #exchange_rate_edit');
              const rate = parseFloat(rateInput.val()) || 0;

              if (amount <= 0) {
                  previewContainer.hide();
                  return;
              }

              let finalUsd = 0;
              if (code === 'USD') {
                  finalUsd = amount;
              } else {
                  finalUsd = amount * rate;
              }

              finalUsdSpan.text(new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(finalUsd));
              previewContainer.fadeIn(200);
          }

          amountInput.on('input', updateNormalizationPreview);
          $('#exchange_rate, #exchange_rate_edit').on('input', updateNormalizationPreview);
          
          // Trigger initially
          initExchangeRateField();
          updateNormalizationPreview();
      });
  </script>
@endsection