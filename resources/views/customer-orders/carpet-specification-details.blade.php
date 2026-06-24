@extends('dsh.master')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(226, 232, 240, 0.8);
    }

    body {
        background-color: #f8fafc;
        font-family: 'Inter', 'Outfit', sans-serif;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        padding: 2rem;
    }

    .order-header-premium {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);
        position: relative;
        overflow: hidden;
    }

    .order-header-premium::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }

    .detail-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 1.1rem;
        color: #0f172a;
        font-weight: 700;
    }

    .section-title {
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 10px;
        margin-bottom: 20px;
        color: #334155;
        font-weight: bold;
    }

    .carpet-image-container {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        height: 100%;
        min-height: 300px;
        background-color: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .carpet-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-premium {
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .badge-premium-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .badge-premium-progress { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-premium-completed { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }

    @media print {
        .hideOnPrint { display: none !important; }
        .glass-card { box-shadow: none !important; border: 1px solid #ccc !important; padding: 1rem !important; }
        .order-header-premium { background: #fff !important; color: #000 !important; border: 1px solid #ccc !important; box-shadow: none !important; }
        .order-header-premium * { color: #000 !important; }
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="order-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl; text-align: right;">
        <div>
            <h2 class="text-white mb-2 font-weight-bold">جزئیات کامل مشخصات قالین</h2>
            <p class="mb-1 opacity-75">مربوط به فرمایش: <strong>#{{ $customer_order->order_name ?? 'نامشخص' }}</strong></p>
            <p class="mb-0 opacity-75">مشتری: {{ $customer_order->customer->name ?? 'N/A' }}</p>
        </div>
        <div class="hideOnPrint">
            <button class="btn btn-light font-weight-bold shadow-sm text-dark mr-2" onclick="window.print()">
                <i class="fa fa-print ml-1"></i> چاپ جزئیات
            </button>
            <a href="/dashboard/customer-order-details/{{ $customer_order->co_id }}" class="btn btn-light font-weight-bold shadow-sm text-dark">
                <i class="fa fa-arrow-right ml-1"></i> بازگشت
            </a>
        </div>
    </div>

    <div class="row" style="direction: rtl; text-align: right;">
        <!-- Image Section -->
        <div class="col-lg-4 mb-4">
            <div class="carpet-image-container">
                @if($carpet->photo)
                    <img src="/{{ $carpet->photo }}" alt="Carpet Image" onerror="this.onerror=null; this.src='/images/logo.png';">
                @else
                    <div class="text-muted text-center py-5">
                        <i class="fa fa-image fa-4x mb-3 opacity-50"></i>
                        <h5 class="font-weight-bold">عکسی ثبت نشده است</h5>
                    </div>
                @endif
            </div>
        </div>

        <!-- Details Section -->
        <div class="col-lg-8 mb-4">
            <div class="glass-card h-100">
                <!-- وضعیت کار -->
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="fa fa-info-circle text-primary ml-2"></i> وضعیت و زمان‌بندی</h5>
                    <div>
                        @if($carpet->current_status == 'pending')
                            <span class="badge-premium badge-premium-pending">معلق (Pending)</span>
                        @elseif($carpet->current_status == 'in_progress')
                            <span class="badge-premium badge-premium-progress">در حال کار (In Progress)</span>
                        @elseif($carpet->current_status == 'completed')
                            <span class="badge-premium badge-premium-completed">تکمیل شده (Completed)</span>
                        @else
                            <span class="badge badge-secondary">{{ $carpet->current_status }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="detail-label">تاریخ شروع بافت</div>
                        <div class="detail-value text-success"><i class="fa fa-calendar-alt ml-1"></i>{{ $carpet->start_date ?: 'ثبت نشده' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detail-label">تاریخ تسلیمی (تخمینی)</div>
                        <div class="detail-value text-danger"><i class="fa fa-calendar-times ml-1"></i>{{ $carpet->end_date ?: 'ثبت نشده' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detail-label">کد بافنده</div>
                        <div class="detail-value">{{ $carpet->weaver_code ?: 'نامشخص' }}</div>
                    </div>
                </div>

                <!-- مشخصات فزیکی -->
                <h5 class="section-title"><i class="fa fa-ruler-combined text-primary ml-2"></i> مشخصات فزیکی و ابعاد</h5>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="detail-label">کیفیت (Quality)</div>
                        <div class="detail-value">{{ $carpet->quality ?: '-' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="detail-label">طول</div>
                        <div class="detail-value">{{ $carpet->height ?: '0' }} متر</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="detail-label">عرض</div>
                        <div class="detail-value">{{ $carpet->width ?: '0' }} متر</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="detail-label">مساحت کل</div>
                        <div class="detail-value text-primary">{{ number_format((float)$carpet->area, 2) }} m²</div>
                    </div>
                </div>

                <!-- جزئیات تخنیکی -->
                <h5 class="section-title"><i class="fa fa-cogs text-primary ml-2"></i> جزئیات تخنیکی بافت</h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">تار (Warp)</div>
                        <div class="detail-value">{{ $carpet->warp ?: '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">پود (Weft)</div>
                        <div class="detail-value">{{ $carpet->weft ?: '-' }}</div>
                    </div>
                </div>

                <!-- خروجی نهایی -->
                <h5 class="section-title"><i class="fa fa-box text-primary ml-2"></i> خروجی نهایی</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="detail-label">نمبر قالین (تکمیل شده)</div>
                        <div class="detail-value">
                            @if($carpet->carpet_number)
                                <span class="badge badge-success px-3 py-2" style="font-size: 1.1rem;">{{ $carpet->carpet_number }}</span>
                            @else
                                <span class="text-muted"><i class="fa fa-info-circle ml-1"></i> هنوز تکمیل و نمبر دهی نشده است.</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
