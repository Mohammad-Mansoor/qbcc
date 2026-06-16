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
    <div class="col-lg-12">
      <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
        
        <!-- Modern Header Section -->
        <div class="card-header bg-white border-bottom p-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h4 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px;">
                        <i class="fa fa-users text-primary mr-2"></i> لیست کارمندان شست
                    </h4>
                    @if(session("status"))
                        <div class="text-success small font-weight-bold mt-2"><i class="fa fa-check-circle mr-1"></i> {{ session('status') }}</div>
                    @endif
                    @if(session("error"))
                        <div class="text-danger small font-weight-bold mt-2"><i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}</div>
                    @endif
                </div>
                
                <div class="col-md-8 text-left d-flex justify-content-end align-items-center hideOnPrint flex-wrap">
                    <!-- Search Bar -->
                    <form action="/dashboard/washing-team/search" method="post" class="mr-3 mb-0" style="width: {{ isset($search) ? '300px' : '250px' }};">
                        @csrf
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <input type="text" name="search" required placeholder="جستجوی کارمند..." class="form-control border-0 bg-light" value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                @if(isset($search) && $search != '')
                                    <a href="/dashboard/washing-team" class="btn btn-danger border-0 px-3" title="پاک کردن جستجو"><i class="fa fa-times"></i></a>
                                @endif
                                <button type="submit" class="btn btn-primary border-0 px-3"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <!-- Export Dropdown Area (Used by TableExport JS) -->
                    <div class="btn-group mr-2 shadow-sm rounded-lg" id="exportButton">
                        <button class="btn btn-light border-0 font-weight-bold text-dark" onclick="printPage('washing-team')">
                            <i class="fa fa-print text-primary mr-1"></i> چاپ
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <a href="/dashboard/washing-accounts" class="btn btn-info shadow-sm mr-2 rounded-lg font-weight-bold px-3 border-0" style="background-color: #06b6d4;">
                        <i class="fa fa-calculator mr-1"></i> کارمندان حسابدار
                    </a>
                    
                    <button type="button" class="btn btn-success shadow-sm rounded-lg font-weight-bold px-3 border-0" data-toggle="modal" data-target="#washingTeamModal" style="background-color: #10b981;">
                        <i class="fa fa-plus-circle mr-1"></i> ایجاد عضو جدید
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0" id="washing_team" style="font-size: 0.95rem;">
              <thead class="bg-light text-dark font-weight-bold">
                <tr>
                  <th class="border-0 py-3">نام</th>
                  <th class="border-0 py-3">تخلص</th>
                  <th class="border-0 py-3">شماره تماس</th>
                  <th class="border-0 py-3">آدرس</th>
                  <th class="border-0 py-3">باقیات (USD - معادل)</th>
                  <th class="border-0 py-3">باقیات بر اساس اسعار</th>
                  <th class="border-0 py-3 hideOnPrint">عملیات سیستم</th>
                </tr>
              </thead>
              <tbody>
                @php $items = isset($search) && !isset($accounts) ? $teams : (isset($accounts) ? $teams : $teams); @endphp
                @foreach($items as $t)
                  @php
                      $norm_bal = $t->normalized_balance;
                      $usd_bal = $t->usd_balance;
                      $af_bal = $t->af_balance;
                      $show_row = !isset($accounts) || ($norm_bal != 0 || $usd_bal != 0 || $af_bal != 0);
                  @endphp
                  
                  @if($show_row)
                  <tr class="border-bottom">
                    <td class="font-weight-bold text-dark align-middle">{{ $t->name }}</td>
                    <td class="align-middle">{{ $t->last_name }}</td>
                    <td class="align-middle" style="direction: ltr; font-family: monospace;">{{ $t->contact_no }}</td>
                    <td class="align-middle text-muted">{{ $t->address }}</td>
                    
                    <!-- Normalized USD Balance -->
                    <td class="align-middle" style="direction: ltr;">
                      @if($norm_bal > 0)
                        <span class="badge badge-light-success px-3 py-2 font-weight-bold rounded-pill text-success shadow-sm"><i class="fa fa-arrow-up mr-1"></i> ${{ number_format($norm_bal, 2) }}</span>
                      @elseif($norm_bal < 0)
                        <span class="badge badge-light-danger px-3 py-2 font-weight-bold rounded-pill text-danger shadow-sm"><i class="fa fa-arrow-down mr-1"></i> ${{ number_format(abs($norm_bal), 2) }}</span>
                      @else
                        <span class="text-muted font-weight-bold">$0.00</span>
                      @endif
                    </td>

                    <!-- Selected Currencies Breakdown -->
                    <td class="align-middle" style="font-size: 0.9rem; direction: ltr;">
                      @if($usd_bal != 0)
                        <div class="font-weight-bold" style="color: {{ $usd_bal > 0 ? '#10b981' : '#ef4444' }};">{{ number_format($usd_bal, 2) }} USD</div>
                      @endif
                      @if($af_bal != 0)
                        <div class="font-weight-bold" style="color: {{ $af_bal > 0 ? '#10b981' : '#ef4444' }};">{{ number_format($af_bal, 2) }} AFN</div>
                      @endif
                      @if($usd_bal == 0 && $af_bal == 0)
                        <span class="badge badge-secondary px-2 py-1 rounded-pill">تصفیه (Cleared)</span>
                      @endif
                    </td>
                    
                    <!-- Action Buttons -->
                    <td class="align-middle hideOnPrint">
                      <div class="btn-group shadow-sm" role="group">
                        <a href="/dashboard/washing-team/{{$t->id}}/edit" class="btn btn-sm btn-light border" title="ویرایش" style="color: #4b5563;"><i class="fa fa-edit"></i></a>
                        <a href="/dashboard/washing-payments/{{$t->id}}" class="btn btn-sm btn-light border text-primary font-weight-bold px-3">حساب</a>
                        <a href="{{ route('accounting.statements.show', ['entity' => 'washing-team', 'id' => $t->id]) }}" class="btn btn-sm btn-light border text-info" title="صورت حساب"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </td>
                  </tr>
                  @endif
                @endforeach
                
                @if(!isset($search))
                  @php
                      $total_base_rec = \App\WashingPayment::where('type', 'رسید')->sum('base_amount');
                      $total_base_sent = \App\WashingPayment::where('type', 'گرفت')->sum('base_amount');
                      $total_normalized_sum = $total_base_rec - $total_base_sent;
                  @endphp
                  <tr style="background-color: #f8fafc; border-top: 2px solid #cbd5e1;">
                    <td colspan="4" class="text-left font-weight-bold text-dark align-middle" style="font-size: 1.1rem;">مجموعه کل (Grand Total):</td>

                    <!-- Total Normalized USD Balance -->
                    <td class="align-middle" style="direction: ltr;">
                      @if($total_normalized_sum > 0)
                        <span class="text-success font-weight-bold" style="font-size: 1.1rem;">+ ${{ number_format($total_normalized_sum, 2) }}</span>
                      @elseif($total_normalized_sum < 0)
                        <span class="text-danger font-weight-bold" style="font-size: 1.1rem;">- ${{ number_format(abs($total_normalized_sum), 2) }}</span>
                      @else
                        <span class="text-dark font-weight-bold" style="font-size: 1.1rem;">$0.00</span>
                      @endif
                    </td>
                    
                    <!-- Total Selected Currencies Breakdown -->
                    <td class="align-middle" style="direction: ltr; font-size: 1rem;">
                      <div class="font-weight-bold" style="color: {{ ($credit_us - $debit_us) >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format($credit_us - $debit_us, 2) }} USD</div>
                      <div class="font-weight-bold" style="color: {{ ($credit_af - $debit_af) >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format($credit_af - $debit_af, 2) }} AFN</div>
                    </td>
                    
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