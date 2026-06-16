@extends('dsh.master')
@section('title', 'Purchased Carpet Registry - QBCC Forensic ERP')

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

    /* Prevent dropdown clipping in responsive tables */
    .table-responsive, .glass-card {
        overflow: visible !important;
        padding-bottom: 80px; /* Even more space */
    }
    
    .table-modern tbody tr {
        position: relative;
        z-index: 1;
    }
    
    .table-modern tbody tr:hover {
        z-index: 100; /* Bring hovered row to front */
    }

    .table-modern td {
        overflow: visible !important;
    }
    
    .dropdown-menu {
        position: absolute !important;
        will-change: transform;
        z-index: 999999 !important; /* Extreme z-index */
    }

    .table-xs td, .table-xs th { padding: 12px 15px; font-size: 13px; vertical-align: middle; }
    .table-modern thead th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; border: none; letter-spacing: 0.5px; }
    
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .badge-purchased { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-repair { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

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
                <h3 class="font-weight-bold mb-1">لیست قالین‌های خریداری شده (Purchased Carpet Registry)</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-shopping-cart mr-1"></i> مدیریت و ارزش‌گذاری قالین‌های خریداری شده با متدولوژی Forensic FX</p>
            </div>
            <div class="d-flex">
                <button class="btn btn-primary rounded-lg shadow px-4 py-2" data-toggle="modal" data-target="#buyCarpetModal">
                    <i class="feather icon-plus mr-1"></i> ثبت خرید قالین جدید
                </button>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        <!-- ADVANCED SEARCH & FILTERS -->
        <div class="glass-card p-4 mb-4">
            <form method="GET" action="/dashboard/list-buy-carpet">
                <div class="row align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label-premium">از تاریخ:</label>
                        <input type="date" name="from_date" class="form-control premium-input bg-light" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-premium">تا تاریخ:</label>
                        <input type="date" name="to_date" class="form-control premium-input bg-light" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-premium">از نمبر پارچه:</label>
                        <input type="text" name="from_id" class="form-control premium-input bg-light" placeholder="مثال: QB1000" value="{{ request('from_id') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-premium">تا نمبر پارچه:</label>
                        <input type="text" name="to_id" class="form-control premium-input bg-light" placeholder="مثال: QB1100" value="{{ request('to_id') }}">
                    </div>
                </div>
                
                <div class="row align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label-premium">فروشنده (Vendor):</label>
                        <select name="agent_id" class="form-control premium-input bg-light select2">
                            <option value="">همه فروشندگان</option>
                            @foreach($agents as $ag)
                                <option value="{{$ag->agent_id}}" {{ request('agent_id') == $ag->agent_id ? 'selected' : '' }}>{{$ag->user->name}} ({{$ag->account_no}})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-premium">نوعیت قالین:</label>
                        <select name="type_id" class="form-control premium-input bg-light">
                            <option value="">همه نوعیت‌ها</option>
                            @foreach($types as $type)
                                <option value="{{ $type->carpet_type_id }}" {{ request('type_id') == $type->carpet_type_id ? 'selected' : '' }}>{{ $type->carpet_type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-premium">کوالیتی قالین:</label>
                        <select name="quality_id" class="form-control premium-input bg-light">
                            <option value="">همه کوالیتی‌ها</option>
                            @foreach($qualities as $quality)
                                <option value="{{ $quality->id }}" {{ request('quality_id') == $quality->id ? 'selected' : '' }}>{{ $quality->quality }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-premium">شماره نقشه (Map No):</label>
                        <input type="text" name="map_number" class="form-control premium-input bg-light" placeholder="جستجوی نقشه..." value="{{ request('map_number') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <div class="d-flex" style="gap: 10px; width: auto;">
                            <button class="btn btn-outline-secondary rounded-lg" type="button" onclick="window.print()"><i class="feather icon-printer"></i></button>
                            <button type="submit" class="btn btn-primary rounded-lg shadow px-4"><i class="feather icon-search"></i> جستجو / فیلتر</button>
                            @php
                                $hasFilters = request()->filled('from_date') || request()->filled('to_date') || request()->filled('from_id') || request()->filled('to_id') || request()->filled('agent_id') || request()->filled('type_id') || request()->filled('quality_id') || request()->filled('map_number');
                            @endphp
                            @if($hasFilters)
                                <a href="/dashboard/list-buy-carpet" class="btn btn-outline-danger rounded-lg px-4"><i class="feather icon-x"></i> پاکسازی</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- MAIN TABLE -->
        <div class="glass-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-modern mb-0" id="list_buy_carpet">
                    <thead>
                        <tr>
                            <th>تصویر</th>
                            <th>نمبر قالین</th>
                            <th>شماره نقشه</th>
                            <th>فروشنده (Vendor)</th>
                            <th>نوعیت / کوالتی</th>
                            <th>ابعاد (m²)</th>
                            <th class="text-right">قیمت فی متر</th>
                            <th class="text-right">مجموع (Forensic USD)</th>
                            <th>وضعیت</th>
                            <th>تاریخ خرید</th>
                            <th class="text-center hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carpets as $carpet)
                            <tr>
                                <td>
                                    @if($carpet->carpet_image)
                                        <img src="/{{$carpet->carpet_image}}" class="rounded shadow-sm" style="height: 40px; width: 40px; object-fit: cover; cursor: pointer;" onclick="showImageModal('/{{$carpet->carpet_image}}', '{{$carpet->carpet_no}}')">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 40px; width: 40px;">
                                            <i class="feather icon-image text-muted" style="font-size: 12px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $carpet->carpet_no }}</td>
                                <td class="font-weight-bold text-secondary">{{ $carpet->map_number ?? 'N/A' }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $carpet->agent->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $carpet->agent->account_no ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="d-block text-dark">{{ $carpet->type->carpet_type ?? 'N/A' }}</span>
                                    <small class="text-muted">{{ $carpet->quality->quality ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="d-block">{{ $carpet->area }} m²</span>
                                    <small class="text-muted">{{ $carpet->height }}x{{ $carpet->width }}</small>
                                </td>
                                <td class="text-right">
                                    <div class="font-weight-bold text-dark">{{ number_format($carpet->original_price ?? $carpet->price, 2) }} {{ $carpet->currency_code ?? '$' }}</div>
                                    <small class="text-muted">Native Price</small>
                                </td>
                                <td class="text-right">
                                    <div class="text-primary font-weight-bold" data-toggle="tooltip" title="Exchange Rate: {{ number_format($carpet->exchange_rate, 4) }}">
                                        ${{ number_format($carpet->total_price, 2) }}
                                    </div>
                                    <small class="text-success font-weight-bold">{{ number_format($carpet->total_price_af, 0) }} AFN</small>
                                </td>
                                <td>
                                    <span class="status-badge {{ $carpet->status == 12 ? 'badge-repair' : 'badge-purchased' }}">
                                        {{ $carpet->status == 12 ? 'کچایی شده' : 'خریداری شده' }}
                                    </span>
                                </td>
                                <td class="small">{{ $carpet->date }}</td>
                                <td class="text-center hideOnPrint" style="min-width: 150px; position: relative;">
                                    <div class="btn-group align-items-center">
                                        <a href="/dashboard/edit-buy-carpet/{{$carpet->carpet_id}}" class="btn btn-sm btn-light-primary border-0 shadow-none p-2" title="Edit"><i class="feather icon-edit-2"></i></a>
                                        <a href="/dashboard/show-buy-carpet/{{$carpet->carpet_id}}" class="btn btn-sm btn-light-info border-0 shadow-none p-2 mx-1" title="Details"><i class="feather icon-eye"></i></a>
                                        
                                        <div class="dropdown" style="position: static;">
                                            <button class="btn btn-sm btn-light-secondary border-0 shadow-none p-2 no-caret" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                                                <i class="feather icon-more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu shadow-lg border-0" style="min-width: 200px; border-radius: 12px; z-index: 1000001; margin-top: 5px;">
                                                <h6 class="dropdown-header small text-muted font-weight-bold">عملیات انتقال (Transfer)</h6>
                                                @if($carpet->status != 12)
                                                    <a class="dropdown-item py-2 px-3 small" href="/dashboard/carpet-repaire/sending-to-kachaee/{{$carpet->carpet_id}}">
                                                        <i class="feather icon-tool mr-2 text-warning"></i> ارسال به کچایی (Repair)
                                                    </a>
                                                @endif
                                                <a class="dropdown-item py-2 px-3 small" href="/dashboard/washing-team/sending-to-washing/{{$carpet->carpet_id}}">
                                                    <i class="feather icon-droplet mr-2 text-info"></i> ارسال به شست (Wash)
                                                </a>
                                                <a class="dropdown-item py-2 px-3 small" href="/dashboard/carpet-wash/sent-to-finish/{{$carpet->carpet_id}}">
                                                    <i class="feather icon-check-circle mr-2 text-success"></i> ارسال به تیاری (Finish)
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item py-2 px-3 small text-primary font-weight-bold" onclick="sendToStock({{$carpet->carpet_id}}, {{$carpet->warehouse_id ?? 'null'}})">
                                                    <i class="feather icon-package mr-2"></i> تایید نهایی و گدام (Stock)
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!isset($all))
                <div class="p-3 border-top bg-light">
                    {{ $carpets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL: CREATE / EDIT -->
<div class="modal fade" id="buyCarpetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content glass-card border-0 overflow-hidden">
            <div class="modal-header border-bottom p-4 bg-light">
                <h5 class="font-weight-bold mb-0 text-primary">{{ $editCarpet ? 'ویرایش اطلاعات خرید' : 'ثبت خرید قالین جدید' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $editCarpet ? '/dashboard/upd-buy-carpet/'.$editCarpet->carpet_id : '/dashboard/post-buy-carpet' }}" method="post" enctype="multipart/form-data" id="buyCarpetForm">
                @csrf
                <input type="hidden" name="status" value="1">
                
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- SECTION: BASIC INFO -->
                        <div class="col-md-8 border-right">
                            <h6 class="font-weight-bold text-primary mb-4 border-bottom pb-2"><i class="feather icon-info mr-1"></i> مشخصات عمومی خرید</h6>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">نمبر پارچه (System ID)</label>
                                    <input type="text" value="{{ $editCarpet ? $editCarpet->carpet_no : $AccountNo }}" class="form-control premium-input bg-light" readonly>
                                    <input type="hidden" name="carpet_no" value="{{ $editCarpet ? $editCarpet->carpet_no : $AccountNo }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">اسم فروشنده (Vendor)</label>
                                    <select name="agent_id" id="modal_agent_id" class="form-control select2" required>
                                        <option value="">انتخاب فروشنده...</option>
                                        @foreach($agents as $ag)
                                            <option value="{{$ag->agent_id}}" {{ ($editCarpet && $editCarpet->agent_id == $ag->agent_id) || (old('agent_id') == $ag->agent_id) ? 'selected' : '' }}>{{$ag->user->name}} ({{$ag->account_no}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">شماره فرمایش</label>
                                    <select name="order_id" class="form-control select2">
                                        <option value="">انتخاب فرمایش...</option>
                                        @foreach($orders as $ord)
                                            <option value="{{$ord->co_id}}" {{ ($editCarpet && $editCarpet->order_id == $ord->co_id) || (old('order_id') == $ord->co_id) ? 'selected' : '' }}>{{$ord->order_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">بل خرید (Purchase Bill)</label>
                                    <select name="purchase_invoice_id" id="modal_purchase_invoice_id" class="form-control select2">
                                        <option value="">انتخاب بل خرید...</option>
                                        @foreach($purchaseInvoices as $invoice)
                                            <option value="{{$invoice->id}}" data-agent="{{ $invoice->agent_id }}" {{ ($editCarpet && $editCarpet->purchase_invoice_id == $invoice->id) || (old('purchase_invoice_id') == $invoice->id) ? 'selected' : '' }}>{{$invoice->invoice_number}} ({{$invoice->agent->user->name ?? ''}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">نوعیت</label>
                                    <select name="type_id" id="modal_type_id" class="form-control premium-input" required>
                                        <option value="">انتخاب...</option>
                                        @foreach($types as $type)
                                            <option value="{{$type->carpet_type_id}}" {{ ($editCarpet && $editCarpet->type_id == $type->carpet_type_id) ? 'selected' : '' }}>{{$type->carpet_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">کوالتی</label>
                                    <select name="quality_id" id="modal_quality_id" class="form-control premium-input" required>
                                        <option value="">انتخاب...</option>
                                        @if($editCarpet)
                                            @foreach($qualities as $q)
                                                <option value="{{$q->id}}" {{ ($editCarpet->quality_id == $q->id) ? 'selected' : '' }}>{{$q->quality}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">نمبر نقشه</label>
                                    <input type="text" name="map_number" value="{{ $editCarpet->map_number ?? '' }}" class="form-control premium-input" placeholder="Map No.">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">تاریخ خرید</label>
                                    <input type="date" name="date" value="{{ $editCarpet->date ?? date('Y-m-d') }}" class="form-control premium-input" required>
                                </div>
                            </div>

                            <h6 class="font-weight-bold text-primary mb-4 border-bottom pb-2 mt-4"><i class="feather icon-maximize mr-1"></i> ابعاد و اندازه‌گیری</h6>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">طول (Length - m)</label>
                                    <input type="number" step="0.01" name="height" id="modal_height" value="{{ $editCarpet->height ?? '' }}" class="form-control premium-input" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">عرض (Width - m)</label>
                                    <input type="number" step="0.01" name="width" id="modal_width" value="{{ $editCarpet->width ?? '' }}" class="form-control premium-input" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">مساحت (m²)</label>
                                    <input type="number" step="0.01" name="area" id="modal_area" value="{{ $editCarpet->area ?? '' }}" class="form-control premium-input bg-light" readonly>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">حاشیه / زمینه</label>
                                    <div class="input-group">
                                        <input type="text" name="margin" value="{{ $editCarpet->margin ?? '' }}" class="form-control premium-input" placeholder="H">
                                        <input type="text" name="field" value="{{ $editCarpet->field ?? '' }}" class="form-control premium-input ml-1" placeholder="Z">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION: FORENSIC FINANCE -->
                        <div class="col-md-4">
                            <h6 class="font-weight-bold text-primary mb-4 border-bottom pb-2"><i class="feather icon-shield mr-1"></i> Forensic FX Snapshot</h6>
                            
                            <div class="form-group">
                                <label class="form-label-premium">ارز معامله (Purchase Currency)</label>
                                <select name="currency_id" id="modal_currency_id" class="form-control border-primary font-weight-bold">
                                    @foreach($currencies as $curr)
                                        <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ ($editCarpet && $editCarpet->currency_id == $curr->id) || (!$editCarpet && $curr->code == 'USD') ? 'selected' : '' }}>{{ $curr->code }} ({{ $curr->symbol }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="exchange_rate" id="modal_exchange_rate" value="{{ $editCarpet->exchange_rate ?? 1 }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label-premium">قیمت فی متر (Native Rate)</label>
                                <input type="number" step="0.01" name="price_input" id="modal_price_input" value="{{ $editCarpet->original_price ?? $editCarpet->price ?? '' }}" class="form-control premium-input border-primary font-weight-bold" required>
                            </div>

                            <!-- FX SNAPSHOT CARD -->
                            <div class="forensic-snapshot">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-muted">Native Total:</span>
                                    <span id="modal_native_total" class="font-weight-bold">0.00 USD</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-dark font-weight-bold">USD Normalized:</span>
                                    <span id="modal_usd_total_label" class="text-primary font-weight-bold">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="small text-success">AFN Value:</span>
                                    <span id="modal_afn_total_label" class="text-success font-weight-bold">0 AFN</span>
                                </div>
                                <div class="small text-center text-muted">
                                    <i class="feather icon-activity mr-1"></i> Conversion: 1 USD = <span id="modal_rate_display">1.00</span> <span id="modal_code_display">USD</span>
                                </div>
                                
                                <input type="hidden" name="total_price" id="modal_total_price_usd">
                                <input type="hidden" name="total_price_af" id="modal_total_price_afn">
                                <input type="hidden" name="price" id="modal_legacy_price">
                            </div>

                            <h6 class="font-weight-bold text-dark mb-3 mt-4 border-bottom pb-2 small uppercase"><i class="feather icon-briefcase mr-1"></i> تنظیمات انبار و دفتر کل</h6>
                            
                            <div class="form-group">
                                <label class="form-label-premium">گدام (Warehouse Location)</label>
                                <select name="warehouse_id" class="form-control premium-input">
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ ($editCarpet && $editCarpet->warehouse_id == $w->id) || ($defaultWarehouseId == $w->id) ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label-premium">حساب دارایی (Inventory GL)</label>
                                <select name="override_inventory_account_id" class="form-control premium-input">
                                    @foreach($inventoryAccounts as $acc)
                                        <option value="{{$acc->id}}" {{ ($editCarpet && $editCarpet->override_inventory_account_id == $acc->id) || (old('override_inventory_account_id', 15) == $acc->id) ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label class="form-label-premium">تصویر محصول (Optional)</label>
                                <div class="custom-file">
                                    <input type="file" name="carpet_image" class="custom-file-input" id="imageInput">
                                    <label class="custom-file-label" for="imageInput" style="border-radius: 10px;">انتخاب عکس...</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top p-4 bg-light">
                    <button type="button" class="btn btn-secondary px-4 rounded-lg" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-lg shadow">
                        <i class="feather icon-save mr-1"></i> {{ $editCarpet ? 'ذخیره تغییرات' : 'ثبت و تایید نهایی' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- IMAGE PREVIEW -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="text-right mb-2">
                <button type="button" class="btn btn-white btn-sm rounded-circle shadow" data-dismiss="modal">&times;</button>
            </div>
            <img src="" id="fullPreviewImage" class="img-fluid rounded shadow-lg mx-auto d-block" style="max-height: 85vh;">
            <div class="text-center mt-3 text-white h5 font-weight-bold" id="previewTitle"></div>
        </div>
    </div>
</div>

<!-- MODAL: SEND TO STOCK -->
<div class="modal fade" id="sendToStockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0 overflow-hidden" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);">
            <div class="modal-header border-bottom p-4 bg-light">
                <h5 class="font-weight-bold mb-0 text-primary">
                    <i class="feather icon-package mr-2"></i> تایید نهایی و انتقال به گدام
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4" style="line-height: 1.8; text-align: right; direction: rtl;">
                    با تایید نهایی این محصول، وضعیت آن به <strong>تکمیل شده (In Stock)</strong> تغییر یافته و از وضعیت موقت خارج خواهد شد. لطفاً گدام نهایی را جهت انتقال محصول انتخاب کنید:
                </p>
                
                <input type="hidden" id="stock_carpet_id">
                
                <div class="form-group mb-0 text-right" style="direction: rtl;">
                    <label class="form-label-premium">گدام هدف (Warehouse Location)</label>
                    <select id="stock_warehouse_id" class="form-control premium-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top p-4 bg-light d-flex justify-content-between" style="direction: rtl;">
                <button type="button" class="btn btn-secondary px-4 rounded-lg" data-dismiss="modal">لغو عملیات</button>
                <button type="button" class="btn btn-primary px-4 rounded-lg shadow" id="btnConfirmSendToStock">
                    <i class="feather icon-check-circle mr-1"></i> تایید و انتقال به انبار
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        @if($editCarpet)
            $('#buyCarpetModal').modal('show');
        @endif

        // Regular Select2 Initialization (for filters outside modal)
        $('form[action="/dashboard/list-buy-carpet"] .select2').select2();

        // Select2 Fix for Modals
        $('#buyCarpetModal .select2').select2({
            dropdownParent: $('#buyCarpetModal')
        });

        // Dependent Dropdown: Filter Invoices by Vendor
        var allInvoiceOptions = $('#modal_purchase_invoice_id option').clone();

        function filterInvoices() {
            var selectedAgent = $('#modal_agent_id').val();
            var $invoiceSelect = $('#modal_purchase_invoice_id');
            var currentlySelected = $invoiceSelect.val();

            // Clear options
            $invoiceSelect.empty();

            // Add the default "please select" option
            $invoiceSelect.append(allInvoiceOptions.first().clone());

            // Filter and append matching options
            allInvoiceOptions.slice(1).each(function() {
                var agentId = $(this).data('agent');
                if (!selectedAgent || agentId == selectedAgent) {
                    $invoiceSelect.append($(this).clone());
                }
            });

            // Restore selection if it still exists in the filtered options
            if ($invoiceSelect.find('option[value="' + currentlySelected + '"]').length > 0) {
                $invoiceSelect.val(currentlySelected);
            } else {
                $invoiceSelect.val('');
            }

            // Trigger change for Select2 to update
            $invoiceSelect.trigger('change.select2');
        }

        // Trigger filter on agent selection change
        $('#modal_agent_id').on('change', function() {
            filterInvoices();
        });

        // Run filter on initial load
        filterInvoices();

        // LIVE CALCULATION ENGINE
        function runForensicCalc() {
            let height = parseFloat($('#modal_height').val()) || 0;
            let width = parseFloat($('#modal_width').val()) || 0;
            let area = height * width;
            $('#modal_area').val(area.toFixed(2));

            let unitPrice = parseFloat($('#modal_price_input').val()) || 0;
            let rate = parseFloat($('#modal_currency_id option:selected').data('rate')) || 1;
            let code = $('#modal_currency_id option:selected').data('code');
            
            $('#modal_rate_display').text(rate.toFixed(4));
            $('#modal_code_display').text(code);
            $('#modal_exchange_rate').val(rate);

            let nativeTotal = area * unitPrice;
            let usdTotal = nativeTotal * rate;
            
            $('#modal_native_total').text(nativeTotal.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + code);
            $('#modal_usd_total_label').text('$' + usdTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
            
            // AFN Legacy Conversion (Fixed Rate 63 as per business notes)
            let afnTotal = usdTotal * 63; 
            $('#modal_afn_total_label').text(afnTotal.toLocaleString(undefined, {maximumFractionDigits: 0}) + ' AFN');

            // Set values for DB persistence
            $('#modal_total_price_usd').val(usdTotal.toFixed(2));
            $('#modal_total_price_afn').val(afnTotal.toFixed(2));
            $('#modal_legacy_price').val((unitPrice * rate).toFixed(2)); 
        }

        $('#modal_height, #modal_width, #modal_price_input, #modal_currency_id').on('input change', runForensicCalc);
        runForensicCalc();

        // Quality Dependent Dropdown
        $("#modal_type_id").change(function () {
            $.ajax({
                url: "{{ route('dashboard.qualities.get_by_type') }}?type_id=" + $(this).val(),
                method: 'GET',
                success: function (data) {
                    $('#modal_quality_id').html(data.html);
                }
            });
        });

        // Double Click Prevention
        $("#buyCarpetForm").submit(function() {
            $(this).find(":submit").attr("disabled", "disabled").html('<i class="feather icon-loader mr-1"></i> در حال پردازش...');
        });

        // Confirm send to stock with selected warehouse
        $('#btnConfirmSendToStock').click(function() {
            let carpet_id = $('#stock_carpet_id').val();
            let warehouse_id = $('#stock_warehouse_id').val();
            
            $(this).attr('disabled', 'disabled').html('<i class="feather icon-loader mr-1"></i> در حال انتقال...');
            
            $.ajax({
                type: 'GET',
                url: '/dashboard/carpet-stock/sent-to-stock/' + carpet_id,
                data: { warehouse_id: warehouse_id },
                success: function (res) {
                    if (res.status == 'success') {
                        $('#sendToStockModal').modal('hide');
                        swal("عملیات موفق!", "محصول با موفقیت به انبار منتقل شد.", "success").then(() => location.reload());
                    } else {
                        $('#btnConfirmSendToStock').removeAttr('disabled').html('<i class="feather icon-check-circle mr-1"></i> تایید و انتقال به انبار');
                        swal("خطا در سیستم!", "متاسفانه امکان انتقال در حال حاضر وجود ندارد.", "error");
                    }
                },
                error: function () {
                    $('#btnConfirmSendToStock').removeAttr('disabled').html('<i class="feather icon-check-circle mr-1"></i> تایید و انتقال به انبار');
                    swal("خطا در سیستم!", "یک خطای غیرمنتظره رخ داد.", "error");
                }
            });
        });
    });

    function showImageModal(src, title) {
        $('#fullPreviewImage').attr('src', src);
        $('#previewTitle').text('تصویر قالین نمبر: ' + title);
        $('#imagePreviewModal').modal('show');
    }

    function sendToStock(carpet_id, current_warehouse_id) {
        $('#stock_carpet_id').val(carpet_id);
        if (current_warehouse_id) {
            $('#stock_warehouse_id').val(current_warehouse_id);
        }
        $('#sendToStockModal').modal('show');
    }
</script>
@endsection
