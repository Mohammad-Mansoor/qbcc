@extends('dsh.master')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
    }

    .page-header-premium {
        background: var(--primary-gradient);
        color: white;
        padding: 2.25rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header-premium::before {
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

    /* Badges */
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
    
    .badge-critical { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-info { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

    /* Table modifications */
    .table thead th {
        font-size: 0.8rem;
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
        font-weight: 500;
    }
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .remaining-days {
        font-size: 1.5rem;
        font-weight: 800;
    }
    .text-critical { color: #dc2626; }
    .text-warning-bold { color: #d97706; }
    .text-info-bold { color: #2563eb; }

    @media print {
        .hideOnPrint { display: none !important; }
        .glass-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

<div class="container-fluid py-4" id="expensePrint">
    <!-- Header -->
    <div class="page-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl; text-align: right;">
        <div>
            <h2 class="text-white mb-2 font-weight-bold"><i class="fa fa-bell ml-2"></i> هشدارهای مهلت فرمایشات</h2>
            <p class="mb-0 opacity-75">لیست فرمایشاتی که مهلت تحویل آنها نزدیک است (۳۰ روز آینده)</p>
        </div>
        <div class="hideOnPrint">
            <button class="btn btn-light font-weight-bold shadow-sm" onclick="printPage('expensePrint')">
                <i class="fa fa-print ml-2"></i> چاپ گزارش
            </button>
        </div>
    </div>

    <!-- Notifications Feed -->
    @if(isset($notifications) && $notifications->count() > 0)
    <div class="row mb-4 hideOnPrint">
        <div class="col-lg-12">
            <div class="card glass-card">
                <div class="card-header bg-transparent py-3" style="direction: rtl;">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-bell text-primary ml-2"></i> آخرین اعلانات سیستم</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="direction: rtl; text-align: right;">
                        @foreach($notifications as $notification)
                            @php
                                $type = $notification->data['type'] ?? 'alert';
                                $message = $notification->data['message'] ?? 'اعلانی وجود دارد.';
                                $orderId = $notification->data['order_id'] ?? null;
                                
                                $icon = 'fa-bell text-secondary';
                                $bgClass = '';
                                
                                if($type == 'new_order') {
                                    $icon = 'fa-plus-circle text-success';
                                    $bgClass = 'bg-light';
                                } elseif($type == 'deadline_alert') {
                                    $icon = 'fa-exclamation-triangle text-danger';
                                }
                            @endphp
                            <div class="list-group-item list-group-item-action {{ $bgClass }} d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <i class="fa {{ $icon }} ml-2" style="font-size: 1.2rem;"></i>
                                    <span class="font-weight-bold text-dark" style="font-size: 1.05rem;">{{ $message }}</span>
                                    <small class="text-muted d-block mt-1" style="margin-right: 2.2rem;">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                </div>
                                @if($orderId)
                                    <a href="/dashboard/customer-order-details/{{ $orderId }}" class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold">مشاهده</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Table Grid -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card glass-card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="direction: rtl;">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-exclamation-circle text-danger ml-2"></i>فرمایشات نیازمند توجه فوری</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="direction: rtl; text-align: right;">
                                    <th class="pr-4">وضعیت مهلت</th>
                                    <th>روزهای باقیمانده</th>
                                    <th>نمبر / نام فرمایش</th>
                                    <th>مشتری</th>
                                    <th>تاریخ ثبت</th>
                                    <th>تاریخ تحویل (پیش‌بینی)</th>
                                    <th class="text-left pl-4 hideOnPrint">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    @php
                                        $endDate = \Carbon\Carbon::parse($order->end_date)->startOfDay();
                                        $today = \Carbon\Carbon::today();
                                        $daysRemaining = $today->diffInDays($endDate, false);
                                        
                                        $badgeClass = '';
                                        $textClass = '';
                                        $icon = '';
                                        $statusText = '';
                                        
                                        if ($daysRemaining < 0) {
                                            $badgeClass = 'badge-critical';
                                            $textClass = 'text-critical';
                                            $icon = 'fa-times-circle';
                                            $statusText = 'تاخیر گذشته (' . abs($daysRemaining) . ' روز)';
                                        } elseif ($daysRemaining <= 7) {
                                            $badgeClass = 'badge-critical';
                                            $textClass = 'text-critical';
                                            $icon = 'fa-exclamation-triangle';
                                            $statusText = 'بحرانی (یک هفته)';
                                        } elseif ($daysRemaining <= 15) {
                                            $badgeClass = 'badge-warning';
                                            $textClass = 'text-warning-bold';
                                            $icon = 'fa-exclamation-circle';
                                            $statusText = 'هشدار (دو هفته)';
                                        } else {
                                            $badgeClass = 'badge-info';
                                            $textClass = 'text-info-bold';
                                            $icon = 'fa-info-circle';
                                            $statusText = 'عادی (یک ماه)';
                                        }
                                    @endphp
                                    <tr style="direction: rtl; text-align: right;">
                                        <td class="pr-4">
                                            <span class="badge badge-premium {{ $badgeClass }}">
                                                <i class="fa {{ $icon }}"></i> {{ $statusText }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="remaining-days {{ $textClass }}">{{ $daysRemaining > 0 ? $daysRemaining : 0 }}</span>
                                            <small class="text-muted">روز</small>
                                        </td>
                                        <td class="font-weight-bold text-dark">
                                            <i class="fa fa-folder-open text-muted ml-2"></i> {{ $order->order_name }}
                                        </td>
                                        <td>{{ optional($order->customer)->name ?? 'مشتری نامشخص' }}</td>
                                        <td class="text-muted">{{ $order->order_date }}</td>
                                        <td class="font-weight-bold text-dark">{{ $order->end_date }}</td>
                                        <td class="text-left pl-4 hideOnPrint">
                                            <a href="/dashboard/customer-order-details/{{$order->co_id}}" class="btn btn-sm btn-outline-primary font-weight-bold">
                                                <i class="fa fa-eye"></i> مشاهده فرمایش
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fa fa-check-circle text-success" style="font-size: 3rem; opacity: 0.5;"></i>
                                            <h5 class="mt-3 text-muted font-weight-bold">هیچ فرمایشی در خطر تاخیر نیست!</h5>
                                            <p class="text-muted">تمامی فرمایشات بیش از ۳۰ روز تا مهلت تحویل زمان دارند.</p>
                                        </td>
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
@endsection
