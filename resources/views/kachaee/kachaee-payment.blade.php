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

                                <div class="form-group mb-4" id="is_advance_group">
                                    <div class="custom-control custom-checkbox mr-sm-2 text-right">
                                        <input type="checkbox" class="custom-control-input" id="is_advance" name="is_advance" value="1"
                                            {{ ($paymentEdit && $paymentEdit->is_advance) ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_advance" style="cursor: pointer; color: #1b5e20;">
                                            به عنوان پیش‌پرداخت (Is Advance Payment)
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">اگر این گزینه را فعال کنید، مبلغ به عنوان علی‌الحساب تیم ثبت شده و بعداً قابل تخصیص به گروپ‌ها خواهد بود.</small>
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
                    <a class="nav-link font-weight-bold" id="grouped-batches-tab" data-toggle="tab" href="#grouped_batches" role="tab"><i class="fa fa-folder-open mr-1"></i> بل‌های دستمزد گروپ شده (Grouped Batches)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="kachaee-advances-tab" data-toggle="tab" href="#kachaee_advances" role="tab"><i class="fa fa-share-square-o mr-1"></i> پیش‌پرداخت‌ها (Advances)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="kachaee-reconciliation-tab" data-toggle="tab" href="#kachaee_reconciliation" role="tab"><i class="fa fa-undo mr-1"></i> تاریخچه تصفیه‌ها (Reconciliations)</a>
                </li>
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
                                            <th>نوعیت (Type)</th>
                                            <th>شرح (Description)</th>
                                            <th>کچایی نمبر (Ref)</th>
                                            <th>حسابات (Accounts)</th>
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
                                            <td>
                                                <span class="badge {{ $pa->type == 'رسید' ? 'badge-success' : 'badge-danger' }} px-2 py-1">
                                                    {{ $pa->type == 'رسید' ? 'رسید (In)' : 'گرفت (Out)' }}
                                                </span>
                                            </td>
                                            <td>{{ $pa->description }}</td>
                                            <td>
                                                <span class="badge badge-light border p-2">
                                                    @if($pa->kachaee_number == 'نقد') نقد @else {{$pa->kachaee_number}} @endif
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

                                                    $debCode = $deb ? $deb->account_code : ($pa->type == 'گرفت' ? ($pa->is_advance ? '11400' : '15000') : ($pa->is_advance ? '10100' : '10900'));
                                                    $credCode = $cred ? $cred->account_code : ($pa->type == 'گرفت' ? ($pa->is_advance ? '10100' : '10900') : ($pa->is_advance ? '11400' : '15000'));

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
                                                @if($pa->is_advance)
                                                    <br>
                                                    <span class="badge badge-info mt-1">پیش‌پرداخت ({{ $pa->payment_status == 'allocated' ? 'تخصیص شده' : ($pa->payment_status == 'partially_allocated' ? 'قسمتی تخصیص شده' : 'تخصیص نشده') }})</span>
                                                @endif
                                            </td>
                                            <td class="hideOnPrint text-center">
                                                    <div class="btn-group">
                                                        <a href="/dashboard/kachaee-payments/{{$pa->id}}/edit" class="btn btn-sm btn-outline-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        @can('cancel_kachaee_payment')
                                                        <button type="button" onclick="deletePayment({{$pa->id}}, {{$pa->team_id}})" class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-times"></i> لغو
                                                        </button>
                                                        @endcan
                                                    </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        @foreach($currencyTotals as $code => $totals)
                                        <tr>
                                            <th colspan="5" class="text-right">خلاصه {{ $code }} ({{ $code }} Summary)</th>
                                            <td colspan="2" class="text-success text-right"><b>رسید: {{ number_format($totals->total_received, 2) }}</b></td>
                                            <td colspan="2" class="text-danger text-right"><b>گرفت: {{ number_format($totals->total_sent, 2) }}</b></td>
                                            @php $balance = $totals->total_received - $totals->total_sent; @endphp
                                            <td colspan="2" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                بیلانس: {{ number_format(abs($balance), 2) }} {{ $code }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="background: #e8f5e9;">
                                            <th colspan="5" class="text-right text-success"><b>مجموع کل بیلانس (Base USD)</b></th>
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

                <!-- Tab 2: Grouped Batches -->
                <div class="tab-pane fade" id="grouped_batches" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1565c0 0%, #1e88e5 100%);">
                            <h5><i class="fa fa-folder-open mr-2"></i> بل‌های دستمزد گروپ شده (Grouped Batches)</h5>
                            <span class="badge badge-light p-2 font-weight-bold text-primary" style="font-size: 0.9rem;">
                                مجموع بل‌ها: {{ count($groupedRepairs ?? []) }} عدد
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ اولین ثبت (First Record)</th>
                                            <th>کچایی نمبر (Batch Ref)</th>
                                            <th>تعداد قالین (Carpets)</th>
                                            <th>هزینه کل (Total Cost)</th>
                                            <th>پرداخت شده (Paid)</th>
                                            <th>باقی‌مانده (Remaining)</th>
                                            <th>وضعیت پرداخت (Status)</th>
                                            <th>عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($groupedRepairs as $group)
                                        <tr>
                                            <td>{{ $group['date'] }}</td>
                                            <td>
                                                @if($group['reference'] !== 'نقد')
                                                    <a href="/dashboard/batches/{{ $group['reference'] }}/details" target="_blank" title="مشاهده صورتحساب">
                                                        <span class="badge badge-info p-2 font-weight-bold" style="cursor: pointer;">
                                                            <i class="fa fa-external-link mr-1"></i> {{ $group['reference'] }}
                                                        </span>
                                                    </a>
                                                @else
                                                    <span class="badge badge-secondary p-2 font-weight-bold">{{ $group['reference'] }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $group['total_carpets'] }} تخته</td>
                                            <td class="font-weight-bold">$ {{ number_format($group['total_cost'], 2) }}</td>
                                            <td class="text-success">$ {{ number_format($group['total_paid'], 2) }}</td>
                                            <td class="text-danger font-weight-bold">$ {{ number_format($group['remaining_balance'], 2) }}</td>
                                            <td>
                                                @if($group['payment_status'] === 'paid')
                                                    <span class="badge badge-success">تصفیه کامل</span>
                                                @elseif($group['payment_status'] === 'partial')
                                                    <span class="badge badge-warning">تصفیه قسمی</span>
                                                @else
                                                    <span class="badge badge-danger">پرداخت نشده</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($group['remaining_balance'] > 0)
                                                <button type="button" class="btn btn-sm btn-success pay-repair-btn" 
                                                        data-ref="{{ $group['reference'] }}" 
                                                        data-remaining="{{ $group['remaining_balance'] }}" 
                                                        data-currency="USD">
                                                    <i class="fa fa-credit-card"></i> تصفیه گروپ
                                                </button>
                                                @else
                                                <span class="text-success"><i class="fa fa-check-circle"></i> پرداخت کامل</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">هیچ کار کچایی برای این تیم ثبت نشده است.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Kachaee Advances -->
                <div class="tab-pane fade" id="kachaee_advances" role="tabpanel">
                    @php
                        $advances = \App\KachaeePayment::where('team_id', $team->id)
                            ->where('is_advance', true)
                            ->where('status', '!=', 2)
                            ->orderBy('date', 'DESC')
                            ->get();
                    @endphp
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);">
                            <h5><i class="fa fa-share-square-o mr-2"></i> پیش‌پرداخت‌های تیم (Kachaee Advances)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right">
                                    <thead>
                                        <tr>
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
                                        <tr>
                                            <td>{{ $adv->date }}</td>
                                            <td><strong>KCH-PAY-{{ $adv->id }}</strong></td>
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
                                                    <button type="button" class="btn btn-sm btn-success open-allocate-modal-btn" 
                                                            data-payment-id="{{ $adv->id }}"
                                                            data-currency="{{ $adv->currency_code }}"
                                                            data-remaining="{{ $adv->remaining_unallocated_amount }}"
                                                            data-exchange-rate="{{ $adv->exchange_rate }}">
                                                        <i class="fa fa-share-square-o"></i> تخصیص به گروپ
                                                    </button>
                                                    @else
                                                    <span class="text-muted mr-2">کامل تخصیص شده / تایید نشده</span>
                                                    @endif

                                                    @can('cancel_kachaee_payment')
                                                    <button type="button" onclick="deletePayment({{$adv->id}} ,{{$adv->team_id}})" class="btn btn-sm btn-outline-danger ml-1" title="لغو پیش‌پرداخت">
                                                        <i class="fa fa-ban"></i> لغو
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">هیچ پیش‌پرداختی برای این تیم یافت نشد.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Kachaee Reconciliation (Allocations History) -->
                <div class="tab-pane fade" id="kachaee_reconciliation" role="tabpanel">
                    @php
                        $kachaeeAllocations = \App\KachaeePaymentAllocation::whereHas('payment', function($q) use ($team) {
                            $q->where('team_id', $team->id)->where('status', '!=', 2);
                        })->with(['payment', 'allocatable'])->orderBy('id', 'DESC')->get();

                        $directPayments = \App\KachaeePayment::where('team_id', $team->id)
                            ->where('kachaee_number', '!=', 'نقد')
                            ->where('is_advance', 0)
                            ->where('status', '!=', 2)
                            ->orderBy('date', 'DESC')->get();
                    @endphp
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1565c0 0%, #1e88e5 100%);">
                            <h5><i class="fa fa-undo mr-2"></i> تاریخچه تخصیص و تصفیه پیش‌پرداخت‌ها (Reconciliation History)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ تخصیص (Allocation Date)</th>
                                            <th>سند پیش‌پرداخت (Source Advance)</th>
                                            <th>گروپ کچایی مقصد (Target Batch)</th>
                                            <th>نوع تراکنش (Type)</th>
                                            <th>مبلغ تخصیص (Allocated Amount)</th>
                                            <th>نرخ ارز (Exchange Rate)</th>
                                            <th>معادل دالر (Base USD Allocated)</th>
                                            <th>اکانت‌ها (Accounts)</th>
                                            <th class="hideOnPrint">عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kachaeeAllocations as $alloc)
                                        <tr>
                                            <td>{{ $alloc->created_at ? $alloc->created_at->format('Y-m-d') : '---' }}</td>
                                            <td>
                                                <a href="#" class="font-weight-bold">
                                                    KCH-PAY-{{ $alloc->kachaee_payment_id }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $alloc->payment->description ?? '' }}</small>
                                            </td>
                                            <td>
                                                @if($alloc->allocatable)
                                                    <span class="badge badge-info text-white">گروپ کچایی</span>
                                                    <strong>{{ $alloc->allocatable->reference_number }}</strong>
                                                @else
                                                    <span class="text-danger">سند حذف شده</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($alloc->payment && $alloc->payment->type == 'رسید')
                                                    <span class="badge badge-success text-white py-1 px-2"><i class="fa fa-arrow-down mr-1"></i>رسید (Receipt)</span>
                                                @else
                                                    <span class="badge badge-danger text-white py-1 px-2"><i class="fa fa-arrow-up mr-1"></i>گرفت (Payment)</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success" style="direction: ltr;">
                                                {{ number_format($alloc->allocated_amount, 2) }} {{ $alloc->payment->currency_code ?? 'USD' }}
                                            </td>
                                            <td class="text-muted small" style="direction: ltr;">{{ number_format($alloc->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">
                                                $ {{ number_format($alloc->base_allocated_amount, 2) }}
                                            </td>
                                            <td class="small" style="font-size: 0.85rem;">
                                                @php
                                                    $advPayment = $alloc->payment;
                                                    if ($advPayment) {
                                                        $advRule = ($advPayment->type == 'گرفت') ? ($advOutRule ?? null) : ($advInRule ?? null);
                                                        $advDeb = $advPayment->debitAccount ?? ($advRule->debitAccount ?? null);
                                                        $advCred = $advPayment->creditAccount ?? ($advRule->creditAccount ?? null);
                                                        $advDebCode = $advDeb ? $advDeb->account_code : ($advPayment->type == 'گرفت' ? 'Advance Debit' : 'Cash/Bank');
                                                        $advCredCode = $advCred ? $advCred->account_code : ($advPayment->type == 'گرفت' ? 'Cash/Bank' : 'Advance Credit');
                                                        $advDebName = $advDeb ? ($advDeb->account_code . ' - ' . $advDeb->account_name) : 'Advance Account';
                                                        $advCredName = $advCred ? ($advCred->account_code . ' - ' . $advCred->account_name) : 'Cash/Bank Account';
                                                    } else {
                                                        $advDebCode = 'N/A'; $advCredCode = 'N/A'; $advDebName = 'N/A'; $advCredName = 'N/A';
                                                    }
                                                    $settleDebCode = 'Exp/Payable'; // Generic placeholder for Kachaee Settlement debit
                                                    $settleCredCode = $advPayment && $advPayment->type == 'گرفت' ? $advDebCode : $advCredCode; // Reversing the advance
                                                @endphp
                                                <div class="d-flex flex-column">
                                                    <span class="text-danger" title="{{ $settleDebCode }} Debit"><i class="fa fa-minus-circle mr-1"></i> {{ $settleDebCode }}</span>
                                                    <span class="text-success" title="{{ $advCredName }} Credit"><i class="fa fa-plus-circle mr-1"></i> {{ $settleCredCode }}</span>
                                                </div>
                                            </td>
                                            <td class="hideOnPrint">
                                                @can('cancel_kachaee_payment')
                                                <button type="button" onclick="editAllocation({{ $alloc->id }}, {{ $alloc->allocated_amount }}, '{{ $alloc->payment->currency_code ?? 'USD' }}')" class="btn btn-sm btn-outline-primary shadow-sm" title="ویرایش تخصیص">
                                                    <i class="fa fa-edit"></i> ویرایش
                                                </button>
                                                <button type="button" onclick="removeAllocation({{ $alloc->id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="حذف تخصیص">
                                                    <i class="fa fa-undo"></i> لغو تخصیص
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach

                                        @foreach($directPayments as $dp)
                                        <tr>
                                            <td>{{ $dp->date ? \Carbon\Carbon::parse($dp->date)->format('Y-m-d') : '---' }}</td>
                                            <td>
                                                <a href="#" class="font-weight-bold">
                                                    KCH-PAY-{{ $dp->id }}
                                                </a>
                                                <br>
                                                <small class="text-info">پرداخت مستقیم (Direct Payment)</small>
                                                <br>
                                                <small class="text-muted">{{ $dp->description }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning text-dark">گروپ کچایی (مستقیم)</span>
                                                <strong>{{ $dp->kachaee_number }}</strong>
                                            </td>
                                            <td>
                                                @if($dp->type == 'رسید')
                                                    <span class="badge badge-success text-white py-1 px-2"><i class="fa fa-arrow-down mr-1"></i>رسید (Receipt)</span>
                                                @else
                                                    <span class="badge badge-danger text-white py-1 px-2"><i class="fa fa-arrow-up mr-1"></i>گرفت (Payment)</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success" style="direction: ltr;">
                                                {{ number_format($dp->original_amount, 2) }} {{ $dp->currency_code ?? 'USD' }}
                                            </td>
                                            <td class="text-muted small" style="direction: ltr;">{{ number_format($dp->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">
                                                $ {{ number_format($dp->base_amount, 2) }}
                                            </td>
                                            <td class="small" style="font-size: 0.85rem;">
                                                @php
                                                    $rule = ($dp->type == 'گرفت') ? ($pymtOutRule ?? null) : ($pymtInRule ?? null);
                                                    $deb = $dp->debitAccount ?? ($rule->debitAccount ?? null);
                                                    $cred = $dp->creditAccount ?? ($rule->creditAccount ?? null);
                                                    $debName = $deb ? ($deb->account_code . ' - ' . $deb->account_name) : 'Debit Account';
                                                    $credName = $cred ? ($cred->account_code . ' - ' . $cred->account_name) : 'Credit Account';
                                                @endphp
                                                <div class="d-flex flex-column">
                                                    <span class="text-danger" title="{{ $debName }}"><i class="fa fa-minus-circle mr-1"></i> {{ $deb ? $deb->account_code : 'N/A' }}</span>
                                                    <span class="text-success" title="{{ $credName }}"><i class="fa fa-plus-circle mr-1"></i> {{ $cred ? $cred->account_code : 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="hideOnPrint">
                                                @can('cancel_kachaee_payment')
                                                <a href="/dashboard/kachaee-payments/{{ $dp->id }}/edit" class="btn btn-sm btn-outline-primary shadow-sm" title="ویرایش">
                                                    <i class="fa fa-edit"></i> ویرایش
                                                </a>
                                                <button type="button" onclick="deletePayment({{ $dp->id }}, {{ $team->id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="لغو پرداخت مستقیم">
                                                    <i class="fa fa-times"></i> ابطال
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach

                                        @if($kachaeeAllocations->isEmpty() && $directPayments->isEmpty())
                                        <tr>
                                            <td colspan="9" class="text-center py-4">هیچ تخصیص پیش‌پرداختی ثبت نشده است.</td>
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
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Map of unpaid balances per reference to check on inputs
    const unpaidBalances = {};
    @foreach($groupedRepairs as $ref => $group)
        unpaidBalances["{{ $ref }}"] = parseFloat("{{ $group['remaining_balance'] }}");
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


    function deletePayment(id, team_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عملیات، پرداخت را لغو کرده و حسابات بانکی را معکوس می‌کند!",
            icon: "warning",
            buttons: {
                cancel: "انصراف",
                confirm: { text: "بلی، لغو شود", className: "btn-danger" }
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
                            swal("خطا در حذف!", res.message || "عملیات با خطا مواجه شد.", { icon: "error" });
                        }
                    },
                    error: function(err) {
                        swal("خطا!", "ارتباط با سرور برقرار نشد یا خطای داخلی رخ داد.", "error");
                    }
                });
            }
        });
    }

    var groupedBatches = [
        @foreach($groupedRepairs as $group)
            @if($group['remaining_balance'] > 0.01 && $group['reference'] !== 'نقد')
                @php
                    $batchObj = \App\ProductionBatch::where('reference_number', $group['reference'])->first();
                @endphp
                @if($batchObj)
                {
                    id: {{ $batchObj->id }},
                    reference_number: "{{ $group['reference'] }}",
                    remaining_balance: {{ $group['remaining_balance'] }}
                },
                @endif
            @endif
        @endforeach
    ];

    $(document).ready(function() {
        // Toggle is_advance checkbox visibility
        function toggleAdvanceCheckbox() {
            const isNqd = $('#kachaee_number').val() === 'نقد';
            if (isNqd) {
                $('#is_advance_group').show();
            } else {
                $('#is_advance_group').hide();
                $('#is_advance').prop('checked', false);
            }
        }
        $('#kachaee_number').on('change', toggleAdvanceCheckbox);
        toggleAdvanceCheckbox();

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
            $('#modal_display_pay_id').text('KCH-PAY-' + payId);
            $('#modal_display_remaining').text(remaining.toFixed(2) + ' ' + currency);
            $('.modal_currency_display').text(currency);
            $('#modal_alloc_amount').val(remaining.toFixed(4)).attr('max', remaining);

            loadDocumentsForAllocation(currency);

            $('#allocateAdvanceModal').modal('show');
        });

        function loadDocumentsForAllocation(currency) {
            const select = $('#modal_allocatable_id');
            select.empty();

            groupedBatches.forEach(batch => {
                const remaining = parseFloat(batch.remaining_balance) || 0;
                if (remaining > 0.01) {
                    select.append(`<option value="${batch.id}" data-remaining="${remaining}">گروپ کچایی ${batch.reference_number} (باقیمانده: $${remaining.toFixed(2)})</option>`);
                }
            });
            select.trigger('change');
        }

        $('#modal_allocatable_id').on('change', function () {
            const selectedOpt = $(this).find(':selected');
            if (selectedOpt.length) {
                const batchRemainingUsd = parseFloat(selectedOpt.data('remaining')) || 0;
                const batchRemainingInPaymentCurrency = batchRemainingUsd / activePaymentExchangeRate;
                const targetAmount = Math.min(activePaymentRemaining, batchRemainingInPaymentCurrency);
                $('#modal_alloc_amount').val(targetAmount.toFixed(4));
            }
        });

        $('#allocateAdvanceForm').on('submit', function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            
            $.ajax({
                type: 'POST',
                url: '/dashboard/kachaee-payments/allocate',
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

    function removeAllocation(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عمل تخصیص پیش‌پرداخت را لغو کرده و گروپ کچایی را دوباره بدهکار می‌سازد.",
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
                    url: '/dashboard/kachaee-payments/allocation/' + id,
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

    function editAllocation(id, currentAmount, currencyCode) {
        $('#edit_modal_allocation_id').val(id);
        $('#edit_modal_alloc_amount').val(currentAmount);
        $('.edit_modal_currency_display').text(currencyCode);
        $('#editAllocationModal').modal('show');
    }

    $(document).ready(function() {
        $('#editAllocationForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_modal_allocation_id').val();
            let amount = $('#edit_modal_alloc_amount').val();
            let btn = $('#btn_submit_edit_allocation');
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> در حال پردازش...');
            
            $.ajax({
                type: 'PUT',
                url: '/dashboard/kachaee-payments/allocation/' + id,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'amount': amount
                },
                success: function(res) {
                    if (res.status == 'success') {
                        $('#editAllocationModal').modal('hide');
                        swal("موفق!", res.message, { icon: "success" });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        swal("خطا!", res.message, { icon: "error" });
                        btn.prop('disabled', false).html('<i class="fa fa-save"></i> ذخیره تغییرات');
                    }
                },
                error: function() {
                    swal("خطا!", "مشکلی در ارتباط با سرور رخ داده است.", "error");
                    btn.prop('disabled', false).html('<i class="fa fa-save"></i> ذخیره تغییرات');
                }
            });
        });
    });
</script>

<!-- Edit Allocation Modal -->
<div class="modal fade" id="editAllocationModal" tabindex="-1" role="dialog" aria-labelledby="editAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content premium-modal">
            <div class="modal-header bg-premium-dark text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f57c00 0%, #ff9800 100%);">
                <h5 class="modal-title font-weight-bold" id="editAllocationModalLabel"><i class="fa fa-edit"></i> ویرایش مبلغ تخصیص</h5>
                <button type="button" class="close text-white m-0 p-0" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAllocationForm">
                <input type="hidden" id="edit_modal_allocation_id">
                <div class="modal-body text-right" style="direction: rtl;">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark field-label">مبلغ تخصیص جدید (New Allocation Amount):</label>
                        <div class="input-group" style="direction: ltr;">
                            <div class="input-group-prepend">
                                <span class="input-group-text edit_modal_currency_display" style="font-weight: bold; background: #fff3e0;">USD</span>
                            </div>
                            <input type="number" step="0.0001" name="amount" id="edit_modal_alloc_amount" class="form-control font-weight-bold text-center text-primary" style="font-size: 1.25rem; direction: ltr;" required>
                        </div>
                        <small class="field-explanation text-right d-block mt-1">مبلغ جدید تخصیص را وارد کنید. سیستم مابه‌التفاوت را محاسبه خواهد کرد.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">انصراف (Cancel)</button>
                    <button type="submit" class="btn btn-premium shadow-sm text-white" id="btn_submit_edit_allocation" style="background: #f57c00;">
                        <i class="fa fa-save"></i> ذخیره تغییرات (Save Changes)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Allocation Modal -->
<div class="modal fade" id="allocateAdvanceModal" tabindex="-1" role="dialog" aria-labelledby="allocateAdvanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content premium-modal">
            <div class="modal-header bg-premium-dark text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);">
                <h5 class="modal-title font-weight-bold" id="allocateAdvanceModalLabel"><i class="fa fa-share-square-o"></i> تخصیص پیش‌پرداخت به گروپ کچایی</h5>
                <button type="button" class="close text-white m-0 p-0" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="allocateAdvanceForm">
                @csrf
                <input type="hidden" name="kachaee_payment_id" id="modal_payment_id">
                <div class="modal-body text-right" style="direction: rtl;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light p-3 mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="fa fa-info-circle"></i> معلومات علی‌الحساب</h6>
                                <p class="mb-2"><strong>شماره پیش‌پرداخت:</strong> <span id="modal_display_pay_id" class="badge badge-secondary py-1 px-2 font-weight-bold"></span></p>
                                <p class="mb-2"><strong>مبلغ باقیمانده (Unallocated):</strong> <span id="modal_display_remaining" class="text-success font-weight-bold" style="font-size: 1.1rem;"></span-remaining></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light p-3 mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                                <h6 class="font-weight-bold text-warning mb-3"><i class="fa fa-file-text-o"></i> انتخاب گروپ جهت تصفیه</h6>
                                
                                <input type="hidden" name="allocatable_type" id="modal_allocatable_type" value="App\ProductionBatch">

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold field-label">گروپ کچایی (Production Batch):</label>
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
                                <small class="field-explanation text-right d-block mt-1">مقداری از پیش‌پرداخت که می‌خواهید به گروپ کچایی انتخاب‌شده تخصیص دهید.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">انصراف (Cancel)</button>
                    <button type="submit" class="btn btn-premium btn-premium-success shadow-sm" id="btn_submit_modal_allocation">
                        <i class="fa fa-save"></i> ثبت تخصیص (Apply Allocation)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection