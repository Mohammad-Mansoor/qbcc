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
        padding: 1.5rem;
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
    
    .stat-icon {
        position: absolute;
        left: 1.5rem;
        bottom: 1rem;
        font-size: 3.5rem;
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

    /* Print layout */
    @media print {
        .hideOnPrint { display: none !important; }
        .glass-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

@php
    // Calculate dashboard statistics dynamically from the current list
    $totalOrders = $customer_orders->count();
    $pendingOrders = $customer_orders->where('status', 'pending')->count();
    $inProgressOrders = $customer_orders->where('status', 'in_progress')->count();
    $completedOrders = $customer_orders->where('status', 'completed')->count();
    $canceledOrders = $customer_orders->where('status', 'cancel')->count();

    $statusProgress = [
        'graphing' => 10, 'dyeing' => 20, 'on_loom' => 40, 'off_loom' => 50,
        'washing' => 60, 'finishing' => 70, 'repairing' => 80, 'ready' => 100,
        'shipped' => 100, 'paused' => 0, 'cancelled' => 0
    ];
@endphp

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="order-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl; text-align: right;">
        <div>
            <a href="/dashboard/customer-orders" class="btn btn-sm btn-light mb-2"><i class="fa fa-arrow-right ml-1"></i> بازگشت به لیست مشتریان</a>
            <h2 class="text-white mb-2 font-weight-bold">فرمایشات {{ $customer->name }}{{ $customer->country ? " ($customer->country)" : '' }}</h2>
            <p class="mb-0 opacity-75">ثبت و مدیریت فرمایشات و لیست قالین‌های این مشتری</p>
        </div>
        <div class="hideOnPrint">
            @can('create_customer_order')
            <button class="btn btn-premium font-weight-bold shadow-sm" data-toggle="modal" data-target="#orderModal">
                <i class="fa fa-plus-circle ml-2"></i> ثبت فرمایش جدید
            </button>
            @endcan
        </div>
    </div>

    @if(session("status") || session("error"))
        <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mb-4 border-0 rounded-lg py-3 text-center font-weight-bold shadow-none" style="direction: rtl;">
            {{ session('status') ?: session('error') }}
        </div>
    @endif

    <!-- Statistics Grid -->
    <div class="row mb-4 hideOnPrint" style="direction: rtl; text-align: right;">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="fa fa-folder-open"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">مجموع کل فرمایشات</small>
                <h2 class="font-weight-bold mb-0">{{ $totalOrders }}</h2>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="fa fa-hourglass-half"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">فرمایشات معلق</small>
                <h2 class="font-weight-bold mb-0">{{ $pendingOrders }}</h2>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="fa fa-spinner"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">فرمایشات در حال اجرا</small>
                <h2 class="font-weight-bold mb-0">{{ $inProgressOrders }}</h2>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">فرمایشات تکمیل شده</small>
                <h2 class="font-weight-bold mb-0">{{ $completedOrders }}</h2>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 hideOnPrint">
            <div class="stat-card" style="background: var(--danger-gradient);">
                <div class="stat-icon"><i class="fa fa-times-circle"></i></div>
                <small class="d-block opacity-75 font-weight-bold mb-1">فرمایشات لغو شده</small>
                <h2 class="font-weight-bold mb-0">{{ $canceledOrders }}</h2>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card glass-card mb-4 hideOnPrint" style="direction: rtl; text-align: right;">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <!-- Search Box -->
                <div class="col-lg-6 col-md-8 mb-2 mb-lg-0">
                    <label class="small font-weight-bold text-dark"><i class="fa fa-search ml-1 text-primary"></i> جستجو بر اساس نمبر/نام فرمایش</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-left-0" style="border-radius: 0 10px 10px 0;"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="tableSearchInput" class="form-control form-control-premium" style="border-radius: 10px 0 0 10px;" placeholder="نام یا نمبر فرمایش را بنویسید...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-lg-5 col-md-4 mb-2 mb-lg-0">
                    <label class="small font-weight-bold text-dark"><i class="fa fa-filter ml-1 text-primary"></i> وضعیت فرمایش</label>
                    <select id="filterStatus" class="form-control select2">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="pending">معلق (Pending)</option>
                        <option value="in_progress">در حال اجرا (In Progress)</option>
                        <option value="completed">تکمیل شده (Completed)</option>
                        <option value="cancel">لغو شده (Canceled)</option>
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <div class="col-lg-1 col-md-12 text-left mt-md-4 mt-lg-0">
                    <button type="button" id="btnClearFilters" class="btn btn-light btn-premium-secondary btn-block font-weight-bold" style="height: calc(1.5em + 1.25rem + 2px); border-radius: 10px;" title="پاک کردن فیلترها">
                        <i class="fa fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Grid -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card glass-card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="direction: rtl;">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-list text-primary ml-2"></i>فرمایشات موجود</h5>
                    <button class="btn btn-sm btn-outline-secondary hideOnPrint" onclick="window.print()"><i class="fa fa-print ml-1"></i> چاپ گزارش</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="direction: rtl; text-align: right;">
                                    <th class="pr-4">نام / نمبر فرمایش</th>
                                    <th>تاریخ ثبت</th>
                                    <th>میزان پیشرفت</th>
                                    <th>وضعیت</th>
                                    <th class="text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer_orders as $co)
                                    @php
                                        // Calculate carpet details statistics for progress
                                        $totalCarpets = $co->details->count();
                                        $completedCarpets = $co->details->whereIn('current_status', ['ready', 'shipped'])->count();
                                        
                                        $totalProgressScore = 0;
                                        $validCarpetsCount = 0;
                                        foreach($co->details as $carpet) {
                                            if ($carpet->current_status != 'cancelled') {
                                                $totalProgressScore += $statusProgress[$carpet->current_status] ?? 0;
                                                $validCarpetsCount++;
                                            }
                                        }
                                        $progressPercentage = $validCarpetsCount > 0 ? round($totalProgressScore / $validCarpetsCount) : 0;
                                    @endphp
                                    <tr class="order-row" 
                                        data-order-name="{{ strtolower($co->order_name) }}" 
                                        data-customer-id="{{ $co->main_customer_id }}" 
                                        data-customer-name="{{ strtolower(optional($co->customer)->name ?? '') }}" 
                                        data-status="{{ $co->status }}" 
                                        style="direction: rtl; text-align: right;">
                                        <td class="pr-4 font-weight-bold text-dark">
                                            <i class="fa fa-folder-open text-muted ml-2"></i> {{ $co->order_name }}
                                        </td>
                                        <td class="text-muted">{{ $co->order_date }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="small font-weight-bold text-muted ml-2">{{ $progressPercentage }}%</span>
                                                <div class="progress-premium flex-grow-1" style="min-width: 100px;">
                                                    <div class="progress-bar-premium {{ $progressPercentage == 100 ? 'progress-bar-completed' : '' }}" style="width: {{ $progressPercentage }}%"></div>
                                                </div>
                                                <small class="text-muted mr-2">({{ $completedCarpets }}/{{ $totalCarpets }} قالین)</small>
                                            </div>
                                        </td>
                                        <td>
                                            @can('create_customer_order')
                                            <form action="/dashboard/customer-orders/{{ $co->co_id }}" method="POST" class="d-inline status-order-form-{{ $co->co_id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="order_name" value="{{ $co->order_name }}">
                                                <input type="hidden" name="order_date" value="{{ $co->order_date }}">
                                                <input type="hidden" name="main_customer_id" value="{{ $co->main_customer_id }}">
                                                <select name="status" class="select-status-premium select-order-status-inline status-{{ $co->status }}" data-id="{{ $co->co_id }}">
                                                    <option value="pending" {{ $co->status == 'pending' ? 'selected' : '' }}>معلق</option>
                                                    <option value="in_progress" {{ $co->status == 'in_progress' ? 'selected' : '' }}>در حال اجرا</option>
                                                    <option value="completed" {{ $co->status == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                                                    <option value="cancel" {{ $co->status == 'cancel' ? 'selected' : '' }}>لغو شده</option>
                                                </select>
                                            </form>
                                            @else
                                                <span class="badge badge-premium badge-premium-{{ $co->status }}">
                                                    {{ $co->status == 'pending' ? 'معلق' : ($co->status == 'in_progress' ? 'در حال اجرا' : ($co->status == 'completed' ? 'تکمیل شده' : 'لغو شده')) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td class="text-left pl-4">
                                            @can('manage_customer_order_details')
                                            <a href="/dashboard/customer-order-details/{{$co->co_id}}" class="btn btn-sm btn-outline-primary ml-1 font-weight-bold">
                                                <i class="fa fa-list"></i> جزئیات قالین
                                            </a>
                                            @endcan
                                            @can('edit_customer_order')
                                            <a href="/dashboard/customer-orders/{{$co->co_id}}/edit" class="btn btn-sm btn-outline-info ml-1 font-weight-bold">
                                                <i class="fa fa-edit"></i> ویرایش
                                            </a>
                                            @endcan
                                            @can('delete_customer_order')
                                            <button onclick="deleteOrder({{$co->co_id}})" class="btn btn-sm btn-outline-danger font-weight-bold">
                                                <i class="fa fa-trash"></i> حذف
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create / Edit Order -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium bg-white" style="direction: rtl;">
                <h5 class="modal-title font-weight-bold text-dark" id="orderModalLabel">
                    <i class="fa {{ $orderEdit ? 'fa-edit text-info' : 'fa-plus-circle text-primary' }} ml-2"></i>
                    {{ $orderEdit ? 'ویرایش فرمایش' : 'ثبت فرمایش جدید' }}
                </h5>
                <button type="button" class="close ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $orderEdit ? '/dashboard/customer-orders/'.$orderEdit->co_id : '/dashboard/customer-orders' }}" method="post">
                @csrf
                @if($orderEdit) {{ method_field('patch') }} @endif
                
                <div class="modal-body modal-body-premium" style="direction: rtl; text-align: right;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">نمبر/نام فرمایش</label>
                            <input type="text" name="order_name" id="order_name" class="form-control form-control-premium bg-light" value="{{ $nextOrderNumber }}" readonly>
                            <small class="text-muted d-block mt-1">این شناسه به صورت خودکار و منحصر به فرد در سیستم ایجاد می‌شود.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">تاریخ فرمایش <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" id="order_date" class="form-control form-control-premium" value="{{ optional($orderEdit)->order_date ?? date('Y-m-d') }}" required>
                            <small class="text-muted d-block mt-1">تاریخ رسمی ثبت سفارش.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">مشتری <span class="text-danger">*</span></label>
                            <input type="hidden" name="main_customer_id" value="{{ $customer->id }}">
                            <input type="text" class="form-control form-control-premium bg-light" value="{{ $customer->name }}{{ $customer->country ? " ($customer->country)" : '' }}" readonly>
                            <small class="text-muted d-block mt-1">این فرمایش برای مشتری فعلی ثبت می‌شود.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">وضعیت فرمایش <span class="text-danger">*</span></label>
                            <select name="status" class="form-control select2-modal" required>
                                <option value="pending" {{ (is_object($orderEdit) && $orderEdit->status == 'pending') ? 'selected' : '' }}>معلق (Pending)</option>
                                <option value="in_progress" {{ (is_object($orderEdit) && $orderEdit->status == 'in_progress') ? 'selected' : '' }}>در حال اجرا (In Progress)</option>
                                <option value="completed" {{ (is_object($orderEdit) && $orderEdit->status == 'completed') ? 'selected' : '' }}>تکمیل شده (Completed)</option>
                                <option value="cancel" {{ (is_object($orderEdit) && $orderEdit->status == 'cancel') ? 'selected' : '' }}>لغو شده (Canceled)</option>
                            </select>
                            <small class="text-muted d-block mt-1">وضعیت کاری کلی فرمایش.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-start py-3" style="border-radius: 0 0 24px 24px;">
                    <button type="submit" class="btn btn-premium font-weight-bold">
                        <i class="fa fa-save ml-1"></i> ذخیره تغییرات
                    </button>
                    <button type="button" class="btn btn-light font-weight-bold mr-2" data-dismiss="modal">انصراف</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Trigger modal immediately if editing
    @if($orderEdit)
        $(document).ready(function() {
            $('#orderModal').modal('show');
        });
    @endif

    function deleteOrder(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "با حذف این فرمایش، تمام مشخصات قالین‌های مربوطه نیز حذف خواهند شد!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/customer-orders/' + id,
                    data: { _token: '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            location.reload();
                        } else {
                            swal("خطا", "امکان حذف وجود ندارد", "error");
                        }
                    }
                });
            }
        });
    }

    $(document).on('change', '.select-order-status-inline', function() {
        var id = $(this).data('id');
        $('.status-order-form-' + id).submit();
    });

    $('.select2').select2({ width: '100%' });
    
    // Select2 inside modals wrapper
    $('.select2-modal').select2({
        dropdownParent: $('#orderModal'),
        width: '100%'
    });

    // AJAX to get next order number when order date changes (only if not editing)
    @if(!$orderEdit)
    $('#order_date').on('change', function() {
        let selectedDate = $(this).val();
        if (selectedDate) {
            $.ajax({
                url: '/dashboard/customer-orders-next-number',
                data: { date: selectedDate },
                success: function(res) {
                    $('#order_name').val(res.next_number);
                }
            });
        }
    });
    @endif

    // Real-time client-side filter function
    function filterOrdersTable() {
        let searchQuery = $('#tableSearchInput').val().toLowerCase().trim();
        let status = $('#filterStatus').val();

        $('.order-row').each(function() {
            let row = $(this);
            let rowOrderName = row.attr('data-order-name') || '';
            let rowStatus = row.attr('data-status') || '';

            // Check if matches search string (order number or customer name)
            let matchesSearch = !searchQuery || 
                                rowOrderName.indexOf(searchQuery) !== -1;

            // Check if matches status dropdown selection
            let matchesStatus = !status || rowStatus === status;

            if (matchesSearch && matchesStatus) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Attach real-time keyup/change listeners
    $('#tableSearchInput').on('keyup change input', filterOrdersTable);
    $('#filterStatus').on('change', filterOrdersTable);

    // Reset button
    $('#btnClearFilters').on('click', function() {
        $('#tableSearchInput').val('');
        $('#filterStatus').val('').trigger('change');
    });
</script>
@endsection
