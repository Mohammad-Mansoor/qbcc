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
                @can('close_invoice')
                <form action="/dashboard/invoices/{{$invoice->id}}/close" method="POST" id="close-invoice-form" class="d-inline-block">
                    @csrf
                    <button type="button" onclick="confirmCloseInvoice()" class="btn btn-danger shadow-sm px-4 font-weight-bold ml-2">
                        <i class="fa fa-lock mr-2"></i> بستن انوایس
                    </button>
                </form>
                @endcan
            @endif
            @can('print_invoice')
            <a href="?export=pdf" class="btn btn-primary shadow-sm px-4 font-weight-bold" target="_blank">
                <i class="fa fa-file-pdf-o mr-2"></i> خروجی PDF (چاپ)
            </a>
            @endcan
            <a href="/dashboard/invoices" class="btn btn-light shadow-sm px-4 ml-2">بازگشت به لیست</a>
        </div>
    </div>

    <!-- Formal Invoice Card -->
    <div class="card border border-secondary shadow-lg rounded-0 mx-auto" style="max-width: 950px; background: #fff;" id="premiumInvoice">
        <!-- Top Colored Bar -->
        <div style="height: 10px; background: #0056b3; width: 100%;"></div>
        
        <!-- Invoice Header -->
        <div class="card-header bg-white border-0 p-5">
            <div class="row align-items-center mb-4 pb-4 border-bottom">
                <div class="col-6">
                    <img src="{{ asset(config('company.logo_path', 'images/logos/qasimi_logo.png')) }}" style="max-width: 250px; height: auto;" alt="Logo" onerror="this.style.display='none'">
                    <h3 class="font-weight-bold text-dark mt-3 mb-0">{{ config('company.name') }}</h3>
                    <p class="text-muted small mb-0">{{ config('company.description') }}</p>
                </div>
                <div class="col-6 text-left" style="direction: ltr;">
                    <h1 class="font-weight-bold mb-1" style="color: #0056b3; letter-spacing: 3px; font-size: 38px;">INVOICE</h1>
                    <p class="text-muted mb-0 font-weight-bold" style="font-size: 16px;">#{{$invoice->invoice_no}}</p>
                    <p class="text-muted small mt-2">Date: {{ $invoice->invoice_date }}</p>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-6 text-right">
                    <div class="p-4 bg-light border">
                        <h6 class="text-primary font-weight-bold text-uppercase mb-3" style="border-bottom: 2px solid #0056b3; padding-bottom: 5px; display: inline-block;">صورتحساب برای (Billed To):</h6>
                        @if($invoice->type === 'carpet')
                            <h5 class="font-weight-bold text-dark mb-2">{{$invoice->customer->name}}</h5>
                            <p class="text-muted small mb-1"><strong>شرکت:</strong> {{$invoice->customer->company_name ?? '---'}}</p>
                            <p class="text-muted small mb-1"><strong>آدرس:</strong> {{$invoice->customer->company_address ?? '---'}}</p>
                            <p class="text-muted small mb-0"><strong>تماس:</strong> <span dir="ltr">{{$invoice->customer->phone ?? '---'}}</span></p>
                        @else
                            <h5 class="font-weight-bold text-dark mb-2">{{$invoice->agent->user->name ?? $invoice->agent->name ?? '---'}}</h5>
                            <p class="text-muted small mb-1"><strong>شماره حساب:</strong> {{$invoice->agent->account_no ?? '---'}}</p>
                            @php
                                $agentPhone = '---';
                                if(isset($invoice->agent->phone) && is_iterable($invoice->agent->phone) && count($invoice->agent->phone) > 0) {
                                    $firstPhone = collect($invoice->agent->phone)->first();
                                    $agentPhone = $firstPhone['phone_no'] ?? $firstPhone->phone_no ?? '---';
                                } elseif (is_string($invoice->agent->phone)) {
                                    $agentPhone = $invoice->agent->phone;
                                }
                            @endphp
                            <p class="text-muted small mb-0"><strong>تماس:</strong> <span dir="ltr">{{$agentPhone}}</span></p>
                        @endif
                    </div>
                </div>
                <div class="col-6 text-left" style="direction: ltr;">
                    <div class="p-4 bg-light border text-right" style="direction: rtl;">
                        <h6 class="text-primary font-weight-bold text-uppercase mb-3" style="border-bottom: 2px solid #0056b3; padding-bottom: 5px; display: inline-block;">خلاصه انوایس (Summary):</h6>
                        @if($invoice->type === 'carpet')
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">مجموع قالین‌ها:</span>
                                <span class="text-dark font-weight-bold">{{ $invoice->sale->where('is_returned', 0)->count() }} تخته</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">مجموع مساحت:</span>
                                <span class="text-dark font-weight-bold" dir="ltr">{{ round($invoice->sale->where('is_returned', 0)->sum('carpet.area'), 2) }} m²</span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">مجموع مقدار:</span>
                                <span class="text-dark font-weight-bold" dir="ltr">{{ number_format($invoice->material_sales->sum('amount'), 2) }} Kg</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                            <span class="text-muted small">حالت سیستم:</span>
                            @if($invoice->status === 'closed')
                                <span class="text-danger font-weight-bold">بسته شده (Closed)</span>
                            @else
                                <span class="text-success font-weight-bold">باز (Open)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0 px-5">
            @php
                if ($invoice->type === 'carpet') {
                    $totalPaid = $invoice->payments->sum('amount_applied');
                } else {
                    $totalPaid = $invoice->paid_amount;
                }
            @endphp
            
            @if($totalPaid > 0)
            <div class="alert alert-warning mb-4 border rounded text-right py-3 px-4 hideOnPrint" style="background-color: #fff3cd; color: #856404; font-size: 13px;">
                <i class="fa fa-exclamation-triangle mr-2"></i> <strong>توجه:</strong> این انوایس دارای پرداخت های ثبت شده به مبلغ <strong dir="ltr">${{ number_format($totalPaid, 2) }}</strong> می‌باشد. امکان برگشت محصولات به گدام تا زمان حذف پرداخت ها قفل می‌باشد.
            </div>
            @endif
            
            @if($invoice->invoice_description)
            <div class="px-4 py-3 bg-light border mb-4 text-right rounded">
                <p class="mb-0 text-muted small"><strong class="text-dark">توضیحات انوایس:</strong> {{$invoice->invoice_description}}</p>
            </div>
            @endif

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0 text-center">
                    <thead style="background-color: #0056b3; color: white;">
                        @if($invoice->type === 'carpet')
                        <tr>
                            <th class="py-3">ردیف</th>
                            <th class="py-3">نمبر قالین</th>
                            <th class="py-3">نقشه و مشخصات</th>
                            <th class="py-3 text-center">ابعاد (m)</th>
                            <th class="py-3 text-center">مساحت (m²)</th>
                            <th class="py-3 text-center">قیمت واحد ($)</th>
                            <th class="py-3 text-center">قیمت کل ($)</th>
                            <th class="py-3 hideOnPrint">عملیات</th>
                        </tr>
                        @else
                        <tr>
                            <th class="py-3">ردیف</th>
                            <th class="py-3">دسته بندی</th>
                            <th class="py-3">نوعیت مواد</th>
                            <th class="py-3 text-center">مقدار (Kg)</th>
                            <th class="py-3 text-center">قیمت فی واحد</th>
                            <th class="py-3 text-center">قیمت کل</th>
                            <th class="py-3 text-center text-success">معادل (USD)</th>
                            <th class="py-3 hideOnPrint">عملیات</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        @php $counter = 1; @endphp
                        @if($invoice->type === 'carpet')
                            @foreach($sales as $sale)
                            <tr class="{{ $sale->is_returned ? 'text-muted bg-light' : '' }}">
                                <td class="py-3 font-weight-bold">{{ $counter++ }}</td>
                                <td class="py-3 font-weight-bold text-primary">
                                    @if($sale->is_returned)
                                        <del>{{$sale->carpet->carpet_no ?? $sale->carpet_no}}</del>
                                        <span class="d-block badge badge-danger mt-1">مرجوع شده</span>
                                    @else
                                        {{$sale->carpet->carpet_no ?? $sale->carpet_no}}
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    @if($sale->is_returned)
                                        <del class="text-dark font-weight-bold">{{$sale->type}} - {{$sale->carpet->map_number ?? '---'}}</del><br>
                                        <del class="text-muted" style="font-size: 11px;">کیفیت: {{$sale->quality}} | رنگ: {{$sale->carpet->field ?? '---'}}/{{$sale->carpet->margin ?? '---'}}</del>
                                    @else
                                        <span class="text-dark font-weight-bold">{{$sale->type}} - {{ $sale->carpet->map_number ?? '---' }}</span><br>
                                        <span class="text-muted" style="font-size: 11px;">کیفیت: {{$sale->quality}} | رنگ: {{$sale->carpet->field ?? '---'}}/{{$sale->carpet->margin ?? '---'}}</span>
                                    @endif
                                </td>
                                <td class="text-center small" dir="ltr">
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
                                <td class="py-3 hideOnPrint">
                                    @if($invoice->status === 'closed')
                                        <span class="text-muted small font-weight-bold"><i class="fa fa-lock"></i> قفل</span>
                                    @elseif($sale->is_returned)
                                        <span class="text-success small font-weight-bold"><i class="fa fa-check"></i> برگشت شد</span>
                                    @else
                                        @if($totalPaid > 0)
                                            <button class="btn btn-outline-secondary btn-sm" disabled title="دارای پرداخت"><i class="fa fa-lock"></i></button>
                                        @else
                                            @can('edit_invoice')
                                            <button onclick="sendToStock({{$sale->carpet_id}})" class="btn btn-outline-danger btn-sm" title="بازگشت به گدام"><i class="fa fa-undo"></i></button>
                                            @endcan
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            @foreach($sales as $material)
                            <tr>
                                <td class="py-3 font-weight-bold">{{ $counter++ }}</td>
                                <td class="py-3 font-weight-bold text-primary">{{ optional($material->category)->material_category }}</td>
                                <td class="py-3 text-right">
                                    <span class="text-dark font-weight-bold d-block">{{ optional($material->type)->material_type }}</span>
                                    <span class="badge {{ optional($material)->subtype == 'dye' ? 'badge-danger' : 'badge-success' }} mt-1">
                                        {{ optional($material)->subtype == 'dye' ? 'رنگ (Dye)' : 'تار (Yarn)' }}
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold text-success">{{ $material->amount }}</td>
                                <td class="text-center text-muted" dir="ltr">{{ number_format($material->price, 2) }} {{ $material->currency_code }}</td>
                                <td class="text-center font-weight-bold" dir="ltr">{{ number_format($material->original_amount, 2) }} {{ $material->currency_code }}</td>
                                <td class="text-center font-weight-bold text-dark" dir="ltr">${{ number_format($material->base_currency_amount, 2) }}</td>
                                <td class="py-3 hideOnPrint">
                                    @if($invoice->status === 'closed')
                                        <span class="text-muted small font-weight-bold"><i class="fa fa-lock"></i> قفل</span>
                                    @else
                                        @if($totalPaid > 0)
                                            <button class="btn btn-outline-secondary btn-sm" disabled><i class="fa fa-lock"></i></button>
                                        @else
                                            @can('edit_invoice')
                                            <button onclick="returnMaterialSale({{$material->id}})" class="btn btn-outline-danger btn-sm"><i class="fa fa-undo"></i></button>
                                            @endcan
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

