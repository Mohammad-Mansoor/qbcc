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
        background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
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
        color: #2e7d32;
        font-weight: 700;
        border-bottom: 2px solid #e8f5e9;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .form-section-title i { margin-left: 10px; color: #43a047; }
    
    .custom-input {
        border-radius: 10px;
        border: 2px solid #e8f5e9;
        padding: 12px 15px;
        transition: all 0.2s;
        height: auto;
    }
    .custom-input:focus {
        border-color: #43a047;
        box-shadow: 0 0 0 0.2rem rgba(67, 160, 71, 0.1);
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
        background: #f1f8e9;
        border-right: 4px solid #43a047;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .truth-label { font-size: 0.8rem; color: #558b2f; font-weight: 600; }
    .truth-value { font-size: 1.4rem; color: #2e7d32; font-weight: 800; font-family: 'Courier New', monospace; }

    .btn-premium {
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    .btn-premium-success {
        background: #43a047;
        color: white;
        border: none;
    }
    .btn-premium-success:hover {
        background: #2e7d32;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
    }
    
    /* Table Styling */
    .premium-table thead th {
        background: #f8f9fa;
        color: #2e7d32;
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

    .accounting-override-box {
        background: #fafafa;
        border: 1px dashed #cfd8dc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    @media print {
        .pcoded-navbar, .header-chat, .pcoded-header, .nav-tabs, 
        .hideOnPrint, #forensicKachaeeForm, .btn, .card-header-premium button,
        .btn-group, #exportButton, #kachaeeTabs, .container-fluid > .row:first-child,
        .profile-card-parent, .form-card-parent {
            display: none !important;
        }
        
        .pcoded-main-container, .card, .card-body, #print-area {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            box-shadow: none !important;
        }
        
        .print-header {
            display: block !important;
        }
        
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        table th, table td {
            border: 1px solid #000000 !important;
            color: #000000 !important;
            font-size: 12px !important;
        }
    }
</style>

<div class="container-fluid mt-4" id="kachaee-payment">
    <!-- Header Section -->
    <div class="row mb-4 hideOnPrint">
        <div class="col-md-8">
            <h3 class="font-weight-bold text-dark">
                <i class="fa fa-users text-success"></i> 
                پرداخت های بخش کچایی (Kachaee Payments)
            </h3>
            <p class="text-muted">مدیریت دستمزدها و تصفیه حساب تیم های پروسس اولیه فرش</p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <button class="btn btn-outline-success btn-sm" onclick="window.print()">
                    <i class="fa fa-print"></i> چاپ گزارش (Print)
                </button>
                <a href="/dashboard/kachaee-payments-all/{{$team->id}}" class="btn btn-outline-info btn-sm">
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
        <!-- Team Profile Card -->
        <div class="col-lg-3 profile-card-parent hideOnPrint">
            <div class="premium-card text-center p-4">
                <div class="mb-3">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
                <h4 class="font-weight-bold mb-1">{{$team->name}}</h4>
                <p class="text-success font-weight-bold mb-3">ACCOUNT #: {{$team->id}}</p>
                <hr>
                <div class="text-right">
                    <p class="mb-1"><small class="text-muted">آدرس:</small><br><strong>{{$team->address}}</strong></p>
                    <p class="mb-0"><small class="text-muted">تماس:</small><br><strong>{{$team->contact_no}} <i class="fa fa-phone small"></i></strong></p>
                </div>
            </div>
        </div>

        <!-- Forensic Payment Form -->
        <div class="col-lg-9 form-card-parent hideOnPrint">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5><i class="fa fa-calculator mr-2"></i> {{ $paymentEdit ? 'ویرایش سند مالی (Edit Wage Record)' : 'ثبت دستمزد جدید (New Wage Entry)' }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ $paymentEdit ? '/dashboard/kachaee-payments/'.$paymentEdit->id : '/dashboard/kachaee-payments' }}" method="post" id="forensicKachaeeForm">
                        @csrf
                        @if($paymentEdit) @method('PUT') @endif
                        <input type="hidden" name="team_id" value="{{$team->id}}">

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

                                <div class="form-group mb-4">
                                    <label class="field-label">نرخ تبادله (Rate)</label>
                                    <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control custom-input font-weight-bold bg-light" 
                                           value="{{ $paymentEdit ? $paymentEdit->exchange_rate : ($currencies->where('code', 'USD')->first()->exchange_rate ?? 1.0) }}" readonly>
                                </div>

                                <!-- Live Truth Preview Box -->
                                <div class="truth-preview-box mb-4">
                                    <span class="truth-label"><i class="fa fa-shield"></i> معادل دالر (USD Equivalent):</span><br>
                                    <span class="truth-value" id="usd_truth_preview">$ 0.0000</span>
                                </div>
                            </div>

                            <!-- Column 2: Transaction Details -->
                            <div class="col-lg-4 col-md-6 border-left">
                                <h6 class="form-section-title"><i class="fa fa-exchange"></i> نوع و مرجع (Ref & Type)</h6>

                                <div class="form-group mb-4">
                                    <label class="field-label">نوع معامله (Entry Type)</label>
                                    <select name="type" id="payment_type" class="form-control custom-input font-weight-bold">
                                        <option value="رسید" class="text-success" {{ ($paymentEdit && $paymentEdit->type == 'رسید') ? 'selected' : '' }}>رسید / تصفیه (Balance In)</option>
                                        <option value="گرفت" class="text-danger" {{ ($paymentEdit && $paymentEdit->type == 'گرفت') ? 'selected' : '' }}>گرفت / علی الحساب (Payment Out)</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">کچایی نمبر (Processing Ref)</label>
                                    <select name="kachaee_number" id="kachaee_number" class="form-control custom-input">
                                        <option value="نقد">نقد (General)</option>
                                        @foreach($kachaee_numbers as $ch)
                                            <option value="{{$ch->kachaee_number}}" {{ ($paymentEdit && $paymentEdit->kachaee_number == $ch->kachaee_number) ? 'selected' : '' }}>{{$ch->kachaee_number}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">تاریخ (Date)</label>
                                    <input type="date" name="date" class="form-control custom-input" value="{{ $paymentEdit ? $paymentEdit->date : date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Column 3: Accounting & Audit -->
                            @php
                                $defaultMapping = ($paymentEdit && $paymentEdit->type == 'گرفت') ? $mappingOut : $mappingIn;
                            @endphp
                            <div class="col-lg-4">
                                <h6 class="form-section-title"><i class="fa fa-university"></i> تنظیمات حسابی (Accounting)</h6>
                                
                                <div class="form-group mb-3">
                                    <label class="field-label small">حساب بدهکار (Debit)</label>
                                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control custom-input">
                                        @foreach($allowedDebitAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ (isset($currentDebitAccountId) && $currentDebitAccountId == $acc->id) || (!isset($currentDebitAccountId) && $defaultMapping && $defaultMapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="field-label small">حساب بستانکار (Credit)</label>
                                    <select name="override_credit_account_id" id="override_credit_account_id" class="form-control custom-input">
                                        @foreach($allowedCreditAccounts as $acc)
                                            <option value="{{ $acc->id }}" {{ (isset($currentCreditAccountId) && $currentCreditAccountId == $acc->id) || (!isset($currentCreditAccountId) && $defaultMapping && $defaultMapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="field-label">توضیحات (Description)</label>
                                    <textarea name="description" rows="2" class="form-control custom-input" required>{{ $paymentEdit ? $paymentEdit->description : '' }}</textarea>
                                </div>

                                <button class="btn btn-premium btn-premium-success btn-block shadow-sm" type="submit" id="submit-payment-btn">
                                    <i class="fa fa-save"></i> {{ $paymentEdit ? 'بروزرسانی سند (Update)' : 'ثبت نهایی (Confirm)' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Ledger & Repairs Section -->
    <div class="row mt-4">
        <div class="col-12">
            <ul class="nav nav-tabs mb-4 hideOnPrint" id="kachaeeTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="payments-tab" data-toggle="tab" href="#payments" role="tab"><i class="fa fa-money mr-1"></i> ریز معاملات و دستمزدها (Wage Ledger)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="repairs-tab" data-toggle="tab" href="#repairs" role="tab"><i class="fa fa-wrench mr-1"></i> مصارف و کارهای کچایی (Repairs & Costs)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="statement-tab" data-toggle="tab" href="#statement" role="tab"><i class="fa fa-file-text-o mr-1"></i> صورت حساب تفصیلی (GL Statement)</a>
                </li>
            </ul>

            <div class="tab-content" id="kachaeeTabContent">
                <!-- Tab 1: Payments List -->
                <div class="tab-pane fade show active" id="payments" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);">
                            <h5><i class="fa fa-list-alt mr-2"></i> ریز معاملات و دستمزدها (Wage Ledger)</h5>
                            <div id="exportButton"></div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0" id="kachaee_payment_table">
                                    <thead>
                                        <tr class="text-right">
                                            <th>تاریخ (Date)</th>
                                            <th>شرح (Description)</th>
                                            <th>کچایی نمبر (Ref)</th>
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
                                                    @if($pa->kachaee_number == 'نقد') نقد @else {{$pa->kachaee_number}} @endif
                                                </span>
                                            </td>
                                            <td class="text-center font-weight-bold text-success">{{ $pa->currency_code ?: ($pa->amount > 0 ? 'USD' : 'AFN') }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">
                                                {{ number_format($pa->original_amount ?: ($pa->amount ?: $pa->amount_af), 2) }}
                                            </td>
                                            <td class="text-muted small" style="direction: ltr;">{{ number_format($pa->exchange_rate ?: $pa->dollar_rate, 8) }}</td>
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
                                                        <a href="/dashboard/kachaee-payments/{{$pa->id}}/edit" class="btn btn-sm btn-outline-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <button type="button" onclick="deletePayment({{$pa->id}}, {{$pa->team_id}})" class="btn btn-sm btn-outline-danger">
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
                                            @php $balance = $totals->total_received - $totals->total_sent; @endphp
                                            <td colspan="2" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                بیلانس: {{ number_format(abs($balance), 2) }} {{ $code }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="background: #e8f5e9;">
                                            <th colspan="3" class="text-right text-success"><b>مجموع کل بیلانس (Base USD)</b></th>
                                            <td colspan="2" class="text-success text-right"><b>$ {{ number_format($totalBaseReceived, 2) }}</b></td>
                                            <td colspan="2" class="text-danger text-right"><b>$ {{ number_format($totalBaseSent, 2) }}</b></td>
                                            @php $baseBalance = $totalBaseReceived - $totalBaseSent; @endphp
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

                <!-- Tab 2: Repairs List -->
                <div class="tab-pane fade" id="repairs" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center">
                            <h5><i class="fa fa-list mr-2"></i> جزئیات کارهای کچایی انجام شده</h5>
                            <span class="badge badge-light p-2 font-weight-bold text-success" style="font-size: 0.9rem;">
                                مجموع مصارف: $ {{ number_format($totalBaseRepairs, 2) }}
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ</th>
                                            <th>کچایی نمبر</th>
                                            <th>نمبر قالین</th>
                                            <th>هزینه کل</th>
                                            <th>پرداخت شده</th>
                                            <th>باقی‌مانده</th>
                                            <th>وضعیت</th>
                                            <th>واحد پولی</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($repairs as $rep)
                                        <tr>
                                            <td>{{ $rep->date }}</td>
                                            <td><span class="badge badge-light border">{{ $rep->kachaee_number }}</span></td>
                                            <td>{{ $rep->carpet->carpet_no ?? 'N/A' }}</td>
                                            <td class="font-weight-bold">$ {{ number_format($rep->total_cost, 2) }}</td>
                                            <td class="text-success">$ {{ number_format($rep->total_paid, 2) }}</td>
                                            <td class="text-danger font-weight-bold">$ {{ number_format($rep->remaining_balance, 2) }}</td>
                                            <td>
                                                @if($rep->payment_status === 'paid')
                                                    <span class="badge badge-success">تصفیه کامل</span>
                                                @elseif($rep->payment_status === 'partial')
                                                    <span class="badge badge-warning">تصفیه قسمی</span>
                                                @else
                                                    <span class="badge badge-danger">پرداخت نشده</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">{{ $rep->currency_code }}</td>
                                            <td>
                                                @if($rep->remaining_balance > 0)
                                                <button type="button" class="btn btn-sm btn-success pay-repair-btn" 
                                                        data-ref="{{ $rep->kachaee_number }}" 
                                                        data-remaining="{{ $rep->remaining_balance }}" 
                                                        data-currency="{{ $rep->currency_code }}">
                                                    <i class="fa fa-credit-card"></i> پرداخت
                                                </button>
                                                @else
                                                <span class="text-success"><i class="fa fa-check-circle"></i> پرداخت کامل</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">هیچ کار کچایی برای این تیم ثبت نشده است.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Unified Ledger Statement -->
                <div class="tab-pane fade" id="statement" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);">
                            <h5><i class="fa fa-book mr-2"></i> صورت حساب مالی تفصیلی (GL Statement)</h5>
                            <button type="button" class="btn btn-light btn-sm font-weight-bold text-dark" onclick="printStatement()">
                                <i class="fa fa-print"></i> چاپ صورت حساب
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" id="print-area">
                                <!-- Print-only Header (Hidden on Screen) -->
                                <div class="d-none print-header text-center mb-4 mt-3">
                                    <h3 class="font-weight-bold">صورت حساب مالی تیم کچایی: {{ $team->name }}</h3>
                                    <p>تاریخ گزارش: {{ date('Y-m-d') }} | اکونت نمبر: {{ $team->id }}</p>
                                </div>
                                <table class="table premium-table table-hover text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ</th>
                                            <th>شرح معامله</th>
                                            <th>مرجع (Ref)</th>
                                            <th>بدهکار (Debit/Paid)</th>
                                            <th>طلبکار (Credit/Cost)</th>
                                            <th>بیلانس (Outstanding)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $runningBalance = 0; @endphp
                                        @forelse($ledgerStatement as $entry)
                                            @php 
                                                $debit = (float)$entry->base_debit;
                                                $credit = (float)$entry->base_credit;
                                                $runningBalance += ($credit - $debit);
                                            @endphp
                                            <tr>
                                                <td>{{ $entry->date }}</td>
                                                <td>{{ $entry->description }}</td>
                                                <td><span class="badge badge-light border">{{ $entry->reference }}</span></td>
                                                <td class="text-danger font-weight-bold">{{ $debit > 0 ? '$ ' . number_format($debit, 2) : '-' }}</td>
                                                <td class="text-success font-weight-bold">{{ $credit > 0 ? '$ ' . number_format($credit, 2) : '-' }}</td>
                                                <td class="font-weight-bold {{ $runningBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    $ {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? '(Cr)' : '(Dr)' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">هیچ تراکنش حسابی یافت نشد.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="3" class="text-right">بیلانس نهایی طلبات (Base USD)</th>
                                            <th class="text-danger">$ {{ number_format($ledgerStatement->sum('base_debit'), 2) }}</th>
                                            <th class="text-success">$ {{ number_format($ledgerStatement->sum('base_credit'), 2) }}</th>
                                            <th class="font-weight-bold text-primary" style="font-size: 1.1rem;">
                                                $ {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? 'باقی مانده (طلبکار)' : 'طلبکار (بدهکار)' }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Map of unpaid balances per reference to check on inputs
    const unpaidBalances = {};
    @foreach($repairs as $rep)
        unpaidBalances["{{ $rep->kachaee_number }}"] = parseFloat("{{ $rep->remaining_balance }}");
    @endforeach

    $(document).ready(function () {
        $('#kachaee_number').select2();
        $('#currency_id').select2();
        $('#override_debit_account_id').select2();
        $('#override_credit_account_id').select2();

        // Dynamic account selection based on payment type (رسید vs گرفت)
        const mappingInDebit = "{{ $mappingIn->debit_account_id ?? '' }}";
        const mappingInCredit = "{{ $mappingIn->credit_account_id ?? '' }}";
        const mappingOutDebit = "{{ $mappingOut->debit_account_id ?? '' }}";
        const mappingOutCredit = "{{ $mappingOut->credit_account_id ?? '' }}";

        $('#payment_type').on('change', function () {
            const type = $(this).val();
            if (type === 'رسید') {
                $('#override_debit_account_id').val(mappingInDebit).trigger('change');
                $('#override_credit_account_id').val(mappingInCredit).trigger('change');
            } else {
                $('#override_debit_account_id').val(mappingOutDebit).trigger('change');
                $('#override_credit_account_id').val(mappingOutCredit).trigger('change');
            }
        });

        // LIVE TRUTH PREVIEW & VALIDATION LOGIC
        function updateUsdPreview() {
            const amount = parseFloat($('#original_amount').val()) || 0;
            const rate = parseFloat($('#exchange_rate').val()) || 0;
            const baseAmount = (amount * rate).toFixed(4);
            
            $('#usd_truth_preview').text('$ ' + parseFloat(baseAmount).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
            
            // Validate remaining balance
            const ref = $('#kachaee_number').val();
            const type = $('#payment_type').val();
            
            $('#overpayment-warning').remove();
            $('#submit-payment-btn').prop('disabled', false);

            if (ref !== 'نقد' && type === 'گرفت' && unpaidBalances[ref] !== undefined) {
                // Determine if we are editing an existing payment to exclude it from the client-side validation logic
                const isEditing = "{{ $paymentEdit ? 'true' : 'false' }}";
                let maxAllowed = unpaidBalances[ref];
                
                if (isEditing === 'true') {
                    const originalEditVal = parseFloat("{{ $paymentEdit ? $paymentEdit->original_amount : 0 }}");
                    const originalEditRef = "{{ $paymentEdit ? $paymentEdit->kachaee_number : '' }}";
                    if (ref === originalEditRef) {
                        maxAllowed += originalEditVal;
                    }
                }

                if (amount > maxAllowed) {
                    $('#original_amount').after(
                        `<small id="overpayment-warning" class="text-danger d-block mt-1 font-weight-bold">
                            هشدار: مبلغ پرداختی از باقی‌مانده کار بیشتر است. حداکثر مجاز: ${maxAllowed.toFixed(2)}
                        </small>`
                    );
                    $('#submit-payment-btn').prop('disabled', true);
                }
            }
        }

        $('#currency_id').on('change', function() {
            const rate = $(this).find(':selected').data('rate');
            $('#exchange_rate').val(rate);
            updateUsdPreview();
        });

        $('#original_amount, #exchange_rate, #kachaee_number, #payment_type').on('input change', updateUsdPreview);
        updateUsdPreview();

        // Pay Button Click Handler
        $('.pay-repair-btn').on('click', function() {
            const refNumber = $(this).data('ref');
            const remaining = $(this).data('remaining');
            const currency = $(this).data('currency');
            
            // 1. Switch to Payments Tab
            $('#payments-tab').tab('show');
            
            // 2. Pre-fill Form Fields
            $('#original_amount').val(remaining).trigger('input');
            $('#payment_type').val('گرفت').trigger('change');
            
            if ($('#kachaee_number option[value="' + refNumber + '"]').length > 0) {
                $('#kachaee_number').val(refNumber).trigger('change');
            } else {
                const newOption = new Option(refNumber, refNumber, true, true);
                $('#kachaee_number').append(newOption).trigger('change');
            }
            
            $('#currency_id option').each(function() {
                if ($(this).text().indexOf(currency) !== -1) {
                    $('#currency_id').val($(this).val()).trigger('change');
                }
            });

            $('html, body').animate({
                scrollTop: $("#forensicKachaeeForm").offset().top - 100
            }, 500);
        });

        // Export
        $("#kachaee_payment_table").tableExport({
            formats: ["xlsx"],
            filename: "kachaee_ledger_{{ $team->id }}",
            bootstrap: true,
            position: "bottom"
        });
        var $buttons = $('#kachaee_payment_table').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });

    function printStatement() {
        window.print();
    }

    function deletePayment(id, team_id) {
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
                    url: '/dashboard/kachaee-payments/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" });
                            setTimeout(() => window.location = '/dashboard/kachaee-payments/' + team_id, 1000);
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