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
              <div class="row">
                <div class="col-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">یادداشت (Note)</label>
                    <textarea id="note" name="note" dir="rtl" class="form-control" placeholder="یادداشت را وارد کنید" style="border-radius: 8px; padding: 10px 14px;" rows="3">{{ Request::old('note') }}</textarea>
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
              <div class="row">
                <div class="col-12">
                  <div class="form-group fill">
                    <label style="font-weight: 500; margin-bottom: 8px;">یادداشت (Note)</label>
                    <textarea id="note" name="note" dir="rtl" class="form-control" placeholder="یادداشت را وارد کنید" style="border-radius: 8px; padding: 10px 14px;" rows="3">{{ $team->note }}</textarea>
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
                    @can('view_washing_team_statement')
                    <a href="/dashboard/washing-accounts" class="btn btn-info shadow-sm mr-2 rounded-lg font-weight-bold px-3 border-0" style="background-color: #06b6d4;">
                        <i class="fa fa-calculator mr-1"></i> کارمندان حسابدار
                    </a>
                    @endcan
                    
                    @can('create_washing_team')
                    <button type="button" class="btn btn-success shadow-sm rounded-lg font-weight-bold px-3 border-0" data-toggle="modal" data-target="#washingTeamModal" style="background-color: #10b981;">
                        <i class="fa fa-plus-circle mr-1"></i> ایجاد عضو جدید
                    </button>
                    @endcan
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
                    <td class="font-weight-bold text-dark align-middle d-flex align-items-center justify-content-center">
                      {{ $t->name }}
                      @if(!empty($t->note))
                        <i class="feather icon-file-text text-warning ml-1 btn-washing-note" 
                           style="cursor:pointer;" 
                           data-toggle="modal" 
                           data-target="#washingNoteModal" 
                           data-id="{{ $t->id }}" 
                           data-name="{{ $t->name . ' ' . $t->last_name }}" 
                           data-note="{{ $t->note }}" 
                           title="دارای یادداشت: {{ $t->note }}"></i>
                      @endif
                    </td>
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
                    
                    <!-- Action Buttons -->
                    <td class="align-middle hideOnPrint">
                      <div class="btn-group shadow-sm" role="group">
                        <button type="button" class="btn btn-sm btn-light border text-warning btn-washing-note"
                           data-toggle="modal"
                           data-target="#washingNoteModal"
                           data-id="{{ $t->id }}"
                           data-name="{{ $t->name . ' ' . $t->last_name }}"
                           data-note="{{ $t->note }}"
                           title="یادداشت (Note)">
                           <i class="feather icon-file-text"></i>
                        </button>
                        @can('edit_washing_team')
                        <a href="/dashboard/washing-team/{{$t->id}}/edit" class="btn btn-sm btn-light border" title="ویرایش" style="color: #4b5563;"><i class="fa fa-edit"></i></a>
                        @endcan
                        @can('manage_washing_payments')
                        <a href="/dashboard/washing-payments/{{$t->id}}" class="btn btn-sm btn-light border text-primary font-weight-bold px-3">حساب</a>
                        @endcan
                        @can('view_washing_team_statement')
                        <a href="{{ route('accounting.statements.show', ['entity' => 'washing-team', 'id' => $t->id]) }}" class="btn btn-sm btn-light border text-info" title="صورت حساب"><i class="fa fa-file-pdf-o"></i></a>
                        @endcan
                      </div>
                    </td>
                  </tr>
                  @endif
                @endforeach
                

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- tables  </div>

  <!-- DEDICATED NOTE MODAL -->
  <div class="modal fade" id="washingNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content QBIC-modal-content" style="border-radius: 12px; border: none;">
        <div class="modal-header p-4" style="background: #10b981;">
          <h5 class="modal-title text-white font-weight-bold" id="noteModalWashingName"><i class="feather icon-file-text mr-1"></i> یادداشت تیم شست</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-4 text-right" style="direction: rtl;">
          <form id="washingNoteForm" method="POST" action="">
            @csrf
            <input type="hidden" id="noteWashingId" name="team_id">
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted small mb-2">متن یادداشت (Note Text):</label>
              <textarea id="noteTextareaWashing" name="note" class="form-control" rows="12" style="border-radius: 12px; border: 1px solid #cbd5e1; font-size: 14px; line-height: 1.6; min-height: 280px; max-height: 500px; resize: vertical;" placeholder="یادداشت را اینجا وارد کنید..." @cannot('edit_washing_team') readonly @endcannot></textarea>
            </div>
            <div class="text-right mt-4">
              @can('edit_washing_team')
              <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">
                <i class="feather icon-save mr-1"></i> ذخیره یادداشت
              </button>
              <button type="button" class="btn btn-light rounded-pill px-4 text-muted mr-2" data-dismiss="modal">انصراف</button>
              @else
              <button type="button" class="btn btn-secondary rounded-pill px-4 font-weight-bold shadow-sm" data-dismiss="modal">بستن</button>
              @endcan
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('footer-plugins')
  <script>
      $(document).ready(function () {
          $(document).on('click', '.btn-washing-note', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var note = $(this).data('note');
            $('#noteModalWashingName').html('<i class="feather icon-file-text mr-1"></i> یادداشت: ' + name);
            $('#noteWashingId').val(id);
            $('#noteTextareaWashing').val(note || '');
            $('#washingNoteForm').attr('action', '/dashboard/washing-team/note/' + id);
          });
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