@extends('dsh.master')
@section('title' , 'حسابات تار')
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
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
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
                      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <label class="pull-left">مقدار تار</label>
                        
                        <input type="text" name="amount"
                               placeholder="مقدار تار" class="form-control">
                        @error('amount') <p class="text-danger">
                          {{trans('message.'.$message)}}</p>
                        @enderror
                      </div>
                      
                      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <label class="pull-left">نوعیت تار</label>
                        <select name="type_id" id="" class="form-control">
                          @foreach($material_type as $t)
                            <option value="{{$t->material_type_id}}">{{$t->material_type}}</option>
                          @endforeach
                        </select>
                        
                        @error('type') <p class="text-danger">
                          {{trans('message.'.$message)}}</p>
                        @enderror
                      </div>
                      
                      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <label class="pull-left">نوع معامله</label>
                        <select name="type" id="" class="form-control">
                          <option disabled>انتخاب</option>
                          <option value="رسید">رسید</option>
                          <option value="گرفت">گرفت</option>
                        </select>
                        
                        @error('type') <p class="text-danger">
                          {{trans('message.'.$message)}}</p>
                        @enderror
                      </div>
                      
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 center marginy">
                        <label class="">توضیحات</label>
                        <textarea name="description" id="description" rows="1"
                                  class="form-control"
                                  placeholder="توضیحات "></textarea>
                        @error('description') <p class="text-danger">
                          {{trans('message.'.$message)}}</p>
                        @enderror
                      </div>
                      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <label class="pull-right">تاریخ</label>
                        <input type="date" name="date"
                               placeholder="تاریخ را وارد کنید"
                               class="form-control">
                        @error('date') <p class="text-danger">
                          {{trans('message.'.$message)}}</p>
                        @enderror
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <button class="btn btn-warning btn-sm" type="reset">انصراف
                      </button>
                      <button class="btn btn-primary marginx btn-sm" type="submit"><span
                                class="fa fa-save"></span> ذخیره
                      </button>
                    </div>
                  </form>
                @else
                  <form action="/dashboard/material-account-payments/{{$paymentEdit->id}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="account_id" value="{{$account->id}}">
               
                      
                      
                          <div class="row">
                            
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">مقدار تار</label>
                              <input type="text" name="amount"
                                     value="{{$paymentEdit->amount}}"
                                     class="form-control">
                              @error('amount') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">نوعیت تار</label>
                              <select name="type_id" id="" class="form-control">
                                @foreach($material_type as $t)
                                  <option {{ $paymentEdit->type_id == $t->material_type_id  ? 'selected' : '' }} value="{{$t->material_type_id}}">{{$t->material_type}}</option>
                                @endforeach
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
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
                            
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 center marginy">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1"
                                        class="form-control"
                                        placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                              @error('description') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$paymentEdit->date}}"
                                     placeholder="تاریخ را وارد کنید"
                                     class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          
                          
                          </div>
                  
                    
                          <div class="row"
                               style="margin-top: 20px;">
                            <button class="btn btn-warning btn-sm" type="reset">انصراف
                            </button>
                            <button class="btn btn-primary marginx btn-sm" type="submit"><span
                                      class="fa fa-save"></span> ذخیره
                            </button>
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
          <div class="card-header">
            <div class="row">
              
              <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"></div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                
                
                <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                  <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('PaidToDA')"><i
                            class="fa fa-print"></i> چاپ
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
                    <table class="table table-xs table-hover " id="account_payment">
                      <thead>
                      <tr>
                        
                        <td><b>بیلانس</b></td>
                        <td><b>رسید</b></td>
                        <td><b>گرفت</b></td>
                        <td><b>نوعیت</b></td>
                        <td><b>تفصیلات</b></td>
                        <td><b>تاریخ</b></td>
                        <td><b>حالت</b></td>
                        
                        
                        <td class="hideOnPrint"><b> ویرایش</b></td>
                      
                      </tr>
                      </thead>
                      <tbody>
                      @php($total_rasid = 0)
                      @php($total_gerft = 0)
                      @foreach($payments as $pa)
                      
                        <tr>
                          @if($pa->type == 'رسید')
                            <span style="display: none"> {{$total_rasid = $total_rasid +  $pa->amount}}</span>
                          @endif
                          
                          @if($pa->type == 'گرفت')
                            <span style="direction: ltr;display: none"> {{$total_gerft = $total_gerft +  $pa->amount}} </span>
                          @endif
  
                          <td style="direction: ltr">{{$total_rasid - $total_gerft}} KG</td>
                          @if($pa->type == 'رسید')
                            <td style="direction: ltr"> {{$pa->amount}} KG</td>
                          @else
                            <td>0</td>
                          @endif
                          @if($pa->type == 'گرفت')
                            <td style="direction: ltr"> {{$pa->amount}} KG</td>
                          @else
                            <td>0</td>
                          @endif
                          
                          <td>{{$pa->materialtype->material_type}}</td>
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
                            <td class="hideOnPrint">
                              <a href="/dashboard/material-account-payments/{{$pa->id}}/edit"
                                 class="btn btn-sm btn-info">ویرایش</a>
  
                              <button onclick="deletePayment( {{$pa->id}}, {{$pa->account_id}})"
                                      class="btn btn-danger btn-sm "><i
                                        class="fa fa-tick"></i>حذف
                              </button>
                            </td>
                          @endif
                        </tr>
                      @endforeach
                      <tr>
                        <td style="direction: ltr"><b>{{$debits}} KG</b></td>
                        <td><b>گرفت ها</b></td>
                      </tr>
                      <tr>
                        <td style="direction: ltr"><b>{{$credits}} KG</b></td>
                        <td><b>رسیدات</b></td>
                      </tr>
                      <tr>
                        <td style="direction: ltr"><b> {{$credits - $debits}} KG</b></td>
                        <td><b>صرف بیلانس</b></td>
                      </tr>
                      
                      </tbody>
                    </table>
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
                          url: '/dashboard/material-account-payments/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/material-accounts/' + account_id
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