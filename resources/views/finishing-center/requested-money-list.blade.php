@extends('dsh.master')
@section('title', 'لیست درخواست های پول تیاری')

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
  
  .btn-approve {
    background: linear-gradient(135deg, #11998e, #38ef7d) !important;
  }
  .btn-reject {
    background: linear-gradient(135deg, #ff9966, #ff5e62) !important;
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
    width: 100%;
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
  
  .badge-premium {
      border-radius: 6px;
      padding: 6px 12px;
      font-weight: 600;
      font-size: 0.75rem;
  }
</style>

@php
  $total_pending_count = $requests ? $requests->count() : 0;
  $total_pending_usd = $requests ? $requests->sum(function($r) {
      return $r->base_amount ?: ($r->amount ?: ($r->amount_af / 70.0));
  }) : 0;
  
  $total_pending_afn = $requests ? $requests->sum(function($r) {
      return $r->currency_code === 'AFN' ? $r->original_amount : ($r->amount_af ?: 0);
  }) : 0;
  
  $usd_requests_count = $requests ? $requests->where('currency_code', 'USD')->count() : 0;
  $afn_requests_count = $requests ? $requests->where('currency_code', 'AFN')->count() : 0;
@endphp

<!-- ANIMATED STATS CARDS ROW -->
<div class="row mb-4">
  <!-- Card 1: Total Pending Requests -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-blue p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ $total_pending_count }} <span style="font-size: 1rem; font-weight: normal;">درخواست</span></div>
          <div class="stat-card-lbl">کل درخواست‌های معلق (Pending Requests)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-hourglass-half"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 2: Total USD Valuation -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-green p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">${{ number_format($total_pending_usd, 2) }}</div>
          <div class="stat-card-lbl">ارزش معادل معلق (USD Valuation)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-dollar"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 3: Total AFN Valuation -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-indigo p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val">{{ number_format($total_pending_afn, 2) }} <span style="font-size: 1rem; font-weight: normal;">AFN</span></div>
          <div class="stat-card-lbl">ارزش معلق به افغانی (AFN Valuation)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-money"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 4: Currency Breakdown -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card stat-card-teal p-4 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-card-val" style="font-size: 1.4rem;">
            USD: {{ $usd_requests_count }} | AFN: {{ $afn_requests_count }}
          </div>
          <div class="stat-card-lbl">تفکیک اسعار (Requests Breakdown)</div>
        </div>
        <div class="stat-card-icon">
          <i class="fa fa-exchange"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card modern-card">
      <div class="card-header modern-header">
        <h3 class="modern-title"><i class="fa fa-money"></i> بررسی درخواست‌های پرداخت نقدی به تیم‌های آماده‌سازی (Finishing Payout Board)</h3>
        
        <div class="btn-modern-action btn-print hideOnPrint pull-left" onclick="printPage('MRDetails')">
          <i class="fa fa-print"></i> چاپ صفحه
        </div>
      </div>
      
      <div class="card-body">
        
        <!-- Live Filtering / Search Input -->
        <div class="row mb-4 hideOnPrint">
          <div class="col-md-4 col-sm-6">
            <input type="text" id="refinish-search" placeholder="🔍 جستجو بر اساس نام تیم، نوعیت پرداخت، اسعار..." class="form-control modern-search">
          </div>
        </div>

        <div class="static-table-list table-responsive" id="MRDetails">
          <table class="table modern-table text-center" id="dataTable">
            <thead>
              <tr>
                <th>نام تیاری گر</th>
                <th>نوعیت پرداخت</th>
                <th>مقدار (USD - معادل)</th>
                <th>مقدار اسعار اصلی</th>
                <th>اسعار انتخابی</th>
                <th>نرخ تبادله</th>
                <th>تاریخ</th>
                <th class="hideOnPrint"><b>تایید پرداخت</b></th>
                <th class="hideOnPrint"><b>رد نمودن</b></th>
              </tr>
            </thead>
            <tbody>
              @if($requests && $requests->count() > 0)
                @foreach($requests as $r)
                  @php
                      $team = DB::table('finishing_teams')->where('id', $r->team_id)->first();
                  @endphp
                  <tr class="ur{{$r->id}}">
                    <td class="font-weight-bold text-dark">{{ $team ? $team->name : 'N/A' }}</td>
                    
                    <!-- Request Type Badge -->
                    <td>
                      @if($r->type == 'رسید')
                        <span class="badge badge-success badge-premium">رسید (دریافت)</span>
                      @else
                        <span class="badge badge-danger badge-premium">گرفت (پرداخت)</span>
                      @endif
                    </td>
                    
                    <!-- Normalized USD Amount -->
                    <td style="direction: ltr;" class="font-weight-bold text-success">
                      {{ number_format($r->base_amount ?: ($r->amount ?: ($r->amount_af / 70.0)), 2) }} $
                    </td>
                    
                    <!-- Original Request Currency & Amount Column -->
                    <td style="direction: ltr;" class="font-weight-bold text-info">
                      {{ number_format($r->original_amount ?: ($r->amount ?: $r->amount_af), 2) }}
                    </td>

                    <!-- Currency Code -->
                    <td>
                      <span class="badge badge-info badge-premium">{{ $r->currency_code ?: ($r->amount > 0 ? 'USD' : 'AFN') }}</span>
                    </td>

                    <!-- Exchange Rate -->
                    <td style="direction: ltr;">
                      {{ number_format($r->exchange_rate ?: ($r->amount > 0 ? 1.00 : 70.00), 2) }}
                    </td>

                    <td>{{ $r->date }}</td>
                    
                    <!-- Approve Button -->
                    <td class="hideOnPrint">
                      <button onclick="approveRequest({{$r->id}}, this)" class="btn-modern-action btn-approve btn-sm">
                        <i class="fa fa-check"></i> تایید
                      </button>
                    </td>
                    
                    <!-- Reject Button -->
                    <td class="hideOnPrint">
                      <button onclick="deleteRequest({{$r->id}}, this)" class="btn-modern-action btn-reject btn-sm">
                        <i class="fa fa-times"></i> رد کردن
                      </button>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="9" class="text-center py-5">
                    <div class="empty-state py-5">
                      <i class="fa fa-check-circle text-success" style="font-size: 3.5rem; margin-bottom: 15px; display: block;"></i>
                      <h4 style="font-weight: bold; color: #475569;">هیچ درخواست پولی در انتظار تایید وجود ندارد</h4>
                      <p style="color: #64748b;">تمام درخواست‌های پرداخت به تیم‌های آماده‌سازی تایید یا رد شده اند.</p>
                    </div>
                  </td>
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
  $(document).ready(function() {
      // Live Instant Client-side Search
      $('#refinish-search').on('keyup', function() {
          var value = $(this).val().toLowerCase();
          $('#dataTable tbody tr').filter(function() {
              if ($(this).find('.empty-state').length === 0) {
                  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
              }
          });
      });
  });

  function approveRequest(id, btn) {
      swal({
          text: "آیا مطمئن هستید که می‌خواهید این درخواست پرداخت را تایید کنید؟",
          buttons: true,
          dangerMode: false,
          buttons: {
              confirm: {text: 'بلی، تایید شود', className: 'btn-success'},
              cancel: 'انصراف'
          },
      })
      .then((willApprove) => {
          if (willApprove) {
              // Prevent double submission & show loading spinner
              var $btn = $(btn);
              var originalHtml = $btn.html();
              $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> در حال ارسال...');
              
              $.ajax({
                  type: 'DELETE',
                  data: {
                      '_token': '{{csrf_token()}}',
                  },
                  url: '/dashboard/finishing-approve-request-money/' + id,
                  success: function (res) {
                      if (res.status == 'success') {
                          swal("موفقانه!", "پرداخت با موفقیت تایید و در دفتر کل ثبت گردید.", "success");
                          $('.ur' + id).fadeOut(600, function() {
                              $(this).remove();
                              if ($('#dataTable tbody tr:visible').length === 0) {
                                  location.reload();
                              }
                          });
                      } else {
                          swal("خطا!", "موجودی صندوق کافی نیست یا خطایی رخ داده است.", "error");
                          $btn.prop('disabled', false).html(originalHtml);
                      }
                  },
                  error: function() {
                      swal("خطا!", "ارتباط با سرور برقرار نشد.", "error");
                      $btn.prop('disabled', false).html(originalHtml);
                  }
              });
          }
      });
  }

  function deleteRequest(id, btn) {
      swal({
          text: "آیا مطمئن هستید که می‌خواهید این درخواست را رد کنید؟",
          buttons: true,
          dangerMode: true,
          buttons: {
              confirm: {text: 'بلی، رد شود', className: 'btn-danger'},
              cancel: 'انصراف'
          },
      })
      .then((willDelete) => {
          if (willDelete) {
              // Prevent double submission & show loading spinner
              var $btn = $(btn);
              var originalHtml = $btn.html();
              $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> در حال ارسال...');

              $.ajax({
                  type: 'DELETE',
                  data: {
                      '_token': '{{csrf_token()}}',
                  },
                  url: '/dashboard/finishing-delete-request-money/' + id,
                  success: function (res) {
                      if (res.status == 'success') {
                          swal("رد شد!", "درخواست با موفقیت رد شد.", "info");
                          $('.ur' + id).fadeOut(600, function() {
                              $(this).remove();
                              if ($('#dataTable tbody tr:visible').length === 0) {
                                  location.reload();
                              }
                          });
                      } else {
                          swal("خطا!", "رد کردن درخواست با خطا مواجه شد.", "error");
                          $btn.prop('disabled', false).html(originalHtml);
                      }
                  },
                  error: function() {
                      swal("خطا!", "ارتباط با سرور برقرار نشد.", "error");
                      $btn.prop('disabled', false).html(originalHtml);
                  }
              });
          }
      });
  }
</script>
@endsection