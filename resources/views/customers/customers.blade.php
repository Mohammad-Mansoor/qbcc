@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  <br>
  <div class="row" id="customers">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="form1">
      <div class="card hideOnPrint">
        <div class="card-header">
          @if(!$customerEdit)
            <h5>ایجاد مشتری جدید</h5>
          @else
            <h5>ویرایش مشتری</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$customerEdit)
              <form method="post" id="" action="/dashboard/customers">
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>کود مشتری</label>
                      <input type="text" value="{{old('customer_code')}}" class="form-control" required
                             name="customer_code">
                      @error('customer_code') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نام</label>
                      <input type="text" class="form-control" value="{{old('name')}}" required name="name">
                      @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نوعیت حساب</label>
                      <select name="type" class="form-control" id="">
                        <option {{ (Request::old('type') == 'مشتری قالین' ? 'selected' : '') }} value="مشتری قالین">
                          مشتری قالین
                        </option>
                      
                      </select>
                      @error('type') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>کمپنی</label>
                      <input type="text" value="{{old('company_name')}}" class="form-control" required
                             name="company_name">
                      @error('company_name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label>ادرس کمپنی</label>
                      <textarea type="text" value="{{old('company_address')}}" class="form-control" required
                                rows="1"
                                name="company_address"></textarea>
                      @error('company_address') <p class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نمبر مبایل</label>
                      <input type="text" value="{{old('phone')}}" class="form-control" required name="phone">
                      @error('phone') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>ایمیل</label>
                      <input type="text" value="{{old('email')}}" class="form-control" name="email">
                      @error('email') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>ویب سایت</label>
                      <input type="text" value="{{old('website')}}" class="form-control" name="website">
                      @error('website') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-warning" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                    </div>
                  </div>
                </div>
              
              </form>
            @else
              <form method="post" id="" action="/dashboard/customers/{{$customerEdit->id}}">
                {{method_field('patch')}}
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نام</label>
                      <input type="text" value="{{$customerEdit->customer_code}}" class="form-control" required
                             name="customer_code">
                      @error('customer_code') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نام</label>
                      <input type="text" class="form-control" value="{{$customerEdit->name}}" required
                             name="name">
                      @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نوعیت حساب</label>
                      <select name="type" class="form-control" id="">
                        <option value="مشتری قالین" {{($customerEdit->type == 'مشتری قالین' ? 'selected' : '')}}>
                          مشتری قالین
                        </option>
                      
                      </select>
                      @error('type') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">کمپنی</label>
                      <input type="text" class="form-control" value="{{$customerEdit->company_name}}" required
                             name="company_name">
                      @error('company_name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">آدرس کمپنی</label>
                      <input type="text" class="form-control" value="{{$customerEdit->company_address}}" required
                             name="company_address">
                      @error('company_address') <p class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نمبر مبایل</label>
                      <input type="text" class="form-control" required value="{{$customerEdit->phone}}"
                             name="phone">
                      @error('phone') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">ایمیل</label>
                      <input type="text" class="form-control" value="{{$customerEdit->email}}"
                             name="email">
                      @error('email') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">ویب سایت</label>
                      <input type="text" class="form-control" value="{{$customerEdit->website}}"
                             name="website">
                      @error('website') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
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
          <h5>مشتری ها</h5>
          
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
    
              <form action="/dashboard/customers/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('customers')"><i
                          class="fa fa-print"></i> چاپ
                </div>
  
              </div>
  
              <a href="/dashboard/customer-accounts" style="float: left" class="btn btn-sm btn-info hideOnPrint">مشتریان
                حسابدار</a>
            </div>
           
            
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs table-hover" id="customer_list">
              <thead>
              <tr>
                <th>نمبر حساب</th>
                <th>کود مشتری</th>
                <th>نام</th>
                <th class="hideOnPrint">کمپنی</th>
                <th>مبایل</th>
                <th>باقیات(دالر)</th>
                <th>باقیات(افغانی)</th>
                <th class="hideOnPrint">ویرایش</th>
                <th class="hideOnPrint">حسابات</th>
              
              </tr>
              </thead>
              <tbody>
              @if(!isset($accounts))
                @foreach($customers as $cust)
                  
                  <tr>
                       <td>{{ $cust->id}}</td>
                    <td>{{ $cust->customer_code}}</td>
                    <td>{{ $cust->name}}</td>
                    <td class="hideOnPrint">{{ $cust->company_name}}</td>
                    <td>{{ $cust->phone}}</td>
                    @php($total_af = 0)
                    @php($total_usd = 0)
                      <?php

                      $total_af = \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'گرفت')->sum('amount_af');
                      $total_usd = \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'گرفت')->sum('amount');

                      ?>
  
                    {{--for dollars balance--}}
                    @if($total_usd > 0)
                      <td style="direction: ltr;color: green;">{{ $total_usd}}</td>
                    @elseif($total_usd < 0)
                      <td style="direction: ltr;color: red;">{{$total_usd}}</td>
                    @else
                      <td>{{ $total_usd }}</td>
                    @endif
                    {{--end dollars balance--}}
  
                    {{--afghani balance--}}
                    @if($total_af > 0)
                      <td style="direction: ltr;color: green;">{{$total_af }}</td>
                    @elseif($total_af < 0)
                      <td style="direction: ltr;color: red;">{{$total_af }}</td>
                    @else
                      <td>{{ $total_af }}</td>
                    @endif
  
  
                    <td class="hideOnPrint">
                      <a href="/dashboard/customers/{{$cust->id}}/edit"
                         class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                    </td>
                    
                    <td class="hideOnPrint">
                      <a href="/dashboard/customer-payments/{{$cust->id}}"
                         class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                    </td>
                  
                  </tr>
                @endforeach
              @else
                @foreach($customers as $cust)
  
                  @php($total_af = 0)
                  @php($total_usd = 0)
                  <?php

                  $total_af = \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'گرفت')->sum('amount_af');
                  $total_usd = \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'گرفت')->sum('amount');

                  ?>

                  @if($cust->payment->count() > 0 && $total_af !=  0 || $total_usd != 0)
                    <tr>
                      <td>{{ $cust->customer_code}}</td>
                      <td>{{ $cust->name}}</td>
                      <td class="hideOnPrint">{{ $cust->company_name}}</td>
                      <td>{{ $cust->phone}}</td>
  
  
                      {{--for dollars balance--}}
                      @if($total_usd > 0)
                        <td style="direction: ltr;color: green;">{{ $total_usd}}</td>
                      @elseif($total_usd < 0)
                        <td style="direction: ltr;color: red;">{{$total_usd}}</td>
                      @else
                        <td>{{ $total_usd }}</td>
                      @endif
                      {{--end dollars balance--}}
  
                      {{--afghani balance--}}
                      @if($total_af > 0)
                        <td style="direction: ltr;color: green;">{{$total_af }}</td>
                      @elseif($total_af < 0)
                        <td style="direction: ltr;color: red;">{{$total_af }}</td>
                      @else
                        <td>{{ $total_af }}</td>
                      @endif
                      
                      
                      <td class="hideOnPrint">
                        <a href="/dashboard/customers/{{$cust->id}}/edit"
                           class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                      </td>
                      
                      <td class="hideOnPrint">
                        <a href="/dashboard/customer-payments/{{$cust->id}}"
                           class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                      </td>
                    
                    
                    </tr>
                  @endif
                @endforeach
              @endif
              
              @if(!isset($search))
                <tr style="background: gainsboro">
                  <td class="hideOnPrint"></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  @if($credit_us  -  $debit_us  > 0)
                    <td style="font-size: 10px;direction: ltr;color: green">
                      {{$credit_us  -  $debit_us }}</td>
                  @elseif($credit_us - $debit_us < 0)
                    <td style="font-size: 10px;direction: ltr;color: red">
                      {{$credit_us  - $debit_us}}</td>
                  @else
                    <td style="font-size: 10px;direction: ltr;">
                      {{$credit_us - $debit_us }}</td>
                  @endif
  
                  @if($credit_af  -  $debit_af  > 0)
                    <td style="font-size: 10px;direction: ltr;color: green;">
                      {{$credit_af  -  $debit_af}}</td>
                  @elseif($credit_af - $debit_af < 0)
                    <td style="font-size: 10px;direction: ltr;color: red;">
                      {{$credit_af  - $debit_af }}</td>
                  @else
                    <td style="font-size: 10px;direction: ltr;">
                      {{$credit_af  -  $debit_af }}</td>
                  @endif
                  
                  <td>مجموعه</td>
                  
                  <td class="hideOnPrint"></td>
                </tr>
              @endif
              </tbody>
            </table>
            {{-- <span class="text-center">{{$customers->links()}}</span> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
      $(document).ready(function () {
          $("#customer_list").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#customer_list').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
      
      $('#form2').hide();


      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);
  
  </script>
@endsection

