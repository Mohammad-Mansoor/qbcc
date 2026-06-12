@extends('dsh.master')
@section('title', 'ثبت قالین برای بخش تیاری')
@section('content')

<style>
    /* QBCC PREMIUM GLASSMORPHISM FINISHING (TAYAARI) THEME */
    :root {
        --qbcc-primary: #4f46e5;
        --qbcc-secondary: #6366f1;
        --qbcc-accent: #8b5cf6;
        --qbcc-success: #10b981;
        --qbcc-glass-bg: rgba(255, 255, 255, 0.75);
        --qbcc-glass-border: rgba(238, 242, 255, 0.8);
        --radius-xl: 24px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-premium: 0 20px 40px -15px rgba(99, 102, 241, 0.08), 0 1px 3px rgba(0, 0, 0, 0.02);
        --shadow-glow: 0 0 20px rgba(139, 92, 246, 0.15);
    }

    body {
        background-color: #faf5ff;
    }

    .premium-container {
        padding: 1.5rem;
    }

    /* CARD STYLE & GLASSMORPHISM */
    .glass-card {
        background: var(--qbcc-glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--qbcc-glass-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-premium);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 30px 60px -20px rgba(99, 102, 241, 0.12);
    }

    /* HERO PROFILE */
    .carpet-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
        color: white;
        padding: 2.5rem;
        border-radius: var(--radius-xl);
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-premium), var(--shadow-glow);
    }

    .carpet-hero::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
        top: -100px;
        right: -100px;
    }

    .carpet-no-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: white;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1.1rem;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* SPECIFICATION GRID */
    .spec-item {
        background: rgba(250, 245, 255, 0.8);
        border: 1px solid #f3e8ff;
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        transition: all 0.2s;
    }

    .spec-item:hover {
        background: white;
        border-color: #e9d5ff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.02);
    }

    .spec-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .spec-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    /* FORM STYLING */
    .form-label-modern {
        font-size: 0.88rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-control-modern {
        background: white;
        border: 2px solid #e0e7ff;
        border-radius: var(--radius-md);
        padding: 12px 16px;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        transition: all 0.2s;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.01);
    }

    .form-control-modern:focus {
        border-color: var(--qbcc-secondary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    /* BUTTONS */
    .btn-modern {
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 800;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
    }

    .btn-submit-premium {
        background: linear-gradient(135deg, #8b5cf6 0%, #4f46e5 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-submit-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45);
        filter: brightness(1.05);
    }

    .btn-cancel-premium {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-cancel-premium:hover {
        background: #e2e8f0;
        color: #475569;
    }
</style>

<div class="container-fluid premium-container" style="direction: rtl;">
    <!-- HEADER HERO SECTION -->
    <div class="carpet-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
        <div class="d-flex align-items-center gap-4">
            <div class="rounded-circle bg-white text-indigo d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.2rem; font-weight: 900; box-shadow: var(--shadow-premium);">
                <i class="feather icon-check-circle" style="color: var(--qbcc-primary);"></i>
            </div>
            <div>
                <h1 class="h2 font-weight-bold text-white mb-2" style="font-family: inherit;">ثبت قالین برای بخش تیاری</h1>
                <div class="carpet-no-badge">
                    <i class="feather icon-hash"></i> قالین نمبر: {{ $carpet->carpet_no }}
                </div>
            </div>
        </div>
        <div>
            <a href="/dashboard/list-buy-carpet" class="btn btn-light btn-modern shadow-sm">
                <i class="feather icon-arrow-right-circle"></i> لیست خرید قالین
            </a>
        </div>
    </div>

    <!-- MAIN FORM AND DETAIL LAYOUT -->
    <div class="row">
        <!-- FORM COLUMN -->
        <div class="col-lg-7 mb-4">
            <div class="card glass-card h-100">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0">
                    <h3 class="card-title font-weight-bold text-dark m-0" style="font-size: 1.25rem;">
                        <i class="feather icon-edit text-indigo mr-2"></i> تعیین تیم تیاری و گدام هدف
                    </h3>
                    <p class="text-muted small mt-2">لطفاً تیم تیاری و انبار نهایی (گدام) را برای این تخته قالین تعیین نمایید.</p>
                </div>
                <div class="card-body p-4">
                    <form action="/dashboard/carpet-wash/sent-to-finish/{{$carpet->carpet_id}}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->previous() }}">
                        
                        <div class="row">
                            <!-- SELECT TEAM -->
                            <div class="col-md-12 form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="feather icon-users text-indigo"></i> تیم تیاری (Finishing Team)
                                </label>
                                <select name="finishing_id" class="form-control form-control-modern select2 w-100" required>
                                    <option value="" disabled selected>انتخاب تیم تیاری...</option>
                                    @foreach ($finishing_teams as $team)
                                        @php
                                            $stats = isset($teamStats) ? $teamStats->get($team->id) : null;
                                            $qty = $stats ? $stats->qty : 0;
                                            $area = $stats ? $stats->total_area : 0;
                                        @endphp
                                        <option value="{{$team->id}}">
                                            {{$team->name}} (فعلاً نزد تیم: {{$qty}} تخته | {{number_format($area, 2)}} م.م)
                                        </option>
                                    @endforeach
                                </select>
                                @error('finishing_id') 
                                    <p class="text-danger small mt-1"><i class="feather icon-info"></i> {{trans('message.'.$message)}}</p> 
                                @enderror
                            </div>
                        </div>

                        <!-- SELECT WAREHOUSE -->
                        <div class="form-group mb-4">
                            <label class="form-label-modern">
                                <i class="feather icon-home text-indigo"></i> گدام هدف (Target Warehouse)
                            </label>
                            <select name="warehouse_id" class="form-control form-control-modern select2 w-100" required>
                                @foreach ($warehouses as $wh)
                                    <option value="{{$wh->id}}" {{ $wh->id == $defaultWarehouse ? 'selected' : '' }}>
                                        {{$wh->name}} 
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id') 
                                <p class="text-danger small mt-1"><i class="feather icon-info"></i> {{trans('message.'.$message)}}</p> 
                            @enderror
                        </div>

                        <!-- BUTTONS -->
                        <div class="d-flex justify-content-start align-items-center gap-3 pt-3 border-top">
                            <button class="btn btn-modern btn-submit-premium shadow" type="submit">
                                <i class="feather icon-save"></i> انتقال و ثبت نهایی به تیاری
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-modern btn-cancel-premium">
                                انصراف
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- DETAIL CARPET INFO COLUMN -->
        <div class="col-lg-5 mb-4">
            <div class="card glass-card">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0">
                    <h3 class="card-title font-weight-bold text-dark m-0" style="font-size: 1.25rem;">
                        <i class="feather icon-layers text-indigo mr-2"></i> مشخصات و ابعاد قالین
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- SPEC ITEM: WIDTH -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">عرض قالین</div>
                                <div class="spec-value" dir="ltr">{{ $carpet->width }} <small class="text-muted">متر</small></div>
                            </div>
                        </div>
                        
                        <!-- SPEC ITEM: HEIGHT -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">طول قالین</div>
                                <div class="spec-value" dir="ltr">{{ $carpet->height }} <small class="text-muted">متر</small></div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: AREA -->
                        <div class="col-12 mb-3">
                            <div class="spec-item bg-light border-indigo-light">
                                <div class="spec-label text-indigo font-weight-bold">مجموع مساحت</div>
                                <div class="spec-value text-indigo" dir="ltr" style="font-size: 1.35rem;">
                                    {{ number_format($carpet->area, 2) }} <small>متر مربع</small>
                                </div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: MAP NUMBER -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">نمبر نقشه / دیزاین</div>
                                <div class="spec-value">{{ $carpet->map_number ?? '---' }}</div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: PARCHA -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">نمبر پارچه</div>
                                <div class="spec-value">{{ $carpet->parcha_number ?? '---' }}</div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: COLORS -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">رنگ زمینه</div>
                                <div class="spec-value">{{ $carpet->field ?? '---' }}</div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: MARGIN -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">رنگ حاشیه</div>
                                <div class="spec-value">{{ $carpet->margin ?? '---' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize select2 if available
        if ($.fn.select2) {
            $('.select2').select2({
                dir: "rtl"
            });
        }
    });
</script>
@endsection
