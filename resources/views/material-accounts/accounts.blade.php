@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          @if(!$accountEdit)
            <h4>ایجاد حساب مواد خام جدید</h4>
          @else
            <h4>ویرایش حساب مواد خام</h4>
          @endif
        </div>
        <div class="card-body">
          @if(!$accountEdit)
            <form method="post" id="" action="/dashboard/material-accounts">
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
                    <label>تاریخ</label>
                    <input type="date" class="form-control" required name="date">
                    @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                
                </div>
  
                <div class="col-md-4" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label>سال حساب</label>
                    <select name="account_year" required id="account_lists" class="form-control">
                      <option value="1395">1395</option>
                      <option value="1396">1396</option>
                      <option value="1397">1397</option>
                      <option value="1398">1398</option>
                      <option value="1399">1399</option>
                      <option value="1400">1400</option>
                      <option value="1401">1401</option>
                      <option value="1402">1402</option>
                      <option value="1403">1403</option>
                      <option value="1404">1404</option>
                      <option value="1405">1405</option>
                      <option value="1406">1406</option>
                      <option value="1407">1407</option>
                      <option value="1408">1408</option>
                      <option value="1409">1409</option>
                      <option value="1410">1410</option>
                      <option value="1411">1411</option>
                      <option value="1412">1412</option>
      
                    </select>
      
                    @error('account_year') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
  
                </div>
              
              
              </div>
              <div class="row">
                <div class="form-group fill">
                  <a href="/dashboard/material-accounts" class="btn btn-sm btn-default">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            
            </form>
          @else
            <form method="post" id="" action="/dashboard/material-accounts/{{$accountEdit->id}}">
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
                    <label>تاریخ</label>
                    <input type="text" class="form-control" required name="date" value="{{$accountEdit->date}}">
                    @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                
                </div>
  
                <div class="col-md-4" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label>سال حساب</label>
                    <select name="account_year" required id="account_lists" class="form-control">
                      <option value="1395" {{($accountEdit->account_year == 1395 ? 'selected' : '')}}>1395</option>
                      <option value="1396" {{($accountEdit->account_year == 1396 ? 'selected' : '')}}>1396</option>
                      <option value="1397" {{($accountEdit->account_year == 1397 ? 'selected' : '')}}>1397</option>
                      <option value="1398" {{($accountEdit->account_year == 1398 ? 'selected' : '')}}>1398</option>
                      <option value="1399" {{($accountEdit->account_year == 1399 ? 'selected' : '')}}>1399</option>
                      <option value="1400" {{($accountEdit->account_year == 1400 ? 'selected' : '')}}>1400</option>
                      <option value="1401" {{($accountEdit->account_year == 1401 ? 'selected' : '')}}>1401</option>
                      <option value="1402" {{($accountEdit->account_year == 1402 ? 'selected' : '')}}>1402</option>
                      <option value="1403" {{($accountEdit->account_year == 1403 ? 'selected' : '')}}>1403</option>
                      <option value="1404" {{($accountEdit->account_year == 1404 ? 'selected' : '')}}>1404</option>
                      <option value="1405" {{($accountEdit->account_year == 1405 ? 'selected' : '')}}>1405</option>
                      <option value="1406" {{($accountEdit->account_year == 1406 ? 'selected' : '')}}>1406</option>
                      <option value="1407" {{($accountEdit->account_year == 1407 ? 'selected' : '')}}>1407</option>
                      <option value="1408" {{($accountEdit->account_year == 1408 ? 'selected' : '')}}>1408</option>
                      <option value="1409" {{($accountEdit->account_year == 1409 ? 'selected' : '')}}>1409</option>
                      <option value="1410" {{($accountEdit->account_year == 1410 ? 'selected' : '')}}>1410</option>
                      <option value="1411" {{($accountEdit->account_year == 1411 ? 'selected' : '')}}>1411</option>
                      <option value="1412" {{($accountEdit->account_year == 1402 ? 'selected' : '')}}>1412</option>
      
                    </select>
      
                    @error('account_year') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
  
                </div>
              
              
              </div>
              
              <div class="row">
                <div class="form-group fill">
                  <a href="/dashboard/material-accounts" class="btn btn-sm btn-default">انصراف</a>
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
          <h5>حسابات مواد خام</h5>
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
          <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  hideOnPrint">
              <form action="/dashboard/material-accounts/search" method="post">
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
                <th>تاریخ</th>
                <th>باقیات(کیلو گرام)</th>
                <th>بیلانس حسابی (USD)</th>
                <th>ارزش تخمینی (USD)</th>
                <th class="hideOnPrint">ویرایش</th>
                @if(auth()->user()->role == 'SP')
                  <th class="hideOnPrint">حذف</th>
                @endif
                <th class="hideOnPrint">حسابات</th>
              
              </tr>
              </thead>
              <tbody>
              @php($total_rasid = 0)
              @php($total_gerft = 0)
              
              @foreach($accounts as $acc)
                <tr>
                  <td>{{$acc->id}}</td>
                  <td>{{$acc->name}}</td>
                  <td>{{$acc->date}}</td>
                  
                  {{-- Physical Weight --}}
                  <td dir="ltr" class="{{ $acc->physical_weight > 0 ? 'text-success' : ($acc->physical_weight < 0 ? 'text-danger' : '') }}">
                      {{ number_format($acc->physical_weight, 2) }} kg
                  </td>

                  {{-- Ledger Balance --}}
                  <td dir="ltr" class="{{ $acc->ledger_balance > 0 ? 'text-success' : ($acc->ledger_balance < 0 ? 'text-danger' : '') }}">
                      {{ number_format($acc->ledger_balance, 2) }}
                  </td>

                  {{-- Valuation --}}
                  <td dir="ltr" class="text-primary font-weight-bold">
                      ${{ number_format($acc->valuation, 2) }}
                  </td>
  
                  <td class="hideOnPrint"><a href="/dashboard/material-accounts/{{$acc->id}}/edit"
                                             class="btn-sm btn-info">&nbsp; ویرایش</a></td>
                  @if(auth()->user()->role == 'SP')
                    <td class="hideOnPrint">
                      <button onclick="deleteAccount({{$acc->id}})" class="btn btn-danger btn-sm"><i
                                class="fa fa-trash"></i> حذف
                      </button>
                    </td>
                  @endif
                  
                  <td class="hideOnPrint"><a href="/dashboard/material-accounts/{{$acc->id}}"
                                             class="btn-sm btn-warning">
                      حسابات</a></td>
                </tr>
              @endforeach


              <tr style="background: #f8f9fa; font-weight: bold;">
                <td colspan="3" class="text-right">مجموع کل:</td>
                <td dir="ltr" class="text-info">{{ number_format($accounts->sum('physical_weight'), 2) }} kg</td>
                <td dir="ltr" class="text-info">{{ number_format($accounts->sum('ledger_balance'), 2) }}</td>
                <td dir="ltr" class="text-primary">${{ number_format($accounts->sum('valuation'), 2) }}</td>
                <td colspan="3"></td>
              </tr>
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
          $('#account_lists').select2();
          
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
                          url: '/dashboard/material-accounts/' + id,
                          success: function (res) {

                           
                              $('.deleteAlert').show();

                              window.setTimeout(function () {
                                  $(".deleteAlert").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);

                              window.location = '/dashboard/material-accounts/'
                          }

                      })
                  }
              });
      }
  </script>
@endsection
