@extends('dsh.master')
@section('title', 'تیم کچایی')
@section('content')

  <style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
      background: white;
      border: 1px solid var(--QBIC-border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-soft);
      margin-bottom: 30px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .glass-header {
      background: var(--QBIC-surface);
      padding: 20px 25px;
      border-bottom: 1px solid var(--QBIC-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .glass-header h4 {
      margin: 0;
      font-weight: 700;
      color: var(--QBIC-primary);
      font-size: 1.2rem;
    }

    .custom-input {
      border-radius: 8px;
      border: 2px solid #e8f5e9;
      padding: 10px 15px;
      transition: all 0.2s;
    }

    .custom-input:focus {
      border-color: #43a047;
      box-shadow: 0 0 0 0.2rem rgba(67, 160, 71, 0.1);
    }

    .table-modern thead th {
      background: #f8fafc;
      color: #64748b;
      font-weight: 700;
      text-transform: uppercase;
      border: none;
      letter-spacing: 0.5px;
      padding: 15px;
      font-size: 0.8rem;
    }

    .table-modern tbody td {
      padding: 15px;
      vertical-align: middle;
      border-top: 1px solid #f1f5f9;
      color: #334155;
    }

    .table-modern tbody tr:hover {
      background: #f8fafc;
    }

    .btn-premium {
      border-radius: 8px;
      font-weight: 600;
      padding: 8px 16px;
      transition: all 0.2s;
    }

    /* ANIMATED STATS CARDS */
    .stat-card {
      border: none;
      border-radius: 16px;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: default;
      box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
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
      <i class="fa fa-exclamation-circle mr-2"></i> {{session('error')}}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <!-- ANIMATED STATS CARDS ROW -->
  <div class="row mb-4">
    <!-- Card 1: Total Kachaee Teams -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-blue p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">{{ count($team) }} <span style="font-size: 1rem; font-weight: normal;">تیم</span>
            </div>
            <div class="stat-card-lbl">تعداد تیم‌های کچایی (Total Teams)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-users"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 2: Active Carpets in Kachaee -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-indigo p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">{{ \App\Carpet::where('status', 2)->whereNotNull('kachaee_id')->count() }} <span
                style="font-size: 1rem; font-weight: normal;">عدد</span></div>
            <div class="stat-card-lbl">قالین‌های در حال کچایی (Active Carpets)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-clock-o"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 3: Net Balance USD -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-green p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">${{ number_format($credit_us - $debit_us, 2) }}</div>
            <div class="stat-card-lbl">مجموع باقیات دالر (Net USD Balance)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-usd"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 4: Net Balance AFN -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-teal p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">؋{{ number_format($credit_af - $debit_af, 2) }}</div>
            <div class="stat-card-lbl">مجموع باقیات افغانی (Net AFN Balance)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-money"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Kachaee Team Modal -->
  <div class="modal fade" id="kachaeeTeamModal" tabindex="-1" role="dialog" aria-labelledby="kachaeeTeamModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content"
        style="border-radius: 20px; border: none; background: rgba(255, 255, 255, 0.98); box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden;">
        <form action="{{ !$teamEdit ? '/dashboard/kachaee-team' : '/dashboard/kachaee-team/' . $teamEdit->id }}"
          method="post" id="kachaeeTeamForm">
          @csrf
          @if($teamEdit)
            @method('PUT')
          @endif

          <div class="modal-header text-white p-4"
            style="border: none; background: linear-gradient(135deg, #11998e, #38ef7d);">
            <h5 class="modal-title font-weight-bold" id="kachaeeTeamModalLabel" style="font-size: 1.25rem;">
              <i class="fa {{ !$teamEdit ? 'fa-user-plus' : 'fa-edit' }} mr-2"></i>
              {{ !$teamEdit ? 'ایجاد تیم کچایی جدید' : 'ویرایش تیم کچایی' }}
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
              style="opacity: 0.8; outline: none;">
              <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
            </button>
          </div>

          <div class="modal-body p-4 text-right" style="direction: rtl;">
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-user text-success mr-1"></i> نام
                    <span class="text-danger">*</span></label>
                  <input type="text" value="{{ $teamEdit ? $teamEdit->name : Request::old('name') }}" id="name"
                    name="name" class="form-control custom-input" required>
                  <small class="text-danger">@error('name') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-user text-muted mr-1"></i> نام پدر
                    <span class="text-danger">*</span></label>
                  <input type="text" id="father_name"
                    value="{{ $teamEdit ? $teamEdit->father_name : Request::old('father_name') }}" name="father_name"
                    class="form-control custom-input" required>
                  <small class="text-danger">@error('father_name') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-user text-muted mr-1"></i> نام پدر
                    کلان <span class="text-danger">*</span></label>
                  <input type="text" id="grand_father_name" name="grand_father_name"
                    value="{{ $teamEdit ? $teamEdit->grand_father_name : Request::old('grand_father_name') }}"
                    class="form-control custom-input" required>
                  <small class="text-danger">@error('grand_father_name') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-id-card text-muted mr-1"></i> نمبر
                    تذکره <span class="text-danger">*</span></label>
                  <input type="text" id="national_id" name="national_id"
                    value="{{ $teamEdit ? $teamEdit->national_id : Request::old('national_id') }}"
                    class="form-control custom-input" required>
                  <small class="text-danger">@error('national_id') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-phone text-muted mr-1"></i> شماره
                    تماس <span class="text-danger">*</span></label>
                  <input type="text" id="contact_no" name="contact_no"
                    value="{{ $teamEdit ? $teamEdit->contact_no : Request::old('contact_no') }}" dir="ltr"
                    class="form-control custom-input text-right" required>
                  <small class="text-danger">@error('contact_no') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-shield text-muted mr-1"></i> نام
                    ضمانت کننده</label>
                  <input type="text" id="g_name" name="g_name"
                    value="{{ $teamEdit ? $teamEdit->g_name : Request::old('g_name') }}"
                    class="form-control custom-input">
                  <small class="text-danger">@error('g_name') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
              <div class="col-12 mb-3">
                <div class="form-group">
                  <label class="font-weight-bold text-muted small mb-2"><i class="fa fa-map-marker text-muted mr-1"></i>
                    آدرس <span class="text-danger">*</span></label>
                  <input type="text" id="address" name="address"
                    value="{{ $teamEdit ? $teamEdit->address : Request::old('address') }}"
                    class="form-control custom-input" required>
                  <small class="text-danger">@error('address') {{ __('message.' . $message) }} @enderror</small>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light p-3" style="border: none;">
            <div class="w-100 d-flex justify-content-between align-items-center">
              @if($teamEdit)
                <a href="/dashboard/kachaee-team" class="btn btn-light btn-premium shadow-sm px-4"
                  style="border-radius: 10px;">انصراف</a>
              @else
                <button type="button" class="btn btn-light btn-premium shadow-sm px-4" data-dismiss="modal"
                  style="border-radius: 10px;">انصراف</button>
              @endif
              <button type="submit" class="btn btn-success btn-premium shadow-sm px-4"
                style="border-radius: 10px; background: linear-gradient(135deg, #11998e, #38ef7d); border: none;">
                <i class="fa fa-save mr-2"></i> {{ !$teamEdit ? 'ذخیره تیم' : 'بروزرسانی تیم' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="row" id="kachaee">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="glass-card">
        <div class="glass-header">
          <h4><i class="fa fa-list mr-2 text-primary"></i> لیست تیم‌های کچایی</h4>
          <div class="d-flex align-items-center hideOnPrint">
            @can('create_kachaee_team')
              <button class="btn btn-success btn-premium shadow-sm mr-3 px-3" data-toggle="modal"
                data-target="#kachaeeTeamModal">
                <i class="fa fa-plus mr-1"></i> ایجاد تیم جدید
              </button>
            @endcan
            <form action="/dashboard/kachaee-team/search" method="post" class="mr-3">
              @csrf
              <div class="input-group">
                <input type="text" name="search" required placeholder="جستجو سریع..." class="form-control custom-input"
                  style="height: 38px;">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
            <div id="exportButton" class="mr-2"></div>
            <button class="btn btn-outline-primary btn-sm mr-2" onclick="printPage('kachaee')"><i
                class="fa fa-print mr-1"></i> چاپ</button>
            @can('view_kachaee_team_statement')
              <a href="/dashboard/kachaee-accounts" class="btn btn-outline-info btn-sm"><i class="fa fa-users mr-1"></i>
                کارمندان حسابدار</a>
            @endcan
          </div>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern text-center mb-0" id="kachaee_team">
              <thead>
                <tr>
                  <th>نام</th>
                  <th>نام پدر</th>
                  <th>نام پدرکلان</th>
                  <th>نمبر تذکره</th>
                  <th>آدرس</th>
                  <th>شماره تماس</th>
                  <th>شخص تضمین کننده</th>
                  <th>باقیات (USD $)</th>
                  <th>باقیات (AFN ؋)</th>
                  <th class="hideOnPrint text-center">عملیات</th>
                </tr>
              </thead>
              <tbody>
                @foreach($team as $t)
                  @php
                    // Using the new Eloquent Accessors instead of raw DB queries inside Blade
                    $total_usd = $t->total_usd_balance;
                    $total_af = $t->total_af_balance;

                    // Skip condition for 'accounts' view
                    if (isset($accounts) && !($t->payment->count() > 0 && ($total_af != 0 || $total_usd != 0))) {
                      continue;
                    }
                  @endphp
                  <tr>
                    <td class="font-weight-bold text-dark">{{ $t->name }}</td>
                    <td>{{ $t->father_name }}</td>
                    <td>{{ $t->grand_father_name }}</td>
                    <td>{{ $t->national_id }}</td>
                    <td>{{ $t->address }}</td>
                    <td style="direction: ltr">{{ $t->contact_no }}</td>
                    <td>{{ $t->g_name }}</td>

                    <!-- USD Balance -->
                    <td style="direction: ltr;"
                      class="font-weight-bold {{ $total_usd > 0 ? 'text-success' : ($total_usd < 0 ? 'text-danger' : '') }}">
                      {{ number_format($total_usd, 2) }}
                    </td>

                    <!-- AFN Balance -->
                    <td style="direction: ltr;"
                      class="font-weight-bold {{ $total_af > 0 ? 'text-success' : ($total_af < 0 ? 'text-danger' : '') }}">
                      {{ number_format($total_af, 2) }}
                    </td>

                    <td class="hideOnPrint text-center">
                      <div class="btn-group">
                        @can('edit_kachaee_team')
                          <a href="/dashboard/kachaee-team/{{$t->id}}/edit"
                            class="btn btn-sm btn-light-primary border-0 shadow-none p-2" title="ویرایش (Edit)">
                            <i class="feather icon-edit-2"></i>
                          </a>
                        @endcan
                        @can('manage_kachaee_payments')
                          <a href="/dashboard/kachaee-payments/{{$t->id}}"
                            class="btn btn-sm btn-light-success border-0 shadow-none p-2 mx-1" title="حسابات مالی (Ledger)">
                            <i class="feather icon-dollar-sign"></i> حساب
                          </a>
                        @endcan
                        @can('view_kachaee_team_statement')
                          <a href="{{ route('accounting.statements.show', ['entity' => 'kachayee-team', 'id' => $t->id]) }}"
                            class="btn btn-sm btn-light-info border-0 shadow-none p-2 mx-1" title="صورت حساب مالی">
                            <i class="feather icon-file-text"></i> صورت حساب
                          </a>
                        @endcan
                      </div>
                    </td>
                  </tr>
                @endforeach

                @if(!isset($search))
                  <tr style="background: #f8fafc;" class="font-weight-bold">
                    <td colspan="7" class="text-right text-muted">مجموع کل (Total):</td>
                    <td style="direction: ltr;"
                      class="{{ ($credit_us - $debit_us) >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ number_format($credit_us - $debit_us, 2) }}
                    </td>
                    <td style="direction: ltr;"
                      class="{{ ($credit_af - $debit_af) >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ number_format($credit_af - $debit_af, 2) }}
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
@endsection

@section('scripts')
  <script>
    $(document).ready(function () {
      $("#kachaee_team").tableExport({
        headers: true,
        footers: true,
        formats: ["xlsx"],
        filename: "kachaee_teams",
        bootstrap: true,
        exportButtons: true,
        position: "bottom",
        ignoreCols: 9,
        trimWhitespace: true,
        RTL: true,
        sheetname: "Teams"
      });
      var $buttons = $('#kachaee_team').find('caption').children().detach();
      $buttons.appendTo('#exportButton');

      // Auto-show modal if validation fails or we are in edit mode
      @if($errors->any() || $teamEdit)
        $('#kachaeeTeamModal').modal('show');
      @endif

      // Handle redirect to list when edit modal is closed/dismissed
      $('#kachaeeTeamModal').on('hidden.bs.modal', function () {
        @if($teamEdit)
          window.location.href = '/dashboard/kachaee-team';
        @endif
            });
    });
  </script>
@endsection