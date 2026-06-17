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
        font-family: 'Inter', 'Outfit', sans-serif;
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

    .form-control-premium {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1rem;
    }

    .badge-premium {
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .badge-premium-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .badge-premium-progress { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-premium-completed { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="order-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl; text-align: right;">
        <div>
            <h2 class="text-white mb-2 font-weight-bold">مدیریت مشتریان و فرمایشات (Customer Orders)</h2>
            <p class="mb-0 opacity-75">لیست مشتریان و مدیریت فرمایشات تولیدی آنها</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card glass-card mb-4" style="direction: rtl; text-align: right;">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-8">
                    <label class="small font-weight-bold text-dark"><i class="fa fa-search ml-1 text-primary"></i> جستجو مشتری</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-left-0" style="border-radius: 0 10px 10px 0;"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="tableSearchInput" class="form-control form-control-premium" style="border-radius: 10px 0 0 10px;" placeholder="نام مشتری را بنویسید...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Grid -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card glass-card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="direction: rtl;">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-users text-primary ml-2"></i>لیست مشتریان</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-right" style="direction: rtl;">
                            <thead>
                                <tr>
                                    <th class="pr-4">نام مشتری</th>
                                    <th>تعداد کل فرمایشات</th>
                                    <th>فرمایشات معلق</th>
                                    <th>در حال اجرا</th>
                                    <th class="text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($main_customers as $customer)
                                    <tr class="customer-row" data-customer-name="{{ strtolower($customer->name) }}">
                                        <td class="pr-4 font-weight-bold text-dark">
                                            <i class="fa fa-user text-muted ml-2"></i> {{ $customer->name }}{{ $customer->country ? " ({$customer->country})" : "" }}
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary px-3 py-2 rounded-pill font-weight-bold">{{ $customer->orders_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if(($customer->pending_orders_count ?? 0) > 0)
                                                <span class="badge-premium badge-premium-pending">{{ $customer->pending_orders_count }} معلق</span>
                                            @else
                                                <span class="text-muted small">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(($customer->in_progress_orders_count ?? 0) > 0)
                                                <span class="badge-premium badge-premium-progress">{{ $customer->in_progress_orders_count }} در اجرا</span>
                                            @else
                                                <span class="text-muted small">0</span>
                                            @endif
                                        </td>
                                        <td class="text-left pl-4">
                                            <a href="/dashboard/customer-orders/{{$customer->id}}" class="btn btn-sm btn-primary font-weight-bold rounded-pill px-4" style="background: var(--primary-gradient); border: none;">
                                                <i class="fa fa-folder-open ml-1"></i> مشاهده فرمایشات
                                            </a>
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
@endsection

@section('scripts')
<script>
    $('#tableSearchInput').on('keyup change input', function() {
        let searchQuery = $(this).val().toLowerCase().trim();
        $('.customer-row').each(function() {
            let rowCustomerName = $(this).attr('data-customer-name') || '';
            if (!searchQuery || rowCustomerName.indexOf(searchQuery) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
</script>
@endsection
