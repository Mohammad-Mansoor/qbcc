@extends('dsh.master')
@section('title' , 'Agent Profile - QBCC Forensic ERP')

@section('content')
<style>
    /* QBCC PREMIUM DASHBOARD SYSTEM */
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-success: #10b981;
        --qbcc-warning: #f59e0b;
        --qbcc-danger: #ef4444;
        --qbcc-border: #e2e8f0;
        --qbcc-bg: #f8fafc;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    .profile-header {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: 1px solid var(--qbcc-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .agent-hero { display: flex; align-items: center; gap: 25px; }
    .hero-avatar {
        width: 100px; height: 100px;
        border-radius: 25px;
        object-fit: cover;
        border: 4px solid #f1f5f9;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .hero-info h2 { font-weight: 800; color: #1e293b; margin-bottom: 5px; font-size: 24px; }
    .hero-info .account-id { 
        background: #f1f5f9; 
        color: var(--qbcc-primary); 
        padding: 4px 12px; 
        border-radius: 8px; 
        font-weight: 700; 
        font-size: 13px; 
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--qbcc-border);
        transition: all 0.2s;
        height: 100%;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .stat-label { color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 10px; display: block; }
    .stat-value { font-size: 20px; font-weight: 800; color: #1e293b; }

    .glass-section {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--qbcc-border);
        margin-bottom: 25px;
        overflow: hidden;
    }
    .section-header {
        background: #f8fafc;
        padding: 15px 25px;
        border-bottom: 1px solid var(--qbcc-border);
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-body { padding: 25px; }

    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .info-item label { display: block; color: #94a3b8; font-size: 11px; font-weight: 700; margin-bottom: 5px; text-transform: uppercase; }
    .info-item span { display: block; color: #1e293b; font-weight: 600; font-size: 14px; }

    .badge-status {
        padding: 6px 16px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 12px;
    }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #fee2e2; color: #b91c1c; }

    .btn-action-premium {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }
    .btn-primary-p { background: var(--qbcc-primary); color: white; }
    .btn-primary-p:hover { background: #1a365d; transform: translateY(-2px); }
    .btn-light-p { background: #f1f5f9; color: #475569; }

    .doc-preview {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        margin-top: 15px;
    }

    .phone-pill {
        background: #f1f5f9;
        padding: 8px 15px;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .phone-pill:hover { border-color: var(--qbcc-secondary); background: white; }
</style>

<div class="container-fluid">
    <!-- HERO HEADER -->
    <div class="profile-header">
        <div class="agent-hero">
            <img src="{{ $agent->image ? '/'.$agent->image : 'https://ui-avatars.com/api/?name='.$agent->user->name.'&background=1e3a8a&color=fff' }}" class="hero-avatar">
            <div class="hero-info">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="account-id">ID: {{ $agent->account_no }}</span>
                    <span class="badge-status {{ $agent->account_status == 1 ? 'status-active' : 'status-inactive' }}">
                        {{ $agent->account_status == 1 ? 'حساب فعال' : 'حساب غیر فعال' }}
                    </span>
                </div>
                <h2>{{ $agent->user->name . ' ' . $agent->user->last_name }}</h2>
                <p class="text-muted mb-0 small"><i class="feather icon-map-pin mr-1"></i> {{ $agent->province->province }} - {{ $agent->agent_address }}</p>
            </div>
        </div>
        <div class="hero-actions">
            <a href="/dashboard/agents/{{ $agent->agent_id }}/edit" class="btn-action-premium btn-primary-p mr-2">
                <i class="feather icon-edit"></i> ویرایش پروفایل
            </a>
            <a href="/dashboard/agent-status-change/{{$agent->agent_id}}" class="btn-action-premium {{ $agent->account_status == 1 ? 'btn-light-p text-danger' : 'btn-success text-white' }}">
                <i class="feather icon-power"></i> {{ $agent->account_status == 1 ? 'غیر فعال سازی' : 'فعال سازی حساب' }}
            </a>
        </div>
    </div>

    <!-- QUICK STATS -->
    @php($bal_af = \DB::table('agent_payments')->where('agent_id', $agent->agent_id)->where('type', 'رسید')->sum('amount_af') - \DB::table('agent_payments')->where('agent_id', $agent->agent_id)->where('type', 'گرفت')->sum('amount_af'))
    @php($bal_usd = \DB::table('agent_payments')->where('agent_id', $agent->agent_id)->where('type', 'رسید')->sum('amount') - \DB::table('agent_payments')->where('agent_id', $agent->agent_id)->where('type', 'گرفت')->sum('amount'))
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">باقیات دالری (USD)</span>
                <span class="stat-value {{ $bal_usd >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($bal_usd, 2) }} $</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">باقیات افغانی (AFN)</span>
                <span class="stat-value {{ $bal_af >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($bal_af, 0) }} ؋</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">نوعیت قرارداد</span>
                <span class="stat-value text-primary">{{ $agent->contract_type == 'contractional' ? 'قراردادی' : 'وزنی' }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">ارز پایه حساب</span>
                <span class="stat-value" style="text-transform: uppercase;">{{ $agent->account_type }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- LEFT COLUMN: CONTRACT & FINANCE -->
        <div class="col-lg-7">
            <div class="glass-section">
                <div class="section-header">
                    <i class="feather icon-file-text text-primary"></i> جزییات قرار داد و اسناد
                </div>
                <div class="section-body">
                    <div class="info-grid mb-4">
                        <div class="info-item">
                            <label>تاریخ ثبت قرارداد</label>
                            <span>{{ $agent->contract_date }}</span>
                        </div>
                        <div class="info-item">
                            <label>تعداد شماره تماس</label>
                            <span>{{ $agent->phone->count() }} شماره ثبت شده</span>
                        </div>
                    </div>
                    
                    <div class="info-item mb-4">
                        <label>نوت و تشریحات مدیریتی</label>
                        <p class="text-dark font-weight-bold">{{ $agent->description ?? 'هیچ توضیحی ثبت نشده است.' }}</p>
                    </div>

                    <label class="small font-weight-bold text-muted mb-3 d-block">اسکن قرارداد (Contract Scan)</label>
                    @if($agent->contract_scan_file)
                        <div class="doc-preview">
                            <i class="feather icon-image text-primary" style="font-size: 40px; display: block; margin-bottom: 15px;"></i>
                            <h5 class="font-weight-bold">فایل اسکن شده موجود است</h5>
                            <p class="small text-muted mb-4">آخرین آپدیت در سیستم QBCC ثبت شده است.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/{{ $agent->contract_scan_file }}" target="_blank" class="btn btn-primary btn-sm rounded-lg px-4"><i class="feather icon-eye mr-1"></i> مشاهده فایل</a>
                                <a href="#" class="btn btn-outline-secondary btn-sm rounded-lg px-4"><i class="feather icon-refresh-cw mr-1"></i> آپدیت جدید</a>
                            </div>
                        </div>
                    @else
                        <div class="doc-preview" style="border-color: #fee2e2; background: #fffafb;">
                            <i class="feather icon-alert-circle text-danger" style="font-size: 40px; display: block; margin-bottom: 15px;"></i>
                            <h5 class="font-weight-bold text-danger">اسکن قرارداد آپلود نشده</h5>
                            <p class="small text-muted mb-3">لطفاً برای تکمیل دوسیه، اسکن قرارداد را آپلود کنید.</p>
                            <button class="btn btn-danger btn-sm rounded-lg px-4"><i class="feather icon-upload-cloud mr-1"></i> آپلود فایل</button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- QUICK SHORTCUTS -->
            <div class="row">
                <div class="col-md-6">
                    <a href="/dashboard/agent-payments/{{$agent->agent_id}}" class="stat-card d-block text-decoration-none mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="stat-label">مالی</span>
                                <h5 class="mb-0 font-weight-bold text-dark">صورت حساب کل</h5>
                            </div>
                            <i class="feather icon-credit-card text-primary" style="font-size: 24px;"></i>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="/dashboard/agent-carpet/{{$agent->agent_id}}" class="stat-card d-block text-decoration-none mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="stat-label">انبار</span>
                                <h5 class="mb-0 font-weight-bold text-dark">قالین های موجود</h5>
                            </div>
                            <i class="feather icon-package text-warning" style="font-size: 24px;"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: IDENTITY & CONTACTS -->
        <div class="col-lg-5">
            <div class="glass-section">
                <div class="section-header">
                    <i class="feather icon-user text-primary"></i> معلومات هویت نماینده
                </div>
                <div class="section-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>نام پدر</label>
                            <span>{{ $agent->agent_father_name }}</span>
                        </div>
                        <div class="info-item">
                            <label>نمبر تذکره</label>
                            <span>{{ $agent->national_id }}</span>
                        </div>
                        <div class="info-item">
                            <label>ایمیل آدرس</label>
                            <span class="text-primary">{{ $agent->user->email }}</span>
                        </div>
                        <div class="info-item">
                            <label>ولایت</label>
                            <span>{{ $agent->province->province }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="feather icon-phone text-primary"></i> شماره های تماس
                    </div>
                    <button onclick="OpenFormBox()" class="btn btn-sm btn-light-primary rounded-lg add-new-phone"><i class="feather icon-plus"></i> جدید</button>
                </div>
                <div class="section-body">
                    @foreach($agent->phone as $index => $ph)
                        <div class="phone-pill">
                            <div class="d-flex align-items-center gap-3">
                                <i class="feather icon-smartphone text-muted"></i>
                                <span dir="ltr" class="font-weight-bold text-dark">{{ $ph->phone_no }}</span>
                                @if($index == 0) <span class="badge badge-light-primary small">اصلی</span> @endif
                            </div>
                            @if($index > 0)
                                <button class="btn btn-sm text-danger shadow-none p-0" onclick="window.location.href = '/dashboard/agent/phone/{{ $ph->phone_id }}'">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach

                    <div class="phone-form mt-4" style="display: none;">
                        <form action="/dashboard/agent/phone/{{ $agent->agent_id }}" method="post">
                            @csrf
                            <label class="small font-weight-bold">شماره جدید را وارد کنید:</label>
                            <div class="input-group">
                                <input type="text" id="phone_no" name="phone_no" class="form-control form-control-modern" dir="ltr" placeholder="(99) 99-999-999">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-3"><i class="feather icon-save"></i></button>
                                    <button type="button" class="btn btn-light px-3" onclick="CloseFormBox()"><i class="feather icon-x"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-plugins')
  <script type="text/javascript" src="/dsh/inmask/dist/jquery.inputmask.js"></script>
  <script>
      $(document).ready(function () {
          $('#phone_no').inputmask({mask: ['(99) 99-999-999']});
      });

      function OpenFormBox() {
          $('.phone-form').slideDown();
          $('.add-new-phone').fadeOut();
      }

      function CloseFormBox() {
          $('.phone-form').slideUp();
          $('.add-new-phone').fadeIn();
      }
  </script>
@endsection