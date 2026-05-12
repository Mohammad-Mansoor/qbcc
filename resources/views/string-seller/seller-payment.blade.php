@extends('dsh.master')

@section('content')
  <br>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="sparkline8-graph text-muted">
                
                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td><b>نام</b></td>
                      <td>{{$seller->name}}</td>
                    </tr>
                    <tr>
                      <td><b>ادرس</b></td>
                      <td> {{$seller->address}}</td>
                    </tr>
                    <tr>
                      <td><b>شماره تماس</b></td>
                      
                      <td>
                        {{$seller->phone}}
                        <i class="fa fa-phone"></i>
                      </td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h4>ACCOUNT #: {{$seller->id}}</h4>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-sm-12 hideOnPrint">
              <div class="all-form-element-inner">
                @if(!$paymentEdit)
                  <form action="/dashboard/string-seller-payments" method="post">
                    @csrf
                    <input type="hidden" name="seller_id" value="{{$seller->id}}">
                    
                    <div class="row" style=" display:flex;justify-content:center">
                      <div class="col-sm-12">
                        <div class="form-group-inner">
                          <div class="row"
                               style=" display:flex;justify-content:space-around">
                            <div class="col-xs-2">
                              <label class="pull-right">فاکتور خرید</label>
                              <select name="purchase_number" id="" class="form-control">
                                <option value="نقد">نقد</option>
                                @foreach($purchase_numbers as $ch)
                                  <option value="{{$ch->purchase_number}}">{{$ch->purchase_number}}</option>
                                @endforeach
                              </select>
                              
                              @error('purchase_number') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">نرخ دالر</label>
                              <input type="text" name="dollar_rate" value="{{$currency}}"
                                     class="form-control">
                              @error('dollar_rate') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">مقدار پول</label>
                              <input type="text" name="amount" value="{{old('amount')}}"
                                     placeholder="مبلغ پول " class="form-control">
                              @error('amount') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">نوع پول</label>
                              <select name="money_type" id="" class="form-control">
                                <option disabled>انتخاب</option>
                                <option value="افغانی">افغانی</option>
                                <option value="دالر">دالر</option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">نوع معامله</label>
                              <select name="type" id="" class="form-control">
                                <option disabled>انتخاب</option>
                                <option value="رسید">رسید</option>
                                <option value="گرفت">گرفت</option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            
                            <div class="col-xs-2 center marginy">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1"
                                        class="form-control"
                                        placeholder="توضیحات "></textarea>
                              @error('description') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date"
                                     placeholder="تاریخ را وارد کنید"
                                     class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                        </div>
                      
                      </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div class="form-group-inner">
                          <div class="row"
                               style="display:flex;justify-content:flex-start;">
                            <button class="btn btn-warning btn-sm" type="reset">انصراف
                            </button>
                            <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                      class="fa fa-save"></span> ذخیره
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                @else
                  <form action="/dashboard/string-seller-payments/{{$paymentEdit->id}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="seller_id" value="{{$seller->id}}">
                    
                    
                    <div class="row" style=" display:flex;justify-content:center">
                      <div class="col-sm-12">
                        <div class="form-group-inner">
                          <div class="row"
                               style=" display:flex;justify-content:space-around">
                            <div class="col-xs-2">
                              <label class="pull-right">فاکتور خرید</label>
                              <select name="purchase_number" id="" class="form-control">
                                <option value="نقد">نقد</option>
                                @foreach($purchase_numbers as $ch)
                                  <option {{ $paymentEdit->purchase_number ==  $ch->purchase_number  ? 'selected' : '' }}  value="{{$ch->purchase_number}}">{{$ch->purchase_number}}</option>
                                @endforeach
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">نرخ دالر</label>
                              <input type="text" name="dollar_rate" value="{{$paymentEdit->dollar_rate}}"
                                     class="form-control">
                              @error('dollar_rate') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            
                            <div class="col-xs-2">
                              <label class="pull-right">مقدار پول</label>
                              <input type="text" name="amount"
                                     value="@if($paymentEdit->amount > 0 ) {{$paymentEdit->amount}} @elseif($paymentEdit->amount_af > 0)  {{$paymentEdit->amount_af}} @endif"
                                     class="form-control">
                              @error('amount') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">نوع پول</label>
                              <select name="money_type" id="" class="form-control">
                                <option disabled>انتخاب</option>
                                <option {{ $paymentEdit->amount_af > 0 ? 'selected' : '' }}  value="افغانی">افغانی
                                </option>
                                <option {{ $paymentEdit->amount > 0  ? 'selected' : '' }}  value="دالر">دالر</option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">نوع معامله</label>
                              <select name="type" id="" class="form-control">
                                <option disabled>انتخاب</option>
                                <option {{ $paymentEdit->type == 'رسید' ? 'selected' : '' }} value="رسید">رسید
                                </option>
                                <option {{ $paymentEdit->type == 'گرفت' ? 'selected' : '' }} value="گرفت">گرفت
                                </option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            
                            <div class="col-xs-2 center marginy">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1"
                                        class="form-control"
                                        placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                              @error('description') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-xs-2">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$paymentEdit->date}}"
                                     placeholder="تاریخ را وارد کنید"
                                     class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div class="form-group-inner">
                          <div class="row"
                               style="display:flex;justify-content:flex-start;">
                            <button class="btn btn-warning btn-sm" type="reset">انصراف
                            </button>
                            <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                      class="fa fa-save"></span> ذخیره
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  
                  </form>
                @endif
              </div>
            </div>
          
          
          </div>
        </div>
      </div>
      
      <div class="card" id="seller-payment">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <div class="btn btn-primary btn-sm hideOnPrint pull-left" onclick="printPage('seller-payment')"
                   style="position: relative;float: left;"><i class="fa fa-print"></i> Print
              </div>
              <a href="/dashboard/seller-payments-all/{{$seller->id}}" style="float: left"
                 class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              
              
              <div class="table-responsive">
                <table class="table table-xs table-hover">
                  <thead>
                  <tr>
                    <td><b>رسید(دالر)</b></td>
                    <td><b>گرفت(دالر)</b></td>
                    <td><b>رسید(افغانی)</b></td>
                    <td><b>گرفت(افغانی)</b></td>
                    <td><b>فاکتور</b></td>
                    <td><b>تفصیلات</b></td>
                    <td><b>تاریخ</b></td>
                    <td><b>حالت</b></td>
                    <td class="hideOnPrint text-center"><b>عملیات</b></td>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($payments as $pa)
                    <tr class="ur{{$pa->id}}">
  
                      @if($pa->type == 'رسید')
                        @if($pa->amount > 0)
                          <td>{{$pa->amount}} </td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      @if($pa->type == 'گرفت')
                        @if($pa->amount > 0)
                          <td>{{$pa->amount}}</td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
  
                      @if($pa->type == 'رسید')
                        @if($pa->amount_af > 0)
                          <td>{{$pa->amount_af}} </td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      @if($pa->type == 'گرفت')
                        @if($pa->amount_af > 0)
                          <td>{{$pa->amount_af}}</td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      @if($pa->purhchase_number == 'نقد')
                        <td>نقد</td>
                      @else
                        
                        
                        <td>
                          <a href="/dashboard/material-purchase/search-purchase-number/{{$pa->purchase_number}}{{$pa->seller_id}}">&nbsp; {{$pa->purchase_number}}</a></td>
                      
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
                          <a href="/dashboard/string-seller-payments/{{$pa->id}}/edit"
                             class="btn btn-sm btn-info">ویرایش</a>
                             
                          @php
                              $transaction = \App\LedgerTransaction::where('source_type', 'seller_payment')->where('source_id', $pa->id)->first();
                          @endphp
                          @if($transaction)
                              <a href="{{ route('accounting.journals.show', $transaction->id) }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-book"></i>&nbsp; روزنامچه مالی</a>
                          @endif
                          
                          <button onclick="deletePayment({{$pa->id}}, {{$pa->seller_id}})"
                                  class="btn btn-danger btn-sm ">
                            <i class="fa fa-tick"></i>حذف
                          </button>
                        </td>
                      @endif
                    </tr>
                  @endforeach
                  <tr>
  
                    <th><b>گرفت ها(افغانی)</b></th>
                    <th><b>گرفت ها(دالر)</b></th>
                  </tr>
                  <tr>
                    <td><b>{{$debits_af}} </b></td>
                    <td><b>{{$debits_us}} </b></td>
                  </tr>

                  <tr>
                    <th><b>رسیدات(افغانی)</b></th>
                    <th><b>رسیدات(دالر)</b></th>
                  </tr>
                  <tr>
                    <td><b>{{$credit_af}} </b></td>
                    <td><b>{{$credit_us}} </b></td>
                  </tr>
                  <tr>
  
                    <th><b>صرف بیلانس(افغانی)</b></th>
                    <th><b>صرف بیلانس(دالر)</b></th>
                  </tr>
                  <tr>
                    @if($credit_af - $debits_af > 0)
                      <td style="direction: ltr;color: green;"><b> {{$credit_af - $debits_af}} </b></td>
                    @elseif($credit_af - $debits_af < 0)
                      <td style="direction: ltr;color: red;"><b> {{round($credit_af - $debits_af , 2)}} </b>
                      </td>
                    @else
                      <td style="direction: ltr">0</td>
                    @endif
                    @if($credit_us - $debits_us > 0)
                      <td style="direction: ltr;color: green;"><b> {{$credit_us - $debits_us}} </b></td>
                    @elseif($credit_us - $debits_us < 0)
                      <td style="direction: ltr;color: red;"><b> {{$credit_us - $debits_us}} </b></td>
                    @else
                      <td style="direction: ltr">0</td>
                    @endif
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
    </div>
  </div>
@endsection
@section('scripts')
  
  <script>
      $(document).ready(function() {
          $('#override_debit_account_id').select2();
          $('#override_credit_account_id').select2();
          $('#override_debit_account_id_edit').select2();
          $('#override_credit_account_id_edit').select2();
      });


      function deletePayment(id, seller_id) {

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
                          url: '/dashboard/string-seller-payments/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/string-seller-payments/' + seller_id
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
  
  
  </script>
@endsection