@extends('dsh.master')

@section('content')
<style>
    /* PREMIUM FORENSIC UI STYLES */
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
    .field-explanation {
        font-size: 0.75rem;
        color: #78909c;
        margin-top: 4px;
        display: block;
    }
    
    /* Live Truth Preview Box */
    .truth-preview-box {
        background: #f1f3f9;
        border-right: 4px solid #3949ab;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .truth-label { font-size: 0.8rem; color: #5c6bc0; font-weight: 600; }
    .truth-value { font-size: 1.4rem; color: #1a237e; font-weight: 800; font-family: 'Courier New', monospace; }

    .btn-premium {
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    .btn-premium-primary {
        background: #3949ab;
        color: white;
        border: none;
    }
    .btn-premium-primary:hover {
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

<div class="container-fluid mt-4" id="agent-payment">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="font-weight-bold text-dark">
                <i class="fa fa-user-secret text-primary"></i> 
                پرداخت های نماینده (Agent Payments & Settlements)
            </h3>
            <p class="text-muted">مدیریت معاملات مالی، انوایس‌های فروش، بل‌های خرید قالین و اسناد پرداخت نمایندگان</p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="fa fa-print"></i> چاپ گزارش (Print)
                </button>
                <a href="/dashboard/agent-payments-all/{{$agent->agent_id}}" class="btn btn-outline-info btn-sm">
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
    @if(session("error"))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa fa-exclamation-circle mr-2"></i> {{session('error')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Forensic Balance Sheet Header Widgets -->
    <div class="row mb-4">
        <!-- Card 1: Unpaid Carpet Bills -->
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #d32f2f 0%, #ff6b6b 100%);">
                <span class="text-uppercase small font-weight-bold">کل بل‌های خریده شده (Carpet Purchases)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalOwedPurchases, 2) }}</h3>
                <div class="d-flex justify-content-between small text-white-50 mt-2">
                    <span>پرداخت شده: $ {{ number_format($totalPaidPurchases, 2) }}</span>
                    <span>باقیمانده: $ {{ number_format($totalOwedPurchases - $totalPaidPurchases, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Sales Invoices (Yarn / Dye) -->
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #388e3c 0%, #81c784 100%);">
                <span class="text-uppercase small font-weight-bold">کل فروشات به نماینده (Material Sales)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalReceivableSales, 2) }}</h3>
                <div class="d-flex justify-content-between small text-white-50 mt-2">
                    <span>دریافت شده: $ {{ number_format($totalReceivedSales, 2) }}</span>
                    <span>باقیمانده: $ {{ number_format($totalReceivableSales - $totalReceivedSales, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Cash Transactions (Net Ledger) -->
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #1976d2 0%, #64b5f6 100%);">
                <span class="text-uppercase small font-weight-bold">پرداختی نقدی ما (Cash Paid to Agent)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalBaseSent, 2) }}</h3>
                <span class="small text-white-50 mt-2 d-block">مجموعه نقد دریافت شده: $ {{ number_format($totalBaseReceived, 2) }}</span>
            </div>
        </div>

        <!-- Card 4: Net Agent Balance -->
        @php
        $netBalance = (($totalOwedPurchases - $totalPaidPurchases) + $totalBaseReceived) - (($totalReceivableSales - $totalReceivedSales) + $totalBaseSent);
        @endphp
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" 
                 style="background: {{ $netBalance >= 0 ? 'linear-gradient(135deg, #f57c00 0%, #ffb74d 100%)' : 'linear-gradient(135deg, #0097a7 0%, #4dd0e1 100%)' }};">
                <span class="text-uppercase small font-weight-bold">حساب کل نماینده (Net Agent Balance)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format(abs($netBalance), 2) }}</h3>
                <span class="small text-white-50 mt-2 d-block">
                    {{ $netBalance >= 0 ? 'طلبکار ما است (We Owe Agent)' : 'بدهکار ما است (Agent Owes Us)' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Profile & Form Section -->
    <div class="row">
        <!-- Agent Profile Card -->
        <div class="col-lg-3">
            <div class="premium-card text-center p-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ mb_substr($agent->user->name, 0, 1) }}
                    </div>
                </div>
                <h4 class="font-weight-bold mb-1">{{$agent->user->name}}</h4>
                <p class="text-primary font-weight-bold mb-3">ACCOUNT #: {{$agent->agent_id}}</p>
                <hr>
                <div class="text-right">
                    <p class="mb-1"><small class="text-muted">آدرس:</small><br><strong>{{$agent->agent_address}}</strong></p>
                    <p class="mb-0"><small class="text-muted">تماس:</small><br>
                        <strong>
                            @foreach($agent->phone as $p)
                                {{$p->phone_no}}@if(!$loop->last), @endif
                            @endforeach
                        </strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Forensic Payment Form -->
        <div class="col-lg-9">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5><i class="fa fa-credit-card-alt mr-2"></i> {{ $paymentEdit ? 'ویرایش معامله (Edit Transaction)' : 'ثبت معامله جدید (New Transaction)' }}</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Allocation Info Alert -->
                    <div class="alert alert-info shadow-sm border-0 mb-4" id="allocation_info_box" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-link mr-2"></i>
                                <span>تخصیص معامله به سند: </span>
                                <strong id="allocation_doc_display"></strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-light" id="btn_cancel_allocation">لغو تخصیص (Cancel)</button>
                        </div>
                    </div>

                    <form action="{{ $paymentEdit ? '/dashboard/agent-payments/'.$paymentEdit->id : '/dashboard/agent-payments' }}" method="post" id="forensicPaymentForm">
                        @csrf
                        @if($paymentEdit) @method('PUT') @endif
                        <input type="hidden" name="agent_id" value="{{$agent->agent_id}}">
                        
                        <!-- Allocation Hidden Fields -->
                        <input type="hidden" name="allocatable_id" id="allocatable_id" value="">
                        <input type="hidden" name="allocatable_type" id="allocatable_type" value="">

                        <div class="row">
                            <!-- Column 1: Financials -->
                            <div class="col-lg-4 col-md-6">
                                <h6 class="form-section-title"><i class="fa fa-money"></i> جزئیات مالی (Financials)</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="field-label">مقدار پول (Original Amount)</label>
                                    <input type="number" step="0.0001" name="amount" id="original_amount" class="form-control custom-input font-weight-bold" 
                                           value="{{ $paymentEdit ? ($paymentEdit->original_amount ?: ($paymentEdit->amount ?: $paymentEdit->amount_af)) : old('amount') }}" required>
                                    <small class="field-explanation text-right">مبلغ را بر اساس واحد پولی انتخابی وارد کنید.</small>
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
                                    <small class="field-explanation text-right">ارزی که پرداخت با آن صورت گرفته است.</small>
                                </div>

                                <!-- Live Truth Preview Box -->
                                <div class="truth-preview-box mb-4">
                                    <span class="truth-label"><i class="fa fa-shield"></i> معادل دالر (USD Equivalent):</span><br>
                                    <span class="truth-value" id="usd_truth_preview">$ 0.0000</span>
                                    <input type="hidden" name="exchange_rate" id="current_rate_snapshot">
                                </div>
                            </div>

                            <!-- Column 2: Transaction Details -->
                            <div class="col-lg-4 col-md-6">
                                <h6 class="form-section-title"><i class="fa fa-exchange"></i> نوع معامله (Type & Ref)</h6>

                                <div class="form-group mb-4">
                                    <label class="field-label">نوع معامله (Transaction Type)</label>
                                    <select name="type" id="payment_type" class="form-control custom-input font-weight-bold">
                                        <option value="رسید" class="text-success" {{ ($paymentEdit && $paymentEdit->type == 'رسید') ? 'selected' : '' }}>رسید (Payment Received)</option>
                                        <option value="گرفت" class="text-danger" {{ ($paymentEdit && $paymentEdit->type == 'گرفت') ? 'selected' : '' }}>گرفت (Payment Sent)</option>
                                    </select>
                                    <small class="field-explanation text-right">آیا پول دریافت شده یا پرداخت شده؟</small>
                                </div>

                                <div class="form-group mb-4" id="is_advance_container">
                                    <div class="custom-control custom-checkbox text-right" style="direction: rtl;">
                                        <input type="hidden" name="is_advance" value="0">
                                        <input type="checkbox" class="custom-control-input" id="is_advance" name="is_advance" value="1" checked {{ ($paymentEdit && !$paymentEdit->is_advance) ? '' : 'checked' }}>
                                        <label class="custom-control-label field-label pr-4" for="is_advance" style="cursor: pointer;">به عنوان پیش‌پرداخت (As Advance Payment)</label>
                                    </div>
                                    <small class="field-explanation text-right">آیا این مبلغ علی‌الحساب بوده و بعداً به بل‌ها تخصیص می‌یابد؟</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">چیک نمبر / سند (Reference)</label>
                                    <select name="check_number" id="check_id" class="form-control custom-input">
                                        <option value="نقد">نقد (Cash)</option>
                                        @foreach($check_numbers as $ch)
                                            <option value="{{$ch->check_number}}" {{ ($paymentEdit && $paymentEdit->check_number == $ch->check_number) ? 'selected' : '' }}>{{$ch->check_number}}</option>
                                        @endforeach
                                        @foreach($sale_numbers as $sa)
                                            <option value="{{$sa->sale_number}}" {{ ($paymentEdit && $paymentEdit->check_number == $sa->sale_number) ? 'selected' : '' }}>{{$sa->sale_number}}</option>
                                        @endforeach
                                    </select>
                                    <small class="field-explanation text-right">شماره چک یا شماره حواله فروش مواد.</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">تاریخ (Date)</label>
                                    <input type="date" name="date" class="form-control custom-input" value="{{ $paymentEdit ? $paymentEdit->date : date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Column 3: Audit & Submit -->
                            <div class="col-lg-4">
                                <h6 class="form-section-title"><i class="fa fa-commenting"></i> توضیحات (Audit Note)</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="field-label">شرح معامله (Description)</label>
                                    <textarea name="description" id="payment_description" rows="4" class="form-control custom-input" placeholder="شرح کامل معامله را اینجا بنویسید..." required>{{ $paymentEdit ? $paymentEdit->description : '' }}</textarea>
                                    <small class="field-explanation text-right">جزئیات این تراکنش برای بررسی های بعدی بسیار مهم است.</small>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-premium btn-premium-primary btn-block shadow-sm" type="submit">
                                        <i class="fa fa-save"></i> {{ $paymentEdit ? 'بروزرسانی معامله (Update)' : 'ثبت نهایی معامله (Save)' }}
                                    </button>
                                    @if($paymentEdit)
                                        <a href="/dashboard/agent-payments/{{$agent->agent_id}}" class="btn btn-outline-secondary btn-block mt-2">انصراف (Cancel)</a>
                                    @endif
                                </div>
                            </div>

                            <!-- Account Overrides Section -->
                            <div class="col-lg-12 mt-4">
                                <h6 class="form-section-title"><i class="fa fa-university"></i> حسابات سفارشی (Manual GL Overrides) - <small class="text-muted">اختیاری (Optional)</small></h6>
                                <div class="row">
                                    <!-- Container for Received (رسید) -->
                                    <div class="col-md-6 account-override-group" id="override_received_group" style="display: none;">
                                        <div class="form-group mb-3">
                                            <label class="field-label">حساب بدهکار / Debit Account (رسید)</label>
                                            <select name="override_debit_account_id" id="debit_received" class="form-control custom-input override-select">
                                                <option value="">-- انتخاب حساب پیشفرض (Default Cash) --</option>
                                                @foreach($pymtInDebit as $acc)
                                                    <option value="{{$acc->id}}" {{ $paymentEdit && $paymentEdit->override_debit_account_id == $acc->id ? 'selected' : '' }}>
                                                        {{$acc->account_code}} - {{$acc->account_name}} ({{$acc->account_type}})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="field-label">حساب بستانکار / Credit Account (رسید)</label>
                                            <select name="override_credit_account_id" id="credit_received" class="form-control custom-input override-select">
                                                <option value="">-- انتخاب حساب پیشفرض (Default Agent Control) --</option>
                                                @foreach($pymtInCredit as $acc)
                                                    <option value="{{$acc->id}}" {{ $paymentEdit && $paymentEdit->override_credit_account_id == $acc->id ? 'selected' : '' }}>
                                                        {{$acc->account_code}} - {{$acc->account_name}} ({{$acc->account_type}})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Container for Sent (گرفت) -->
                                    <div class="col-md-6 account-override-group" id="override_sent_group" style="display: none;">
                                        <div class="form-group mb-3">
                                            <label class="field-label">حساب بدهکار / Debit Account (گرفت)</label>
                                            <select name="override_debit_account_id" id="debit_sent" class="form-control custom-input override-select" disabled>
                                                <option value="">-- انتخاب حساب پیشفرض (Default Agent Control) --</option>
                                                @foreach($pymtOutDebit as $acc)
                                                    <option value="{{$acc->id}}" {{ $paymentEdit && $paymentEdit->override_debit_account_id == $acc->id ? 'selected' : '' }}>
                                                        {{$acc->account_code}} - {{$acc->account_name}} ({{$acc->account_type}})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="field-label">حساب بستانکار / Credit Account (گرفت)</label>
                                            <select name="override_credit_account_id" id="credit_sent" class="form-control custom-input override-select" disabled>
                                                <option value="">-- انتخاب حساب پیشفرض (Default Cash) --</option>
                                                @foreach($pymtOutCredit as $acc)
                                                    <option value="{{$acc->id}}" {{ $paymentEdit && $paymentEdit->override_credit_account_id == $acc->id ? 'selected' : '' }}>
                                                        {{$acc->account_code}} - {{$acc->account_name}} ({{$acc->account_type}})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Tab Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card">
                <div class="card-header-premium p-0 d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs border-0" id="agentDetailTabs" role="tablist" style="padding: 10px 15px 0 15px;">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold text-white" id="cash-tab" data-toggle="tab" href="#cash-ledger" role="tab" style="background: transparent; border: none; border-bottom: 3px solid #ffffff; padding: 15px 20px;">
                                <i class="fa fa-money"></i> معاملات نقدی (Cash Ledger)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="bills-tab" data-toggle="tab" href="#purchase-bills" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-file-text"></i> بل‌های خرید قالین (Purchase Bills)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="sales-tab" data-toggle="tab" href="#sales-invoices" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-shopping-cart"></i> انوایس‌های فروش مواد (Sales Invoices)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="advances-tab" data-toggle="tab" href="#agent-advances" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-cubes"></i> پیش‌پرداخت‌ها (Advances)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="reconciliation-tab" data-toggle="tab" href="#agent-reconciliation" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-handshake-o"></i> تاریخچه تصفیه (Reconciliation)
                            </a>
                        </li>
                    </ul>
                    <div id="exportButton" class="mr-3"></div>
                </div>
                
                <div class="card-body p-0 tab-content" id="agentDetailTabsContent">
                    <!-- Tab 1: Cash Ledger (Existing View) -->
                    <div class="tab-pane fade show active" id="cash-ledger" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0" id="agent_payment_table">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>نوع (Type)</th>
                                        <th>شرح (Description)</th>
                                        <th>سند (Ref)</th>
                                        <th>حسابات (Accounts)</th>
                                        <th>ارز (CCY)</th>
                                        <th>مقدار اصلی (Original)</th>
                                        <th>نرخ (Rate)</th>
                                        <th>معادل دالر (USD)</th>
                                        <th>حالت (Status)</th>
                                        <th class="hideOnPrint">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $pa)
                                    <tr class="text-right ur{{$pa->id}}">
                                        <td class="font-weight-bold">{{ $pa->date }}</td>
                                        <td>
                                            <span class="badge {{ $pa->type == 'رسید' ? 'badge-success' : 'badge-danger' }} px-3 py-2">
                                                {{ $pa->type == 'رسید' ? 'رسید (Received)' : 'گرفت (Sent)' }}
                                            </span>
                                            @if($pa->is_advance)
                                                <span class="badge badge-info px-2 py-1 text-white d-block mt-1">علی‌الحساب (Advance)</span>
                                            @endif
                                        </td>
                                        <td>{{ $pa->description }}</td>
                                        <td>
                                            <span class="badge badge-light p-2 border">
                                                @if($pa->check_number == 'نقد') نقد @else {{$pa->check_number}} @endif
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                if ($pa->is_advance) {
                                                    $rule = ($pa->type == 'گرفت') ? ($advOutRule ?? null) : ($advInRule ?? null);
                                                } else {
                                                    $rule = ($pa->type == 'گرفت') ? ($pymtOutRule ?? null) : ($pymtInRule ?? null);
                                                }
                                                $deb = $pa->debitAccount ?? ($rule->debitAccount ?? null);
                                                $cred = $pa->creditAccount ?? ($rule->creditAccount ?? null);

                                                $debCode = $deb ? $deb->account_code : ($pa->type == 'گرفت' ? ($pa->is_advance ? '11400' : '15000') : ($pa->is_advance ? '10000' : '10900'));
                                                $credCode = $cred ? $cred->account_code : ($pa->type == 'گرفت' ? ($pa->is_advance ? '10000' : '10900') : ($pa->is_advance ? '11400' : '15000'));

                                                $debName = $deb ? ($deb->account_code . ' - ' . $deb->account_name) : 'Debit Account';
                                                $credName = $cred ? ($cred->account_code . ' - ' . $cred->account_name) : 'Credit Account';
                                            @endphp
                                            <div style="font-size: 0.78rem; line-height: 1.3;">
                                                <span class="badge badge-light border text-primary font-weight-bold d-block mb-1" style="padding: 3px 6px;" title="حساب بدهکار (Debit): {{ $debName }}" data-toggle="tooltip">
                                                    Dr: {{ $debCode }}
                                                </span>
                                                <span class="badge badge-light border text-success font-weight-bold d-block" style="padding: 3px 6px;" title="حساب بستانکار (Credit): {{ $credName }}" data-toggle="tooltip">
                                                    Cr: {{ $credCode }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold text-primary">{{ $pa->currency_code ?: ($pa->amount > 0 ? 'USD' : 'AFN') }}</td>
                                        <td class="font-weight-bold" style="direction: ltr;">
                                            {{ number_format($pa->original_amount ?: ($pa->amount ?: $pa->amount_af), 2) }}
                                            @if($pa->is_advance)
                                                <div class="small text-success mt-1">باقیمانده: {{ number_format($pa->remaining_unallocated_amount, 2) }}</div>
                                            @endif
                                        </td>
                                        <td class="text-muted small" style="direction: ltr;">{{ number_format($pa->exchange_rate ?: $pa->dollar_rate, 8) }}</td>
                                        <td class="font-weight-bold text-dark" style="direction: ltr;">
                                            $ {{ number_format($pa->base_amount ?: ($pa->amount ?: 0), 2) }}
                                        </td>
                                        <td>
                                            @if($pa->status == 0)
                                                <span class="status-badge bg-warning text-dark">در انتظار تایید</span>
                                            @else
                                                <span class="status-badge bg-success text-white">تایید شده</span>
                                            @endif
                                        </td>
                                        <td class="hideOnPrint text-center">
                                            @if(auth()->user()->role != 'AO')
                                                <div class="btn-group">
                                                    <a href="/dashboard/agent-payments/{{$pa->id}}/edit" class="btn btn-sm btn-outline-info" title="ویرایش">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @can('cancel_agent_payment')
                                                    <button onclick="deletePayment({{$pa->id}} ,{{$pa->agent_id}})" class="btn btn-sm btn-outline-danger" title="لغو پرداخت">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                    @endcan
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    @foreach($currencyTotals as $code => $totals)
                                    <tr>
                                        <th colspan="4" class="text-right">خلاصه {{ $code }} ({{ $code }} Summary)</th>
                                        <td colspan="2" class="text-success text-right"><b>رسید: {{ number_format($totals->total_received, 2) }}</b></td>
                                        <td colspan="2" class="text-danger text-right"><b>گرفت: {{ number_format($totals->total_sent, 2) }}</b></td>
                                        @php
                                            $balance = $totals->total_received - $totals->total_sent;
                                        @endphp
                                        <td colspan="3" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $balance >= 0 ? 'طلبکار' : 'بدهکار' }}: {{ number_format(abs($balance), 2) }} {{ $code }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr style="background: #e3f2fd;">
                                        <th colspan="4" class="text-right"><b>مجموع کل بیلانس نقدی (Base USD)</b></th>
                                        <td colspan="2" class="text-success text-right"><b>$ {{ number_format($totalBaseReceived, 2) }}</b></td>
                                        <td colspan="2" class="text-danger text-right"><b>$ {{ number_format($totalBaseSent, 2) }}</b></td>
                                        @php
                                            $baseBalance = $totalBaseReceived - $totalBaseSent;
                                        @endphp
                                        <td colspan="2" class="text-center font-weight-bold {{ $baseBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                                            بیلانس نهایی نقد: $ {{ number_format(abs($baseBalance), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Purchase Bills -->
                    <div class="tab-pane fade" id="purchase-bills" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>نمبر بل (Bill No)</th>
                                        <th>تعداد قالین (Carpets)</th>
                                        <th>مجموعه مساحت (Total Area)</th>
                                        <th>قیمت کل (Total Price)</th>
                                        <th>پرداخت شده (Paid)</th>
                                        <th>باقیمانده (Remaining)</th>
                                        <th>حالت ویرایش (Edit Status)</th>
                                        <th>حالت تصفیه (Payment Status)</th>
                                        <th class="hideOnPrint">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseBills as $bill)
                                    <tr class="text-right">
                                        <td>{{ $bill->date }}</td>
                                        <td><a href="/dashboard/check-book/{{ $bill->id }}" target="_blank" title="مشاهده بل خرید"><strong>{{ $bill->invoice_number }}</strong></a></td>
                                        <td>{{ $bill->carpets->count() }} تخته</td>
                                        <td>{{ number_format($bill->carpets->sum('area'), 2) }} m²</td>
                                        <td>$ {{ number_format($bill->total_amount, 2) }}</td>
                                        <td class="text-success">$ {{ number_format($bill->paid_amount, 2) }}</td>
                                        <td class="font-weight-bold text-danger">$ {{ number_format($bill->remaining_balance, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $bill->status === 'closed' ? 'badge-dark' : 'badge-light border' }}">
                                                {{ $bill->status === 'closed' ? 'قفل شده' : 'قابل ویرایش' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $bill->payment_status === 'paid' ? 'bg-success text-white' : ($bill->payment_status === 'partially_paid' ? 'bg-info text-white' : 'bg-warning text-dark') }}">
                                                {{ $bill->payment_status === 'paid' ? 'تصفیه شده' : ($bill->payment_status === 'partially_paid' ? 'تادیه قسمتی' : 'پرداخت نشده') }}
                                            </span>
                                        </td>
                                        <td class="hideOnPrint">
                                            @if($bill->payment_status !== 'paid' && $bill->remaining_balance > 0.01)
                                            <button class="btn btn-sm btn-primary quick-pay-btn" 
                                                    data-id="{{ $bill->id }}" 
                                                    data-no="{{ $bill->invoice_number }}" 
                                                    data-balance="{{ $bill->remaining_balance }}"
                                                    data-type="App\PurchaseInvoice">
                                                <i class="fa fa-credit-card"></i> تادیه بل
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>تصفیه کامل</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">هیچ بل خریدی برای این نماینده یافت نشد.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Sales Invoices -->
                    <div class="tab-pane fade" id="sales-invoices" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>نمبر انوایس (Invoice No)</th>
                                        <th>نوع مواد (Material)</th>
                                        <th>وزن کل (Total Weight)</th>
                                        <th>ارزش کل (Total Value)</th>
                                        <th>دریافت شده (Received)</th>
                                        <th>باقیمانده (Remaining)</th>
                                        <th>حالت ویرایش (Edit Status)</th>
                                        <th>حالت تصفیه (Payment Status)</th>
                                        <th class="hideOnPrint">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesInvoices as $inv)
                                    <tr class="text-right">
                                        <td>{{ $inv->invoice_date }}</td>
                                        <td><a href="/dashboard/invoices/{{ $inv->id }}" target="_blank" title="مشاهده انوایس فروش"><strong>{{ $inv->invoice_no }}</strong></a></td>
                                        <td class="text-capitalize">{{ $inv->type }}</td>
                                        <td>{{ number_format($inv->material_sales->sum('amount'), 2) }} kg</td>
                                        <td>$ {{ number_format($inv->total_amount, 2) }}</td>
                                        <td class="text-success">$ {{ number_format($inv->paid_amount, 2) }}</td>
                                        <td class="font-weight-bold text-danger">$ {{ number_format($inv->remaining_balance, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $inv->status === 'closed' ? 'badge-dark' : 'badge-light border' }}">
                                                {{ $inv->status === 'closed' ? 'قفل شده' : 'قابل ویرایش' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $inv->payment_status === 'paid' ? 'bg-success text-white' : ($inv->payment_status === 'partially_paid' ? 'bg-info text-white' : 'bg-warning text-dark') }}">
                                                {{ $inv->payment_status === 'paid' ? 'تصفیه شده' : ($inv->payment_status === 'partially_paid' ? 'دریافت قسمتی' : 'دریافت نشده') }}
                                            </span>
                                        </td>
                                        <td class="hideOnPrint">
                                            @if($inv->payment_status !== 'paid' && $inv->remaining_balance > 0.01)
                                            <button class="btn btn-sm btn-success quick-pay-btn" 
                                                    data-id="{{ $inv->id }}" 
                                                    data-no="{{ $inv->invoice_no }}" 
                                                    data-balance="{{ $inv->remaining_balance }}"
                                                    data-type="App\Invoice">
                                                <i class="fa fa-download"></i> دریافت پول
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>دریافت کامل</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">هیچ انوایس فروشی برای این نماینده یافت نشد.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Agent Advances -->
                    <div class="tab-pane fade" id="agent-advances" role="tabpanel">
                        @php
                            $advances = \App\AgentPayment::where('agent_id', $agent->agent_id)
                                ->where('is_advance', true)
                                ->where('status', 1)
                                ->orderBy('date', 'DESC')
                                ->get();
                        @endphp
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>نمبر پیش‌پرداخت (Payment ID)</th>
                                        <th>نوعیت (Type)</th>
                                        <th>شرح (Description)</th>
                                        <th>ارز (Currency)</th>
                                        <th>مبلغ اصلی (Original Amount)</th>
                                        <th>نرخ ارز (Exchange Rate)</th>
                                        <th>معادل دالر (USD Amount)</th>
                                        <th>باقیمانده مصرف‌نشده (Unallocated Balance)</th>
                                        <th class="hideOnPrint">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($advances as $adv)
                                    <tr class="text-right">
                                        <td>{{ $adv->date }}</td>
                                        <td><strong>AGT-PAY-{{ $adv->id }}</strong></td>
                                        <td>
                                            <span class="badge {{ $adv->type == 'رسید' ? 'badge-success' : 'badge-danger' }} px-3 py-2">
                                                {{ $adv->type == 'رسید' ? 'رسید (Received)' : 'گرفت (Sent)' }}
                                            </span>
                                        </td>
                                        <td>{{ $adv->description }}</td>
                                        <td class="text-center font-weight-bold text-primary">{{ $adv->currency_code }}</td>
                                        <td class="font-weight-bold" style="direction: ltr;">{{ number_format($adv->original_amount, 2) }}</td>
                                        <td class="text-muted small" style="direction: ltr;">{{ number_format($adv->exchange_rate, 4) }}</td>
                                        <td class="font-weight-bold" style="direction: ltr;">$ {{ number_format($adv->base_amount, 2) }}</td>
                                        <td class="font-weight-bold text-success" style="direction: ltr;">
                                            {{ number_format($adv->remaining_unallocated_amount, 2) }} {{ $adv->currency_code }}
                                        </td>
                                        <td class="hideOnPrint">
                                            <div class="btn-group">
                                                @if($adv->status == 1 && $adv->remaining_unallocated_amount > 0.01)
                                                <button class="btn btn-sm btn-premium btn-premium-primary open-allocate-modal-btn" 
                                                        data-payment-id="{{ $adv->id }}"
                                                        data-currency="{{ $adv->currency_code }}"
                                                        data-remaining="{{ $adv->remaining_unallocated_amount }}"
                                                        data-exchange-rate="{{ $adv->exchange_rate }}">
                                                    <i class="fa fa-share-square-o"></i> تخصیص
                                                </button>
                                                @else
                                                <span class="text-muted mr-2">کامل تخصیص شده / تایید نشده</span>
                                                @endif
                                                
                                                @can('cancel_agent_payment')
                                                <button onclick="deletePayment({{$adv->id}} ,{{$adv->agent_id}})" class="btn btn-sm btn-outline-danger ml-1" title="لغو پیش‌پرداخت">
                                                    <i class="fa fa-ban"></i> لغو
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">هیچ پیش‌پرداختی برای این نماینده یافت نشد.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 5: Agent Reconciliation (Allocations History) -->
                    <div class="tab-pane fade" id="agent-reconciliation" role="tabpanel">
                        @php
                            $agentAllocations = \App\AgentPaymentAllocation::whereHas('agent_payment', function($q) use ($agent) {
                                $q->where('agent_id', $agent->agent_id)->where('status', 1);
                            })->with(['agent_payment', 'allocatable'])->orderBy('id', 'DESC')->get();
                        @endphp
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ تخصیص (Allocation Date)</th>
                                        <th>سند پیش‌پرداخت (Source Advance)</th>
                                        <th>نوعیت (Type)</th>
                                        <th>بل/انوایس مقصد (Target Document)</th>
                                        <th>حسابات درگیر (Accounts Involved)</th>
                                        <th>مبلغ تخصیص (Allocated Amount)</th>
                                        <th>نرخ ارز (Exchange Rate)</th>
                                        <th>معادل دالر (Base USD Allocated)</th>
                                        <th class="hideOnPrint">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($agentAllocations as $alloc)
                                    <tr class="text-right">
                                        <td>{{ $alloc->created_at ? $alloc->created_at->format('Y-m-d') : '---' }}</td>
                                        <td>
                                            <a href="#" class="font-weight-bold">
                                                AGT-PAY-{{ $alloc->agent_payment_id }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $alloc->agent_payment->description ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $alloc->agent_payment->type == 'رسید' ? 'badge-success' : 'badge-danger' }} px-3 py-2">
                                                {{ $alloc->agent_payment->type == 'رسید' ? 'رسید (Received)' : 'گرفت (Sent)' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($alloc->allocatable)
                                                @if($alloc->allocatable_type == 'App\PurchaseInvoice')
                                                    <span class="badge badge-info text-white">بل خرید</span>
                                                    <strong>{{ $alloc->allocatable->invoice_number }}</strong>
                                                @else
                                                    <span class="badge badge-success text-white">انوایس فروش</span>
                                                    <strong>{{ $alloc->allocatable->invoice_no }}</strong>
                                                @endif
                                            @else
                                                <span class="text-danger">سند حذف شده</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $advPayment = $alloc->agent_payment;
                                                if ($advPayment) {
                                                    $advRule = ($advPayment->type == 'گرفت') ? ($advOutRule ?? null) : ($advInRule ?? null);
                                                    $advDeb = $advPayment->debitAccount ?? ($advRule->debitAccount ?? null);
                                                    $advCred = $advPayment->creditAccount ?? ($advRule->creditAccount ?? null);
                                                    $advDebCode = $advDeb ? $advDeb->account_code : ($advPayment->type == 'گرفت' ? '11400' : '10000');
                                                    $advCredCode = $advCred ? $advCred->account_code : ($advPayment->type == 'گرفت' ? '10000' : '11400');
                                                    $advDebName = $advDeb ? ($advDeb->account_code . ' - ' . $advDeb->account_name) : 'Advance Account';
                                                    $advCredName = $advCred ? ($advCred->account_code . ' - ' . $advCred->account_name) : 'Cash/Bank Account';
                                                } else {
                                                    $advDebCode = '11400';
                                                    $advCredCode = '10000';
                                                    $advDebName = 'Advance to Suppliers';
                                                    $advCredName = 'Cash-USD';
                                                }

                                                $settleDebCode = ($alloc->allocatable_type == 'App\PurchaseInvoice') ? '15000' : '12000';
                                                $settleCredCode = '11400';
                                                $targetTypeName = ($alloc->allocatable_type == 'App\PurchaseInvoice') ? 'Accounts Payable (15000)' : 'Accounts Receivable (12000)';
                                            @endphp
                                            <div style="font-size: 0.78rem; line-height: 1.3;">
                                                <div class="text-nowrap mb-1">
                                                    <span class="badge badge-light border text-primary font-weight-bold" style="padding: 3px 6px;" title="پیش‌پرداخت (Advance): Dr {{ $advDebName }} / Cr {{ $advCredName }}" data-toggle="tooltip">
                                                        پیش‌پرداخت: {{ $advDebCode }} / {{ $advCredCode }}
                                                    </span>
                                                </div>
                                                <div class="text-nowrap">
                                                    <span class="badge badge-light border text-success font-weight-bold" style="padding: 2px 5px;" title="تصفیه تخصیص (Settlement): Dr {{ $targetTypeName }} -> Cr Advance to Suppliers (11400)" data-toggle="tooltip">
                                                        تصفیه: Dr {{ $settleDebCode }} &rarr; Cr {{ $settleCredCode }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-weight-bold text-success" style="direction: ltr;">
                                            {{ number_format($alloc->allocated_amount, 2) }} {{ $alloc->agent_payment->currency_code ?? 'USD' }}
                                        </td>
                                        <td class="text-muted small" style="direction: ltr;">{{ number_format($alloc->exchange_rate, 4) }}</td>
                                        <td class="font-weight-bold text-dark" style="direction: ltr;">
                                            $ {{ number_format($alloc->base_allocated_amount, 2) }}
                                        </td>
                                        <td class="hideOnPrint">
                                            @can('cancel_agent_payment')
                                            <button onclick="removeAllocation({{ $alloc->id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="حذف تخصیص">
                                                <i class="fa fa-undo"></i> لغو تصفیه
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">هیچ تخصیصی در سیستم ثبت نشده است.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    @if(!isset($all))
                        {{$payments->links()}}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Allocation Modal -->
    <div class="modal fade" id="allocateAdvanceModal" tabindex="-1" role="dialog" aria-labelledby="allocateAdvanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content premium-modal">
                <div class="modal-header bg-premium-dark text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h5 class="modal-title font-weight-bold" id="allocateAdvanceModalLabel"><i class="fa fa-share-square-o"></i> تخصیص پیش‌پرداخت به سند بدهی</h5>
                    <button type="button" class="close text-white m-0 p-0" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="allocateAdvanceForm">
                    @csrf
                    <input type="hidden" name="agent_payment_id" id="modal_payment_id">
                    <div class="modal-body text-right" style="direction: rtl;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light p-3 mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                                    <h6 class="font-weight-bold text-primary mb-3"><i class="fa fa-info-circle"></i> معلومات علی‌الحساب</h6>
                                    <p class="mb-2"><strong>شماره پرداخت:</strong> <span id="modal_display_pay_id" class="badge badge-secondary py-1 px-2 font-weight-bold"></span></p>
                                    <p class="mb-2"><strong>مبلغ باقیمانده (Unallocated):</strong> <span id="modal_display_remaining" class="text-success font-weight-bold" style="font-size: 1.1rem;"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light p-3 mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                                    <h6 class="font-weight-bold text-warning mb-3"><i class="fa fa-file-text-o"></i> انتخاب سند جهت تصفیه</h6>
                                    
                                    <input type="hidden" name="allocatable_type" id="modal_allocatable_type" value="App\PurchaseInvoice">

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold field-label">سند بدهی:</label>
                                        <select name="allocatable_id" id="modal_allocatable_id" class="form-control custom-input" style="width: 100%;">
                                            <!-- Dynamically filled via JS -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark field-label">مبلغ تخصیص (Allocation Amount):</label>
                                    <div class="input-group" style="direction: ltr;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text modal_currency_display" style="font-weight: bold; background: #e3f2fd;">USD</span>
                                        </div>
                                        <input type="number" step="0.0001" name="amount" id="modal_alloc_amount" class="form-control font-weight-bold text-center text-success" style="font-size: 1.25rem; direction: ltr;" required>
                                    </div>
                                    <small class="field-explanation text-right d-block mt-1">مقداری از پیش‌پرداخت که می‌خواهید به سند انتخاب‌شده تخصیص دهید.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">انصراف (Cancel)</button>
                        <button type="submit" class="btn btn-premium btn-premium-primary shadow-sm" id="btn_submit_modal_allocation">
                            <i class="fa fa-save"></i> ثبت تخصیص (Apply Allocation)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#check_id').select2();
        $('#currency_id').select2();

        // TAB SWITCHING WORKFLOW
        $('#agentDetailTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
            $('#agentDetailTabs a').removeClass('text-white').addClass('text-white-50').css('border-bottom', 'none');
            $(this).removeClass('text-white-50').addClass('text-white').css('border-bottom', '3px solid #ffffff');
        });

        // LIVE TRUTH PREVIEW LOGIC
        function updateUsdPreview() {
            const amount = parseFloat($('#original_amount').val()) || 0;
            const selectedCurrency = $('#currency_id option:selected');
            const rate = parseFloat(selectedCurrency.data('rate')) || 0;
            
            // Formula: base_amount = original_amount * exchange_rate
            const baseAmount = (amount * rate).toFixed(4);
            
            $('#usd_truth_preview').text('$ ' + parseFloat(baseAmount).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
            $('#current_rate_snapshot').val(rate);
        }

        $('#original_amount, #currency_id').on('input change', updateUsdPreview);
        updateUsdPreview(); // Initial call

        // QUICK SETTLEMENT ALLOCATION WORKFLOW
        $('.quick-pay-btn').on('click', function () {
            const docId = $(this).data('id');
            const docNo = $(this).data('no');
            const balanceUsd = parseFloat($(this).data('balance')) || 0;
            const type = $(this).data('type');

            // Find USD option and select it
            $('#currency_id').val($('#currency_id option').filter(function() {
                return $(this).text().indexOf('USD') !== -1;
            }).val()).trigger('change');

            // Set amount to outstanding USD balance
            $('#original_amount').val(balanceUsd.toFixed(4));
            
            // Set check reference list selection if matches invoice/bill no
            if ($("#check_id option[value='" + docNo + "']").length > 0) {
                $('#check_id').val(docNo).trigger('change');
            } else {
                $('#check_id').val('نقد').trigger('change');
            }

            // Set allocation inputs
            $('#allocatable_id').val(docId);
            $('#allocatable_type').val(type);
            $('#is_advance').prop('checked', false);

            // Set transaction type and description note
            if (type.indexOf('PurchaseInvoice') !== -1) {
                $('#payment_type').val('گرفت').trigger('change'); // Payment Sent
                $('#payment_description').val(`بابت تصفیه بل خرید قالین شماره ${docNo}`);
                $('#allocation_doc_display').text(`بل خرید قالین شماره ${docNo} (باقیمانده: $${balanceUsd.toFixed(2)})`);
            } else {
                $('#payment_type').val('رسید').trigger('change'); // Payment Received
                $('#payment_description').val(`بابت دریافت پول انوایس فروش مواد شماره ${docNo}`);
                $('#allocation_doc_display').text(`انوایس فروش مواد شماره ${docNo} (باقیمانده: $${balanceUsd.toFixed(2)})`);
            }

            $('#allocation_info_box').fadeIn();
            updateUsdPreview();

            // Smooth Scroll to form
            $('html, body').animate({
                scrollTop: $("#agent-payment").offset().top - 20
            }, 600);
        });

        // CANCEL ALLOCATION ACTION
        $('#btn_cancel_allocation').on('click', function () {
            $('#allocatable_id').val('');
            $('#allocatable_type').val('');
            $('#allocation_info_box').fadeOut();
            $('#original_amount').val('');
            $('#payment_description').val('');
            $('#is_advance').prop('checked', true);
        });

        // Export functionality
        $("#agent_payment_table").tableExport({
            formats: ["xlsx"],
            filename: "agent_payments_{{ $agent->agent_id }}",
            bootstrap: true,
            position: "bottom"
        });
        
        var $buttons = $('#agent_payment_table').find('caption').children().detach();
        $buttons.appendTo('#exportButton');

        // MANUAL GL OVERRIDES TOGGLE
        function toggleOverrideAccounts() {
            const type = $('select[name="type"]').val();
            if (type === 'رسید') {
                $('#override_received_group').show();
                $('#debit_received, #credit_received').prop('disabled', false).trigger('change');
                
                $('#override_sent_group').hide();
                $('#debit_sent, #credit_sent').prop('disabled', true).trigger('change');
            } else {
                $('#override_received_group').hide();
                $('#debit_received, #credit_received').prop('disabled', true).trigger('change');
                
                $('#override_sent_group').show();
                $('#debit_sent, #credit_sent').prop('disabled', false).trigger('change');
            }
        }

        $('.override-select').select2({ width: '100%' });
        $('select[name="type"]').on('change', toggleOverrideAccounts);
        toggleOverrideAccounts(); // Initial call

        // ADVANCE ALLOCATION MODAL ACTIONS
        const purchaseBills = @json($purchaseBills);
        const salesInvoices = @json($salesInvoices);

        let activePaymentRemaining = 0;
        let activePaymentExchangeRate = 1;

        $('.open-allocate-modal-btn').on('click', function () {
            const payId = $(this).data('payment-id');
            const currency = $(this).data('currency');
            const remaining = parseFloat($(this).data('remaining')) || 0;
            const rate = parseFloat($(this).data('exchange-rate')) || 1;

            activePaymentRemaining = remaining;
            activePaymentExchangeRate = rate;

            $('#modal_payment_id').val(payId);
            $('#modal_display_pay_id').text('AGT-PAY-' + payId);
            $('#modal_display_remaining').text(remaining.toFixed(2) + ' ' + currency);
            $('.modal_currency_display').text(currency);
            $('#modal_alloc_amount').val(remaining.toFixed(4)).attr('max', remaining);

            loadDocumentsForAllocation(currency);

            $('#allocateAdvanceModal').modal('show');
        });

        function loadDocumentsForAllocation(currency) {
            const select = $('#modal_allocatable_id');
            select.empty();

            purchaseBills.forEach(bill => {
                const remaining = parseFloat(bill.remaining_balance) || 0;
                if (remaining > 0.01) {
                    select.append(`<option value="${bill.id}" data-remaining="${remaining}">بل خرید شماره ${bill.invoice_number} (باقیمانده: $${remaining.toFixed(2)})</option>`);
                }
            });
            select.trigger('change');
        }

        $('#modal_allocatable_id').on('change', function () {
            const selectedOpt = $(this).find(':selected');
            if (selectedOpt.length) {
                const billRemainingUsd = parseFloat(selectedOpt.data('remaining')) || 0;
                const billRemainingInPaymentCurrency = billRemainingUsd / activePaymentExchangeRate;
                const targetAmount = Math.min(activePaymentRemaining, billRemainingInPaymentCurrency);
                $('#modal_alloc_amount').val(targetAmount.toFixed(4));
            }
        });

        $('#allocateAdvanceForm').on('submit', function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            
            $.ajax({
                type: 'POST',
                url: '/dashboard/agent-payments/allocate',
                data: data,
                success: function (res) {
                    if (res.status === 'success') {
                        $('#allocateAdvanceModal').modal('hide');
                        swal("موفقانه انجام شد!", res.message, "success");
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        swal("خطا!", res.message, "error");
                    }
                },
                error: function (xhr) {
                    swal("خطا!", "مشکلی در پروسس درخواست رخ داد.", "error");
                }
            });
        });
    });

    function deletePayment(id, agent_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عمل پرداخت را لغو کرده و سند مرتبط در روزنامه کل (GL) معکوس می‌گردد.",
            icon: "warning",
            buttons: {
                cancel: "نخیر",
                confirm: { text: "بلی، لغو شود", className: "btn-danger" }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/agent-payments/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" });
                            setTimeout(() => window.location = '/dashboard/agent-payments/' + agent_id, 1000);
                        } else {
                            swal("خطا در حذف!", { icon: "error" });
                        }
                    }
                });
            }
        });
    }

    function removeAllocation(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عمل تخصیص پیش‌پرداخت را لغو کرده و سند بدهی را دوباره بدهکار می‌سازد.",
            icon: "warning",
            buttons: {
                cancel: "نخیر",
                confirm: { text: "بلی، لغو شود", className: "btn-danger" }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/agent-payments/allocation/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه لغو شد!", res.message, { icon: "success" });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            swal("خطا در لغو تخصیص!", res.message, { icon: "error" });
                        }
                    },
                    error: function () {
                        swal("خطا!", "ارتباط با سرور برقرار نشد.", "error");
                    }
                });
            }
        });
    }
</script>
@endsection
