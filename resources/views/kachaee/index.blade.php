@extends('dsh.master')
@section('title' , 'تیم کچایی')
@section('content')

<style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .glass-header {
        background: var(--qbcc-surface);
        padding: 20px 25px;
        border-bottom: 1px solid var(--qbcc-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .glass-header h4 {
        margin: 0;
        font-weight: 700;
        color: var(--qbcc-primary);
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

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="glass-card">
        <div class="glass-header">
          <h4><i class="fa fa-users text-success mr-2"></i> {{ !$teamEdit ? 'ایجاد تیم کچایی جدید' : 'ویرایش تیم کچایی' }}</h4>
        </div>
        <div class="card-body p-4">
          @if(!$teamEdit)
            <form action="/dashboard/kachaee-team" method="post">
              @csrf
              <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام</label>
                    <input type="text" value="{{ Request::old('name') }}" id="name" name="name" class="form-control custom-input">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام پدر</label>
                    <input type="text" id="father_name" value="{{ Request::old('father_name') }}" name="father_name" class="form-control custom-input">
                    <small class="text-danger">@error('father_name') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام پدر کلان</label>
                    <input type="text" id="grand_father_name" name="grand_father_name" value="{{ Request::old('grand_father_name') }}" class="form-control custom-input">
                    <small class="text-danger">@error('grand_father_name') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نمبر تذکره</label>
                    <input type="text" id="national_id" name="national_id" value="{{ Request::old('national_id') }}" class="form-control custom-input">
                    <small class="text-danger">@error('national_id') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">شماره تماس</label>
                    <input type="text" id="contact_no" name="contact_no" value="{{ Request::old('contact_no') }}" dir="ltr" class="form-control custom-input">
                    <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام ضمانت کننده</label>
                    <input type="text" id="g_name" name="g_name" value="{{ Request::old('g_name') }}" class="form-control custom-input">
                    <small class="text-danger">@error('g_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-8 col-sm-12 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">آدرس</label>
                    <input type="text" id="address" name="address" value="{{ Request::old('address') }}" class="form-control custom-input">
                    <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-lg-12 text-right">
                  <button class="btn btn-primary btn-premium shadow-sm" type="submit"><i class="fa fa-save mr-2"></i> ذخیره تیم</button>
                  <a href="/dashboard/kachaee-team" class="btn btn-light btn-premium shadow-sm ml-2">انصراف</a>
                </div>
              </div>
            </form>
          @else
            <form action="/dashboard/kachaee-team/{{$teamEdit->id}}" method="post">
              @csrf
              @method('PUT')
              <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام</label>
                    <input type="text" value="{{ $teamEdit->name }}" id="name" name="name" class="form-control custom-input">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام پدر</label>
                    <input type="text" id="father_name" value="{{ $teamEdit->father_name}}" name="father_name" class="form-control custom-input">
                    <small class="text-danger">@error('father_name') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام پدر کلان</label>
                    <input type="text" id="grand_father_name" name="grand_father_name" value="{{ $teamEdit->grand_father_name }}" class="form-control custom-input">
                    <small class="text-danger">@error('grand_father_name') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نمبر تذکره</label>
                    <input type="text" id="national_id" name="national_id" value="{{ $teamEdit->national_id }}" class="form-control custom-input">
                    <small class="text-danger">@error('national_id') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">شماره تماس</label>
                    <input type="text" id="contact_no" name="contact_no" value="{{ $teamEdit->contact_no }}" dir="ltr" class="form-control custom-input">
                    <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }}@enderror</small>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">نام ضمانت کننده</label>
                    <input type="text" id="g_name" name="g_name" value="{{ $teamEdit->g_name }}" class="form-control custom-input">
                    <small class="text-danger">@error('g_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-8 col-sm-12 mb-3">
                  <div class="form-group">
                    <label class="font-weight-bold text-muted small">آدرس</label>
                    <input type="text" id="address" name="address" value="{{$teamEdit->address}}" class="form-control custom-input">
                    <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-lg-12 text-right">
                  <button class="btn btn-primary btn-premium shadow-sm" type="submit"><i class="fa fa-save mr-2"></i> بروزرسانی تیم</button>
                  <a href="/dashboard/kachaee-team" class="btn btn-light btn-premium shadow-sm ml-2">انصراف</a>
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
      <div class="glass-card">
        <div class="glass-header">
          <h4><i class="fa fa-list mr-2 text-primary"></i> لیست تیم‌های کچایی</h4>
          <div class="d-flex align-items-center hideOnPrint">
            <form action="/dashboard/kachaee-team/search" method="post" class="mr-3">
              @csrf
              <div class="input-group">
                <input type="text" name="search" required placeholder="جستجو سریع..." class="form-control custom-input" style="height: 38px;">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
            <div id="exportButton" class="mr-2"></div>
            <button class="btn btn-outline-primary btn-sm mr-2" onclick="printPage('kachaee')"><i class="fa fa-print mr-1"></i> چاپ</button>
            <a href="/dashboard/kachaee-accounts" class="btn btn-outline-info btn-sm"><i class="fa fa-users mr-1"></i> کارمندان حسابدار</a>
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
                  if(isset($accounts) && !($t->payment->count() > 0 && ($total_af != 0 || $total_usd != 0))) {
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
                  <td style="direction: ltr;" class="font-weight-bold {{ $total_usd > 0 ? 'text-success' : ($total_usd < 0 ? 'text-danger' : '') }}">
                    {{ number_format($total_usd, 2) }}
                  </td>
                  
                  <!-- AFN Balance -->
                  <td style="direction: ltr;" class="font-weight-bold {{ $total_af > 0 ? 'text-success' : ($total_af < 0 ? 'text-danger' : '') }}">
                    {{ number_format($total_af, 2) }}
                  </td>
                  
                  <td class="hideOnPrint text-center">
                    <div class="btn-group">
                        <a href="/dashboard/kachaee-team/{{$t->id}}/edit" class="btn btn-sm btn-light-primary border-0 shadow-none p-2" title="ویرایش (Edit)">
                            <i class="feather icon-edit-2"></i>
                        </a>
                        <a href="/dashboard/kachaee-payments/{{$t->id}}" class="btn btn-sm btn-light-success border-0 shadow-none p-2 mx-1" title="حسابات مالی (Ledger)">
                            <i class="feather icon-dollar-sign"></i> حساب
                        </a>
                    </div>
                  </td>
                </tr>
              @endforeach
              
              @if(!isset($search))
                <tr style="background: #f8fafc;" class="font-weight-bold">
                  <td colspan="7" class="text-right text-muted">مجموع کل (Total):</td>
                  <td style="direction: ltr;" class="{{ ($credit_us - $debit_us) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($credit_us - $debit_us, 2) }}
                  </td>
                  <td style="direction: ltr;" class="{{ ($credit_af - $debit_af) >= 0 ? 'text-success' : 'text-danger' }}">
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
      });
  </script>
@endsection