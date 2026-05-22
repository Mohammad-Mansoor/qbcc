@extends('dsh.master')
@section('title' , 'Agent Carpets - QBCC Forensic ERP')

@section('content')
<style>
    /* QBCC PREMIUM DESIGN SYSTEM */
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-header-bg: #ffffff;
        --qbcc-border: #e2e8f0;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    .page-header-modern {
        background: var(--qbcc-header-bg);
        border-bottom: 1px solid var(--qbcc-border);
        margin-bottom: 25px;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* PREMIUM MODAL DESIGN */
    .qbcc-modal-content {
        background: white !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
    }

    .modal-header {
        background: var(--qbcc-gradient);
        padding: 18px 30px;
        border: none;
    }

    .modal-title { color: #ffffff; font-weight: 800; font-size: 18px; }
    .modal-header .close { color: #ffffff; opacity: 0.9; font-size: 24px; }

    .form-section {
        background: #f8fafc;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 800;
        color: var(--qbcc-primary);
        font-size: 12px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-right: 4px solid var(--qbcc-secondary);
    }

    .input-group-modern { margin-bottom: 15px; }
    .input-group-modern label {
        display: block;
        font-weight: 600;
        color: #475569;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .form-control-modern {
        width: 100%;
        height: 40px;
        padding: 8px 14px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.2s ease;
        color: #1e293b;
    }

    .form-control-modern:focus {
        border-color: var(--qbcc-secondary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* TABLE STYLING */
    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .table-modern thead th { background: #f8fafc; padding: 15px; font-weight: 700; color: #64748b; font-size: 11px; border: none; text-transform: uppercase; }
    .table-modern tbody tr { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s; }
    .table-modern tbody tr:hover { transform: scale(1.002); box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    .table-modern td { padding: 15px; vertical-align: middle; border: none; font-size: 13px; }
    .table-modern td:first-child { border-radius: 12px 0 0 12px; }
    .table-modern td:last-child { border-radius: 0 12px 12px 0; }

    .badge-premium { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; }

    .btn-action-round { 
        width: 32px; height: 32px; 
        border-radius: 8px; 
        display: inline-flex; 
        align-items: center; justify-content: center; 
        margin: 0 2px; border: none; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-action-round:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>

<div class="container-fluid">
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 text-dark font-weight-bold">لیست قالین های نزد {{ $agent->user->name }}</h3>
            <p class="text-muted mb-0 small"><i class="feather icon-package text-primary mr-1"></i> مدیریت گدام و تولید نماینده</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary rounded-lg px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#carpetModal">
                <i class="feather icon-plus mr-1"></i> ثبت پارچه جدید
            </button>
            <button class="btn btn-white border-light shadow-sm px-4 rounded-lg" onclick="printStandardized('agent-carpet')">
                <i class="feather icon-printer mr-1"></i> چاپ لیست
            </button>
        </div>
    </div>

    @if(session("status"))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            {{session('status')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card glass-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <form action="/dashboard/agent-carpet/search" method="post" style="width: 300px;">
                    @csrf
                    <input type="hidden" name="agent_id" value="{{$agent->agent_id}}">
                    <div class="position-relative">
                        <i class="feather icon-search position-absolute" style="right: 15px; top: 13px; color: #94a3b8;"></i>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="جستجوی قالین..." class="form-control-modern pr-5 w-100">
                    </div>
                </form>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-right">
                        <span class="text-muted small d-block">مجموع مساحت</span>
                        <span class="font-weight-bold text-dark">{{ number_format($metrazh, 2) }} m²</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive" id="agent-carpet">
                <table class="table-modern" id="carpet_list_table">
                    <thead>
                        <tr>
                            <th>شماره قالین</th>
                            <th>بافنده</th>
                            <th>نمبر فرمایش</th>
                            <th>نوعیت / کوالتی</th>
                            <th class="text-center">ابعاد (W×H)</th>
                            <th class="text-center">مساحت</th>
                            <th class="text-center">قیمت (USD)</th>
                            <th class="hideOnPrint text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carpets as $carpet)
                        <tr>
                            <td class="font-weight-bold text-primary">{{ $carpet->carpet_no }}</td>
                            <td>{{ $carpet->carpetEmployee ? $carpet->carpetEmployee->first_name : '---' }}</td>
                            <td><span class="badge-premium bg-light-info text-info">{{ $carpet->carpet_order ? $carpet->carpet_order->order_number : '---' }}</span></td>
                            <td>
                                <span class="d-block font-weight-bold">{{ $carpet->type ? $carpet->type->carpet_type : '---' }}</span>
                                <small class="text-muted">{{ $carpet->quality ? $carpet->quality->quality : '---' }}</small>
                            </td>
                            <td class="text-center" dir="ltr">{{ $carpet->width }} × {{ $carpet->height }} m</td>
                            <td class="text-center font-weight-bold text-dark">{{ $carpet->area }} m²</td>
                            <td class="text-center font-weight-bold text-success" dir="ltr">${{ number_format($carpet->total_price, 2) }}</td>
                            <td class="hideOnPrint text-center">
                                <div class="d-flex justify-content-center">
                                    <a href="/dashboard/agent-carpet/{{$carpet->carpet_id}}/edit" class="btn-action-round bg-light-info text-info" title="ویرایش"><i class="feather icon-edit"></i></a>
                                    <a href="/dashboard/agent-carpets/{{$carpet->carpet_id}}" class="btn-action-round bg-light-primary text-primary" title="جزییات"><i class="feather icon-eye"></i></a>
                                    <button class="btn-action-round bg-light-warning text-warning" onclick="sendToService('{{$carpet->carpet_id}}', 'kachaee')" title="ارسال به کچایی"><i class="feather icon-tool"></i></button>
                                    <button class="btn-action-round bg-light-success text-success" onclick="sendToService('{{$carpet->carpet_id}}', 'washing')" title="ارسال به شست"><i class="feather icon-droplet"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $carpets->links() }}
            </div>
        </div>
    </div>
</div>

<!-- CARPET MODAL -->
<div class="modal fade" id="carpetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content qbcc-modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $editCarpet ? 'ویرایش پارچه' : 'ثبت پارچه جدید' }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="carpetForm" action="{{ $editCarpet ? '/dashboard/contract-carpet/'.$editCarpet->carpet_id : '/dashboard/contract-carpet' }}" method="post">
                    @csrf
                    @if($editCarpet) @method('PATCH') @endif
                    
                    <input type="hidden" name="agent_id" value="{{ $agent->agent_id }}">
                    <input type="hidden" name="status" value="0">
                    <input type="hidden" name="agent_carpet" value="agent carpet">
                    <input type="hidden" name="carpet_no" value="{{ $editCarpet ? $editCarpet->carpet_no : $AccountNo }}">

                    <div class="form-section"><i class="feather icon-tag"></i> ۱. مشخصات عمومی و بافنده</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>نام نماینده</label>
                                <input type="text" value="{{ $agent->user->name }}" class="form-control-modern bg-light font-weight-bold" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>نمبر قالین</label>
                                <input type="text" value="{{ $editCarpet ? $editCarpet->carpet_no : $AccountNo }}" class="form-control-modern bg-light font-weight-bold" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>اسم بافنده / کارگر</label>
                                <select name="employee_id" class="form-control-modern">
                                    <option value="">انتخاب بافنده...</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ ($editCarpet && $editCarpet->employee_id == $emp->id) ? 'selected' : '' }}>{{ $emp->first_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>شماره فرمایش</label>
                                <select name="order_id" class="form-control-modern">
                                    <option value="">انتخاب فرمایش...</option>
                                    @foreach($orders as $ord)
                                        <option value="{{ $ord->id }}" {{ ($editCarpet && $editCarpet->order_id == $ord->id) ? 'selected' : '' }}>{{ $ord->order_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>نوعیت قالین</label>
                                <select name="type_id" id="type_id_select" class="form-control-modern">
                                    <option value="">انتخاب نوعیت...</option>
                                    @foreach($types as $t)
                                        <option value="{{ $t->carpet_type_id }}" {{ ($editCarpet && $editCarpet->type_id == $t->carpet_type_id) ? 'selected' : '' }}>{{ $t->carpet_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>کوالتی</label>
                                <select name="quality_id" id="quality_id_select" class="form-control-modern">
                                    <option value="">ابتدا نوعیت را انتخاب کنید</option>
                                    @if($editCarpet)
                                        <option value="{{ $editCarpet->quality_id }}" selected>{{ $editCarpet->quality->quality }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>گدام پیش فرض</label>
                                <select name="warehouse_id" class="form-control-modern">
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ ($editCarpet && $editCarpet->warehouse_id == $wh->id) ? 'selected' : '' }}>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mt-2"><i class="feather icon-maximize"></i> ۲. ابعاد و دیزاین</div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group-modern">
                                <label>عرض (m)</label>
                                <input type="number" step="0.01" name="width" id="width_input" value="{{ $editCarpet ? $editCarpet->width : '' }}" class="form-control-modern calc-trigger">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group-modern">
                                <label>طول (m)</label>
                                <input type="number" step="0.01" name="height" id="height_input" value="{{ $editCarpet ? $editCarpet->height : '' }}" class="form-control-modern calc-trigger">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group-modern">
                                <label>مساحت (m²)</label>
                                <input type="text" name="area" id="area_input" value="{{ $editCarpet ? $editCarpet->area : '' }}" class="form-control-modern bg-light" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group-modern">
                                <label>نمبر نقشه</label>
                                <input type="text" name="map_number" value="{{ $editCarpet ? $editCarpet->map_number : '' }}" class="form-control-modern">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group-modern">
                                <label>حاشیه</label>
                                <input type="text" name="margin" value="{{ $editCarpet ? $editCarpet->margin : '' }}" class="form-control-modern">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group-modern">
                                <label>زمینه</label>
                                <input type="text" name="field" value="{{ $editCarpet ? $editCarpet->field : '' }}" class="form-control-modern">
                            </div>
                        </div>
                    </div>

                    <div class="form-section mt-2"><i class="feather icon-dollar-sign"></i> ۳. تنظیمات ارز و قیمت گذاری</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>ارز انتخابی</label>
                                <select id="currency_select" class="form-control-modern">
                                    @foreach($currencies as $curr)
                                        @php
                                            $isDefault = false;
                                            if ($editCarpet) {
                                                $isDefault = ($editCarpet->currency_code == $curr->code);
                                            } else {
                                                // Map agent account_type to currency code
                                                $agentType = strtolower($agent->account_type);
                                                if ($agentType == 'dollar' || $agentType == 'usd') $isDefault = ($curr->code == 'USD');
                                                elseif ($agentType == 'afghani' || $agentType == 'afn') $isDefault = ($curr->code == 'AFN');
                                                elseif ($agentType == 'pkr' || $agentType == 'rs') $isDefault = ($curr->code == 'PKR');
                                                elseif ($agentType == 'eur' || $agentType == 'euro') $isDefault = ($curr->code == 'EUR');
                                            }
                                        @endphp
                                        <option value="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}" {{ $isDefault ? 'selected' : '' }}>
                                            {{ $curr->name }} ({{ $curr->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="currency_code" id="currency_code_hidden">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label id="price_label">قیمت فی متر (AFN)</label>
                                <input type="number" step="0.01" id="price_per_meter" name="original_price" value="{{ $editCarpet ? $editCarpet->original_price : '' }}" class="form-control-modern calc-trigger">
                                <input type="hidden" name="price" id="price_af_hidden">
                                <input type="hidden" name="dollar_rate" id="dollar_rate_hidden">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label id="total_selected_label">مجموع ارز انتخابی</label>
                                <input type="text" id="total_selected_input" class="form-control-modern bg-light font-weight-bold text-dark" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>مجموع (USD)</label>
                                <input type="text" name="total_price" id="total_usd_input" class="form-control-modern bg-light font-weight-bold text-success" readonly>
                                <input type="hidden" name="total_price_af" id="total_af_hidden">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>تاریخ ثبت</label>
                                <input type="date" name="date" value="{{ $editCarpet ? $editCarpet->date : date('Y-m-d') }}" class="form-control-modern">
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 font-weight-bold shadow-sm">تایید و ثبت قالین</button>
                        <button type="button" class="btn btn-link text-muted ml-3" data-dismiss="modal">انصراف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-plugins')
<script>
    function printStandardized(sectionID) {
        window.print();
    }

    function sendToService(id, type) {
        let url = type === 'kachaee' ? '/dashboard/carpet-repaire/sending-to-kachaee/' : '/dashboard/washing-team/sending-to-washing/';
        window.location.href = url + id;
    }

    $(document).ready(function () {
        @if($editCarpet) $('#carpetModal').modal('show'); @endif

        // DYNAMIC QUALITY
        $("#type_id_select").change(function () {
            $.ajax({
                url: "{{ route('dashboard.qualities.get_by_type') }}?type_id=" + $(this).val(),
                method: 'GET',
                success: function (data) {
                    $('#quality_id_select').html(data.html);
                }
            });
        });

        // FORENSIC CALCULATIONS (5th PILLAR)
        function performCalculations() {
            let width = parseFloat($('#width_input').val()) || 0;
            let height = parseFloat($('#height_input').val()) || 0;
            let area = width * height;
            $('#area_input').val(area.toFixed(2));

            let selectedOption = $('#currency_select option:selected');
            let currencyCode = selectedOption.val();
            let exchangeRate = parseFloat(selectedOption.data('rate')) || 0; // Value of 1 unit in USD
            let priceInput = parseFloat($('#price_per_meter').val()) || 0;

            // Update Labels
            $('#price_label').text('قیمت فی متر (' + currencyCode + ')');
            $('#total_selected_label').text('مجموع (' + currencyCode + ')');

            // 1. Calculate Total in Selected Currency
            let totalSelected = area * priceInput;
            $('#total_selected_input').val(totalSelected.toLocaleString(undefined, {minimumFractionDigits: 2}));

            // 2. Calculate Total in USD (Normalization)
            let totalUSD = totalSelected * exchangeRate;
            $('#total_usd_input').val(totalUSD.toFixed(2));

            // 3. Calculate legacy AFN Total (for reports)
            let afnOption = $('#currency_select option[value="AFN"]');
            let afnInUSD = parseFloat(afnOption.data('rate')) || 0.0158;
            let totalAFN = totalUSD / afnInUSD;

            // Populate Hidden Fields for Backend
            $('#total_af_hidden').val(totalAFN.toFixed(2));
            $('#price_af_hidden').val((totalAFN / area || 0).toFixed(2));
            $('#dollar_rate_hidden').val(exchangeRate);
            $('#currency_code_hidden').val(currencyCode);
        }

        $('.calc-trigger').on('input change', performCalculations);
        $('#currency_select').on('change', function() {
            performCalculations();
        });

        // Trigger calculations once on load (important for edit mode)
        performCalculations();

        // Handle URL cleanup when closing edit modal
        $('#carpetModal').on('hidden.bs.modal', function () {
            @if($editCarpet)
                window.location.href = "/dashboard/agent-carpet/{{ $agent->agent_id }}";
            @endif
        });
    });
</script>
@endsection