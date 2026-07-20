@extends('dsh.master')
@section('title', 'اطلاعات کامل تیاری قالین - ' . $finish->carpet->carpet_no)
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
    .finishing-hero {
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
        color: white;
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        position: relative;
        overflow: hidden;
    }
    .finishing-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
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
        color: #047857;
        margin-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .section-title i {
        margin-left: 10px;
        color: #10b981;
    }
    
    @media print {
        .glass-panel {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
        .finishing-hero {
            background: #f8f9fa !important;
            color: #000 !important;
            box-shadow: none !important;
            border: 1px solid #000;
        }
        .finishing-hero * {
            color: #000 !important;
            text-shadow: none !important;
        }
        .hideOnPrint {
            display: none !important;
        }
        body {
            background: #fff;
        }
    }
</style>

<div class="row mb-4" id="details">
    <div class="col-12">
        <div class="finishing-hero d-flex justify-content-between align-items-center">
            <div>
                <h1 class="font-weight-bold mb-2 text-white" style="font-size: 3rem; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">{{ $finish->carpet->carpet_no }}</h1>
                <h5 class="text-white-50 mb-0">تعداد عملیات انجام شده: <span class="text-white font-weight-bold">{{ $finish->carpet->finishing_works->count() }}</span></h5>
            </div>
            <div class="text-left" style="z-index: 10;">
                <button class="btn btn-light rounded-pill px-4 shadow-sm mb-2 hideOnPrint" onclick="window.print()">
                    <i class="feather icon-printer mr-1"></i> چاپ
                </button>
                <div class="mt-2">
                    <span class="badge badge-light px-3 py-2 shadow-sm" style="font-size: 14px; color: #047857; border-radius: 30px;">
                        <i class="feather icon-calendar mr-1"></i> تاریخ آخرین عملیات: {{ $finish->carpet->finishing_works->max('date') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Basic Carpet Info -->
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="glass-panel h-100">
            <h4 class="section-title"><i class="feather icon-info"></i> مشخصات عمومی قالین</h4>
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="info-label">شماره قالین</div>
                    <div class="info-value text-primary font-weight-bold" style="font-size: 18px;">{{ $finish->carpet->carpet_no }}</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">شماره فرمایش</div>
                    <div class="info-value">{{ $finish->carpet->carpet_order ? $finish->carpet->carpet_order->order_number : 'ندارد' }}</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">طول قالین</div>
                    <div class="info-value" style="direction: ltr; text-align: right;">{{ $newCarpet->height }} m</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">عرض قالین</div>
                    <div class="info-value" style="direction: ltr; text-align: right;">{{ $newCarpet->width }} m</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">مساحت قالین</div>
                    <div class="info-value" style="direction: ltr; text-align: right;">{{ $newCarpet->area }} m²</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">شماره نقشه</div>
                    <div class="info-value">{{ $finish->carpet->map_number ?? 'N/A' }}</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">زمینه قالین</div>
                    <div class="info-value">{{ $finish->carpet->field ?? 'N/A' }}</div>
                </div>
                <div class="col-6 mb-4">
                    <div class="info-label">حاشیه قالین</div>
                    <div class="info-value">{{ $finish->carpet->margin ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Finishing Details Info -->
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="glass-panel h-100">
            <h4 class="section-title"><i class="feather icon-check-circle"></i> لیست عملیات تیاری قالین</h4>
            
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th>عملیات</th>
                            <th>تیم اجرایی</th>
                            <th class="text-center">نرخ واحد (Rate)</th>
                            <th class="text-right">هزینه کل ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAllFinish = 0; @endphp
                        @foreach($finish->carpet->finishing_works as $fw)
                            @php 
                                $totalAllFinish += $fw->price; // Base USD price
                                
                                $fwTotalOriginal = ($fw->currency_code == 'AFN') ? $fw->price_af : $fw->price;
                                $fwUnitPrice = 0;
                                $fwUnitLabel = '';
                                $fwCategoryId = $fw->category_id;
                                
                                if (in_array($fwCategoryId, [1, 3, 5, 6, 7, 9])) {
                                    $fwUnitPrice = $newCarpet->area > 0 ? ($fwTotalOriginal / $newCarpet->area) : 0;
                                    $fwUnitLabel = '/m²';
                                } elseif (in_array($fwCategoryId, [4, 8])) {
                                    $fwUnitPrice = $newCarpet->height > 0 ? ($fwTotalOriginal / ($newCarpet->height * 2)) : 0;
                                    $fwUnitLabel = '/m';
                                } elseif ($fwCategoryId == 2) {
                                    $fwUnitPrice = $fwTotalOriginal;
                                    $fwUnitLabel = '(ثابت)';
                                }
                                
                                $isCurrent = ($fw->id == $finish->id);
                            @endphp
                            <tr class="{{ $isCurrent ? 'bg-light' : '' }}" style="{{ $isCurrent ? 'border-left: 4px solid #10b981;' : '' }}">
                                <td>
                                    <span class="badge {{ $isCurrent ? 'badge-success' : 'badge-light border' }} px-2 py-1">
                                        {{ $fw->category->category }}
                                    </span>
                                </td>
                                <td>{{ $fw->team->name ?? 'N/A' }} <br><small class="text-muted" style="direction: ltr;">{{ $fw->date }}</small></td>
                                <td class="text-center text-muted" style="direction: ltr;">
                                    <small>{{ number_format($fwUnitPrice, 2) }} {{ $fwUnitLabel }}<br>({{ $fw->currency_code ?? 'USD' }})</small>
                                </td>
                                <td class="text-right font-weight-bold" style="direction: ltr;">
                                    ${{ number_format($fw->price, 2) }}
                                </td>
                            </tr>
                            
                            @if($fw->description && $isCurrent)
                            <tr class="bg-light">
                                <td colspan="4" class="pt-0 border-0">
                                    <p class="text-dark mb-0 ml-2 mr-2 bg-white p-2 border rounded text-sm text-right">
                                        <i class="feather icon-file-text text-muted"></i> {{ $fw->description }}
                                    </p>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td colspan="3" class="text-left font-weight-bold text-dark pt-3">مجموع تمامی عملیات تیاری (USD):</td>
                            <td class="text-right font-weight-bold text-success pt-3" style="direction: ltr; font-size: 18px;">
                                ${{ number_format($totalAllFinish, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
