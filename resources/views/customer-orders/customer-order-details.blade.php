@extends('dsh.master')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.2);
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: transform 0.3s ease;
    }

    .order-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-on-loom { background: #fff3cd; color: #856404; }
    .status-ready { background: #d4edda; color: #155724; }
    .status-shipped { background: #cce5ff; color: #004085; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .action-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 5px;
        transition: all 0.2s;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .table-xs th {
        font-weight: 600;
        color: #4a5568;
        background: #f7fafc;
        border-top: none !important;
    }

    .finance-summary {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }

    .finance-item {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 10px;
    }

    @media print {
        .hideOnPrint { display: none !important; }
        .glass-card { box-shadow: none; border: 1px solid #ddd; }
    }
</style>

<div class="container-fluid">
    <!-- Order Summary Header -->
    <div class="order-header glass-card">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-1 text-white">Order: #{{$customer_order->order_name}}</h2>
                <p class="mb-0 opacity-75">Customer: <strong>{{$customer_order->customer->name ?? 'N/A'}}</strong></p>
                <p class="mb-0 opacity-75">Date: {{ $customer_order->order_date }}</p>
            </div>
            <div class="col-md-6 text-right">
                <div class="finance-summary justify-content-end">
                    <div class="finance-item">
                        <small class="d-block opacity-75">Total Value (USD)</small>
                        <span class="h4 text-white font-weight-bold">${{ number_format($customer_order_details->sum(function($q){ return $q->total_amount / ($q->exchange_rate ?: 1); }), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger glass-card mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Main Form Column -->
        <div class="col-xl-12 col-lg-12 hideOnPrint">
            <div class="card glass-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="direction: rtl;">
                    <h5 class="mb-0">{{ $orderEdit ? 'ویرایش مشخصات تخنیکی قالین' : 'ثبت مشخصات تخنیکی قالین جدید' }}</h5>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#formCollapse">
                        <i class="fa fa-chevron-down"></i>
                    </button>
                </div>
                <div id="formCollapse" class="collapse show">
                    <div class="card-body">
                        <!-- Dari Explanation Block -->
                        <div class="alert alert-primary mb-4" style="direction: rtl; text-align: right; border-radius: 12px; background: #e3f2fd; border: 1px solid #bbdefb;">
                            <h5 class="text-primary mb-2"><i class="fa fa-cogs mr-2"></i> مشخصات تخنیکی و تاثیر بر سیستم‌های گدام و حسابداری</h5>
                            <p class="mb-2">این صفحه برای ثبت دقیق هر قالین و مدیریت چرخه تولید آن تا فروش نهایی طراحی شده است.</p>
                            <div class="row small">
                                <div class="col-md-6 border-left">
                                    <strong>۱. تاثیر بر گدام (Warehouse):</strong><br>
                                    وقتی آیکون <i class="fa fa-download text-warning"></i> (رسید به گدام) را فشار می‌دهید، سیستم به صورت خودکار یک بارکد برای این قالین صادر کرده و آن را در گدام انتخابی شما ثبت می‌کند.
                                </div>
                                <div class="col-md-6">
                                    <strong>۲. تاثیر بر حسابداری (Accounting):</strong><br>
                                    با هر مرحله (تولید یا فروش)، سیستم به صورت خودکار در دفتر کل (Ledger) سند می‌زند. دکمه <i class="fa fa-truck text-primary"></i> (تسلیمی) باعث کسر موجودی از گدام و ثبت عاید در حساب مالی مشتری می‌شود.
                                </div>
                            </div>
                            <p class="mt-2 mb-0 small text-muted"><strong>نکته:</strong> برای مشاهده تمام اسناد مالی صادر شده برای هر قالین، از آیکون <i class="fa fa-list-alt"></i> (دفتر تفصیلی) استفاده کنید.</p>
                        </div>
                                            <div class="row" style="direction: rtl; text-align: right;">
                                <!-- Technical Specs -->
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">کیفیت (Quality) <span class="text-danger">*</span></label>
                                    <input type="text" name="quality" class="form-control form-control-sm" value="{{ optional($orderEdit)->quality ?? '' }}" required>
                                    <small class="text-muted small">نوع گره، رج یا صنف قالین را وارد کنید.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">طول (متر) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="height" id="height" class="form-control form-control-sm" value="{{ optional($orderEdit)->height ?? '' }}" required>
                                    <small class="text-muted small">طول قالین به متر.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">عرض (متر) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="width" id="width" class="form-control form-control-sm" value="{{ optional($orderEdit)->width ?? '' }}" required>
                                    <small class="text-muted small">عرض قالین به متر.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">مساحت (m²)</label>
                                    <input type="text" name="area" id="area" class="form-control form-control-sm bg-light" value="{{ optional($orderEdit)->area ?? '' }}" readonly>
                                    <small class="text-muted small">محاسبه خودکار.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">تار (Warp)</label>
                                    <input type="text" name="warp" class="form-control form-control-sm" value="{{ optional($orderEdit)->warp ?? '' }}">
                                    <small class="text-muted small">جنس تار.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">پود (Weft)</label>
                                    <input type="text" name="weft" class="form-control form-control-sm" value="{{ optional($orderEdit)->weft ?? '' }}">
                                    <small class="text-muted small">جنس پود.</small>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">نمبر بافنده</label>
                                    <input type="text" name="weaver_code" class="form-control form-control-sm" value="{{ optional($orderEdit)->weaver_code ?? '' }}">
                                    <small class="text-muted small">کد شناسایی بافنده.</small>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">وضعیت فعلی <span class="text-danger">*</span></label>
                                    <select name="current_status" class="form-control form-control-sm select2">
                                        @foreach(['Graphing' => 'نقشه‌کشی', 'Dyeing' => 'رنگ‌ریزی', 'On loom' => 'روی دستگاه', 'Off loom' => 'ختم بافت', 'Washing' => 'شستشو', 'Finishing' => 'پرداخت', 'Repairing' => 'ترمیم', 'Ready' => 'آماده', 'Shipped' => 'تسلیم شده', 'Paused' => 'متوقف', 'Cancelled' => 'لغو شده'] as $val => $label)
                                            <option value="{{ $val }}" {{ (is_object($orderEdit) && $orderEdit->current_status == $val) ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted small">مرحله تولید قالین.</small>
                                </div>

                                <!-- Finance Fields -->
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">واحد پولی <span class="text-danger">*</span></label>
                                    <select name="currency_code" id="currency_code" class="form-control form-control-sm select2" required>
                                        <option value="USD" {{ (optional($orderEdit)->currency_code == 'USD') ? 'selected' : '' }}>USD (دالر)</option>
                                        <option value="AFN" {{ (optional($orderEdit)->currency_code == 'AFN') ? 'selected' : '' }}>AFN (افغانی)</option>
                                    </select>
                                    <small class="text-muted small">ارز مورد معامله.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">نرخ ارز</label>
                                    <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" class="form-control form-control-sm" value="{{ optional($orderEdit)->exchange_rate ?? '1' }}" required>
                                    <small class="text-muted small">نرخ تبدیل به پول پایه.</small>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="small font-weight-bold">قیمت فی متر</label>
                                    <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control form-control-sm" value="{{ optional($orderEdit)->unit_price ?? '' }}">
                                    <small class="text-muted small">قیمت فروش فی متر مربع.</small>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">مجموع مبلغ</label>
                                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control form-control-sm bg-light" value="{{ optional($orderEdit)->total_amount ?? '' }}" readonly>
                                    <small class="text-muted small">محاسبه خودکار کل مبلغ.</small>
                                </div>

                                <!-- Logistics -->
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">تاریخ شروع <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ optional($orderEdit)->start_date ?? date('Y-m-d') }}" required>
                                    <small class="text-muted small">شروع بافت.</small>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">تاریخ ختم (تخمینی)</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ optional($orderEdit)->end_date ?? '' }}">
                                    <small class="text-muted small">تاریخ تسلیمی احتمالی.</small>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="small font-weight-bold">عکس قالین</label>
                                    <input type="file" name="photo" class="form-control form-control-sm">
                                    <small class="text-muted small">نقشه یا عکس نمونه.</small>
                                </div>

                                <div class="col-md-2 mb-3 align-self-end">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-save"></i> {{ $orderEdit ? 'بروزرسانی مشخصات' : 'ثبت مشخصات قالین' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details List Column -->
        <div class="col-xl-12 col-lg-12">
            <div class="card glass-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Carpets in this Order</h5>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-light" onclick="window.print()"><i class="fa fa-print"></i> Print Report</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-xs mb-0">
                            <thead>
                                <tr style="direction: rtl; text-align: right;">
                                    <th class="pr-4">مشخصات تخنیکی قالین</th>
                                    <th>وضعیت تولید</th>
                                    <th>اطلاعات مالی</th>
                                    <th>زمان‌بندی</th>
                                    <th class="text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer_order_details as $co)
                                <tr>
                                    <td class="pl-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="mr-3" data-toggle="modal" data-target="#imageModal" data-src="/{{$co->photo}}">
                                                <img src="/{{$co->photo}}" class="rounded" style="height: 45px; width: 45px; object-fit: cover; border: 1px solid #eee;" onerror="this.src='/uploads/placeholder.png'">
                                            </a>
                                            <div>
                                                <span class="d-block font-weight-bold">{{ $co->quality }} ({{ $co->height }}x{{ $co->width }})</span>
                                                <small class="text-muted">Area: {{ $co->area }} m² | Warp/Weft: {{ $co->warp }}/{{ $co->weft }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $co->current_status)) }}">
                                            {{ $co->current_status }}
                                        </span>
                                        @if($co->carpet_id)
                                            <br><small class="text-info font-weight-bold">Barcode: {{ \App\Carpet::find($co->carpet_id)->carpet_no ?? 'LINKED' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="d-block font-weight-bold text-dark">{{ number_format($co->total_amount, 2) }} {{ $co->currency_code }}</span>
                                        <small class="text-muted">{{ number_format($co->unit_price, 2) }}/m² (Rate: {{ $co->exchange_rate }})</small>
                                    </td>
                                    <td>
                                        <small class="d-block text-success">Start: {{ $co->start_date }}</small>
                                        <small class="d-block text-danger">Due: {{ $co->end_date }}</small>
                                    </td>
                                    <td class="text-right pr-4">
                                        <div class="btn-group">
                                            <!-- Basic Actions -->
                                            <a href="/dashboard/customer-order-details/{{$co->cod_id}}/edit" class="btn btn-light action-btn text-info" title="Edit Specs">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <!-- ERP Integration Actions -->
                                            @if(!$co->carpet_id)
                                                <button class="btn btn-light action-btn text-warning" title="Receive into Stock" onclick="openReceiveModal({{$co->cod_id}})">
                                                    <i class="fa fa-download"></i>
                                                </button>
                                            @elseif($co->current_status != 'Shipped')
                                                <button class="btn btn-light action-btn text-primary" title="Complete Sale & Deliver" onclick="processSale({{$co->cod_id}})">
                                                    <i class="fa fa-truck"></i>
                                                </button>
                                            @endif

                                            @if($co->carpet_id)
                                                <a href="/dashboard/accounting/reports/account-ledger?source_id={{$co->carpet_id}}&source_type=App\Carpet" class="btn btn-light action-btn text-secondary" title="View Ledger">
                                                    <i class="fa fa-list-alt"></i>
                                                </a>
                                            @endif

                                            <button class="btn btn-light action-btn text-danger" title="Delete" onclick="deleteOrder({{$co->cod_id}})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No specifications added yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Large Image -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-body p-0 text-center">
                <img src="" id="modalImg" class="img-fluid rounded" style="max-height: 85vh;">
            </div>
        </div>
    </div>
</div>

<!-- Modal: Receive into Stock -->
<div class="modal fade" id="receiveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <form id="receiveForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Receive Carpet into Stock</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="receive_cod_id">
                    <div class="form-group">
                        <label>Barcode / Parcha Number</label>
                        <input type="text" name="carpet_no" class="form-control" placeholder="e.g. PN-2024-001" required>
                    </div>
                    <div class="form-group">
                        <label>Warehouse Location</label>
                        <select name="warehouse_id" class="form-control">
                            @foreach(\App\Warehouse::all() as $wh)
                                <option value="{{$wh->id}}">{{$wh->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Reception</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Real-time area and total calculation
    function calculateTotals() {
        let h = parseFloat($('#height').val()) || 0;
        let w = parseFloat($('#width').val()) || 0;
        let area = h * w;
        $('#area').val(area.toFixed(2));

        let up = parseFloat($('#unit_price').val()) || 0;
        $('#total_amount').val((area * up).toFixed(2));
    }

    $("#height, #width, #unit_price").on("keyup change", calculateTotals);

    // Auto-fill exchange rate based on currency selection
    $('#currency_code').on('change', function() {
        let cur = $(this).val();
        if(cur == 'USD') {
            $('#exchange_rate').val(1);
        } else if(cur == 'AFN') {
            $('#exchange_rate').val(77); // Default rate
        }
    });

    // Image Modal
    $('#imageModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let src = button.data('src');
        $('#modalImg').attr('src', src);
    });

    // Delete Logic
    function deleteOrder(id) {
        swal({
            title: "Are you sure?",
            text: "This will also reverse any linked accounting entries!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/customer-order-details/' + id,
                    data: { _token: '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            location.reload();
                        } else {
                            swal("Error", res.message || "Operation failed", "error");
                        }
                    }
                });
            }
        });
    }

    // Receive Logic
    function openReceiveModal(id) {
        $('#receive_cod_id').val(id);
        $('#receiveModal').modal('show');
    }

    $('#receiveForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#receive_cod_id').val();
        $.ajax({
            type: 'POST',
            url: '/dashboard/customer-order-details/' + id + '/receive',
            data: $(this).serialize(),
            success: function(res) {
                if(res.status == 'success') {
                    swal("Success", res.message, "success").then(() => location.reload());
                } else {
                    swal("Operation Failed", res.message || "An unknown error occurred", "error");
                }
            },
            error: function(xhr) {
                let msg = "Server Error (500)";
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                swal("System Error", msg, "error");
            }
        });
    });

    // Final Sale Logic
    function processSale(id) {
        swal({
            title: "Process Final Sale?",
            text: "This will record revenue, COGS, and mark the order as delivered.",
            icon: "info",
            buttons: ["Cancel", "Yes, Shipped"],
        }).then((confirm) => {
            if (confirm) {
                $.ajax({
                    type: 'POST',
                    url: '/dashboard/customer-order-details/' + id + '/sell',
                    data: { _token: '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("Shipped!", res.message, "success").then(() => location.reload());
                        } else {
                            swal("Operation Failed", res.message || "Could not process sale", "error");
                        }
                    },
                    error: function(xhr) {
                        let msg = "Server Error (500)";
                        if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        swal("System Error", msg, "error");
                    }
                });
            }
        });
    }

    $('.select2').select2({ width: '100%' });
</script>
@endsection
