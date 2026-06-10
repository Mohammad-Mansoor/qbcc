@extends('dsh.master')
@section('title' , 'مدیریت بل‌های خرید')
@section('content')
<style>
    :root {
        --qbcc-primary: #0f172a;
        --qbcc-secondary: #334155;
        --qbcc-accent: #3b82f6;
        --qbcc-glass: rgba(255, 255, 255, 0.8);
        --qbcc-border: #e2e8f0;
        --radius-lg: 16px;
        --shadow-soft: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        backdrop-filter: blur(10px);
    }

    .table-modern thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        letter-spacing: 0.5px;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-open {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-closed {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .badge-paid {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-partially {
        background: #fff3e0;
        color: #e65100;
        border: 1px solid #ffe0b2;
    }

    .badge-unpaid {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .form-label-premium {
        font-weight: 700;
        color: #334155;
        font-size: 12px;
        margin-bottom: 8px;
        display: block;
    }

    .premium-input {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 12px;
        transition: all 0.2s;
        background: #fff;
        width: 100%;
    }

    .premium-input:focus {
        border-color: var(--qbcc-accent);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* ANIMATED STATS CARDS */
    .stat-card {
        border: none;
        border-radius: 16px;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: default;
        box-shadow: 0 4px 20px 0 rgba(0,0,0,0.05);
    }
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 50%;
        bottom: -30px;
        left: -30px;
        transition: all 0.5s ease;
    }
    .stat-card:hover::after {
        transform: scale(1.5);
    }
    
    .stat-card-blue {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .stat-card-indigo {
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: white;
    }
    .stat-card-green {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
    }
    .stat-card-teal {
        background: linear-gradient(135deg, #14b8a6, #0f766e);
        color: white;
    }
    
    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        font-size: 1.4rem;
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }
    .stat-card:hover .stat-card-icon {
        transform: rotate(-10deg) scale(1.1);
        background: rgba(255, 255, 255, 0.25);
    }
    
    .stat-card-val {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .stat-card-lbl {
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0.9;
        margin-top: 4px;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <!-- HEADER -->
        <div class="glass-card p-4 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="font-weight-bold mb-1">مدیریت بل‌های خرید (Purchase Bills)</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-file-text mr-1"></i> لیست و مدیریت بل‌های خرید قالین‌های دریافتی</p>
            </div>
            <div>
                <button class="btn btn-primary rounded-lg shadow px-4 py-2" data-toggle="modal" data-target="#createInvoiceModal">
                    <i class="feather icon-plus mr-1"></i> ایجاد بل خرید جدید
                </button>
            </div>
        </div>

        <!-- STATS CARDS ROW -->
        <div class="row mb-4">
            <!-- Card 1: Total Invoices -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card stat-card-blue p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-right w-100">
                            <div class="stat-card-val">{{ $totalInvoicesCount }} <span style="font-size: 1rem; font-weight: normal;">عدد</span></div>
                            <div class="stat-card-lbl">کل بل‌های خرید (Total Purchase Bills)</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="feather icon-file-text"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 2: Open Invoices -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card stat-card-indigo p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-right w-100">
                            <div class="stat-card-val">{{ $openInvoicesCount }} <span style="font-size: 1rem; font-weight: normal;">باز</span></div>
                            <div class="stat-card-lbl">بل‌های خرید باز (Open Purchase Bills)</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="feather icon-unlock"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 3: Closed Invoices -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card stat-card-green p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-right w-100">
                            <div class="stat-card-val">{{ $closedInvoicesCount }} <span style="font-size: 1rem; font-weight: normal;">بسته</span></div>
                            <div class="stat-card-lbl">بل‌های خرید بسته (Closed Purchase Bills)</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="feather icon-lock"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 4: Total Purchased Value -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card stat-card-teal p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-right w-100">
                            <div class="stat-card-val">${{ number_format($totalAmountUsd, 2) }}</div>
                            <div class="stat-card-lbl">{{ $totalCarpetsCount }} عدد قالین ({{ number_format($totalArea, 2) }} m²)</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="feather icon-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        <!-- SEARCH & FILTERS -->
        <div class="glass-card p-4 mb-4 text-right" style="direction: rtl;">
            <form action="/dashboard/check-book" method="GET" class="row align-items-end">
                <div class="col-md-5 form-group mb-0">
                    <label class="form-label-premium">جستجو بر اساس نمبر بل خرید</label>
                    <input type="text" name="search" class="form-control premium-input" placeholder="نمبر بل خرید..." value="{{ request('search') }}">
                </div>
                <div class="col-md-5 form-group mb-0">
                    <label class="form-label-premium">فیلتر بر اساس نماینده (فروشنده)</label>
                    <select name="agent_id" class="form-control select2">
                        <option value="">همه نماینده‌ها...</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->agent_id }}" {{ request('agent_id') == $agent->agent_id ? 'selected' : '' }}>
                                {{ $agent->user->name }} ({{ $agent->account_no }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group mb-0 text-left">
                    <button type="submit" class="btn btn-dark rounded-lg px-4 py-2 mr-2">
                        <i class="feather icon-filter"></i> فیلتر
                    </button>
                    @if(request()->filled('search') || request()->filled('agent_id'))
                        <a href="/dashboard/check-book" class="btn btn-outline-danger rounded-lg px-4 py-2">
                            پاک کردن فیلترها
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- MAIN TABLE -->
        <div class="glass-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-modern mb-0 text-right" style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>آی دی</th>
                            <th>نمبر بل خرید</th>
                            <th>نام نماینده (فروشنده)</th>
                            <th>تاریخ ثبت</th>
                            <th>وضعیت سند</th>
                            <th>وضعیت پرداخت</th>
                            <th class="text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td class="font-weight-bold">{{ $invoice->invoice_number }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $invoice->agent->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $invoice->agent->account_no ?? '' }}</small>
                                </td>
                                <td>{{ $invoice->date }}</td>
                                <td>
                                    <span class="status-badge {{ $invoice->status == 'open' ? 'badge-open' : 'badge-closed' }}">
                                        {{ $invoice->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice->payment_status === 'paid')
                                        <span class="status-badge badge-paid">تصفیه شده (Paid)</span>
                                    @elseif($invoice->payment_status === 'partially_paid')
                                        <span class="status-badge badge-partially">تادیه قسمتی (Partially)</span>
                                    @else
                                        <span class="status-badge badge-unpaid">پرداخت نشده (Unpaid)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="/dashboard/check-book/{{ $invoice->id }}" class="btn btn-sm btn-light-primary border-0 shadow-none px-3" title="Details">
                                        <i class="feather icon-eye mr-1"></i> مشاهده جزئیات
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="feather icon-info" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                                    هیچ بل خریدی در سیستم ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CREATE INVOICE -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0 overflow-hidden" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);">
            <div class="modal-header border-bottom p-4 bg-light">
                <h5 class="font-weight-bold mb-0 text-primary">ایجاد بل خرید جدید</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/dashboard/check-book" method="post" id="createInvoiceForm">
                @csrf
                <div class="modal-body p-4 text-right" style="direction: rtl;">
                    <div class="form-group">
                        <label class="form-label-premium">نمبر بل خرید (Bill Number)</label>
                        <input type="text" name="invoice_number" class="form-control premium-input" value="{{ $nextBillNumber }}" readonly required>
                    </div>
                    <div class="form-group">
                        <label class="form-label-premium">نماینده (فروشنده قالین)</label>
                        <select name="agent_id" class="form-control premium-input" required>
                            <option value="">انتخاب فروشنده...</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->agent_id }}">{{ $agent->user->name }} ({{ $agent->account_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label-premium">تاریخ بل خرید</label>
                        <input type="date" name="date" class="form-control premium-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top p-4 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-4 rounded-lg" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-lg shadow">
                        <i class="feather icon-save mr-1"></i> ثبت بل خرید
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            dir: "rtl",
            width: '100%'
        });

        $("#createInvoiceForm").submit(function() {
            $(this).find(":submit").attr("disabled", "disabled").html('<i class="feather icon-loader mr-1"></i> در حال ثبت...');
        });
    });
</script>
@endsection