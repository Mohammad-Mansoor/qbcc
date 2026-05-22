@extends('dsh.master')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    /* =========================================
       ENTERPRISE UX DESIGN - V2 REDESIGN
       ========================================= */
    :root {
        --color-bg-mesh: radial-gradient(at 0% 0%, hsla(253,16%,7%,0) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,0) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(242, 80%, 85%, 0.5) 0, transparent 50%), radial-gradient(at 0% 100%, hsla(160, 80%, 85%, 0.3) 0, transparent 50%);
        --color-bg: #f4f7fe;
        
        --color-primary: #4f46e5;
        --color-primary-hover: #4338ca;
        --color-primary-light: #e0e7ff;
        --color-primary-glow: rgba(79, 70, 229, 0.3);
        
        --color-success: #10b981;
        --color-success-light: #d1fae5;
        --color-warning: #f59e0b;
        --color-warning-light: #fef3c7;
        --color-danger: #ef4444;
        
        --color-text-main: #0f172a;
        --color-text-muted: #64748b;
        
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.8);
        --glass-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        
        --radius-xl: 24px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        
        --transition-bounce: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body, .pcoded-main-container {
        font-family: 'Vazirmatn', sans-serif !important;
        background-color: var(--color-bg) !important;
        background-image: var(--color-bg-mesh) !important;
        background-attachment: fixed !important;
        color: var(--color-text-main);
        line-height: 1.6;
    }

    /* GLASS CARDS */
    .ux-glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--glass-shadow);
        padding: 32px;
        margin-bottom: 24px;
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }
    .ux-glass-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        z-index: 1;
    }
    .ux-glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.12);
    }

    /* TYPOGRAPHY */
    .ux-title-main {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--color-text-main);
        margin: 0;
        background: linear-gradient(45deg, #0f172a, #334155);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .ux-title-sub {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--color-text-main);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ux-title-sub i {
        background: var(--color-primary-light);
        color: var(--color-primary);
        padding: 8px;
        border-radius: var(--radius-sm);
        font-size: 1.1rem;
    }

    /* HEADER */
    .ux-header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 36px;
        animation: fadeUp 0.6s ease-out backwards;
    }
    .ux-header-left {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    .ux-icon-box {
        width: 72px;
        height: 72px;
        background: white;
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--color-primary);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
        border: 1px solid rgba(79, 70, 229, 0.1);
        transition: var(--transition-bounce);
    }
    .ux-icon-box:hover {
        transform: scale(1.05) rotate(5deg);
    }

    /* BADGES */
    .ux-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
        transition: var(--transition-smooth);
    }
    .ux-badge.success { background: var(--color-success-light); color: var(--color-success); border-color: rgba(16, 185, 129, 0.2); }
    .ux-badge.warning { background: var(--color-warning-light); color: #92400e; border-color: rgba(245, 158, 11, 0.2); }
    .ux-badge.primary { background: var(--color-primary-light); color: var(--color-primary); border-color: rgba(79, 70, 229, 0.2); }
    .ux-badge:hover { transform: translateY(-2px); }

    /* STATS GRID */
    .ux-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        margin-bottom: 36px;
    }
    .ux-stat-box {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-xl);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid rgba(255,255,255,0.9);
        transition: var(--transition-bounce);
        animation: fadeUp 0.6s ease-out backwards;
    }
    .ux-stat-box:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        background: white;
    }
    .ux-stat-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .ux-stat-icon-wrap.primary { background: var(--color-primary-light); color: var(--color-primary); }
    .ux-stat-icon-wrap.success { background: var(--color-success-light); color: var(--color-success); }
    .ux-stat-icon-wrap.warning { background: var(--color-warning-light); color: var(--color-warning); }
    .ux-stat-icon-wrap.dark { background: #f1f5f9; color: #334155; }
    
    .ux-stat-content { flex: 1; }
    .ux-stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }
    .ux-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--color-text-main);
        line-height: 1.2;
    }

    /* TABS */
    .ux-tabs-nav {
        display: flex;
        gap: 12px;
        margin-bottom: 32px;
        background: rgba(255,255,255,0.5);
        padding: 8px;
        border-radius: var(--radius-lg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        animation: fadeUp 0.6s ease-out backwards;
    }
    .ux-tab-btn {
        flex: 1;
        background: transparent;
        border: none;
        padding: 14px 20px;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--color-text-muted);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: var(--transition-bounce);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .ux-tab-btn:hover {
        background: rgba(255,255,255,0.8);
        color: var(--color-primary);
    }
    .ux-tab-btn.active {
        background: white;
        color: var(--color-primary);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transform: scale(1.02);
    }

    /* MODERN INPUTS */
    .ux-form-group {
        position: relative;
        margin-bottom: 24px;
    }
    .ux-input {
        width: 100%;
        padding: 16px 20px;
        background: rgba(255,255,255,0.8);
        border: 2px solid transparent;
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--color-text-main);
        transition: var(--transition-smooth);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .ux-input:focus {
        background: white;
        border-color: var(--color-primary);
        outline: none;
        box-shadow: 0 0 0 5px var(--color-primary-glow);
    }
    .ux-input[readonly] {
        background: #f1f5f9;
        color: var(--color-text-muted);
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .ux-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-text-muted);
        margin-bottom: 8px;
        padding-right: 4px;
    }

    /* BUTTONS */
    .ux-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 32px;
        border-radius: var(--radius-md);
        font-weight: 800;
        font-size: 1.05rem;
        border: none;
        cursor: pointer;
        transition: var(--transition-bounce);
        letter-spacing: 0.02em;
    }
    .ux-btn-primary {
        background: linear-gradient(135deg, var(--color-primary), #3730a3);
        color: white;
        box-shadow: 0 10px 25px var(--color-primary-glow);
    }
    .ux-btn-primary:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 15px 35px var(--color-primary-glow);
        color: white;
    }
    .ux-btn-outline {
        background: white;
        border: 2px solid #e2e8f0;
        color: var(--color-text-main);
        font-weight: 700;
    }
    .ux-btn-outline:hover {
        background: #f8fafc;
        border-color: var(--color-primary);
        color: var(--color-primary);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    /* DETAILS LIST */
    .ux-details-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .ux-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: rgba(248, 250, 252, 0.5);
        border-radius: var(--radius-md);
        border: 1px solid rgba(226, 232, 240, 0.5);
        transition: var(--transition-smooth);
    }
    .ux-detail-item:hover {
        background: white;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .ux-detail-label {
        font-size: 0.95rem;
        color: var(--color-text-muted);
        font-weight: 700;
    }
    .ux-detail-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--color-text-main);
        text-align: left;
    }

    /* TIMELINE */
    .ux-timeline {
        position: relative;
        padding-right: 36px;
    }
    .ux-timeline::before {
        content: '';
        position: absolute;
        right: 15px;
        top: 8px;
        bottom: 8px;
        width: 4px;
        background: linear-gradient(to bottom, var(--color-primary), rgba(79, 70, 229, 0.1));
        border-radius: 4px;
    }
    .ux-timeline-item {
        position: relative;
        margin-bottom: 36px;
        animation: fadeLeft 0.6s ease-out backwards;
    }
    .ux-timeline-dot {
        position: absolute;
        right: -36px;
        top: 4px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: white;
        border: 6px solid var(--color-primary);
        box-shadow: 0 0 0 6px var(--color-primary-glow);
        z-index: 2;
        transition: var(--transition-bounce);
    }
    .ux-timeline-item:hover .ux-timeline-dot {
        transform: scale(1.2);
    }
    .ux-timeline-content {
        background: white;
        border-radius: var(--radius-xl);
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.03);
        transition: var(--transition-smooth);
    }
    .ux-timeline-content:hover {
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        transform: translateX(-6px);
    }
    .ux-timeline-time {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ux-timeline-title {
        font-weight: 800;
        font-size: 1.15rem;
        color: var(--color-text-main);
        margin-bottom: 6px;
    }

    /* TABLES */
    .ux-table-wrap {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--glass-border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: white;
    }
    .ux-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ux-table th {
        background: #f8fafc;
        padding: 18px 24px;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: right;
        border-bottom: 2px solid #e2e8f0;
    }
    .ux-table td {
        padding: 18px 24px;
        font-size: 1.05rem;
        font-weight: 600;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        transition: var(--transition-smooth);
    }
    .ux-table tbody tr:hover td {
        background: #f8fafc;
    }
    .ux-table tbody tr:last-child td { border-bottom: none; }

    /* ANIMATIONS */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeLeft {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* STAGGER DELAYS */
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }
    
    [dir="rtl"] .ux-timeline { padding-right: 36px; padding-left: 0; }
    [dir="rtl"] .ux-timeline::before { right: 15px; left: auto; }
    [dir="rtl"] .ux-timeline-dot { right: -36px; left: auto; }
    [dir="rtl"] .ux-table th { text-align: right; }
    [dir="rtl"] .ux-timeline-content:hover { transform: translateX(-6px); }
</style>

<div class="container-fluid py-4" dir="rtl">

    <!-- HEADER -->
    <div class="ux-header-wrapper">
        <div class="ux-header-left">
            <div class="ux-icon-box">
                <i class="feather icon-layers"></i>
            </div>
            <div>
                <h2 class="ux-title-main">شناسنامه قالین: {{ $carpet->carpet_no }}</h2>
                <div style="display: flex; gap: 12px; margin-top: 8px; align-items: center;">
                    <span class="ux-badge {{ $carpet->status == 5 ? 'success' : 'warning' }}">
                        @if($carpet->status == 5) <i class="feather icon-check-circle"></i> تکمیل شده @else <i class="feather icon-clock"></i> در حال کار @endif
                    </span>
                    <span class="ux-badge primary">
                        <i class="feather icon-map-pin"></i> {{ $carpet->warehouse->warehouse ?? 'انبار مرکزی' }}
                    </span>
                </div>
            </div>
        </div>
        <div>
            <button onclick="window.print()" class="ux-btn ux-btn-outline hide-on-print">
                <i class="feather icon-printer"></i> چاپ پرونده
            </button>
        </div>
    </div>

    <!-- STATS GRID -->
    <div class="ux-stats-grid">
        <div class="ux-stat-box delay-1">
            <div class="ux-stat-icon-wrap primary">
                <i class="feather icon-maximize"></i>
            </div>
            <div class="ux-stat-content">
                <div class="ux-stat-label">مساحت (متر مربع)</div>
                <div class="ux-stat-value">{{ $carpet->area }} <span style="font-size: 0.9rem; font-weight: 600; color: var(--color-text-muted);">m²</span></div>
            </div>
        </div>
        <div class="ux-stat-box delay-2">
            <div class="ux-stat-icon-wrap success">
                <i class="feather icon-dollar-sign"></i>
            </div>
            <div class="ux-stat-content">
                <div class="ux-stat-label">هزینه تولید (USD)</div>
                <div class="ux-stat-value">${{ number_format($carpet->total_price, 2) }}</div>
            </div>
        </div>
        <div class="ux-stat-box delay-3">
            <div class="ux-stat-icon-wrap dark">
                <i class="feather icon-book"></i>
            </div>
            <div class="ux-stat-content">
                <div class="ux-stat-label">ارزش دفتری (AFN)</div>
                <div class="ux-stat-value">{{ number_format($carpet->total_price_af, 0) }}</div>
            </div>
        </div>
        <div class="ux-stat-box delay-4">
            <div class="ux-stat-icon-wrap warning">
                <i class="feather icon-package"></i>
            </div>
            <div class="ux-stat-content">
                <div class="ux-stat-label">مواد مصرفی (KG)</div>
                <div class="ux-stat-value">{{ $carpetMaterials->sum('amount') }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- MAIN CONTENT COLUMN -->
        <div class="col-xl-8 col-lg-7">
            
            <div class="ux-tabs-nav delay-5 hide-on-print">
                <button class="ux-tab-btn active" data-target="#tab-spec">
                    <i class="feather icon-info"></i> مشخصات فنی
                </button>
                <button class="ux-tab-btn" data-target="#tab-audit">
                    <i class="feather icon-check-square"></i> چک بٌک (Valuation)
                </button>
                <button class="ux-tab-btn" data-target="#tab-material">
                    <i class="feather icon-layers"></i> مصرف مواد (Inventory)
                </button>
            </div>

            <!-- TAB: SPECS -->
            <div class="ux-glass-card tab-content-panel animate__animated animate__fadeIn" id="tab-spec">
                <h4 class="ux-title-sub">
                    <i class="feather icon-file-text"></i> جزئیات ساختاری و شناسنامه
                </h4>
                <div class="row mt-4">
                    <div class="col-md-6" style="border-left: 2px dashed rgba(0,0,0,0.05);">
                        <div class="ux-details-list">
                            <div class="ux-detail-item">
                                <span class="ux-detail-label">شماره فرمایش</span>
                                <span class="ux-detail-value">{{ $carpet->carpet_order->order_number ?? '---' }}</span>
                            </div>
                            <div class="ux-detail-item">
                                <span class="ux-detail-label">ابعاد (طول × عرض)</span>
                                <span class="ux-detail-value" dir="ltr">{{ $carpet->width }}m x {{ $carpet->height }}m</span>
                            </div>
                            <div class="ux-detail-item">
                                <span class="ux-detail-label">نوعیت قالین</span>
                                <span class="ux-detail-value">{{ $carpet->type->carpet_type ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ux-details-list" style="padding-right: 15px;">
                            <div class="ux-detail-item">
                                <span class="ux-detail-label">کوالتی</span>
                                <span class="ux-detail-value">{{ $carpet->quality->quality ?? 'N/A' }}</span>
                            </div>
                            <div class="ux-detail-item">
                                <span class="ux-detail-label">زمینه / حاشیه</span>
                                <span class="ux-detail-value">{{ $carpet->field }} / {{ $carpet->margin }}</span>
                            </div>
                            <div class="ux-detail-item">
                                <span class="ux-detail-label">تاریخ ثبت اولیه</span>
                                <span class="ux-detail-value">{{ $carpet->date }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: AUDIT -->
            <div class="ux-glass-card tab-content-panel animate__animated animate__fadeIn" id="tab-audit" style="display: none;">
                @if(!$carpetCheckBook)
                    <h4 class="ux-title-sub">
                        <i class="feather icon-edit-3"></i> ثبت مرحله چک بٌک
                    </h4>
                    <form action="/dashboard/check-book" method="post" id="cb-form" class="mt-4">
                        @csrf
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                        <input type="hidden" name="agent_carpet" value="agent_carpet">
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ux-form-group">
                                    <label class="ux-label">تاریخ چک بٌک <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="ux-input" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ux-form-group">
                                    <label class="ux-label">حساب دیبیت (DR) <span class="text-danger">*</span></label>
                                    <select name="debit_account_id" class="ux-input select2" required>
                                        <option value="">انتخاب حساب دیبیت</option>
                                        @foreach($debitAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ux-form-group">
                                    <label class="ux-label">حساب کریدیت (CR) <span class="text-danger">*</span></label>
                                    <select name="credit_account_id" class="ux-input select2" required>
                                        <option value="">انتخاب حساب کریدیت</option>
                                        @foreach($creditAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ux-form-group">
                                    <label class="ux-label">شماره چک بٌک</label>
                                    <input type="text" name="check_number" value="{{$CheckNo}}" class="ux-input" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ux-form-group">
                                    <label class="ux-label">ارز قیمت گذاری</label>
                                    <select name="currency_id" id="cb_c" class="ux-input" required>
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" {{ $curr->code == 'AFN' ? 'selected' : '' }}>{{ $curr->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="exchange_rate" id="cb_r">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ux-form-group">
                                    <label id="cb_l" class="ux-label">فیس کچایی</label>
                                    <input type="number" step="0.01" name="original_price" id="cb_p" class="ux-input cb-calc">
                                    <input type="hidden" name="kachaee_amount" id="cb_af">
                                    <input type="hidden" name="kachaee_dollar_amount" id="cb_us">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ux-form-group">
                                    <label class="ux-label">طول نهایی (m)</label>
                                    <input type="number" step="0.01" name="height" id="cb_h" value="{{$carpet->height}}" class="ux-input cb-calc">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ux-form-group">
                                    <label class="ux-label">عرض نهایی (m)</label>
                                    <input type="number" step="0.01" name="width" id="cb_w" value="{{$carpet->width}}" class="ux-input cb-calc">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ux-form-group">
                                    <label class="ux-label">مساحت کل (m²)</label>
                                    <input type="text" name="area" id="cb_a" class="ux-input" readonly style="font-weight: 800; color: var(--color-primary);">
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 16px;">
                            <button type="submit" class="ux-btn ux-btn-primary">
                                <i class="feather icon-check"></i> تایید و بروزرسانی سیستم
                            </button>
                        </div>
                    </form>
                @else
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                        <h4 class="ux-title-sub" style="margin: 0;">
                            <i class="feather icon-check-circle" style="background: var(--color-success-light); color: var(--color-success);"></i> ارزیابی نهایی تکمیل شده
                        </h4>
                    </div>
                    <div style="background: white; border: 1px solid rgba(0,0,0,0.05); border-radius: var(--radius-xl); padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                        <div class="row">
                            <div class="col-md-6" style="border-left: 2px dashed rgba(0,0,0,0.05);">
                                <div class="ux-details-list">
                                    <div class="ux-detail-item">
                                        <span class="ux-detail-label">رفرنس</span>
                                        <span class="ux-detail-value">{{ $carpetCheckBook->check_number }}</span>
                                    </div>
                                    <div class="ux-detail-item">
                                        <span class="ux-detail-label">مساحت نهایی</span>
                                        <span class="ux-detail-value">{{ $carpetCheckBook->area }} m²</span>
                                    </div>
                                    <div class="ux-detail-item">
                                        <span class="ux-detail-label">ابعاد</span>
                                        <span class="ux-detail-value" dir="ltr">{{ $carpetCheckBook->width }}m x {{ $carpetCheckBook->height }}m</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ux-details-list" style="padding-right: 15px;">
                                    <div class="ux-detail-item">
                                        <span class="ux-detail-label">ارزش (AFN)</span>
                                        <span class="ux-detail-value">{{ number_format($carpetCheckBook->kachaee_amount, 0) }} AF</span>
                                    </div>
                                    <div class="ux-detail-item">
                                        <span class="ux-detail-label">ارزش (USD)</span>
                                        <span class="ux-detail-value" style="color: var(--color-success);">${{ number_format($carpetCheckBook->kachaee_dollar_amount, 2) }}</span>
                                    </div>
                                    <div class="ux-detail-item">
                                        <span class="ux-detail-label">تاریخ ارزیابی</span>
                                        <span class="ux-detail-value">{{ $carpetCheckBook->date }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- TAB: MATERIAL -->
            <div class="ux-glass-card tab-content-panel animate__animated animate__fadeIn" id="tab-material" style="display: none;">
                <h4 class="ux-title-sub">
                    <i class="feather icon-plus"></i> صدور مواد خام جدید
                </h4>
                <form action="/dashboard/carpet-material" method="post" id="mt-form" class="mt-4">
                    @csrf
                    <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                    <input type="hidden" name="agent_id" value="{{$carpet->agent->agent_id}}">
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="ux-form-group">
                                <label class="ux-label">تاریخ صدور <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="ux-input" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="ux-form-group">
                                <label class="ux-label">گدام مواد <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="ux-input select2" required>
                                    <option value="">انتخاب گدام</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="ux-form-group">
                                <label class="ux-label">حساب دیبیت (DR) <span class="text-danger">*</span></label>
                                <select name="debit_account_id" class="ux-input select2" required>
                                    <option value="">انتخاب حساب دیبیت</option>
                                    @foreach($debitAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="ux-form-group">
                                <label class="ux-label">حساب کریدیت (CR) <span class="text-danger">*</span></label>
                                <select name="credit_account_id" class="ux-input select2" required>
                                    <option value="">انتخاب حساب کریدیت</option>
                                    @foreach($creditAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="ux-form-group">
                                <label class="ux-label">کتگوری مواد</label>
                                <select name="category_id" class="ux-input select2">
                                    @foreach ($categories as $category)
                                        <option value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ux-form-group">
                                <label class="ux-label">نوع مواد</label>
                                <select name="type_id" class="ux-input select2">
                                    @foreach ($material_types as $type)
                                        <option value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ux-form-group">
                                <label class="ux-label">ارز</label>
                                <select name="currency_id" id="m_curr" class="ux-input select2" required>
                                    @foreach($currencies as $curr)
                                        <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" {{ $curr->code == 'AFN' ? 'selected' : '' }}>{{ $curr->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="exchange_rate" id="m_rt">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="ux-form-group">
                                <label class="ux-label">مقدار (KG)</label>
                                <input type="number" step="0.01" name="amount" id="m_amt" class="ux-input mt-calc">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ux-form-group">
                                <label id="m_lbl" class="ux-label">قیمت فی واحد</label>
                                <input type="number" step="0.01" name="original_price" id="m_prc" class="ux-input mt-calc">
                                <input type="hidden" name="price" id="m_hid">
                                <input type="hidden" name="total_price_af" id="m_total_af">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ux-form-group">
                                <label class="ux-label">مجموع کل (USD)</label>
                                <input type="text" name="total_price" id="m_usd" class="ux-input" readonly style="font-weight: 800; color: var(--color-success);">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-end mt-2">
                            <button type="submit" class="ux-btn ux-btn-primary" style="min-width: 200px;">
                                <i class="feather icon-plus"></i> ثبت صدور مواد
                            </button>
                        </div>
                    </div>
                </form>

                <div style="margin-top: 40px;">
                    <h5 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: var(--color-text-main);">سابقه مواد صادر شده</h5>
                    <div class="ux-table-wrap">
                        <table class="ux-table">
                            <thead>
                                <tr>
                                    <th>شرح مواد</th>
                                    <th>مقدار</th>
                                    <th>فی واحد</th>
                                    <th>مجموع (USD)</th>
                                    <th>ارز</th>
                                    <th>نرخ تبادله</th>
                                    <th>تاریخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carpetMaterials as $mat)
                                    <tr>
                                        <td style="font-weight: 700;">{{ $mat->category->material_category }}</td>
                                        <td>{{ $mat->amount }} <span style="font-size: 0.85rem; color: var(--color-text-muted);">kg</span></td>
                                        <td>${{ number_format($mat->total_price / $mat->amount, 2) }}</td>
                                        <td style="font-weight: 800; color: var(--color-success);">${{ number_format($mat->total_price, 2) }}</td>
                                        <td><span class="badge badge-info">{{ $mat->currency_code ?? 'AFN' }}</span></td>
                                        <td style="font-weight: 600;">{{ number_format($mat->exchange_rate ?? 1, 2) }}</td>
                                        <td style="color: var(--color-text-muted);">{{ $mat->date }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; color: var(--color-text-muted); padding: 48px; font-weight: 600;">هیچ موادی تا کنون صادر نشده است.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT SIDEBAR: TIMELINE -->
        <div class="col-xl-4 col-lg-5">
            <div class="ux-glass-card delay-5">
                <h4 class="ux-title-sub">
                    <i class="feather icon-activity"></i> روند تولید و تفتیش
                </h4>
                
                <div class="ux-timeline mt-4">
                    <!-- Registration Node -->
                    <div class="ux-timeline-item">
                        <div class="ux-timeline-dot"></div>
                        <div class="ux-timeline-content">
                            <div class="ux-timeline-time"><i class="feather icon-calendar"></i> {{ $carpet->created_at->format('Y-m-d H:i') }}</div>
                            <div class="ux-timeline-title">ثبت اولیه قطعه در سیستم</div>
                            <div style="font-size: 0.9rem; font-weight: 600; color: var(--color-text-muted);">توسط: {{ Auth::user()->name }}</div>
                        </div>
                    </div>

                    <!-- Material Nodes -->
                    @foreach($carpetMaterials as $mat)
                        <div class="ux-timeline-item">
                            <div class="ux-timeline-dot" style="border-color: var(--color-warning); box-shadow: 0 0 0 6px var(--color-warning-light);"></div>
                            <div class="ux-timeline-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="ux-timeline-time" style="color: var(--color-warning);"><i class="feather icon-layers"></i> {{ $mat->date }}</div>
                                    <form action="/dashboard/carpet-material/{{$mat->id}}" method="POST" onsubmit="return confirm('آیا مطمین هستید که این صدور مواد را لغو میکنید؟ (این عملیه مقدار مواد را در گدام دوباره جمع و روزنامچه را معکوس میکند)')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; font-family: 'Vazirmatn', sans-serif;">
                                            <i class="feather icon-trash-2"></i> لغو صدور
                                        </button>
                                    </form>
                                </div>
                                <div class="ux-timeline-title">صدور {{ $mat->amount }}kg {{ $mat->category->material_category }}</div>
                                <div class="mt-1" style="font-size: 0.8rem; color: var(--color-text-muted);">
                                    ارز: <span class="badge badge-info">{{ $mat->currency_code ?? 'AFN' }}</span> | 
                                    نرخ: <span class="badge badge-light text-dark">{{ number_format($mat->exchange_rate ?? 1, 2) }}</span>
                                </div>
                                <div style="font-size: 0.9rem; font-weight: 600; color: var(--color-text-muted);">ارزش معادل: <span style="color: var(--color-text-main);">${{ $mat->total_price }}</span></div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Audit Node -->
                    @if($carpetCheckBook)
                        <div class="ux-timeline-item">
                            <div class="ux-timeline-dot" style="border-color: var(--color-success); box-shadow: 0 0 0 6px var(--color-success-light);"></div>
                            <div class="ux-timeline-content" style="border-right: 4px solid var(--color-success);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="ux-timeline-time" style="color: var(--color-success);"><i class="feather icon-check-circle"></i> {{ $carpetCheckBook->date }}</div>
                                    <form action="/dashboard/check-book/{{$carpetCheckBook->id}}" method="POST" onsubmit="return confirm('آیا مطمین هستید که این چک بٌک را لغو میکنید؟ (این عملیه قیمت قالین را به حالت اول برگردانده و روزنامچه را معکوس میکند)')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; font-family: 'Vazirmatn', sans-serif;">
                                            <i class="feather icon-trash-2"></i> لغو ارزیابی
                                        </button>
                                    </form>
                                </div>
                                <div class="ux-timeline-title">ارزیابی نهایی و تکمیل (چک بک)</div>
                                <div class="mt-1" style="font-size: 0.8rem; color: var(--color-text-muted);">
                                    ارز: <span class="badge badge-info">{{ $carpetCheckBook->currency_code ?? 'AFN' }}</span> | 
                                    نرخ: <span class="badge badge-light text-dark">{{ number_format($carpetCheckBook->exchange_rate ?? 1, 2) }}</span>
                                </div>
                                <div style="font-size: 0.9rem; font-weight: 600; color: var(--color-text-muted);">Ref: <span style="color: var(--color-text-main);">{{ $carpetCheckBook->check_number }}</span></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- SELECT2 INITIALIZATION ---
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body'),
            placeholder: "جستجو و انتخاب حساب..."
        });

        // --- UX TAB ENGINE ---
        const tabBtns = document.querySelectorAll('.ux-tab-btn');
        const tabPanels = document.querySelectorAll('.tab-content-panel');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active classes
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanels.forEach(p => p.style.display = 'none');
                
                // Add active class to clicked
                btn.classList.add('active');
                const target = document.querySelector(btn.getAttribute('data-target'));
                target.style.display = 'block';
            });
        });

        // --- CALC ENGINE (AUDIT) ---
        function runCB() {
            let h = parseFloat(document.getElementById('cb_h')?.value) || 0;
            let w = parseFloat(document.getElementById('cb_w')?.value) || 0;
            if(document.getElementById('cb_a')) document.getElementById('cb_a').value = (h * w).toFixed(2);

            let sel = document.getElementById('cb_c');
            if(!sel) return;
            let opt = sel.options[sel.selectedIndex];
            let rate = parseFloat(opt.getAttribute('data-rate')) || 0;
            let prc = parseFloat(document.getElementById('cb_p').value) || 0;

            document.getElementById('cb_l').innerText = 'فیس کچایی (' + opt.text + ')';
            document.getElementById('cb_r').value = rate;

            let us = prc * rate;
            document.getElementById('cb_us').value = us.toFixed(2);

            let afnOpt = document.querySelector('#cb_c option[value="AFN"]');
            let afR = afnOpt ? parseFloat(afnOpt.getAttribute('data-rate')) : 0.0158;
            document.getElementById('cb_af').value = (us / afR).toFixed(0);
        }
        
        document.querySelectorAll('.cb-calc, #cb_c').forEach(el => {
            el.addEventListener('input', runCB);
            el.addEventListener('change', runCB);
        });
        if(document.getElementById('cb_c')) runCB();

        // --- CALC ENGINE (MATERIAL) ---
        function runMT() {
            let amt = parseFloat(document.getElementById('m_amt')?.value) || 0;
            let sel = document.getElementById('m_curr');
            if(!sel) return;
            let opt = sel.options[sel.selectedIndex];
            let rate = parseFloat(opt.getAttribute('data-rate')) || 0;
            let p_kg = parseFloat(document.getElementById('m_prc').value) || 0;

            document.getElementById('m_lbl').innerText = 'قیمت فی واحد (' + opt.text + ')';
            document.getElementById('m_rt').value = rate;

            let t_us = (amt * p_kg) * rate;
            document.getElementById('m_usd').value = t_us.toFixed(2);

            let afnOpt = document.querySelector('#m_curr option[value="AFN"]');
            let afR = afnOpt ? parseFloat(afnOpt.getAttribute('data-rate')) : 0.0158;
            let t_af = (t_us / afR);
            document.getElementById('m_total_af').value = t_af.toFixed(2);
            document.getElementById('m_hid').value = (t_af / amt || 0).toFixed(2);
        }

        document.querySelectorAll('.mt-calc, #m_curr').forEach(el => {
            el.addEventListener('input', runMT);
            el.addEventListener('change', runMT);
        });
        if(document.getElementById('m_curr')) runMT();
    });
</script>
@endsection
