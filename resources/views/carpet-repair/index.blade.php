@extends('dsh.master')
@section('title', 'مدیریت ترمیم قالین (Kachaee)')
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
        background-color: var(--qbcc-primary, #43a047) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    /* KEBAB MENU OVERRIDES */
    .table-responsive, .glass-card {
        overflow: visible !important;
        padding-bottom: 80px; 
    }
    
    .table-modern tbody tr {
        position: relative;
        z-index: 1;
    }
    
    .table-modern tbody tr:hover {
        z-index: 100;
    }

    .table-modern td {
        overflow: visible !important;
    }

    .dropdown-menu-action {
        position: absolute !important;
        will-change: transform;
        z-index: 999999 !important;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        padding: 8px;
        min-width: 180px;
    }
    .dropdown-menu-action .dropdown-item {
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dropdown-menu-action .dropdown-item:hover {
        background-color: #f1f5f9;
        color: var(--qbcc-primary);
    }
    .dropdown-menu-action .dropdown-item.text-danger:hover {
        background-color: #fef2f2;
        color: #ef4444;
    }
    .kebab-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .kebab-btn:hover, .kebab-btn:focus {
        background: #f1f5f9;
        color: var(--qbcc-primary);
        outline: none;
        box-shadow: none;
    }
    
    /* SEARCH INPUT MODERNIZATION */
    .search-modern .form-control {
        border-radius: 0 8px 8px 0;
        border: 2px solid #e2e8f0;
        border-left: none;
        box-shadow: none;
        padding-left: 15px;
        transition: all 0.2s;
    }
    .search-modern .form-control:focus {
        border-color: var(--qbcc-accent, #3b82f6);
    }
    .search-modern .btn-search {
        border-radius: 8px 0 0 8px;
        background: var(--qbcc-accent, #3b82f6);
        color: white;
        border: none;
        padding: 0 20px;
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
    <!-- Card 1: Pending Pcs -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-blue p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">{{$nonrepaireds->count()}} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
            <div class="stat-card-lbl">قالین‌های آماده ترمیم (Pending Pcs)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-clock-o"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 2: Pending Area -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-indigo p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">{{number_format($nonrepaireds->sum('area'), 2)}} <span style="font-size: 1rem; font-weight: normal;">متر مربع (m²)</span></div>
            <div class="stat-card-lbl">مساحت آماده ترمیم (Pending Area)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-cube"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 3: Completed Pcs -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-green p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">{{$repaireds->count()}} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
            <div class="stat-card-lbl">قالین‌های ترمیم شده (Completed Pcs)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-check-circle"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 4: Completed Area -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card stat-card-teal p-4 h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-card-val">{{number_format($repaireds->sum(function($r) { return $r->carpet->area ?? 0; }), 2)}} <span style="font-size: 1rem; font-weight: normal;">متر مربع (m²)</span></div>
            <div class="stat-card-lbl">مساحت ترمیم شده (Completed Area)</div>
          </div>
          <div class="stat-card-icon">
            <i class="fa fa-expand"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="glass-card">
        <div class="glass-header">
            <h4><i class="fa fa-wrench mr-2 text-primary"></i> بخش ترمیم قالین (Kachaee)</h4>
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
                <a class="nav-link {{$search == null ? 'active' : ''}}" id="pills-non-repaired-tab"
                   data-toggle="pill" href="#non-repaired" role="tab">
                   <i class="fa fa-clock-o mr-1"></i> ترمیم نشده ها (Pending)
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{$search != null ? 'active' : ''}}" id="pills-repaired-tab"
                   data-toggle="pill" href="#repaired" role="tab">
                   <i class="fa fa-check-circle mr-1"></i> ترمیم شده ها (Completed)
                </a>
              </li>
            </ul>
            
            <div class="tab-content" id="pills-tabContent">
              <!-- PENDING REPAIRS -->
              <div class="tab-pane fade {{$search == null ? 'active show' : ''}}" id="non-repaired" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <form action="/dashboard/repair-search" method="POST" class="form-inline search-modern">
                      @csrf
                      <div class="input-group">
                          <input type="text" name="search" placeholder="جستجو شماره قالین..." class="form-control" value="{{ isset($search) ? $search : '' }}" required>
                          <div class="input-group-append">
                              <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                          </div>
                      </div>
                      @if(isset($search))
                          <a href="/dashboard/carpet-repair" class="btn btn-clear py-2 px-3"><i class="fa fa-times ml-1"></i> پاک کردن فیلتر</a>
                      @endif
                    </form>
                    <button class="btn btn-light shadow-sm" style="border-radius: 8px;" onclick="printPage('noneRepairPrint')"><i class="fa fa-print mr-1"></i> Print</button>
                </div>
                
                <div class="table-responsive" style="overflow: visible;" id="noneRepairPrint">
                  <table class="table table-modern text-center" id="dataTable">
                    <thead>
                    <tr>
                      <th>شماره قالین</th>
                      <th>اسم کچایی گر</th>
                      <th>گدام</th>
                      <th>نوعیت</th>
                      <th>طول</th>
                      <th>عرض</th>
                      <th>حاشیه</th>
                      <th>زمینه</th>
                      <th>شماره فرمایش</th>
                      <th class="printTitle text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($nonrepaireds as $nonrepaired)
                      <tr class="ur{{ $nonrepaired->carpet_id }}">
                        <td class="font-weight-bold text-dark">{{$nonrepaired->carpet_no}}</td>
                        <td>{{$nonrepaired->kachaee->name}}</td>
                        <td><span class="badge badge-light border">{{$nonrepaired->warehouse->name ?? 'نامشخص'}}</span></td>
                        <td><span class="badge badge-light border">{{$nonrepaired->type->carpet_type}}</span></td>
                        <td style="direction: ltr;">{{$nonrepaired->height}}</td>
                        <td style="direction: ltr;">{{$nonrepaired->width}}</td>
                        <td>{{$nonrepaired->margin}}</td>
                        <td>{{$nonrepaired->field}}</td>
                        <td>
                            @if($nonrepaired->carpet_order)
                              <span class="badge badge-info">{{$nonrepaired->carpet_order->order_number}}</span>
                            @else
                              <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="hideOnPrint text-center">
                            <!-- KEBAB MENU -->
                            <div class="dropdown">
                                <button class="kebab-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="viewport">
                                    <i class="feather icon-more-vertical" style="font-size: 1.2rem;"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-action">
                                    <h6 class="dropdown-header text-muted text-right">عملیات ترمیم</h6>
                                    <a class="dropdown-item text-right" href="/dashboard/carpet-repair-create/{{$nonrepaired->carpet_id}}">
                                        <i class="feather icon-tool text-primary"></i>
                                        ثبت ترمیم (Repair)
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-right text-warning btn-return-to-center" 
                                       href="javascript:void(0)" 
                                       data-carpet-id="{{$nonrepaired->carpet_id}}" 
                                       data-carpet-no="{{$nonrepaired->carpet_no}}" 
                                       data-current-warehouse-id="{{$nonrepaired->warehouse_id}}"
                                       data-action-url="/dashboard/return-to-center-from-non-repair/{{$nonrepaired->carpet_id}}">
                                        <i class="feather icon-corner-up-right"></i>
                                        بازگشت به مرکزی
                                    </a>
                                </div>
                            </div>
                        </td>
                      </tr>
                    @endforeach
                    <tr style="background: #f8fafc;" class="font-weight-bold">
                      <td colspan="4" class="text-right">مجموع:</td>
                      <td colspan="5" class="text-left" style="direction: ltr;">
                          {{$nonrepaireds->count()}} pcs | {{number_format($nonrepaireds->sum('area'), 2)}} m<sup>2</sup>
                      </td>
                      <td class="hideOnPrint"></td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <!-- COMPLETED REPAIRS -->
              <div class="tab-pane fade {{$search != null ? 'active show' : ''}}" id="repaired" role="tabpanel">
                <div class="row align-items-center mb-4 search-modern">
                    <div class="col-md-5">
                        <form action="/dashboard/search-repaired" method="POST" class="form-inline">
                          @csrf
                          <div class="input-group w-75">
                              <input type="text" name="search" placeholder="جستجوی سریع..." class="form-control" value="{{ isset($search) ? $search : '' }}" required>
                              <div class="input-group-append">
                                  <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                              </div>
                          </div>
                          @if(isset($search))
                              <a href="/dashboard/carpet-repair" class="btn btn-clear py-2 px-3"><i class="fa fa-times ml-1"></i> پاک</a>
                          @endif
                        </form>
                    </div>
                    <div class="col-md-7 text-right">
                        <form action="/dashboard/repair-date-search" method="POST" class="form-inline justify-content-end">
                          @csrf
                          <div class="input-group input-group-sm mr-2">
                              <div class="input-group-prepend"><span class="input-group-text bg-light" style="border-radius: 0 6px 6px 0; border: 1px solid #e2e8f0;">از</span></div>
                              <input type="date" name="start" class="form-control" style="border-radius: 6px 0 0 6px; border: 1px solid #e2e8f0;" required>
                          </div>
                          <div class="input-group input-group-sm mr-2">
                              <div class="input-group-prepend"><span class="input-group-text bg-light" style="border-radius: 0 6px 6px 0; border: 1px solid #e2e8f0;">تا</span></div>
                              <input type="date" name="end" class="form-control" style="border-radius: 6px 0 0 6px; border: 1px solid #e2e8f0;" required>
                          </div>
                          <button type="submit" class="btn btn-sm btn-info py-1 px-3" style="border-radius: 6px;"><i class="fa fa-filter"></i> فیلتر تاریخ</button>
                          
                          @if(isset($search))
                              <a href="/dashboard/carpet-repair" class="btn btn-sm btn-clear py-1 px-2 ml-2"><i class="fa fa-times"></i></a>
                          @endif
                          
                          <button type="button" class="btn btn-sm btn-light ml-2 border" style="border-radius: 6px;" onclick="printPage('repairPrint')"><i class="fa fa-print text-primary"></i></button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive" style="overflow: visible;" id="repairPrint">
                  <table class="table table-modern text-center" id="secondDataTable">
                    <thead>
                    <tr>
                      <th>شماره قالین</th>
                      <th>نمبر کچایی</th>
                      <th>نوعیت</th>
                      <th>گدام</th>
                      <th>حاشیه</th>
                      <th>زمینه</th>
                      <th>قیمت فی متر</th>
                      <th>قیمت مجموع</th>
                      <th>تاریخ</th>
                      <th>تیم ترمیم کننده</th>
                      <th>شرح</th>
                      <th class="printTitle text-center">وضعیت / عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($repaireds as $repaired)
                      <tr class="ur{{ $repaired->id }}">
                        <td class="font-weight-bold text-dark">{{$repaired->carpet->carpet_no}}</td>
                        <td>
                          <a href="/dashboard/carpet-repair/search-kachaee-number/{{$repaired->kachaee_number}}{{$repaired->team_id}}" class="badge badge-light border">
                          {{$repaired->kachaee_number}}</a>
                        </td>
                        <td>{{$repaired->carpet->type->carpet_type}}</td>
                        <td><span class="badge badge-light border">{{$repaired->carpet->warehouse->name ?? 'نامشخص'}}</span></td>
                        <td>{{$repaired->carpet->margin}}</td>
                        <td>{{$repaired->carpet->field}}</td>
                        
                        <td style="direction: ltr" class="text-muted">{{ number_format($repaired->price, 2) }}</td>
                        <td style="direction: ltr" class="font-weight-bold text-primary">{{ number_format($repaired->af_total_price, 2) }}</td>
                        <td>{{$repaired->date}}</td>
                        <td>{{$repaired->team->name}}</td>
                        <td><small>{{ Str::limit($repaired->description, 20) }}</small></td>
                        
                        <td class="hideOnPrint text-center">
                             <!-- KEBAB MENU -->
                            <div class="dropdown">
                                <button class="kebab-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="viewport">
                                    <i class="feather icon-more-vertical" style="font-size: 1.2rem;"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-action">
                                    <h6 class="dropdown-header text-muted text-right">عملیات</h6>
                                    
                                    <a class="dropdown-item text-right" href="/dashboard/carpet-repair/{{$repaired->id}}">
                                        <i class="feather icon-eye text-info"></i>
                                        نمایش جزئیات (View)
                                    </a>
                                    
                                    <a class="dropdown-item text-right" href="/dashboard/carpet-repair/{{$repaired->id}}/edit">
                                        <i class="feather icon-edit-2 text-warning"></i>
                                        ویرایش مالی (Edit)
                                    </a>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    @if ($repaired->carpet->status == 12)
                                      <a class="dropdown-item text-right text-success font-weight-bold" href="/dashboard/washing-team/sending-to-washing/{{$repaired->carpet->carpet_id}}">
                                          <i class="feather icon-send"></i>
                                          ارسال به شست (Send to Wash)
                                      </a>
                                      <a class="dropdown-item text-right text-danger btn-return-to-center" 
                                         href="javascript:void(0)" 
                                         data-carpet-id="{{$repaired->carpetId}}" 
                                         data-carpet-no="{{$repaired->carpet->carpet_no}}" 
                                         data-current-warehouse-id="{{$repaired->carpet->warehouse_id}}"
                                         data-action-url="/dashboard/return-to-center-from-repair/{{$repaired->carpetId}}">
                                          <i class="feather icon-corner-up-right"></i>
                                          بازگشت به مرکزی
                                      </a>
                                    @else
                                      <div class="dropdown-item text-right text-muted" style="cursor: not-allowed; opacity: 0.6;">
                                          <i class="feather icon-lock"></i>
                                          @if($repaired->carpet->status == 3)
                                              در شست نشده ها
                                          @elseif($repaired->carpet->status == 13)
                                              در شست شده ها
                                          @elseif($repaired->carpet->status == 4)
                                              در بخش تیاری
                                          @elseif($repaired->carpet->status == 5)
                                              در گدام فروشات
                                          @elseif($repaired->carpet->status == 6)
                                              فروخته شده
                                          @else
                                              قفل شده
                                          @endif
                                      </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                      </tr>
                    @endforeach
                    <tr style="background: #f8fafc;" class="font-weight-bold">
                      <td colspan="4" class="text-right">مجموع:</td>
                      <td colspan="7" class="text-left" style="direction: ltr;">
                          {{$repaireds->count()}} pcs | {{number_format($repaireds->sum(function($r) { return $r->carpet->area ?? 0; }), 2)}} m<sup>2</sup>
                      </td>
                      <td class="hideOnPrint"></td>
                    </tr>
                    </tbody>
                  </table>
                  @if(!isset($search))
                    <div class="mt-3">
                        {{$repaireds->links()}}
                    </div>
                  @endif
                </div>
              </div>
  <!-- Return to Central Confirmation Modal -->
  <div class="modal fade" id="returnToCenterModal" tabindex="-1" role="dialog" aria-labelledby="returnToCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
        <form id="returnToCenterForm" method="POST" action="">
          @csrf
          <div class="modal-header bg-warning text-dark" style="border-radius: 16px 16px 0 0; border: none; padding: 15px 20px;">
            <h5 class="modal-title font-weight-bold" id="returnToCenterModalLabel">
              <i class="fa fa-exclamation-triangle mr-2"></i> تأیید بازگشت به دفتر مرکزی
            </h5>
            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4 text-right" style="direction: rtl;">
            <p class="font-weight-bold text-dark mb-3">آیا مطمئن هستید که می‌خواهید قالین شماره <span id="modalCarpetNo" class="text-danger font-weight-bold"></span> را به دفتر مرکزی بازگشت دهید؟</p>
            
            <div class="form-group">
              <label for="modalWarehouseId" class="font-weight-bold text-muted">انتخاب گدام جهت بازگشت:</label>
              <select name="warehouse_id" id="modalWarehouseId" class="form-control" style="border-radius: 8px; height: auto; padding: 10px;" required>
                @foreach($warehouses as $wh)
                  <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px; border: none; padding: 15px 20px;">
            <button type="button" class="btn btn-secondary px-4" style="border-radius: 8px;" data-dismiss="modal">انصراف</button>
            <button type="submit" class="btn btn-warning px-4 text-dark font-weight-bold" style="border-radius: 8px;">تأیید و بازگشت</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @section('scripts')
  <script>
      $(document).ready(function() {
          $('.btn-return-to-center').on('click', function(e) {
              e.preventDefault();
              var carpetId = $(this).data('carpet-id');
              var carpetNo = $(this).data('carpet-no');
              var currentWarehouseId = $(this).data('current-warehouse-id');
              var actionUrl = $(this).data('action-url');
              
              // Set carpet number in the text
              $('#modalCarpetNo').text(carpetNo);
              
              // Set action URL on the form
              $('#returnToCenterForm').attr('action', actionUrl);
              
              // Select the current warehouse as default
              if (currentWarehouseId) {
                  $('#modalWarehouseId').val(currentWarehouseId);
              }
              
              // Show modal
              $('#returnToCenterModal').modal('show');
          });
      });
  </script>
  @endsection
@endsection