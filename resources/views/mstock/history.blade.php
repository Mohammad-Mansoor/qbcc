@extends('dsh.master')
@section('title' , 'تاریخچه حرکات گدام — Stock History')
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
    transform: translateY(-2px);
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
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
  .badge-premium {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
  }
</style>

@php
  $balance = 0;
  $totalIn = 0;
  $totalOut = 0;
  $exchRate = $afnCurrency->exchange_rate ?? 0.0125;
@endphp

@foreach($movements as $m)
  @php
    if($m->direction == 'IN') {
      $balance += $m->quantity;
      $totalIn += $m->quantity;
    } else {
      $balance -= $m->quantity;
      $totalOut += $m->quantity;
    }
  @endphp
@endforeach

{{-- Header Card --}}
<div class="card glass-card mb-4" style="border: none;">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap text-right" style="gap: 15px;">
      <div>
        <h4 style="color: #0f172a; margin: 0 0 4px 0; font-weight: 800;">
          تاریخچه حرکات: {{ $category->material_category }}
        </h4>
        <div class="d-flex align-items-center flex-row-reverse" style="gap: 8px;">
          <span class="text-muted" style="font-size: 0.95rem; font-weight: 500;">
            نوعیت مواد: <strong class="text-dark">{{ $typeModel->material_type }}</strong>
          </span>
          <span class="badge badge-premium" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); font-size: 0.7rem; padding: 2px 8px;">
            {{ ($typeModel->subtype === 'yarn') ? 'تار (Yarn)' : 'رنگ (Dye)' }}
          </span>
        </div>
      </div>
      <div>
        <a href="{{ route('material-stock.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 600;">
          <i class="fa fa-chevron-right" style="margin-left: 5px; font-size: 0.8rem;"></i> بازگشت به لیست گدام
        </a>
      </div>
    </div>
  </div>
</div>

{{-- KPI Metric Cards --}}
<div class="row">
  <!-- Card 1: Current Stock Balance -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card glass-card h-100 py-2" style="border-left: 4px solid #3b82f6 !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-primary">موجودی فعلی (Current Balance)</div>
            <div class="kpi-value">{{ number_format($balance, 2) }} kg</div>
            <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
              وضعیت: <span style="font-weight: 700; color: {{ $balance < 10 ? '#ef4444' : '#10b981' }};">
                {{ $balance < 10 ? 'ذخیره کم' : 'ذخیره مطلوب' }}
              </span>
            </div>
          </div>
          <div class="col-auto">
            <div style="background: rgba(59, 130, 246, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-cubes text-primary" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 2: Total Receipts (IN) -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card glass-card h-100 py-2" style="border-left: 4px solid #10b981 !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-success">مجموع ورودی‌ها (Total Receipts)</div>
            <div class="kpi-value">{{ number_format($totalIn, 2) }} kg</div>
            <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
              تعداد دفعات دریافت: <span style="font-weight: 700; color: #4b5563;">{{ $movements->where('direction', 'IN')->count() }} بار</span>
            </div>
          </div>
          <div class="col-auto">
            <div style="background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-arrow-down text-success" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 3: Total Issues (OUT) -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card glass-card h-100 py-2" style="border-left: 4px solid #ef4444 !important;">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2 text-right">
            <div class="kpi-title text-danger">مجموع خروجی‌ها (Total Issues)</div>
            <div class="kpi-value">{{ number_format($totalOut, 2) }} kg</div>
            <div class="text-xs text-muted mt-2" style="font-size: 0.76rem;">
              تعداد دفعات مصرف: <span style="font-weight: 700; color: #4b5563;">{{ $movements->where('direction', 'OUT')->count() }} بار</span>
            </div>
          </div>
          <div class="col-auto">
            <div style="background: rgba(239, 68, 68, 0.1); padding: 12px; border-radius: 12px; margin-right: 10px;">
              <i class="fa fa-arrow-up text-danger" style="font-size: 1.8rem; width: 24px; text-align: center;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Movements Table --}}
<div class="card shadow-sm mb-4" id="movementsTableCard" style="border: none; border-radius: 12px; overflow: hidden;">
  <div class="card-header bg-white py-3 border-0 text-right">
    <div class="d-flex align-items-center justify-content-between flex-row-reverse">
      <div>
        <h5 style="color: #0f172a; margin: 0; font-weight: 700;">دفتر معین حرکات کالا</h5>
        <small class="text-muted">لیست جزئیات تراکنش‌های خرید، فروش و انتقالات کالا</small>
      </div>
      <div>
        <button onclick="printPage('movementsTableCard')" class="btn btn-outline-primary border-0 bg-light" style="border-radius: 8px; width: 45px; height: 38px;" title="چاپ لیست">
          <i class="fa fa-print"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-premium mb-0" style="font-size: 0.9rem;">
        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
          <tr>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">تاریخ تراکنش</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">گدام</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: center;">نوعیت حرکت</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: center; color: #10b981;">ورودی (IN)</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: center; color: #ef4444;">خروجی (OUT)</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">قیمت فی کیلو (USD)</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">ارزش کل حرکت (USD)</th>
            <th style="padding: 12px 16px; font-weight: 600; text-align: right;">نمبر سند / منبع</th>
          </tr>
        </thead>
        <tbody>
        @forelse($movements as $m)
          <tr>
            <td style="padding: 12px 16px; text-align: right; color: #475569; font-size: 0.85rem;" dir="ltr">
              {{ \Carbon\Carbon::parse($m->created_at)->format('Y-m-d H:i') }}
            </td>
            
            <td style="padding: 12px 16px; text-align: right; color: #1e293b;">
              {{ $m->warehouse_name ?? 'گدام مرکزی' }}
            </td>
            
            <td style="padding: 12px 16px; text-align: center;">
              @if($m->type === 'PURCHASE')
                <span class="badge badge-premium" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);">خریداری (Purchase)</span>
              @elseif($m->type === 'SALE')
                <span class="badge badge-premium" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">فروش (Sale)</span>
              @elseif($m->type === 'TRANSFER')
                <span class="badge badge-premium" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2);">انتقال (Transfer)</span>
              @else
                <span class="badge badge-premium badge-secondary">{{ $m->type }}</span>
              @endif
            </td>
            
            <td style="padding: 12px 16px; text-align: center;" dir="ltr">
              @if($m->direction === 'IN')
                <span class="font-weight-bold text-success" style="font-size: 0.95rem;">+{{ number_format($m->quantity, 2) }}</span>
                <span class="text-muted small"> kg</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            
            <td style="padding: 12px 16px; text-align: center;" dir="ltr">
              @if($m->direction === 'OUT')
                <span class="font-weight-bold text-danger" style="font-size: 0.95rem;">-{{ number_format($m->quantity, 2) }}</span>
                <span class="text-muted small"> kg</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            
            <td style="padding: 12px 16px; text-align: right;" dir="ltr">
              <div class="d-flex flex-column align-items-end">
                <span class="font-weight-bold text-dark">${{ number_format($m->unit_cost, 2) }}</span>
                @if($afnCurrency)
                  <small class="text-muted" style="font-size: 0.72rem; margin-top: 1px;">
                    {{ number_format($m->unit_cost / $exchRate, 2) }} AFN
                  </small>
                @endif
              </div>
            </td>
            
            <td style="padding: 12px 16px; text-align: right;" dir="ltr">
              <div class="d-flex flex-column align-items-end">
                <span class="font-weight-bold text-primary">${{ number_format($m->total_cost, 2) }}</span>
                @if($afnCurrency)
                  <small class="text-success font-weight-bold" style="font-size: 0.72rem; margin-top: 1px;">
                    {{ number_format($m->total_cost / $exchRate, 2) }} AFN
                  </small>
                @endif
              </div>
            </td>
            
            <td style="padding: 12px 16px; text-align: right;">
              <span class="badge badge-light border" style="font-family: monospace; font-size: 0.8rem;">
                {{ $m->reference_type }} #{{ $m->reference_id }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="fa fa-inbox text-muted mb-2 d-block" style="font-size: 2.5rem; opacity: 0.5;"></i>
              <strong>هیچ حرکتی برای این کالا ثبت نشده است</strong>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
