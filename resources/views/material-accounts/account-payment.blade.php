@extends('dsh.master')
@section('title' , 'حسابات مواد خام')
@section('content')
  
  
  <!-- navbar -->
  
  <div id="PaidToDA">
    
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card text-right" style="direction: rtl;">
          <div class="card-header">
            
            @if(session("status"))
              <div class="alert alert-success status text-center" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('status')}}
              </div>
            @endif

            @if(session("error"))
              <div class="alert alert-danger status text-center" role="alert">
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
                  <table class="table table-xs table-hover">
                    <tbody>
                    <tr>
                      <td><b>نام حساب</b></td>
                      <td>{{$account->name}}</td>
                    </tr>
                    <tr>
                      <td><b>سال مالی</b></td>
                      <td> {{$account->account_year}}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 text-left">
                <h4>ACCOUNT #: {{$account->id}}</h4>
              </div>
            </div>
            <hr>
            <div class="row">
              
              <div class="col-sm-12 hideOnPrint">
                
                @if(!$paymentEdit)
                  <form action="/dashboard/material-account-payments" method="post">
                    @csrf
                    <input type="hidden" name="account_id" value="{{$account->id}}">
                    
                    <div class="row">
                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">مقدار مواد خام (KG)</label>
                        <input type="text" name="amount" id="amount_in"
                               placeholder="مقدار مواد خام" class="form-control" required>
                        @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">قیمت فی کیلو</label>
                        <input type="text" name="price" id="price_in"
                               placeholder="قیمت فی کیلو" class="form-control" required>
                        @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">ارز معامله</label>
                        <select name="currency_id" id="currency_id_in" class="form-control" required>
                          @foreach($currencies as $curr)
                            <option value="{{$curr->id}}" data-rate="{{$curr->exchange_rate}}" {{ $curr->is_base_currency ? 'selected' : '' }}>
                              {{$curr->code}} ({{$curr->symbol}})
                            </option>
                          @endforeach
                        </select>
                        @error('currency_id') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">نرخ تبادله به دالر</label>
                        <input type="text" name="exchange_rate" id="exchange_rate_in"
                               value="1.00" class="form-control" required>
                        @error('exchange_rate') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">گدام (Warehouse)</label>
                        <select name="warehouse_id" id="warehouse_id_in" class="form-control" required>
                          @foreach($warehouses as $w)
                            <option value="{{$w->id}}">{{$w->name}}</option>
                          @endforeach
                        </select>
                        @error('warehouse_id') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                    </div>

                    <div class="row mt-2">
                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">نوعیت مواد خام</label>
                        <select name="type_id" id="" class="form-control" required>
                          @foreach($material_type as $t)
                            <option value="{{$t->material_type_id}}">{{$t->material_type}}</option>
                          @endforeach
                        </select>
                        @error('type_id') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                      
                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">نوع معامله</label>
                        <select name="type" id="type_in" class="form-control" required>
                          <option selected disabled>انتخاب</option>
                          <option value="رسید">رسید (ورود بار)</option>
                          <option value="گرفت">گرفت (خروج بار)</option>
                        </select>
                        @error('type') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                      
                      <div class="col-lg-6 col-md-6 col-sm-12">
                        <label class="pull-right">توضیحات</label>
                        <textarea name="description" id="description" rows="1"
                                  class="form-control"
                                  placeholder="توضیحات " required></textarea>
                        @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">تاریخ</label>
                        <input type="date" name="date"
                               class="form-control" required value="{{ date('Y-m-d') }}">
                        @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                    </div>

                    <!-- Calculation Live Preview -->
                    <div class="row mt-3 mb-3 p-3 text-right" style="background: #fafafa; border: 1px dashed #ccc; border-radius: 5px; margin: 5px 0;">
                      <div class="col-md-6">
                        <strong>مبلغ کل (ارز اصلی): </strong> <span id="original_amount_preview" style="font-weight: bold; color: #2563eb;">0.00</span>
                      </div>
                      <div class="col-md-6">
                        <strong>معادل به دالر (USD): </strong> <span id="base_amount_preview" style="font-weight: bold; color: #16a34a;">0.00</span>
                      </div>
                    </div>
                    
                    <!-- ACCOUNT OVERRIDES -->
                    <div id="accounting-overrides-receipt" class="row mt-2 p-2 mb-3" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 5px; display:none;">
                      <div class="col-lg-6">
                        <label class="text-info pull-right">حساب نقد/بانک (Debit)</label>
                        <select name="override_debit_account_id" id="override_debit_account_id_in" class="form-control select2">
                          @foreach($allowedDebitAccountsIn as $acc)
                            <option value="{{ $acc->id }}" {{ ($mappingIn && $mappingIn->debit_account_id == $acc->id) ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-lg-6">
                        <label class="text-info pull-right">حساب پرداختنی (Credit)</label>
                        <select name="override_credit_account_id" id="override_credit_account_id_in" class="form-control select2">
                          @foreach($allowedCreditAccountsIn as $acc)
                            <option value="{{ $acc->id }}" {{ ($mappingIn && $mappingIn->credit_account_id == $acc->id) ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>

                    <div id="accounting-overrides-payment" class="row mt-2 p-2 mb-3" style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 5px; display:none;">
                      <div class="col-lg-6">
                        <label class="text-warning pull-right">حساب پرداختنی (Debit)</label>
                        <select name="override_debit_account_id" id="override_debit_account_id_out" class="form-control select2" disabled>
                          @foreach($allowedDebitAccountsOut as $acc)
                            <option value="{{ $acc->id }}" {{ ($mappingOut && $mappingOut->debit_account_id == $acc->id) ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-lg-6">
                        <label class="text-warning pull-right">حساب نقد/بانک (Credit)</label>
                        <select name="override_credit_account_id" id="override_credit_account_id_out" class="form-control select2" disabled>
                          @foreach($allowedCreditAccountsOut as $acc)
                            <option value="{{ $acc->id }}" {{ ($mappingOut && $mappingOut->credit_account_id == $acc->id) ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    
                    <div class="row mr-1 mt-2">
                      <button class="btn btn-warning btn-sm" type="reset">انصراف</button>
                      <button class="btn btn-primary marginx btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                    </div>
                  </form>
                @else
                  <form action="/dashboard/material-account-payments/{{$paymentEdit->id}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="account_id" value="{{$account->id}}">
                    
                    <div class="row">
                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">مقدار مواد خام (KG)</label>
                        <input type="text" name="amount" id="amount_edit"
                               value="{{$paymentEdit->amount}}" class="form-control" required>
                        @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">قیمت فی کیلو</label>
                        <input type="text" name="price" id="price_edit"
                               value="{{$paymentEdit->price}}" class="form-control" required>
                        @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">ارز معامله</label>
                        <select name="currency_id" id="currency_id_edit" class="form-control" required>
                          @foreach($currencies as $curr)
                            <option value="{{$curr->id}}" data-rate="{{$curr->exchange_rate}}" {{ $paymentEdit->currency_id == $curr->id ? 'selected' : '' }}>
                              {{$curr->code}} ({{$curr->symbol}})
                            </option>
                          @endforeach
                        </select>
                        @error('currency_id') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">نرخ تبادله به دالر</label>
                        <input type="text" name="exchange_rate" id="exchange_rate_edit"
                               value="{{$paymentEdit->exchange_rate}}" class="form-control" required>
                        @error('exchange_rate') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">گدام (Warehouse)</label>
                        <select name="warehouse_id" id="warehouse_id_edit" class="form-control" required>
                          @foreach($warehouses as $w)
                            <option value="{{$w->id}}" {{ $paymentEdit->warehouse_id == $w->id ? 'selected' : '' }}>{{$w->name}}</option>
                          @endforeach
                        </select>
                        @error('warehouse_id') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                    </div>

                    <div class="row mt-2">
                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">نوعیت مواد خام</label>
                        <select name="type_id" id="" class="form-control" required>
                          @foreach($material_type as $t)
                            <option {{ $paymentEdit->type_id == $t->material_type_id  ? 'selected' : '' }} value="{{$t->material_type_id}}">{{$t->material_type}}</option>
                          @endforeach
                        </select>
                        @error('type_id') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                      
                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">نوع معامله</label>
                        <select name="type" id="type_edit" class="form-control" required>
                          <option {{ $paymentEdit->type == 'رسید' ? 'selected' : '' }} value="رسید">رسید (ورود بار)</option>
                          <option {{ $paymentEdit->type == 'گرفت' ? 'selected' : '' }} value="گرفت">گرفت (خروج بار)</option>
                        </select>
                        @error('type') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                      
                      <div class="col-lg-6 col-md-6 col-sm-12">
                        <label class="pull-right">توضیحات</label>
                        <textarea name="description" id="description" rows="1"
                                  class="form-control"
                                  placeholder="توضیحات " required>{{$paymentEdit->description}}</textarea>
                        @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-6">
                        <label class="pull-right">تاریخ</label>
                        <input type="date" name="date" value="{{$paymentEdit->date}}"
                               class="form-control" required>
                        @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                      </div>
                    </div>

                    <!-- Calculation Live Preview -->
                    <div class="row mt-3 mb-3 p-3 text-right" style="background: #fafafa; border: 1px dashed #ccc; border-radius: 5px; margin: 5px 0;">
                      <div class="col-md-6">
                        <strong>مبلغ کل (ارز اصلی): </strong> <span id="original_amount_edit_preview" style="font-weight: bold; color: #2563eb;">0.00</span>
                      </div>
                      <div class="col-md-6">
                        <strong>معادل به دالر (USD): </strong> <span id="base_amount_edit_preview" style="font-weight: bold; color: #16a34a;">0.00</span>
                      </div>
                    </div>

                    <!-- ACCOUNT OVERRIDES EDIT -->
                    <div id="accounting-overrides-edit-receipt" class="row mt-3 p-2 mb-3" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 5px; display: {{ $paymentEdit->type == 'رسید' ? 'flex' : 'none' }}">
                      <div class="col-lg-6">
                        <label class="pull-right text-info">حساب نقد/بانک (Debit)</label>
                        <select name="override_debit_account_id" class="form-control select2" {{ $paymentEdit->type == 'رسید' ? '' : 'disabled' }}>
                          @foreach($allowedDebitAccountsIn as $acc)
                            <option value="{{ $acc->id }}" {{ $paymentEdit->override_debit_account_id == $acc->id ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-lg-6">
                        <label class="pull-right text-info">حساب پرداختنی (Credit)</label>
                        <select name="override_credit_account_id" class="form-control select2" {{ $paymentEdit->type == 'رسید' ? '' : 'disabled' }}>
                          @foreach($allowedCreditAccountsIn as $acc)
                            <option value="{{ $acc->id }}" {{ $paymentEdit->override_credit_account_id == $acc->id ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>

                    <div id="accounting-overrides-edit-payment" class="row mt-3 p-2 mb-3" style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 5px; display: {{ $paymentEdit->type == 'گرفت' ? 'flex' : 'none' }}">
                      <div class="col-lg-6">
                        <label class="pull-right text-warning">حساب پرداختنی (Debit)</label>
                        <select name="override_debit_account_id" class="form-control select2" {{ $paymentEdit->type == 'گرفت' ? '' : 'disabled' }}>
                          @foreach($allowedDebitAccountsOut as $acc)
                            <option value="{{ $acc->id }}" {{ $paymentEdit->override_debit_account_id == $acc->id ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-lg-6">
                        <label class="pull-right text-warning">حساب نقد/بانک (Credit)</label>
                        <select name="override_credit_account_id" class="form-control select2" {{ $paymentEdit->type == 'گرفت' ? '' : 'disabled' }}>
                          @foreach($allowedCreditAccountsOut as $acc)
                            <option value="{{ $acc->id }}" {{ $paymentEdit->override_credit_account_id == $acc->id ? 'selected' : '' }}>
                              {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  
                    <div class="row mr-1 mt-2">
                      <a href="/dashboard/material-accounts/{{ $account->id }}" class="btn btn-warning btn-sm">انصراف</a>
                      <button class="btn btn-primary marginx btn-sm" type="submit"><span class="fa fa-save"></span> ویرایش</button>
                    </div>
                  </form>
                @endif
              
              </div>
            
            
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
          <div class="card-header text-right">
            <div class="row">
              <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                <h4 style="margin: 0; padding-top: 5px;">لیست پرداخت‌ها و رسیدهای مواد خام</h4>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint text-left">
                <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                  <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('PaidToDA')">
                    <i class="fa fa-print"></i> چاپ
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="sparkline8-graph text-muted">
                  
                  <div class="table-responsive">
                    <table class="table table-xs table-hover text-right" id="account_payment" style="direction: rtl;">
                      <thead>
                      <tr>
                        <td><b>بیلانس (وزن)</b></td>
                        <td><b>ورودی (KG)</b></td>
                        <td><b>خروجی (KG)</b></td>
                        <td><b>نرخ فی کیلو</b></td>
                        <td><b>مبلغ کل (ارز)</b></td>
                        <td><b>ارز / FX نرخ</b></td>
                        <td><b>معادل دالر (USD)</b></td>
                        <td><b>گدام</b></td>
                        <td><b>نوعیت مواد خام</b></td>
                        <td><b>تفصیلات</b></td>
                        <td><b>تاریخ</b></td>
                        <td><b>حالت</b></td>
                        <td class="hideOnPrint"><b>اقدامات</b></td>
                      </tr>
                      </thead>
                      <tbody>
                      @php($running_balance = 0)
                      @foreach($payments->reverse() as $pa)
                        @php($running_balance += ($pa->type == 'رسید' ? $pa->amount : -$pa->amount))
                      @endforeach
                      
                      @foreach($payments as $pa)
                        <tr>
                          <td style="direction: ltr; font-weight: bold; color: #475569;">{{ number_format($running_balance, 2) }} KG</td>
                          @if($pa->type == 'رسید')
                            <td style="direction: ltr; color: #16a34a;">+{{ number_format($pa->amount, 2) }} KG</td>
                            <td>0.00</td>
                          @else
                            <td>0.00</td>
                            <td style="direction: ltr; color: #dc2626;">-{{ number_format($pa->amount, 2) }} KG</td>
                          @endif
                          
                          <td style="direction: ltr;">{{ number_format($pa->price, 2) }}</td>
                          <td style="direction: ltr; font-weight: 500;">{{ number_format($pa->original_amount, 2) }} {{ $pa->currency_code }}</td>
                          <td style="direction: ltr; font-size: 0.9em; color: #64748b;">
                            {{ $pa->currency_code }} ({{ number_format($pa->exchange_rate, 4) }})
                          </td>
                          <td style="direction: ltr; font-weight: 600; color: #0f172a;">${{ number_format($pa->base_currency_amount, 2) }}</td>
                          <td>{{ $pa->warehouse->name ?? 'Default' }}</td>
                          <td>{{$pa->materialtype->material_type}}</td>
                          <td>{{$pa->description}}</td>
                          <td>{{$pa->date}}</td>
                          
                          @if($pa->status == 0)
                            <td class="hideOnPrint">
                              <span class="badge badge-warning">درخواست تایید نشده</span>
                            </td>
                          @else
                            <td class="hideOnPrint">
                              <span class="badge badge-success">تایید شده</span>
                            </td>
                          @endif
                          
                          <td class="hideOnPrint">
                            @if($pa->status == 0 || auth()->user()->role == 'SP')
                              <a href="/dashboard/material-account-payments/{{$pa->id}}/edit"
                                 class="btn btn-xs btn-info">ویرایش</a>
                              <button onclick="deletePayment({{$pa->id}}, {{$pa->account_id}})"
                                      class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> حذف</button>
                            @endif
                          </td>
                        </tr>
                        @php($running_balance -= ($pa->type == 'رسید' ? $pa->amount : -$pa->amount))
                      @endforeach
                      
                      <tr style="background: #f8fafc; font-weight: bold;">
                        <td style="direction: ltr">{{$credits - $debits}} KG</td>
                        <td style="direction: ltr; color: #16a34a;">{{$credits}} KG</td>
                        <td style="direction: ltr; color: #dc2626;">{{$debits}} KG</td>
                        <td colspan="10">صرف بیلانس کل وزن جاری</td>
                      </tr>
                      
                      </tbody>
                    </table>
                  </div>
                  
                  @if(!isset($all))
                    <p>{{$payments->links()}}</p>
                  @endif
                </div>
              </div>
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
          $("#account_payment").tableExport({
              headers: true,
              footers: true,
              formats: ["xlsx"],
              filename: "id",
              bootstrap: true,
              exportButtons: true,
              position: "bottom",
              ignoreRows: null,
              ignoreCols: 12,
              trimWhitespace: true,
              RTL: true,
              sheetname: "id",
          });
          var $buttons = $('#account_payment').find('caption').children().detach();
          $buttons.appendTo('#exportButton');
      });

      // Show/Hide Accounting Overrides based on Transaction Type
      function toggleOverrides(type, prefix) {
          if (type == 'رسید') {
              $('#accounting-overrides-' + prefix + 'receipt').show();
              $('#accounting-overrides-' + prefix + 'receipt select').prop('disabled', false);
              $('#accounting-overrides-' + prefix + 'payment').hide();
              $('#accounting-overrides-' + prefix + 'payment select').prop('disabled', true);
          } else if (type == 'گرفت') {
              $('#accounting-overrides-' + prefix + 'payment').show();
              $('#accounting-overrides-' + prefix + 'payment select').prop('disabled', false);
              $('#accounting-overrides-' + prefix + 'receipt').hide();
              $('#accounting-overrides-' + prefix + 'receipt select').prop('disabled', true);
          } else {
              $('#accounting-overrides-' + prefix + 'receipt, #accounting-overrides-' + prefix + 'payment').hide();
              $('#accounting-overrides-' + prefix + 'receipt select, #accounting-overrides-' + prefix + 'payment select').prop('disabled', true);
          }
      }

      $('#type_in').change(function() {
          toggleOverrides($(this).val(), '');
      });

      $('#type_edit').change(function() {
          toggleOverrides($(this).val(), 'edit-');
      });

      // Live currency and amount calculations
      function calculateLiveAmounts() {
          var amount = parseFloat($('#amount_in').val()) || 0;
          var price = parseFloat($('#price_in').val()) || 0;
          var rate = parseFloat($('#exchange_rate_in').val()) || 1;
          
          var original = amount * price;
          var base = original * rate;
          
          $('#original_amount_preview').text(original.toFixed(2));
          $('#base_amount_preview').text(base.toFixed(2));
      }
      
      $('#amount_in, #price_in, #exchange_rate_in').on('input', calculateLiveAmounts);
      $('#currency_id_in').change(function() {
          var rate = $(this).find(':selected').data('rate') || 1;
          $('#exchange_rate_in').val(rate);
          calculateLiveAmounts();
      });

      function calculateLiveEditAmounts() {
          var amount = parseFloat($('#amount_edit').val()) || 0;
          var price = parseFloat($('#price_edit').val()) || 0;
          var rate = parseFloat($('#exchange_rate_edit').val()) || 1;
          
          var original = amount * price;
          var base = original * rate;
          
          $('#original_amount_edit_preview').text(original.toFixed(2));
          $('#base_amount_edit_preview').text(base.toFixed(2));
      }
      
      $('#amount_edit, #price_edit, #exchange_rate_edit').on('input', calculateLiveEditAmounts);
      $('#currency_id_edit').change(function() {
          var rate = $(this).find(':selected').data('rate') || 1;
          $('#exchange_rate_edit').val(rate);
          calculateLiveEditAmounts();
      });

      $(document).ready(function() {
          $('.select2').select2();
          calculateLiveAmounts();
          if ($('#amount_edit').length) {
              calculateLiveEditAmounts();
          }
      });

      function deletePayment(id, account_id) {
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
                      url: '/dashboard/material-account-payments/' + id,
                      success: function (res) {
                          if (res.status == 'success') {
                              window.location = '/dashboard/material-accounts/' + account_id
                          } else {
                              swal("خطا در انجام عملیات", { icon: "error" });
                          }
                      }
                  });
              }
          });
      }
  </script>
@endsection