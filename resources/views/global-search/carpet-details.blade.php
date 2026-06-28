@extends('dsh.master')
@section('title', 'اطلاعات کامل قالین - ' . $carpet->carpet_no)
@section('content')

<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
        transition: transform 0.3s ease;
    }
    .glass-panel:hover {
        transform: translateY(-2px);
    }
    .carpet-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
    }
    .carpet-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        transform: rotate(45deg);
    }
    .info-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        font-weight: 700;
    }
    .info-value {
        font-size: 15px;
        color: #0f172a;
        font-weight: 800;
    }
    .section-title {
        font-size: 18px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .section-title i {
        margin-left: 10px;
        color: #3b82f6;
    }
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline-item {
        position: relative;
        padding-right: 40px;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        right: 14px;
        top: 0;
        bottom: -20px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-dot {
        position: absolute;
        right: 6px;
        top: 0;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #3b82f6;
        border: 4px solid #eff6ff;
        z-index: 1;
    }
    .timeline-content {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="carpet-hero d-flex justify-content-between align-items-center">
            <div>
                <h1 class="font-weight-bold mb-2 text-white" style="font-size: 3rem; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">{{ $carpet->carpet_no }}</h1>
                <h5 class="text-white-50 mb-0">نقشه: <span class="text-white font-weight-bold">{{ $carpet->map_number ?? 'نامشخص' }}</span></h5>
            </div>
            <div class="text-left">
                <span class="badge badge-light px-4 py-2 mb-2 shadow-sm" style="font-size: 14px; color: #1e3a8a; border-radius: 30px;">
                    <i class="feather icon-activity mr-2"></i> {{ $carpet->status_string }}
                </span>
                <h3 class="text-white font-weight-bold" style="direction: ltr;">{{ number_format($carpet->area, 2) }} m²</h3>
                <small class="text-white-50">{{ $carpet->width }}m × {{ $carpet->height }}m</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Right Column: Info Panels -->
    <div class="col-lg-9 col-md-8 order-md-2">
        <div class="row">
            <!-- Basic Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="glass-panel h-100">
                    <h4 class="section-title"><i class="feather icon-info"></i> مشخصات اصلی</h4>
                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="info-label">نوعیت قالین</div>
                            <div class="info-value">{{ $carpet->type->carpet_type ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="info-label">کوالیتی</div>
                            <div class="info-value">{{ $carpet->quality->quality ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="info-label">رنگ</div>
                            <div class="info-value">{{ $carpet->color ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="info-label">سیستم/ID</div>
                            <div class="info-value text-primary" style="direction: ltr;">{{ $carpet->carpet_id }}</div>
                        </div>
                        <div class="col-12 mb-4">
                            <div class="info-label">تاریخ ثبت سیستم</div>
                            <div class="info-value" style="direction: ltr; text-align: right;">{{ $carpet->date }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agent & Purchase Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="glass-panel h-100">
                    <h4 class="section-title"><i class="feather icon-shopping-cart"></i> اطلاعات خرید</h4>
                    
                    <div class="mb-4">
                        <div class="info-label">نماینده / فروشنده</div>
                        <div class="info-value text-primary">
                            <i class="feather icon-user mr-1"></i> {{ $carpet->agent && $carpet->agent->user ? $carpet->agent->user->name : 'N/A' }}
                        </div>
                    </div>

                    @if($carpet->purchaseInvoice)
                    <div class="mb-4">
                        <div class="info-label">بل خرید (Purchase Bill)</div>
                        <div class="info-value">
                            <a href="/dashboard/check-book/{{ $carpet->purchase_invoice_id }}" class="text-info text-decoration-underline" style="direction: ltr; display: inline-block;">
                                {{ $carpet->purchaseInvoice->invoice_number }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Order Details -->
                    <div class="mb-4">
                        <div class="info-label">شماره فرمایش (Order No)</div>
                        <div class="info-value text-primary">
                            <i class="feather icon-file-text mr-1"></i> {{ $carpet->carpet_order->order_number ?? 'بدون فرمایش' }}
                        </div>
                    </div>
                    @if($carpet->carpet_order && $carpet->carpet_order->design_number)
                    <div class="mb-4">
                        <div class="info-label">نمبر دیزاین (Design No)</div>
                        <div class="info-value">
                            {{ $carpet->carpet_order->design_number }}
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="info-label">قیمت فی متر</div>
                            <div class="info-value text-success" style="direction: ltr; text-align: right;">${{ number_format($carpet->price, 2) }}</div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="info-label">قیمت کل خرید</div>
                            <div class="info-value text-success" style="direction: ltr; text-align: right;">${{ number_format($carpet->total_price, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Info -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="glass-panel h-100">
                    <h4 class="section-title"><i class="feather icon-trending-up"></i> اطلاعات فروش</h4>
                    
                    @if($carpet->sale)
                        <div class="mb-4">
                            <div class="info-label">کد مشتری (Customer Code)</div>
                            <div class="info-value text-primary">
                                <i class="feather icon-user mr-1"></i> {{ $carpet->sale->customer ? $carpet->sale->customer->customer_code : 'N/A' }}
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">تاریخ فروش (Sold Date)</div>
                            <div class="info-value" style="direction: ltr; text-align: right;">{{ $carpet->sale->date }}</div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="feather icon-package text-muted" style="font-size: 3rem; opacity: 0.2;"></i>
                            <p class="text-muted mt-3 mb-0">این قالین هنوز فروخته نشده است.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Left Column: Carpet Image -->
    <div class="col-lg-3 col-md-4 order-md-1 mb-4">
        <div class="glass-panel h-100 d-flex flex-column">
            <h4 class="section-title mb-3"><i class="feather icon-image"></i> تصویر قالین</h4>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center bg-light rounded-lg overflow-hidden" style="min-height: 250px; border: 1px solid rgba(0,0,0,0.05);">
                @if($carpet->carpet_image)
                    <img src="/{{ $carpet->carpet_image }}" class="img-fluid rounded-lg shadow-sm"
                        style="max-height: 350px; object-fit: cover; width: 100%; border-radius: 12px; cursor: pointer;"
                        onclick="showImageModal('/{{ $carpet->carpet_image }}', '{{ $carpet->carpet_no }}')">
                @else
                    <div class="text-center p-4">
                        <i class="feather icon-image text-muted" style="font-size: 48px; opacity: 0.5;"></i>
                        <p class="text-muted small mt-2 mb-0">تصویری ثبت نشده است</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Operations Timeline -->
    <div class="col-lg-8">
        <div class="glass-panel">
            <h4 class="section-title"><i class="feather icon-layers"></i> مراحل عملیاتی و خدمات</h4>
            
            <div class="timeline">
                
                @if($carpet->repair && $carpet->repair->count() > 0)
                    @foreach($carpet->repair as $rep)
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content border-warning border-left-0 border-right-0 border-top-0" style="border-bottom-width: 3px;">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-bold text-warning mb-1"><i class="feather icon-scissors"></i> کچایی (Repair)</h6>
                                <span class="badge badge-warning text-white" style="direction:ltr;">{{ $rep->date }}</span>
                            </div>
                            <p class="mb-1 text-muted text-sm">تیم/کارمند: {{ $rep->team->name ?? 'N/A' }}</p>
                            <p class="mb-0 font-weight-bold text-dark" style="direction: ltr; text-align: right;">Cost: ${{ number_format($rep->total_price, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                @endif

                @if($carpet->carpet_wash)
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content border-info border-left-0 border-right-0 border-top-0" style="border-bottom-width: 3px;">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-bold text-info mb-1"><i class="feather icon-droplet"></i> شست (Wash)</h6>
                                <span class="badge badge-info text-white" style="direction:ltr;">{{ $carpet->carpet_wash->date }}</span>
                            </div>
                            <p class="mb-1 text-muted text-sm">شماره شست: {{ $carpet->carpet_wash->wash_number_sh ?? 'N/A' }} | تیم: {{ $carpet->carpet_wash->washing_team->name ?? 'N/A' }}</p>
                            <p class="mb-0 font-weight-bold text-dark" style="direction: ltr; text-align: right;">Cost: ${{ number_format($carpet->carpet_wash->total_price, 2) }}</p>
                        </div>
                    </div>
                @endif

                @if($carpet->finishing_works && $carpet->finishing_works->count() > 0)
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content border-success border-left-0 border-right-0 border-top-0" style="border-bottom-width: 3px;">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-bold text-success mb-2"><i class="feather icon-check-circle"></i> تیاری (Finishing)</h6>
                                <span class="badge badge-success text-white">تکمیل شده</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th>تیم/تاریخ</th>
                                            <th>عملیات</th>
                                            <th class="text-right">هزینه ($)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalFinishCost = 0; @endphp
                                        @foreach($carpet->finishing_works as $fw)
                                        @php $totalFinishCost += $fw->price; @endphp
                                        <tr>
                                            <td>{{ $fw->team->name ?? 'N/A' }} <br><small class="text-muted" style="direction: ltr;">{{ $fw->date }}</small></td>
                                            <td>
                                                <span class="badge badge-light border">{{ $fw->category ? $fw->category->category : 'N/A' }}</span>
                                            </td>
                                            <td class="text-right font-weight-bold" style="direction: ltr;">${{ number_format($fw->price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="border-top">
                                            <td colspan="2" class="text-left font-weight-bold text-dark">مجموع هزینه تیاری:</td>
                                            <td class="text-right font-weight-bold text-success" style="direction: ltr;">${{ number_format($totalFinishCost, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$carpet->repair->count() && !$carpet->carpet_wash && !$carpet->finishing_works->count())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">هیچ عملیاتی روی این قالین ثبت نشده است.</p>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
    
    <!-- Warehouse & Asset Val -->
    <div class="col-lg-4">
        <div class="glass-panel h-100">
            <h4 class="section-title"><i class="feather icon-box"></i> موقعیت و ارزش گذاری</h4>
            
            <div class="mb-4">
                <div class="info-label">موقعیت فعلی گدام</div>
                <div class="info-value text-primary">
                    <i class="feather icon-map-pin mr-1"></i> {{ $carpet->warehouse->name ?? 'گدام مرکزی' }}
                </div>
            </div>

            @php
                $kachaeeCost = $carpet->repair ? $carpet->repair->sum('total_price') : 0;
                $washCost = $carpet->carpet_wash ? $carpet->carpet_wash->total_price : 0;
                $finishingCost = $carpet->finishing_works ? $carpet->finishing_works->sum('price') : 0;
                
                // The database already accumulates all costs into total_price
                $totalAssetValue = $carpet->total_price;
                // Reverse-engineer the base purchase cost for visual display
                $purchaseCost = $totalAssetValue - ($kachaeeCost + $washCost + $finishingCost);
            @endphp

            <div class="bg-light p-3 rounded-lg border">
                <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">ارزش دفتری (Book Value)</h6>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">قیمت خرید:</span>
                    <span class="font-weight-bold" style="direction: ltr;">${{ number_format($purchaseCost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">مصارف کچایی:</span>
                    <span class="font-weight-bold" style="direction: ltr;">${{ number_format($kachaeeCost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">مصارف شست:</span>
                    <span class="font-weight-bold" style="direction: ltr;">${{ number_format($washCost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">مصارف تیاری:</span>
                    <span class="font-weight-bold" style="direction: ltr;">${{ number_format($finishingCost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                    <span class="font-weight-bold text-primary">ارزش نهایی (COGS):</span>
                    <span class="font-weight-bold text-primary" style="direction: ltr; font-size: 16px;">${{ number_format($totalAssetValue, 2) }}</span>
                </div>
                
                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                    <span class="font-weight-bold text-muted">Cost per m² (هزینه فی متر):</span>
                    <span class="font-weight-bold text-muted" style="direction: ltr;">${{ $carpet->area > 0 ? number_format($totalAssetValue / $carpet->area, 2) : 0 }}</span>
                </div>
                
                @if($carpet->sale)
                <div class="d-flex justify-content-between mt-3 pt-2 border-top bg-success text-white p-2 rounded">
                    <span class="font-weight-bold">سود خالص (Gross Profit):</span>
                    <span class="font-weight-bold" style="direction: ltr; font-size: 16px;">${{ number_format($carpet->sale->sale_cost_total - $totalAssetValue, 2) }}</span>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- IMAGE PREVIEW MODAL -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="text-right mb-2">
                <button type="button" class="btn btn-white btn-sm rounded-circle shadow"
                    data-dismiss="modal" style="background: white; border: none; width: 30px; height: 30px; font-weight: bold;">&times;</button>
            </div>
            <img src="" id="fullPreviewImage" class="img-fluid rounded shadow-lg mx-auto d-block"
                style="max-height: 85vh; border-radius: 16px;">
            <div class="text-center mt-3 text-white h5 font-weight-bold" id="previewTitle"></div>
        </div>
    </div>
</div>

<script>
function showImageModal(src, title) {
    document.getElementById('fullPreviewImage').src = src;
    document.getElementById('previewTitle').innerText = 'تصویر قالین نمبر: ' + title;
    $('#imagePreviewModal').modal('show');
}
</script>

@endsection
