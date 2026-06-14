@extends('dsh.master')
@section('title' , 'تیم شست')
@section('content')
  <!-- navbar -->
  <!-- modal view for form -->
  <div class="modal fade" id="washingTeamModal" tabindex="-1" role="dialog" aria-labelledby="washingTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; padding: 18px 24px;">
          <h5 class="modal-title" id="washingTeamModalLabel" style="font-weight: bold; margin: 0;">
            @if(!$team)
              <i class="fa fa-user-plus"></i> ایجاد عضو جدید
            @else
              <i class="fa fa-user-edit"></i> ویرایش عضو شست
            @endif
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8; outline: none;">
            <span aria-hidden="true" style="font-size: 24px;">&times;</span>
          </button>
        </div>
        @if(!$team)
          <form action="/dashboard/washing-team" method="post">
            @csrf
            <div class="modal-body" style="padding: 24px;">
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">نام</label>
                    <input type="text" value="{{ Request::old('name') }}" id="name" name="name" class="form-control" placeholder="نام را وارد کنید" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">تخلص</label>
                    <input type="text" id="last_name" value="{{ Request::old('last_name') }}" name="last_name" class="form-control" placeholder="تخلص را وارد کنید" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('last_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">شماره تماس</label>
                    <input type="text" id="contact_no" name="contact_no" value="{{ Request::old('contact_no') }}" dir="ltr" class="form-control" placeholder="شماره تماس را وارد کنید" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">آدرس</label>
                    <input type="text" id="address" name="address" value="{{ Request::old('address') }}" dir="rtl" class="form-control" placeholder="آدرس را وارد کنید" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px;">
              <button class="btn btn-warning btn-sm" type="button" data-dismiss="modal" style="border-radius: 6px; padding: 6px 16px;">انصراف</button>
              <button class="btn btn-primary btn-sm" type="submit" style="border-radius: 6px; padding: 6px 16px;"><span class="fa fa-save"></span> ذخیره</button>
            </div>
          </form>
        @else
          <form action="/dashboard/washing-team/{{$team->id}}" method="post">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding: 24px;">
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">نام</label>
                    <input type="text" value="{{ $team->name}}" id="name" name="name" class="form-control" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">تخلص</label>
                    <input type="text" id="last_name" value="{{ $team->last_name}}" name="last_name" class="form-control" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('last_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">شماره تماس</label>
                    <input type="text" id="contact_no" name="contact_no" value="{{ $team->contact_no}}" dir="ltr" class="form-control" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">آدرس</label>
                    <input type="text" id="address" name="address" value="{{$team->address}}" dir="rtl" class="form-control" required style="border-radius: 8px; padding: 10px 14px;">
                    <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px;">
              <button class="btn btn-warning btn-sm" type="button" style="border-radius: 6px; padding: 6px 16px;"><a href="/dashboard/washing-team" style="color: inherit; text-decoration: none;">انصراف</a></button>
              <button class="btn btn-primary btn-sm" type="submit" style="border-radius: 6px; padding: 6px 16px;"><span class="fa fa-save"></span> ذخیره</button>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
  <div class="row" id="washing-team">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4>لیست کارمندان شست</h4>
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
          <div class="row hideOnPrint">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              <form action="/dashboard/washing-team/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('washing-team')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
              <a href="/dashboard/washing-accounts" style="float: left; margin-left: 5px;" class="btn btn-sm btn-info hideOnPrint">کارمندان حسابدار</a>
              <button type="button" class="btn btn-sm btn-success hideOnPrint" data-toggle="modal" data-target="#washingTeamModal" style="float: left;">
                <i class="fa fa-plus"></i> ایجاد عضو جدید
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-sm table-hover" id="washing_team">
            <thead>
            <tr>
              <th>نام</th>
              <th>تخلص</th>
              <th>شماره تماس</th>
              <th>آدرس</th>
              <th>باقیات (USD - معادل)</th>
              <th>باقیات بر اساس اسعار</th>
              <th class="hideOnPrint">ویرایش</th>
              <th class="hideOnPrint">حساب</th>
              <th class="hideOnPrint">صورت حساب</th>
            </tr>
            </thead>
            <tbody>
            @if(!isset($accounts))
              @foreach($teams as $t)
                
                <tr>
                  <td>{{ $t->name}}</td>
                  <td>{{ $t->last_name}}</td>
                  <td style="direction: ltr">{{ $t->contact_no }}</td>
                  <td>{{ $t->address }}</td>
                  
                  <!-- Normalized USD Balance -->
                  @php($norm_bal = $t->normalized_balance)
                  @if($norm_bal > 0)
                    <td style="direction: ltr;color: green; font-weight: bold;">{{ number_format($norm_bal, 2) }} $</td>
                  @elseif($norm_bal < 0)
                    <td style="direction: ltr;color: red; font-weight: bold;">{{ number_format($norm_bal, 2) }} $</td>
                  @else
                    <td style="direction: ltr;">0.00 $</td>
                  @endif

                  <!-- Selected Currencies Breakdown -->
                  @php($usd_bal = $t->usd_balance)
                  @php($af_bal = $t->af_balance)
                  <td style="font-size: 0.9rem; direction: ltr;">
                    @if($usd_bal != 0)
                      <span style="color: {{ $usd_bal > 0 ? 'green' : 'red' }};">{{ number_format($usd_bal, 2) }} USD</span>
                    @endif
                    @if($af_bal != 0)
                      @if($usd_bal != 0) <br> @endif
                      <span style="color: {{ $af_bal > 0 ? 'green' : 'red' }};">{{ number_format($af_bal, 2) }} AFN</span>
                    @endif
                    @if($usd_bal == 0 && $af_bal == 0)
                      <span class="text-muted">تصفیه</span>
                    @endif
                  </td>
                  
                  <td class="hideOnPrint">
                    <a href="/dashboard/washing-team/{{$t->id}}/edit"
                       class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                  </td>
                  
                  <td class="hideOnPrint">
                    <a href="/dashboard/washing-payments/{{$t->id}}" class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                  </td>
                  <td class="hideOnPrint">
                    <a href="{{ route('accounting.statements.show', ['entity' => 'washing-team', 'id' => $t->id]) }}" class="btn btn-sm btn-info hideOnPrint">صورت حساب</a>
                  </td>
                
                </tr>
              @endforeach
            @else
              @foreach($teams as $t)
                
                @php($norm_bal = $t->normalized_balance)
                @php($usd_bal = $t->usd_balance)
                @php($af_bal = $t->af_balance)
                
                @if($norm_bal != 0 || $usd_bal != 0 || $af_bal != 0)
                  <tr>
                    <td>{{ $t->name}}</td>
                    <td>{{ $t->last_name}}</td>
                    <td style="direction: ltr">{{ $t->contact_no }}</td>
                    <td>{{ $t->address }}</td>
  
                    <!-- Normalized USD Balance -->
                    @if($norm_bal > 0)
                      <td style="direction: ltr;color: green; font-weight: bold;">{{ number_format($norm_bal, 2) }} $</td>
                    @elseif($norm_bal < 0)
                      <td style="direction: ltr;color: red; font-weight: bold;">{{ number_format($norm_bal, 2) }} $</td>
                    @else
                      <td style="direction: ltr;">0.00 $</td>
                    @endif

                    <!-- Selected Currencies Breakdown -->
                    <td style="font-size: 0.9rem; direction: ltr;">
                      @if($usd_bal != 0)
                        <span style="color: {{ $usd_bal > 0 ? 'green' : 'red' }};">{{ number_format($usd_bal, 2) }} USD</span>
                      @endif
                      @if($af_bal != 0)
                        @if($usd_bal != 0) <br> @endif
                        <span style="color: {{ $af_bal > 0 ? 'green' : 'red' }};">{{ number_format($af_bal, 2) }} AFN</span>
                      @endif
                      @if($usd_bal == 0 && $af_bal == 0)
                        <span class="text-muted">تصفیه</span>
                      @endif
                    </td>
  
                    <td class="hideOnPrint">
                      <a href="/dashboard/washing-team/{{$t->id}}/edit" class="btn btn-xs btn-primary hideOnPrint">ویرایش</a>
                    </td>
                    
                    <td class="hideOnPrint">
                      <a href="/dashboard/washing-payments/{{$t->id}}"
                         class="btn btn-xs btn-primary hideOnPrint">حساب</a>
                    </td>
                    <td class="hideOnPrint">
                      <a href="{{ route('accounting.statements.show', ['entity' => 'washing-team', 'id' => $t->id]) }}"
                         class="btn btn-xs btn-info hideOnPrint">صورت حساب</a>
                    </td>
                  
                  </tr>
                @endif
              @endforeach
            @endif
            
            @if(!isset($search))
              @php($total_base_rec = \App\WashingPayment::where('type', 'رسید')->sum('base_amount'))
              @php($total_base_sent = \App\WashingPayment::where('type', 'گرفت')->sum('base_amount'))
              @php($total_normalized_sum = $total_base_rec - $total_base_sent)
              <tr style="background: gainsboro">
                
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                <!-- Total Normalized USD Balance -->
                @if($total_normalized_sum > 0)
                  <td style="font-size: 11px; direction: ltr; color: green; font-weight: bold;">
                    {{ number_format($total_normalized_sum, 2) }} $ (معادل)
                  </td>
                @elseif($total_normalized_sum < 0)
                  <td style="font-size: 11px; direction: ltr; color: red; font-weight: bold;">
                    {{ number_format($total_normalized_sum, 2) }} $ (معادل)
                  </td>
                @else
                  <td style="font-size: 11px; direction: ltr; font-weight: bold;">
                    0.00 $
                  </td>
                @endif
                
                <!-- Total Selected Currencies Breakdown -->
                <td style="font-size: 11px; direction: ltr; font-weight: bold;">
                  <span style="color: {{ ($credit_us - $debit_us) >= 0 ? 'green' : 'red' }};">
                    {{ number_format($credit_us - $debit_us, 2) }} USD
                  </span>
                  <br>
                  <span style="color: {{ ($credit_af - $debit_af) >= 0 ? 'green' : 'red' }};">
                    {{ number_format($credit_af - $debit_af, 2) }} AFN
                  </span>
                </td>
                
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
  
  <!-- tables -->

@endsection
@section('footer-plugins')
  
  
  
  <script>
      $(document).ready(function () {
          $("#washing_team").tableExport({
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
          var $buttons = $('#washing_team').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

          @if($team || $errors->any())
              $('#washingTeamModal').modal('show');
          @endif
      });
  
  
  </script>
@endsection