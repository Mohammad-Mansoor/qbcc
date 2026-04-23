@extends('dsh.master')
@section('title' , 'تیم کچایی')
@section('content')
  <!-- navbar -->
  @if(session("status"))
    <div class="alert alert-success status text-center" style="display:none;" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
      {{session('status')}}
    </div>
  
  @endif
  @if(session("error"))
    
    <div class="alert alert-success status text-center" style="display:none;" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
      {{session('error')}}
    </div>
  
  @endif
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4>ایجاد تیم جدید</h4>
        </div>
        <div class="card-body">
          @if(!$teamEdit)
            <form action="/dashboard/kachaee-team" method="post">
              @csrf
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام</label>
                    <input type="text" value="{{ Request::old('name') }}" id="name" name="name"
                           class="form-control">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام پدر</label>
                    <input type="text" id="father_name" value="{{ Request::old('father_name') }}"
                           name="father_name" class="form-control">
                    <small class="text-danger">@error('father_name') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام ضمانت کننده</label>
                    <input type="text" id="g_name" name="g_name" value="{{ Request::old('g_name') }}"
                           class="form-control">
                    <small class="text-danger">@error('g_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام پدر کلان</label>
                    <input type="text" id="grand_father_name" name="grand_father_name"
                           value="{{ Request::old('grand_father_name') }}" class="form-control">
                    <small class="text-danger">@error('grand_father_name') {{ __('message.'.$message) }}
                      @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">شماره تماس</label>
                    <input type="text" id="contact_no" name="contact_no"
                           value="{{ Request::old('contact_no') }}" dir="ltr" class="form-control">
                    <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نمبر تذکره</label>
                    <input type="text" id="national_id" name="national_id"
                           value="{{ Request::old('national_id') }}" class="form-control">
                    <small class="text-danger">@error('national_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">آدرس</label>
                    <input type="text" id="address" name="address" value="{{ Request::old('address') }}"
                           dir="rtl" class="form-control">
                    <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="login-horizental cancel-wp pull-left">
                    <button class="btn btn-warning btn-sm" type="button"><a href="/dashboard/kachaee-team">انصراف</a>
                    </button>
                    <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                  </div>
                </div>
              </div>
            </form>
          
          @else
            <form action="/dashboard/kachaee-team/{{$teamEdit->id}}" method="post">
              @csrf
              @method('PUT')
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام</label>
                    <input type="text" value="{{ $teamEdit->name }}" id="name" name="name"
                           class="form-control">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام پدر</label>
                    <input type="text" id="father_name" value="{{ $teamEdit->father_name}}"
                           name="father_name" class="form-control">
                    <small class="text-danger">@error('father_name') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام ضمانت کننده</label>
                    <input type="text" id="g_name" name="g_name" value="{{ $teamEdit->g_name }}"
                           class="form-control">
                    <small class="text-danger">@error('g_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نام پدر کلان</label>
                    <input type="text" id="grand_father_name" name="grand_father_name"
                           value="{{ $teamEdit->grand_father_name }}" class="form-control">
                    <small class="text-danger">@error('grand_father_name') {{ __('message.'.$message) }}
                      @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">شماره تماس</label>
                    <input type="text" id="contact_no" name="contact_no"
                           value="{{ $teamEdit->contact_no }}" dir="ltr" class="form-control">
                    <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">نمبر تذکره</label>
                    <input type="text" id="national_id" name="national_id"
                           value="{{ $teamEdit->national_id }}" class="form-control">
                    <small class="text-danger">@error('national_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="form-group fill">
                    <label class="">آدرس</label>
                    <input type="text" id="address" name="address" value="{{$teamEdit->address}}"
                           dir="rtl" class="form-control">
                    <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="login-horizental cancel-wp pull-left">
                    <button class="btn btn-warning btn-sm" type="button"><a href="/dashboard/kachaee-team">انصراف</a>
                    </button>
                    <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                  </div>
                </div>
              </div>
            </form>
          @endif
        </div>
      </div>
    </div>
  </div>
  
  <div class="row" id="kachaee">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4>کارمندان کچایی </h4>
          <div class="row hideOnPrint">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
              <form action="/dashboard/kachaee-team/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
  
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('kachaee')"><i
                          class="fa fa-print"></i> چاپ
                </div>
  
              </div>
              <a href="/dashboard/kachaee-accounts" style="float: left" class="btn btn-sm btn-info hideOnPrint">کارمندان
                حسابدار</a>
            
            </div>
          
          </div>
        </div>
        <div class="card-body">
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              
              <table class="table table-sm text-center" id="kachaee_team">
                <thead>
                <tr>
                  <th>نام</th>
                  <th>نام پدر</th>
                  <th>نام پدرکلان</th>
                  <th>نمبر تذکره</th>
                  <th>آدرس</th>
                  <th>شماره تماس</th>
                  <th>شخص تضمین کننده</th>
                  <th>باقیات(دالر)</th>
                  <th>باقیات(افغانی)</th>
                  <th class="hideOnPrint">ویرایش</th>
                  <th class="hideOnPrint">حساب</th>
                </tr>
                </thead>
                <tbody>
                @if(!isset($accounts))
                  @foreach($team as $t)
                    
                    <tr>
                      <td>{{ $t->name}}</td>
                      <td>{{ $t->father_name}}</td>
                      <td>{{ $t->grand_father_name}}</td>
                      <td>{{ $t->national_id}}</td>
                      <td>{{ $t->address }}</td>
                      <td style="direction: ltr">{{ $t->contact_no }}</td>
                      <td>{{ $t->g_name }}</td>
  
                      @php($total_af = 0)
                      @php($total_usd = 0)
                        <?php

                        $total_af = \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount_af');
                        $total_usd = \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount');

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
                        <a href="/dashboard/kachaee-team/{{$t->id}}/edit" class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                      </td>
                      
                      <td class="hideOnPrint">
                        <a href="/dashboard/kachaee-payments/{{$t->id}}"
                           class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                      </td>
                    
                    </tr>
                  @endforeach
                @else
                  @foreach($team as $t)
  
                    @php($total_af = 0)
                    @php($total_usd = 0)
                    <?php

                    $total_af = \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount_af');
                    $total_usd = \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('kachaee_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount');

                    ?>


                    @if($t->payment->count() > 0 && $total_af !=  0 || $total_usd != 0)
                      <tr>
                        <td>{{ $t->name}}</td>
                        <td>{{ $t->father_name}}</td>
                        <td>{{ $t->grand_father_name}}</td>
                        <td>{{ $t->national_id}}</td>
                        <td>{{ $t->address }}</td>
                        <td style="direction: ltr">{{ $t->contact_no }}</td>
                        <td>{{ $t->g_name }}</td>
  
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
                          <a href="/dashboard/kachaee-team/{{$t->id}}/edit"
                             class="btn btn-xs btn-primary hideOnPrint">ویرایش</a>
                        </td>
                        
                        <td class="hideOnPrint">
                          <a href="/dashboard/kachaee-payments/{{$t->id}}" class="btn btn-xs btn-primary hideOnPrint">حساب</a>
                        </td>
                      
                      </tr>
                    @endif
                  @endforeach
                @endif
                
                @if(!isset($search))
                  <tr style="background: gainsboro" class="text-center">
                    
                    <td></td>
                    <td></td>
                    <td class="hideOnPrint"></td>
                    <td></td>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- tables -->

@endsection

@section('footer-plugins')
  
  
  
  <script>
      $(document).ready(function () {
          $("#kachaee_team").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: 9,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#kachaee_team').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });

    
  
  </script>
@endsection