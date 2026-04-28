@extends('dsh.master')

@section('content')
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
              <form action="/dashboard/customer-payments" method="post">
                @csrf
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                
                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right"> انوایس نمبر</label>
                      <select name="invoice_number" id="" class="form-control">
                        <option value="نقد">نقد</option>
                        @foreach($invoice_numbers as $ch)
                          <option value="{{$ch->invoice_no}}">{{$ch->invoice_no}}</option>
                        @endforeach
                      </select>
                      
                      @error('wash_number') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right">نرخ دالر</label>
                      <input type="text" name="dollar_rate" value="{{$currency}}"
                             class="form-control">
                      @error('dollar_rate') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right">مقدار پول</label>
                      <input type="text" name="amount" value="{{old('amount')}}"
                             placeholder="مبلغ پول " class="form-control">
                      @error('amount') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
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
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
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
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
                      <label class="">توضیحات</label>
                      <textarea name="description" id="description" rows="1"
                                class="form-control"
                                placeholder="توضیحات "></textarea>
                      @error('description') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
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
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
                      <button class="btn btn-warning btn-sm" type="reset">انصراف
                      </button>
                      <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                class="fa fa-save"></span> ذخیره
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form action="/dashboard/customer-payments/{{$paymentEdit->id}}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                <div class="row">
                  
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right">انوایس نمبر</label>
                      <select name="invoice_number" id="" class="form-control">
                        <option value="نقد">نقد</option>
                        @foreach($invoice_numbers as $ch)
                          <option {{ $paymentEdit->invoice_number ==  $ch->invoice_no  ? 'selected' : '' }}  value="{{$ch->invoice_no}}">{{$ch->invoice_no}}</option>
                        @endforeach
                      </select>
                      
                      @error('type') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right">نرخ دالر</label>
                      <input type="text" name="dollar_rate" value="{{$paymentEdit->dollar_rate}}"
                             class="form-control">
                      @error('dollar_rate') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
                      <label class="pull-right">مقدار پول</label>
                      <input type="text" name="amount"
                             value="@if($paymentEdit->amount > 0 ) {{$paymentEdit->amount}} @elseif($paymentEdit->amount_af > 0)  {{$paymentEdit->amount_af}} @endif"
                             class="form-control">
                      @error('amount') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
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
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
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
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
                      <label class="">توضیحات</label>
                      <textarea name="description" id="description" rows="1"
                                class="form-control"
                                placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                      @error('description') <p class="text-danger">
                        {{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
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
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">انصراف
                      </button>
                      <button class="btn btn-primary marginx" type="submit"><span
                                class="fa fa-save"></span> ذخیره
                      </button>
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
                <td><b>رسید(دالر)</b></td>
                <td><b>گرفت(دالر)</b></td>
                <td><b>رسید(افغانی)</b></td>
                <td><b>گرفت(افغانی)</b></td>
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
                      <a href="/dashboard/customer-payments/{{$pa->id}}/edit"
                         class="btn btn-sm btn-info">ویرایش</a>
                      
                      @if($pa->ledger_transaction_id)
                          <a href="{{ route('accounting.journals.show', $pa->ledger_transaction_id) }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-book"></i>&nbsp; روزنامچه مالی</a>
                      @endif
                      
                      <button onclick="deletePayment( {{$pa->id}}, {{$pa->customer_id}})" class="btn btn-danger btn-sm">
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
@endsection
@section('scripts')
  
  <script>

      $(document).ready(function () {
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
  
  
  </script>
@endsection