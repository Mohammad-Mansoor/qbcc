@extends('dsh.master')

@section('content')
<style>
  .stock-stat-card {
    border: none;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
  }
  .stock-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  }
  .stat-card-gradient-1 {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
  }
  .stat-card-gradient-2 {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff;
  }
  .stat-card-gradient-3 {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #ffffff;
  }
  .stat-icon {
    font-size: 2.5rem;
    opacity: 0.85;
  }
  .stat-val-main {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1.2;
  }
  .stat-val-sub {
    font-size: 0.95rem;
    opacity: 0.9;
    font-weight: 600;
  }
  .category-stock-card {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    margin-bottom: 24px;
    transition: all 0.25s ease;
  }
  .category-stock-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
  }
  .category-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    padding: 14px 20px;
  }
  .pill-metric {
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.88rem;
  }
  .pill-wip {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fef3c7;
  }
  .pill-ready {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #d1fae5;
  }
</style>

<div class="pcoded-content">
  <div class="pcoded-inner-content">
    <div class="main-body">
      <div class="page-wrapper">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h3 class="font-weight-bold text-dark mb-1">
              <i class="feather icon-package text-emerald mr-2" style="color: #10b981;"></i> 
              موجودی قالین‌ها (Carpet Available Stock)
            </h3>
            <p class="text-muted small mb-0">گزارش تفکیکی موجودی قالین‌ها، کالاهای در جریان تولید (WIP)، و آماده فروش به تفکیک کیفیت و نوعیت</p>
          </div>
          <div>
            <a href="{{ route('accounting.warehouses.carpet_stock', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger btn-sm rounded-pill px-3 mr-1">
              <i class="feather icon-file-text mr-1"></i> خروجی PDF
            </a>
            <a href="{{ route('accounting.warehouses.carpet_stock', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success btn-sm rounded-pill px-3 mr-1">
              <i class="feather icon-grid mr-1"></i> خروجی Excel
            </a>
            <a href="{{ route('accounting.warehouses.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
              <i class="feather icon-arrow-right mr-1"></i> بازگشت به انبارها
            </a>
          </div>
        </div>

        <!-- Top 3 Statistics Cards -->
        <div class="row mb-4">
          <!-- Card 1: Total Inventory Stock -->
          <div class="col-md-4 mb-3">
            <div class="stock-stat-card stat-card-gradient-1 p-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">موجودی کل قالین‌ها (Total Inventory)</span>
                <i class="feather icon-box stat-icon"></i>
              </div>
              <div class="stat-val-main mb-1">{{ number_format($totalAvailableQty) }} <span class="small font-weight-normal">تخته</span></div>
              <div class="stat-val-sub">
                <i class="feather icon-maximize-2 mr-1"></i> {{ number_format($totalAvailableSqm, 2) }} مـتر مربع (m²)
              </div>
            </div>
          </div>

          <!-- Card 2: Total Sold Carpets -->
          <div class="col-md-4 mb-3">
            <div class="stock-stat-card stat-card-gradient-2 p-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">قالین‌های فروخته شده (Total Sold)</span>
                <i class="feather icon-shopping-cart stat-icon"></i>
              </div>
              <div class="stat-val-main mb-1">{{ number_format($totalSoldQty) }} <span class="small font-weight-normal">تخته</span></div>
              <div class="stat-val-sub">
                <i class="feather icon-maximize-2 mr-1"></i> {{ number_format($totalSoldSqm, 2) }} مـتر مربع (m²)
              </div>
            </div>
          </div>

          <!-- Card 3: Ready for Sale Carpets -->
          <div class="col-md-4 mb-3">
            <div class="stock-stat-card stat-card-gradient-3 p-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">قالین‌های آماده فروش (Ready to Sale)</span>
                <i class="feather icon-check-circle stat-icon"></i>
              </div>
              <div class="stat-val-main mb-1">{{ number_format($totalReadyQty) }} <span class="small font-weight-normal">تخته</span></div>
              <div class="stat-val-sub">
                <i class="feather icon-maximize-2 mr-1"></i> {{ number_format($totalReadySqm, 2) }} مـتر مربع (m²)
              </div>
            </div>
          </div>
        </div>

        <!-- Server-Side Filter Form Bar -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
          <div class="card-body py-3">
            <form method="GET" action="{{ route('accounting.warehouses.carpet_stock') }}" id="carpetStockFilterForm">
              <div class="row align-items-end">
                
                <!-- Quality / Category Select -->
                <div class="col-md-5 mb-2 mb-md-0">
                  <label class="font-weight-bold small text-muted mb-1">کیفیت / دسته قالین (Quality):</label>
                  <select name="quality_id" class="form-control form-control-sm select2">
                    <option value="">— همه کیفیت‌ها —</option>
                    @foreach($qualities as $q)
                      <option value="{{ $q->id }}" {{ request('quality_id') == $q->id ? 'selected' : '' }}>{{ $q->quality }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Carpet Type Select -->
                <div class="col-md-5 mb-2 mb-md-0">
                  <label class="font-weight-bold small text-muted mb-1">نوعیت / دیزاین (Carpet Type):</label>
                  <select name="type_id" class="form-control form-control-sm select2">
                    <option value="">— همه نوعیت‌ها —</option>
                    @foreach($carpetTypes as $t)
                      <option value="{{ $t->carpet_type_id }}" {{ request('type_id') == $t->carpet_type_id ? 'selected' : '' }}>{{ $t->carpet_type }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Action Buttons: Apply & Clear -->
                <div class="col-md-2 text-right">
                  <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                      <i class="feather icon-filter mr-1"></i> اعمال فیلتر
                    </button>
                    @if($isFiltered)
                      <a href="{{ route('accounting.warehouses.carpet_stock') }}" class="btn btn-outline-danger btn-sm font-weight-bold" title="پاک کردن فیلترها">
                        <i class="feather icon-refresh-cw mr-1"></i> پاک کردن
                      </a>
                    @endif
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>

        <!-- Categorized Inventory Cards Grid -->
        <div class="row" id="carpetStockCardsContainer">
          @forelse($groupedCategories as $group)
            <div class="col-lg-6 col-xl-4 carpet-group-card-wrapper">
              <div class="category-stock-card">
                
                <!-- Card Header -->
                <div class="category-card-header d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="font-weight-bold mb-1 text-dark">{{ $group->quality_name }}</h5>
                    <span class="badge badge-light border text-muted font-weight-normal px-2 py-1">
                      <i class="feather icon-tag mr-1 text-primary"></i> {{ $group->type_name }}
                    </span>
                  </div>
                  <div class="text-right">
                    <span class="badge badge-emerald font-weight-bold" style="background: #10b981; color: #fff; font-size: 0.9rem; padding: 6px 12px; border-radius: 8px;" dir="ltr">
                      $ {{ number_format($group->total_cost, 2) }}
                    </span>
                  </div>
                </div>

                <!-- Card Body Metric Details -->
                <div class="card-body p-3">
                  
                  <!-- Metric Row 1: WIP vs Ready -->
                  <div class="row mb-3">
                    <div class="col-6">
                      <div class="pill-metric pill-wip">
                        <div class="small opacity-80 mb-1"><i class="feather icon-clock mr-1"></i> در حال تولید (WIP)</div>
                        <div class="font-weight-bold" style="font-size: 1.05rem;">
                          {{ number_format($group->wip_qty) }} تخته
                        </div>
                        <div class="small font-weight-normal" dir="ltr">
                          {{ number_format($group->wip_sqm, 2) }} m²
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="pill-metric pill-ready">
                        <div class="small opacity-80 mb-1"><i class="feather icon-check-circle mr-1"></i> آماده فروش (Ready)</div>
                        <div class="font-weight-bold" style="font-size: 1.05rem;">
                          {{ number_format($group->ready_qty) }} تخته
                        </div>
                        <div class="small font-weight-normal" dir="ltr">
                          {{ number_format($group->ready_sqm, 2) }} m²
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Summary Specs Table -->
                  <table class="table table-borderless table-sm mb-0 bg-light rounded text-right" style="font-size: 0.85rem;">
                    <tbody>
                      <tr>
                        <td class="text-muted"><i class="feather icon-maximize-2 mr-1"></i> کل مساحت (Total SQM):</td>
                        <td class="font-weight-bold text-dark text-left" dir="ltr">{{ number_format($group->total_sqm, 2) }} m²</td>
                      </tr>
                      <tr>
                        <td class="text-muted"><i class="feather icon-layers mr-1"></i> مجموع کل موجودی (Pcs):</td>
                        <td class="font-weight-bold text-dark text-left">{{ number_format($group->total_count) }} تخته</td>
                      </tr>
                      <tr>
                        <td class="text-muted"><i class="feather icon-dollar-sign mr-1"></i> ارزش مالی کل (Total Cost):</td>
                        <td class="font-weight-bold text-success text-left" dir="ltr">$ {{ number_format($group->total_cost, 2) }}</td>
                      </tr>
                    </tbody>
                  </table>

                </div>

              </div>
            </div>
          @empty
            <div class="col-12 text-center py-5">
              <div class="text-muted">
                <i class="feather icon-inbox" style="font-size: 3rem; opacity: 0.4;"></i>
                <p class="mt-2 font-weight-bold">هیچ قالینی با مشخصات فیلتر شده یافت نشد.</p>
                @if($isFiltered)
                  <a href="{{ route('accounting.warehouses.carpet_stock') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                    <i class="feather icon-refresh-cw mr-1"></i> پاک کردن فیلترها
                  </a>
                @endif
              </div>
            </div>
          @endforelse
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
