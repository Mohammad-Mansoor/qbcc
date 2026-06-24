@extends('dsh.master')
@section('title' , 'حسابات متفرقه')
@section('content')
  
  
  <!-- navbar -->
  
  <div id="PaidToDA">
    
    <div class="row">
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
                  <table class="table table-xs table-hover">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td><b>نام</b></td>
                      <td>{{$account->name}}</td>
                    </tr>
                    <tr>
                      <td><b>ادرس</b></td>
                      <td> {{$account->address}}</td>
                    </tr>
                    <tr>
                      <td><b>شماره تماس</b></td>
                      <td><i class="fa fa-phone"></i> {{$account->phone}}</td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <h4>ACCOUNT #: {{$account->id}}</h4>
              </div>
            </div>
            <hr>
            <div class="row">
              
              <div class="col-sm-12 hideOnPrint">
                <div class="all-form-element-inner">
                  @can('manage_different_account_payments')
                  @if(!$paymentEdit)
                    <form action="/dashboard/new-different-account-payments" method="post">
                      @csrf
                      <input type="hidden" name="account_id" value="{{$account->id}}">
                      
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-12">
                          <div class="form-group-inner">
                            <div class="row"
                                 style=" display:flex;justify-content:space-around">
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-left">مقدار پول</label>
                                
                                <input type="text" name="amount"
                                       placeholder="مقدار پول" class="form-control">
                                @error('amount') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-left">نوع پول</label>
                                <select name="currency" id="currency_id" class="form-control">
                                  <option value="3">کلدار</option>
                                  <option value="1">افغانی</option>
                                  <option value="2">دالر</option>
                                
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-left">نوع معامله</label>
                                <select name="type" id="type_id" class="form-control">
                                  <option disabled>انتخاب</option>
                                  <option value="رسید">رسید</option>
                                  <option value="گرفت">گرفت</option>
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-right">تاریخ</label>
                                <input type="date" name="date"
                                       placeholder="تاریخ را وارد کنید"
                                       class="form-control">
                                @error('date') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="1"
                                          class="form-control"
                                          placeholder="توضیحات "></textarea>
                                @error('description') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                            
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row"
                                 style="display:flex;justify-content:flex-start;margin-top: 20px;">
                              <button class="btn btn-warning btn-sm" type="reset">انصراف
                              </button>
                              <button class="btn btn-primary marginx btn-sm" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  @else
                    <form action="/dashboard/new-different-account-payments/{{$paymentEdit->id}}" method="post">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="account_id" value="{{$account->id}}">
                      
                      
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-12">
                          <div class="form-group-inner">
                            <div class="row"
                                 style=" display:flex;justify-content:space-around">
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-right">مقدار پول</label>
                                
                                <input type="text" name="amount" value="{{$paymentEdit->amount}}"
                                       class="form-control">
                                @error('amount') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-left">نوع پول</label>
                                <select name="currency" id="currency_id" class="form-control">
                                  <option value="3" {{ $paymentEdit->currency == 3 ? 'selected' : '' }}>کلدار</option>
                                  <option value="1" {{ $paymentEdit->currency == 1 ? 'selected' : '' }}>افغانی</option>
                                  <option value="2" {{ $paymentEdit->currency == 2 ? 'selected' : '' }}>دالر</option>
                                
                                </select>
                                
                                @error('currency') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-right">نوع معامله</label>
                                <select name="type" id="type_id" class="form-control">
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
                              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="pull-right">تاریخ</label>
                                <input type="date" name="date" value="{{$paymentEdit->date}}"
                                       placeholder="تاریخ را وارد کنید"
                                       class="form-control">
                                @error('date') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="1"
                                          class="form-control"
                                          placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                                @error('description') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                            
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row"
                                 style="display:flex;justify-content:flex-start;margin-top: 20px;">
                              <button class="btn btn-warning btn-sm" type="reset">انصراف
                              </button>
                              <button class="btn btn-primary marginx btn-sm" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  @endif
                  @endcan
                </div>
              </div>
            
            
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              
              <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"></div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                
                
                <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                  <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('PaidToDA')"><i
                            class="fa fa-print"></i> چاپ
                  </div>
                
                </div>
                <a href="/dashboard/new-different-account-payments-all/{{$account->id}}" style="float: left"
                   class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="sparkline8-graph text-muted">
                  
                  <div class="table-responsive">
                    <table class="table table-xs table-hover " id="account_payment">
                      <thead>
                      <tr>
                        
                        <td><b>رسید</b></td>
                        <td><b>گرفت</b></td>
                        
                        <td><b>تفصیلات</b></td>
                        <td><b>تاریخ</b></td>
                        <td><b>حالت</b></td>
                        
                        
                        <td class="hideOnPrint"><b> ویرایش</b></td>
                      
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($payments as $pa)
                        <tr>
                          
                          @if($pa->type == 'رسید')
                            <td>{{$pa->amount}} &nbsp; @if($pa->currency == 1) افغانی @elseif($pa->currency == 2 ) دالر @else کلدار @endif</td>
                          @else
                            <td>0</td>
                          @endif
                          @if($pa->type == 'گرفت')
                            <td>{{$pa->amount}}  &nbsp; @if($pa->currency == 1) افغانی @elseif($pa->currency == 2 ) دالر @else کلدار @endif</td>
                          @else
                            <td>0</td>
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
                            @can('manage_different_account_payments')
                            <td class="hideOnPrint">
                              <a href="/dashboard/new-different-account-payments/{{$pa->id}}/edit"
                                 class="btn btn-sm btn-info">ویرایش</a>
                              <button onclick="deletePayment( {{$pa->id}}, {{$pa->account_id}})" class="btn btn-danger btn-sm">
                                <i
                                        class="fa fa-tick"></i>حذف
                              </button>

                            </td>
                            @endcan
                          @endif
                        </tr>
                      @endforeach
                      <tr>
                        
                        <th><b>گرفت ها(افغانی)</b></th>
                        
                        <th><b>گرفت ها(دالر)</b></th>
                        
                        <th><b>گرفت ها(کلدار)</b></th>
                      </tr>
                      <tr>
                        <td><b>{{$debits_af}} </b></td>
                        <td><b>{{$debits_usd}} </b></td>
                        <td><b>{{$debits_cd}} </b></td>
                      
                      </tr>
                      
                      <tr>
                        
                        <th><b>رسیدات(افغانی)</b></th>
                        <th><b>رسیدات(دالر)</b></th>
                        <th><b>رسیدات(کلدار)</b></th>
                      </tr>
                      <tr>
                        <td><b>{{$credits_af}} </b></td>
                        <td><b>{{$credits_usd}} </b></td>
                        <td><b>{{$credits_cd}} </b></td>
                      </tr>
                      
                      <tr>
                        
                        <th><b>صرف بیلانس(افغانی)</b></th>
                        <th><b>صرف بیلانس(دالر)</b></th>
                        <th><b>صرف بیلانس(کلدار)</b></th>
                      </tr>
                      <tr>
                        <td style="direction: ltr"><b> {{$credits_af - $debits_af}} </b></td>
                        <td style="direction: ltr"><b> {{$credits_usd - $debits_usd}} </b></td>
                        <td style="direction: ltr"><b> {{$credits_cd - $debits_cd}} </b></td>
                      
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
    </div>
  </div>

@endsection
@section('scripts')
  
  <script>
      $(document).ready(function () {
          $("#account_payment").tableExport({
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
          var $buttons = $('#account_payment').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

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
                          url: '/dashboard/new-different-account-payments/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/new-different-account/' + account_id
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