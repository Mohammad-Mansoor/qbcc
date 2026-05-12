@extends('dsh.master')
@section('title' , 'لیست خرید تار')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>فورم خرید تار</h5>
        </div>
        <div class="card-body">
          @if(!$purchaseMaterial)
            <form action="/dashboard/material-purchase" method="post" enctype="multipart/form-data">
              @csrf
              <br>
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">فاکتور خرید</label>
                    <input type="text" class="form-control " value="{{$PurchaseNo}}" name="purchase_number">
                  </div>
                </div>
                  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">فروشنده</label>
                    <select name="seller_id" id="seller_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($sellers as $s)
                        <option value="{{ $s->id }}" {{ old('seller_id') == $s->id ? 'selected' : '' }} >{{ $s->name }}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('seller_id') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
               <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">کتگوری مواد</label>
                    <select name="material_category" id="material_category" class="form-control">
                      <option value="">~~~</option>
                      @foreach($material_category as $mc)
                        <option {{ (Request::old('material_category') == $mc->material_category_id ? 'selected' : '') }} value="{{ $mc->material_category_id }}">{{ $mc->material_category }}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('material_category') {{ __('message.'.$message) }}
                      @enderror
                    </small>
                  </div>
                </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">نوعیت مواد</label>
                    <select name="material_type" id="material_type" class="form-control">
                      <option value="">~~~</option>
                      @foreach($material_type as $mt)
                        <option {{ (Request::old('material_type') == $mt->material_type_id ? 'selected' : '') }} value="{{ $mt->material_type_id }}">{{ $mt->material_type }}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('material_type') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">گودام (Warehouse)</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control">
                      @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }} >{{ $w->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">مقدار</label>
                        <input name="quantity" id="material-amount" onkeyup="Calculate()" type="text" dir="ltr"
                               value="{{ old('quantity') }}" class="form-control quantity">
                        <small class="text-danger">@error('quantity') {{ __('message.'.$message) }} @enderror</small>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <label class="">واحد</label>
                        <input value="KG" type="text" class="form-control" readonly>
                      </div>
                    
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">قیمت فی کیلو به افغانی</label>
                        <input name="price_per_kilo" id="material-price" value="{{ old('price_per_kilo') }}"
                               onkeyup="Calculate()" type="text" dir="ltr" class="form-control quantity">
                        <input type="hidden" value="{{$currency}}" id="currency">
                        <small class="text-danger">@error('price_per_kilo') {{ __('message.'.$message) }}@enderror
                        </small>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label class="">واحد</label>
                        <input type="text" value="AF" disabled class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">قیمت مجموع افغانی</label>
                        <input id="material-af-total-price" name="total_af" type="text" dir="ltr"
                               class="form-control quantity" readonly>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label class="">واحد</label>
                        <input value="AF" type="text" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">قیمت مجموع دالر</label>
                        <input id="material-total-price" name="total" type="text" dir="ltr"
                               class="form-control quantity" readonly>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <label class="">واحد</label>
                        <input value="$" type="text" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">قیمت به حروف</label>
                    <input type="text" class="form-control " name="in_words" placeholder="قیمت به حروف وارد کنید">
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">تاریخ</label>
                    <input id="purchase_date" name="purchase_date" type="date" class="form-control quantity">
                    <small class="text-danger">@error('purchase_date') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
              
              </div>

              <!-- ACCOUNT OVERRIDES -->
              <div class="row mt-2" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                <div class="col-lg-12">
                   <h6 class="text-muted mb-3"><i class="fa fa-university"></i> تنظیمات حسابی (Accounting)</h6>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">حساب گدام (Debit Account) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2" data-placeholder="انتخاب حساب گدام">
                        <option value=""></option>

                      @foreach($allowedDebitAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">حساب تادیه (Credit Account) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                    <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2" data-placeholder="انتخاب حساب تادیه">
                        <option value=""></option>

                      @foreach($allowedCreditAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                   <div class="alert alert-info py-2" style="font-size: 0.85rem;">
                      <i class="fa fa-info-circle"></i> به صورت پیش‌فرض حساب‌های استاندارد انتخاب شده‌اند. تنها در صورت ضرورت تغییر دهید.
                   </div>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <button class="btn btn-warning btn-sm" type="button">انصراف</button>
                    <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                  </div>
                </div>
              </div>
            </form>
          @else
            <form action="/dashboard/material-purchase/{{$purchaseMaterial->id}}" method="post"
                  enctype="multipart/form-data">
              @csrf
              @method('PUT')
  
              <input type="hidden" name="old_material_category" value="{{$purchaseMaterial->material_category}}">
              <input type="hidden" name="old_material_type" value="{{$purchaseMaterial->material_type}}">
              <input type="hidden" name="old_quantity" value="{{$purchaseMaterial->quantity}}">
              <input type="hidden" name="old_price_per_kilo" value="{{$purchaseMaterial->price_per_kilo}}">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">فاکتور خرید</label>
                    <input type="text" class="form-control " value="{{$purchaseMaterial->purchase_number}}"
                           name="purchase_number">
                  </div>
                </div>
                  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">فروشنده</label>
                    <select name="seller_id" id="seller_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($sellers as $s)
                        <option value="{{ $s->id }}" {{ $purchaseMaterial->seller_id == $s->id ? 'selected' : '' }} >{{ $s->name }}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('seller_id') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                     <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">کتگوری مواد</label>
                    <select name="material_category" id="material_category" class="form-control">
                      <option value="">~~~</option>
                      @foreach($material_category as $mc)
                        <option {{ ($purchaseMaterial->material_category == $mc->material_category_id ? 'selected' : '') }} value="{{ $mc->material_category_id }}">{{ $mc->material_category }}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('material_category') {{ __('message.'.$message) }}
                      @enderror
                    </small>
                  </div>
                </div>
                  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">نوعیت مواد</label>
                    <select name="material_type" id="material_type" class="form-control">
                      <option value="">~~~</option>
                      @foreach($material_type as $mt)
                        <option {{ ($purchaseMaterial->material_type == $mt->material_type_id ? 'selected' : '') }} value="{{ $mt->material_type_id }}">{{ $mt->material_type }}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('material_type') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">گودام (Warehouse)</label>
                    <select name="warehouse_id" id="warehouse_id_edit" class="form-control">
                      @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ $purchaseMaterial->warehouse_id == $w->id ? 'selected' : '' }} >{{ $w->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">مقدار</label>
                        <input name="quantity" id="material-amount" onkeyup="Calculate()" type="text" dir="ltr"
                               value="{{ $purchaseMaterial->quantity}}" class="form-control quantity">
                        <small class="text-danger">@error('quantity') {{ __('message.'.$message) }} @enderror</small>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <label class="">واحد</label>
                        <input value="KG" type="text" class="form-control" readonly>
                      </div>
                    
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">قیمت فی کیلو به افغانی</label>
                        <input name="price_per_kilo" id="material-price" value="{{ $purchaseMaterial->price_per_kilo }}"
                               onkeyup="Calculate()" type="text" dir="ltr" class="form-control quantity">
                        <input type="hidden" value="{{$currency}}" id="currency">
                        <small class="text-danger">@error('price_per_kilo') {{ __('message.'.$message) }}@enderror
                        </small>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label class="">واحد</label>
                        <input type="text" value="AF" disabled class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">قیمت مجموع افغانی</label>
                        <input id="material-af-total-price" name="total_af" value="{{$purchaseMaterial->total_af}}"
                               type="text" dir="ltr"
                               class="form-control quantity" readonly>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label class="">واحد</label>
                        <input value="AF" type="text" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <div class="row">
                      
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <label class="">قیمت مجموع دالر</label>
                        <input id="material-total-price" value="{{$purchaseMaterial->total}}" name="total" type="text"
                               dir="ltr"
                               class="form-control quantity" readonly>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <label class="">واحد</label>
                        <input value="$" type="text" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">قیمت به حروف</label>
                    <input type="text" class="form-control " value="{{$purchaseMaterial->in_words}}" name="in_words"
                           placeholder="قیمت به حروف وارد کنید">
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">تاریخ</label>
                    <input id="purchase_date" name="purchase_date" value="{{$purchaseMaterial->purchase_date}}"
                           type="date" class="form-control quantity">
                    <small class="text-danger">@error('purchase_date') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
              
              </div>

              <!-- ACCOUNT OVERRIDES EDIT -->
              <div class="row mt-2" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                <div class="col-lg-12">
                   <h6 class="text-muted mb-3"><i class="fa fa-university"></i> تنظیمات حسابی (Accounting Edit)</h6>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">حساب گدام (Debit Account) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                    <select name="override_debit_account_id" class="form-control select2" data-placeholder="انتخاب حساب گدام">
                        <option value=""></option>

                       <option value="">Standard Default</option>
                       @foreach($allowedDebitAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">حساب تادیه (Credit Account) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                    <select name="override_credit_account_id" class="form-control select2" data-placeholder="انتخاب حساب تادیه">
                        <option value=""></option>

                       <option value="">Standard Default</option>
                       @foreach($allowedCreditAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <button class="btn btn-warning btn-sm" type="button">انصراف</button>
                    <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                  </div>
                </div>
              </div>
            </form>
          @endif
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5>لیست خرید ها</h5>
          @if(session("status"))
            <div class="alert alert-primary status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          @endif
        </div>
        <div class="card-body">
          <table class="table table-hover table-xs">
            <thead>
            <tr>
              <th>فاکتور خرید</th>
              <th>فروشنده</th>
              <th>تاریخ خرید</th>
              <th>کتگوری مواد</th>
              <th>نوعیت مواد</th>
              <th>مقدار</th>
              <th>قیمت فی کیلو</th>
              <th>قیمت مجموع افغانی</th>
              <th>قیمت مجموع دالر</th>
              <th>قیمت به حروف</th>
              <th>حالت</th>
              <th>ویرایش</th>
            </tr>
            </thead>
            <tbody>
            @foreach($purchase as $p)
              <tr>
                <td>
                  <a href="/dashboard/material-purchase/search-purchase-number/{{$p->purchase_number}},{{$p->seller_id}}"
                  >&nbsp; {{$p->purchase_number}}</a></td>
                <td>{{ $p->seller->name }}</td>
                <td dir="ltr"
                    style="text-align: right;">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-M-Y') }}</td>
                <td>{{ $p->materialCategory->material_category }}</td>
                <td>{{ $p->materialType->material_type }}</td>
                
                <td dir="ltr">{{ $p->quantity }} KG</td>
                <td dir="ltr">{{ $p->price_per_kilo .'AF' }}</td>
                <td dir="ltr">AF {{ $p->total_af}}</td>
                <td dir="ltr">$ {{ $p->total}}</td>
                
                <td>{{ $p->in_words .' ' }}</td>
                
                @if($p->status == 0)
                  
                  <td class="hideOnPrint">
                    <label class="badge badge-warning">درخواست تایید نشده</label></td>
                @else
                  
                  <td class="hideOnPrint"><label for="" class="badge badge-success">درخواست تایید
                      شد</label></td>
                
                @endif
                @if($p->status == 1 || auth()->user()->role == 'SP')
                <td><a href="/dashboard/material-purchase/{{$p->id}}/edit"
                       class="btn btn-sm btn-info"> ویرایش</a>
                </td>
                @endif
              </tr>
            @endforeach
            </tbody>
          </table>
          <p class="text-center">{{$purchase->links()}}</p>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
        $('#seller_id').select2();
      $('#material_category').select2();
      $('#material_type').select2();
      $('#override_debit_account_id').select2();
      $('#override_credit_account_id').select2();
      $('.select2').select2();
  
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {
 
              $(this).remove();
          });
      }, 2000);

      // Material Amount
      $("#material-amount").blur(function () {
          var ma = $('#material-amount').val();
          var mainma = parseFloat(ma).toFixed(2);
          if (isNaN(mainma)) {
              $("#material-amount").val();
          } else {
              $("#material-amount").val(mainma);
          }
      });
      // Material Price
      $("#material-price").blur(function () {
          var mp = $('#material-price').val();
          var mainmp = parseFloat(mp).toFixed(2);
          if (isNaN(mainmp)) {
              $("#material-price").val();
          } else {
              $("#material-price").val(mainmp);
          }
      });
      //total price
      $("#material-amount,#material-price").blur(function () {
          var mp = $('#material-price').val();
          var midmp = parseFloat(mp).toFixed(2);
          var c = $('#currency').val();
          var mainmp = midmp / c;
          var ma = $('#material-amount').val();
          var mainma = parseFloat(ma).toFixed(2);
          if (mainma != '' && mainmp != '') {
              var t = mainmp * mainma;
              var at = midmp * mainma;
              var total = parseFloat(t).toFixed(2);
              var atotal = parseFloat(at).toFixed(2);
              if (isNaN(total)) {
                  $("#material-total-price").val();

              } else {
                  $("#material-total-price").val(total);
              }
              if (isNaN(atotal)) {
                  $("#material-af-total-price").val();
              } else {
                  $("#material-af-total-price").val(atotal);
              }
          }
      });
  
  </script>
@endsection
