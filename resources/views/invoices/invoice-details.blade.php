@extends('dsh.master')
@section('title' , 'جزئیات انوایس')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Action Header -->
    <div class="row mb-4 hideOnPrint">
        <div class="col-md-6 text-right d-flex align-items-center">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-file-text-o text-primary mr-2"></i> جزئیات انوایس #{{$invoice->invoice_no}}</h3>
            @if($invoice->status === 'closed')
                <span class="badge badge-danger ml-3 font-weight-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                    <i class="fa fa-lock mr-1"></i> بسته شده (Closed)
                </span>
            @else
                <span class="badge badge-success ml-3 font-weight-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                    <i class="fa fa-unlock-alt mr-1"></i> باز (Open)
                </span>
            @endif
        </div>
        <div class="col-md-6 text-left d-flex align-items-center justify-content-end">
            @if($invoice->status === 'open')
                <form action="/dashboard/invoices/{{$invoice->id}}/close" method="POST" id="close-invoice-form" class="d-inline-block">
                    @csrf
                    <button type="button" onclick="confirmCloseInvoice()" class="btn btn-danger shadow-sm px-4 font-weight-bold ml-2">
                        <i class="fa fa-lock mr-2"></i> بستن انوایس
                    </button>
                </form>
            @endif
            <button class="btn btn-primary shadow-sm px-4 font-weight-bold" onclick="printPage('premiumInvoice')">
                <i class="fa fa-print mr-2"></i> چاپ انوایس
            </button>
            <a href="/dashboard/invoices" class="btn btn-light shadow-sm px-4 ml-2">بازگشت به لیست</a>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden" id="premiumInvoice">
        <!-- Invoice Header (Visible in Print) -->
        <div class="card-header bg-white border-0 p-5">
            <div class="row align-items-center">
                <div class="col-6">
                    <h1 class="font-weight-bold text-primary mb-0" style="letter-spacing: 2px;">INVOICE</h1>
                    <p class="text-muted small">نمبر انوایس: <span class="text-dark font-weight-bold">#{{$invoice->invoice_no}}</span></p>
                </div>
                <div class="col-6 text-left">
                    <!-- Placeholder for Company Logo -->
                    <h3 class="font-weight-bold text-dark mb-0">QBIC ERP SYSTEM</h3>
                    <p class="text-muted small mb-0">کابل، افغانستان</p>
                    <p class="text-muted tiny">تاریخ صدور: {{ $invoice->invoice_date }}</p>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-6 text-right">
                    @if($invoice->type === 'carpet')
                        <p class="text-muted small text-uppercase font-weight-bold mb-2">صورتحساب برای:</p>
                        <h5 class="font-weight-bold text-dark mb-1">{{$invoice->customer->name}}</h5>
                        <p class="text-muted small mb-0">{{$invoice->customer->company_name}}</p>
                        <p class="text-muted small mb-0">{{$invoice->customer->company_address}}</p>
                        <p class="text-muted small mb-0"><i class="fa fa-phone mr-1"></i> {{$invoice->customer->phone}}</p>
                    @else
                        <p class="text-muted small text-uppercase font-weight-bold mb-2">نماینده خریدار:</p>
                        <h5 class="font-weight-bold text-dark mb-1">{{$invoice->agent->user->name ?? $invoice->agent->name ?? '---'}}</h5>
                        <p class="text-muted small mb-0"><i class="fa fa-phone mr-1"></i> {{$invoice->agent->phone ?? ''}}</p>
                    @endif
                </div>
                <div class="col-6 text-left" style="direction: ltr;">
                    <p class="text-muted small text-uppercase font-weight-bold mb-2">Invoice Summary:</p>
                    @if($invoice->type === 'carpet')
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Quantity:</span>
                            <span class="text-dark font-weight-bold small">{{ $invoice->sale->where('is_returned', 0)->count() }} Pcs</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Area:</span>
                            <span class="text-dark font-weight-bold small">{{ round($invoice->sale->where('is_returned', 0)->sum('carpet.area'), 2) }} m²</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Quantity:</span>
                            <span class="text-dark font-weight-bold small">{{ number_format($invoice->material_sales->sum('amount'), 2) }} Kg</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @php
                if ($invoice->type === 'carpet') {
                    $totalPaid = $invoice->payments->sum('amount_applied');
                } else {
                    $totalPaid = $invoice->paid_amount;
                }
            @endphp
            @if($totalPaid > 0)
            <div class="alert alert-warning mb-0 border-0 rounded-0 text-right py-3 px-5 hideOnPrint" style="background-color: #fff3cd; color: #856404; font-size: 13px;">
                <i class="fa fa-exclamation-triangle mr-2"></i> <strong>توجه:</strong> این انوایس دارای پرداخت های ثبت شده به مبلغ <strong>${{ number_format($totalPaid, 2) }}</strong> می‌باشد. امکان برگشت قالین ها به گدام تا زمان حذف یا معکوس نمودن پرداخت ها قفل می‌باشد.
            </div>
            @endif
            @if($invoice->invoice_description)
            <div class="px-5 py-3 bg-light border-top border-bottom text-right">
                <p class="mb-0 text-muted small"><i class="fa fa-info-circle mr-1"></i> توضیحات: {{$invoice->invoice_description}}</p>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right">
                    <thead class="bg-primary text-white">
                        @if($invoice->type === 'carpet')
                        <tr>
                            <th class="border-0 px-4 py-3">نمبر قالین</th>
                            <th class="border-0 py-3">نوعیت و کیفیت</th>
                            <th class="border-0 py-3 text-center">ابعاد (m)</th>
                            <th class="border-0 py-3 text-center">مساحت (m²)</th>
                            <th class="border-0 py-3 text-center">قیمت واحد ($)</th>
                            <th class="border-0 py-3 text-center">قیمت کل ($)</th>
                            <th class="border-0 px-4 py-3 text-left hideOnPrint">عملیات</th>
                        </tr>
                        @else
                        <tr>
                            <th class="border-0 px-4 py-3">دسته بندی</th>
                            <th class="border-0 py-3">نوعیت مواد</th>
                            <th class="border-0 py-3 text-center">گدام</th>
                            <th class="border-0 py-3 text-center">مقدار (Kg)</th>
                            <th class="border-0 py-3 text-center">قیمت فی واحد</th>
                            <th class="border-0 py-3 text-center">قیمت کل</th>
                            <th class="border-0 py-3 text-center text-success">معادل (USD)</th>
                            <th class="border-0 px-4 py-3 text-left hideOnPrint">عملیات</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        @if($invoice->type === 'carpet')
                            @foreach($sales as $sale)
                            <tr class="border-bottom {{ $sale->is_returned ? 'text-muted bg-light' : '' }}">
                                <td class="px-4 py-3 font-weight-bold text-primary">
                                    @if($sale->is_returned)
                                        <del>{{$sale->carpet->carpet_no ?? $sale->carpet_no}}</del>
                                        <span class="badge badge-danger ml-2 font-weight-bold px-2 py-1 tiny" style="text-decoration: none !important; display: inline-block;">
                                            <i class="fa fa-reply mr-1"></i> مرجوع شده (Returned)
                                        </span>
                                    @else
                                        {{$sale->carpet->carpet_no ?? $sale->carpet_no}}
                                    @endif
                                </td>
                                <td>
                                    @if($sale->is_returned)
                                        <del class="text-dark small font-weight-bold">{{$sale->type}}</del><br>
                                        <del class="text-muted tiny">{{$sale->quality}}</del>
                                        <div class="tiny text-danger mt-1" style="text-decoration: none !important;">
                                            <i class="fa fa-calendar mr-1"></i> تاریخ برگشت: {{ $sale->returned_at }} <br>
                                            <i class="fa fa-user mr-1"></i> توسط: {{ \App\User::find($sale->returned_by)->name ?? 'ناشناس' }}
                                        </div>
                                    @else
                                        <span class="text-dark small font-weight-bold">{{$sale->type}}</span><br>
                                        <span class="text-muted tiny">{{$sale->quality}}</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    @if($sale->is_returned)
                                        <del>{{$sale->carpet_height ?? ($sale->carpet->height ?? '---')}} × {{$sale->carpet_width ?? ($sale->carpet->width ?? '---')}}</del>
                                    @else
                                        {{$sale->carpet_height ?? ($sale->carpet->height ?? '---')}} × {{$sale->carpet_width ?? ($sale->carpet->width ?? '---')}}
                                    @endif
                                </td>
                                <td class="text-center font-weight-bold">
                                    @if($sale->is_returned)
                                        <del>{{round($sale->carpet_area ?? ($sale->carpet->area ?? 0), 2)}}</del>
                                    @else
                                        {{round($sale->carpet_area ?? ($sale->carpet->area ?? 0), 2)}}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($sale->is_returned)
                                        <del>${{number_format($sale->sale_cost_per_meter, 2)}}</del>
                                    @else
                                        ${{number_format($sale->sale_cost_per_meter, 2)}}
                                    @endif
                                </td>
                                <td class="text-center font-weight-bold text-dark">
                                    @if($sale->is_returned)
                                        <del>${{number_format($sale->sale_cost_total, 2)}}</del>
                                    @else
                                        ${{number_format($sale->sale_cost_total, 2)}}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-left hideOnPrint">
                                    @if($invoice->status === 'closed')
                                        <span class="text-muted small font-weight-bold">
                                            <i class="fa fa-lock mr-1"></i> غیرقابل تغییر (بسته شده)
                                        </span>
                                    @elseif($sale->is_returned)
                                        <span class="text-muted small font-weight-bold">
                                            <i class="fa fa-check-circle text-muted mr-1"></i> برگشت شده به گدام
                                        </span>
                                    @else
                                        @php
                                            $totalPaid = $invoice->payments->sum('amount_applied');
                                        @endphp
                                        @if($totalPaid > 0)
                                            <button class="btn btn-soft-danger btn-sm rounded-pill px-3" disabled title="انوایس دارای پرداخت است. برای برگشت ابتدا پرداخت را حذف کنید.">
                                                <i class="fa fa-lock mr-1"></i> قفل شده (دارای پرداخت)
                                            </button>
                                        @else
                                            <button onclick="sendToStock({{$sale->carpet_id}})" class="btn btn-soft-danger btn-sm rounded-pill px-3">
                                                <i class="fa fa-undo mr-1"></i> بازگشت به گدام
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            @foreach($sales as $material)
                            <tr class="border-bottom">
                                <td class="px-4 py-3 font-weight-bold text-primary">{{ optional($material->category)->material_category }}</td>
                                <td>
                                    @if(optional($material)->subtype == 'dye')
                                        <span class="badge badge-danger">رنگ (Dye)</span>
                                    @else
                                        <span class="badge badge-success">تار (Yarn)</span>
                                    @endif
                                    <small class="d-block text-muted font-weight-bold">{{ optional($material->type)->material_type }}</small>
                                </td>
                                <td class="text-center"><span class="text-secondary font-weight-bold">{{ optional($material)->warehouse_id ? \App\Warehouse::find($material->warehouse_id)->name : '---' }}</span></td>
                                <td class="text-center font-weight-bold text-success">{{ $material->amount }} kg</td>
                                <td class="text-center">{{ number_format($material->price, 2) }} {{ $material->currency_code }}</td>
                                <td class="text-center font-weight-bold">{{ number_format($material->original_amount, 2) }} {{ $material->currency_code }}</td>
                                <td class="text-center font-weight-bold text-success">${{ number_format($material->base_currency_amount, 2) }}</td>
                                <td class="px-4 py-3 text-left hideOnPrint">
                                    @if($invoice->status === 'closed')
                                        <span class="text-muted small font-weight-bold">
                                            <i class="fa fa-lock mr-1"></i> غیرقابل تغییر (بسته شده)
                                        </span>
                                    @else
                                        @php
                                            $totalPaid = $invoice->payments->sum('amount');
                                        @endphp
                                        @if($totalPaid > 0)
                                            <button class="btn btn-soft-danger btn-sm rounded-pill px-3" disabled title="انوایس دارای پرداخت است. برای برگشت ابتدا پرداخت را حذف کنید.">
                                                <i class="fa fa-lock mr-1"></i> قفل شده (دارای پرداخت)
                                            </button>
                                        @else
                                            <button onclick="returnMaterialSale({{$material->id}})" class="btn btn-soft-danger btn-sm rounded-pill px-3">
                                                <i class="fa fa-undo mr-1"></i> بازگشت به گدام
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Footer -->
        <div class="card-footer bg-white border-0 p-5 mt-4">
            <div class="row">
                <div class="col-md-7 text-right">
                    <div class="p-3 rounded-lg border border-dashed text-muted small" style="border-style: dashed !important;">
                        <h6 class="font-weight-bold text-dark small mb-2">شرایط و ضوابط:</h6>
                        <ul class="mb-0 pr-3">
                            <li>این انوایس به صورت سیستمی تولید شده و معتبر می‌باشد.</li>
                            <li>در صورت هرگونه مغایرت، ظرف مدت ۲۴ ساعت اطلاع دهید.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5">
                    @php
                        if ($invoice->type === 'carpet') {
                            $totalPaid = $invoice->payments->sum('amount_applied');
                            $totalDue = $invoice->sale->where('is_returned', 0)->sum('sale_cost_total');
                        } else {
                            $totalPaid = $invoice->paid_amount;
                            $totalDue = $invoice->material_sales->sum('base_currency_amount');
                        }
                        $balance = $totalDue - $totalPaid;
                    @endphp
                    <div class="bg-light p-4 rounded-lg shadow-sm" style="direction: ltr;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Invoice Total:</span>
                            <span class="text-dark font-weight-bold">${{number_format($totalDue, 2)}}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Paid:</span>
                            <span class="text-success font-weight-bold">${{number_format($totalPaid, 2)}}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-top pt-2 mt-2">
                            <h5 class="text-primary font-weight-bold mb-0">BALANCE DUE:</h5>
                            <h5 class="{{ $balance > 0 ? 'text-danger' : 'text-primary' }} font-weight-bold mb-0">${{number_format($balance, 2)}}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAYMENT TRANSACTIONS / ALLOCATIONS HISTORY -->
            @if($invoice->type === 'carpet')
                @php($docPayments = $invoice->payments)
            @else
                @php($docPayments = $invoice->allocations)
            @endif

            @if($docPayments && $docPayments->count() > 0)
            <div class="row mt-4 pt-4 border-top text-right" style="margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px;">
                <div class="col-12">
                    <h5 class="font-weight-bold text-dark mb-3" style="font-size: 15px; margin-bottom: 15px;"><i class="fa fa-credit-card text-success mr-1"></i> تاریخچه تادیات و پرداخت‌های انوایس (Payment History)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle" style="font-size: 12px; width: 100%;">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>تاریخ پرداخت (Date)</th>
                                    <th class="text-right">سند/تفصیلات (Reference / Description)</th>
                                    <th>نوعیت پرداخت</th>
                                    <th>مقدار پرداختی ارز اصلی (Amount)</th>
                                    <th>نرخ تسعیر (FX Rate)</th>
                                    <th>معادل دالر (USD Amount)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($docPayments as $pay)
                                    @if($invoice->type === 'carpet')
                                        <tr>
                                            <td>{{ $pay->payment->date ?? '---' }}</td>
                                            <td class="text-right">{{ $pay->payment->description ?? 'بابت تصفیه انوایس قالین' }}</td>
                                            <td><span class="badge badge-success px-2 py-1">دریافت مشتری (Customer Inflow)</span></td>
                                            <td>{{ number_format($pay->amount_applied, 2) }} USD</td>
                                            <td>1.00000000</td>
                                            <td class="font-weight-bold text-success">${{ number_format($pay->amount_applied, 2) }}</td>
                                        </tr>
                                    @else
                                        @php($ap = $pay->agent_payment)
                                        @if($ap)
                                        <tr>
                                            <td>{{ $ap->date }}</td>
                                            <td class="text-right">
                                                <strong>سند #: {{ $ap->check_number }}</strong> - 
                                                {{ $ap->description }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $ap->type == 'رسید' ? 'badge-success' : 'badge-danger' }} px-2 py-1">
                                                    {{ $ap->type == 'رسید' ? 'رسید (Inflow)' : 'گرفت (Outflow)' }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($pay->allocated_amount, 2) }} {{ $ap->currency_code }}</td>
                                            <td style="direction: ltr;">{{ number_format($pay->exchange_rate, 8) }}</td>
                                            <td class="font-weight-bold text-success">${{ number_format($pay->base_allocated_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="row mt-5 pt-5 text-center">
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold">امضای تحویل دهنده</div>
                </div>
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold">مهر فروشگاه</div>
                </div>
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold">امضای مشتری</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #premiumInvoice { font-family: 'Inter', 'Outfit', sans-serif; }
    .btn-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: none; }
    .btn-soft-danger:hover { background-color: #dc3545; color: white; }
    .rounded-lg { border-radius: 1rem !important; }
    .tiny { font-size: 10px; }
    .opacity-5 { opacity: 0.5; }
    @media print {
        body { background: white !important; }
        .hideOnPrint { display: none !important; }
        .card { box-shadow: none !important; border: none !important; }
        .card-header { padding: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .bg-primary { background-color: #007bff !important; -webkit-print-color-adjust: exact; }
        .text-white { color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection

@section('scripts')
<script>
    function sendToStock(carpet_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این قالین از لیست فروش حذف شده و به گدام بازگشت داده خواهد شد. همچنین اسناد مالی مربوطه معکوس می‌گردد.",
            icon: "warning",
            buttons: {
                confirm: {text: 'بلی، بازگشت داده شود', className: 'btn-danger'},
                cancel: 'نخیر، انصراف'
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'GET',
                    url: '/dashboard/invoices/sent-to-stock/' + carpet_id,
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقیت!", "قالین با موفقیت به گدام بازگشت داده شد.", "success").then(() => {
                                location.reload();
                            });
                        } else {
                            swal("خطا!", "مشکلی در انجام عملیات رخ داد.", "error");
                        }
                    },
                    error: function() {
                        swal("خطا!", "خطای سیستمی رخ داد.", "error");
                    }
                });
            }
        });
    }

    function confirmCloseInvoice() {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "با بستن انوایس، دیگر قادر به افزودن یا ویرایش فروش در این انوایس نخواهید بود.",
            icon: "warning",
            buttons: {
                confirm: {text: 'بلی، بسته شود', className: 'btn-danger'},
                cancel: 'نخیر، انصراف'
            },
            dangerMode: true,
        }).then((willClose) => {
            if (willClose) {
                $('#close-invoice-form').submit();
            }
        });
    }

    function returnMaterialSale(sale_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این جنس از لیست فروش حذف شده و به گدام بازگشت داده خواهد شد. همچنین اسناد مالی مربوطه معکوس می‌گردد.",
            icon: "warning",
            buttons: {
                confirm: {text: 'بلی، بازگشت داده شود', className: 'btn-danger'},
                cancel: 'نخیر، انصراف'
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'POST',
                    url: '/dashboard/material-sales/' + sale_id,
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقیت!", "جنس با موفقیت به گدام بازگشت داده شد.", "success").then(() => {
                                location.reload();
                            });
                        } else {
                            swal("خطا!", "مشکلی در انجام عملیات رخ داد.", "error");
                        }
                    },
                    error: function() {
                        swal("خطا!", "خطای سیستمی رخ داد.", "error");
                    }
                });
            }
        });
    }
</script>
@endsection

