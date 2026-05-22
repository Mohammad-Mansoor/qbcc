@extends('dsh.master')
@section('title' , 'جزئیات انوایس')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Action Header -->
    <div class="row mb-4 hideOnPrint">
        <div class="col-md-6 text-right">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-file-text-o text-primary mr-2"></i> جزئیات انوایس #{{$invoice->invoice_no}}</h3>
        </div>
        <div class="col-md-6 text-left">
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
                    <p class="text-muted small text-uppercase font-weight-bold mb-2">صورتحساب برای:</p>
                    <h5 class="font-weight-bold text-dark mb-1">{{$invoice->customer->name}}</h5>
                    <p class="text-muted small mb-0">{{$invoice->customer->company_name}}</p>
                    <p class="text-muted small mb-0">{{$invoice->customer->company_address}}</p>
                    <p class="text-muted small mb-0"><i class="fa fa-phone mr-1"></i> {{$invoice->customer->phone}}</p>
                </div>
                <div class="col-6 text-left" style="direction: ltr;">
                    <p class="text-muted small text-uppercase font-weight-bold mb-2">Invoice Summary:</p>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Total Quantity:</span>
                        <span class="text-dark font-weight-bold small">{{ $invoice->sale->where('is_returned', 0)->count() }} Pcs</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Total Area:</span>
                        <span class="text-dark font-weight-bold small">{{ round($invoice->sale->where('is_returned', 0)->sum('carpet.area'), 2) }} m²</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @php
                $totalPaid = $invoice->payments->sum('amount');
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
                        <tr>
                            <th class="border-0 px-4 py-3">نمبر قالین</th>
                            <th class="border-0 py-3">نوعیت و کیفیت</th>
                            <th class="border-0 py-3 text-center">ابعاد (m)</th>
                            <th class="border-0 py-3 text-center">مساحت (m²)</th>
                            <th class="border-0 py-3 text-center">قیمت واحد ($)</th>
                            <th class="border-0 py-3 text-center">قیمت کل ($)</th>
                            <th class="border-0 px-4 py-3 text-left hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                @if($sale->is_returned)
                                    <span class="text-muted small font-weight-bold">
                                        <i class="fa fa-check-circle text-muted mr-1"></i> برگشت شده به گدام
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
                                        <button onclick="sendToStock({{$sale->carpet_id}})" class="btn btn-soft-danger btn-sm rounded-pill px-3">
                                            <i class="fa fa-undo mr-1"></i> بازگشت به گدام
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
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
                        $totalPaid = $invoice->payments->sum('amount');
                        $totalDue = $invoice->sale->where('is_returned', 0)->sum('sale_cost_total');
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
</script>
@endsection

