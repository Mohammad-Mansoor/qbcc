@extends('dsh.master')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(226, 232, 240, 0.8);
    }

    body {
        background-color: #f8fafc;
        font-family: 'Inter', 'Outfit', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
    }

    .order-header-premium {
        background: var(--primary-gradient);
        color: white;
        padding: 2.25rem;
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

    /* Stat Cards */
    .stat-card {
        padding: 1.25rem;
        border-radius: 16px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
    }
    .stat-card-primary { background: var(--primary-gradient); }
    .stat-card-warning { background: var(--warning-gradient); }
    .stat-card-info { background: var(--info-gradient); }
    .stat-card-success { background: var(--success-gradient); }
    .stat-card-danger { background: var(--danger-gradient); }
    
    .stat-icon {
        position: absolute;
        left: 1.25rem;
        bottom: 0.75rem;
        font-size: 3rem;
        opacity: 0.15;
    }

    /* Custom Badges */
    .badge-premium {
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-premium-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .badge-premium-progress { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-premium-completed { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }

    /* Custom progress bar */
    .progress-premium {
        height: 8px;
        border-radius: 9999px;
        background-color: #f1f5f9;
        overflow: hidden;
        position: relative;
    }
    .progress-bar-premium {
        height: 100%;
        border-radius: 9999px;
        background: var(--info-gradient);
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .progress-bar-completed {
        background: var(--success-gradient);
    }

    /* Table modifications */
    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        border-top: none;
        padding: 1rem 1.5rem;
    }
    .table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Custom inline select */
    .select-status-premium {
        padding: 0.4rem 2rem 0.4rem 0.8rem;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background-color: #fff;
        font-size: 0.825rem;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
        width: 140px;
    }
    .select-status-premium:focus {
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .select-status-premium.status-pending { color: #d97706; border-color: #fde68a; background-color: #fffbeb; }
    .select-status-premium.status-in_progress { color: #2563eb; border-color: #bfdbfe; background-color: #eff6ff; }
    .select-status-premium.status-completed { color: #059669; border-color: #a7f3d0; background-color: #ecfdf5; }

    /* Modals styling */
    .modal-content-premium {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .modal-header-premium {
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem 2rem;
    }
    .modal-body-premium {
        padding: 2rem;
    }

    .form-control-premium {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }
    .form-control-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .btn-premium {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
    }
    .btn-premium:hover {
        opacity: 0.95;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        color: white;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 5px;
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: translateY(-2px);
    }

    /* Print layout */
    @media print {
        .hideOnPrint { display: none !important; }
        .glass-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

@php
    $totalCarpets = count($customer_order_details);
    $completedCarpets = $customer_order_details->where('current_status', 'completed')->count();
    $inProgressCarpets = $customer_order_details->where('current_status', 'in_progress')->count();
    $pendingCarpets = $customer_order_details->where('current_status', 'pending')->count();
    $totalArea = $customer_order_details->sum('area');
    $totalAmount = $customer_order_details->sum('total_amount');
    $progressPercentage = $totalCarpets > 0 ? round(($completedCarpets / $totalCarpets) * 100) : 0;
@endphp

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="order-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl; text-align: right;">
        <div>
            <h2 class="text-white mb-2 font-weight-bold">فرمایش: #{{$customer_order->order_name}}</h2>
            <p class="mb-1 opacity-75">مشتری: <strong>{{$customer_order->customer->name ?? 'N/A'}} ({{$customer_order->customer->country ?? 'N/A'}})</strong></p>
            <p class="mb-0 opacity-75">تاریخ ثبت: {{ $customer_order->order_date }} | وضعیت کلی: 
                <span class="badge badge-light text-dark font-weight-bold" style="text-transform: uppercase;">{{ $customer_order->status }}</span>
            </p>
        </div>
        <div class="hideOnPrint d-flex gap-2">
            <button class="btn btn-premium font-weight-bold shadow-sm" data-toggle="modal" data-target="#carpetModal">
                <i class="fa fa-plus-circle ml-2"></i> ثبت مشخصات قالین جدید
            </button>
            <a href="/dashboard/customer-orders" class="btn btn-light font-weight-bold shadow-sm text-dark mr-2">
                <i class="fa fa-arrow-right ml-1"></i> بازگشت
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger glass-card mb-4 text-right" style="direction: rtl;">
            <ul class="mb-0 pr-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session("status") || session("error"))
        <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mb-4 border-0 rounded-lg py-3 text-center font-weight-bold shadow-none" style="direction: rtl;">
            {{ session('status') ?: session('error') }}
        </div>
    @endif

    <!-- Progress Dashboard Indicator -->
    <div class="card glass-card mb-4 p-4 hideOnPrint" style="direction: rtl; text-align: right;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="font-weight-bold text-dark mb-0">میزان تکمیل قالین‌های فرمایش:</h6>
            <h5 class="font-weight-bold text-primary mb-0">{{ $progressPercentage }}%</h5>
        </div>
        <div class="progress-premium" style="height: 12px;">
            <div class="progress-bar-premium {{ $progressPercentage == 100 ? 'progress-bar-completed' : '' }}" style="width: {{ $progressPercentage }}%"></div>
        </div>
        <small class="text-muted d-block mt-2">تعداد {{ $completedCarpets }} قالین از مجموع {{ $totalCarpets }} قالین این فرمایش تکمیل شده است.</small>
    </div>

    <!-- Statistics Grid -->
    <div class="row mb-4 hideOnPrint" style="direction: rtl; text-align: right;">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="fa fa-th-large"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">تعداد کل قالین‌ها</small>
                <h3 class="font-weight-bold mb-0">{{ $totalCarpets }} عدد</h3>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">قالین‌های تکمیل شده</small>
                <h3 class="font-weight-bold mb-0">{{ $completedCarpets }} عدد</h3>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="fa fa-spinner"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">قالین‌های در حال کار</small>
                <h3 class="font-weight-bold mb-0">{{ $inProgressCarpets }} عدد</h3>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="fa fa-hourglass-start"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">قالین‌های معلق</small>
                <h3 class="font-weight-bold mb-0">{{ $pendingCarpets }} عدد</h3>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="stat-card stat-card-success" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                <div class="stat-icon"><i class="fa fa-vector-square"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">مجموع مساحت</small>
                <h3 class="font-weight-bold mb-0">{{ number_format($totalArea, 2) }} m²</h3>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="stat-card stat-card-primary" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="stat-icon"><i class="fa fa-dollar-sign"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">مجموع کل مبالغ</small>
                <h3 class="font-weight-bold mb-0">${{ number_format($totalAmount, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Carpets List Grid -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card glass-card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="direction: rtl;">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-th text-primary ml-2"></i>لیست قالین‌های ثبت شده در این فرمایش</h5>
                    <button class="btn btn-sm btn-outline-secondary hideOnPrint" onclick="window.print()"><i class="fa fa-print ml-1"></i> چاپ لیست</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="direction: rtl; text-align: right;">
                                    <th class="pr-4">مشخصات تخنیکی قالین</th>
                                    <th>وضعیت کار</th>
                                    <th>قیمت و مبلغ</th>
                                    <th>زمان‌بندی</th>
                                    <th class="text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer_order_details as $co)
                                <tr style="direction: rtl; text-align: right;">
                                    <td class="pl-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="ml-3 hideOnPrint" data-toggle="modal" data-target="#imageModal" data-src="/{{$co->photo}}">
                                                <img src="/{{$co->photo}}" class="rounded" style="height: 50px; width: 50px; object-fit: cover; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onerror="this.src='/uploads/placeholder.png'">
                                            </a>
                                            <div>
                                                <span class="d-block font-weight-bold text-dark">{{ $co->quality }} ({{ $co->height }}x{{ $co->width }})</span>
                                                <small class="text-muted">مساحت: {{ $co->area }} m² | تار و پود: {{ $co->warp }}/{{ $co->weft }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="/dashboard/customer-order-details/{{ $co->cod_id }}/change-status" method="POST" class="d-inline status-carpet-form-{{ $co->cod_id }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="select-status-premium select-carpet-status-inline status-{{ $co->current_status }}" data-id="{{ $co->cod_id }}">
                                                <option value="pending" {{ $co->current_status == 'pending' ? 'selected' : '' }}>معلق</option>
                                                <option value="in_progress" {{ $co->current_status == 'in_progress' ? 'selected' : '' }}>در حال کار</option>
                                                <option value="completed" {{ $co->current_status == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="d-block font-weight-bold text-dark">${{ number_format($co->total_amount, 2) }}</span>
                                        <small class="text-muted d-block">فی متر: ${{ number_format($co->unit_price, 2) }}</small>
                                    </td>
                                    <td>
                                        <small class="d-block text-success font-weight-bold"><i class="fa fa-calendar-alt ml-1"></i>شروع: {{ $co->start_date }}</small>
                                        <small class="d-block text-danger font-weight-bold"><i class="fa fa-calendar-times ml-1"></i>تحویل: {{ $co->end_date ?: 'نامشخص' }}</small>
                                    </td>
                                    <td class="text-left pr-4 pl-4">
                                        <div class="btn-group">
                                            <a href="/dashboard/customer-order-details/{{$co->cod_id}}/edit" class="btn btn-light action-btn text-info" title="ویرایش مشخصات">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-light action-btn text-danger mr-1" title="حذف قالین" onclick="deleteOrder({{$co->cod_id}})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted font-weight-bold">هیچ قالینی برای این فرمایش ثبت نشده است. برای ثبت قالین دکمه بالا را کلیک کنید.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create / Edit Carpet Detail -->
<div class="modal fade" id="carpetModal" tabindex="-1" role="dialog" aria-labelledby="carpetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium bg-white" style="direction: rtl;">
                <h5 class="modal-title font-weight-bold text-dark" id="carpetModalLabel">
                    <i class="fa {{ $orderEdit ? 'fa-edit text-info' : 'fa-plus-circle text-primary' }} ml-2"></i>
                    {{ $orderEdit ? 'ویرایش مشخصات تخنیکی قالین' : 'ثبت مشخصات تخنیکی قالین جدید' }}
                </h5>
                <button type="button" class="close ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $orderEdit ? '/dashboard/customer-order-details/'.$orderEdit->cod_id : '/dashboard/customer-order-details' }}" method="post" enctype="multipart/form-data">
                @csrf
                @if($orderEdit)
                    {{ method_field('patch') }}
                @endif
                <input type="hidden" name="customer_order_id" value="{{ $customer_order->co_id }}">
                <input type="hidden" name="currency_id" value="1">
                <input type="hidden" name="exchange_rate" value="1">

                <div class="modal-body modal-body-premium" style="direction: rtl; text-align: right;">
                    <div class="row">
                        <!-- Technical Specs -->
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">کیفیت (Quality) <span class="text-danger">*</span></label>
                            <input type="text" name="quality" class="form-control form-control-premium" value="{{ optional($orderEdit)->quality ?? '' }}" required placeholder="مثال: چوب‌رنگ 60 رجه">
                            <small class="text-muted small">نوع گره، رج یا صنف قالین.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">طول (متر) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="height" id="height" class="form-control form-control-premium" value="{{ optional($orderEdit)->height ?? '' }}" required placeholder="طول">
                            <small class="text-muted small">طول قالین به متر.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">عرض (متر) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="width" id="width" class="form-control form-control-premium" value="{{ optional($orderEdit)->width ?? '' }}" required placeholder="عرض">
                            <small class="text-muted small">عرض قالین به متر.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">مساحت (m²)</label>
                            <input type="text" name="area" id="area" class="form-control form-control-premium bg-light" value="{{ optional($orderEdit)->area ?? '' }}" readonly>
                            <small class="text-muted small">محاسبه خودکار مساحت.</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">قیمت فی متر مربع (USD)</label>
                            <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control form-control-premium" value="{{ optional($orderEdit)->unit_price ?? '' }}" placeholder="قیمت فی متر مربع">
                            <small class="text-muted small">قیمت فروش فی متر مربع.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">مجموع مبلغ (USD)</label>
                            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control form-control-premium bg-light font-weight-bold" value="{{ optional($orderEdit)->total_amount ?? '' }}" readonly>
                            <small class="text-muted small">محاسبه خودکار کل مبلغ.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">تار (Warp)</label>
                            <input type="text" name="warp" class="form-control form-control-premium" value="{{ optional($orderEdit)->warp ?? '' }}" placeholder="جنس تار">
                            <small class="text-muted small">جنس تار مورد استفاده.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">پود (Weft)</label>
                            <input type="text" name="weft" class="form-control form-control-premium" value="{{ optional($orderEdit)->weft ?? '' }}" placeholder="جنس پود">
                            <small class="text-muted small">جنس پود مورد استفاده.</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">نمبر بافنده</label>
                            <input type="text" name="weaver_code" class="form-control form-control-premium" value="{{ optional($orderEdit)->weaver_code ?? '' }}" placeholder="کد بافنده">
                            <small class="text-muted small">کد شناسایی بافنده.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">وضعیت فعلی تولید <span class="text-danger">*</span></label>
                            <select name="current_status" class="form-control select2-modal">
                                @foreach(['pending' => 'معلق (Pending)', 'in_progress' => 'در حال کار (In Progress)', 'completed' => 'تکمیل شده (Completed)'] as $val => $label)
                                    <option value="{{ $val }}" {{ (is_object($orderEdit) && $orderEdit->current_status == $val) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted small">وضعیت کار قالین.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">تاریخ شروع بافت <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control form-control-premium" value="{{ optional($orderEdit)->start_date ?? date('Y-m-d') }}" required>
                            <small class="text-muted small">شروع بافت.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold">تاریخ ختم (تخمینی)</label>
                            <input type="date" name="end_date" class="form-control form-control-premium" value="{{ optional($orderEdit)->end_date ?? '' }}">
                            <small class="text-muted small">تاریخ تسلیمی احتمالی.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">عکس یا نقشه قالین</label>
                            <input type="file" name="photo" class="form-control form-control-premium">
                            <small class="text-muted small">نقشه یا عکس نمونه طراحی.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-start py-3" style="border-radius: 0 0 24px 24px;">
                    <button type="submit" class="btn btn-premium font-weight-bold">
                        <i class="fa fa-save ml-1"></i> {{ $orderEdit ? 'بروزرسانی مشخصات' : 'ثبت مشخصات قالین' }}
                    </button>
                    <button type="button" class="btn btn-light font-weight-bold mr-2" data-dismiss="modal">انصراف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Large Image -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-body p-0 text-center">
                <img src="" id="modalImg" class="img-fluid rounded animate__animated animate__zoomIn" style="max-height: 85vh; width: 100%; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Trigger modal immediately if editing
    @if($orderEdit)
        $(document).ready(function() {
            $('#carpetModal').modal('show');
        });
    @endif

    // Real-time area and total calculation
    function calculateTotals() {
        let h = parseFloat($('#height').val()) || 0;
        let w = parseFloat($('#width').val()) || 0;
        let area = h * w;
        $('#area').val(area.toFixed(2));

        let up = parseFloat($('#unit_price').val()) || 0;
        let total = area * up;
        $('#total_amount').val(total.toFixed(2));
    }

    $("#height, #width, #unit_price").on("keyup change input", calculateTotals);

    // Initial run on page load
    calculateTotals();

    // Image Modal
    $('#imageModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let src = button.data('src');
        $('#modalImg').attr('src', src);
    });

    // Delete Logic
    function deleteOrder(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "مشخصات این قالین برای همیشه حذف خواهد شد!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/customer-order-details/' + id,
                    data: { _token: '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            location.reload();
                        } else {
                            swal("خطا", res.message || "عملیات با خطا مواجه شد", "error");
                        }
                    }
                });
            }
        });
    }

    $(document).on('change', '.select-carpet-status-inline', function() {
        var id = $(this).data('id');
        $('.status-carpet-form-' + id).submit();
    });

    $('.select2').select2({ width: '100%' });

    // Select2 inside modal
    $('.select2-modal').select2({
        dropdownParent: $('#carpetModal'),
        width: '100%'
    });
</script>
@endsection
