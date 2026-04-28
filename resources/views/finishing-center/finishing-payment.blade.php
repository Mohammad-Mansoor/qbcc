@extends('dsh.master')

@section('content')
  <br>
  <div class="row" id="finishing-payment">
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
            <div class="col-sm-4">
              <div class="table-responsive">
                <table class="table table-xs table-hover text-left">
                  <thead>
                  
                  </thead>
                  <tbody>
                  
                  <tr>
                    <td><b>نام تیم</b></td>
                    <td>{{$team->name}}</td>
                  </tr>
                  
                  
                  </tbody>
                </table>
                <hr>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h5>ACCOUNT #: {{$team->id}}</h5>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
              <div class="all-form-element-inner">
                @if(!$paymentEdit)
                  <form action="/dashboard/finishing-payments" method="post">
                    @csrf
                    <input type="hidden" name="team_id" value="{{$team->id}}">
                    
                          <div class="row">
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12" style="margin-top: 10px">
                              <label class="pull-right">نمبر تیاری</label>
                              <select name="finish_number" id="finish_number" class="form-control">
                                <option value="نقد">نقد</option>
                                @foreach($finish_numbers as $ch)
                                  <option value="{{$ch->finish_number}}">{{$ch->finish_number}}</option>
                                @endforeach
                              </select>
                              
                              @error('wash_number') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
                              <label class="pull-right">نرخ دالر</label>
                              <input type="text" name="dollar_rate" value="{{$currency}}"
                                     class="form-control">
                              @error('dollar_rate') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                              <label class="pull-right">مقدار پول</label>
                              <input type="text" name="amount" value="{{old('amount')}}"
                                     placeholder="مبلغ پول " class="form-control">
                              @error('amount') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" >
                              <label class="pull-right">نوع پول</label>
                              <select name="money_type" id="money_type" class="form-control">
                                <option disabled>انتخاب</option>
                                <option value="افغانی">افغانی</option>
                                <option value="دالر">دالر</option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" >
                              <label class="pull-right">نوع معامله</label>
                              <select name="type" id="type_id" class="form-control">
                                <option disabled>انتخاب</option>
                                <option value="رسید">رسید</option>
                                <option value="گرفت">گرفت</option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1"
                                        class="form-control"
                                        placeholder="توضیحات "></textarea>
                              @error('description') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date"
                                     placeholder="تاریخ را وارد کنید"
                                     class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                      <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
                        
                        <button class="btn btn-warning btn-sm" type="reset">انصراف
                        
                        </button>
                        <button class="btn btn-primary btn-sm" type="submit"><span
                                  class="fa fa-save"></span> ذخیره
                        </button>
                      </div>
                    </div>
                  
                  </form>
                @else
                  <form action="/dashboard/finishing-payments/{{$paymentEdit->id}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="team_id" value="{{$team->id}}">
                    
                    
                    <div class="row" style=" display:flex;justify-content:center">
                     
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12" >
                              <label class="pull-right">نمبر تیاری</label>
                              <select name="finish_number" id="finish_number" class="form-control">
                                <option value="نقد">نقد</option>
                                @foreach($finish_numbers as $ch)
                                  <option {{ $paymentEdit->finish_number ==  $ch->finish_number  ? 'selected' : '' }}  value="{{$ch->finish_number}}">{{$ch->finish_number}}</option>
                                @endforeach
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
                              <label class="pull-right">نرخ دالر</label>
                              <input type="text" name="dollar_rate" value="{{$paymentEdit->dollar_rate}}"
                                     class="form-control">
                              @error('dollar_rate') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                              <label class="pull-right">مقدار پول</label>
                              <input type="text" name="amount"
                                     value="@if($paymentEdit->amount > 0 ) {{$paymentEdit->amount}} @elseif($paymentEdit->amount_af > 0)  {{$paymentEdit->amount_af}} @endif"
                                     class="form-control">
                              @error('amount') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" >
                              <label class="pull-right">نوع پول</label>
                              <select name="money_type" id="money_type" class="form-control">
                                <option disabled>انتخاب</option>
                                <option {{ $paymentEdit->amount_af > 0 ? 'selected' : '' }}  value="افغانی">افغانی
                                </option>
                                <option {{ $paymentEdit->amount > 0  ? 'selected' : '' }}  value="دالر">دالر</option>
                              </select>
                              
                              @error('type') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" >
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
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1"
                                        class="form-control"
                                        placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                              @error('description') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$paymentEdit->date}}"
                                     placeholder="تاریخ را وارد کنید"
                                     class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                    
                    </div>
                    <div class="row" style="margin-top: 10px;">
                      <div class="form-group-inner">
                        <div class="row">
                          <button class="btn btn-warning btn-sm" type="reset">انصراف
                          </button>
                          <button class="btn btn-primary btn-sm" type="submit"><span
                                    class="fa fa-save"></span> ذخیره
                          </button>
                        </div>
                      </div>
                    </div>
                  
                  </form>
                @endif
              </div>
            </div>
            
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('finishing-payment')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
              <a href="/dashboard/finishing-payments-all/{{$team->id}}" style="float: left"
                 class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
            
            </div>
            
            
            <div class="table-responsive">
              
              <table class="table table-xs table-hover" id="finishing_payment">
                <thead>
                <tr>
                  <td><b>رسید(دالر)</b></td>
                  <td><b>گرفت(دالر)</b></td>
                  <td><b>رسید(افغانی)</b></td>
                  <td><b>گرفت(افغانی)</b></td>
                  <td><b>نمبر تیاری</b></td>
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
                    @if($pa->finish_number == 'نقد')
                      <td>نقد</td>
                    @else
                      <td>
                        <a href="/dashboard/finishing-center/search-finish-number/{{$pa->finish_number}},{{$pa->team_id}}"
                        >&nbsp; {{$pa->finish_number}}</a></td>
                    
                    
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
                        <a href="/dashboard/finishing-payments/{{$pa->id}}/edit"
                           class="btn btn-sm btn-info">ویرایش</a>
                           
                        @php
                            $transaction = \App\LedgerTransaction::where('source_type', 'finishing_payment')->where('source_id', $pa->id)->first();
                        @endphp
                        @if($transaction)
                            <a href="{{ route('accounting.journals.show', $transaction->id) }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-book"></i>&nbsp; روزنامچه مالی</a>
                        @endif
                        
                        <button onclick="deletePayment( {{$pa->id}}, {{$pa->team_id}})"
                                class="btn btn-danger btn-sm "><i
                                  class="fa fa-tick"></i>حذف
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
@endsection
@section('scripts')
  
  <script>
    
    $('#finish_number').select2();

      $(document).ready(function () {
          $("#finishing_payment").tableExport({
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
          var $buttons = $('#finishing_payment').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });

      function deletePayment(id, team_id) {

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
                          url: '/dashboard/finishing-payments/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/finishing-payments/' + team_id
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