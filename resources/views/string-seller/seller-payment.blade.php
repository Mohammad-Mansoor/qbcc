@extends('dsh.master')

@section('content')
<style>
    /* PREMIUM FORENSIC UI STYLES - INDIGO THEME */
    .premium-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card-header-premium {
        background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
        padding: 20px 25px;
        border: none;
    }
    .card-header-premium h5 {
        color: #ffffff;
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .form-section-title {
        color: #1a237e;
        font-weight: 700;
        border-bottom: 2px solid #e8eaf6;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .form-section-title i { margin-left: 10px; color: #3949ab; }
    
    .custom-input {
        border-radius: 10px;
        border: 2px solid #e8eaf6;
        padding: 12px 15px;
        transition: all 0.2s;
        height: auto;
    }
    .custom-input:focus {
        border-color: #3949ab;
        box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.1);
    }
    .field-label {
        font-weight: 600;
        color: #455a64;
        margin-bottom: 8px;
        display: block;
        font-size: 0.9rem;
    }
    
    /* Live Truth Preview Box */
    .truth-preview-box {
        background: #e8eaf6;
        border-right: 4px solid #3949ab;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .truth-label { font-size: 0.8rem; color: #303f9f; font-weight: 600; }
    .truth-value { font-size: 1.4rem; color: #1a237e; font-weight: 800; font-family: 'Courier New', monospace; }

    .btn-premium {
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    .btn-premium-indigo {
        background: #3949ab;
        color: white;
        border: none;
    }
    .btn-premium-indigo:hover {
        background: #1a237e;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
    }
    
    /* Table Styling */
    .premium-table thead th {
        background: #f8f9fa;
        color: #1a237e;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        border-top: none;
        padding: 15px;
    }
    .premium-table tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #455a64;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
</style>

<div class="container-fluid mt-4" id="seller-payment">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="font-weight-bold text-dark">
                <i class="fa fa-shopping-cart text-indigo" style="color:#1a237e"></i> 
                پرداخت به فروشندگان مواد (Supplier Payments)
            </h3>
            <p class="text-muted">مدیریت حسابات و تصفیه با تامین کنندگان مواد اولیه</p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <button class="btn btn-outline-indigo btn-sm" onclick="window.print()" style="border-color:#1a237e; color:#1a237e">
                    <i class="fa fa-print"></i> چاپ گزارش (Print)
                </button>
                <a href="/dashboard/seller-payments-all/{{$seller->id}}" class="btn btn-outline-info btn-sm">
                    <i class="fa fa-list"></i> نمایش همه (Show All)
                </a>
            </div>
        </div>
    </div>

    @if(session("status"))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa fa-check-circle mr-2"></i> {{session('status')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Profile & Form Section -->
    <div class="row">
        <!-- Seller Profile Card -->
        <div class="col-lg-3">
            <div class="premium-card text-center p-4">
                <div class="mb-3">
                    <div class="bg-indigo text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; background-color:#1a237e">
                        <i class="fa fa-truck"></i>
                    </div>
                </div>
                <h4 class="font-weight-bold mb-1">{{$seller->name}}</h4>
                <p class="text-indigo font-weight-bold mb-3" style="color:#1a237e">ACCOUNT #: {{$seller->id}}</p>
                <hr>
                <div class="text-right">
                    <p class="mb-1"><small class="text-muted">آدرس:</small><br><strong>{{$seller->address}}</strong></p>
                    <p class="mb-0"><small class="text-muted">تماس:</small><br><strong>{{$seller->phone}} <i class="fa fa-phone small"></i></strong></p>
                </div>
            </div>
        </div>

        <!-- Forensic Payment Form -->
        <div class="col-lg-9">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5><i class="fa fa-calculator mr-2"></i> {{ $paymentEdit ? 'ویرایش سند مالی (Edit Supplier Payment)' : 'ثبت سند مالی جدید (New Supplier Entry)' }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ $paymentEdit ? '/dashboard/string-seller-payments/'.$paymentEdit->id : '/dashboard/string-seller-payments' }}" method="post" id="forensicSellerForm">
                        @csrf
                        @if($paymentEdit) @method('PUT') @endif
                        <input type="hidden" name="seller_id" value="{{$seller->id}}">

                        <div class="row">
                            <!-- Column 1: Financials -->
                            <div class="col-lg-4 col-md-6 border-left">
                                <h6 class="form-section-title"><i class="fa fa-money"></i> جزئیات مالی (Financials)</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="field-label">مقدار پول (Amount)</label>
                                    <input type="number" step="0.0001" name="amount" id="original_amount" class="form-control custom-input font-weight-bold" 
                                           value="{{ $paymentEdit ? ($paymentEdit->original_amount ?: ($paymentEdit->amount ?: $paymentEdit->amount_af)) : old('amount') }}" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">واحد پولی (Currency)</label>
                                    <select name="currency_id" id="currency_id" class="form-control custom-input font-weight-bold" required>
                                        @foreach($currencies as $curr)
                                            <option value="{{$curr->id}}" data-rate="{{$curr->exchange_rate}}" data-symbol="{{$curr->symbol}}"
                                                {{ ($paymentEdit && $paymentEdit->currency_code == $curr->code) || (!$paymentEdit && $curr->code == 'USD') ? 'selected' : '' }}>
                                                {{$curr->name}} ({{$curr->code}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Live Truth Preview Box -->
                                <div class="truth-preview-box mb-4">
                                    <span class="truth-label"><i class="fa fa-shield"></i> معادل دالر (USD Equivalent):</span><br>
                                    <span class="truth-value" id="usd_truth_preview">$ 0.0000</span>
                                    <input type="hidden" name="exchange_rate" id="current_rate_snapshot">
                                </div>
                            </div>

                            <!-- Column 2: Transaction Details -->
                            <div class="col-lg-4 col-md-6 border-left">
                                <h6 class="form-section-title"><i class="fa fa-exchange"></i> نوع و مرجع (Ref & Type)</h6>

                                <div class="form-group mb-4">
                                    <label class="field-label">نوع معامله (Entry Type)</label>
                                    <select name="type" class="form-control custom-input font-weight-bold">
                                        <option value="رسید" class="text-success" {{ ($paymentEdit && $paymentEdit->type == 'رسید') ? 'selected' : '' }}>رسید / تصفیه (Balance In)</option>
                                        <option value="گرفت" class="text-danger" {{ ($paymentEdit && $paymentEdit->type == 'گرفت') ? 'selected' : '' }}>گرفت / پرداخت (Payment Out)</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">فاکتور خرید (Purchase Ref)</label>
                                    <select name="purchase_number" id="purchase_number" class="form-control custom-input">
                                        <option value="نقد">نقد (Cash/General)</option>
                                        @foreach($purchase_numbers as $ch)
                                            <option value="{{$ch->purchase_number}}" {{ ($paymentEdit && $paymentEdit->purchase_number == $ch->purchase_number) ? 'selected' : '' }}>{{$ch->purchase_number}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">تاریخ (Date)</label>
                                    <input type="date" name="date" class="form-control custom-input" value="{{ $paymentEdit ? $paymentEdit->date : date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Column 3: Accounting & Audit -->
                            <div class="col-lg-4">
                                <h6 class="form-section-title"><i class="fa fa-university"></i> تنظیمات حسابی (Accounting)</h6>
                                
                                <div class="form-group mb-3">
                                    <label class="field-label small">حساب بدهکار (Debit)</label>
                                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control custom-input">
                                        @foreach($allowedDebitAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) || ($paymentEdit && $paymentEdit->override_debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="field-label small">حساب بستانکار (Credit)</label>
                                    <select name="override_credit_account_id" id="override_credit_account_id" class="form-control custom-input">
                                        @foreach($allowedCreditAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) || ($paymentEdit && $paymentEdit->override_credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">توضیحات (Description)</label>
                                    <textarea name="description" rows="2" class="form-control custom-input" required>{{ $paymentEdit ? $paymentEdit->description : '' }}</textarea>
                                </div>

                                <button class="btn btn-premium btn-premium-indigo btn-block shadow-sm" type="submit">
                                    <i class="fa fa-save"></i> {{ $paymentEdit ? 'بروزرسانی سند (Update)' : 'ثبت نهایی (Confirm)' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card">
                <div class="card-header-premium d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d47a1 0%, #1a237e 100%);">
                    <h5><i class="fa fa-list-alt mr-2"></i> ریز معاملات و پرداخت ها (Supplier Ledger)</h5>
                    <div id="exportButton"></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table premium-table table-hover mb-0" id="seller_payment_table">
                            <thead>
                                <tr class="text-right">
                                    <th>تاریخ (Date)</th>
                                    <th>شرح (Description)</th>
                                    <th>فاکتور (Purchase Ref)</th>
                                    <th>ارز (CCY)</th>
                                    <th>مقدار اصلی (Amount)</th>
                                    <th>نرخ (Rate)</th>
                                    <th>معادل دالر (USD)</th>
                                    <th>حالت (Status)</th>
                                    <th class="hideOnPrint">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $pa)
                                <tr class="text-right ur{{$pa->id}}">
                                    <td class="font-weight-bold">{{ $pa->date }}</td>
                                    <td>{{ $pa->description }}</td>
                                    <td>
                                        <span class="badge badge-light border p-2">
                                            @if($pa->purchase_number == 'نقد') نقد @else {{$pa->purchase_number}} @endif
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold text-indigo">{{ $pa->currency_code ?: ($pa->amount > 0 ? 'USD' : 'AFN') }}</td>
                                    <td class="font-weight-bold" style="direction: ltr;">
                                        {{ number_format($pa->original_amount ?: ($pa->amount ?: $pa->amount_af), 2) }}
                                    </td>
                                    <td class="text-muted small" style="direction: ltr;">{{ number_format($pa->exchange_rate ?: ($pa->dollar_rate ?: 1), 8) }}</td>
                                    <td class="font-weight-bold text-dark" style="direction: ltr;">
                                        $ {{ number_format($pa->base_amount ?: ($pa->amount ?: 0), 2) }}
                                    </td>
                                    <td>
                                        @if($pa->status == 0)
                                            <span class="status-badge bg-warning text-dark">انتظار تایید</span>
                                        @else
                                            <span class="status-badge bg-success text-white">تایید شده</span>
                                        @endif
                                    </td>
                                    <td class="hideOnPrint text-center">
                                        @if($pa->status == 0 || auth()->user()->role == 'SP')
                                            <div class="btn-group">
                                                <a href="/dashboard/string-seller-payments/{{$pa->id}}/edit" class="btn btn-sm btn-outline-indigo">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                
                                                @php
                                                    $transaction = \App\LedgerTransaction::where('source_type', 'seller_payment')->where('source_id', $pa->id)->first();
                                                @endphp
                                                @if($transaction)
                                                    <a href="{{ route('accounting.journals.show', $transaction->id) }}" target="_blank" class="btn btn-sm btn-outline-success" title="روزنامچه مالی">
                                                        <i class="fa fa-book"></i>
                                                    </a>
                                                @endif

                                                <button onclick="deletePayment({{$pa->id}}, {{$pa->seller_id}})" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                @foreach($currencyTotals as $code => $totals)
                                <tr>
                                    <th colspan="3" class="text-right">خلاصه {{ $code }} ({{ $code }} Summary)</th>
                                    <td colspan="2" class="text-success text-right"><b>رسید: {{ number_format($totals->total_received, 2) }}</b></td>
                                    <td colspan="2" class="text-danger text-right"><b>گرفت: {{ number_format($totals->total_sent, 2) }}</b></td>
                                    @php($balance = $totals->total_received - $totals->total_sent)
                                    <td colspan="2" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        بیلانس: {{ number_format(abs($balance), 2) }} {{ $code }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr style="background: #e8eaf6;">
                                    <th colspan="3" class="text-right text-indigo"><b>مجموع کل بیلانس (Base USD)</b></th>
                                    <td colspan="2" class="text-success text-right"><b>$ {{ number_format($totalBaseReceived, 2) }}</b></td>
                                    <td colspan="2" class="text-danger text-right"><b>$ {{ number_format($totalBaseSent, 2) }}</b></td>
                                    @php($baseBalance = $totalBaseReceived - $totalBaseSent)
                                    <td colspan="2" class="text-center font-weight-bold {{ $baseBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                                        $ {{ number_format(abs($baseBalance), 2) }}
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
<script>
    $(document).ready(function () {
        $('#purchase_number').select2();
        $('#currency_id').select2();
        $('#override_debit_account_id').select2();
        $('#override_credit_account_id').select2();

        // LIVE TRUTH PREVIEW LOGIC
        function updateUsdPreview() {
            const amount = parseFloat($('#original_amount').val()) || 0;
            const selectedCurrency = $('#currency_id option:selected');
            const rate = parseFloat(selectedCurrency.data('rate')) || 0;
            const baseAmount = (amount * rate).toFixed(4);
            
            $('#usd_truth_preview').text('$ ' + parseFloat(baseAmount).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
            $('#current_rate_snapshot').val(rate);
        }

        $('#original_amount, #currency_id').on('input change', updateUsdPreview);
        updateUsdPreview();

        // Export
        $("#seller_payment_table").tableExport({
            formats: ["xlsx"],
            filename: "supplier_ledger_{{ $seller->id }}",
            bootstrap: true,
            position: "bottom"
        });
        var $buttons = $('#seller_payment_table').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });

    function deletePayment(id, seller_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این سند و تراکنش مالی آن حذف خواهد شد!",
            icon: "warning",
            buttons: {
                cancel: "نخیر",
                confirm: { text: "بلی، حذف شود", className: "btn-danger" }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/string-seller-payments/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" });
                            setTimeout(() => window.location = '/dashboard/string-seller-payments/' + seller_id, 1000);
                        } else {
                            swal("خطا در حذف!", { icon: "error" });
                        }
                    }
                });
            }
        });
    }
</script>
@endsection