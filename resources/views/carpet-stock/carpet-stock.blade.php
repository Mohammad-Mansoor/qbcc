@extends('dsh.master')
@section('title' , ' گدام قالین')
@section('content')

<style>
  /* Premium Glassmorphism & Custom Elements */
  /* Prevent dropdown clipping in responsive tables */
  .table-responsive,
  .modern-card,
  .modern-table td {
    overflow: visible !important;
  }
  .dropdown-menu {
    position: absolute !important;
    will-change: transform;
    z-index: 999999 !important;
  }
  
  .modern-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06) !important;
    backdrop-filter: blur(12px);
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .modern-card:hover {
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1) !important;
  }
  .modern-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
    padding: 20px 24px !important;
    border-bottom: none !important;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .modern-title {
    color: #ffffff !important;
    font-weight: 700 !important;
    margin: 0 !important;
    font-size: 1.25rem !important;
    letter-spacing: 0.5px;
  }
  .modern-table {
    width: 100%;
    margin-top: 15px;
    border-collapse: separate !important;
    border-spacing: 0 8px !important;
  }
  .modern-table th {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.85rem;
    padding: 16px 12px !important;
    border: none !important;
    text-align: right;
  }
  .modern-table tbody tr {
    background: #ffffff;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.01);
  }
  .modern-table tbody tr:hover {
    background: #f8fafc !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
  }
  .modern-table tbody td {
    padding: 14px 12px !important;
    vertical-align: middle !important;
    border-top: 1px solid #f1f5f9 !important;
    border-bottom: 1px solid #f1f5f9 !important;
    font-size: 0.9rem;
    color: #334155;
  }
  .modern-table tbody td:first-child {
    border-left: 1px solid #f1f5f9 !important;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
  }
  .modern-table tbody td:last-child {
    border-right: 1px solid #f1f5f9 !important;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
  }
  
  /* Modern Luxury Actions Buttons */
  .btn-modern-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer;
  }
  .btn-modern-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12) !important;
    color: #ffffff !important;
    text-decoration: none;
  }
  .btn-modern-action:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
  }
  
  .btn-sell {
    background: linear-gradient(135deg, #11998e, #38ef7d) !important;
  }
  .btn-view-details {
    background: linear-gradient(135deg, #00c6ff, #0072ff) !important;
    color: white !important;
  }
  
  /* Inputs Customization */
  .modern-search {
    border-radius: 10px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 12px 18px !important;
    font-size: 0.9rem !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.01) !important;
    transition: all 0.3s ease !important;
    width: 100%;
  }
  .modern-search:focus {
    border-color: #2a5298 !important;
    box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1) !important;
    outline: none;
  }
  
  /* Status Badges */
  .badge-premium {
      border-radius: 6px;
      padding: 6px 12px;
      font-weight: 600;
      font-size: 0.75rem;
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
  .stat-card-orange {
    background: linear-gradient(135deg, #f59e0b, #d97706);
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

@php
  $page_total_qty = $carpets->count();
  $page_total_area = $carpets->sum('area');
  $page_total_value = $carpets->sum('total_price');
  
  $aging_90_count = 0;
  $aging_180_count = 0;
  
  foreach($carpets as $c) {
      $days = \Carbon\Carbon::parse($c->date)->diffInDays(now());
      if ($days > 180) {
          $aging_180_count++;
      } elseif ($days > 90) {
          $aging_90_count++;
      }
  }
@endphp

<!-- METRICS OVERVIEW ROW -->
<div class="row mb-4 hideOnPrint">
  <!-- Total Quantity -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-blue p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ $carpets->total() }} <span style="font-size: 1rem; font-weight: normal;">تخته</span></div>
          <div class="stat-card-lbl">تعداد کل قالین‌های موجود (Total Stock Qty)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-th-large"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Area -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-green p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ number_format($page_total_area, 2) }} <span style="font-size: 1rem; font-weight: normal;">m²</span></div>
          <div class="stat-card-lbl">مجموع مساحت صفحه جاری (Page Area)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-arrows-alt"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Dollar Value -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-indigo p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">${{ number_format($page_total_value, 2) }}</div>
          <div class="stat-card-lbl">ارزش تمام شده صفحه (Page Valuation)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-dollar"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Aging Alerts -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-orange p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val" style="font-size: 1.4rem;">
            90d+: {{ $aging_90_count }} | 180d+: {{ $aging_180_count }}
          </div>
          <div class="stat-card-lbl">قالین‌های راکد (Aging Stock Index)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-clock-o"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card modern-card">
      <div class="card-header modern-header">
        <h3 class="modern-title"><i class="fa fa-cubes"></i> مدیریت موجودی قالین در گدام (Carpet Stock Registry)</h3>
        
        <div class="d-flex hideOnPrint">
          <a href="{{ request()->fullUrlWithQuery(array_merge(request()->except('_token'), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-sm btn-danger font-weight-bold mr-2" style="border-radius: 8px;">
            <i class="fa fa-file-pdf-o"></i> چاپ / PDF
          </a>
          <a href="{{ request()->fullUrlWithQuery(array_merge(request()->except('_token'), ['export' => 'excel'])) }}" class="btn btn-sm btn-success font-weight-bold mr-2" style="border-radius: 8px;">
            <i class="fa fa-file-excel-o"></i> خروجی اکسل
          </a>
          @if(request()->fullUrl() != url('/dashboard/carpet-stock'))
            <a href="/dashboard/carpet-stock" class="btn btn-sm btn-light font-weight-bold" style="border-radius: 8px; color: #dc3545;">
              <i class="fa fa-times-circle"></i> پاک کردن فیلترها
            </a>
          @endif
        </div>
      </div>
      
      <div class="card-body">
        
        <!-- CONSOLIDATED MODERN FILTERS & SEARCH ROW -->
        <div class="card p-3 mb-4 hideOnPrint" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
          <form action="/dashboard/carpet-stock" method="GET" id="unifiedSearchForm">
            <div class="row align-items-end">
              <!-- 1. Live Instant Search -->
              <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <label class="font-weight-bold small text-muted">🔍 جستجو (شماره/نقشه)</label>
                <input type="text" value="{{ request('search') }}" name="search" id="live-carpet-search"
                       placeholder="نمبر قالین، نقشه..." class="form-control modern-search" style="border-radius: 8px !important;" autocomplete="off">
              </div>
              
              <!-- 2. Filter by Warehouse -->
              <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                <label class="font-weight-bold small text-muted">🏢 گدام</label>
                <select name="warehouse_id" id="warehouse_filter" class="form-control">
                  <option value="">همه گدام ها</option>
                  @foreach($warehouses as $wh)
                    <option value="{{$wh->id}}" {{ (request('warehouse_id') == $wh->id ? 'selected' : '') }}>{{$wh->name}} ({{$wh->location}})</option>
                  @endforeach
                </select>
              </div>

              <!-- 4. Filter by Type -->
              <div class="col-lg-2 col-md-4 mb-3 mb-lg-0">
                <label class="font-weight-bold small text-muted">🎨 نوعیت قالین</label>
                <select name="carpet_type" id="type_id" class="form-control">
                  <option value="">همه نوعیت‌ها</option>
                  @foreach($carpet_types as $ag)
                    <option value="{{$ag->carpet_type_id}}" {{ (request('carpet_type') == $ag->carpet_type_id ? 'selected' : '') }}>{{$ag->carpet_type}}</option>
                  @endforeach
                </select>
              </div>

              <!-- 3. Filter by Date Range & Submit -->
              <div class="col-lg-5 col-md-8 mb-3 mb-lg-0">
                <div class="row no-gutters">
                  <div class="col-4">
                    <label class="font-weight-bold small text-muted">شروع</label>
                    <input type="date" value="{{ request('from_date') ?? (isset($from_date) ? $from_date : '') }}" name="from_date" class="form-control" style="border-radius: 8px 0 0 8px;">
                  </div>
                  <div class="col-4">
                    <label class="font-weight-bold small text-muted">ختم</label>
                    <input type="date" name="to_date" value="{{ request('to_date') ?? (isset($to_date) ? $to_date : '') }}" class="form-control" style="border-radius: 0;">
                  </div>
                  <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 27px; height: 38px; border-radius: 0 8px 8px 0; font-weight: bold;">
                      <i class="fa fa-filter"></i> اعمال فیلترها
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        @if(session("status"))
          <div class="alert alert-success status" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <p class="text-center mb-0">{{session('status')}}</p>
          </div>
        @endif

        <div class="card-body p-0" id="carpet_stock_print">
  
          <div class="static-table-list table-responsive">
            <table class="table modern-table text-center" id="dataTable">
              <thead>
                <tr>
                  <th>شماره قالین</th>
                  <th>نقشه / کوالتی</th>
                  <th>نوعیت</th>
                  <th>ابعاد (m)</th>
                  <th>مساحت</th>
                  <th>زمینه/حاشیه</th>
                  <th>گدام</th>
                  <th>مدت در گدام</th>
                  <th>قیمت تمام شد</th>
                  <th class="hideOnPrint">عملیات</th>
                </tr>
              </thead>
              <tbody>
                @if($carpets->count() > 0)
                  @foreach($carpets as $carpet)
                    @php
                      $daysInStock = \Carbon\Carbon::parse($carpet->date)->diffInDays(now());
                      $agingClass = $daysInStock > 180 ? 'text-danger font-weight-bold' : ($daysInStock > 90 ? 'text-warning font-weight-bold' : 'text-success font-weight-bold');
                    @endphp
                    <tr>
                      <td class="font-weight-bold text-dark">{{ $carpet->carpet_no }}</td>
                      <td>
                         <div class="small">{{ $carpet->map_number }}</div>
                         <span class="badge badge-light border">{{ $carpet->quality->quality ?? '---' }}</span>
                      </td>
                      <td><span class="badge badge-soft-primary">{{ $carpet->type->carpet_type ?? '---' }}</span></td>
                      <td><span class="small">{{ $carpet->width }} × {{ $carpet->height }}</span></td>
                      <td class="font-weight-bold">{{ $carpet->area }} <small>m²</small></td>
                      <td>
                         <span class="small">{{ $carpet->field }} / {{ $carpet->margin }}</span>
                      </td>
                      <td>
                         <div class="font-weight-bold text-info">{{ $carpet->warehouse->name ?? 'نامشخص' }}</div>
                         <small class="text-muted">{{ $carpet->warehouse->location ?? '' }}</small>
                      </td>
                      <td class="{{ $agingClass }}">
                         {{ $daysInStock }} روز
                         @if($daysInStock > 180) <i class="fa fa-clock-o text-danger ml-1" title="بیش از ۶ ماه"></i> @endif
                      </td>
                      <td class="text-primary font-weight-bold font-italic">{{ number_format($carpet->total_price, 2) }} $</td>
                      <td class="hideOnPrint">
                         <div class="btn-group align-items-center">
                             @can('view_carpet_stock_details')
                             <a class="btn-modern-action btn-view-details btn-sm" href="/dashboard/carpet-stock-details/{{ $carpet->carpet_id }}" title="مشاهده جزئیات">
                                 <i class="fa fa-eye"></i> جزئیات
                             </a>
                             @endcan
                             @can('sell_carpet_from_stock')
                             <button class="btn-modern-action btn-sell btn-sm ml-1" data-toggle="modal" data-target="#sale_modal"
                                 onclick="
                                 $('#carpet_id').val('{{$carpet->carpet_id}}');
                                 $('#carpet_no').val('{{$carpet->carpet_no}}');
                                 $('#carpet_type').val('{{$carpet->type->carpet_type ?? ''}}');
                                 $('#carpet_quality').val('{{$carpet->quality->quality ?? ''}}');
                                 $('#carpet_area').val('{{$carpet->area}}');
                                 $('#carpet_height').val('{{$carpet->height}}');
                                 $('#carpet_width').val('{{$carpet->width}}');
                                 $('#total_price_cost').val('{{$carpet->total_price}}');
                                 $('#price_per_meter').val('{{ $carpet->area > 0 ? round($carpet->total_price / $carpet->area, 2) : 0 }}');
                                 $('#margin-warning').hide();
                                 $('#sale_cost_total_usd').removeClass('is-invalid');
                                 " title="ثبت فروش">
                                 <i class="fa fa-shopping-cart"></i> فروش
                             </button>
                             @endcan

                             @if(auth()->user()->can('send_carpet_to_kachaee') || auth()->user()->can('send_carpet_to_washing') || auth()->user()->can('send_carpet_to_finishing'))
                             <div class="dropdown d-inline-block ml-1" style="position: static;">
                                 <button class="btn-modern-action btn-sm p-2 no-caret"
                                     style="background: #f1f5f9; color: #475569 !important; border: 1px solid #cbd5e1 !important; height: 33px; width: 33px; border-radius: 8px !important;"
                                     data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true"
                                     aria-expanded="false" title="بیشتر">
                                     <i class="feather icon-more-vertical"></i>
                                 </button>
                                 <div class="dropdown-menu shadow-lg border-0 text-right"
                                     style="min-width: 200px; border-radius: 12px; z-index: 1000001; margin-top: 5px;">
                                     <h6 class="dropdown-header small text-muted font-weight-bold text-right">عملیات انتقال (Transfer)</h6>
                                     
                                     @if($carpet->status != 12)
                                         @can('send_carpet_to_kachaee')
                                         <a class="dropdown-item py-2 px-3 small text-right text-dark animate-fade-in"
                                             href="/dashboard/carpet-repaire/sending-to-kachaee/{{$carpet->carpet_id}}">
                                             <i class="feather icon-tool mr-2 text-warning"></i> ارسال به کچایی (Repair)
                                         </a>
                                         @endcan
                                     @endif
                                     
                                     @can('send_carpet_to_washing')
                                     <a class="dropdown-item py-2 px-3 small text-right text-dark animate-fade-in"
                                         href="/dashboard/washing-team/sending-to-washing/{{$carpet->carpet_id}}">
                                         <i class="feather icon-droplet mr-2 text-info"></i> ارسال به شست (Wash)
                                     </a>
                                     @endcan
                                     
                                     @can('send_carpet_to_finishing')
                                     <a class="dropdown-item py-2 px-3 small text-right text-dark animate-fade-in"
                                         href="/dashboard/carpet-wash/sent-to-finish/{{$carpet->carpet_id}}">
                                         <i class="feather icon-check-circle mr-2 text-success"></i> ارسال به تیاری (Finish)
                                     </a>
                                     @endcan
                                 </div>
                             </div>
                             @endif
                         </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr class="no-records-row">
                    <td colspan="10" class="text-center py-5 text-muted">
                         <i class="fa fa-search fa-3x mb-3 d-block opacity-25"></i>
                         هیچ قالینی در حال حاضر در گدام موجود نیست
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
            @if(!isset($search))
               <div class="d-flex justify-content-center mt-3">
                   {{ $carpets->appends(request()->input())->links() }}
               </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SALES REGISTER MODAL -->
<div class="modal fade" id="sale_modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-modal="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title h4 text-white" id="roomEditModalLabel">ثبت فروش قالین</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <form action="/dashboard/sales" method="post" id="sale_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class=""> نمبر انوایس</label>
                <select name="invoice_id" id="invoice_id" required class="form-control select2-modal">
                  <option value="">~~~</option>
                  @foreach($invoices as $invoice)
                    <option {{ (Request::old('invoice_id') == $invoice->id ? 'selected' : '') }} value="{{$invoice->id}}"
                            customer_name="{{$invoice->customer->name}}"
                            customer_code="{{$invoice->customer->customer_code}}"
                            customer_company="{{$invoice->customer->company_name}}"
                            customer_address="{{$invoice->customer->company_address}}"
                    >{{$invoice->invoice_no}}</option>
                  @endforeach
                </select>
                <small class="text-danger">@error('invoice_id') {{ __('message.'.$message) }}@enderror</small>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>نام مشتری</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control bg-light" readonly>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>کود مشتری</label>
                <input type="text" name="customer_code" id="customer_code" class="form-control bg-light" readonly>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
              <div class="form-group fill">
                <label class="login2 pull-right pull-right-pro">نام کمپنی</label>
                <input type="text" name="company_name" id="company_name" class="form-control bg-light" readonly>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-12 location_value_div">
              <div class="form-group fill">
                <label>ادرس کمپنی</label>
                <input type="text" name="company_address" id="company_address" class="form-control bg-light" readonly>
              </div>
            </div>

            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>نمبر قالین</label>
                <input type="text" name="carpet_no" id="carpet_no" class="form-control bg-light" readonly>
                <input type="hidden" name="carpet_id" id="carpet_id">
              </div>
            </div>
            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>نوعیت قالین</label>
                <input type="text" name="carpet_type" id="carpet_type" class="form-control bg-light" readonly>
              </div>
            </div>
            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>کوالتی</label>
                <input type="text" name="carpet_quality" id="carpet_quality" class="form-control bg-light" readonly>
              </div>
            </div>
            <div class="col col-lg-4 col-md-4 col-sm-4 col-6">
              <div class="form-group fill">
                <label>طول قالین</label>
                <input type="text" name="carpet_height" id="carpet_height" class="form-control">
              </div>
            </div>
    
            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>عرض قالین</label>
                <input type="text" name="carpet_width" id="carpet_width" class="form-control">
              </div>
            </div>
            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label>سایز قالین</label>
                <input type="text" name="carpet_area" readonly id="carpet_area" class="form-control bg-light">
              </div>
            </div>
    
            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-muted">قیمت تمام شد فی متر (USD)</label>
                <input type="text" name="price_per_meter" id="price_per_meter" class="form-control bg-light" readonly>
              </div>
            </div>
    
            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-muted">قیمت مجموع تمام شد (USD)</label>
                <input type="text" name="total_price_cost" id="total_price_cost" class="form-control bg-light font-weight-bold text-danger" readonly>
              </div>
            </div>

            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-success">اسعار فروش (Currency)</label>
                <select name="currency_id" id="sale_currency_id" class="form-control font-weight-bold border-success" style="border: 2px solid #28a745;" required>
                  @foreach($currencies as $curr)
                    <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ $curr->code == 'USD' ? 'selected' : '' }}>
                      {{ $curr->code }} ({{ $curr->symbol }}) - Rate: {{ $curr->exchange_rate }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-success">نرخ تبادله (Exchange Rate)</label>
                <input type="number" step="any" name="exchange_rate" id="sale_exchange_rate" class="form-control border-success font-weight-bold" style="border: 2px solid #28a745;" value="1.0" required>
              </div>
            </div>

            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-success">قیمت فروش فی متر (به اسعار انتخابی)</label>
                <input type="text" required name="sale_cost_per_meter" id="sale_cost_per_meter" class="form-control border-primary font-weight-bold">
                <small class="text-danger">@error('sale_cost_per_meter') {{ __('message.'.$message) }} @enderror</small>
              </div>
            </div>

            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-primary">قیمت مجموع فروش (به اسعار انتخابی)</label>
                <input type="text" name="sale_cost_total" id="sale_cost_total" class="form-control bg-light font-weight-bold text-primary" readonly>
              </div>
            </div>

            <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
              <div class="form-group fill">
                <label class="font-weight-bold text-danger">مجموع فروش به دالر (Total Sale USD)</label>
                <input type="text" name="sale_cost_total_usd" id="sale_cost_total_usd" class="form-control bg-light font-weight-bold text-danger" readonly>
                <div id="margin-warning" class="text-danger font-weight-bold mt-1" style="display:none; font-size: 0.82rem;">
                  <i class="fa fa-exclamation-triangle"></i> هشدار: قیمت فروش کمتر از قیمت تمام‌شد است! (Negative Margin)
                </div>
              </div>
            </div>


  
          </div>

          <!-- ACCOUNT OVERRIDES -->
          <div class="row mt-4" style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid #e9ecef;">
              <div class="col-lg-12 mb-3">
                  <h6 class="font-weight-bold text-dark border-bottom pb-2">
                      <i class="fa fa-university text-primary mr-2"></i> تنظیمات حسابی (Accounting Overrides)
                  </h6>
                  <p class="small text-muted mb-0">در این بخش می‌توانید حساب‌های پیش‌فرض را برای این فروش تغییر دهید.</p>
              </div>
              
              <!-- Revenue Mapping Overrides -->
              <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                      <label class="text-info font-weight-bold small">حساب دریافتنی/نقد (Revenue Debit)</label>
                      <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2-modal">
                          @foreach($allowedRevenueDebit as $acc)
                              <option value="{{ $acc->id }}" {{ ($mappingRevenue && $mappingRevenue->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                      <label class="text-info font-weight-bold small">حساب فروش/عاید (Revenue Credit)</label>
                      <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2-modal">
                          @foreach($allowedRevenueCredit as $acc)
                              <option value="{{ $acc->id }}" {{ ($mappingRevenue && $mappingRevenue->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
              </div>

              <!-- COGS Mapping Overrides (Optional/Advanced) -->
              <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                  <div class="form-group fill">
                      <label class="text-warning font-weight-bold small">حساب هزینه تمام شد (COGS Debit)</label>
                      <select name="override_cogs_debit_id" id="override_cogs_debit_id" class="form-control select2-modal">
                          @foreach($allowedCogsDebit as $acc)
                              <option value="{{ $acc->id }}" {{ ($mappingCogs && $mappingCogs->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                  <div class="form-group fill">
                      <label class="text-warning font-weight-bold small">حساب موجودی گدام (Inventory Credit)</label>
                      <select name="override_cogs_credit_id" id="override_cogs_credit_id" class="form-control select2-modal">
                          @foreach($allowedCogsCredit as $acc)
                              <option value="{{ $acc->id }}" {{ ($mappingCogs && $mappingCogs->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
              </div>

              <div class="col-lg-12">
                  <div class="alert alert-warning border-0 bg-soft-warning mt-3 py-2" style="font-size: 0.85rem;">
                      <i class="fa fa-exclamation-triangle"></i> <strong>توجه:</strong> هرگونه تغییر در این بخش مستقیماً بر بیلانس مالی و گزارشات عایدات تاثیر می‌گذارد.
                  </div>
              </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="submit" class="btn btn-primary shadow-sm" id="sale_submit_btn">
            <i class="fa fa-check-circle mr-1"></i> تایید و ثبت فروش
          </button>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">انصراف</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  <script>
    // Fix Select2 in Modal
    $(document).ready(function() {
        $('#type_id').select2();
        $('#warehouse_filter').select2();
        
        // Live client-side row filtering (instant filtering)
        $('#live-carpet-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#dataTable tbody tr').filter(function() {
                if ($(this).hasClass('no-records-row')) return;
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        // Prevent double submit on sales form
        $('#sale_form').on('submit', function() {
            var $btn = $('#sale_submit_btn');
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> در حال ثبت...');
        });

        // Fix for Select2 in Bootstrap Modal
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};

        $('.select2-modal').each(function() {
            var $p = $(this).closest('.modal');
            $(this).select2({
                dropdownParent: $p,
                width: '100%',
                dropdownAutoWidth: true,
                placeholder: "انتخاب کنید..."
            });
        });

        $('#sale_modal').on('shown.bs.modal', function () {
            $('.select2-modal').select2({
                dropdownParent: $('#sale_modal'),
                width: '100%'
            });
        });

        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 2000);
    });
  </script>

  <script type="text/javascript">
      $(document).ready(function () {

          function calculateSalePrices() {
              var carpet_area = parseFloat($('#carpet_area').val()) || 0;
              var sale_cost_per_meter = parseFloat($('#sale_cost_per_meter').val()) || 0;
              var exchange_rate = parseFloat($('#sale_exchange_rate').val()) || 1.0;
              var cost_total = parseFloat($('#total_price_cost').val()) || 0;
              
              if (carpet_area > 0 && sale_cost_per_meter > 0) {
                  var total_cost = (sale_cost_per_meter * carpet_area).toFixed(2);
                  $('#sale_cost_total').val(total_cost);
                  
                  // Total in USD
                  var total_usd = (total_cost * exchange_rate).toFixed(2);
                  $('#sale_cost_total_usd').val(total_usd);
                  
                  // Verify profit margin (Warning badge for negative margin)
                  if (parseFloat(total_usd) < cost_total) {
                      $('#margin-warning').show();
                      $('#sale_cost_total_usd').addClass('is-invalid');
                  } else {
                      $('#margin-warning').hide();
                      $('#sale_cost_total_usd').removeClass('is-invalid');
                  }
              } else {
                  $('#sale_cost_total').val('0.00');
                  $('#sale_cost_total_usd').val('0.00');
                  $('#margin-warning').hide();
                  $('#sale_cost_total_usd').removeClass('is-invalid');
              }
          }

          $('#sale_currency_id').change(function () {
              var selected = $(this).find(':selected');
              var rate = parseFloat(selected.data('rate')) || 1.0;
              $('#sale_exchange_rate').val(rate);
              calculateSalePrices();
          });

          $('#sale_exchange_rate, #sale_cost_per_meter').on('input change keyup', function() {
              calculateSalePrices();
          });

          $('#carpet_height, #carpet_width').keyup(function () {
              var carpet_height = parseFloat($('#carpet_height').val()) || 0;
              var carpet_width = parseFloat($('#carpet_width').val()) || 0;
              var carpet_area = (carpet_width * carpet_height).toFixed(2);
              $('#carpet_area').val(carpet_area);
              
              var total_price = parseFloat($('#total_price_cost').val()) || 0;
              if(carpet_area > 0) {
                  $('#price_per_meter').val((total_price / carpet_area).toFixed(2));
              }
              calculateSalePrices();
          });

          $('#invoice_id').change(function () {
              var customer_name = $('#invoice_id option:selected').attr('customer_name');
              $('#customer_name').val(customer_name);

              var customer_code = $('#invoice_id option:selected').attr('customer_code');
              $('#customer_code').val(customer_code);

              var customer_company = $('#invoice_id option:selected').attr('customer_company');
              $('#company_name').val(customer_company);

              var company_address = $('#invoice_id option:selected').attr('customer_address');
              $('#company_address').val(company_address);
          });
      });
  </script>
@endsection
