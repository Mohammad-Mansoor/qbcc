@extends('dsh.master')
@section('title' , 'تیم تیاری')
@section('content')

@php
  // Fetch unified USD stats using the normalized base_amount (USD Truth)
  $total_received_usd = \App\FinishingTeamPayment::where('status', 1)->where('type', 'رسید')->sum('base_amount');
  $total_sent_usd = \App\FinishingTeamPayment::where('status', 1)->where('type', 'گرفت')->sum('base_amount');
  $net_balance_usd = $total_received_usd - $total_sent_usd;
@endphp

<style>
  /* Premium Glassmorphism & Custom Elements */
  .modern-card {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
    overflow: hidden;
    margin-bottom: 30px;
  }
  .modern-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 20px 25px;
  }
  .modern-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
  }
  
  /* ANIMATED STATS CARDS */
  .stat-card {
    border: none;
    border-radius: 16px;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: default;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,0.05);
  }
  .stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
  }
  .stat-card::after {
    content: '';
    position: absolute;
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.06);
    border-radius: 50%;
    bottom: -30px;
    left: -30px;
    transition: all 0.5s ease;
  }
  .stat-card:hover::after {
    transform: scale(1.5);
  }
  
  .stat-card-blue {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  .stat-card-indigo {
    background: linear-gradient(135deg, #6366f1, #4338ca);
    color: white;
  }
  .stat-card-green {
    background: linear-gradient(135deg, #10b981, #047857);
    color: white;
  }
  .stat-card-teal {
    background: linear-gradient(135deg, #14b8a6, #0f766e);
    color: white;
  }
  
  .stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.15);
    font-size: 1.4rem;
    margin-bottom: 12px;
    transition: all 0.3s ease;
  }
  .stat-card:hover .stat-card-icon {
    transform: rotate(-10deg) scale(1.1);
    background: rgba(255, 255, 255, 0.25);
  }
  
  .stat-card-val {
    font-size: 1.8rem;
    font-weight: 800;
    letter-spacing: -0.5px;
  }
  .stat-card-lbl {
    font-size: 0.85rem;
    font-weight: 600;
    opacity: 0.9;
    margin-top: 4px;
  }
</style>

<!-- ANIMATED STATS CARDS ROW -->
<div class="row mb-4">
  <!-- Card 1: Total Teams -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-blue p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ $teams->count() }} <span style="font-size: 1rem; font-weight: normal;">تیم</span></div>
          <div class="stat-card-lbl">تعداد کل تیم‌ها (Total Teams)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-users"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 2: Total Payments Sent (گرفت) -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-indigo p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">$ {{ number_format($total_sent_usd, 2) }}</div>
          <div class="stat-card-lbl">کل پرداختی‌ها (Total Paid - USD)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-paper-plane"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 3: Total Receipts (رسید) -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-green p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">$ {{ number_format($total_received_usd, 2) }}</div>
          <div class="stat-card-lbl">کل دریافتی‌ها (Total Received - USD)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-check-circle"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 4: Net Balance -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-teal p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">$ {{ number_format($net_balance_usd, 2) }}</div>
          <div class="stat-card-lbl">تصفیه نهایی (Net Balance - USD)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-shield"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Table Card -->
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card modern-card" id="teams">
      <div class="card-header modern-header text-right">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h3 class="modern-title"><i class="fa fa-users"></i> مدیریت تیم‌های تیاری (Finishing Teams)</h3>
          </div>
          <div class="col-md-6 text-left hideOnPrint">
            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 ml-2" data-toggle="modal" data-target="#createTeamModal">
              <i class="fa fa-plus"></i> ثبت تیم جدید
            </button>
            <a href="/dashboard/finishing-accounts" class="btn btn-sm btn-info rounded-pill px-3 ml-2">
              <i class="fa fa-user-secret"></i> کارمندان حسابدار
            </a>
            <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="printPage('teams')">
              <i class="fa fa-print"></i> چاپ
            </button>
          </div>
        </div>
        
        <div class="row mt-3 hideOnPrint">
          <div class="col-md-4">
            <form action="/dashboard/finish-team/search" method="post">
              @csrf
              <div class="input-group">
                <input type="text" name="search" required placeholder="جستجوی تیم..." class="form-control rounded-pill-right" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-primary rounded-pill-left"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="card-body p-4">
        @if(session("status"))
          <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa fa-check-circle mr-2"></i> {{session('status')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif
        @if(session("error"))
          <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa fa-times-circle mr-2"></i> {{session('error')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-hover text-right mb-0" id="finishing_teams">
            <thead>
              <tr>
                <th class="px-4">آی دی</th>
                <th>نام تیم</th>
                <th>نام پدر</th>
                <th>نمبر تذکره</th>
                <th>شماره تماس</th>
                <th>آدرس</th>
                <th>نام ضمانت کننده</th>
                <th>باقیات (USD Normalized)</th>
                <th class="hideOnPrint text-center">ویرایش</th>
                <th class="hideOnPrint text-center">حساب</th>
              </tr>
            </thead>
            <tbody>
              @if(!isset($accounts))
                @foreach($teams as $t)
                  @php
                    $total_balance_usd = \Illuminate\Support\Facades\DB::table('finishing_team_payments')
                        ->where('team_id', $t->id)
                        ->where('status', 1)
                        ->sum(\DB::raw("CASE WHEN type = 'رسید' THEN base_amount ELSE -base_amount END"));
                  @endphp
                  <tr>
                    <td class="px-4 font-weight-bold text-muted">{{ $t->id }}</td>
                    <td class="font-weight-bold">{{ $t->name }}</td>
                    <td>{{ $t->father_name ?? 'N/A' }}</td>
                    <td>{{ $t->tazkira_number ?? 'N/A' }}</td>
                    <td style="direction: ltr; text-align: right;">{{ $t->contact_number ?? 'N/A' }}</td>
                    <td>{{ $t->address ?? 'N/A' }}</td>
                    <td>{{ $t->grantor_name ?? 'N/A' }}</td>
                    
                    @if($total_balance_usd > 0)
                      <td style="direction: ltr; color: green; font-weight: bold;">$ {{ number_format($total_balance_usd, 2) }}</td>
                    @elseif($total_balance_usd < 0)
                      <td style="direction: ltr; color: red; font-weight: bold;">$ {{ number_format($total_balance_usd, 2) }}</td>
                    @else
                      <td style="direction: ltr; color: #64748b;">$ 0.00</td>
                    @endif

                    <td class="hideOnPrint text-center">
                      <a href="/dashboard/finish-team/{{$t->id}}/edit" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="ویرایش تیم">
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                    <td class="hideOnPrint text-center">
                      <a href="/dashboard/finishing-payments/{{$t->id}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="حسابات و پرداخت‌ها">
                        <i class="fa fa-calculator"></i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              @else
                @foreach($teams as $t)
                  @php
                    $total_balance_usd = \Illuminate\Support\Facades\DB::table('finishing_team_payments')
                        ->where('team_id', $t->id)
                        ->where('status', 1)
                        ->sum(\DB::raw("CASE WHEN type = 'رسید' THEN base_amount ELSE -base_amount END"));
                  @endphp
                  @if($t->payment->count() > 0 && $total_balance_usd != 0)
                    <tr>
                      <td class="px-4 font-weight-bold text-muted">{{ $t->id }}</td>
                      <td class="font-weight-bold">{{ $t->name }}</td>
                      <td>{{ $t->father_name ?? 'N/A' }}</td>
                      <td>{{ $t->tazkira_number ?? 'N/A' }}</td>
                      <td style="direction: ltr; text-align: right;">{{ $t->contact_number ?? 'N/A' }}</td>
                      <td>{{ $t->address ?? 'N/A' }}</td>
                      <td>{{ $t->grantor_name ?? 'N/A' }}</td>
                      
                      @if($total_balance_usd > 0)
                        <td style="direction: ltr; color: green; font-weight: bold;">$ {{ number_format($total_balance_usd, 2) }}</td>
                      @elseif($total_balance_usd < 0)
                        <td style="direction: ltr; color: red; font-weight: bold;">$ {{ number_format($total_balance_usd, 2) }}</td>
                      @else
                        <td style="direction: ltr; color: #64748b;">$ 0.00</td>
                      @endif

                      <td class="hideOnPrint text-center">
                        <a href="/dashboard/finish-team/{{$t->id}}/edit" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="ویرایش تیم">
                          <i class="fa fa-edit"></i>
                        </a>
                      </td>
                      <td class="hideOnPrint text-center">
                        <a href="/dashboard/finishing-payments/{{$t->id}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="حسابات و پرداخت‌ها">
                          <i class="fa fa-calculator"></i>
                        </a>
                      </td>
                    </tr>
                  @endif
                @endforeach
              @endif

              @if(!isset($search))
                @php
                  $grand_total_balance = \App\FinishingTeamPayment::where('status', 1)
                      ->sum(\DB::raw("CASE WHEN type = 'رسید' THEN base_amount ELSE -base_amount END"));
                @endphp
                <tr style="background: #f8fafc; font-weight: bold;">
                  <td class="px-4"></td>
                  <td>مجموعه کل:</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  @if($grand_total_balance > 0)
                    <td style="direction: ltr; color: green;">$ {{ number_format($grand_total_balance, 2) }}</td>
                  @elseif($grand_total_balance < 0)
                    <td style="direction: ltr; color: red;">$ {{ number_format($grand_total_balance, 2) }}</td>
                  @else
                    <td style="direction: ltr; color: #64748b;">$ 0.00</td>
                  @endif
                  <td class="hideOnPrint"></td>
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

{{-- MODALS SECTION --}}

<!-- Create Team Modal -->
<div class="modal fade" id="createTeamModal" tabindex="-1" role="dialog" aria-labelledby="createTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.15);">
      <div class="modal-header text-right" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px 25px; display: flex; justify-content: space-between; align-items: center;">
        <h5 class="modal-title font-weight-bold" id="createTeamModalLabel"><i class="fa fa-plus-circle text-success"></i> ایجاد تیم جدید</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: -20px -25px -20px auto;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/dashboard/finish-team" method="post">
        @csrf
        <div class="modal-body text-right" style="padding: 25px; direction: rtl;">
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نام تیم</label>
              <input type="text" name="name" id="name" class="form-control custom-input" placeholder="نام تیم را وارد کنید" required>
              @error('name') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نام پدر</label>
              <input type="text" name="father_name" id="father_name" class="form-control custom-input" placeholder="نام پدر را وارد کنید">
              @error('father_name') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نمبر تذکره</label>
              <input type="text" name="tazkira_number" id="tazkira_number" class="form-control custom-input" placeholder="نمبر تذکره را وارد کنید">
              @error('tazkira_number') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">شماره تماس</label>
              <input type="text" name="contact_number" id="contact_number" class="form-control custom-input text-left" placeholder="شماره تماس را وارد کنید" style="direction: ltr;">
              @error('contact_number') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نام ضمانت کننده</label>
              <input type="text" name="grantor_name" id="grantor_name" class="form-control custom-input" placeholder="نام ضمانت کننده را وارد کنید">
              @error('grantor_name') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">آدرس</label>
              <textarea name="address" id="address" class="form-control custom-input" placeholder="آدرس را وارد کنید" rows="1"></textarea>
              @error('address') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 15px 25px;">
          <button type="button" class="btn btn-light rounded-pill px-3" data-dismiss="modal">انصراف</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">ذخیره</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Team Modal -->
@if($fteamEdit)
<div class="modal fade" id="editTeamModal" tabindex="-1" role="dialog" aria-labelledby="editTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.15);">
      <div class="modal-header text-right" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px 25px; display: flex; justify-content: space-between; align-items: center;">
        <h5 class="modal-title font-weight-bold" id="editTeamModalLabel"><i class="fa fa-pencil text-warning"></i> ویرایش تیم</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: -20px -25px -20px auto;" onclick="window.location='/dashboard/finish-team'">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/dashboard/finish-team/{{$fteamEdit->id}}" method="post">
        @csrf
        @method('PUT')
        <div class="modal-body text-right" style="padding: 25px; direction: rtl;">
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نام تیم</label>
              <input type="text" name="name" value="{{$fteamEdit->name}}" id="name" class="form-control custom-input" required>
              @error('name') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نام پدر</label>
              <input type="text" name="father_name" value="{{$fteamEdit->father_name}}" id="father_name" class="form-control custom-input">
              @error('father_name') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نمبر تذکره</label>
              <input type="text" name="tazkira_number" value="{{$fteamEdit->tazkira_number}}" id="tazkira_number" class="form-control custom-input">
              @error('tazkira_number') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">شماره تماس</label>
              <input type="text" name="contact_number" value="{{$fteamEdit->contact_number}}" id="contact_number" class="form-control custom-input text-left" style="direction: ltr;">
              @error('contact_number') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">نام ضمانت کننده</label>
              <input type="text" name="grantor_name" value="{{$fteamEdit->grantor_name}}" id="grantor_name" class="form-control custom-input">
              @error('grantor_name') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold mb-2">آدرس</label>
              <textarea name="address" id="address" class="form-control custom-input" rows="1">{{$fteamEdit->address}}</textarea>
              @error('address') <p class="text-danger mt-1">{{$message}}</p> @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 15px 25px;">
          <a href="/dashboard/finish-team" class="btn btn-light rounded-pill px-3">انصراف</a>
          <button type="submit" class="btn btn-primary rounded-pill px-4">ذخیره</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection

@section('scripts')
  <script>
      $(document).ready(function () {
          // Initialize Tooltips
          $('[data-toggle="tooltip"]').tooltip();

          // Auto open Edit modal if editing
          @if($fteamEdit)
            $('#editTeamModal').modal('show');
          @endif

          // Table Export xlsx
          $("#finishing_teams").tableExport({
              headers: true,
              footers: true,
              formats: ["xlsx"],
              filename: "finishing_teams_export",
              bootstrap: true,
              exportButtons: true,
              position: "bottom",
              ignoreRows: null,
              ignoreCols: [8, 9],
              trimWhitespace: true,
              RTL: true,
              sheetname: "finishing_teams"
          });
          
          var $buttons = $('#finishing_teams').find('caption').children().detach();
          $buttons.appendTo('#exportButton');
      });
  </script>
@endsection
