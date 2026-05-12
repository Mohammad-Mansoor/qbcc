@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          @if(!$accountEdit)
            <h4>ایجاد حساب متفرقه جدید</h4>
          @else
            <h4>ویرایش حساب متفرقه</h4>
          @endif
        </div>
        <div class="card-body">
          @if(!$accountEdit)
            <form method="post" id="" action="/dashboard/different-account">
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
                  <a href="/dashboard/different-account" class="btn btn-sm btn-default">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            
            </form>
          @else
            <form method="post" id="" action="/dashboard/different-account/{{$accountEdit->id}}">
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
                  <a href="/dashboard/different-account" class="btn btn-sm btn-default">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            </form>
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
          <h5>حسابات متفرقه</h5>
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
              <form action="/dashboard/different-account/search" method="post">
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
                @if(auth()->user()->role == 'SP')
                  <th>دفتر</th>
                @endif
                <th>باقیات</th>
                <th>طلبات</th>
                <th class="hideOnPrint">ویرایش</th>
                @if(auth()->user()->role == 'SP')
                  <th class="hideOnPrint">حذف</th>
                @endif
                <th class="hideOnPrint">حسابات</th>
              
              </tr>
              </thead>
              <tbody>
              @if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO')
                
                @foreach($center_accounts as $account)
                  <tr class="ur{{ $account->id }}">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>
                    
                    
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining <= 0)
                          <div style="direction: ltr;color: red;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining > 0)
                          <div style="direction: ltr;color: green;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td class="hideOnPrint"><a href="/dashboard/different-account/{{$account->id}}/edit"
                                               class="btn-sm btn-info">&nbsp; ویرایش</a></td>
                    <td class="hideOnPrint"><a href="/dashboard/different-account/{{$account->id}}"
                                               class="btn-sm btn-warning">
                        حسابات</a></td>
                  
                  </tr>
                @endforeach
                
                
              @elseif(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                
                @foreach($froshat_accounts as $account)
                  <tr class="ur{{ $account->id }}">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>
                    
                    
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining <= 0)
                          <div style="direction: ltr;color: red;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining > 0)
                          <div style="direction: ltr;color: green;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td class="hideOnPrint">
                      
                      <a href="/dashboard/different-account/{{$account->id}}/edit"
                         class="btn-sm btn-info">&nbsp; ویرایش</a>
                    
                    
                    </td>
                    
                    <td class="hideOnPrint"><a href="/dashboard/different-account/{{$account->id}}"
                                               class="btn-sm btn-warning">&nbsp;
                        حسابات</a></td>
                  
                  </tr>
                @endforeach
                
                
              @elseif(auth()->user()->role == 'MO')
                
                @foreach($mo_accounts as $account)
                  <tr class="ur{{ $account->id }}">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>
                    
                    
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining <= 0)
                          <div style="direction: ltr;color: red;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining > 0)
                          <div style="direction: ltr;color: green;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td class="hideOnPrint"><a href="/dashboard/different-account/{{$account->id}}/edit"
                                               class="btn-sm btn-info">&nbsp; ویرایش</a></td>
                    <td class="hideOnPrint"><a href="/dashboard/different-account/{{$account->id}}"
                                               class="btn-sm btn-warning">
                        حسابات</a></td>
                  
                  </tr>
                @endforeach
                
                
              @elseif(auth()->user()->role == 'SP')
                
                @foreach($sp_accounts as $account)
                  <tr class="ur{{ $account->id }} ">
                    <td>{{$account->id}}</td>
                    <td>{{$account->name}}</td>
                    <td>{{$account->phone}}</td>
                    <td>{{$account->address}}</td>
                    @if(auth()->user()->role == 'SP')
                      @if($account->user_role== 'CO' || $account->user_role== 'CCO')
                        <td> دفتر مرکزی</td>
                      @elseif($account->user_role== 'SO' || $account->user_role== 'SCO')
                        <td>دفتر فروشات</td>
                      @elseif($account->user_role== 'MO')
                        <td>حساب متفرقه</td>
                      @else
                        <td>سوپرادمین</td>
                      @endif
                    @endif
                    
                    
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining <= 0)
                          <div style="direction: ltr;color: red;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining > 0)
                          <div style="direction: ltr;color: green;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    
                    <td class="hideOnPrint">
                      
                      <a href="/dashboard/different-account/{{$account->id}}/edit"
                         class="btn btn-sm btn-info">&nbsp; ویرایش</a>
                    
                    </td>
                    
                    
                    <td class="hideOnPrint">
                      
                      <button onclick="deleteAccount({{$account->id}})" class="btn btn-danger btn-sm"><i
                                class="fa fa-tick"></i>حذف
                      </button>
                    
                    </td>
                    
                    
                    <td class="hideOnPrint"><a href="/dashboard/different-account/{{$account->id}}"
                                               class="btn btn-sm btn-warning">
                        حسابات</a></td>
                  
                  </tr>
                
                
                @endforeach
                
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
                          url: '/dashboard/different-account/' + id,
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

