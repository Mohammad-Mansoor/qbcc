@extends('dsh.master')
@section('title', 'Weight Carpet Details - ' . $carpet->parcha_number)

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
        transition: all 0.3s;
    }

    .glass-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .nav-pills .nav-link {
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.2s;
    }

    .nav-pills .nav-link.active {
        background: var(--qbcc-primary);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
    }

    .stat-pill {
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
    }

    .table-modern thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border: none;
    }

    .forensic-snapshot {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 15px;
        border: 1px dashed #cbd5e1;
    }

    .form-label-premium { font-weight: 700; color: #334155; font-size: 12px; margin-bottom: 5px; display: block; }
    .premium-input { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; transition: all 0.2s; }
</style>

<div class="row">
    <!-- LEFT COLUMN: CARPET SUMMARY & FORENSIC SNAPSHOT -->
    <div class="col-md-4">
        <div class="glass-card p-4 mb-4 text-center">
            @if($carpet->carpet_image)
                <img src="/{{$carpet->carpet_image}}" class="img-fluid rounded-lg shadow-sm mb-3" style="max-height: 300px; object-fit: cover;">
            @else
                <div class="bg-light rounded-lg d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                    <i class="feather icon-image text-muted" style="font-size: 40px;"></i>
                </div>
            @endif
            <h4 class="font-weight-bold mb-1">PN-{{ $carpet->parcha_number }}</h4>
            <div class="badge badge-pill {{ $carpet->status == 5 ? 'badge-success' : 'badge-primary' }} px-3 py-2">
                {{ $carpet->status == 5 ? 'تکمیل شده (Completed)' : 'در حال کار (WIP)' }}
            </div>
        </div>

        <!-- FORENSIC FX SNAPSHOT -->
        <div class="glass-card p-4 mb-4">
            <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-shield mr-2"></i> Forensic FX Snapshot</h6>
            <div class="forensic-snapshot">
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="small text-muted">Original Currency:</span>
                    <span class="font-weight-bold">{{ $carpet->currency_code ?? 'USD' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small">Original Price:</span>
                    <span class="font-weight-bold">{{ number_format($carpet->original_price ?? $carpet->price, 2) }} {{ $carpet->currency_code ?? '$' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-dark font-weight-bold">USD Normalization:</span>
                    <span class="text-dark font-weight-bold">${{ number_format($carpet->total_price, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-success">AFN Reporting:</span>
                    @php
                        $displayRate = $carpet->dollar_rate > 0 ? $carpet->dollar_rate : (\App\Currency::where('code', 'AFN')->first()->exchange_rate > 0 ? (1 / \App\Currency::where('code', 'AFN')->first()->exchange_rate) : 0);
                    @endphp
                    <span class="text-success font-weight-bold">{{ number_format($carpet->total_price * $displayRate, 0) }} AFN</span>
                </div>
                <div class="mt-3 pt-3 border-top small text-muted text-center">
                    <i class="feather icon-clock mr-1"></i> Exchange Rate: 1 USD = {{ number_format($displayRate, 2) }} AFN
                </div>
            </div>
        </div>

        <!-- QUICK STATS -->
        <div class="glass-card p-4">
            <h6 class="font-weight-bold text-dark mb-3"><i class="feather icon-info mr-2"></i> مشخصات عمومی</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted">نماینده:</td><td class="text-right font-weight-bold">{{ $carpet->agent->user->name ?? 'N/A' }}</td></tr>
                <tr><td class="text-muted">نوعیت:</td><td class="text-right font-weight-bold">{{ $carpet->type->carpet_type ?? 'N/A' }}</td></tr>
                <tr><td class="text-muted">کوالتی:</td><td class="text-right font-weight-bold">{{ $carpet->quality->quality ?? 'N/A' }}</td></tr>
                <tr><td class="text-muted">مساحت:</td><td class="text-right font-weight-bold">{{ $carpet->area }} m²</td></tr>
                <tr><td class="text-muted">ابعاد:</td><td class="text-right font-weight-bold" dir="ltr">{{ $carpet->height }} x {{ $carpet->width }}</td></tr>
                <tr><td class="text-muted">نقشه:</td><td class="text-right font-weight-bold">{{ $carpet->map_number }}</td></tr>
            </table>
        </div>
    </div>

    <!-- RIGHT COLUMN: TABS & FORMS -->
    <div class="col-md-8">
        @if(session("status"))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('status') }}</div>
        @endif
        @if(session("error"))
            <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        <div class="glass-card p-4">
            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                <li class="nav-item mr-2">
                    <a class="nav-link {{ $material == null ? 'active' : '' }}" id="pills-carpet-details-tab" data-toggle="pill" href="#carpet-details">
                        <i class="feather icon-list mr-2"></i> جزییات قالین
                    </a>
                </li>
                <li class="nav-item mr-2">
                    <a class="nav-link" id="pills-carpet-checkbook-tab" data-toggle="pill" href="#carpet-checkbook">
                        <i class="feather icon-book mr-2"></i> چک بٌک
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $material != null ? 'active' : '' }}" id="pills-material-recieves-tab" data-toggle="pill" href="#material-recieves">
                        <i class="feather icon-package mr-2"></i> دریافت‌های مواد
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- TAB: DETAILS -->
                <div class="tab-pane fade {{ $material == null ? 'show active' : '' }}" id="carpet-details" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0">جزییات فنی و ارزشی</h5>
                        <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()">
                            <i class="feather icon-printer mr-1"></i> چاپ مشخصات
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-lg mb-3">
                                <label class="small text-muted d-block mb-1">زمینه (Field)</label>
                                <span class="font-weight-bold">{{ $carpet->field }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-lg mb-3">
                                <label class="small text-muted d-block mb-1">حاشیه (Margin)</label>
                                <span class="font-weight-bold">{{ $carpet->margin }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="bg-light p-3 rounded-lg mb-3">
                                <label class="small text-muted d-block mb-1">شماره فرمایش (Order No)</label>
                                <span class="font-weight-bold">{{ $carpet->order->order_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-lg mb-3">
                                <label class="small text-muted d-block mb-1">تاریخ ثبت</label>
                                <span class="font-weight-bold">{{ $carpet->date }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-lg mb-3">
                                <label class="small text-muted d-block mb-1">گودام فعلی</label>
                                <span class="font-weight-bold">{{ $carpet->warehouse->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: CHECKBOOK -->
                <div class="tab-pane fade" id="carpet-checkbook" role="tabpanel">
                    @if (!$carpetCheckBook)
                        <div class="bg-light p-4 rounded-lg">
                            <h5 class="font-weight-bold mb-4">ثبت چک بٌک جدید</h5>
                            <form action="/dashboard/check-book" method="post">
                                @csrf
                                <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                                <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">ارز انتخاب شده (Currency)</label>
                                        <select name="currency_id" id="check_currency_id" class="form-control border-primary">
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}">{{ $curr->code }} ({{ $curr->symbol }})</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="exchange_rate" id="check_exchange_rate" value="1">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">مقدار کچایی (<span id="check_ppm_label">USD</span>)</label>
                                        <input type="number" step="0.01" name="kachaee_amount_input" id="check_kachaee_input" class="form-control premium-input border-primary" placeholder="مقدار را وارد کنید">
                                        <input type="hidden" name="kachaee_amount" id="final_kachaee_native">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">معادل دالر (USD Normalization)</label>
                                        <input type="text" name="kachaee_dollar_amount" id="check_kachaee_usd" class="form-control premium-input bg-white" readonly>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">نمبر چک بوک</label>
                                        <input type="text" name="check_number" value="{{$CheckNo}}" class="form-control premium-input">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="form-label-premium">حساب بدهکار (Debit: Expense/Repair)</label>
                                        <select name="debit_account_id" class="form-control select2">
                                            @foreach($expenseAccounts as $acc)
                                                <option value="{{$acc->id}}" {{ $acc->account_code == '5100' ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="form-label-premium">حساب بستانکار (Credit: WIP Inventory)</label>
                                        <select name="credit_account_id" class="form-control select2">
                                            @foreach($inventoryAccounts as $acc)
                                                <option value="{{$acc->id}}" {{ ($carpet->override_inventory_account_id == $acc->id) ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">طول</label>
                                        <input type="text" name="height" value="{{$carpet->height}}" class="form-control premium-input" readonly>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">عرض</label>
                                        <input type="text" name="width" value="{{$carpet->width}}" class="form-control premium-input" readonly>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">مساحت</label>
                                        <input type="text" name="area" value="{{$carpet->area}}" class="form-control premium-input">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">تاریخ</label>
                                        <input type="date" name="date" value="{{$carpet->date}}" class="form-control premium-input">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-lg px-5 shadow mt-3">ذخیره چک بٌک</button>
                            </form>
                        </div>
                    @else
                        <div id="editDetails">
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="font-weight-bold">جزییات چک بٌک ثبت شده</h5>
                                <button class="btn btn-info btn-sm rounded-pill" id="editBtn"><i class="feather icon-edit mr-1"></i> ویرایش</button>
                            </div>
                            <table class="table table-bordered">
                                <tr><th class="bg-light w-25">شماره چک بوک</th><td>{{$carpetCheckBook->check_number}}</td></tr>
                                <tr><th class="bg-light">طول / عرض</th><td>{{$carpetCheckBook->height}} x {{$carpetCheckBook->width}}</td></tr>
                                <tr><th class="bg-light">مساحت</th><td>{{$carpetCheckBook->area}} m²</td></tr>
                                <tr>
                                    <th class="bg-light">پول کچایی (Native)</th>
                                    <td class="font-weight-bold">
                                        {{number_format($carpetCheckBook->kachaee_amount, 2)}} {{ $carpetCheckBook->currency_code ?? 'AFN' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-primary">معادل دالر (Forensic Base)</th>
                                    <td class="text-primary font-weight-bold">
                                        ${{number_format($carpetCheckBook->kachaee_dollar_amount, 2)}}
                                        @if($carpetCheckBook->currency_code && $carpetCheckBook->currency_code != 'USD')
                                            <span class="small text-muted ml-2">(Rate: {{ number_format($carpetCheckBook->exchange_rate, 4) }})</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr><th class="bg-light">تاریخ</th><td>{{$carpetCheckBook->date}}</td></tr>
                            </table>
                        </div>

                        <div id="editForm" style="display:none" class="bg-light p-4 rounded-lg">
                            <h5 class="font-weight-bold mb-4">ویرایش چک بٌک</h5>
                            <form action="/dashboard/check-book/{{$carpetCheckBook->id}}" method="post">
                                @csrf @method("PUT")
                                <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">ارز انتخاب شده</label>
                                        <select name="currency_id" id="check_currency_id_edit" class="form-control border-info">
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ ($carpetCheckBook->currency_code == $curr->code) ? 'selected' : '' }}>{{ $curr->code }} ({{ $curr->symbol }})</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="exchange_rate" id="check_exchange_rate_edit" value="{{ $carpetCheckBook->exchange_rate }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">مقدار کچایی (<span id="check_ppm_label_edit">{{ $carpetCheckBook->currency_code ?? 'AFN' }}</span>)</label>
                                        <input type="number" step="0.01" name="kachaee_amount_input" id="check_kachaee_input_edit" value="{{$carpetCheckBook->kachaee_amount}}" class="form-control premium-input border-info">
                                        <input type="hidden" name="kachaee_amount" id="final_kachaee_native_edit" value="{{$carpetCheckBook->kachaee_amount}}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">معادل دالر</label>
                                        <input type="text" name="kachaee_dollar_amount" id="check_kachaee_usd_edit" value="{{$carpetCheckBook->kachaee_dollar_amount}}" class="form-control premium-input bg-white" readonly>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="form-label-premium">نمبر چک بوک</label>
                                        <input type="text" name="check_number" value="{{$carpetCheckBook->check_number}}" class="form-control premium-input">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="form-label-premium">حساب بدهکار (Debit)</label>
                                        <select name="debit_account_id" class="form-control select2">
                                            @foreach($expenseAccounts as $acc)
                                                <option value="{{$acc->id}}" {{ $acc->account_code == '5100' ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="form-label-premium">حساب بستانکار (Credit)</label>
                                        <select name="credit_account_id" class="form-control select2">
                                            @foreach($inventoryAccounts as $acc)
                                                <option value="{{$acc->id}}" {{ ($carpet->override_inventory_account_id == $acc->id) ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <input type="hidden" name="height" value="{{$carpetCheckBook->height}}">
                                    <input type="hidden" name="width" value="{{$carpetCheckBook->width}}">
                                    <input type="hidden" name="area" value="{{$carpetCheckBook->area}}">
                                    <input type="hidden" name="date" value="{{$carpetCheckBook->date}}">
                                    <input type="hidden" name="old_kachaee_amount" value="{{$carpetCheckBook->kachaee_amount}}">
                                    <input type="hidden" name="old_kachaee_dollar_amount" value="{{$carpetCheckBook->kachaee_dollar_amount}}">
                                </div>
                                <button type="submit" class="btn btn-info rounded-lg px-5 shadow mt-3">بروزرسانی چک بٌک</button>
                                <button type="button" class="btn btn-light rounded-lg px-4 mt-3 ml-2" onclick="$('#editForm').hide(); $('#editDetails').show();">انصراف</button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- TAB: MATERIAL -->
                <div class="tab-pane fade {{ $material != null ? 'show active' : '' }}" id="material-recieves" role="tabpanel">
                    <div class="bg-light p-4 rounded-lg mb-4">
                        <h5 class="font-weight-bold mb-4">{{ $material ? 'ویرایش دریافت مواد' : 'ثبت دریافت مواد جدید' }}</h5>
                        <form action="{{ $material ? '/dashboard/carpet-material/'.$material->id : '/dashboard/carpet-material' }}" method="post">
                            @csrf
                            @if($material) @method('PUT') @endif
                            <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                            <input type="hidden" name="agent_id" value="{{$carpet->agent->agent_id}}">
                            
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">ارز انتخاب شده (Currency)</label>
                                    <select name="currency_id" id="mat_currency_id" class="form-control border-primary">
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ ($material && $material->currency_code == $curr->code) || (!$material && $curr->code == 'AFN') ? 'selected' : '' }}>{{ $curr->code }} ({{ $curr->symbol }})</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="exchange_rate" id="mat_exchange_rate" value="1">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">مقدار مواد (KG)</label>
                                    <input type="number" step="0.01" name="amount" id="material-amount" value="{{ $material->amount ?? '' }}" class="form-control premium-input border-primary" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">قیمت فی کیلو (<span id="mat_unit_label">AFN</span>)</label>
                                    <input type="number" step="0.01" name="price" id="material-price" value="{{ $material->price ?? '' }}" class="form-control premium-input border-primary" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">گدام منبع (Source Warehouse)</label>
                                    <select name="warehouse_id" class="form-control">
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">نوعیت</label>
                                    <select name="type_id" class="form-control">
                                        @foreach ($material_types as $type)
                                            <option value="{{$type->material_type_id}}" {{ ($material && $material->type_id == $type->material_type_id) ? 'selected' : '' }}>{{$type->material_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">دسته بندی</label>
                                    <select name="category_id" class="form-control">
                                        @foreach ($categories as $category)
                                            <option value="{{$category->material_category_id}}" {{ ($material && $material->category_id == $category->material_category_id) ? 'selected' : '' }}>{{$category->material_category}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label-premium">تاریخ</label>
                                    <input type="date" name="date" value="{{ $material->date ?? $carpet->date }}" class="form-control premium-input">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label-premium">حساب بدهکار (Debit: WIP/Carpet Inventory)</label>
                                    <select name="debit_account_id" class="form-control select2">
                                        @foreach($inventoryAccounts as $acc)
                                            <option value="{{$acc->id}}" {{ ($carpet->override_inventory_account_id == $acc->id) ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label-premium">حساب بستانکار (Credit: Raw Material Stock)</label>
                                    <select name="credit_account_id" class="form-control select2">
                                        @foreach($rawMaterialAccounts as $acc)
                                            <option value="{{$acc->id}}" {{ $acc->account_code == '1202' ? 'selected' : '' }}>{{$acc->account_name}} ({{$acc->account_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row forensic-snapshot m-1 mb-3">
                                <div class="col-6"><small class="text-muted">مجموع به اسعار:</small> <span id="mat_native_total_label" class="font-weight-bold">0.00 AFN</span></div>
                                <div class="col-6 text-right"><small class="text-muted">معادل دالر (Normalized):</small> <span id="mat_usd_total_label" class="font-weight-bold text-primary">$0.00</span></div>
                                <input type="hidden" name="total_price_af" id="material-af-total-price">
                                <input type="hidden" name="total_price" id="material-total-price">
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary px-5 rounded-lg shadow">{{ $material ? 'بروزرسانی' : 'ذخیره مواد' }}</button>
                            </div>
                        </form>
                    </div>

                    <!-- MATERIAL LIST -->
                    <h6 class="font-weight-bold mb-3 mt-4">تاریخچه مواد دریافتی</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
                            <thead>
                                <tr>
                                    <th>مقدار (Qty)</th>
                                    <th>قیمت فی (Unit Price)</th>
                                    <th>مجموع اسعار (Native Total)</th>
                                    <th>معادل دالر (USD Base)</th>
                                    <th>نوعیت / کتگوری</th>
                                    <th>تاریخ</th>
                                    <th class="hideOnPrint">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carpetMaterials as $mat)
                                    <tr>
                                        <td class="font-weight-bold">{{ $mat->amount }} kg</td>
                                        <td>
                                            <span class="text-muted small">{{ $mat->currency_code ?? 'AFN' }}</span>
                                            {{ number_format($mat->price, 2) }}
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ number_format($mat->price * $mat->amount, 2) }}
                                            <span class="text-muted small">{{ $mat->currency_code ?? 'AFN' }}</span>
                                        </td>
                                        <td class="text-primary font-weight-bold">
                                            ${{ number_format($mat->total_price, 2) }}
                                            @if($mat->currency_code && $mat->currency_code != 'USD')
                                                <div class="small text-muted" style="font-size: 10px;">Forensic FX: {{ number_format($mat->exchange_rate, 4) }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $mat->type->material_type ?? 'N/A' }} ({{ $mat->category->material_category ?? 'N/A' }})</td>
                                        <td>{{ $mat->date }}</td>
                                        <td class="hideOnPrint">
                                            <a href="/dashboard/carpet-material/{{$mat->id}}/edit" class="btn btn-sm btn-light-info"><i class="feather icon-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="2" class="text-dark">مجموع مواد مصرفی (Total Production Inputs):</td>
                                    <td class="text-success">
                                        <small class="text-muted d-block">Reporting:</small>
                                        {{ number_format($carpetMaterials->sum('total_price_af')) }} AFN
                                    </td>
                                    <td class="text-primary" colspan="3">
                                        <small class="text-muted d-block">USD Normalization (Forensic Base):</small>
                                        ${{ number_format($carpetMaterials->sum('total_price'), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    $(document).ready(function () {
        feather.replace();

        // Edit toggle for checkbook
        $('#editBtn').click(function () {
            $('#editForm').toggle();
            $('#editDetails').toggle();
        });

        // CALCULATIONS: CHECKBOOK
        function runCheckbookCalc() {
            let inputVal = parseFloat($('#check_kachaee_input').val()) || 0;
            let rate = parseFloat($('#check_currency_id option:selected').data('rate')) || 1;
            let code = $('#check_currency_id option:selected').data('code');

            $('#check_ppm_label').text(code);
            $('#check_exchange_rate').val(rate);
            
            // Native amount for DB (AFN fallback or original)
            $('#final_kachaee_native').val(inputVal.toFixed(2));
            
            // USD Normalization
            let usdVal = inputVal * rate;
            $('#check_kachaee_usd').val(usdVal.toFixed(2));
        }

        $('#check_kachaee_input, #check_currency_id').on('input change', runCheckbookCalc);
        runCheckbookCalc();

        // CALCULATIONS: CHECKBOOK EDIT
        function runCheckbookCalcEdit() {
            let inputVal = parseFloat($('#check_kachaee_input_edit').val()) || 0;
            let rate = parseFloat($('#check_currency_id_edit option:selected').data('rate')) || 1;
            let code = $('#check_currency_id_edit option:selected').data('code');

            $('#check_ppm_label_edit').text(code);
            $('#check_exchange_rate_edit').val(rate);
            
            $('#final_kachaee_native_edit').val(inputVal.toFixed(2));
            let usdVal = inputVal * rate;
            $('#check_kachaee_usd_edit').val(usdVal.toFixed(2));
        }

        $('#check_kachaee_input_edit, #check_currency_id_edit').on('input change', runCheckbookCalcEdit);
        runCheckbookCalcEdit();

        // CALCULATIONS: MATERIAL
        function runMaterialCalc() {
            let amount = parseFloat($('#material-amount').val()) || 0;
            let price = parseFloat($('#material-price').val()) || 0;
            let rate = parseFloat($('#mat_currency_id option:selected').data('rate')) || 1;
            let code = $('#mat_currency_id option:selected').data('code');

            $('#mat_unit_label').text(code);
            $('#mat_exchange_rate').val(rate);

            let native_total = amount * price;
            let usd_total = native_total * rate;

            $('#mat_native_total_label').text(native_total.toLocaleString() + ' ' + code);
            $('#mat_usd_total_label').text('$' + usd_total.toLocaleString(undefined, {minimumFractionDigits: 2}));

            // Values for DB (normalized USD + AFN reporting)
            $('#material-total-price').val(usd_total.toFixed(2));
            
            // For AFN reporting column, we need to convert USD back to AFN using the system rate
            let carpet_dollar_rate = {{ $carpet->dollar_rate > 0 ? $carpet->dollar_rate : 1 }};
            $('#material-af-total-price').val((usd_total * (carpet_dollar_rate > 0 ? (1/carpet_dollar_rate) : 1)).toFixed(2));
        }

        $('#material-amount, #material-price, #mat_currency_id').on('input change', runMaterialCalc);
        runMaterialCalc();
    });
</script>
@endsection