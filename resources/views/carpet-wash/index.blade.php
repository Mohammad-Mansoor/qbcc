@extends('dsh.master')

@section('content')

@php
  $all_washes = \App\CarpetWash::with('carpet')->get();
  
  $pending_washes = $all_washes->filter(function($w) {
      return $w->carpet && $w->carpet->status == 3;
  });
  $pending_pcs = $pending_washes->count();
  $pending_area = $pending_washes->sum(function($w) {
      return $w->area ?: ($w->carpet->area ?? 0);
  });

  $completed_washes = $all_washes->filter(function($w) {
      return $w->carpet && $w->carpet->status != 3;
  });
  $completed_pcs = $completed_washes->count();
  $completed_area = $completed_washes->sum(function($w) {
      return $w->area ?: ($w->carpet->area ?? 0);
  });
  $completed_cost = $completed_washes->sum('total_price');
@endphp

<style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .glass-header {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid var(--qbcc-border, #e2e8f0);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .glass-header h4 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 1.2rem;
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

    .nav-pills .nav-link {
        border-radius: 8px;
        font-weight: 600;
        color: #64748b;
        padding: 10px 20px;
        transition: all 0.2s;
    }
    .nav-pills .nav-link.active {
        background-color: var(--qbcc-primary, #3b82f6) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

    /* SEARCH INPUT MODERNIZATION */
    .search-modern .form-control {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        box-shadow: none;
        padding: 10px 15px;
        transition: all 0.2s;
    }
    .search-modern .form-control:focus {
        border-color: var(--qbcc-accent, #3b82f6);
    }
    .search-modern .btn-search {
        border-radius: 8px;
        background: var(--qbcc-accent, #3b82f6);
        color: white;
        border: none;
        padding: 8px 20px;
    }
    .search-modern .btn-search:hover {
        background: #2563eb;
    }
    .search-modern .btn-clear {
        border-radius: 8px;
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        margin-right: 10px;
        transition: all 0.2s;
    }
    .search-modern .btn-clear:hover {
        background: #fee2e2;
        color: #dc2626;
    }
</style>

<!-- ANIMATED STATS CARDS ROW -->
<div class="row mb-4">
  <!-- Card 1: Pending Pcs -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-blue p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{$pending_pcs}} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
          <div class="stat-card-lbl">قالین‌های آماده شست (Pending Washes)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fas fa-clock"></i>
        </div>
      </div>
    </div>
  </div>
  <!-- Card 2: Pending Area -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-indigo p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{number_format($pending_area, 2)}} <span style="font-size: 1rem; font-weight: normal;">متر مربع (m²)</span></div>
          <div class="stat-card-lbl">مساحت آماده شست (Pending Area)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-expand"></i>
        </div>
      </div>
    </div>
  </div>
  <!-- Card 3: Completed Pcs -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-green p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{$completed_pcs}} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
          <div class="stat-card-lbl">قالین‌های شسته شده (Washed Pcs)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-check-circle"></i>
        </div>
      </div>
    </div>
  </div>
  <!-- Card 4: Total Wash Cost -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-teal p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">${{number_format($completed_cost, 2)}}</div>
          <div class="stat-card-lbl">هزینه کل شست (Total Wash Cost)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fas fa-money-bill"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="glass-card">
      <div class="glass-header">
          <h4><i class="fa fa-tint mr-2 text-primary"></i> بخش شستشوی قالین (Carpet Wash)</h4>
          <div>
              @if(session("status"))
                <span class="badge badge-success px-3 py-2"><i class="fa fa-check mr-1"></i> {{session('status')}}</span>
              @endif
              @if(session("error"))
                <span class="badge badge-danger px-3 py-2"><i class="fa fa-times mr-1"></i> {{session('error')}}</span>
              @endif
          </div>
      </div>
      
      <div class="card-body p-4">
        <ul class="nav nav-pills mb-4 pb-3 border-bottom" id="pills-tab" role="tablist">
          <li class="nav-item mr-2">
            <a class="nav-link {{$wash_check == 1 ? 'active' : ''}}" id="pills-non-washed-tab"
               data-toggle="pill" href="#non-washed" role="tab">
               <i class="fa fa-list mr-1"></i> لیست شست نمبر ها (Wash Bills)
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{$wash_check == 2 ? 'active' : ''}}" id="pills-washed-tab"
               data-toggle="pill" href="#washed" role="tab">
               <i class="fa fa-check-circle mr-1"></i> لیست شسته شده و نشسته (Wash Registry)
            </a>
          </li>
        </ul>
        
        <div class="tab-content" id="pills-tabContent">
          
          <!-- TAB 1: WASH BILLS -->
          <div class="tab-pane fade {{$wash_check == 1 ? 'active show' : ''}}" id="non-washed" role="tabpanel"
               aria-labelledby="pills-non-washed-tab">
            <div class="row align-items-center mb-4">
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <form action="/dashboard/carpet-wash/search" method="POST" class="form-inline search-modern">
                  @csrf
                  <input type="hidden" name="from_non_washed" value="from non washed">
                  <div class="form-group mr-2 mb-0">
                    <span class="mr-2 font-weight-bold">نام کاریگر (شست گر):</span>
                    <select name="team_id" id="team_id" class="form-control" style="min-width: 200px;" required>
                      @foreach($team as $t)
                        <option value="{{$t->id}}">{{$t->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <button type="submit" class="btn btn-search ml-2"><i class="fa fa-search"></i> جستجو</button>
                </form>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-left">
                <button class="btn btn-light border shadow-sm" onclick="printPage('noneRepairPrint')"><i class="fa fa-print mr-1"></i> Print</button>
              </div>
            </div>
            
            <div class="table-responsive" style="overflow: visible;" id="noneRepairPrint">
              <table class="table table-modern text-center">
                <thead>
                <tr>
                  <th>شست گر</th>
                  <th>شماره تماس</th>
                  <th>آدرس</th>
                  <th class="hideOnPrint">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($team as $t)
                  @if($t->carpet_wash->count() > 0)
                    <tr class="ur{{ $t->id }}">
                      <td class="font-weight-bold text-dark">{{$t->name}}</td>
                      <td>{{$t->contact_no ?: '-'}}</td>
                      <td>{{$t->address ?: '-'}}</td>
                      <td class="hideOnPrint">
                        <a class="btn btn-primary btn-sm px-3" style="border-radius: 8px;"
                           href="/dashboard/carpet-wash/wash-numbers/{{$t->id}}">
                          <i class="fa fa-list mr-1"></i> لیست شست نمبر
                        </a>
                      </td>
                    </tr>
                  @endif
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-4">هنوز موردی ثبت نشده است</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
          
          <!-- TAB 2: WASH REGISTRY -->
          <div class="tab-pane fade {{$wash_check == 2 ? 'active show' : ''}}" id="washed" role="tabpanel"
               aria-labelledby="pills-washed-tab">
            
            <div class="row align-items-center mb-4 search-modern">
              <!-- Text Search -->
              <div class="col-md-4 mb-2 mb-md-0">
                <form action="/dashboard/carpet-wash/search" method="POST" class="w-100">
                  @csrf
                  <input type="hidden" name="from_washed" value="from washed">
                  <div class="input-group">
                    <input type="text" name="search" placeholder="جستجو شماره قالین، شست نمبر، کاریگر..." class="form-control" value="{{ request('search') }}">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- Carpet Type Filter -->
              <div class="col-md-4 mb-2 mb-md-0">
                <form action="/dashboard/carpet-wash/search-carpet-type" method="POST" class="w-100">
                  @csrf
                  <div class="d-flex align-items-center">
                    <span class="mr-2 font-weight-bold text-nowrap">نوعیت قالین:</span>
                    <?php $carpet_types = \App\CarpetType::all(); ?>
                    <select name="carpet_type_id" id="carpet_type_id" class="form-control" onchange="this.form.submit()">
                      <option value="">همه نوعیت ها</option>
                      @foreach($carpet_types as $type)
                        <option value="{{$type->carpet_type_id}}" {{ request('carpet_type_id') == $type->carpet_type_id ? 'selected' : '' }}>{{$type->carpet_type}}</option>
                      @endforeach
                    </select>
                  </div>
                </form>
              </div>
              <!-- Reset & Print -->
              <div class="col-md-4 text-left">
                @if(request('search') || request('carpet_type_id'))
                  <a href="/dashboard/carpet-wash" class="btn btn-clear py-2 px-3"><i class="fa fa-times ml-1"></i> پاک کردن فیلتر</a>
                @endif
                <button class="btn btn-light border shadow-sm" onclick="printPage('repairPrint')"><i class="fa fa-print mr-1"></i> Print</button>
              </div>
            </div>
            
            <div class="table-responsive" style="overflow: visible;" id="repairPrint">
              <table class="table table-modern text-center">
                <thead>
                <tr>
                  <th>شماره قالین</th>
                  <th>نوعیت قالین</th>
                  <th>شست نمبر (مرکزی)</th>
                  <th>شست نمبر (فروشات)</th>
                  <th>قیمت فی متر</th>
                  <th>قیمت مجموع</th>
                  <th>تاریخ شست</th>
                  <th>تیم شوینده</th>
                  <th>شرح</th>
                  <th class="printTitle">وضعیت</th>
                  <th class="printTitle">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($washeds as $washed)
                  @if($washed->carpet)
                    <tr class="ur{{ $washed->id }}">
                      <td class="font-weight-bold text-dark">{{$washed->carpet->carpet_no}}</td>
                      <td><span class="badge badge-light border">{{$washed->carpet->type->carpet_type ?? '-'}}</span></td>
                      <td><span class="badge badge-light border">{{$washed->wash_number}}</span></td>
                      <td>
                        <a href="/dashboard/search-wash-numbersh-payment/{{$washed->wash_number_sh}},{{$washed->team_id}}" class="font-weight-bold" style="color: #3b82f6;">
                          {{$washed->wash_number_sh}}
                        </a>
                      </td>
                      <td style="direction: ltr">${{ number_format($washed->price, 2) }}</td>
                      <td style="direction: ltr">
                        <span class="font-weight-bold text-success">{{ number_format($washed->af_total_price, 2) }} {{ $washed->currency_code ?: 'USD' }}</span>
                        @if($washed->currency_code && $washed->currency_code != 'USD')
                          <br><small class="text-muted">({{ number_format($washed->total_price, 2) }} USD)</small>
                        @endif
                      </td>
                      <td>{{$washed->date}}</td>
                      <td>{{$washed->washing_team->name ?? '-'}}</td>
                      <td><small class="text-muted" title="{{ $washed->description }}">{{ Str::limit($washed->description, 20) }}</small></td>
                      
                      <!-- Status Badge -->
                      <td>
                        @if($washed->carpet->status == 13)
                          <span class="badge badge-success px-2 py-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">شسته شده</span>
                        @elseif($washed->carpet->status == 0)
                          <span class="badge badge-success px-2 py-1" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">در نزد نماینده</span>
                        @elseif($washed->carpet->status == 1)
                          <span class="badge badge-warning px-2 py-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">در گدام مرکزی</span>
                        @elseif($washed->carpet->status == 2)
                          <span class="badge badge-danger px-2 py-1" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">در کچایی نشده ها</span>
                        @elseif($washed->carpet->status == 12)
                          <span class="badge badge-primary px-2 py-1" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">در کچایی شده ها</span>
                        @elseif($washed->carpet->status == 3)
                          <span class="badge badge-warning px-2 py-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">در شست نشده ها</span>
                        @elseif($washed->carpet->status == 4)
                          <span class="badge badge-info px-2 py-1" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">در بخش تیاری</span>
                        @elseif($washed->carpet->status == 5)
                          <span class="badge badge-dark px-2 py-1" style="background: rgba(52, 58, 64, 0.1); color: #343a40;">در گدام فروشات</span>
                        @elseif($washed->carpet->status == 6)
                          <span class="badge badge-success px-2 py-1" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">فروخته شده</span>
                        @endif
                      </td>

                      <!-- Actions -->
                      <td class="hideOnPrint">
                        <div class="d-flex justify-content-center align-items-center">
                          <a href="/dashboard/carpet-wash/{{$washed->id}}/edit"
                             class="btn btn-sm btn-outline-primary mr-1" title="ویرایش">
                            <i class="fa fa-pencil-alt" style="color:#3b82f6;"></i>
                          </a>

                          @if ($washed->carpet->status == 13)
                            <button type="button"
                               class="btn btn-sm btn-outline-success mr-1 btn-confirm-action"
                               title="ارسال به بخش تیاری"
                               data-action-url="/dashboard/carpet-wash/sent-to-finish/{{$washed->carpet->carpet_id}}"
                               data-carpet-no="{{$washed->carpet->carpet_no}}"
                               data-action-label="ارسال به بخش تیاری"
                               data-action-sublabel="Send to Finishing Section"
                               data-icon="fa fa-paper-plane"
                               data-btn-class="success"
                               data-needs-warehouse="1">
                              <i class="fa fa-paper-plane" style="color:#10b981;"></i>
                            </button>
                          @endif

                          @if($washed->carpet->status == 13 || $washed->carpet->status == 3)
                            @if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO')
                              <?php $kachaee = \App\CarpetRepair::where('carpetId', $washed->carpetId)->first(); ?>
                              @if($kachaee)
                                <button type="button"
                                   class="btn btn-sm btn-outline-warning mr-1 btn-confirm-action"
                                   title="بازگشت به کچایی"
                                   data-action-url="/dashboard/carpet-wash/return-to-kachaee/{{$washed->id}}"
                                   data-carpet-no="{{$washed->carpet->carpet_no}}"
                                   data-action-label="بازگشت به کچایی"
                                   data-action-sublabel="Return to Kachaee (Repair)"
                                   data-icon="fa fa-reply"
                                   data-btn-class="warning"
                                   data-needs-warehouse="1">
                                  <i class="fa fa-reply" style="color:#d97706;"></i>
                                </button>
                              @else
                                <button type="button"
                                   class="btn btn-sm btn-outline-danger mr-1 btn-confirm-action"
                                   title="بازگشت به مرکزی"
                                   data-action-url="/dashboard/carpet-wash/return-to-center/{{$washed->id}}"
                                   data-carpet-no="{{$washed->carpet->carpet_no}}"
                                   data-action-label="بازگشت به مرکزی"
                                   data-action-sublabel="Return to Central Warehouse"
                                   data-icon="fa fa-reply-all"
                                   data-btn-class="danger"
                                   data-needs-warehouse="0">
                                  <i class="fa fa-reply-all" style="color:#dc2626;"></i>
                                </button>
                              @endif
                            @endif
                          @endif
                        </div>
                      </td>
                    </tr>
                  @endif
                @endforeach
                
                <tr style="background: #f8fafc;" class="font-weight-bold">
                  <td colspan="4" class="text-right">مجموع:</td>
                  <td style="direction: ltr">${{ number_format($washeds->sum('total_price'), 2) }}</td>
                  <td style="direction: ltr">{{$washeds->sum('area')}} m<sup>2</sup></td>
                  <td>{{$washeds->count()}} قالین</td>
                  <td class="hideOnPrint"></td>
                  <td class="hideOnPrint"></td>
                  <td></td>
                  <td></td>
                </tr>
                </tbody>
              </table>
              @if($wash_check != 2)
                <div class="mt-3">
                  {{$washeds->links()}}
                </div>
              @endif
            </div>
            
          </div>
          
        </div>
      </div>
      
    </div>
  </div>
</div>

{{-- ============================================================
     CONFIRMATION MODAL — shared for all status-changing actions
     ============================================================ --}}
<div class="modal fade" id="confirmActionModal" tabindex="-1" role="dialog"
     aria-labelledby="confirmActionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
    <div class="modal-content" style="border: none; border-radius: 20px; overflow: hidden;
         box-shadow: 0 25px 60px rgba(0,0,0,0.18);">

      {{-- Header --}}
      <div class="modal-header" id="confirmModalHeader"
           style="padding: 24px 28px 16px; border-bottom: none;">
        <div class="d-flex align-items-center">
          <div id="confirmModalIconWrap"
               style="width:52px; height:52px; border-radius:14px; display:flex;
                      align-items:center; justify-content:center; font-size:1.4rem;
                      margin-left:16px; flex-shrink:0;">
            <i id="confirmModalIcon"></i>
          </div>
          <div>
            <h5 class="mb-0 font-weight-bold" id="confirmModalTitle"
                style="font-size:1.1rem; color:#1e293b;">تأیید عملیات</h5>
            <small id="confirmModalSubtitle" class="text-muted">لطفاً قبل از ادامه مطالعه کنید</small>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                style="position:absolute; top:16px; left:20px; font-size:1.4rem; color:#94a3b8;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      {{-- Body --}}
      <div class="modal-body" style="padding: 8px 28px 24px;">

        {{-- Info card --}}
        <div style="background:#f8fafc; border-radius:12px; padding:18px 20px;
                    border: 1px solid #e2e8f0; margin-bottom: 16px;">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:.85rem;">شماره قالین</span>
            <span class="font-weight-bold" id="confirmModalCarpetNo"
                  style="font-size:1rem; color:#1e293b; letter-spacing:.5px;"></span>
          </div>
          <hr style="border-color:#e2e8f0; margin: 10px 0;">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:.85rem;">عملیات</span>
            <span id="confirmModalActionName" style="font-size:.9rem; font-weight:600;"></span>
          </div>
        </div>

        {{-- ► Warehouse selector (only shown for Return-to-Kachaee) --}}
        <div id="confirmModalWarehouseWrap" style="display:none; margin-bottom:16px;">
          <label style="font-size:.85rem; font-weight:700; color:#374151; margin-bottom:6px; display:block;">
            <i class="fa fa-building-o mr-1" style="color:#f59e0b;"></i>
            انتخاب انبار مقصد (Destination Warehouse)
            <span class="text-danger">*</span>
          </label>
          <select id="confirmModalWarehouseSelect" name="warehouse_id"
                  style="width:100%; border-radius:10px; border:2px solid #e2e8f0;
                         padding:10px 14px; font-size:.9rem; color:#1e293b;
                         background:#fff; appearance:none; outline:none;
                         transition: border-color .2s;">
            <option value="">— انبار را انتخاب کنید —</option>
            @foreach(\App\Warehouse::where('is_active', true)->get() as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }}@if($wh->location) — {{ $wh->location }}@endif</option>
            @endforeach
          </select>
          <small class="text-muted" style="font-size:.78rem; margin-top:4px; display:block;">
            قالین پس از بازگشت به کچایی در این انبار ثبت خواهد شد.
          </small>
        </div>

        <p class="text-muted mb-0" style="font-size:.85rem; line-height:1.7;">
          آیا مطمئن هستید؟ این عملیات وضعیت قالین را تغییر می‌دهد
          و نمی‌توان به راحتی آن را بازگرداند.
          <br><em style="font-size:.8rem;">
            Are you sure? This will change the carpet status and may not be easily reversible.
          </em>
        </p>
      </div>

      {{-- Footer: hidden POST form + buttons --}}
      <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 28px 20px;
           background:#fcfcfd; border-radius: 0 0 20px 20px;">

        {{-- Hidden form used when action needs POST (e.g. return-to-kachaee with warehouse) --}}
        <form id="confirmModalPostForm" method="POST" action="" style="display:none;">
          @csrf
          <input type="hidden" id="confirmModalPostWarehouseId" name="warehouse_id" value="">
        </form>

        <button type="button" class="btn btn-light" data-dismiss="modal"
                style="border-radius:10px; padding:9px 22px; font-weight:600;
                       border: 1px solid #e2e8f0; color:#64748b;">
          <i class="fa fa-times mr-1"></i> انصراف
        </button>

        {{-- GET link (default: send-to-finish, return-to-center) --}}
        <a href="#" id="confirmModalProceedBtn" class="btn btn-success"
           style="border-radius:10px; padding:9px 24px; font-weight:600;
                  box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width:140px;">
          <i id="confirmModalBtnIcon" class="fa fa-check mr-1"></i>
          <span id="confirmModalBtnLabel">تأیید</span>
        </a>

        {{-- POST submit (return-to-kachaee only) --}}
        <button type="button" id="confirmModalPostBtn" class="btn btn-warning"
                style="display:none; border-radius:10px; padding:9px 24px; font-weight:600;
                       min-width:140px;">
          <i id="confirmModalPostBtnIcon" class="fa fa-reply mr-1"></i>
          <span id="confirmModalPostBtnLabel">تأیید</span>
        </button>
      </div>

    </div>
  </div>
</div>

@endsection
@section('scripts')
  <script>
    $('#carpet_type_id').select2();
    $('#team_id').select2();

    /* ── Confirmation Modal Logic ── */
    var colorMap = {
      success : { bg: 'rgba(16,185,129,.12)', color: '#10b981', btn: 'success' },
      warning : { bg: 'rgba(245,158,11,.12)',  color: '#f59e0b', btn: 'warning' },
      danger  : { bg: 'rgba(239,68,68,.12)',   color: '#ef4444', btn: 'danger'  }
    };

    $(document).on('click', '.btn-confirm-action', function () {
      var url           = $(this).data('action-url');
      var carpetNo      = $(this).data('carpet-no');
      var label         = $(this).data('action-label');
      var sublabel      = $(this).data('action-sublabel');
      var icon          = $(this).data('icon');
      var btnClass      = $(this).data('btn-class');
      var needsWarehouse = parseInt($(this).data('needs-warehouse') || 0);
      var palette       = colorMap[btnClass] || colorMap['success'];

      /* populate modal header */
      $('#confirmModalIconWrap').css({ background: palette.bg, color: palette.color });
      $('#confirmModalIcon').attr('class', icon);
      $('#confirmModalHeader').css('border-left', '4px solid ' + palette.color);
      $('#confirmModalTitle').text(label);
      $('#confirmModalSubtitle').text(sublabel);
      $('#confirmModalCarpetNo').text(carpetNo);
      $('#confirmModalActionName').text(label).css('color', palette.color);

      if (needsWarehouse) {
        /* ── KACHAEE RETURN & TAYAARI: show warehouse selector, use POST form ── */
        $('#confirmModalWarehouseWrap').show();
        $('#confirmModalWarehouseSelect').val('');
        $('#confirmModalPostForm').attr('action', url);
        $('#confirmModalPostWarehouseId').val('');

        /* Dynamically update helper text based on action */
        if (url.indexOf('sent-to-finish') !== -1) {
          $('#confirmModalWarehouseWrap small').text('قالین پس از ارسال به بخش تیاری در این انبار ثبت خواهد شد.');
        } else {
          $('#confirmModalWarehouseWrap small').text('قالین پس از بازگشت به کچایی در این انبار ثبت خواهد شد.');
        }

        /* hide GET button, show POST button */
        $('#confirmModalProceedBtn').hide();
        $('#confirmModalPostBtn')
          .show()
          .removeClass('btn-success btn-warning btn-danger')
          .addClass('btn-' + palette.btn);
        $('#confirmModalPostBtnIcon').attr('class', icon + ' mr-1');
        $('#confirmModalPostBtnLabel').text(label);
      } else {
        /* ── OTHER ACTIONS: simple GET link ── */
        $('#confirmModalWarehouseWrap').hide();
        $('#confirmModalProceedBtn')
          .show()
          .attr('href', url)
          .removeClass('btn-success btn-warning btn-danger')
          .addClass('btn-' + palette.btn)
          .css('box-shadow', '0 4px 12px ' + palette.bg);
        $('#confirmModalBtnIcon').attr('class', icon + ' mr-1');
        $('#confirmModalBtnLabel').text(label);
        $('#confirmModalPostBtn').hide();
      }

      $('#confirmActionModal').modal('show');
    });

    /* POST form submit — validate warehouse selected first */
    $('#confirmModalPostBtn').on('click', function () {
      var warehouseId = $('#confirmModalWarehouseSelect').val();
      if (!warehouseId) {
        /* highlight the select as required */
        $('#confirmModalWarehouseSelect').css('border-color', '#ef4444');
        $('#confirmModalWarehouseSelect').focus();
        return;
      }
      $('#confirmModalWarehouseSelect').css('border-color', '#e2e8f0');
      $('#confirmModalPostWarehouseId').val(warehouseId);
      $('#confirmModalPostForm').submit();
    });

    /* Clear validation highlight when user picks a warehouse */
    $('#confirmModalWarehouseSelect').on('change', function () {
      if ($(this).val()) {
        $(this).css('border-color', '#10b981');
      }
    });
  </script>
@endsection
