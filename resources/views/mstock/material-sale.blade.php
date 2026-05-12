@extends('dsh.master')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$saleEdit)
            <h5>فروش تار</h5>
          @else
            <h5>ویرایش فروش</h5>
          @endif
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$saleEdit)
              <form action="/dashboard/material-sales" method="post">
                @csrf
                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right">فاکتور فروش</label>
                      <input type="text" name="sale_number" value="{{$SaleNo}}"
                             class="form-control">
                      @error('sale_number') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نام نماینده</label>
                      <select name="agent_id" id="agent_id" required class="form-control select2"
                              style="direction: rtl">
                        <option value="">انتخاب نماینده</option>
                        @foreach ($agents as $ag)
                          <option value="{{$ag->agent_id}}">{{$ag->user->name}}</option>
                        @endforeach
                      </select>
                      <small id="agent-balance" class="text-muted"></small>
                      @error('agent_id') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مقدار مواد</label>
                      <input type="text" name="amount" required
                             placeholder="مقدار را به کیلو گرام وارد کنید"
                             class="form-control" id="material-amount">
                      @error('amount') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت مواد</label>
                      <select name="type_id" id="type_id" required class="form-control select2"
                              style="direction: rtl">
                         <option value="">انتخاب نوعیت</option>
                        @foreach ($material_types as $type)
                          <option value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                        @endforeach
                      </select>
                      @error('type_id') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">دسته بندی مواد</label>
                      <select name="category_id" id="category_id" required class="form-control select2"
                              style="direction: rtl">
                         <option value="">انتخاب دسته بندی</option>
                        @foreach ($categories as $category)
                          <option value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                        @endforeach
                      </select>
                      <small id="material-wac" class="text-success" style="font-weight:bold"></small>
                      @error('category_id') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>

                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">گدام (Warehouse)</label>
                      <select name="warehouse_id" required class="form-control select2">
                        @foreach ($warehouses as $w)
                          <option value="{{$w->id}}">{{$w->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right"> قیمت فی کیلو به افغانی</label>
                      <input type="text" name="price" required
                             placeholder="قیمت مواد مذکور" class="form-control"
                             id="material-price">
                      <input type="hidden" value="{{$currency}}" id="currency">
                      @error('price') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right"> قیمت مجموع به افغانی</label>
                      <input type="text" name="total_price_af" readonly required
                             class="form-control"
                             id="material-af-total-price">
                      @error('total_price_af') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right"> قیمت مجموع به دالر</label>
                      <input type="text" name="total_price" readonly required
                             class="form-control"
                             id="material-total-price">
                      @error('total_price') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ</label>
                      <input type="date" name="date" required
                             placeholder="تاریخ را وارد کنید"
                             class="form-control">
                      @error('date') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                </div>

                <hr>
                <!-- ACCOUNT OVERRIDES -->
                <div class="row mt-3 p-3" style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px;">
                    <div class="col-lg-12">
                        <h6 class="mb-3 text-primary"><i class="fa fa-university"></i> تنظیمات حسابی (Material Sale Accounting)</h6>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب دریافتنی (Debit) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                            <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2">
                                @foreach($allowedDebitAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب فروش مواد (Credit) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                            <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2">
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب مصرف (COGS Debit) <span class="badge badge-info">{{ count($allowedCogsDebit) }}</span></label>
                            <select name="override_cogs_debit_id" id="override_cogs_debit_id" class="form-control select2">
                                @foreach($allowedCogsDebit as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب گدام (COGS Credit) <span class="badge badge-info">{{ count($allowedCogsCredit) }}</span></label>
                            <select name="override_cogs_credit_id" id="override_cogs_credit_id" class="form-control select2">
                                @foreach($allowedCogsCredit as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">انصراف</button>
                      <button class="btn btn-primary marginx" type="submit"><span
                                class="fa fa-save"></span> ذخیره
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form action="/dashboard/material-sales/{{$saleEdit->id}}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="old_agent_id" value="{{$saleEdit->agent_id}}">
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="">فاکتور فروش</label>
                      <input type="text" class="form-control " value="{{$saleEdit->sale_number}}" name="sale_number">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نام نماینده</label>
                      <select name="agent_id" id="agent_id" required class="form-control select2"
                              style="direction: rtl">
                        @foreach ($agents as $ag)
                          <option {{($ag->agent_id == $saleEdit->agent_id ? 'selected' : '')}} value="{{$ag->agent_id}}">{{$ag->user->name}}</option>
                        @endforeach
                      </select>
                      <small id="agent-balance" class="text-muted"></small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ</label>
                      <input type="date" name="date" value="{{$saleEdit->date}}" required
                             placeholder="تاریخ را وارد کنید"
                             class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مقدار مواد</label>
                      <input type="text" name="amount" required
                             placeholder="مقدار را به کیلو گرام وارد کنید"
                             class="form-control" value="{{$saleEdit->amount}}" id="material-amount">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت مواد</label>
                      <select name="type_id" id="type_id" required class="form-control select2"
                              style="direction: rtl">
                        @foreach ($material_types as $type)
                          <option {{($saleEdit->type_id == $type->material_type_id ? 'selected' : '')}} value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">دسته بندی مواد</label>
                      <select name="category_id" id="category_id" required class="form-control select2"
                              style="direction: rtl">
                        @foreach ($categories as $category)
                          <option {{($saleEdit->category_id == $category->material_category_id ? 'selected' : '')}} value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                        @endforeach
                      </select>
                      <small id="material-wac" class="text-success" style="font-weight:bold"></small>
                    </div>
                  </div>

                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">گدام (Warehouse)</label>
                      <select name="warehouse_id" required class="form-control select2">
                        @foreach ($warehouses as $w)
                          <option value="{{$w->id}}" {{ $saleEdit->warehouse_id == $w->id ? 'selected' : '' }}>{{$w->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right"> قیمت فی کیلو به افغانی</label>
                      <input type="text" name="price" value="{{$saleEdit->price}}" required
                             placeholder="قیمت مواد مذکور" class="form-control"
                             id="material-price">
                      <input type="hidden" value="{{$currency}}" id="currency">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right"> قیمت مجموع به افغانی</label>
                      <input type="text" name="total_price_af" value="{{$saleEdit->total_price_af}}" readonly required
                             class="form-control"
                             id="material-af-total-price">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right"> قیمت مجموع به دالر</label>
                      <input type="text" name="total_price" value="{{$saleEdit->total_price}}" readonly required
                             class="form-control"
                             id="material-total-price">
                    </div>
                  </div>
                </div>

                <hr>
                <!-- ACCOUNT OVERRIDES EDIT -->
                <div class="row mt-3 p-3" style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px;">
                    <div class="col-lg-12">
                        <h6 class="mb-3 text-primary"><i class="fa fa-university"></i> تنظیمات حسابی (Material Sale Accounting Edit)</h6>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب دریافتنی (Debit) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                            <select name="override_debit_account_id" class="form-control select2">
                                <option value="">Standard Default</option>
                                @foreach($allowedDebitAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_debit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب فروش مواد (Credit) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                            <select name="override_credit_account_id" class="form-control select2">
                                <option value="">Standard Default</option>
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_credit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب مصرف (COGS Debit) <span class="badge badge-info">{{ count($allowedCogsDebit) }}</span></label>
                            <select name="override_cogs_debit_id" class="form-control select2">
                                <option value="">Standard Default</option>
                                @foreach($allowedCogsDebit as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_cogs_debit_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب گدام (COGS Credit) <span class="badge badge-info">{{ count($allowedCogsCredit) }}</span></label>
                            <select name="override_cogs_credit_id" class="form-control select2">
                                <option value="">Standard Default</option>
                                @foreach($allowedCogsCredit as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_cogs_credit_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">انصراف</button>
                      <button class="btn btn-primary marginx" type="submit"><span
                                class="fa fa-save"></span> ذخیره
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            @endif
          
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header">
          <h5> لیست فروشات تار</h5>
        </div>
        <div class="card-body">
          <div class="btn btn-sm btn-primary adgustbtn hideOnPrint" style="float: left"
               onclick="printPage('sales')"><i class="fa fa-print"></i> Print
          </div>
          <br>
          <div class="static-table-list" id="sales">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
                <th>فاکتور فروش</th>
                <th>نام نماینده</th>
                <th> مقدار مواد</th>
                <th>قیمت فی کیلو</th>
                <th>قیمت مجموع به افغانی</th>
                <th>قیمت مجموع به دالر</th>
                <th>کتگوری</th>
                <th> نوعیت مواد</th>
                <th> تاریخ</th>
                <th>حالت</th>
                <th class="hideOnPrint"> ویرایش</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($material_sales as $material)
                <tr>
                  <td>
                    <a href="/dashboard/material-sales/search-sale-number/{{$material->sale_number}},{{$material->agent_id}}"
                    >&nbsp; {{$material->sale_number}}</a></td>
                  <td>{{$material->agent->user->name}}</td>
                  <td>{{$material->amount . "kg"}}</td>
                  <td>{{$material->price . "AFG"}}</td>
                  <td>AF{{$material->total_price_af }}</td>
                  <td>${{$material->total_price }}</td>
                  <td>{{$material->category->material_category}}</td>
                  <td>{{$material->type->material_type}}</td>
                  <td>{{$material->date}}</td>
                  @if($material->status == 0)
    
                    <td class="hideOnPrint">
                      <label class="badge badge-warning">درخواست تایید نشده</label></td>
                  @else
    
                    <td class="hideOnPrint"><label for="" class="badge badge-success">درخواست تایید
                        شد</label></td>
  
                  @endif
                   @if($material->status == 1 || auth()->user()->role == 'SP')
                  <td class="hideOnPrint"><a class="btn btn-sm btn-info"
                                             href="/dashboard/material-sales/{{$material->id}}/edit"><i
                               class="fa fa-pencil">&nbsp;&nbsp;&nbsp;</i>ویرایش</a>
                  </td>
                  @endif
                </tr>
              @empty
                <p style="text-align:center;color:red;margin:40px auto;">هنوز فروش ثبت نشده
                  است</p>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    
    
    </div>
  </div>
@endsection


@section('scripts')
  <script>
      $(document).ready(function() {
          $('.select2').select2({
              width: '100%',
              dir: 'rtl'
          });

          // AJAX Sale Info
          $('#agent_id, #category_id, #type_id').on('change', function() {
              fetchSaleInfo();
          });

          // Initial load if edit
          if ($('#agent_id').val() || $('#category_id').val()) {
              fetchSaleInfo();
          }

          function fetchSaleInfo() {
              var agentId = $('#agent_id').val();
              var catId = $('#category_id').val();
              var typeId = $('#type_id').val();

              if (agentId || (catId && typeId)) {
                  $.ajax({
                      url: "{{ route('dashboard.material-sales-info') }}",
                      data: {
                          agent_id: agentId,
                          category_id: catId,
                          type_id: typeId
                      },
                      success: function(res) {
                          if (agentId && res.balance !== undefined) {
                              $('#agent-balance').html('Balance: ' + parseFloat(res.balance).toLocaleString() + ' AFN');
                          }
                          if (catId && typeId && res.wac !== undefined) {
                              $('#material-wac').html('Current Cost (WAC): ' + parseFloat(res.wac).toLocaleString() + ' AFN');
                          }
                      },
                      error: function(err) {
                          console.error("AJAX Error:", err);
                      }
                  });
              }
          }
      });

      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {
              $(this).remove();
          });
      }, 2000);

      // Calculations
      $("#material-amount, #material-price").blur(function () {
          var ma = $('#material-amount').val();
          var mp = $('#material-price').val();
          var c = $('#currency').val();
          
          if (ma && mp) {
              var totalAf = parseFloat(ma) * parseFloat(mp);
              var totalUsd = totalAf / parseFloat(c);
              
              $("#material-af-total-price").val(totalAf.toFixed(2));
              $("#material-total-price").val(totalUsd.toFixed(2));
          }
      });
  
  </script>
@endsection