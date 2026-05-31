@extends('dsh.master')

@section('content')
<style>
  /* Premium Glassmorphism & Custom Elements */
  .modern-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06) !important;
    backdrop-filter: blur(12px);
    overflow: hidden;
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
  .nav-pills {
    background: rgba(240, 242, 245, 0.8);
    padding: 6px;
    border-radius: 12px;
    display: inline-flex;
    margin-bottom: 24px !important;
    border: none !important;
  }
  .nav-pills .nav-item {
    margin-right: 4px;
  }
  .nav-pills .nav-link {
    border-radius: 8px !important;
    padding: 10px 20px !important;
    font-weight: 600 !important;
    color: #4a5568 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none !important;
  }
  .nav-pills .nav-link.active {
    background: #2a5298 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(42, 82, 152, 0.2);
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
  
  .btn-tiari {
    background: linear-gradient(135deg, #11998e, #38ef7d) !important;
  }
  .btn-wash {
    background: linear-gradient(135deg, #ff9966, #ff5e62) !important;
  }
  .btn-central {
    background: linear-gradient(135deg, #2193b0, #6dd5ed) !important;
  }
  .btn-re-tiari {
    background: linear-gradient(135deg, #4e54c8, #8f94fb) !important;
  }
  .btn-edit {
    background: linear-gradient(135deg, #f39c12, #f1c40f) !important;
  }
  .btn-view {
    background: linear-gradient(135deg, #00c6ff, #0072ff) !important;
  }
  .btn-print {
    background: linear-gradient(135deg, #34495e, #2c3e50) !important;
    color: #fff !important;
  }
  
  /* Inputs Customization */
  .modern-search {
    border-radius: 10px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 12px 18px !important;
    font-size: 0.9rem !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.01) !important;
    transition: all 0.3s ease !important;
  }
  .modern-search:focus {
    border-color: #2a5298 !important;
    box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1) !important;
    outline: none;
  }
  
  /* Status Badges */
  .modern-badge {
    padding: 6px 12px !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    display: inline-block;
  }
  .badge-success-modern {
    background-color: #e2fbe8 !important;
    color: #0f762e !important;
  }
  .badge-warning-modern {
    background-color: #fffbeb !important;
    color: #b45309 !important;
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

@php
  $pending_carpets = \App\Carpet::where('status', 4)->with('carpet_wash')->get();
  $pending_pcs = $pending_carpets->count();
  $pending_area = $pending_carpets->sum(function($c) {
      return $c->carpet_wash ? $c->carpet_wash->area : ($c->area ?? 0);
  });

  $finished_works = \App\FinishingWork::where('status', 1)->get();
  $finished_ops_count = $finished_works->count();
  $finished_carpets_count = $finished_works->unique('carpetId')->count();
  $total_cost_usd = $finished_works->sum('price');
  $total_cost_afn = $finished_works->sum('price_af');
@endphp

<!-- ANIMATED STATS CARDS ROW -->
<div class="row mb-4">
  <!-- Card 1: Pending Pcs -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-blue p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ $pending_pcs }} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
          <div class="stat-card-lbl">آماده تیاری (Pending Finishing)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-scissors"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 2: Pending Area -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-indigo p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ number_format($pending_area, 2) }} <span style="font-size: 1rem; font-weight: normal;">متر مربع (m²)</span></div>
          <div class="stat-card-lbl">مساحت آماده (Pending Area)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-expand"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 3: Finished Pcs -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-green p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ $finished_carpets_count }} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
          <div class="stat-card-lbl">قالین‌های آماده شده (Finished Pcs)</div>
          <small style="font-size: 0.75rem; opacity: 0.8;">تعداد عملیات: {{ $finished_ops_count }}</small>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-check-circle"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 4: Total Finishing Costs -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-teal p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">${{ number_format($total_cost_usd, 2) }}</div>
          <div class="stat-card-lbl" style="font-size: 0.8rem;">
            مجموع مصارف تیاری (Total Cost)
            <br>
            <span style="font-size: 0.75rem; opacity: 0.8;">{{ number_format($total_cost_afn, 2) }} AFN</span>
          </div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-money"></i>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- navbar -->
  <div class="row" style="display: flex; justify-content: center;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card modern-card">
        <div class="card-header modern-header">
          <h3 class="modern-title"><i class="fa fa-scissors"></i> مدیریت و بررسی کارگاه تیاری (Finishing Center)</h3>
          
          <div>
            <div class="alert alert-success" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              مرحله ترمیم حذف شد
            </div>
            
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
          </div>
        
        </div>
        <div class="card-body">
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link {{$check == null ? 'active' : ''}}"
                 id="pills-non-finished-tab"
                 data-toggle="pill"
                 href="#non-finished"
                 role="tab" aria-controls="pills-non-finished" aria-selected="true"><i class="fa fa-clock-o"></i> تیاری نشده ها</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{$check != null ? 'active' : ''}}"
                 id="pills-finished-tab"
                 data-toggle="pill" href="#finished"
                 role="tab" aria-controls="pills-finished" aria-selected="false"><i class="fa fa-check-circle"></i> تیاری شده ها</a>
            </li>
          </ul>
          
          <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade {{$check == null ? 'active show' : ''}}" id="non-finished"
                 role="tabpanel"
                 aria-labelledby="pills-non-finished-tab">
               <div class="row align-items-center" style="margin-bottom: 20px;">
                 <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4 hideOnPrint">
                   <form action="/dashboard/finishing-center/search-non" method="post">
                     @csrf
                     <input type="text" name="search_non" required
                            placeholder="🔍 جستجو بر اساس شماره قالین، نوعیت قالین..." class="form-control modern-search">
                   </form>
                 </div>
                 <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8 text-left">
                   <div class="btn-modern-action btn-print hideOnPrint"
                        onclick="printPage('noneRepairPrint')"><i
                             class="fa fa-print"></i> چاپ گزارش
                   </div>
                 </div>
               </div>

              <div class="static-table-list table-responsive" id="noneRepairPrint">
                <table class="table modern-table" id="secondDataTable">
                  <thead>
                  <tr>
                    
                    <th>شماره قالین</th>
                    <th>اسم نماینده</th>
                    <th>شماره فرمایش</th>
                    <th>نوعیت قالین</th>
                    <th>نام شست گر</th>
                    <th>شست نمبر</th>
                    <th>طول</th>
                    <th>عرض</th>
                    <th>مصاحت</th>
                    <th>تاریخ شست</th>
                    <th class="printTitle">تیاری</th>
            
                    
                    <th class="printTitle">بازگشت</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($nonfinished as $nonfinish)
                    <tr class="ur{{ $nonfinish->carpet_id  ?? ''}}">
                      
                      <td>{{$nonfinish->carpet_no ?? ''}}</td>
                      @foreach($agents as $agent)
                        @if($agent->agent_id == $nonfinish->agent_id)
                          <td>{{$agent->user->name ?? ''}}</td>
                        @endif
                      @endforeach
                      <td>{{$nonfinish->carpet_order->order_number ?? ''}}</td>
                      <td>{{$nonfinish->type->carpet_type ?? ''}}</td>
                      @if($nonfinish->washing)
                        <td>{{$nonfinish->washing->name ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->wash_number ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->height ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->width ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->area ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->date ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      
                      
                      <td class="hideOnPrint"><a
                                href="/dashboard/finishing-center/finish-work/{{$nonfinish->carpet_id ?? ''}}"
                                class="btn-modern-action btn-tiari printBTN"><i
                                  class="fa fa-scissors"></i>&nbsp; تیاری</a></td>
  
                     
                        <?php $wash = \App\CarpetWash::where('carpetId', $nonfinish->carpet_id)->first(); ?>
                   
                      
                      @if($wash)
                        <td><a href="/dashboard/return-to-wash/{{$nonfinish->carpet_id}}"
                               class="btn-modern-action btn-wash printBTN"><i
                                    class="fa fa-undo"></i>&nbsp; بازگشت به شست</a></td>
                      @else
                        
                        <td><a href="/dashboard/return-to-center-from-finish/{{$nonfinish->carpet_id}}"
                               class="btn-modern-action btn-central printBTN"><i
                                    class="fa fa-reply"></i>&nbsp; بازگشت به مرکزی</a></td>
                      @endif
                    
                    
                    </tr>
                  @endforeach
                  
                  </tbody>
                </table>
                <div class="hideOnPrint">{{$nonfinished->links() ?? ''}}</div>
              </div>
              
              <div class="row" style="margin-top: 20px;">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <table class="table table-bordered table-sm" style="background: #f8fafc; border-radius: 8px; overflow: hidden;">
                    <tbody>
                    <tr>
                      <td style="font-weight: 600; color: #475569;">تعداد کل قالین ها</td>
                      <td style="direction: ltr; font-weight: bold; text-align: left; color: #2a5298;">{{$nonfinished->count()}} pcs</td>
                    </tr>
                    <tr>
                      <td style="font-weight: 600; color: #475569;">مجموع متراژ (m²)</td>
                      <td style="direction: ltr; font-weight: bold; text-align: left; color: #2a5298;">{{$nonfinished->sum('area')}} m²</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            
            </div>
            
            
            <div class="tab-pane fade {{$check != null ? 'active show' : ''}}" id="finished"
                 role="tabpanel"
                 aria-labelledby="pills-finished-tab">
              
              <div class="row align-items-center" style="margin-bottom: 20px;">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                  <form action="/dashboard/finishing-center/search" method="POST" id="dateSearch">
                    @csrf
                    <input type="text" name="search_finish" required
                           placeholder="🔍 جستجو بر اساس شماره قالین، تیم، شماره تیاری..." class="form-control modern-search">
                  </form>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 text-left">
                  <div class="btn-modern-action btn-print hideOnPrint"
                       onclick="printPage('repairPrint')"><i
                            class="fa fa-print"></i> چاپ گزارش
                  </div>
                </div>
              </div>
              
              <div class="static-table-list table-responsive" style="margin-top: 20px" id="repairPrint">
                <table class="table modern-table">
                  <thead>
                  <tr>
                    <th>شماره قالین</th>
                    <th>نمبر تیاری</th>
                    <th> قیمت تیاری (USD / اسعار)</th>
                    <th>تاریخ تیاری</th>
                    <th>تیم تیاری</th>
                    <th>نوع تیاری</th>
                    <th>شرح</th>
                    <th class="printTitle">حالت</th>
                    <th class="printTitle">دوباره تیاری</th>
                    <th class="printTitle">ویرایش</th>
                    {{-- <th class="printTitle">ارسال به گدام</th> --}}
                    <th class="printTitle">جزئیات کلی</th>
                  </tr>
                  </thead>
                  <tbody>
                  @php($total_area = 0)
                  @foreach($finisheds as $finish)
                    <tr class="ur{{ $finish->id  ?? ''}}">
                      <td>{{$finish->carpet->carpet_no ?? ''}}</td>
                      <td>
                        <a href="/dashboard/finishing-center/search-finish-number/{{$finish->finish_number}},{{$finish->team_id}}"
                           style="font-weight: 600; color: #2a5298;"
                        >&nbsp; {{$finish->finish_number}}</a></td>
                      <td style="direction: ltr; font-weight: bold;">
                        <span class="text-primary">{{ number_format($finish->price, 2) }} USD</span>
                        @if(($finish->currency_code ?? 'USD') !== 'USD')
                          <br>
                          <span style="font-size: 11px; color: #6c757d;">
                            {{ number_format($finish->price_af, 2) }} {{ $finish->currency_code }}
                          </span>
                        @elseif($finish->price_af != $finish->price)
                          <br>
                          <span style="font-size: 11px; color: #6c757d;">
                            {{ number_format($finish->price_af, 2) }} AFN
                          </span>
                        @endif
                      </td>
                      <td>{{$finish->date ?? ''}}</td>
                      <td>{{$finish->team->name ?? ''}}</td>
                      <td>{{$finish->category->category ?? ''}}</td>
                      <td>{{$finish->description ?? ''}}</td>
                      @if($finish->status == 0)
                        <td class="hideOnPrint">
                          <span class="modern-badge badge-warning-modern"><i class="fa fa-spinner"></i> در انتظار تایید</span>
                        </td>
                      @else
                        <td class="hideOnPrint">
                          <span class="modern-badge badge-success-modern"><i class="fa fa-check"></i> تایید شده</span>
                        </td>
                      @endif
  
                      <td class="hideOnPrint"><a
                                href="/dashboard/finishing-center/re-finish-work/{{$finish->carpet->carpet_id ?? ''}}"
                                class="btn-modern-action btn-re-tiari printBTN"><i
                                  class="fa fa-refresh"></i>&nbsp; دوباره تیاری</a></td>
                      <td class="hideOnPrint"><a href="/dashboard/finishing-center/{{$finish->id ?? ''}}/edit" class="btn-modern-action btn-edit printBTN"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                      
                      <td class="hideOnPrint"><a href="/dashboard/finishing-center/{{$finish->id ?? ''}}"
                                                 class="btn-modern-action btn-view printBTN"><i
                                  class="fa fa-eye"></i>&nbsp; نمایش</a></td>
                    </tr>
                    
                    <span style="display: none">
                      @if($finish->carpet->carpet_wash)
                        {{$total_area += $finish->carpet->carpet_wash->area}}
                      @endif
                    </span>
                  
                  @endforeach
                  <tr>
                    
                    
                    <th colspan="2"><b>تعداد</b></th>
                    
                    <td style="direction: ltr"><b>{{$finisheds->count()}} pcs </b></td>
                  
                  
                  </tr>
                  <tr>
                    
                    
                    <th colspan="2"><b>متراژ</b></th>
                    
                    <td style="direction: ltr"><b>{{$total_area}} m <sup>2</sup> </b></td>
                  
                  
                  </tr>
                  <tr>
                    <th colspan="2"><b>مجموع مصارف (USD)</b></th>
                    <td style="direction: ltr; font-weight: bold; color: #28a745;">
                      <b>{{ number_format($finisheds->sum('price'), 2) }} USD</b>
                    </td>
                  </tr>
                  <tr>
                    <th colspan="2"><b>مجموع مصارف (AFN)</b></th>
                    <td style="direction: ltr; font-weight: bold; color: #17a2b8;">
                      <b>{{ number_format($finisheds->sum('price_af'), 2) }} AFN</b>
                    </td>
                  </tr>
                  </tbody>
                </table>
                
                <p>{{$finisheds->links() ?? ''}}</p>
              
              </div>
            
            </div>
          
          </div>
        
        </div>
      </div>
    </div>
  </div>

@endsection