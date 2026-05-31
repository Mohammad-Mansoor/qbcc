@extends('dsh.master')
@section('title' , 'گدام مواد خام — Material Stock')
@section('content')

<style>
  .glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
  }
  .pulse-warning {
    animation: warning-pulse 2s infinite alternate;
  }
  @keyframes warning-pulse {
    0% {
      box-shadow: 0 4px 12px rgba(246, 194, 62, 0.15);
    }
    100% {
      box-shadow: 0 4px 20px rgba(239, 68, 68, 0.35);
      border-color: rgba(239, 68, 68, 0.4) !important;
    }
  }
  .table-premium tbody tr {
    transition: background-color 0.15s ease;
  }
  .table-premium tbody tr:hover {
    background-color: #f8fafc !important;
  }
  .kpi-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }
  .kpi-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
  }
</style>

@php
  $totalUSD = $stock->sum('total_value');
  $exchRate = $afnCurrency->exchange_rate ?? 0.0125;
  $totalAFN = $exchRate > 0 ? ($totalUSD / $exchRate) : 0;
  
  $yarnWeight = $stock->where('subtype', 'yarn')->sum('quantity');
  $yarnCount = $stock->where('subtype', 'yarn')->count();
  
  $dyeWeight = $stock->where('subtype', 'dye')->sum('quantity');
  $dyeCount = $stock->where('subtype', 'dye')->count();
  
  $lowStockCount = $stock->filter(function($item) { return $item->quantity < 10; })->count();
@endphp

{{-- KPI Metric Cards --}}
<div class="row">
  <!-- Card 1: Total Stock Value -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card glass-card h-100 py-2" style="border-left: 4px solid #3b82f6 !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-primary">ارزش کل موجودی (Base Currency)</div>
            <div class="kpi-value">${{ number_format($totalUSD, 2) }}</div>
            @if($afnCurrency)
              <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
                معادل: <span style="font-weight: 700; color: #10b981;">{{ number_format($totalAFN, 2) }} AFN</span>
                <span class="d-block text-muted" style="font-size: 0.68rem; opacity: 0.85; margin-top: 1px;">
                  نرخ ارز: 1 USD = {{ number_format(1 / $exchRate, 2) }} AFN
                </span>
              </div>
            @endif
          </div>
          <div class="col-auto">
            <div style="background: rgba(59, 130, 246, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-dollar text-primary" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 2: Yarn Stock -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div id="yarnKpiCard" class="card glass-card h-100 py-2" style="border-left: 4px solid #10b981 !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-success">موجودی کل تار (Yarn)</div>
            <div class="kpi-value">{{ number_format($yarnWeight, 2) }} kg</div>
            <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
              شامل: <span style="font-weight: 700; color: #3b82f6;">{{ $yarnCount }} نوع تار</span>
            </div>
          </div>
          <div class="col-auto">
            <div style="background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-cubes text-success" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 3: Dye Stock -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div id="dyeKpiCard" class="card glass-card h-100 py-2" style="border-left: 4px solid #06b6d4 !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-info">موجودی کل رنگ (Dye)</div>
            <div class="kpi-value">{{ number_format($dyeWeight, 2) }} kg</div>
            <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
              شامل: <span style="font-weight: 700; color: #3b82f6;">{{ $dyeCount }} نوع رنگ</span>
            </div>
          </div>
          <div class="col-auto">
            <div style="background: rgba(6, 182, 212, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-flask text-info" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 4: Low Stock Alert -->
  <div class="col-xl-3 col-md-6 mb-4">
    <div id="alertKpiCard" class="card glass-card h-100 py-2 {{ $lowStockCount > 0 ? 'pulse-warning' : '' }}" style="border-left: 4px solid #f59e0b !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-warning">اقلام رو به اتمام (&lt; 10kg)</div>
            <div class="kpi-value" style="color: {{ $lowStockCount > 0 ? '#ef4444' : '#0f172a' }};">
              {{ $lowStockCount }} مورد
            </div>
            <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
              @if($lowStockCount > 0)
                <span class="text-danger font-weight-bold"><i class="fa fa-warning"></i> نیاز به خرید مواد</span>
              @else
                <span class="text-success font-weight-bold">کل اقلام در حد مطلوب</span>
              @endif
            </div>
          </div>
          <div class="col-auto">
            <div style="background: rgba(245, 158, 11, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-exclamation-triangle text-warning" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filters Panel --}}
<div class="card mb-4 shadow-sm" style="border: none; border-radius: 12px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
  <div class="card-body p-3">
    <div class="row align-items-center">
      <!-- Search Keyword -->
      <div class="col-lg-3 col-md-6 mb-2 mb-lg-0 text-right">
        <label class="small font-weight-bold text-muted mb-1 d-block">جستجو (Keyword Search)</label>
        <div class="input-group">
          <input type="text" id="filterSearch" class="form-control bg-light border-0" placeholder="نام مواد، کتگوری و..." style="border-radius: 8px 0 0 8px; text-align: right;">
          <div class="input-group-append">
            <span class="input-group-text bg-light border-0" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search text-muted"></i></span>
          </div>
        </div>
      </div>

      <!-- Subtype Filter -->
      <div class="col-lg-2 col-md-6 mb-2 mb-lg-0 text-right">
        <label class="small font-weight-bold text-muted mb-1 d-block">نوعیت ذخیره (Subtype)</label>
        <select id="filterSubtype" class="form-control bg-light border-0 custom-select" style="text-align: right; direction: rtl;">
          <option value="">همه موارد (All)</option>
          <option value="yarn">تار (Yarn)</option>
          <option value="dye">رنگ (Dye)</option>
        </select>
      </div>

      <!-- Category Filter -->
      <div class="col-lg-3 col-md-6 mb-2 mb-lg-0 text-right">
        <label class="small font-weight-bold text-muted mb-1 d-block">کتگوری (Category)</label>
        <select id="filterCategory" class="form-control bg-light border-0 custom-select" style="text-align: right; direction: rtl;">
          <option value="">همه کتگوری‌ها (All)</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->material_category_id }}">{{ $cat->material_category }}</option>
          @endforeach
        </select>
      </div>

      <!-- Type Filter -->
      <div class="col-lg-3 col-md-6 mb-2 mb-lg-0 text-right">
        <label class="small font-weight-bold text-muted mb-1 d-block">نوعیت مواد (Type)</label>
        <select id="filterType" class="form-control bg-light border-0 custom-select" style="text-align: right; direction: rtl;">
          <option value="">همه نوعیت‌ها (All)</option>
          @foreach($types as $type)
            <option value="{{ $type->material_type_id }}">{{ $type->material_type }}</option>
          @endforeach
        </select>
      </div>

      <!-- Reset & Print Buttons -->
      <div class="col-lg-1 col-md-6 text-center mt-3 mt-lg-0 d-flex justify-content-center" style="gap: 5px;">
        <button id="resetFilters" class="btn btn-outline-secondary border-0 bg-light" style="border-radius: 8px; width: 45px; height: 38px;" title="Reset Filters">
          <i class="fa fa-refresh"></i>
        </button>
        <button onclick="printPage('stockTableCard')" class="btn btn-outline-primary border-0 bg-light" style="border-radius: 8px; width: 45px; height: 38px;" title="Print stock list">
          <i class="fa fa-print"></i>
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Stock List Table --}}
<div class="card shadow-sm mb-4" id="stockTableCard" style="border: none; border-radius: 12px; overflow: hidden;">
  <div class="card-header bg-white py-3 border-0">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h5 style="color: #0f172a; margin: 0; font-weight: 700;">لیست موجودی مواد خام</h5>
        <small class="text-muted">نشان‌دهنده کالاها بر اساس مقدار، ارزش خرید (WAC) و گدام</small>
      </div>
      <div>
        <span class="badge badge-light border" style="font-size: 0.8rem; padding: 6px 12px; border-radius: 20px;">
          تعداد ردیف‌ها: <span id="visibleCount" class="font-weight-bold text-primary">{{ $stock->count() }}</span>
        </span>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-premium mb-0" style="font-size: 0.9rem;">
        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
          <tr>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">کتگوری مواد</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">نوعیت مواد</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: center;">نوعیت ذخیره</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">گدام</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: center;">مقدار موجود</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">قیمت فی (WAC)</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">ارزش مجموعی</th>
            <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">تاریخچه</th>
          </tr>
        </thead>
        <tbody>
        @forelse($stock as $p)
          @php 
            $statusClass = '';
            if($p->quantity < 10) $statusClass = 'table-danger';
            elseif($p->quantity < 50) $statusClass = 'table-warning';
          @endphp
          <tr class="stock-row {{ $statusClass }}"
              data-subtype="{{ $p->subtype }}"
              data-category-id="{{ $p->cat_id }}"
              data-type-id="{{ $p->type_id }}"
              data-quantity="{{ $p->quantity }}"
              data-value="{{ $p->total_value }}"
              data-search="{{ $p->material_category }} {{ $p->material_type }} {{ $p->warehouse_name }}">
            
            <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #1e293b;">
              {{ $p->material_category }}
            </td>
            
            <td style="padding: 12px 16px; text-align: right; color: #475569;">
              {{ $p->material_type }}
            </td>
            
            <td style="padding: 12px 16px; text-align: center;">
              @if($p->subtype === 'yarn')
                <span class="badge badge-premium" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">تار (Yarn)</span>
              @elseif($p->subtype === 'dye')
                <span class="badge badge-premium" style="background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2);">رنگ (Dye)</span>
              @else
                <span class="badge badge-premium badge-secondary">{{ ucfirst($p->subtype) }}</span>
              @endif
            </td>
            
            <td style="padding: 12px 16px; text-align: right;">
              <span class="badge badge-light border">{{ $p->warehouse_name ?? 'گدام مرکزی' }}</span>
            </td>
            
            <td style="padding: 12px 16px; text-align: center;" dir="ltr">
              <span class="font-weight-bold text-dark">{{ number_format($p->quantity, 2) }}</span>
              <span class="text-muted small"> kg</span>
              @if($p->quantity < 10)
                <span class="d-block text-danger font-weight-bold" style="font-size: 0.72rem; margin-top: 2px;">
                  <i class="fa fa-warning"></i> ذخیره کم است
                </span>
              @endif
            </td>
            
            <td style="padding: 12px 16px; text-align: right;" dir="ltr">
              <div class="d-flex flex-column align-items-end">
                <span class="font-weight-bold text-dark">${{ number_format($p->price_per_kilo, 2) }}</span>
                @if($afnCurrency)
                  <small class="text-muted" style="font-size: 0.72rem; margin-top: 1px;">
                    {{ number_format($p->price_per_kilo / $exchRate, 2) }} AFN
                  </small>
                @endif
              </div>
            </td>
            
            <td style="padding: 12px 16px; text-align: right;" dir="ltr">
              <div class="d-flex flex-column align-items-end">
                <span class="font-weight-bold text-primary" style="font-size: 0.95rem;">${{ number_format($p->total_value, 2) }}</span>
                @if($afnCurrency)
                  <small class="text-success font-weight-bold" style="font-size: 0.72rem; margin-top: 1px;">
                    {{ number_format($p->total_value / $exchRate, 2) }} AFN
                  </small>
                @endif
              </div>
            </td>
            
            <td class="hideOnPrint" style="padding: 12px 16px; text-align: center;">
              <a href="{{ route('material-stock.history', [$p->cat_id, $p->type_id]) }}" 
                 class="btn btn-outline-info btn-xs" 
                 style="border-radius: 6px; padding: 4px 10px; font-weight: 600;"
                 title="مشاهده حرکات">
                <i class="fa fa-history"></i> تاریخچه
              </a>
            </td>
          </tr>
        @empty
          <tr id="noStockRow">
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="fa fa-inbox text-muted mb-2 d-block" style="font-size: 2.5rem; opacity: 0.5;"></i>
              <strong>هنوز موادی در گدام ثبت نشده است</strong>
            </td>
          </tr>
        @endforelse
          {{-- Empty fallback row (initially hidden) --}}
          <tr id="noStockRow" style="display: none;">
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="fa fa-search text-muted mb-2 d-block" style="font-size: 2.5rem; opacity: 0.5;"></i>
              <strong>هیچ موردی مطابق فیلترهای شما یافت نشد</strong>
            </td>
          </tr>
        </tbody>

        {{-- Dynamic Live Summary Footer --}}
        @if($stock->count() > 0)
        <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0; font-weight: 700; color: #1e293b;">
          <tr>
            <td colspan="4" style="padding: 12px 16px; text-align: right;">مجموع مقادیر فیلتر شده:</td>
            <td style="padding: 12px 16px; text-align: center;" dir="ltr">
              <span id="filteredQty" class="font-weight-bold">0.00 kg</span>
            </td>
            <td></td>
            <td style="padding: 12px 16px; text-align: right;" dir="ltr">
              <div class="d-flex flex-column align-items-end">
                <span id="filteredVal" class="text-primary" style="font-size: 1rem;">$0.00</span>
                @if($afnCurrency)
                  <span id="filteredValLocal" class="text-success small" style="font-size: 0.76rem;">0.00 AFN</span>
                @endif
              </div>
            </td>
            <td class="hideOnPrint"></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    
    // Live filter function
    function applyFilters() {
      var searchVal = $('#filterSearch').val().toLowerCase();
      var subtypeVal = $('#filterSubtype').val();
      var categoryVal = $('#filterCategory').val();
      var typeVal = $('#filterType').val();

      var visibleCount = 0;
      var totalQty = 0;
      var totalVal = 0;

      $('.stock-row').each(function() {
        var row = $(this);
        var subtype = row.data('subtype');
        var categoryId = row.data('category-id').toString();
        var typeId = row.data('type-id').toString();
        var searchText = row.data('search').toLowerCase();

        var matchSearch = !searchVal || searchText.indexOf(searchVal) !== -1;
        var matchSubtype = !subtypeVal || subtype === subtypeVal;
        var matchCategory = !categoryVal || categoryId === categoryVal;
        var matchType = !typeVal || typeId === typeVal;

        if (matchSearch && matchSubtype && matchCategory && matchType) {
          row.show();
          visibleCount++;
          totalQty += parseFloat(row.data('quantity'));
          totalVal += parseFloat(row.data('value'));
        } else {
          row.hide();
        }
      });

      // Update summary counts
      $('#visibleCount').text(visibleCount);
      $('#filteredQty').text(totalQty.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg');
      $('#filteredVal').text('$' + totalVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
      
      // Update local currency equivalent
      var exchRate = parseFloat('{{ $exchRate }}');
      if (exchRate > 0) {
        var localVal = totalVal / exchRate;
        $('#filteredValLocal').text(localVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' AFN');
      }

      if (visibleCount === 0) {
        $('#noStockRow').show();
      } else {
        $('#noStockRow').hide();
      }
    }

    // Trigger filters on input/change
    $('#filterSearch').on('keyup input', applyFilters);
    $('#filterSubtype, #filterCategory, #filterType').on('change', applyFilters);

    // Reset button
    $('#resetFilters').on('click', function() {
      $('#filterSearch').val('');
      $('#filterSubtype').val('');
      $('#filterCategory').val('');
      $('#filterType').val('');
      applyFilters();
    });

    // KPI Card Click Handlers for interactive filtering
    $('#yarnKpiCard').css('cursor', 'pointer').on('click', function() {
      $('#filterSubtype').val('yarn').trigger('change');
    });
    
    $('#dyeKpiCard').css('cursor', 'pointer').on('click', function() {
      $('#filterSubtype').val('dye').trigger('change');
    });
    
    $('#alertKpiCard').css('cursor', 'pointer').on('click', function() {
      // Filter for quantity < 10
      $('#filterSearch').val('');
      $('#filterSubtype').val('');
      $('#filterCategory').val('');
      $('#filterType').val('');
      
      var visibleCount = 0;
      var totalQty = 0;
      var totalVal = 0;

      $('.stock-row').each(function() {
        var row = $(this);
        var qty = parseFloat(row.data('quantity'));
        if (qty < 10) {
          row.show();
          visibleCount++;
          totalQty += qty;
          totalVal += parseFloat(row.data('value'));
        } else {
          row.hide();
        }
      });

      $('#visibleCount').text(visibleCount);
      $('#filteredQty').text(totalQty.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg');
      $('#filteredVal').text('$' + totalVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
      
      var exchRate = parseFloat('{{ $exchRate }}');
      if (exchRate > 0) {
        var localVal = totalVal / exchRate;
        $('#filteredValLocal').text(localVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' AFN');
      }

      if (visibleCount === 0) {
        $('#noStockRow').show();
      } else {
        $('#noStockRow').hide();
      }
    });

    // Initialize live totals on first load
    applyFilters();

    // Auto-hide session alerts
    window.setTimeout(function() {
      $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 4000);

  });
</script>
@endsection
