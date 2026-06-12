@extends('dsh.master')
@section('title', 'ثبت قالین برای شستشو')
@section('content')

<style>
    /* QBCC PREMIUM GLASSMORPHISM WASHING THEME */
    :root {
        --qbcc-primary: #0f766e;
        --qbcc-secondary: #0d9488;
        --qbcc-accent: #06b6d4;
        --qbcc-success: #10b981;
        --qbcc-glass-bg: rgba(255, 255, 255, 0.75);
        --qbcc-glass-border: rgba(204, 251, 241, 0.8);
        --radius-xl: 24px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-premium: 0 20px 40px -15px rgba(13, 148, 136, 0.08), 0 1px 3px rgba(0, 0, 0, 0.02);
        --shadow-glow: 0 0 20px rgba(6, 182, 212, 0.15);
    }

    body {
        background-color: #f0fdfa;
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
        box-shadow: 0 30px 60px -20px rgba(13, 148, 136, 0.12);
    }

    /* HERO PROFILE */
    .carpet-hero {
        background: linear-gradient(135deg, #0f766e 0%, #06b6d4 100%);
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
        background: rgba(240, 253, 250, 0.8);
        border: 1px solid #ccfbf1;
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        transition: all 0.2s;
    }

    .spec-item:hover {
        background: white;
        border-color: #99f6e4;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.02);
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
        border: 2px solid #ccfbf1;
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
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
        outline: none;
    }

    .form-control-modern[readonly] {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
        cursor: not-allowed;
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
        background: linear-gradient(135deg, #06b6d4 0%, #0f766e 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }

    .btn-submit-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.45);
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
            <div class="rounded-circle bg-white text-teal d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.2rem; font-weight: 900; box-shadow: var(--shadow-premium);">
                <i class="feather icon-droplet" style="color: var(--qbcc-primary);"></i>
            </div>
            <div>
                <h1 class="h2 font-weight-bold text-white mb-2" style="font-family: inherit;">ثبت قالین برای شستشو</h1>
                <div class="carpet-no-badge">
                    <i class="feather icon-hash"></i> قالین نمبر: {{ $carpetId->carpet_no }}
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
                        <i class="feather icon-edit text-teal mr-2"></i> تعیین تیم شوینده و شماره شستشو
                    </h3>
                    <p class="text-muted small mt-2">لطفاً تیم شستشو، شماره مرجع شست و انبار موقت را برای این تخته قالین تعیین نمایید.</p>
                </div>
                <div class="card-body p-4">
                    <form action="/dashboard/washing-team/sent-to-washing/{{$carpetId->carpet_id}}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- SELECT TEAM -->
                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="feather icon-users text-teal"></i> تیم شوینده
                                </label>
                                <select name="team_id" class="form-control form-control-modern select2 w-100" required>
                                    <option value="" disabled selected>انتخاب تیم شوینده...</option>
                                    @foreach ($washing_team as $team)
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
                                @error('team_id') 
                                    <p class="text-danger small mt-1"><i class="feather icon-info"></i> {{trans('message.'.$message)}}</p> 
                                @enderror
                            </div>

                            <!-- WASH NUMBER -->
                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="feather icon-file-text text-teal"></i> نمبر شستشو
                                </label>
                                <div class="input-group">
                                    <select name="wash_number" id="wash_number" required class="form-control form-control-modern select2 w-100">
                                        <option value="">-- انتخاب نمبر شستشو --</option>
                                        @foreach($openBatches as $batch)
                                            <option value="{{ $batch->reference_number }}">{{ $batch->reference_number }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success font-weight-bold" id="btn_generate_wash_number" title="ایجاد نمبر جدید" style="border-top-left-radius: var(--radius-md); border-bottom-left-radius: var(--radius-md); height: 50px;">
                                            <i class="feather icon-plus"></i> ایجاد
                                        </button>
                                    </div>
                                </div>
                                @error('wash_number')
                                    <p class="text-danger small mt-1"><i class="feather icon-info"></i> {{$message}}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- SELECT WAREHOUSE -->
                        <div class="form-group mb-4">
                            <label class="form-label-modern">
                                <i class="feather icon-home text-teal"></i> گدام شست (WIP Warehouse)
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
                                <i class="feather icon-save"></i> انتقال و ثبت نهایی شستشو
                            </button>
                            <button class="btn btn-modern btn-cancel-premium" type="reset">
                                انصراف
                            </button>
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
                        <i class="feather icon-layers text-teal mr-2"></i> مشخصات و ابعاد قالین
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- SPEC ITEM: WIDTH -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">عرض قالین</div>
                                <div class="spec-value" dir="ltr">{{ $carpetId->width }} <small class="text-muted">متر</small></div>
                            </div>
                        </div>
                        
                        <!-- SPEC ITEM: HEIGHT -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">طول قالین</div>
                                <div class="spec-value" dir="ltr">{{ $carpetId->height }} <small class="text-muted">متر</small></div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: AREA -->
                        <div class="col-12 mb-3">
                            <div class="spec-item bg-light border-teal-light">
                                <div class="spec-label text-teal font-weight-bold">مجموع مساحت</div>
                                <div class="spec-value text-teal" dir="ltr" style="font-size: 1.35rem;">
                                    {{ number_format($carpetId->area, 2) }} <small>متر مربع</small>
                                </div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: MAP NUMBER -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">نمبر نقشه / دیزاین</div>
                                <div class="spec-value">{{ $carpetId->map_number ?? '---' }}</div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: PARCHA -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">نمبر پارچه</div>
                                <div class="spec-value">{{ $carpetId->parcha_number ?? '---' }}</div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: COLORS -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">رنگ زمینه</div>
                                <div class="spec-value">{{ $carpetId->field ?? '---' }}</div>
                            </div>
                        </div>

                        <!-- SPEC ITEM: MARGIN -->
                        <div class="col-6 mb-3">
                            <div class="spec-item">
                                <div class="spec-label">رنگ حاشیه</div>
                                <div class="spec-value">{{ $carpetId->margin ?? '---' }}</div>
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

        // AJAX Generate Wash Number
        $('#btn_generate_wash_number').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="feather icon-loader fa-spin"></i>');
            
            $.ajax({
                url: '/dashboard/batches/wash',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.batch) {
                        var newRef = response.batch.reference_number;
                        if ($('#wash_number option[value="' + newRef + '"]').length === 0) {
                            var newOption = new Option(newRef, newRef, true, true);
                            $('#wash_number').append(newOption).trigger('change');
                        } else {
                            $('#wash_number').val(newRef).trigger('change');
                        }
                        if (typeof swal === 'function') {
                            swal("موفقیت", "نمبر شستشو جدید با موفقیت ایجاد و انتخاب گردید: " + newRef, "success");
                        } else {
                            alert("نمبر شستشو جدید با موفقیت ایجاد و انتخاب گردید: " + newRef);
                        }
                    } else {
                        if (typeof swal === 'function') {
                            swal("خطا", "ایجاد نمبر با خطا مواجه شد.", "error");
                        } else {
                            alert("ایجاد نمبر با خطا مواجه شد.");
                        }
                    }
                },
                error: function() {
                    if (typeof swal === 'function') {
                        swal("خطا", "ارتباط با سرور برقرار نشد.", "error");
                    } else {
                        alert("ارتباط با سرور برقرار نشد.");
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="feather icon-plus"></i> ایجاد');
                }
            });
        });
    });
</script>
@endsection