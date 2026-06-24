@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          @canany(['create_different_account', 'edit_different_account'])
          @if(!$accountEdit)
            <h4>ایجاد حساب متفرقه جدید</h4>
          @else
            <h4>ویرایش حساب متفرقه جدید</h4>
          @endif
          @endcanany
        </div>
        <div class="card-body">
          @if(!$accountEdit)
            @can('create_different_account')
            <form method="post" id="" action="/dashboard/new-different-account">
              @csrf
              <div class="row">
                <div class="col-md-4">
                  
                  <div class="form-group fill">
                    <label>نام</label>
                    <input type="text" class="form-control" name="name">
                    @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group fill">
                    <label>مبایل</label>
                    <input type="text" class="form-control" required name="phone">
                    @error('phone') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                
                </div>
                <div class="col-md-4">
                  
                  <div class="form-group fill">
                    <label>ادرس</label>
                    <textarea type="text" class="form-control" required rows="1" name="address"></textarea>
                    @error('address') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  
                  </div>
                </div>
              
              
              </div>
              <div class="row">
                <div class="form-group fill">
                  <a href="/dashboard/new-different-account" class="btn btn-sm btn-default">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            
            </form>
            @endcan
          @else
            @can('edit_different_account')
            <form method="post" id="" action="/dashboard/new-different-account/{{$accountEdit->id}}">
              {{method_field('patch')}}
              @csrf
              <div class="row">
                <div class="col-md-4">
                  
                  <div class="form-group fill">
                    <label>نام</label>
                    <input type="text" class="form-control" value="{{$accountEdit->name}}" name="name">
                    @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group fill">
                    <label>مبایل</label>
                    <input type="text" class="form-control" value="{{$accountEdit->phone}}" required name="phone">
                    @error('phone') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                
                </div>
                <div class="col-md-4">
                  
                  <div class="form-group fill">
                    <label>ادرس</label>
                    <textarea type="text" class="form-control" required rows="1"
                              name="address">{{$accountEdit->address}}</textarea>
                    @error('address') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  
                  </div>
                </div>
              
              
              </div>
              
              <div class="row">
                <div class="form-group fill">
                  <a href="/dashboard/new-different-account" class="btn btn-sm btn-default">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            </form>
            @endcan
          @endif
        
        </div>
      </div>
    </div>
  
  </div>
  
  <div class="row" id="accounts">
    <!-- Extra small table start-->
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h5>حسابات متفرقه جدید</h5>
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
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  hideOnPrint">
              <form action="/dashboard/new-different-account/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('accounts')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
            </div>
          </div>
        
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs table-hover" id="account_list">
              <thead>
              <tr>
                <th>نمبر حساب</th>
                <th>نام</th>
                <th>مبایل</th>
                <th>آدرس</th>
                @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                  <th>دفتر</th>
                @endif
                <th>باقیات(افغانی)</th>
                <th>باقیات(دالر)</th>
                <th>باقیات(کلدار)</th>
                <th class="hideOnPrint">ویرایش</th>
                @if(auth()->user()->role == 'SP')
                  <th class="hideOnPrint">حذف</th>
                @endif
                <th class="hideOnPrint">حسابات</th>
                <th class="hideOnPrint">صورت حساب</th>
              
              </tr>
              </thead>
              <tbody>
              @if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO')
                @php($remaining_af = 0)
                @php($remaining_usd = 0)
                @php($remaining_cd = 0)
                @foreach($center_accounts as $account)
                  <tr class="ur{{ $account->id }}">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>

                      <?php

                      $total_afg_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','گرفت')->sum('amount');
                      $total_usd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','گرفت')->sum('amount');
                      $total_cd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','گرفت')->sum('amount');

                      ?>
                    <span style="display: none">{{$remaining_af += $total_afg_amount }}</span>
                    <span style="display: none">{{$remaining_usd += $total_usd_amount }}</span>
                    <span style="display: none">{{$remaining_cd += $total_cd_amount }}</span>
                    
                    <td style="direction: ltr; @if($total_afg_amount > 0) color:#00e3ae; @elseif($total_afg_amount < 0) color:red; @endif " >{{$total_afg_amount}}</td>
                    <td style="direction: ltr; @if($total_usd_amount > 0) color:#00e3ae; @elseif($total_usd_amount < 0) color:red; @endif">{{$total_usd_amount}}</td>
                    <td style="direction: ltr;@if($total_cd_amount > 0) color:#00e3ae; @elseif($total_cd_amount < 0) color:red; @endif">{{$total_cd_amount}}</td>
                    
                    <td class="hideOnPrint">
                      @can('edit_different_account')
                      <a href="/dashboard/new-different-account/{{$account->id}}/edit"
                                                class="btn-sm btn-info">&nbsp; ویرایش</a>
                      @endcan
                    </td>
                    <td class="hideOnPrint"><a href="/dashboard/new-different-account/{{$account->id}}"
                                               class="btn-sm btn-warning">
                        حسابات</a></td>
                    <td class="hideOnPrint"><a href="{{ route('accounting.statements.show', ['entity' => 'different-account', 'id' => $account->id]) }}"
                                               class="btn-sm btn-info">
                        صورت حساب</a></td>
                  
                  </tr>
                @endforeach
                
                @if(!isset($search))
                  <tr style="background: gainsboro">
                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="direction: ltr; @if($remaining_af > 0) color:#00e3ae; @elseif($remaining_af < 0) color:red; @endif">{{$remaining_af}}</td>
                    <td style="direction: ltr; @if($remaining_usd > 0) color:#00e3ae; @elseif($remaining_usd < 0) color:red; @endif">{{$remaining_usd}}</td>
                    <td style="direction: ltr; @if($remaining_cd > 0) color:#00e3ae; @elseif($remaining_cd < 0) color:red; @endif">{{$remaining_cd}}</td>
                    <td>مجموعه</td>
                  </tr>
                @endif
              @elseif(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                @php($remaining_af = 0)
                @php($remaining_usd = 0)
                @php($remaining_cd = 0)
                @foreach($froshat_accounts as $account)
                  <tr class="ur{{ $account->id }}">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>

                      <?php

                      $total_afg_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','گرفت')->sum('amount');
                      $total_usd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','گرفت')->sum('amount');
                      $total_cd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','گرفت')->sum('amount');

                      ?>
                    <span style="display: none">{{$remaining_af += $total_afg_amount }}</span>
                    <span style="display: none">{{$remaining_usd += $total_usd_amount }}</span>
                    <span style="display: none">{{$remaining_cd += $total_cd_amount }}</span>
                    
                    <td style="direction: ltr; @if($total_afg_amount > 0) color:#00e3ae; @elseif($total_afg_amount < 0) color:red; @endif " >{{$total_afg_amount}}</td>
                    <td style="direction: ltr; @if($total_usd_amount > 0) color:#00e3ae; @elseif($total_usd_amount < 0) color:red; @endif">{{$total_usd_amount}}</td>
                    <td style="direction: ltr;@if($total_cd_amount > 0) color:#00e3ae; @elseif($total_cd_amount < 0) color:red; @endif">{{$total_cd_amount}}</td>
                    
                    <td class="hideOnPrint">
                      
                      @can('edit_different_account')
                      <a href="/dashboard/new-different-account/{{$account->id}}/edit"
                         class="btn-sm btn-info">&nbsp; ویرایش</a>
                      @endcan
                    
                    </td>
                    
                    <td class="hideOnPrint"><a href="/dashboard/new-different-account/{{$account->id}}"
                                               class="btn-sm btn-warning">&nbsp;
                        حسابات</a></td>
                    <td class="hideOnPrint"><a href="{{ route('accounting.statements.show', ['entity' => 'different-account', 'id' => $account->id]) }}"
                                               class="btn-sm btn-info">&nbsp;
                        صورت حساب</a></td>
                  
                  </tr>
                @endforeach
                
                @if(!isset($search))
                  <tr style="background: gainsboro">
                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="direction: ltr; @if($remaining_af > 0) color:#00e3ae; @elseif($remaining_af < 0) color:red; @endif">{{$remaining_af}}</td>
                    <td style="direction: ltr; @if($remaining_usd > 0) color:#00e3ae; @elseif($remaining_usd < 0) color:red; @endif">{{$remaining_usd}}</td>
                    <td style="direction: ltr; @if($remaining_cd > 0) color:#00e3ae; @elseif($remaining_cd < 0) color:red; @endif">{{$remaining_cd}}</td>
                    <td>مجموعه</td>
                  </tr>
                @endif
              @elseif(auth()->user()->role == 'MO')
                @php($remaining_af = 0)
                @php($remaining_usd = 0)
                @php($remaining_cd = 0)
                
                @foreach($mo_accounts as $account)
                  <tr class="ur{{ $account->id }}">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>

                      <?php

                      $total_afg_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','گرفت')->sum('amount');
                      $total_usd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','گرفت')->sum('amount');
                      $total_cd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','گرفت')->sum('amount');

                      ?>
                    <span style="display: none">{{$remaining_af += $total_afg_amount }}</span>
                    <span style="display: none">{{$remaining_usd += $total_usd_amount }}</span>
                    <span style="display: none">{{$remaining_cd += $total_cd_amount }}</span>
                    
                    <td style="direction: ltr; @if($total_afg_amount > 0) color:#00e3ae; @elseif($total_afg_amount < 0) color:red; @endif " >{{$total_afg_amount}}</td>
                    <td style="direction: ltr; @if($total_usd_amount > 0) color:#00e3ae; @elseif($total_usd_amount < 0) color:red; @endif">{{$total_usd_amount}}</td>
                    <td style="direction: ltr;@if($total_cd_amount > 0) color:#00e3ae; @elseif($total_cd_amount < 0) color:red; @endif">{{$total_cd_amount}}</td>
                    <td class="hideOnPrint">
                      @can('edit_different_account')
                      <a href="/dashboard/new-different-account/{{$account->id}}/edit"
                                                class="btn-sm btn-info">&nbsp; ویرایش</a>
                      @endcan
                    </td>
                    <td class="hideOnPrint"><a href="/dashboard/new-different-account/{{$account->id}}"
                                               class="btn-sm btn-warning">
                        حسابات</a></td>
                    <td class="hideOnPrint"><a href="{{ route('accounting.statements.show', ['entity' => 'different-account', 'id' => $account->id]) }}"
                                               class="btn-sm btn-info">
                        صورت حساب</a></td>
                  
                  </tr>
                @endforeach
                
                @if(!isset($search))
                  <tr style="background: gainsboro">
                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="direction: ltr; @if($remaining_af > 0) color:#00e3ae; @elseif($remaining_af < 0) color:red; @endif">{{$remaining_af}}</td>
                    <td style="direction: ltr; @if($remaining_usd > 0) color:#00e3ae; @elseif($remaining_usd < 0) color:red; @endif">{{$remaining_usd}}</td>
                    <td style="direction: ltr; @if($remaining_cd > 0) color:#00e3ae; @elseif($remaining_cd < 0) color:red; @endif">{{$remaining_cd}}</td>
                    <td>مجموعه</td>
                  </tr>
                @endif
              @elseif(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                @php($remaining_af = 0)
                @php($remaining_usd = 0)
                @php($remaining_cd = 0)
                
                @foreach($sp_accounts as $account)
                  <tr class="ur{{ $account->id }} ">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>
                    @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                      @if($account->user_role== 'CO' || $account->user_role== 'CCO')
                        <td> دفتر مرکزی</td>
                      @elseif($account->user_role== 'SO' || $account->user_role== 'SCO')
                        <td>دفتر فروشات</td>
                      @elseif($account->user_role== 'MO')
                        <td>حساب متفرقه</td>
                        @elseif($account->user_role== 'FI')
                        <td>حساب مالی</td>
                      @else
                        <td>سوپرادمین</td>
                      @endif
                    @endif
                      <?php

                      $total_afg_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',1)->where('type','گرفت')->sum('amount');
                      $total_usd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',2)->where('type','گرفت')->sum('amount');
                      $total_cd_amount = \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('new_different_account_payments')->where('account_id',$account->id)->where('currency',3)->where('type','گرفت')->sum('amount');

                      ?>
                    <span style="display: none">{{$remaining_af += $total_afg_amount }}</span>
                    <span style="display: none">{{$remaining_usd += $total_usd_amount }}</span>
                    <span style="display: none">{{$remaining_cd += $total_cd_amount }}</span>
                    
                    <td style="direction: ltr; @if($total_afg_amount > 0) color:#00e3ae; @elseif($total_afg_amount < 0) color:red; @endif " >{{$total_afg_amount}}</td>
                    <td style="direction: ltr; @if($total_usd_amount > 0) color:#00e3ae; @elseif($total_usd_amount < 0) color:red; @endif">{{$total_usd_amount}}</td>
                    <td style="direction: ltr;@if($total_cd_amount > 0) color:#00e3ae; @elseif($total_cd_amount < 0) color:red; @endif">{{$total_cd_amount}}</td>
                    
                    
                    
                    <td class="hideOnPrint">
                      
                      @can('edit_different_account')
                      <a href="/dashboard/new-different-account/{{$account->id}}/edit"
                         class="btn btn-sm btn-info">&nbsp; ویرایش</a>
                      @endcan
                    
                    </td>
                    
                    @can('delete_different_account')
                    @if(auth()->user()->role == 'SP')
                    <td class="hideOnPrint">
                      
                      <button onclick="deleteAccount({{$account->id}})" class="btn btn-danger btn-sm"><i
                                class="fa fa-tick"></i>حذف
                      </button>
                    
                    </td>
                    @endif
                    @endcan
                    
                    
                    <td class="hideOnPrint"><a href="/dashboard/new-different-account/{{$account->id}}"
                                               class="btn btn-sm btn-warning">
                        حسابات</a></td>
                    <td class="hideOnPrint"><a href="{{ route('accounting.statements.show', ['entity' => 'different-account', 'id' => $account->id]) }}"
                                               class="btn btn-sm btn-info">
                        صورت حساب</a></td>
                  
                  </tr>
                
                
                @endforeach
                @if(!isset($search))
                  <tr style="background: gainsboro">
                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="direction: ltr; @if($remaining_af > 0) color:#00e3ae; @elseif($remaining_af < 0) color:red; @endif">{{$remaining_af}}</td>
                    <td style="direction: ltr; @if($remaining_usd > 0) color:#00e3ae; @elseif($remaining_usd < 0) color:red; @endif">{{$remaining_usd}}</td>
                    <td style="direction: ltr; @if($remaining_cd > 0) color:#00e3ae; @elseif($remaining_cd < 0) color:red; @endif">{{$remaining_cd}}</td>
                    <td>مجموعه</td>
                  </tr>
                @endif
              @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- Extra small table start-->
  </div>
@endsection

@section('scripts')
  <script>
      $(document).ready(function () {
          $("#account_list").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: 7,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#account_list').find('caption').children().detach();
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


     function deleteAccount(id) {

          swal({
              text: "آیا مطمعین هستید ؟",
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
                          url: '/dashboard/new-different-account/' + id,
                          success: function (res) {

                              $('.ur' + id).hide();
                              $('.deleteAlert').show();

                              window.setTimeout(function () {
                                  $(".deleteAlert").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);
                          }

                      })
                  }
              });
      }
  </script>
@endsection

