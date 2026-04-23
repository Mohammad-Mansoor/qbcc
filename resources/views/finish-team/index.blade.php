@extends('dsh.master')
@section('title' , 'تیم تیاری')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$fteamEdit)
            <h5 class="text-right">ایجاد تیم جدید</h5>
          @else
            <h5 class="text-right">ویرایش تیم </h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$fteamEdit)
              <form action="/dashboard/finish-team" method="post">
                @csrf
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label> نام تیم</label>
                      <input type="text" style="direction: rtl" name="name" id="name" class="form-control">
                      @error('name') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-warning" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form action="/dashboard/finish-team/{{$fteamEdit->id}}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label> نام تیم</label>
                      <input type="text" style="direction: rtl" name="name" value="{{$fteamEdit->name}}" id="name" class="form-control">
                      @error('name') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <a href="/dashboard/finish-team" class="btn btn-sm btn-default" type="reset">انصراف</a>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                    </div>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>
      <div class="card" id="teams">
        <div class="card-header">
          <h5>تیم تیاری</h5>
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                <form action="/dashboard/finish-team/search" method="post">
                  @csrf
                  <input type="text" name="search" required
                         placeholder="جستجو" class="form-control">
                </form>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
                <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                  <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('teams')"><i
                            class="fa fa-print"></i> چاپ
                  </div>
  
                </div>
  
                <a href="/dashboard/finishing-accounts" style="float:left;" class="btn btn-sm btn-info hideOnPrint">کارمندان
                  حسابدار</a>
              </div>
            </div>
         
            <div class="alert alert-success" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center"> تیم حذف شد</p>
            </div>
            @if(session("status"))
              <div class="alert alert-success status" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                <p class="text-center">{{session('status')}}</p>
              </div>
            @endif
            @if(session("error"))
              <div class="alert alert-success status" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                <p class="text-center">{{session('error')}}</p>
              </div>
            @endif
           
              
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-xs table-hover" id="finishing_teams">
              <thead>
              <tr >
                <th>آی دی</th>
                <th> تیم</th>
                <th>باقیات(دالر)</th>
                <th>باقیات(افغانی)</th>
                <th class=" hideOnPrint">ویرایش</th>
                <th class=" hideOnPrint">حساب</th>
      
              </tr>
              </thead>
              <tbody>
              @if(!isset($accounts))
                @foreach($teams as $t)
          
                  <tr>
                    <td>{{ $t->id}}</td>
                    <td>{{ $t->name}}</td>
  
                    @php($total_af = 0)
                    @php($total_usd = 0)
                      <?php

                      $total_af = \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount_af');
                      $total_usd = \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount');

                      ?>
  
                    {{--for dollars balance--}}
                    @if($total_usd > 0)
                      <td style="direction: ltr;color: green;">{{ round($total_usd , 3)}}</td>
                    @elseif($total_usd < 0)
                      <td style="direction: ltr;color: red;">{{round($total_usd , 3)}}</td>
                    @else
                      <td>{{ round($total_usd  , 3)}}</td>
                    @endif
                    {{--end dollars balance--}}
  
                    {{--afghani balance--}}
                    @if($total_af > 0)
                      <td style="direction: ltr;color: green;">{{round($total_af  , 3)}}</td>
                    @elseif($total_af < 0)
                      <td style="direction: ltr;color: red;">{{round($total_af ,  3 ) }}</td>
                    @else
                      <td>{{ $total_af }}</td>
                    @endif
            
                    <td class="hideOnPrint">
                      <a href="/dashboard/finish-team/{{$t->id}}/edit"
                         class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                    </td>
            
                    <td class="hideOnPrint">
                      <a href="/dashboard/finishing-payments/{{$t->id}}"
                         class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                    </td>
          
                  </tr>
                @endforeach
              @else
                @foreach($teams as $t)
  
                  @php($total_af = 0)
                  @php($total_usd = 0)
                  <?php

                  $total_af = \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount_af');
                  $total_usd = \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('finishing_team_payments')->where('team_id', $t->id)->where('type', 'گرفت')->sum('amount');

                  ?>



                  @if($t->payment->count() > 0 && $total_af !=  0 || $total_usd != 0)
                    <tr>
                      <td>{{ $t->id}}</td>
                      <td>{{ $t->name}}</td>
                      
  
                      {{--for dollars balance--}}
                      @if($total_usd > 0)
                        <td style="direction: ltr;color: green;">{{ round($total_usd,3)}}</td>
                      @elseif($total_usd < 0)
                        <td style="direction: ltr;color: red;">{{round($total_usd,3)}}</td>
                      @else
                        <td>{{ $total_usd }}</td>
                      @endif
                      {{--end dollars balance--}}
  
                      {{--afghani balance--}}
                      @if($total_af > 0)
                        <td style="direction: ltr;color: green;">{{round($total_af  , 3)}}</td>
                      @elseif($total_af < 0)
                        <td style="direction: ltr;color: red;">{{round($total_af , 3) }}</td>
                      @else
                        <td>{{ $total_af }}</td>
                      @endif
              
                      <td class="hideOnPrint">
                        <a href="/dashboard/finish-team/{{$t->id}}/edit"
                           class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                      </td>
              
                      <td class="hideOnPrint">
                        <a href="/dashboard/finishing-payments/{{$t->id}}"
                           class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                      </td>
            
            
                    </tr>
                  @endif
                @endforeach
              @endif
      
              @if(!isset($search))
                <tr style="background: gainsboro">
                  
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
@endsection

@section('scripts')
  <script>
      $(document).ready(function () {
          $("#finishing_teams").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: 4,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#finishing_teams').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
  
  
  
  </script>
  
  <script>
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);

      function RemoveCategory(id) {
          swal({
              style: "text-center",
              text: " تیم حذف شود؟",
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
                          method: 'DELETE',
                          data: {'_token': '{{ csrf_token() }}'},
                          url: '/dashboard/finish-team/' + id,
                          success: function (data) {
                              $('.ur' + id).hide();
                              $('.alert').show();
                              window.setTimeout(function () {
                                  $(".alert").fadeTo(500, 0).slideUp(500, function () {

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
