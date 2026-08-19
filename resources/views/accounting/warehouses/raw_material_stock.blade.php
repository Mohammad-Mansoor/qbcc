@extends('dsh.master')

@section('content')
<style>
  .mat-stat-card {
    border: none;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
  }
  .mat-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  }
  .mat-card-gradient-1 {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: #ffffff;
  }
  .mat-card-gradient-2 {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
  }
  .mat-card-gradient-3 {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
  }
  .mat-card-gradient-4 {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
    color: #ffffff;
  }
  .mat-stat-icon {
    font-size: 2.3rem;
    opacity: 0.85;
  }
  .mat-val-main {
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1.2;
  }
  .mat-val-sub {
    font-size: 0.92rem;
    opacity: 0.9;
    font-weight: 600;
  }
  .raw-mat-card {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    margin-bottom: 24px;
    transition: all 0.25s ease;
  }
  .raw-mat-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
  }
  .raw-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    padding: 14px 20px;
  }
  .pill-mat {
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
  }
  .pill-avail {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }
  .pill-sold {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
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
              <i class="feather icon-disc text-primary mr-2" style="color: #6366f1;"></i> 
              موجودی مواد خام (Raw Material Stock)
            </h3>
            <p class="text-muted small mb-0">گزارش تفکیکی موجودی فعال، فروش رفته و خریداری شده مواد اولیه (رنگ و تار/الیاف) به تفکیک دسته و نوعیت</p>
          </div>
          <div>
            <a href="{{ route('accounting.warehouses.raw_material_stock', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger btn-sm rounded-pill px-3 mr-1">
              <i class="feather icon-file-text mr-1"></i> خروجی PDF
            </a>
            <a href="{{ route('accounting.warehouses.raw_material_stock', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success btn-sm rounded-pill px-3 mr-1">
              <i class="feather icon-grid mr-1"></i> خروجی Excel
            </a>
            <a href="{{ route('accounting.warehouses.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
              <i class="feather icon-arrow-right mr-1"></i> بازگشت به انبارها
            </a>
          </div>
        </div>

        <!-- Top 4 Statistics Cards -->
        <div class="row mb-4">
          <!-- Card 1: Available Dye -->
          <div class="col-md-3 mb-3">
            <div class="mat-stat-card mat-card-gradient-1 p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">موجودی رنگ (Available Dye)</span>
                <i class="feather icon-droplet mat-stat-icon"></i>
              </div>
              <div class="mat-val-main mb-1">{{ number_format($totalAvailableDyeKg, 2) }} <span class="small font-weight-normal">KG</span></div>
              <div class="mat-val-sub" dir="ltr">
                $ {{ number_format($totalAvailableDyeCost, 2) }}
              </div>
            </div>
          </div>

          <!-- Card 2: Available Yarn -->
          <div class="col-md-3 mb-3">
            <div class="mat-stat-card mat-card-gradient-2 p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">موجودی تار/الیاف (Available Yarn)</span>
                <i class="feather icon-layers mat-stat-icon"></i>
              </div>
              <div class="mat-val-main mb-1">{{ number_format($totalAvailableYarnKg, 2) }} <span class="small font-weight-normal">KG</span></div>
              <div class="mat-val-sub" dir="ltr">
                $ {{ number_format($totalAvailableYarnCost, 2) }}
              </div>
            </div>
          </div>

          <!-- Card 3: Sold Dye -->
          <div class="col-md-3 mb-3">
            <div class="mat-stat-card mat-card-gradient-3 p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">رنگ فروخته شده (Sold Dye)</span>
                <i class="feather icon-shopping-cart mat-stat-icon"></i>
              </div>
              <div class="mat-val-main mb-1">{{ number_format($totalSoldDyeKg, 2) }} <span class="small font-weight-normal">KG</span></div>
              <div class="mat-val-sub" dir="ltr">
                $ {{ number_format($totalSoldDyeCost, 2) }}
              </div>
            </div>
          </div>

          <!-- Card 4: Sold Yarn -->
          <div class="col-md-3 mb-3">
            <div class="mat-stat-card mat-card-gradient-4 p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-uppercase small opacity-90">تار فروخته شده (Sold Yarn)</span>
                <i class="feather icon-trending-up mat-stat-icon"></i>
              </div>
              <div class="mat-val-main mb-1">{{ number_format($totalSoldYarnKg, 2) }} <span class="small font-weight-normal">KG</span></div>
              <div class="mat-val-sub" dir="ltr">
                $ {{ number_format($totalSoldYarnCost, 2) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Server-Side Filter Form Bar -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
          <div class="card-body py-3">
            <form method="GET" action="{{ route('accounting.warehouses.raw_material_stock') }}" id="rawMatStockFilterForm">
              <div class="row align-items-end">
                
                <!-- Category Select -->
                <div class="col-md-5 mb-2 mb-md-0">
                  <label class="font-weight-bold small text-muted mb-1">دسته مواد (Category):</label>
                  <select name="category_id" class="form-control form-control-sm select2">
                    <option value="">— همه دسته‌ها —</option>
                    @foreach($categories as $c)
                      <option value="{{ $c->material_category_id }}" {{ request('category_id') == $c->material_category_id ? 'selected' : '' }}>{{ $c->material_category }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Type Select -->
                <div class="col-md-5 mb-2 mb-md-0">
                  <label class="font-weight-bold small text-muted mb-1">نوع مواد (Material Type):</label>
                  <select name="type_id" class="form-control form-control-sm select2">
                    <option value="">— همه نوعیت‌ها —</option>
                    @foreach($types as $t)
                      <option value="{{ $t->material_type_id }}" {{ request('type_id') == $t->material_type_id ? 'selected' : '' }}>{{ $t->material_type }}</option>
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
                      <a href="{{ route('accounting.warehouses.raw_material_stock') }}" class="btn btn-outline-danger btn-sm font-weight-bold" title="پاک کردن فیلترها">
                        <i class="feather icon-refresh-cw mr-1"></i> پاک کردن
                      </a>
                    @endif
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>

        <!-- Categorized Raw Material Cards Grid -->
        <div class="row" id="rawMatCardsContainer">
          @forelse($groupedMaterials as $mat)
            <div class="col-lg-6 col-xl-4 raw-mat-card-wrapper">
              <div class="raw-mat-card">
                
                <!-- Card Header -->
                <div class="raw-card-header d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="font-weight-bold mb-1 text-dark">{{ $mat['category_name'] }}</h5>
                    <span class="badge badge-light border text-muted font-weight-normal px-2 py-1">
                      <i class="feather icon-hash mr-1 text-info"></i> {{ $mat['type_name'] }}
                    </span>
                  </div>
                  <div class="text-right">
                    <span class="badge font-weight-bold" style="background: #e0e7ff; color: #3730a3; font-size: 0.88rem; padding: 6px 10px; border-radius: 8px;" dir="ltr">
                      {{ number_format($mat['avail_kg'], 2) }} KG
                    </span>
                  </div>
                </div>

                <!-- Card Body Details -->
                <div class="card-body p-3">
                  
                  <!-- Metric Row: Available vs Sold -->
                  <div class="row mb-3">
                    <div class="col-6">
                      <div class="pill-mat pill-avail">
                        <div class="small opacity-80 mb-1"><i class="feather icon-check mr-1"></i> موجودی فعال</div>
                        <div class="font-weight-bold" dir="ltr">
                          {{ number_format($mat['avail_kg'], 2) }} KG
                        </div>
                        <div class="small font-weight-normal text-emerald" dir="ltr">
                          $ {{ number_format($mat['avail_cost'], 2) }}
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="pill-mat pill-sold">
                        <div class="small opacity-80 mb-1"><i class="feather icon-shopping-bag mr-1"></i> فروخته شده</div>
                        <div class="font-weight-bold" dir="ltr">
                          {{ number_format($mat['sold_kg'], 2) }} KG
                        </div>
                        <div class="small font-weight-normal" dir="ltr">
                          $ {{ number_format($mat['sold_cost'], 2) }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Summary Specs Table -->
                  <table class="table table-borderless table-sm mb-0 bg-light rounded text-right" style="font-size: 0.85rem;">
                    <tbody>
                      <tr>
                        <td class="text-muted"><i class="feather icon-package mr-1"></i> کل خرید (Total KG):</td>
                        <td class="font-weight-bold text-dark text-left" dir="ltr">{{ number_format($mat['purchased_kg'], 2) }} KG</td>
                      </tr>
                      <tr>
                        <td class="text-muted"><i class="feather icon-dollar-sign mr-1"></i> ارزش کل خرید (Total Cost):</td>
                        <td class="font-weight-bold text-indigo text-left" dir="ltr" style="color: #4f46e5;">$ {{ number_format($mat['purchased_cost'], 2) }}</td>
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
                <p class="mt-2 font-weight-bold">هیچ مواد خامی با مشخصات فیلتر شده ثبت نشده است.</p>
                @if($isFiltered)
                  <a href="{{ route('accounting.warehouses.raw_material_stock') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
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
