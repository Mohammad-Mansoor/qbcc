@extends('dsh.master')
@section('title', 'Contract Carpet Registry - QBCC Forensic ERP')

@section('content')
<style>
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-glass: rgba(255, 255, 255, 0.7);
        --qbcc-border: #e2e8f0;
        --radius-lg: 16px;
        --shadow-soft: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
    }

    .table-xs td, .table-xs th { padding: 8px 12px; font-size: 13px; vertical-align: middle; }
    .table-modern thead th { background: #f1f5f9; color: #475569; font-weight: 700; text-transform: uppercase; border: none; }
    
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .badge-wip { background: #fef3c7; color: #92400e; }
    .badge-done { background: #d1fae5; color: #065f46; }

    .forensic-snapshot {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 10px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .form-label-premium { font-weight: 700; color: #334155; font-size: 12px; margin-bottom: 5px; display: block; }
    .premium-input { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; transition: all 0.2s; }
    .premium-input:focus { border-color: var(--qbcc-primary); box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1); }
</style>

<div class="row">
    <div class="col-sm-12">
        <!-- HEADER -->
        <div class="glass-card p-4 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="font-weight-bold mb-1">لیست قالین‌های قراردادی (WIP Registry)</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-activity mr-1"></i> مدیریت موجودی در حال بافت و ارزش‌گذاری اسعاری (Forensic Ready)</p>
            </div>
            <div class="d-flex">
                <button class="btn btn-primary rounded-lg shadow px-4" data-toggle="modal" data-target="#createCarpetModal">
                    <i class="feather icon-plus mr-1"></i> ثبت پارچه جدید
                </button>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        <!-- SEARCH BAR -->
        <div class="glass-card p-4 mb-4">
            <div class="row">
                <div class="col-md-4">
                    <form action="/dashboard/contract-carpet/search" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-left" placeholder="جستجوی نمبر پارچه، نقشه یا زمینه..." required>
                            <div class="input-group-append">
                                <button class="btn btn-dark" type="submit"><i class="feather icon-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="/dashboard/search-contract-carpet-by-agent" method="POST">
                        @csrf
                        <select name="agent_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">جستجو بر اساس نماینده...</option>
                            @foreach($agents as $ag)
                                <option value="{{$ag->agent_id}}">{{$ag->user->name}} ({{$ag->account_no}})</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-4 text-left">
                    <a href="/dashboard/contract-carpet-show-all" class="btn btn-outline-secondary">نمایش همه</a>
                    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="feather icon-printer"></i></button>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE -->
        <div class="glass-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-xs table-modern mb-0">
                    <thead>
                        <tr>
                            <th># پارچه</th>
                            <th>نماینده / کاریگر</th>
                            <th>فرمایش</th>
                            <th>نوعیت / کوالتی</th>
                            <th>نقشه / زمینه</th>
                            <th>ابعاد (H x W)</th>
                            <th>مساحت</th>
                            <th>قیمت فی متر</th>
                            <th>ارزش کل (Forensic USD)</th>
                            <th>تاریخ تولید</th>
                            <th>تصویر</th>
                            <th class="hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carpets as $carpet)
                        <tr class="ur{{ $carpet->carpet_id }}">
                            <td class="font-weight-bold">PN-{{ $carpet->parcha_number }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $carpet->agent->user->name ?? $carpet->name }}</div>
                                <div class="small text-muted">{{ $carpet->employee_name }}</div>
                            </td>
                            <td>{{ $carpet->order_number ?? ($carpet->order->order_number ?? 'N/A') }}</td>
                            <td>
                                <div class="small">{{ $carpet->carpet_type ?? ($carpet->type->carpet_type ?? '') }}</div>
                                <div class="small text-muted">{{ $carpet->quality ?? ($carpet->quality->quality ?? '') }}</div>
                            </td>
                            <td>
                                <div class="small">نقشه: {{ $carpet->map_number }}</div>
                                <div class="small text-muted">زمینه: {{ $carpet->field }} / {{ $carpet->margin }}</div>
                            </td>
                            <td dir="ltr">{{ $carpet->height }} x {{ $carpet->width }}</td>
                            <td dir="ltr">{{ $carpet->area }} m²</td>
                            <td dir="ltr">
                                <span class="text-primary font-weight-bold">${{ number_format($carpet->price, 2) }}</span>
                            </td>
                            <td dir="ltr">
                                <div class="font-weight-bold text-dark">${{ number_format($carpet->total_price, 2) }}</div>
                                <div class="small text-info">
                                    {{ number_format(($carpet->original_price ? ($carpet->original_price * $carpet->area) : $carpet->total_price_af), 2) }} 
                                    {{ $carpet->currency_code ?? 'AFN' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">شروع: {{ $carpet->date }}</div>
                                <div class="small">ختم: {{ $carpet->end_date }}</div>
                            </td>
                            <td>
                                @if($carpet->carpet_image)
                                    <img src="/{{$carpet->carpet_image}}" class="rounded shadow-sm" style="height: 35px; width: 35px; object-fit: cover; cursor: pointer;" onclick="showFullImage('/{{$carpet->carpet_image}}')">
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td class="hideOnPrint">
                                <div class="btn-group">
                                    <a href="/dashboard/contract-carpet/{{ $carpet->carpet_id }}/edit" class="btn btn-sm btn-light-primary" title="ویرایش (Edit)">
                                        <i class="feather icon-edit"></i>
                                    </a>
                                    <button onclick="openPassModal({{ $carpet->carpet_id }}, '{{ $carpet->parcha_number }}', '{{ $carpet->agent->user->name ?? '' }}')" 
                                            class="btn btn-sm btn-light-success" title="پاس کردن / تکمیل تولید (Pass/Complete)">
                                        <i class="feather icon-check-circle"></i>
                                    </button>
                                    <a href="/dashboard/contract-carpet/{{ $carpet->carpet_id }}" class="btn btn-sm btn-light-info" title="جزییات بیشتر (Details)">
                                        <i class="feather icon-list"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-light">
                {{ $carpets->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CREATE / EDIT CARPET -->
<div class="modal fade" id="createCarpetModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ $editCarpet ? '/dashboard/contract-carpet/'.$editCarpet->carpet_id : '/dashboard/contract-carpet' }}" method="post" enctype="multipart/form-data">
                @csrf
                @if($editCarpet) @method('PATCH') @endif
                
                <div class="modal-header {{ $editCarpet ? 'bg-info' : 'bg-primary' }} text-white p-4" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="feather {{ $editCarpet ? 'icon-edit' : 'icon-file-plus' }} mr-2"></i> 
                        {{ $editCarpet ? 'ویرایش پارچه قراردادی' : 'ثبت پارچه جدید قراردادی' }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" onclick="{{ $editCarpet ? "window.location='/dashboard/contract-carpet'" : "" }}">&times;</button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- HIDDEN PILLAR DEFAULTS -->
                    <input type="hidden" name="parcha_number" value="{{ $editCarpet ? $editCarpet->parcha_number : $AccountNo }}">
                    <input type="hidden" name="status" value="{{ $editCarpet ? $editCarpet->status : 0 }}">

                    <div class="row">
                        <!-- COLUMN 1: PRODUCTION DETAILS -->
                        <div class="col-md-4">
                            <div class="glass-card p-3 mb-3">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-user mr-1"></i> جزئیات تولید</h6>
                                <div class="form-group">
                                    <label class="form-label-premium">نمبر پارچه</label>
                                    <input type="text" class="form-control bg-white" value="{{ $editCarpet ? $editCarpet->parcha_number : $AccountNo }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="form-label-premium">نماینده</label>
                                    <select name="agent_id" class="form-control select2" required>
                                        <option value="">انتخاب نماینده...</option>
                                        @foreach($agents as $ag)
                                            <option value="{{$ag->agent_id}}" {{ ($editCarpet && $editCarpet->agent_id == $ag->agent_id) ? 'selected' : '' }}>{{$ag->user->name}} ({{$ag->account_no}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label-premium">نام کاریگر</label>
                                    <input type="text" name="employee_name" class="form-control premium-input" placeholder="اسم کاریگر" value="{{ $editCarpet ? $editCarpet->employee_name : '' }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label-premium">شماره فرمایش</label>
                                    <select name="order_id" class="form-control select2">
                                        @foreach($orders as $ord)
                                            <option value="{{$ord->id}}" {{ ($editCarpet && $editCarpet->order_id == $ord->id) ? 'selected' : '' }}>{{$ord->order_number}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMN 2: TECHNICAL DETAILS -->
                        <div class="col-md-4">
                            <div class="glass-card p-3 mb-3">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-settings mr-1"></i> مشخصات تخنیکی</h6>
                                <div class="row">
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">نوعیت</label>
                                        <select name="type_id" class="form-control">
                                            @foreach($types as $type)
                                                <option value="{{$type->carpet_type_id}}" {{ ($editCarpet && $editCarpet->type_id == $type->carpet_type_id) ? 'selected' : '' }}>{{$type->carpet_type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">کوالتی</label>
                                        <select name="quality_id" class="form-control select2">
                                            @foreach($qualities as $q)
                                                <option value="{{$q->id}}" {{ ($editCarpet && $editCarpet->quality_id == $q->id) ? 'selected' : '' }}>{{$q->quality}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 form-group">
                                        <label class="form-label-premium">نمبر نقشه</label>
                                        <input type="text" name="map_number" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->map_number : '' }}">
                                    </div>
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">زمینه</label>
                                        <input type="text" name="field" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->field : '' }}">
                                    </div>
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">حاشیه</label>
                                        <input type="text" name="margin" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->margin : '' }}">
                                    </div>
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">طول (m)</label>
                                        <input type="number" step="0.01" name="height" id="calc_height" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->height : '' }}">
                                    </div>
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">عرض (m)</label>
                                        <input type="number" step="0.01" name="width" id="calc_width" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->width : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMN 3: FINANCE & PILLARS -->
                        <div class="col-md-4">
                            <div class="glass-card p-3 mb-3 shadow" style="border-right: 4px solid var(--qbcc-primary);">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-shield mr-1"></i> ستون‌های مالی و انبار</h6>
                                
                                <div class="form-group">
                                    <label class="form-label-premium">ارز انتخاب شده (Currency)</label>
                                    <select name="currency_id" id="modal_currency_id" class="form-control border-primary">
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ ($editCarpet && $editCarpet->currency_id == $curr->id) ? 'selected' : '' }}>{{ $curr->code }} ({{ $curr->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label-premium">قیمت فی متر (<span id="ppm_currency_label">USD</span>)</label>
                                    <input type="number" step="0.01" name="price_input" id="calc_ppm" class="form-control premium-input border-primary" value="{{ $editCarpet ? ($editCarpet->original_price ?? $editCarpet->price) : '' }}">
                                    <input type="hidden" name="price" id="final_ppm_usd" value="{{ $editCarpet ? $editCarpet->price : '' }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label-premium">گودام هدف (Warehouse)</label>
                                    <select name="warehouse_id" class="form-control">
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ ($editCarpet && $editCarpet->warehouse_id == $w->id) ? 'selected' : '' }}>{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label-premium">حساب انبار (GL)</label>
                                    <select name="override_inventory_account_id" class="form-control">
                                        @foreach($inventoryAccounts as $acc)
                                            <option value="{{$acc->id}}" {{ ($editCarpet ? ($editCarpet->override_inventory_account_id == $acc->id) : ($acc->account_code == '1200')) ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="forensic-snapshot">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small font-weight-bold">مساحت:</span>
                                        <span id="label_area" class="font-weight-bold">{{ $editCarpet ? $editCarpet->area : '0.00' }} m²</span>
                                        <input type="hidden" name="area" id="input_area" value="{{ $editCarpet ? $editCarpet->area : '' }}">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small font-weight-bold">ارزش (Native):</span>
                                        <span id="label_total_native" class="font-weight-bold text-primary">{{ $editCarpet ? number_format($editCarpet->total_price_af, 2) : '0.00' }}</span>
                                        <input type="hidden" name="total_price_af" id="input_total_native" value="{{ $editCarpet ? $editCarpet->total_price_af : '' }}">
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small font-weight-bold text-dark">ارزش (Forensic USD):</span>
                                        <span id="label_total_usd" class="font-weight-bold text-dark">${{ $editCarpet ? number_format($editCarpet->total_price, 2) : '0.00' }}</span>
                                        <input type="hidden" name="total_price" id="input_total_usd" value="{{ $editCarpet ? $editCarpet->total_price : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3 form-group">
                            <label class="form-label-premium">تاریخ شروع کار</label>
                            <input type="date" name="date" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->date : '' }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="form-label-premium">تاریخ ختم کار</label>
                            <input type="date" name="end_date" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->end_date : '' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label-premium">تصویر قالین</label>
                            <input type="file" name="carpet_image" class="form-control premium-input">
                            @if($editCarpet && $editCarpet->carpet_image)
                                <div class="mt-2 small text-muted">تصویر فعلی: {{ basename($editCarpet->carpet_image) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white p-4" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-light rounded-lg px-4" data-dismiss="modal" onclick="{{ $editCarpet ? "window.location='/dashboard/contract-carpet'" : "" }}">انصراف</button>
                    <button type="submit" class="btn {{ $editCarpet ? 'btn-info' : 'btn-primary' }} rounded-lg px-5 shadow-lg font-weight-bold">
                        {{ $editCarpet ? 'بروزرسانی تغییرات' : 'ذخیره و ایجاد در سیستم مالی' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: PASS CARPET -->
<div class="modal fade" id="passModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-xl shadow-lg border-0">
            <div class="modal-body text-center p-5">
                <i class="feather icon-check-circle text-success mb-4" style="font-size: 80px;"></i>
                <h4 class="font-weight-bold mb-3">تکمیل تولید و پاس کردن</h4>
                <p>آیا از پاس کردن پارچه <strong id="pass_no"></strong> مربوط به <strong id="pass_agent"></strong> اطمینان دارید؟</p>
                
                <div class="text-right mt-4 p-3 glass-card bg-light">
                    <div class="form-group">
                        <label class="form-label-premium">گودام مقصد (Destination Warehouse)</label>
                        <select id="pass_warehouse_id" class="form-control">
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label-premium">حساب انبار مقصد (GL Account)</label>
                        <select id="pass_inventory_account_id" class="form-control">
                            @foreach($inventoryAccounts as $acc)
                                <option value="{{$acc->id}}" {{ $acc->account_code == '1201' ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="text-muted small mt-3">این تراکنش باعث انتقال موجودی از انبار تولید به انبار مرکزی و حساب تعیین شده می‌گردد.</p>
                <input type="hidden" id="pass_id">
                <div class="mt-4">
                    <button class="btn btn-light rounded-lg px-4 mr-2" data-dismiss="modal">انصراف</button>
                    <button class="btn btn-success rounded-lg px-4" onclick="confirmPass()">بله، تایید شود</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        // Initialize Select2 for the main page
        $('.select2').select2({ width: '100%' });

        // Fix Select2 in Bootstrap Modals
        $('#createCarpetModal').on('shown.bs.modal', function () {
            $('.select2', this).select2({
                width: '100%',
                dropdownParent: $('#createCarpetModal')
            });
        });

        // AUTO-OPEN MODAL IF EDITING
        @if($editCarpet)
            $('#createCarpetModal').modal('show');
            runCalculations();
        @endif
    });

    // CALCULATION LOGIC
    function runCalculations() {
        let h = parseFloat($('#calc_height').val()) || 0;
        let w = parseFloat($('#calc_width').val()) || 0;
        let ppm = parseFloat($('#calc_ppm').val()) || 0;
        let rate = parseFloat($('#modal_currency_id option:selected').data('rate')) || 1;
        let currencyCode = $('#modal_currency_id option:selected').data('code');

        // Update Label
        $('#ppm_currency_label').text(currencyCode);

        // Area
        let area = h * w;
        $('#label_area').text(area.toFixed(2) + ' m²');
        $('#input_area').val(area.toFixed(2));

        // Total in selected currency (Native)
        let totalNative = area * ppm;
        $('#label_total_native').text(totalNative.toLocaleString());
        $('#input_total_native').val(totalNative.toFixed(2));

        // Total in USD (Forensic Base)
        // If ppm is in Native, USD = (area * ppm) * rate
        let totalUsd = totalNative * rate;
        $('#label_total_usd').text('$' + totalUsd.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#input_total_usd').val(totalUsd.toFixed(2));
        
        // Final PPM in USD for the controller
        let ppmUsd = ppm * rate;
        $('#final_ppm_usd').val(ppmUsd.toFixed(4));
    }

    $('#calc_height, #calc_width, #calc_ppm, #modal_currency_id').on('input change', runCalculations);

    function showFullImage(src) {
        window.open(src, '_blank');
    }

    function openPassModal(id, no, agent) {
        $('#pass_id').val(id);
        $('#pass_no').text(no);
        $('#pass_agent').text(agent);
        $('#passModal').modal('show');
    }

    function confirmPass() {
        let id = $('#pass_id').val();
        let warehouseId = $('#pass_warehouse_id').val();
        let accountId = $('#pass_inventory_account_id').val();

        $.ajax({
            url: '/dashboard/pass-parcha',
            type: 'POST',
            data: { 
                _token: '{{ csrf_token() }}', 
                carpet_id: id,
                warehouse_id: warehouseId,
                override_debit_account_id: accountId
            },
            success: function(res) {
                if(res.status == 'success') location.reload();
                else swal("Error", res.message, "error");
            }
        });
    }
</script>
@endsection