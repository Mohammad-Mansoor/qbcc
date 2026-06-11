@extends('dsh.master')
@section('title', 'Weight Carpet Registry - QBCC Forensic ERP')

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

    .table-xs td, .table-xs th { padding: 12px 15px; font-size: 13px; vertical-align: middle; }
    .table-modern thead th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; border: none; letter-spacing: 0.5px; }
    
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .badge-wip { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-passed { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

    .forensic-snapshot {
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        padding: 15px;
        border-radius: 12px;
        margin-top: 15px;
    }

    .form-label-premium { font-weight: 700; color: #334155; font-size: 12px; margin-bottom: 8px; display: block; }
    .premium-input { border-radius: 10px; border: 1px solid #cbd5e1; padding: 12px; transition: all 0.2s; background: #fff; width: 100%; }
    .premium-input:focus { border-color: var(--qbcc-accent); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }
    
    .modal-xl { max-width: 1200px; }
    .select2-container--default .select2-selection--single { border-radius: 10px; border: 1px solid #cbd5e1; height: 45px; padding-top: 8px; }
</style>

<div class="row">
    <div class="col-sm-12">
        <!-- HEADER -->
        <div class="glass-card p-4 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="font-weight-bold mb-1">لیست قالین‌های وزنی (Weight Carpet Registry)</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-anchor mr-1"></i> مدیریت و ارزش‌گذاری قالین‌های وزنی با متدولوژی Forensic FX</p>
            </div>
            <div class="d-flex">
                <button class="btn btn-primary rounded-lg shadow px-4 py-2" data-toggle="modal" data-target="#weightCarpetModal">
                    <i class="feather icon-plus mr-1"></i> ثبت پارچه وزنی جدید
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
                    <form action="/dashboard/list-weight/search" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-left" placeholder="جستجوی نمبر پارچه، نقشه، کاریگر..." required value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-dark" type="submit"><i class="feather icon-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="/dashboard/search-weight-carpet-by-agent" method="POST">
                        @csrf
                        <select name="agent_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">جستجو بر اساس نماینده...</option>
                            @foreach($agents as $ag)
                                <option value="{{$ag->agent_id}}" {{ (isset($agent_id) && $agent_id == $ag->agent_id) ? 'selected' : '' }}>{{$ag->user->name}} ({{$ag->account_no}})</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-4 text-left">
                    <a href="/dashboard/list-weight-show-all" class="btn btn-outline-secondary">نمایش همه</a>
                    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="feather icon-printer"></i></button>
                </div>
            </div>
        </div>

        <!-- SUMMARY STATS (Only when agent is selected) -->
        @if(isset($agent_id))
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="glass-card p-3 text-center">
                    <div class="small text-muted">تار پخته</div>
                    <div class="h5 font-weight-bold mb-0">{{ number_format($tar_pakhta, 2) }} kg</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="glass-card p-3 text-center">
                    <div class="small text-muted">تار پشم</div>
                    <div class="h5 font-weight-bold mb-0">{{ number_format($tar_pashm, 2) }} kg</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="glass-card p-3 text-center">
                    <div class="small text-muted">تار ابریشم</div>
                    <div class="h5 font-weight-bold mb-0">{{ number_format($tar_abrishm, 2) }} kg</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-3 text-center border-left border-info" style="border-left-width: 4px !important;">
                    <div class="small text-muted">جمله پول (Native)</div>
                    <div class="h5 font-weight-bold mb-0 text-info">{{ number_format($afg_money, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-3 text-center border-left border-primary" style="border-left-width: 4px !important;">
                    <div class="small text-muted">جمله پول (USD)</div>
                    <div class="h5 font-weight-bold mb-0 text-primary">${{ number_format($usd_money, 2) }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- MAIN TABLE -->
        <div class="glass-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-xs table-modern mb-0">
                    <thead>
                        <tr>
                            <th>PN #</th>
                            <th>نماینده / کاریگر</th>
                            <th>فرمایش / نوعیت</th>
                            <th>نقشه / کوالتی</th>
                            <th>زمینه / حاشیه</th>
                            <th>ابعاد (H x W)</th>
                            <th>مساحت</th>
                            <th>قیمت فی متر (USD)</th>
                            <th>ارزش کل (Forensic)</th>
                            <th>وضعیت</th>
                            <th class="hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carpets as $carpet)
                        <tr>
                            <td class="font-weight-bold text-dark">PN-{{ $carpet->parcha_number }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $carpet->name ?? ($carpet->agent->user->name ?? 'N/A') }}</div>
                                <div class="small text-muted">{{ $carpet->employee_name }}</div>
                            </td>
                            <td>
                                <div class="small">{{ $carpet->order_number ?? ($carpet->order->order_number ?? 'N/A') }}</div>
                                <div class="small text-muted">{{ $carpet->carpet_type ?? ($carpet->type->carpet_type ?? '') }}</div>
                            </td>
                            <td>
                                <div class="small">نقشه: {{ $carpet->map_number }}</div>
                                <div class="small text-muted">کوالتی: {{ $carpet->quality ?? ($carpet->quality->quality ?? '') }}</div>
                            </td>
                            <td>
                                <div class="small">زمینه: {{ $carpet->field }}</div>
                                <div class="small text-muted">حاشیه: {{ $carpet->margin }}</div>
                            </td>
                            <td dir="ltr">{{ $carpet->height }} x {{ $carpet->width }}</td>
                            <td dir="ltr">{{ number_format($carpet->area, 2) }} m²</td>
                            <td dir="ltr" class="text-primary font-weight-bold">
                                ${{ number_format($carpet->price, 2) }}
                            </td>
                            <td dir="ltr">
                                <div class="font-weight-bold text-dark">${{ number_format($carpet->total_price, 2) }}</div>
                                <div class="small text-info">
                                    {{ number_format($carpet->total_price_af, 2) }} {{ $carpet->currency_code ?? 'AFN' }}
                                </div>
                            </td>
                            <td>
                                @if($carpet->status == 0)
                                    <span class="status-badge badge-wip">تولید (WIP)</span>
                                @else
                                    <span class="status-badge badge-passed">پاس شده</span>
                                @endif
                            </td>
                            <td class="hideOnPrint">
                                <div class="btn-group">
                                    <a href="/dashboard/edit-weight/{{ $carpet->carpet_id }}" class="btn btn-sm btn-light-primary" title="ویرایش">
                                        <i class="feather icon-edit"></i>
                                    </a>
                                    @if($carpet->status == 0)
                                    <button onclick="openPassModal({{ $carpet->carpet_id }}, '{{ $carpet->parcha_number }}', '{{ $carpet->name ?? ($carpet->agent->user->name ?? '') }}')" 
                                            class="btn btn-sm btn-light-success" title="پاس کردن">
                                        <i class="feather icon-check-circle"></i>
                                    </button>
                                    @endif
                                    <a href="/dashboard/show-weight/{{ $carpet->carpet_id }}" class="btn btn-sm btn-light-info" title="جزییات">
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

<!-- MODAL: CREATE / EDIT WEIGHT CARPET -->
<div class="modal fade" id="weightCarpetModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ $editCarpet ? '/dashboard/upd-weight/'.$editCarpet->carpet_id : '/dashboard/post-weight' }}" method="post" enctype="multipart/form-data">
                @csrf
                
                <div class="modal-header {{ $editCarpet ? 'bg-info' : 'bg-primary' }} text-white p-4" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="feather {{ $editCarpet ? 'icon-edit' : 'icon-anchor' }} mr-2"></i> 
                        {{ $editCarpet ? 'ویرایش پارچه وزنی' : 'ثبت پارچه وزنی جدید' }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" onclick="{{ $editCarpet ? "window.location='/dashboard/list-weight'" : "" }}">&times;</button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- HIDDEN DEFAULTS -->
                    <input type="hidden" name="parcha_number" value="{{ $editCarpet ? $editCarpet->parcha_number : $AccountNo }}">
                    <input type="hidden" name="status" value="0">

                    <div class="row">
                        <!-- COLUMN 1: PRODUCTION -->
                        <div class="col-md-4">
                            <div class="glass-card p-3 mb-3">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-user mr-1"></i> جزئیات تولید</h6>
                                <div class="form-group">
                                    <label class="form-label-premium">نمبر پارچه</label>
                                    <input type="text" class="form-control bg-white" value="{{ $editCarpet ? $editCarpet->parcha_number : $AccountNo }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="form-label-premium">نماینده</label>
                                    <select name="agent_id" id="modal_agent_id" class="form-control select2" required>
                                        <option value="">انتخاب نماینده...</option>
                                        @foreach($agents as $ag)
                                            <option value="{{$ag->agent_id}}" {{ ($editCarpet && $editCarpet->agent_id == $ag->agent_id) ? 'selected' : '' }}>{{$ag->user->name}} ({{$ag->account_no}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label-premium">نام کاریگر</label>
                                    <input type="text" name="employee_name" class="form-control premium-input" placeholder="نام کاریگر" value="{{ $editCarpet ? $editCarpet->employee_name : '' }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label-premium">شماره فرمایش</label>
                                    <select name="order_id" class="form-control select2">
                                        <option value="">انتخاب...</option>
                                        @foreach($orders as $ord)
                                            <option value="{{$ord->co_id}}" {{ ($editCarpet && $editCarpet->order_id == $ord->co_id) ? 'selected' : '' }}>{{$ord->order_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMN 2: TECHNICAL -->
                        <div class="col-md-4">
                            <div class="glass-card p-3 mb-3">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-settings mr-1"></i> مشخصات فنی</h6>
                                <div class="row">
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">نوعیت</label>
                                        <select name="type_id" id="modal_type_id" class="form-control">
                                            <option value="">انتخاب...</option>
                                            @foreach($types as $type)
                                                <option value="{{$type->carpet_type_id}}" {{ ($editCarpet && $editCarpet->type_id == $type->carpet_type_id) ? 'selected' : '' }}>{{$type->carpet_type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 form-group">
                                        <label class="form-label-premium">کوالتی</label>
                                        <select name="quality_id" id="modal_quality_id" class="form-control select2">
                                            <option value="">انتخاب...</option>
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

                        <!-- COLUMN 3: FORENSIC FINANCE -->
                        <div class="col-md-4">
                            <div class="glass-card p-3 mb-3 shadow" style="border-right: 4px solid var(--qbcc-accent);">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-dollar-sign mr-1"></i> ارزش‌گذاری مالی (Forensic)</h6>
                                
                                <div class="form-group">
                                    <label class="form-label-premium">ارز انتخاب شده (Native)</label>
                                    <select name="currency_id" id="modal_currency_id" class="form-control border-primary">
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ ($editCarpet && $editCarpet->currency_id == $curr->id) ? 'selected' : ($curr->code == 'USD' ? 'selected' : '') }}>{{ $curr->code }} ({{ $curr->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label-premium">قیمت فی متر (<span id="ppm_currency_label">USD</span>)</label>
                                    <input type="number" step="0.01" name="price_input" id="calc_ppm" class="form-control premium-input border-primary" value="{{ $editCarpet ? ($editCarpet->original_price ?? $editCarpet->price) : '' }}">
                                    <input type="hidden" name="price" id="final_ppm_usd" value="{{ $editCarpet ? $editCarpet->price : '' }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label-premium">گودام / حساب انبار</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <select name="warehouse_id" class="form-control small">
                                                @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}" {{ (old('warehouse_id', $defaultWarehouseId) == $w->id || ($editCarpet && $editCarpet->warehouse_id == $w->id)) ? 'selected' : '' }}>{{ $w->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <select name="override_inventory_account_id" class="form-control small">
                                                @foreach($inventoryAccounts as $acc)
                                                    <option value="{{$acc->id}}" {{ (old('override_inventory_account_id', 15) == $acc->id || ($editCarpet && $editCarpet->override_inventory_account_id == $acc->id)) ? 'selected' : '' }}>{{$acc->account_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="forensic-snapshot">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small font-weight-bold">مساحت (Area):</span>
                                        <span id="label_area" class="font-weight-bold text-dark">{{ $editCarpet ? $editCarpet->area : '0.00' }} m²</span>
                                        <input type="hidden" name="area" id="input_area" value="{{ $editCarpet ? $editCarpet->area : '' }}">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small font-weight-bold text-muted">ارزش کل (Native):</span>
                                        <span id="label_total_native" class="font-weight-bold text-info">{{ $editCarpet ? number_format($editCarpet->total_price_af, 2) : '0.00' }}</span>
                                        <input type="hidden" name="total_price_af" id="input_total_native" value="{{ $editCarpet ? $editCarpet->total_price_af : '' }}">
                                    </div>
                                    <div class="d-flex justify-content-between pt-2 border-top border-secondary">
                                        <span class="small font-weight-bold text-dark">ارزش کل (Forensic USD):</span>
                                        <span id="label_total_usd" class="h6 font-weight-bold text-dark mb-0">${{ $editCarpet ? number_format($editCarpet->total_price, 2) : '0.00' }}</span>
                                        <input type="hidden" name="total_price" id="input_total_usd" value="{{ $editCarpet ? $editCarpet->total_price : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3 form-group">
                            <label class="form-label-premium">تاریخ شروع کار</label>
                            <input type="date" name="date" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->date : date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="form-label-premium">تاریخ ختم کار</label>
                            <input type="date" name="end_date" class="form-control premium-input" value="{{ $editCarpet ? $editCarpet->end_date : '' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label-premium">تصویر پارچه</label>
                            <input type="file" name="carpet_image" class="form-control premium-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white p-4" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-light rounded-lg px-4" data-dismiss="modal" onclick="{{ $editCarpet ? "window.location='/dashboard/list-weight'" : "" }}">انصراف</button>
                    <button type="submit" class="btn {{ $editCarpet ? 'btn-info' : 'btn-primary' }} rounded-lg px-5 shadow-lg font-weight-bold">
                        {{ $editCarpet ? 'بروزرسانی اطلاعات' : 'ثبت در سیستم (WIP Entry)' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: PASS WEIGHT CARPET -->
<div class="modal fade" id="passModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-xl shadow-lg border-0">
            <div class="modal-body text-center p-5">
                <i class="feather icon-check-circle text-success mb-4" style="font-size: 80px;"></i>
                <h4 class="font-weight-bold mb-3">تکمیل تولید و انتقال به انبار</h4>
                <p>آیا از پاس کردن پارچه <strong id="pass_no"></strong> مربوط به <strong id="pass_agent"></strong> اطمینان دارید؟</p>
                
                <div class="text-right mt-4 p-3 glass-card bg-light">
                    <div class="form-group">
                        <label class="form-label-premium">گودام مقصد (Final Warehouse)</label>
                        <select id="pass_warehouse_id" class="form-control">
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label-premium">حساب دارایی انبار (GL)</label>
                        <select id="pass_inventory_account_id" class="form-control">
                            @foreach($inventoryAccounts as $acc)
                                <option value="{{$acc->id}}" {{ $acc->account_code == '1201' ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="hidden" id="pass_id">
                <div class="mt-4">
                    <button class="btn btn-light rounded-lg px-4 mr-2" data-dismiss="modal">انصراف</button>
                    <button class="btn btn-success rounded-lg px-4" onclick="confirmPass()">تایید و پاس کردن</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        $('.select2').select2({ width: '100%' });

        $('#weightCarpetModal').on('shown.bs.modal', function () {
            $('.select2', this).select2({
                width: '100%',
                dropdownParent: $('#weightCarpetModal')
            });
        });

        // Type -> Quality linkage
        $("#modal_type_id").change(function () {
            $.ajax({
                url: "{{ route('dashboard.qualities.get_by_type') }}?type_id=" + $(this).val(),
                method: 'GET',
                success: function (data) {
                    $('#modal_quality_id').html(data.html).trigger('change');
                }
            });
        });

        @if($editCarpet)
            $('#weightCarpetModal').modal('show');
            runCalculations();
        @endif
    });

    function runCalculations() {
        let h = parseFloat($('#calc_height').val()) || 0;
        let w = parseFloat($('#calc_width').val()) || 0;
        let ppm = parseFloat($('#calc_ppm').val()) || 0;
        let rate = parseFloat($('#modal_currency_id option:selected').data('rate')) || 1;
        let currencyCode = $('#modal_currency_id option:selected').data('code');

        $('#ppm_currency_label').text(currencyCode);

        // Area
        let area = h * w;
        $('#label_area').text(area.toFixed(2) + ' m²');
        $('#input_area').val(area.toFixed(2));

        // Total in Native Currency (User requested total_price_af to store this)
        let totalNative = area * ppm;
        $('#label_total_native').text(totalNative.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#input_total_native').val(totalNative.toFixed(2));

        // Total in USD (Normalized)
        let totalUsd = totalNative * rate;
        $('#label_total_usd').text('$' + totalUsd.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#input_total_usd').val(totalUsd.toFixed(2));
        
        // Final Price in USD for the base price column
        let ppmUsd = ppm * rate;
        $('#final_ppm_usd').val(ppmUsd.toFixed(4));
    }

    $('#calc_height, #calc_width, #calc_ppm, #modal_currency_id').on('input change', runCalculations);

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
                if(res.status == 'success') {
                    Swal.fire('موفقانه!', 'پارچه به انبار منتقل شد.', 'success').then(() => location.reload());
                } else {
                    Swal.fire('خطا', res.message, 'error');
                }
            }
        });
    }
</script>
@endsection