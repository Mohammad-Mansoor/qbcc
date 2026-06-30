@extends('dsh.master')

@section('content')
@php($lockDate = \DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value'))

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
    .normalization-box {
        background: linear-gradient(135deg, #e0f7fa 0%, #e1f5fe 100%);
        border-radius: 12px;
        border: 1px solid #b3e5fc;
        transition: all 0.3s;
    }
    .normalization-box-edit {
        background: linear-gradient(135deg, #fff3e0 0%, #fffde7 100%);
        border-radius: 12px;
        border: 1px solid #ffe082;
        transition: all 0.3s;
    }

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

<div class="container-fluid mt-4" id="customer_payment">
    <!-- Header Section -->
    <div class="row mb-4 hideOnPrint">
        <div class="col-md-8">
            <h3 class="font-weight-bold text-dark text-right">
                <i class="fa fa-users text-primary"></i> 
                پرداخت های مشتری (Customer Payments & Settlements)
            </h3>
            <p class="text-muted text-right">مدیریت معاملات مالی، انوایس‌های فروش قالین، و دفتر کل نقدی مشتریان</p>
        </div>
        <div class="col-md-4 text-left">
            <div class="btn-group">
                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="fa fa-print"></i> چاپ گزارش (Print)
                </button>
                <a href="/dashboard/customer-payments-all/{{$customer->id}}" class="btn btn-outline-info btn-sm">
                    <i class="fa fa-list"></i> نمایش همه (Show All)
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
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
        <!-- Card 1: Carpet Sales Invoices -->
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #d32f2f 0%, #ff6b6b 100%);">
                <span class="text-uppercase small font-weight-bold">کل فروشات قالین (Carpet Sales)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalOwedSales, 2) }}</h3>
                <div class="d-flex justify-content-between small text-white-50 mt-2">
                    <span>دریافت شده: $ {{ number_format($totalPaidSales, 2) }}</span>
                    <span>باقیمانده: $ {{ number_format($totalOwedSales - $totalPaidSales, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Unallocated Cash Received -->
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #388e3c 0%, #81c784 100%);">
                <span class="text-uppercase small font-weight-bold">دریافتی نقدی (Cash Received)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalBaseReceived, 2) }}</h3>
                <span class="small text-white-50 mt-2 d-block">نقد دریافت شده غیر تخصیص یافته</span>
            </div>
        </div>

        <!-- Card 3: Unallocated Cash Sent -->
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" style="background: linear-gradient(135deg, #1976d2 0%, #64b5f6 100%);">
                <span class="text-uppercase small font-weight-bold">پرداختی نقدی (Cash Sent)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format($totalBaseSent, 2) }}</h3>
                <span class="small text-white-50 mt-2 d-block">پرداخت نقدی غیر تخصیص یافته</span>
            </div>
        </div>

        <!-- Card 4: Net Customer Balance -->
        @php($netBalance = ($totalOwedSales - $totalPaidSales) + $totalBaseSent - $totalBaseReceived)
        <div class="col-md-3">
            <div class="premium-card p-3 text-white shadow-sm" 
                 style="background: {{ $netBalance >= 0 ? 'linear-gradient(135deg, #f57c00 0%, #ffb74d 100%)' : 'linear-gradient(135deg, #0097a7 0%, #4dd0e1 100%)' }};">
                <span class="text-uppercase small font-weight-bold">حساب کل مشتری (Net Customer Balance)</span>
                <h3 class="font-weight-bold mt-1">$ {{ number_format(abs($netBalance), 2) }}</h3>
                <span class="small text-white-50 mt-2 d-block">
                    {{ $netBalance >= 0 ? 'بدهکار ما است (Customer Owes Us)' : 'طلبکار ما است (We Owe Customer)' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Profile & Form Section -->
    <div class="row">
        <!-- Customer Profile Card -->
        <div class="col-lg-3">
            <div class="premium-card text-center p-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                </div>
                <h4 class="font-weight-bold mb-1">{{$customer->name}}</h4>
                <p class="text-primary font-weight-bold mb-3">ACCOUNT #: {{$customer->id}}</p>
                <hr>
                <div class="text-right text-dark">
                    <p class="mb-1"><small class="text-muted">آدرس:</small><br><strong>{{$customer->company_address}}</strong></p>
                    <p class="mb-0"><small class="text-muted">تماس:</small><br>
                        <strong style="direction: ltr; display: inline-block;">{{$customer->phone}}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="col-lg-9 hideOnPrint">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5 class="text-right"><i class="fa fa-credit-card-alt mr-2"></i> {{ $paymentEdit ? 'ویرایش معامله (Edit Transaction)' : 'ثبت معامله جدید (New Transaction)' }}</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Allocation Info Alert -->
                    <div class="alert alert-info shadow-sm border-0 mb-4 text-right" id="allocation_info_box" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center flex-row-reverse">
                            <div>
                                <i class="fa fa-link mr-2"></i>
                                <span>تخصیص معامله به سند: </span>
                                <strong id="allocation_doc_display"></strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-light font-weight-bold" id="btn_cancel_allocation">لغو تخصیص (Cancel)</button>
                        </div>
                    </div>

                    @if(!$paymentEdit)
                        <form action="/dashboard/customer-payments" method="post">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{$customer->id}}">
                            
                            <div class="row text-right">
                                <!-- Column 1: Payment Details -->
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="form-section-title"><i class="fa fa-money"></i> جزئیات پرداخت (Payment Details)</h6>
                                    
                                    <div class="form-group mb-4">
                                        <label class="field-label">مقدار پول (Amount)</label>
                                        <input type="number" step="0.01" name="amount" id="original_amount" value="{{old('amount')}}" class="form-control custom-input h5 font-weight-bold text-center" placeholder="0.00" required>
                                        <small class="field-explanation">مبلغ پرداختی را وارد کنید. سیستم به صورت خودکار آن را به دالر تبدیل می‌کند.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">نوع پول (Currency)</label>
                                        <select name="currency_id" id="currency_id" class="form-control custom-input">
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}">
                                                    {{ $curr->code }} - {{ $curr->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-explanation">واحد پولی معامله را انتخاب کنید.</small>
                                    </div>

                                    <div class="form-group mb-4" id="exchange_rate_container">
                                        <label class="field-label">نرخ تبادله به دالر (Exchange Rate to USD)</label>
                                        <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" value="{{old('exchange_rate')}}" class="form-control custom-input h5 font-weight-bold text-center" placeholder="1.00000000">
                                        <small class="field-explanation">ارزش ۱ واحد از این اسعار را به دالر وارد کنید.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">نوع معامله (Transaction Type)</label>
                                        <select name="type" id="payment_type" class="form-control custom-input font-weight-bold">
                                            <option value="رسید" class="text-success">رسید (Payment Received)</option>
                                            <option value="گرفت" class="text-danger">گرفت (Payment Sent/Adjustment)</option>
                                        </select>
                                        <small class="field-explanation">آیا پول دریافت شده (رسید) یا پرداخت شده است (گرفت)؟</small>
                                    </div>
                                </div>

                                <!-- Column 2: Reference & Dates -->
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="form-section-title"><i class="fa fa-file-text-o"></i> اسناد و تاریخ (Reference & Date)</h6>

                                    <div class="form-group mb-4">
                                        <label class="field-label">انوایس نمبر (Invoice Reference)</label>
                                        <select name="invoice_number" id="invoice_number_select" class="form-control custom-input">
                                            <option value="نقد">نقد (Cash Payment)</option>
                                            @foreach($invoice_numbers as $ch)
                                                <option value="{{$ch->invoice_no}}">انوایس شماره: {{$ch->invoice_no}}</option>
                                            @endforeach
                                        </select>
                                        <small class="field-explanation">اگر پرداخت مربوط به انوایس خاصی است، آن را انتخاب کنید.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">تاریخ (Transaction Date)</label>
                                        <input type="date" name="date" class="form-control custom-input text-center" value="{{ date('Y-m-d') }}" required>
                                        <small class="field-explanation">تاریخ واقعی معامله را وارد کنید.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">توضیحات (Description)</label>
                                        <textarea name="description" id="payment_description" rows="3" class="form-control custom-input" placeholder="مثلاً: بابت تسویه حساب انوایس فروش قالین" required></textarea>
                                        <small class="field-explanation">جزئیات بیشتر در مورد این پرداخت را اینجا بنویسید.</small>
                                    </div>
                                </div>

                                <!-- Column 3: Live Preview & Action -->
                                <div class="col-lg-4 col-md-12">
                                    <h6 class="form-section-title"><i class="fa fa-calculator"></i> محاسبه آنی (Live Truth Preview)</h6>
                                    
                                    <div id="normalization_preview_container" class="normalization-box p-4 text-center mb-4 shadow-sm" style="display:none;">
                                        <div class="text-muted small mb-2">ارزش نهایی در دفتر کل (USD Value)</div>
                                        <div class="h2 font-weight-bold text-primary mb-0">$<span id="final_usd_value">0.00</span></div>
                                        <div class="mt-2 badge badge-pill badge-primary px-3">سیستم از نرخ رسمی استفاده می‌کند</div>
                                    </div>

                                    <div class="row p-3 mb-3 mx-1" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px;">
                                        <div class="col-lg-12 mb-3">
                                            <label class="field-label text-info"><i class="fa fa-university"></i> حسابات مالی (Accounting)</label>
                                        </div>
                                        <div class="col-lg-12 mb-2" id="override_debit_container">
                                            <select name="override_debit_account_id" id="override_debit_account_id" class="form-control form-control-sm select2-simple">
                                                @foreach($allowedDebitAccounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                        بدهکار: {{ $acc->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mb-3" id="override_credit_container">
                                            <select name="override_credit_account_id" id="override_credit_account_id" class="form-control form-control-sm select2-simple">
                                                @foreach($allowedCreditAccounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                        بستانکار: {{ $acc->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12">
                                             <button class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold" type="submit"><i class="fa fa-check-circle"></i> ثبت نهایی معامله</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Allocation Section -->
                            <div id="invoice_allocation_section" style="display:none; margin-top: 20px; width: 100%;" class="mt-4 text-right">
                                <h6 class="form-section-title text-success"><i class="fa fa-list"></i> تخصیص به انوایس‌های باقی‌مانده (Invoice Match)</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="bg-light">
                                            <tr class="text-right">
                                                <th>نمبر انوایس</th>
                                                <th>تاریخ</th>
                                                <th>مجموع</th>
                                                <th>باقیمانده</th>
                                                <th width="150">مقدار تادیه</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoice_list_body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Edit form block -->
                        <form action="/dashboard/customer-payments/{{$paymentEdit->id}}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="customer_id" value="{{$customer->id}}">
                            
                            <div class="row text-right">
                                <!-- Column 1: Edit Details -->
                                <div class="col-lg-4 col-md-6 border-left">
                                    <h6 class="form-section-title"><i class="fa fa-edit text-warning"></i> ویرایش پرداخت (Edit Payment)</h6>
                                    
                                    <div class="form-group mb-4">
                                        <label class="field-label">مقدار پول (Amount)</label>
                                        <input type="number" step="0.01" name="amount" id="original_amount_edit" value="{{ $paymentEdit->original_amount ?? ($paymentEdit->amount > 0 ? $paymentEdit->amount : $paymentEdit->amount_af) }}" class="form-control custom-input h5 font-weight-bold text-center" required>
                                        <small class="field-explanation">مبلغ جدید را وارد کنید.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">نوع پول (Currency)</label>
                                        <select name="currency_id" id="currency_id_edit" class="form-control custom-input">
                                            @foreach($currencies as $curr)
                                                @php($isCurrent = ($paymentEdit->currency_code == $curr->code))
                                                <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ $isCurrent ? 'selected' : '' }}>
                                                    {{ $curr->code }} - {{ $curr->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-explanation">واحد پولی معامله.</small>
                                    </div>

                                    <div class="form-group mb-4" id="exchange_rate_container_edit">
                                        <label class="field-label">نرخ تبادله به دالر (Exchange Rate to USD)</label>
                                        <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate_edit" value="{{ $paymentEdit->exchange_rate }}" class="form-control custom-input h5 font-weight-bold text-center" placeholder="1.00000000">
                                        <small class="field-explanation">ارزش ۱ واحد از این اسعار را به دالر وارد کنید.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">نوع معامله</label>
                                        <select name="type" id="payment_type_edit" class="form-control custom-input font-weight-bold">
                                            <option value="رسید" {{ $paymentEdit->type == 'رسید' ? 'selected' : '' }} class="text-success">رسید (Received)</option>
                                            <option value="گرفت" {{ $paymentEdit->type == 'گرفت' ? 'selected' : '' }} class="text-danger">گرفت (Sent)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Column 2: Ref & Date -->
                                <div class="col-lg-4 col-md-6 border-left">
                                    <h6 class="form-section-title"><i class="fa fa-calendar"></i> تاریخ و انوایس</h6>
                                    
                                    <div class="form-group mb-4">
                                        <label class="field-label">انوایس نمبر</label>
                                        <select name="invoice_number" class="form-control custom-input">
                                            <option value="نقد">نقد</option>
                                            @foreach($invoice_numbers as $ch)
                                                <option value="{{$ch->invoice_no}}" {{ $paymentEdit->invoice_number == $ch->invoice_no ? 'selected' : '' }}>{{$ch->invoice_no}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">تاریخ</label>
                                        <input type="date" name="date" class="form-control custom-input text-center" value="{{$paymentEdit->date}}" required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="field-label">توضیحات</label>
                                        <textarea name="description" rows="3" class="form-control custom-input" required>{{$paymentEdit->description}}</textarea>
                                    </div>
                                </div>

                                <!-- Column 3: Update & Calculation -->
                                <div class="col-lg-4 col-md-12">
                                    <h6 class="form-section-title"><i class="fa fa-refresh text-primary"></i> محاسبه مجدد (Update Preview)</h6>
                                    <div id="normalization_preview_container_edit" class="normalization-box-edit p-4 text-center mb-4 shadow-sm">
                                        <div class="text-muted small mb-2">ارزش بروز شده در دفتر کل</div>
                                        <div class="h2 font-weight-bold text-primary mb-0">$<span id="final_usd_value_edit">0.00</span></div>
                                    </div>

                                    <div class="row p-3 mb-3 mx-1 bg-light rounded border">
                                        <div class="col-lg-12 mb-3">
                                            <label class="field-label text-info"><i class="fa fa-university"></i> حسابات مالی (Accounting)</label>
                                            <select name="override_debit_account_id" id="override_debit_account_id_edit" class="form-control form-control-sm mb-2 select2-simple">
                                                @foreach($allowedDebitAccounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ (($paymentEdit->override_debit_account_id ?? $mapping->debit_account_id) == $acc->id) ? 'selected' : '' }}>
                                                        بدهکار: {{ $acc->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select name="override_credit_account_id" id="override_credit_account_id_edit" class="form-control form-control-sm select2-simple">
                                                @foreach($allowedCreditAccounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ (($paymentEdit->override_credit_account_id ?? $mapping->credit_account_id) == $acc->id) ? 'selected' : '' }}>
                                                        بستانکار: {{ $acc->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12">
                                             <button class="btn btn-warning btn-block btn-lg shadow-sm font-weight-bold" type="submit"><i class="fa fa-save"></i> بروزرسانی نهایی</button>
                                             <a href="/dashboard/customer-payments/{{$customer->id}}" class="btn btn-block btn-link text-muted mt-2">انصراف (Cancel)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Details Tab Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card">
                <div class="card-header-premium p-0 d-flex justify-content-between align-items-center flex-row-reverse">
                    <ul class="nav nav-tabs border-0 pr-0" id="customerDetailTabs" role="tablist" style="padding: 10px 15px 0 15px;">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold text-white" id="cash-tab" data-toggle="tab" href="#cash-ledger" role="tab" style="background: transparent; border: none; border-bottom: 3px solid #ffffff; padding: 15px 20px;">
                                <i class="fa fa-money"></i> معاملات نقدی (Cash Ledger)
                            </a>
                        </li>
                                                <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="sales-tab" data-toggle="tab" href="#sales-invoices" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-shopping-cart"></i> انوایس‌های فروش قالین (Sales Invoices)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="recon-tab" data-toggle="tab" href="#customer-reconciliation" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-undo"></i> تصفیه‌ها (Reconciliations)
                            </a>
                        </li>
                    </ul>
                    <div id="exportButton" class="ml-3 hideOnPrint"></div>
                </div>

                <div class="card-body p-0 tab-content" id="customerDetailTabsContent">
                    <!-- Tab 1: Cash Ledger -->
                    <div class="tab-pane fade show active" id="cash-ledger" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0" id="customer_payments">
                                <thead>
                                    <tr class="text-right">
                                        <th>نمبر معامله</th>
                                        <th>مقدار پرداخت (اسعار اصلی)</th>
                                        <th>نرخ تبادله</th>
                                        <th>معادل دالر (USD)</th>
                                        <th>نوعیت</th>
                                        <th>انوایس نمبر</th>
                                        <th>تفصیلات</th>
                                        <th>تاریخ</th>
                                        <th>حالت</th>
                                        <th class="hideOnPrint text-center">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $pa)
                                    <tr class="text-right ur{{$pa->id}}">
                                        <td>PAY-{{ $pa->id }}</td>
                                        <td class="font-weight-bold" dir="ltr">
                                            {{ number_format($pa->original_amount ?? ($pa->amount > 0 ? $pa->amount : $pa->amount_af), 2) }}
                                            <span class="badge badge-light border text-dark font-weight-normal">{{ $pa->currency_code ?? ($pa->amount > 0 ? 'USD' : 'AFN') }}</span>
                                        </td>
                                        <td dir="ltr">{{ number_format($pa->exchange_rate ?? ($pa->amount > 0 ? 1.0 : (1 / ($pa->dollar_rate > 0 ? $pa->dollar_rate : 1))), 8) }}</td>
                                        <td class="font-weight-bold text-primary" dir="ltr">
                                            ${{ number_format($pa->base_amount ?? ($pa->amount > 0 ? $pa->amount : ($pa->amount_af * ($pa->exchange_rate ?? 1.0))), 2) }}
                                        </td>
                                        <td>
                                            @if($pa->type == 'رسید')
                                                <span class="badge badge-success px-2 py-1">رسید</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1">گرفت</span>
                                            @endif
                                        </td>
                                        @if($pa->invoice_number == 'نقد')
                                            <td>نقد</td>
                                        @else
                                            <td>
                                                <a href="/dashboard/invoices/search-invoice-number/{{$pa->invoice_number}},{{$pa->customer_id}}">&nbsp; {{$pa->invoice_number}}</a>
                                            </td>
                                        @endif
                                        <td>{{$pa->description}}</td>
                                        <td>{{$pa->date}}</td>
                                        <td>
                                            @if($pa->status == 0)
                                                <span class="status-badge bg-warning text-dark">درخواست تایید نشده</span>
                                            @else
                                                <span class="status-badge bg-success text-white">تایید شده</span>
                                            @endif
                                        </td>
                                        <td class="hideOnPrint text-center">
                                            @can('manage_customer_payments')
                                                @if(Carbon\Carbon::parse($pa->date)->gt(Carbon\Carbon::parse($lockDate)))
                                                    <a href="/dashboard/customer-payments/{{$pa->id}}/edit" class="btn btn-sm btn-info">ویرایش</a>
                                                    @can('cancel_customer_payment')
                                                    <button onclick="deletePayment({{$pa->id}}, {{$pa->customer_id}})" class="btn btn-danger btn-sm">حذف</button>
                                                    @endcan
                                                @else
                                                    <span class="badge badge-secondary"><i class="fa fa-lock"></i> قفل شده</span>
                                                @endif
                                                @if($pa->ledger_transaction_id)
                                                    <a href="{{ route('accounting.journals.show', $pa->ledger_transaction_id) }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-book"></i> روزنامچه مالی</a>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    @foreach($currencyTotals as $code => $totals)
                                    <tr class="text-right">
                                        <th colspan="3" class="text-right font-weight-bold">خلاصه {{ $code }} ({{ $code }} Summary)</th>
                                        <td colspan="2" class="text-success"><strong>رسیدات: {{ number_format($totals->total_received, 2) }} {{ $code }}</strong></td>
                                        <td colspan="2" class="text-danger"><strong>گرفت‌ها: {{ number_format($totals->total_sent, 2) }} {{ $code }}</strong></td>
                                        @php($balance = $totals->total_received - $totals->total_sent)
                                        <td colspan="3" class="text-left font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            بیلانس: {{ number_format($balance, 2) }} {{ $code }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr style="background: #e3f2fd;" class="text-right">
                                        <th colspan="3" class="text-right font-weight-bold">مجموع کل (Base USD)</th>
                                        <td colspan="2" class="text-success"><strong>$ {{ number_format($totalBaseReceived, 2) }}</strong></td>
                                        <td colspan="2" class="text-danger"><strong>$ {{ number_format($totalBaseSent, 2) }}</strong></td>
                                        @php($baseBalance = $totalBaseReceived - $totalBaseSent)
                                        <td colspan="3" class="text-left font-weight-bold {{ $baseBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                                            بیلانس نهایی: $ {{ number_format($baseBalance, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Sales Invoices -->
                    <div class="tab-pane fade" id="sales-invoices" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>نمبر انوایس (Invoice No)</th>
                                        <th>قیمت کل (Total Price)</th>
                                        <th>پرداخت شده (Paid)</th>
                                        <th>باقیمانده (Remaining)</th>
                                        <th>حالت ویرایش (Edit Status)</th>
                                        <th>حالت تصفیه (Payment Status)</th>
                                        <th class="hideOnPrint text-center">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesInvoices as $inv)
                                    <tr class="text-right">
                                        <td>{{ $inv->invoice_date }}</td>
                                        <td><strong>{{ $inv->invoice_no }}</strong></td>
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
                                        <td class="hideOnPrint text-center">
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
                                        <td colspan="8" class="text-center py-4">هیچ انوایس فروشی برای این مشتری یافت نشد.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Reconciliations -->
                    <div class="tab-pane fade" id="customer-reconciliation" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0 text-right">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>سند پرداخت (Payment)</th>
                                        <th>تخصیص به انوایس (Allocated Invoice)</th>
                                        <th>مبلغ پرداختی (Amount)</th>
                                        <th>ارز (Currency)</th>
                                        <th>نرخ ارز (Exchange Rate)</th>
                                        <th>معادل دالر (Base USD)</th>
                                        <th class="hideOnPrint text-center">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allocatedPayments as $allocPay)
                                    <tr class="text-right">
                                        <td>{{ $allocPay->date }}</td>
                                        <td>
                                            <span class="font-weight-bold">PAY-{{ $allocPay->id }}</span><br>
                                            <small class="text-muted">{{ $allocPay->description }}</small>
                                        </td>
                                        <td>
                                            @foreach($allocPay->allocations as $allocation)
                                                <span class="badge badge-info mb-1">
                                                    انوایس {{ $allocation->invoice->invoice_no ?? 'N/A' }} 
                                                    ($ {{ number_format($allocation->amount_applied, 2) }})
                                                </span><br>
                                            @endforeach
                                        </td>
                                        <td class="font-weight-bold" style="direction: ltr;">{{ number_format($allocPay->original_amount ?? ($allocPay->amount > 0 ? $allocPay->amount : $allocPay->amount_af), 2) }}</td>
                                        <td><span class="badge badge-light border text-dark">{{ $allocPay->currency_code ?? ($allocPay->amount > 0 ? 'USD' : 'AFN') }}</span></td>
                                        <td class="text-muted" style="direction: ltr;">{{ number_format($allocPay->exchange_rate ?? ($allocPay->amount > 0 ? 1.0 : (1 / ($allocPay->dollar_rate > 0 ? $allocPay->dollar_rate : 1))), 4) }}</td>
                                        <td class="font-weight-bold text-primary" style="direction: ltr;">$ {{ number_format($allocPay->base_amount ?? ($allocPay->amount > 0 ? $allocPay->amount : ($allocPay->amount_af * ($allocPay->exchange_rate ?? 1.0))), 2) }}</td>
                                        <td class="hideOnPrint text-center">
                                            @can('cancel_customer_payment')
                                            <button type="button" onclick="deletePayment({{$allocPay->id}}, {{$allocPay->customer_id}})" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i> ابطال
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">هیچ پرداخت تخصیص یافته‌ای یافت نشد.</td>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.select2-simple').select2({ width: '100%' });

        // TAB SWITCHING WORKFLOW
                // Accounting Data injected from backend
        const accountsData = {
            'رسید': {
                debit: [
                    @foreach($allowedDebitAccounts as $acc)
                    { id: {{ $acc->id }}, text: "بدهکار: {{ $acc->account_name }}", selected: {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ],
                credit: [
                    @foreach($allowedCreditAccounts as $acc)
                    { id: {{ $acc->id }}, text: "بستانکار: {{ $acc->account_name }}", selected: {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ]
            },
            'گرفت': {
                debit: [
                    @foreach($allowedDebitAccountsOut as $acc)
                    { id: {{ $acc->id }}, text: "بدهکار: {{ $acc->account_name }}", selected: {{ ($mappingOut && $mappingOut->debit_account_id == $acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ],
                credit: [
                    @foreach($allowedCreditAccountsOut as $acc)
                    { id: {{ $acc->id }}, text: "بستانکار: {{ $acc->account_name }}", selected: {{ ($mappingOut && $mappingOut->credit_account_id == $acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ]
            }
        };

        function updateAccountingDropdowns(type, isEdit = false) {
            const data = accountsData[type];
            if (!data) return;

            const debitSelect = isEdit ? $('#override_debit_account_id_edit') : $('#override_debit_account_id');
            const creditSelect = isEdit ? $('#override_credit_account_id_edit') : $('#override_credit_account_id');

            debitSelect.empty();
            data.debit.forEach(acc => {
                debitSelect.append(new Option(acc.text, acc.id, false, acc.selected));
            });

            creditSelect.empty();
            data.credit.forEach(acc => {
                creditSelect.append(new Option(acc.text, acc.id, false, acc.selected));
            });
            
            debitSelect.trigger('change.select2');
            creditSelect.trigger('change.select2');
        }

        $('#payment_type').on('change', function() {
            updateAccountingDropdowns($(this).val(), false);
        });
        
        $('#payment_type_edit').on('change', function() {
            updateAccountingDropdowns($(this).val(), true);
        });

        // Initialize on load
        if ($('#payment_type').length) {
            updateAccountingDropdowns($('#payment_type').val(), false);
        }

        $('#customerDetailTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
            $('#customerDetailTabs a').removeClass('text-white').addClass('text-white-50');
            $(this).removeClass('text-white-50').addClass('text-white');
        });

        // Table Export
        $("#customer_payments").tableExport({
            headers: true,
            footers: true,
            formats: ["xlsx"],
            filename: "customer_payments_{{ $customer->id }}",
            bootstrap: true,
            position: "bottom",
            ignoreCols: 9,
            trimWhitespace: true,
            RTL: true,
            sheetname: "Payments"
        });
        var $buttons = $('#customer_payments').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });

    function deletePayment(id, customer_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عمل قابل بازگشت نیست!",
            icon: "warning",
            buttons: {
                cancel: "نخیر",
                confirm: {text: 'بلی، حذف شود', className: 'btn-danger'}
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/customer-payments/' + id,
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" });
                            setTimeout(() => {
                                window.location = '/dashboard/customer-payments/' + customer_id;
                            }, 1000);
                        } else {
                            swal("خطا در حذف!", { icon: "error" });
                        }
                    }
                });
            }
        });
    }

    // Invoice Matching Logic
    $(document).ready(function() {
        const customerId = "{{$customer->id}}";
        window.pendingAllocation = null;

        if (customerId) {
            fetchOutstandingInvoices(customerId);
        }

        function fetchOutstandingInvoices(id) {
            $.ajax({
                url: "{{ route('dashboard.customer_payments.get_outstanding') }}",
                data: { customer_id: id },
                success: function(data) {
                    if (data.invoices.length > 0) {
                        $('#invoice_allocation_section').show();
                        let html = '';
                        data.invoices.forEach(inv => {
                            html += `
                                <tr class="text-right">
                                    <td>${inv.invoice_no}</td>
                                    <td>${inv.invoice_date}</td>
                                    <td>$${inv.total_amount}</td>
                                    <td><b class="text-danger">$${inv.remaining_balance}</b></td>
                                    <td>
                                        <input type="number" step="0.01" 
                                            name="allocations[${inv.id}]" 
                                            class="form-control form-control-sm allocation-input text-center" 
                                            max="${inv.remaining_balance}" 
                                            data-invoiceno="${inv.invoice_no}" 
                                            placeholder="0.00" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </td>
                                </tr>
                            `;
                        });
                        $('#invoice_list_body').html(html);

                        // If a pending allocation was stored, apply it now
                        if (window.pendingAllocation) {
                            const allocationInput = $(`input[name="allocations[${window.pendingAllocation.id}]"]`);
                            if (allocationInput.length > 0) {
                                $('.allocation-input').val('');
                                allocationInput.val(window.pendingAllocation.balance.toFixed(2)).trigger('input');
                            }
                        }
                        
                        // Auto-fill allocations now that inputs are loaded
                        updateNormalizationPreview();
                    } else {
                        $('#invoice_allocation_section').hide();
                    }
                }
            });
        }

        // Initialize exchange rate fields and handle live updates
        function initExchangeRateField() {
            const dropdown = $('#currency_id, #currency_id_edit');
            dropdown.each(function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.length === 0) return;
                const code = selectedOption.data('code');
                const rateInput = $(this).attr('id') === 'currency_id' ? $('#exchange_rate') : $('#exchange_rate_edit');
                
                if (!rateInput.val()) {
                    rateInput.val(selectedOption.data('rate'));
                }
                
                if (code === 'USD') {
                    rateInput.prop('readonly', true);
                } else {
                    rateInput.prop('readonly', false);
                }
            });
        }

        $('#currency_id, #currency_id_edit').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const rate = selectedOption.data('rate');
            const code = selectedOption.data('code');
            
            const rateInput = $(this).attr('id') === 'currency_id' ? $('#exchange_rate') : $('#exchange_rate_edit');
            rateInput.val(rate);
            
            if (code === 'USD') {
                rateInput.prop('readonly', true);
            } else {
                rateInput.prop('readonly', false);
            }
            
            updateNormalizationPreview();
        });

        // Live Normalization Preview
        const amountInput = $('#original_amount, #original_amount_edit');
        const previewContainer = $('#normalization_preview_container, #normalization_preview_container_edit');
        const finalUsdSpan = $('#final_usd_value, #final_usd_value_edit');

        function updateNormalizationPreview() {
            const amount = parseFloat(amountInput.val()) || 0;
            const selectedOption = $('#currency_id option:selected, #currency_id_edit option:selected');
            if (selectedOption.length === 0) return;
            const code = selectedOption.data('code');
            
            const rateInput = $('#exchange_rate, #exchange_rate_edit');
            const rate = parseFloat(rateInput.val()) || 0;

            if (amount <= 0) {
                previewContainer.hide();
                $('.allocation-input').val('');
                return;
            }

            let finalUsd = 0;
            if (code === 'USD') {
                finalUsd = amount;
            } else {
                finalUsd = amount * rate;
            }

            finalUsdSpan.text(new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(finalUsd));
            previewContainer.fadeIn(200);
            
            autoFillAllocations(finalUsd);
        }

        function autoFillAllocations(totalUsd) {
            $('.allocation-input').val(''); // Clear all first
            let remainingToAllocate = totalUsd;

            // 1. Check if an invoice is explicitly selected from the dropdown
            const selectedInvoiceNo = $('#invoice_number_select').val();
            
            // If Cash (نقد) is selected, DO NOT auto-allocate to any invoices. It should remain a pure cash ledger entry.
            if (selectedInvoiceNo === 'نقد') {
                return;
            }

            if (selectedInvoiceNo) {
                const specificInput = $(`.allocation-input[data-invoiceno="${selectedInvoiceNo}"]`);
                if (specificInput.length > 0) {
                    const maxBalance = parseFloat(specificInput.attr('max')) || 0;
                    const allocateToThis = Math.min(remainingToAllocate, maxBalance);
                    if (allocateToThis > 0) {
                        specificInput.val(allocateToThis.toFixed(2));
                        remainingToAllocate -= allocateToThis;
                    }
                }
            }

            // 2. Distribute any remaining amount top-down (FIFO) to other invoices
            $('.allocation-input').each(function() {
                if (remainingToAllocate <= 0.001) return;
                
                // Skip if already filled
                if ($(this).val() !== '') return; 

                const maxBalance = parseFloat($(this).attr('max')) || 0;
                const allocateToThis = Math.min(remainingToAllocate, maxBalance);
                
                if (allocateToThis > 0) {
                    $(this).val(allocateToThis.toFixed(2));
                    remainingToAllocate -= allocateToThis;
                }
            });
        }

        amountInput.on('input', updateNormalizationPreview);
        $('#exchange_rate, #exchange_rate_edit').on('input', updateNormalizationPreview);
        $('#invoice_number_select').on('change', updateNormalizationPreview);
        
        // Trigger initially
        initExchangeRateField();
        updateNormalizationPreview();

        // QUICK SETTLEMENT ALLOCATION WORKFLOW
        $(document).on('click', '.quick-pay-btn', function () {
            const docId = $(this).data('id');
            const docNo = $(this).data('no');
            const balanceUsd = parseFloat($(this).data('balance')) || 0;

            // Set pending allocation in global memory first (to prevent race conditions)
            window.pendingAllocation = { id: docId, balance: balanceUsd };

            // Find USD option and select it
            const usdVal = $('#currency_id option').filter(function() {
                return $(this).data('code') === 'USD';
            }).val();
            
            if (usdVal) {
                $('#currency_id').val(usdVal).trigger('change');
            }

            // Set amount to outstanding USD balance
            $('#original_amount').val(balanceUsd.toFixed(2)).trigger('input');
            
            // Set invoice reference dropdown
            if ($("#invoice_number_select option[value='" + docNo + "']").length > 0) {
                $('#invoice_number_select').val(docNo).trigger('change');
            } else {
                $('#invoice_number_select').val('نقد').trigger('change');
            }

            // Set transaction type and description note
            $('#payment_type').val('رسید').trigger('change'); // Payment Received
            $('#payment_description').val(`بابت دریافت پول انوایس فروش قالین شماره ${docNo}`);
            
            // Try to fill allocation input immediately if already loaded, otherwise reload them
            const allocationInput = $(`input[name="allocations[${docId}]"]`);
            if (allocationInput.length > 0) {
                $('.allocation-input').val('');
                allocationInput.val(balanceUsd.toFixed(2)).trigger('input');
            } else {
                fetchOutstandingInvoices(customerId);
            }

            $('#allocation_doc_display').text(`انوایس شماره ${docNo} (باقیمانده: $${balanceUsd.toFixed(2)})`);
            $('#allocation_info_box').fadeIn();
            updateNormalizationPreview();

            // Smooth Scroll to form
            $('html, body').animate({
                scrollTop: $("#customer_payment").offset().top - 20
            }, 600);
        });

        // CANCEL ALLOCATION ACTION
        $('#btn_cancel_allocation').on('click', function () {
            window.pendingAllocation = null;
            $('#allocation_info_box').fadeOut();
            $('#original_amount').val('').trigger('input');
            $('#payment_description').val('');
            $('.allocation-input').val('');
            $('#invoice_number_select').val('نقد').trigger('change');
            updateNormalizationPreview();
        });

        // MANUAL GL OVERRIDES TOGGLE
        function toggleOverrideAccounts() {
            const type = $('#payment_type, #payment_type_edit').val();
            if (type === 'رسید') {
                $('#override_debit_container').show();
                $('#override_credit_container').show();
            } else {
                $('#override_debit_container').show();
                $('#override_credit_container').show();
            }
        }
        $('#payment_type, #payment_type_edit').on('change', toggleOverrideAccounts);
        toggleOverrideAccounts();
    });
</script>
@endsection