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

    {{-- ===================== KPI WIDGETS ===================== --}}
    <div class="row mb-4">
        {{-- Widget 1: Total RM Purchased --}}
        <div class="col-md-4">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #d32f2f 0%, #ef9a9a 100%);">
                <span class="text-uppercase small font-weight-bold">کل مواد خام خریداری شده (RM Purchased)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalPurchased, 2) }}</h3>
                <div class="d-flex justify-content-between small text-white-50 mt-2">
                    <span>{{ $rmPurchaseBills->count() }} بل خرید</span>
                    <span>{{ number_format($rmPurchaseBills->sum('total_qty'), 2) }} kg</span>
                </div>
            </div>
        </div>

        {{-- Widget 2: Total Cash Paid to Seller --}}
        <div class="col-md-4">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #1976d2 0%, #64b5f6 100%);">
                <span class="text-uppercase small font-weight-bold">پرداخت نقدی به فروشنده (Cash Paid Out)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalBaseSent, 2) }}</h3>
                <div class="d-flex justify-content-between small text-white-50 mt-2">
                    <span>رسید: $ {{ number_format($totalBaseReceived, 2) }}</span>
                    <span>گرفت: $ {{ number_format($totalBaseSent, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Widget 3: Net Balance --}}
        <div class="col-md-4">
            @php $netAbs = abs($netBalance); @endphp
            <div class="premium-card p-3 text-white shadow-sm"
                 style="background: {{ $netBalance > 0 ? 'linear-gradient(135deg, #f57c00 0%, #ffb74d 100%)' : 'linear-gradient(135deg, #388e3c 0%, #81c784 100%)' }};">
                <span class="text-uppercase small font-weight-bold">بیلانس خالص فروشنده (Net Balance)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($netAbs, 2) }}</h3>
                <span class="small text-white-50 mt-2 d-block">
                    {{ $netBalance > 0 ? 'بدهی ما به فروشنده (We Owe Seller)' : 'حساب تصفیه شده / اضافه پرداخت' }}
                </span>
            </div>
        </div>
    </div>
    {{-- ===================== END KPI WIDGETS ===================== --}}

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
                    <!-- Allocation Info Alert -->
                    <div class="alert alert-info shadow-sm border-0 mb-4" id="allocation_info_box" style="display: {{ $paymentEdit && $paymentEdit->allocations->count() > 0 ? 'block' : 'none' }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-link mr-2"></i>
                                <span>تخصیص معامله به سند: </span>
                                <strong id="allocation_doc_display">
                                    @if($paymentEdit && $paymentEdit->allocations->count() > 0)
                                        بل خرید مواد خام شماره {{ $paymentEdit->allocations->first()->purchase_bill->bill_number }} (مبلغ: ${{ number_format($paymentEdit->allocations->first()->purchase_bill->total_amount, 2) }})
                                    @endif
                                </strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-light" id="btn_cancel_allocation">لغو تخصیص (Cancel)</button>
                        </div>
                    </div>

                    <form action="{{ $paymentEdit ? '/dashboard/string-seller-payments/'.$paymentEdit->id : '/dashboard/string-seller-payments' }}" method="post" id="forensicSellerForm">
                        @csrf
                        @if($paymentEdit) @method('PUT') @endif
                        <input type="hidden" name="seller_id" value="{{$seller->id}}">
                        <input type="hidden" name="raw_material_purchase_bill_id" id="raw_material_purchase_bill_id" value="{{ $paymentEdit && $paymentEdit->allocations->count() > 0 ? $paymentEdit->allocations->first()->raw_material_purchase_bill_id : '' }}">

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

    {{-- ===================== TABBED LEDGER SECTION ===================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card">
                {{-- Tab Header --}}
                <div class="card-header-premium p-0 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #0d47a1 0%, #1a237e 100%);">
                    <ul class="nav nav-tabs border-0" id="sellerDetailTabs" role="tablist" style="padding: 10px 15px 0 15px;">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold text-white" id="cash-tab"
                               data-toggle="tab" href="#cash-ledger" role="tab"
                               style="background:transparent; border:none; border-bottom:3px solid #fff; padding:15px 20px;">
                                <i class="fa fa-money"></i> معاملات نقدی (Cash Ledger)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="bills-tab"
                               data-toggle="tab" href="#rm-purchase-bills" role="tab"
                               style="background:transparent; border:none; padding:15px 20px;">
                                <i class="fa fa-file-text"></i> بل‌های خرید مواد خام (RM-PB)
                                @if($rmPurchaseBills->count() > 0)
                                    <span class="badge badge-warning ml-1">{{ $rmPurchaseBills->count() }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                    <div id="exportButton" class="mr-3"></div>
                </div>

                <div class="card-body p-0 tab-content" id="sellerDetailTabsContent">

                    {{-- ===== TAB 1: Cash Ledger ===== --}}
                    <div class="tab-pane fade show active" id="cash-ledger" role="tabpanel">
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
                                        <td class="text-center font-weight-bold" style="color:#1a237e;">{{ $pa->currency_code ?: ($pa->amount > 0 ? 'USD' : 'AFN') }}</td>
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
                                                    <a href="/dashboard/string-seller-payments/{{$pa->id}}/edit" class="btn btn-sm btn-outline-primary">
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
                                        <th colspan="3" class="text-right">خلاصه {{ $code }}</th>
                                        <td colspan="2" class="text-success text-right"><b>رسید: {{ number_format($totals->total_received, 2) }}</b></td>
                                        <td colspan="2" class="text-danger text-right"><b>گرفت: {{ number_format($totals->total_sent, 2) }}</b></td>
                                        @php($balance = $totals->total_received - $totals->total_sent)
                                        <td colspan="2" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            بیلانس: {{ number_format(abs($balance), 2) }} {{ $code }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr style="background: #e8eaf6;">
                                        <th colspan="3" class="text-right" style="color:#1a237e;"><b>مجموع کل بیلانس (Base USD)</b></th>
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
                        <div class="p-3">
                            @if(!isset($all)) {{ $payments->links() }} @endif
                        </div>
                    </div>

                    {{-- ===== TAB 2: RM Purchase Bills ===== --}}
                    <div class="tab-pane fade" id="rm-purchase-bills" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>شماره بل (Bill No)</th>
                                        <th>تعداد فاکتورها</th>
                                        <th>کل کیلوگرام (KG)</th>
                                        <th>ارزش کل USD</th>
                                        <th>پرداخت شده (Paid)</th>
                                        <th>باقیمانده (Remaining)</th>
                                        <th>وضعیت بل</th>
                                        <th>وضعیت پرداخت</th>
                                        <th class="hideOnPrint">اقدام سریع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rmPurchaseBills as $bill)
                                    <tr class="text-right">
                                        <td class="font-weight-bold">{{ \Carbon\Carbon::parse($bill->date)->format('d M Y') }}</td>
                                        <td>
                                            <a href="/dashboard/raw-material-purchase-bills/{{ $bill->id }}"
                                               target="_blank" style="font-family:monospace; font-weight:700; color:#3b82f6; font-size:.9rem;">
                                                {{ $bill->bill_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <span style="background:#ede9fe; color:#7c3aed; padding:2px 10px; border-radius:12px; font-size:.8rem; font-weight:600;">
                                                {{ $bill->purchase_count }} خرید
                                            </span>
                                        </td>
                                        <td class="font-weight-bold" style="direction:ltr;">{{ number_format($bill->total_qty, 2) }} kg</td>
                                        <td class="font-weight-bold text-dark" style="direction:ltr;">$ {{ number_format($bill->total_amount, 2) }}</td>
                                        <td class="text-success" style="direction:ltr;">$ {{ number_format($bill->paid_amount, 2) }}</td>
                                        <td class="font-weight-bold text-danger" style="direction:ltr;">$ {{ number_format($bill->remaining_balance, 2) }}</td>
                                        <td>
                                            @if($bill->status == 'open')
                                                <span style="background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600;">
                                                    <i class="fa fa-unlock"></i> باز
                                                </span>
                                            @else
                                                <span style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600;">
                                                    <i class="fa fa-lock"></i> بسته
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $bill->payment_status === 'paid' ? 'bg-success text-white' : ($bill->payment_status === 'partially_paid' ? 'bg-info text-white' : 'bg-warning text-dark') }}">
                                                {{ $bill->payment_status === 'paid' ? 'تصفیه شده' : ($bill->payment_status === 'partially_paid' ? 'تادیه قسمتی' : 'پرداخت نشده') }}
                                            </span>
                                        </td>
                                        <td class="hideOnPrint">
                                            @if($bill->payment_status !== 'paid' && $bill->remaining_balance > 0.01)
                                            <button class="btn btn-sm btn-primary rm-quick-pay-btn"
                                                    data-id="{{ $bill->id }}"
                                                    data-no="{{ $bill->bill_number }}"
                                                    data-balance="{{ $bill->remaining_balance }}"
                                                    title="تادیه این بل">
                                                <i class="fa fa-credit-card"></i> تادیه بل
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>تصفیه کامل</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5" style="color:#94a3b8;">
                                            <i class="fa fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                            هیچ بل خرید مواد خامی ثبت نشده است.
                                            <a href="/dashboard/raw-material-purchase-bills/create" class="btn btn-sm btn-outline-primary mt-2 d-block w-50 mx-auto">
                                                <i class="fa fa-plus-circle"></i> ثبت بل جدید
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($rmPurchaseBills->count() > 0)
                                <tfoot class="bg-light">
                                    <tr style="background:#e8eaf6;">
                                        <th colspan="4" class="text-right" style="color:#1a237e;"><b>مجموع کل (Totals)</b></th>
                                        <th class="text-dark" style="direction:ltr;"><b>$ {{ number_format($totalPurchased, 2) }}</b></th>
                                        <th class="text-success" style="direction:ltr;"><b>$ {{ number_format($totalPaidPurchases, 2) }}</b></th>
                                        <th class="text-danger" style="direction:ltr;"><b>$ {{ number_format($totalPurchased - $totalPaidPurchases, 2) }}</b></th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>
        </div>
    </div>
    {{-- ===================== END TABBED LEDGER SECTION ===================== --}}
</div>{{-- end #seller-payment --}}

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#purchase_number').select2();
        $('#currency_id').select2();
        $('#override_debit_account_id').select2();
        $('#override_credit_account_id').select2();

        // ── TAB SWITCHING ──
        $('#sellerDetailTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
            $('#sellerDetailTabs a').removeClass('text-white').addClass('text-white-50')
                .css('border-bottom', 'none');
            $(this).removeClass('text-white-50').addClass('text-white')
                .css('border-bottom', '3px solid #fff');
        });

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

        // ── RM QUICK-PAY: Pre-fill form from Purchase Bill tab ──
        $('.rm-quick-pay-btn').on('click', function () {
            const billId     = $(this).data('id');
            const billNo     = $(this).data('no');
            const balance    = parseFloat($(this).data('balance')) || 0;

            // Switch to USD and set amount
            $('#currency_id option').filter(function() {
                return $(this).text().indexOf('USD') !== -1;
            }).prop('selected', true);
            $('#currency_id').trigger('change');

            $('#original_amount').val(balance.toFixed(4));

            $('select[name="type"]').val('گرفت').trigger('change');
            $('textarea[name="description"]').val(`بابت تادیه بل خرید مواد خام شماره ${billNo}`);

            // Set hidden field value
            $('#raw_material_purchase_bill_id').val(billId);

            $('#allocation_doc_display').text(`بل خرید مواد خام شماره ${billNo} (باقیمانده: $${balance.toFixed(2)})`);
            $('#allocation_info_box').fadeIn();

            updateUsdPreview();

            // Switch to cash tab & scroll up to form
            $('#cash-tab').tab('show');
            $('#sellerDetailTabs a').removeClass('text-white').addClass('text-white-50').css('border-bottom','none');
            $('#cash-tab').removeClass('text-white-50').addClass('text-white').css('border-bottom','3px solid #fff');

            $('html, body').animate({ scrollTop: $('#seller-payment').offset().top - 20 }, 600);
        });

        // Cancel Allocation
        $('#btn_cancel_allocation').on('click', function () {
            $('#raw_material_purchase_bill_id').val('');
            $('#allocation_info_box').fadeOut();
            $('#original_amount').val('');
            $('textarea[name="description"]').val('');
        });

        // ── EXPORT ──
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