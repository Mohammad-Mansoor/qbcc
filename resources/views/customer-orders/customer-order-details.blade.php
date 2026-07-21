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
    $statusProgress = [
        'graphing' => 10, 'dyeing' => 20, 'on_loom' => 40, 'off_loom' => 50,
        'washing' => 60, 'finishing' => 70, 'repairing' => 80, 'ready' => 100,
        'shipped' => 100, 'paused' => 0, 'cancelled' => 0
    ];

    $totalCarpets = count($customer_order_details);
    $completedCarpets = $customer_order_details->whereIn('current_status', ['ready', 'shipped'])->count();
    $inProgressCarpets = $customer_order_details->whereNotIn('current_status', ['ready', 'shipped', 'cancelled', 'paused'])->count();
    $pendingCarpets = $customer_order_details->where('current_status', 'graphing')->count(); // Taking graphing as the initial active state
    $totalArea = $customer_order_details->sum('area');
    $totalAmount = $customer_order_details->sum('total_amount');
    
    $totalProgressScore = 0;
    $validCarpetsCount = 0;
    foreach($customer_order_details as $carpet) {
        if ($carpet->current_status != 'cancelled') {
            $totalProgressScore += $statusProgress[$carpet->current_status] ?? 0;
            $validCarpetsCount++;
        }
    }
    $progressPercentage = $validCarpetsCount > 0 ? round($totalProgressScore / $validCarpetsCount) : 0;
@endphp

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="order-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl; text-align: right;">
        <div>
            <h2 class="text-white mb-2 font-weight-bold">فرمایش: #{{$customer_order->order_name}}</h2>
            @if($customer_order->customer_order_number)
            <p class="mb-1 text-white font-weight-bold" style="font-size: 1.1rem;">نمبر فرمایش مشتری: {{$customer_order->customer_order_number}}</p>
            @endif
            <p class="mb-1 opacity-75">مشتری: <strong>{{$customer_order->customer->name ?? 'N/A'}} ({{$customer_order->customer->country ?? 'N/A'}})</strong></p>
            <p class="mb-0 opacity-75">تاریخ ثبت: {{ $customer_order->order_date }} | وضعیت کلی: 
                <span class="badge badge-light text-dark font-weight-bold" style="text-transform: uppercase;">{{ $customer_order->status }}</span>
            </p>
        </div>
        <div class="hideOnPrint d-flex gap-2">
            @can('manage_customer_order_details')
            <button class="btn btn-premium font-weight-bold shadow-sm" data-toggle="modal" data-target="#carpetModal">
                <i class="fa fa-plus-circle ml-2"></i> ثبت مشخصات قالین جدید
            </button>
            @endcan
            <a href="/dashboard/customer-order-details/{{ $customer_order->co_id }}?export=pdf" target="_blank" class="btn btn-light font-weight-bold shadow-sm text-dark mr-2">
                <i class="fa fa-file-pdf text-danger ml-1"></i> چاپ (PDF)
            </a>
            <a href="/dashboard/customer-order-details/{{ $customer_order->co_id }}?export=excel" class="btn btn-light font-weight-bold shadow-sm text-dark mr-2">
                <i class="fa fa-file-excel text-success ml-1"></i> خروجی (Excel)
            </a>
            <a href="/dashboard/customer-orders" class="btn btn-light font-weight-bold shadow-sm text-dark mr-2">
                <i class="fa fa-arrow-right ml-1"></i> بازگشت
            </a>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
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
                                <tr style="direction: rtl; text-align: right; white-space: nowrap;">
                                    <th class="pr-4">عکس</th>
                                    <th>کیفیت</th>
                                    <th>طول</th>
                                    <th>عرض</th>
                                    <th>مساحت</th>
                                    <th>تار</th>
                                    <th>پود</th>
                                    <th>کد بافنده</th>
                                    <th>نمبر قالین</th>
                                    <th>شروع</th>
                                    <th>ختم</th>
                                    <th>وضعیت</th>
                                    <th class="text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer_order_details as $co)
                                <tr style="direction: rtl; text-align: right;">
                                    <td class="pl-4 py-2">
                                        @if($co->photo)
                                        <a href="#" class="hideOnPrint" data-toggle="modal" data-target="#imageModal" data-src="/{{$co->photo}}">
                                            <img src="/{{$co->photo}}" class="rounded" style="height: 40px; width: 40px; object-fit: cover; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" onerror="this.onerror=null; this.src='/images/logo.png';">
                                        </a>
                                        @else
                                        <span class="text-muted"><i class="fa fa-image fa-2x"></i></span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-dark">{{ $co->quality ?: '-' }}</td>
                                    <td>{{ $co->height ?: '-' }}</td>
                                    <td>{{ $co->width ?: '-' }}</td>
                                    <td><span class="badge badge-light border">{{ number_format((float)$co->area, 2) }}</span></td>
                                    <td>{{ $co->warp ?: '-' }}</td>
                                    <td>{{ $co->weft ?: '-' }}</td>
                                    <td>{{ $co->weaver_code ?: '-' }}</td>
                                    <td>
                                        @if($co->carpet_number)
                                            <span class="badge badge-success">{{ $co->carpet_number }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-success">{{ $co->start_date }}</td>
                                    <td class="text-danger">{{ $co->end_date ?: '-' }}</td>
                                    <td>
                                        @can('manage_customer_order_details')
                                        <form action="/dashboard/customer-order-details/{{ $co->cod_id }}/change-status" method="POST" class="d-inline status-carpet-form-{{ $co->cod_id }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="select-status-premium select-carpet-status-inline status-{{ $co->current_status }}" data-id="{{ $co->cod_id }}">
                                                <option value="graphing" {{ $co->current_status == 'graphing' ? 'selected' : '' }}>نقشه کشی</option>
                                                <option value="dyeing" {{ $co->current_status == 'dyeing' ? 'selected' : '' }}>رنگ ریزی</option>
                                                <option value="on_loom" {{ $co->current_status == 'on_loom' ? 'selected' : '' }}>در جریان بافت</option>
                                                <option value="off_loom" {{ $co->current_status == 'off_loom' ? 'selected' : '' }}>ختمِ بافت</option>
                                                <option value="washing" {{ $co->current_status == 'washing' ? 'selected' : '' }}>شستشو</option>
                                                <option value="finishing" {{ $co->current_status == 'finishing' ? 'selected' : '' }}>تیاری</option>
                                                <option value="repairing" {{ $co->current_status == 'repairing' ? 'selected' : '' }}>ترمیم</option>
                                                <option value="ready" {{ $co->current_status == 'ready' ? 'selected' : '' }}>آماده</option>
                                                <option value="shipped" {{ $co->current_status == 'shipped' ? 'selected' : '' }}>ارسال شده</option>
                                                <option value="paused" {{ $co->current_status == 'paused' ? 'selected' : '' }}>متوقف</option>
                                                <option value="cancelled" {{ $co->current_status == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                                            </select>
                                        </form>
                                        @else
                                            <span class="badge badge-light text-dark">{{ $co->current_status }}</span>
                                        @endcan
                                    </td>
                                    <td class="text-left pr-4 pl-4" style="white-space: nowrap;">
                                        <div class="btn-group">
                                            <a href="/dashboard/carpet-specification/{{$co->cod_id}}" class="btn btn-light action-btn text-primary" title="مشاهده جزئیات کامل">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @can('manage_customer_order_details')
                                            <a href="/dashboard/customer-order-details/{{$co->cod_id}}/edit" class="btn btn-light action-btn text-info" title="ویرایش مشخصات">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-light action-btn text-danger mr-1" title="حذف قالین" onclick="deleteOrder({{$co->cod_id}})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="15" class="text-center py-5 text-muted font-weight-bold">هیچ قالینی برای این فرمایش ثبت نشده است. برای ثبت قالین دکمه بالا را کلیک کنید.</td>
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
                <input type="hidden" name="unit_price" id="unit_price" value="{{ optional($orderEdit)->unit_price ?? 0 }}">
                <input type="hidden" name="total_amount" id="total_amount" value="{{ optional($orderEdit)->total_amount ?? 0 }}">

                <div class="modal-body modal-body-premium" style="direction: rtl; text-align: right;">
                    <!-- Section 1: Physical Specs & Dimensions -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-ruler-combined ml-2"></i> مشخصات فزیکی و ابعاد قالین
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-dark">کیفیت (Quality) <span class="text-danger">*</span></label>
                                <input type="text" name="quality" class="form-control form-control-premium" value="{{ optional($orderEdit)->quality ?? '' }}" required placeholder="مثال: چوب‌رنگ 60 رجه">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">نوع گره، رج یا صنف قالین.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-dark">طول (متر) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="height" id="height" class="form-control form-control-premium" value="{{ optional($orderEdit)->height ?? '' }}" required placeholder="طول">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">طول قالین به متر.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-dark">عرض (متر) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="width" id="width" class="form-control form-control-premium" value="{{ optional($orderEdit)->width ?? '' }}" required placeholder="عرض">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">عرض قالین به متر.</small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-dark">مساحت محاسبه شده (m²)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-left-0" style="border-radius: 0 10px 10px 0;"><i class="fa fa-calculator text-muted"></i></span>
                                    </div>
                                    <input type="text" name="area" id="area" class="form-control form-control-premium bg-light font-weight-bold" style="border-radius: 10px 0 0 10px;" value="{{ optional($orderEdit)->area ?? '' }}" readonly>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">محاسبه خودکار مساحت قالین.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-dark">تار (Warp)</label>
                                <input type="text" name="warp" class="form-control form-control-premium" value="{{ optional($orderEdit)->warp ?? '' }}" placeholder="جنس تار">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">جنس تار مورد استفاده.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-dark">پود (Weft)</label>
                                <input type="text" name="weft" class="form-control form-control-premium" value="{{ optional($orderEdit)->weft ?? '' }}" placeholder="جنس پود">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">جنس پود مورد استفاده.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Production Status & Timelines -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-cogs ml-2"></i> مشخصات تولید و زمان‌بندی بافت
                        </h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-dark">نمبر/کد بافنده</label>
                                <input type="text" name="weaver_code" class="form-control form-control-premium" value="{{ optional($orderEdit)->weaver_code ?? '' }}" placeholder="کد بافنده">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">کد شناسایی بافنده.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-dark">وضعیت تولید <span class="text-danger">*</span></label>
                                <select name="current_status" class="form-control select2-modal">
                                    @foreach(['graphing' => 'نقشه کشی', 'dyeing' => 'رنگ ریزی', 'on_loom' => 'در جریان بافت', 'off_loom' => 'ختمِ بافت', 'washing' => 'شستشو', 'finishing' => 'تیاری', 'repairing' => 'ترمیم', 'ready' => 'آماده (تکمیل)', 'shipped' => 'ارسال شده', 'paused' => 'متوقف', 'cancelled' => 'لغو شده'] as $val => $label)
                                        <option value="{{ $val }}" {{ (is_object($orderEdit) && $orderEdit->current_status == $val) ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">وضعیت کار قالین.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-dark">تاریخ شروع بافت <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control form-control-premium" value="{{ optional($orderEdit)->start_date ?? date('Y-m-d') }}" required>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">شروع بافت قالین.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-dark">تاریخ ختم (تخمینی)</label>
                                <input type="date" name="end_date" class="form-control form-control-premium" value="{{ optional($orderEdit)->end_date ?? '' }}">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">تاریخ تسلیمی احتمالی.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Design File / Image Upload -->
                    <div>
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-image ml-2"></i> فایل‌ها و پیوست‌های نقشه
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="small font-weight-bold text-dark">عکس یا نقشه قالین</label>
                                <div class="custom-file-premium border rounded p-3 bg-light d-flex align-items-center justify-content-between">
                                    <input type="file" name="photo" id="photo" class="d-none">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('photo').click()">
                                        <i class="fa fa-upload ml-1"></i> انتخاب فایل عکس / نقشه
                                    </button>
                                    <span id="file-chosen-text" class="text-muted small">هیچ فایلی انتخاب نشده است</span>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">نقشه یا عکس نمونه طراحی قالین.</small>
                            </div>
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

<!-- Modal: Carpet Number for Completed Status -->
<div class="modal fade" id="carpetNumberModal" tabindex="-1" role="dialog" aria-labelledby="carpetNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium bg-white" style="direction: rtl;">
                <h5 class="modal-title font-weight-bold text-success" id="carpetNumberModalLabel">
                    <i class="fa fa-check-circle ml-2"></i> ثبت نمبر قالین تکمیل شده
                </h5>
                <button type="button" class="close ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-premium" style="direction: rtl; text-align: right;">
                <p class="text-muted small mb-3">برای تکمیل این قالین، لطفا نمبر یا بارکد نهایی آن را وارد کنید:</p>
                <input type="hidden" id="pending_status_form_id" value="">
                <input type="hidden" id="pending_status_value" value="">
                
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-dark">نمبر قالین (Carpet Number) <span class="text-danger">*</span></label>
                    <input type="text" id="completed_carpet_number" class="form-control form-control-premium text-left" dir="ltr" placeholder="مثال: C-10293">
                </div>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-start py-3" style="border-radius: 0 0 24px 24px;">
                <button type="button" class="btn btn-success font-weight-bold px-4" onclick="submitStatusWithCarpetNumber()">
                    <i class="fa fa-check ml-1"></i> تایید و تکمیل
                </button>
                <button type="button" class="btn btn-light font-weight-bold mr-2" data-dismiss="modal" onclick="resetStatusDropdown()">انصراف</button>
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

    // Store original values when clicking on select
    let originalStatus = {};
    $(document).ready(function() {
        $('.select-carpet-status-inline').each(function() {
            originalStatus[$(this).data('id')] = $(this).val();
        });
    });

    $(document).on('change', '.select-carpet-status-inline', function() {
        var id = $(this).data('id');
        var val = $(this).val();
        
        if(val === 'off_loom') {
            // Open modal to get carpet number
            $('#pending_status_form_id').val(id);
            $('#pending_status_value').val(val);
            $('#completed_carpet_number').val('');
            $('#carpetNumberModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        } else {
            // Submit directly
            $('.status-carpet-form-' + id).submit();
        }
    });

    function resetStatusDropdown() {
        var id = $('#pending_status_form_id').val();
        if(id && originalStatus[id]) {
            $('.select-carpet-status-inline[data-id="'+id+'"]').val(originalStatus[id]);
        }
    }

    function submitStatusWithCarpetNumber() {
        var id = $('#pending_status_form_id').val();
        var num = $('#completed_carpet_number').val().trim();
        
        if(num === '') {
            swal("خطا", "لطفا نمبر قالین را وارد کنید", "error");
            return;
        }

        // Add hidden input with carpet number to the form
        var form = $('.status-carpet-form-' + id);
        if(form.find('input[name="carpet_number"]').length === 0) {
            form.append('<input type="hidden" name="carpet_number" value="">');
        }
        form.find('input[name="carpet_number"]').val(num);
        
        // Hide modal and submit
        $('#carpetNumberModal').modal('hide');
        form.submit();
    }

    $('.select2').select2({ width: '100%' });

    // Select2 inside modal
    $('.select2-modal').select2({
        dropdownParent: $('#carpetModal'),
        width: '100%'
    });

    // File name text change
    $('#photo').on('change', function() {
        let fileName = this.files[0] ? this.files[0].name : "هیچ فایلی انتخاب نشده است";
        $('#file-chosen-text').text(fileName);
    });
</script>
@endsection
