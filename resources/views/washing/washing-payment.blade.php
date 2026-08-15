@extends('dsh.master')

@section('title', 'پرداخت بخش شست‌وشو - ' . $team->name)

@section('content')
<style>
    /* PREMIUM WASHING UI STYLES - BLUE/CYAN THEME */
    :root {
        --primary-blue: #0277bd;
        --secondary-blue: #01579b;
        --accent-cyan: #00acc1;
        --soft-blue: #e1f5fe;
        --dark-blue: #0d47a1;
    }
    
    .premium-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.06);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }
    .card-header-premium {
        background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--accent-cyan) 100%);
        padding: 22px 28px;
        border: none;
        color: white;
    }
    .card-header-premium h5 {
        color: #ffffff;
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .stat-card {
        border-radius: 12px;
        border: 1px solid var(--soft-blue);
        background: #fff;
        padding: 15px;
        height: 100%;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(2, 119, 189, 0.1); }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    
    .truth-preview-box {
        background: var(--soft-blue);
        border-right: 4px solid var(--primary-blue);
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .truth-label { font-size: 0.8rem; color: var(--secondary-blue); font-weight: 600; }
    .truth-value { font-size: 1.4rem; color: var(--secondary-blue); font-weight: 800; font-family: 'Courier New', monospace; }

    .custom-input {
        border-radius: 10px;
        border: 2px solid var(--soft-blue);
        padding: 12px 15px;
        transition: all 0.2s;
    }
    .custom-input:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(2, 119, 189, 0.1);
    }
    .field-label {
        font-weight: 600;
        color: #455a64;
        margin-bottom: 8px;
        display: block;
        font-size: 0.85rem;
    }

    .premium-table thead th {
        background: #f8f9fa;
        color: var(--secondary-blue);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 15px;
    }
    .premium-table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .nav-tabs-premium {
        border-bottom: 2px solid var(--soft-blue);
        margin-bottom: 25px;
    }
    .nav-tabs-premium .nav-link {
        border: none;
        color: #607d8b;
        padding: 14px 20px;
        font-size: 0.95rem;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    .nav-tabs-premium .nav-link:hover {
        color: var(--primary-blue);
        border-bottom-color: var(--soft-blue);
    }
    .nav-tabs-premium .nav-link.active {
        color: var(--primary-blue) !important;
        border-bottom-color: var(--primary-blue) !important;
        background: transparent;
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
        .hideOnPrint, #forensicWashingForm, .btn, .card-header-premium button,
        .btn-group, #exportButton, #washingTabs, .container-fluid > .row:first-child,
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
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            font-size: 10pt !important;
        }
    }
</style>

<div class="container-fluid mt-4" id="washing-payment-dashboard">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-7 text-right">
            <h3 class="font-weight-bold text-white">
                <i class="fa fa-tint" style="color:var(--soft-blue)"></i> 
                پرداخت به شست‌گر: {{ $team->name }}
            </h3>
            <p class="text-white" style="opacity: 0.9;">مدیریت مالی و تصفیه حسابات بخش شست‌وشوی فرشی (Labor Payments)</p>
        </div>
        <div class="col-md-5 text-left">
            <div class="btn-group">
                <a href="/dashboard/washing-team" class="btn btn-outline-light btn-sm rounded-pill px-3 text-white">
                    <i class="fa fa-arrow-right"></i> بازگشت
                </a>
                <button class="btn btn-outline-light btn-sm rounded-pill px-3 ml-2 text-white" onclick="window.print()">
                    <i class="fa fa-print"></i> چاپ صورت حساب
                </button>
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

    <!-- Currency Summaries -->
    <div class="row mb-4">
        @foreach($currencyTotals as $code => $totals)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="stat-icon" style="background-color: var(--soft-blue); color: var(--primary-blue);">
                            <i class="fa fa-money fa-lg"></i>
                        </div>
                        <span class="badge badge-soft-blue px-2 py-1">{{ $code }}</span>
                    </div>
                    <h6 class="font-weight-bold text-dark mb-1">خلاصه حساب ({{ $code }})</h6>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between small mb-1 text-danger">
                            <span>پرداختی (گرفت):</span>
                            <span class="font-weight-bold">{{ number_format($totals->total_sent, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1 text-success">
                            <span>دریافتی (رسید):</span>
                            <span class="font-weight-bold">{{ number_format($totals->total_received, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        @php $balance = $totals->total_received - $totals->total_sent; @endphp
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>باقیمانده:</span>
                            <span class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card" style="border: 2px dashed var(--accent-cyan);">
                <div class="stat-icon" style="background-color: #e0f7fa; color: var(--accent-cyan);">
                    <i class="fa fa-shield fa-lg"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-1">تصفیه کل لجر (Base USD)</h6>
                <div class="mt-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">کل پرداختی:</span>
                        <span class="font-weight-bold text-danger">$ {{ number_format($totalBaseSent, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">کل دریافتی:</span>
                        <span class="font-weight-bold text-success">$ {{ number_format($totalBaseReceived, 2) }}</span>
                    </div>
                    <hr class="my-2">
                    @php $netBalance = $totalBaseReceived - $totalBaseSent; @endphp
                    <div class="d-flex justify-content-between font-weight-bold" style="color:var(--secondary-blue)">
                        <span>بیلانس جاری:</span>
                        <span class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">$ {{ number_format(abs($netBalance), 2) }} {{ $netBalance >= 0 ? '(Cr)' : '(Dr)' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entry Form Section -->
    @can('manage_washing_payments')
    @if(!isset($all))
    <div class="premium-card form-card-parent">
        <div class="card-header-premium">
            <h5><i class="fa fa-plus-circle mr-2"></i> {{ $paymentEdit ? 'ویرایش سند پرداخت (Edit Payment)' : 'ثبت تراکنش جدید (New Entry)' }}</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ $paymentEdit ? '/dashboard/washing-payments/'.$paymentEdit->id : '/dashboard/washing-payments' }}" method="post" id="forensicWashingForm">
                @csrf
                @if($paymentEdit) @method('PUT') @endif
                <input type="hidden" name="team_id" value="{{ $team->id }}">

                <div class="row">
                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">نوع تراکنش (Type)</label>
                        <select name="type" id="payment_type" class="form-control custom-input font-weight-bold" required>
                            <option value="گرفت" {{ ($paymentEdit && $paymentEdit->type == 'گرفت') ? 'selected' : '' }}>گرفت (پرداخت به تیم)</option>
                            <option value="رسید" {{ ($paymentEdit && $paymentEdit->type == 'رسید') ? 'selected' : '' }}>رسید (برگشتی از تیم)</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">واحد پولی (Currency)</label>
                        <select name="currency_id" id="currency_id" class="form-control custom-input font-weight-bold" required>
                            @foreach($currencies as $curr)
                                <option value="{{$curr->id}}" data-rate="{{$curr->exchange_rate}}" 
                                    {{ ($paymentEdit && $paymentEdit->currency_code == $curr->code) || (!$paymentEdit && $curr->code == 'USD') ? 'selected' : '' }}>
                                    {{$curr->name}} ({{$curr->code}})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 form-group mb-4">
                        <label class="field-label">نرخ تبادله (Rate)</label>
                        <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control custom-input font-weight-bold bg-light" 
                               value="{{ $paymentEdit ? $paymentEdit->exchange_rate : ($currencies->where('code', 'USD')->first()->exchange_rate ?? 1.0) }}" readonly>
                    </div>

                    <div class="col-lg-2 col-md-6 form-group mb-4 text-right">
                        <label class="field-label">مقدار پول (Amount)</label>
                        <input type="number" step="0.0001" name="amount" id="original_amount" class="form-control custom-input font-weight-bold text-primary" 
                               value="{{ $paymentEdit ? $paymentEdit->original_amount : old('amount') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="truth-preview-box">
                            <span class="truth-label"><i class="fa fa-shield"></i> معادل دالر (USD Normalized):</span><br>
                            <span class="truth-value" id="usd_truth_preview">$ 0.0000</span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">تاریخ (Date)</label>
                        <input type="date" name="date" class="form-control custom-input" value="{{ $paymentEdit ? $paymentEdit->date : date('Y-m-d') }}" required>
                    </div>

                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">نمبر شست (Wash #)</label>
                        <select name="wash_number" id="wash_number" class="form-control custom-input select2">
                            <option value="General">General (نقد)</option>
                            @foreach($wash_numbers as $wn)
                                <option value="{{$wn->wash_number_sh}}" {{ ($paymentEdit && $paymentEdit->wash_number == $wn->wash_number_sh) ? 'selected' : '' }}>
                                     {{$wn->wash_number_sh}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 form-group mb-4" id="is_advance_group">
                        <label class="field-label">&nbsp;</label>
                        <div class="custom-control custom-checkbox mr-sm-2 text-right pt-2">
                            <input type="checkbox" class="custom-control-input" id="is_advance" name="is_advance" value="1"
                                {{ ($paymentEdit && $paymentEdit->is_advance) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-success" for="is_advance" style="cursor: pointer;">
                                پیش‌پرداخت (Is Advance)
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 form-group mb-4">
                        <label class="field-label">توضیحات (Description)</label>
                        <input type="text" name="description" class="form-control custom-input" value="{{ $paymentEdit ? $paymentEdit->description : '' }}" placeholder="شرح پرداخت..." required>
                    </div>

                    <!-- Account Selection Overrides (Advanced) -->
                    <div class="col-12 mb-4">
                        <button class="btn btn-sm btn-link p-0 text-muted" type="button" data-toggle="collapse" data-target="#advancedAccounting">
                            <i class="fa fa-cog"></i> تنظیمات پیشرفته حسابداری (Accounting Overrides)
                        </button>
                        <div class="collapse mt-3" id="advancedAccounting">
                            <div class="accounting-override-box">
                                @php
                                    $selectedDebit = $paymentEdit ? ($paymentEdit->override_debit_account_id ?? $paymentEdit->actual_debit_account_id ?? '') : '';
                                    $selectedCredit = $paymentEdit ? ($paymentEdit->override_credit_account_id ?? $paymentEdit->actual_credit_account_id ?? '') : '';
                                @endphp
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="small font-weight-bold">حساب بدهکار (Debit Account Override)</label>
                                        <select name="override_debit_account_id" id="override_debit_account_id" class="form-control custom-input select2">
                                            <option value="" {{ (empty($selectedDebit)) ? 'selected' : '' }}>Default: {{ ($paymentEdit && $paymentEdit->type == 'رسید' ? ($mappingIn->debit_account->account_name ?? 'System') : ($mappingOut->debit_account->account_name ?? 'System')) }}</option>
                                            @foreach($allowedDebitAccounts as $acc)
                                                <option value="{{ $acc->id }}" {{ ($selectedDebit == $acc->id) ? 'selected' : '' }}>{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="small font-weight-bold">حساب بستانکار (Credit Account Override)</label>
                                        <select name="override_credit_account_id" id="override_credit_account_id" class="form-control custom-input select2">
                                            <option value="" {{ (empty($selectedCredit)) ? 'selected' : '' }}>Default: {{ ($paymentEdit && $paymentEdit->type == 'رسید' ? ($mappingIn->credit_account->account_name ?? 'System') : ($mappingOut->credit_account->account_name ?? 'System')) }}</option>
                                            @foreach($allowedCreditAccounts as $acc)
                                                <option value="{{ $acc->id }}" {{ ($selectedCredit == $acc->id) ? 'selected' : '' }}>{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 text-left">
                        <button id="submit-payment-btn" class="btn btn-primary btn-block rounded-pill py-3 font-weight-bold shadow-lg" type="submit" style="background:var(--primary-blue); border:none;">
                            <i class="fa fa-save"></i> {{ $paymentEdit ? 'بروزرسانی تراکنش' : 'ثبت نهایی تراکنش' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endcan

    <!-- 3-TABBED FORENSIC MODULE -->
    <div class="row profile-card-parent">
        <div class="col-12">
            <ul class="nav nav-tabs nav-tabs-premium border-0" id="washingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="payments-tab" data-toggle="tab" href="#payments" role="tab"><i class="fa fa-money mr-1"></i> ریز معاملات و دستمزدها (Wage Ledger)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="grouped-batches-tab" data-toggle="tab" href="#grouped_batches" role="tab"><i class="fa fa-folder-open mr-1"></i> بل‌های دستمزد گروپ شده (Grouped Batches)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="washing-advances-tab" data-toggle="tab" href="#washing_advances" role="tab"><i class="fa fa-share-square-o mr-1"></i> پیش‌پرداخت‌ها (Advances)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="washing-reconciliation-tab" data-toggle="tab" href="#washing_reconciliation" role="tab"><i class="fa fa-undo mr-1"></i> تصفیه و پرداخت‌های مستقیم (Reconciliations)</a>
                </li>
            </ul>

            <div class="tab-content" id="washingTabContent">
                <!-- Tab 1: Payments List (Wage Ledger) -->
                <div class="tab-pane fade show active" id="payments" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--accent-cyan) 100%);">
                            <h5><i class="fa fa-list-alt mr-2"></i> ریز معاملات و دستمزدها (Wage Ledger)</h5>
                            <div id="exportButton"></div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right" id="washing_ledger_table">
                                    <thead>
                                        <tr>
                                            <th class="px-4">تاریخ (Date)</th>
                                            <th>نوع (Type)</th>
                                            <th>نمبر شست (Wash #)</th>
                                            <th>شرح (Description)</th>
                                            <th>ارز (CCY)</th>
                                            <th>نرخ (Rate)</th>
                                            <th>مقدار اصلی (Amount)</th>
                                            <th>معادل دالر (USD)</th>
                                            <th>حالت (Status)</th>
                                            <th class="hideOnPrint text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $p)
                                        <tr>
                                            <td class="px-4 font-weight-bold text-muted">{{ $p->date }}</td>
                                            <td>
                                                <span class="badge {{ $p->type == 'رسید' ? 'badge-success' : 'badge-danger' }} px-3 py-2 rounded-pill font-weight-bold">
                                                    {{ $p->type }}
                                                </span>
                                            </td>
                                            <td class="text-info font-weight-bold">{{ $p->wash_number ?: 'N/A' }}</td>
                                            <td class="small">{{ $p->description }}</td>
                                            <td class="font-weight-bold text-primary">{{ $p->currency_code ?: 'USD' }}</td>
                                            <td class="small" style="direction: ltr;">{{ number_format($p->exchange_rate, 8) }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">{{ number_format($p->original_amount ?: ($p->amount ?: $p->amount_af), 2) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">$ {{ number_format($p->base_amount ?: ($p->amount ?: $p->amount_af), 2) }}</td>
                                            <td>
                                                @if($p->status == 0)
                                                    <span class="status-badge bg-warning text-dark">انتظار تایید</span>
                                                @else
                                                    <span class="status-badge bg-success text-white">تایید شده</span>
                                                @endif
                                                @if($p->is_advance)
                                                    <br>
                                                    <span class="badge badge-info mt-1">پیش‌پرداخت ({{ $p->payment_status == 'allocated' ? 'تخصیص شده' : ($p->payment_status == 'partially_allocated' ? 'قسمتی تخصیص شده' : 'تخصیص نشده') }})</span>
                                                @endif
                                            </td>
                                            <td class="hideOnPrint text-center">
                                                <a href="/dashboard/washing-payments/{{$p->id}}/edit" class="btn btn-sm btn-outline-info" title="ویرایش">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @can('cancel_washing_payment')
                                                    <button type="button" onclick="deletePayment({{$p->id}}, {{$p->team_id}})" class="btn btn-sm btn-outline-danger" title="ابطال / حذف">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                @endcan
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
                                            <td colspan="3" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                بیلانس: {{ number_format(abs($balance), 2) }} {{ $code }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="background: #e8f5e9;">
                                            <th colspan="3" class="text-right text-success"><b>مجموع کل بیلانس لجر (Base USD)</b></th>
                                            <td colspan="2" class="text-success text-right"><b>$ {{ number_format($totalBaseReceived, 2) }}</b></td>
                                            <td colspan="2" class="text-danger text-right"><b>$ {{ number_format($totalBaseSent, 2) }}</b></td>
                                            @php $baseBalance = $totalBaseReceived - $totalBaseSent; @endphp
                                            <td colspan="3" class="text-center font-weight-bold {{ $baseBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                                                $ {{ number_format(abs($baseBalance), 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @if(!isset($all))
                        <div class="card-footer bg-white border-0 py-3 text-left">
                            {{ $payments->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 2: Grouped Batches -->
                <div class="tab-pane fade" id="grouped_batches" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1565c0 0%, #1e88e5 100%);">
                            <h5><i class="fa fa-folder-open mr-2"></i> بل‌های دستمزد گروپ شده (Grouped Batches)</h5>
                            <span class="badge badge-light p-2 font-weight-bold text-primary" style="font-size: 0.9rem;">
                                مجموع بل‌ها: {{ count($groupedWashes) }} عدد
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ اولین ثبت (First Record)</th>
                                            <th>شست‌وشو نمبر (Batch Ref)</th>
                                            <th>تعداد قالین (Carpets)</th>
                                            <th>هزینه کل (Total Cost)</th>
                                            <th>پرداخت شده (Paid)</th>
                                            <th>باقی‌مانده (Remaining)</th>
                                            <th>وضعیت پرداخت (Status)</th>
                                            <th>عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($groupedWashes as $group)
                                        <tr>
                                            <td>{{ $group['date'] }}</td>
                                            <td>
                                                @if($group['reference'] !== 'General')
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
                                                @can('manage_washing_payments')
                                                <button type="button" class="btn btn-sm btn-success pay-wash-btn" 
                                                        data-ref="{{ $group['reference'] }}" 
                                                        data-remaining="{{ $group['remaining_balance'] }}" 
                                                        data-currency="USD">
                                                    <i class="fa fa-credit-card"></i> تصفیه گروپ
                                                </button>
                                                @endcan
                                                @else
                                                <span class="text-success"><i class="fa fa-check-circle"></i> پرداخت کامل</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">هیچ کار شست‌وشویی برای این تیم ثبت نشده است.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>




                <!-- Tab 4: Washing Advances -->
                <div class="tab-pane fade" id="washing_advances" role="tabpanel">
                    @php
                        $advances = \App\WashingPayment::where('team_id', $team->id)
                            ->where('is_advance', true)
                            ->where('status', '!=', 2)
                            ->orderBy('date', 'DESC')
                            ->get();
                    @endphp
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0277bd 0%, #00acc1 100%);">
                            <h5><i class="fa fa-share-square-o mr-2"></i> پیش‌پرداخت‌های تیم (Washing Advances)</h5>
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
                                            <td><strong>WSH-PAY-{{ $adv->id }}</strong></td>
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
                                                @if($adv->status == 1 && $adv->remaining_unallocated_amount > 0.01)
                                                @can('manage_washing_payments')
                                                <button type="button" class="btn btn-sm btn-success open-allocate-modal-btn" 
                                                        data-payment-id="{{ $adv->id }}"
                                                        data-currency="{{ $adv->currency_code }}"
                                                        data-remaining="{{ $adv->remaining_unallocated_amount }}"
                                                        data-exchange-rate="{{ $adv->exchange_rate }}">
                                                    <i class="fa fa-share-square-o"></i> تخصیص به گروپ
                                                </button>
                                                @endcan
                                                @else
                                                <span class="text-muted">کامل تخصیص شده / تایید نشده</span>
                                                @endif
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

                <!-- Tab 5: Washing Reconciliation (Allocations History) -->
                <div class="tab-pane fade" id="washing_reconciliation" role="tabpanel">
                    @php
                        $washingAllocations = \App\WashingPaymentAllocation::whereHas('payment', function($q) use ($team) {
                            $q->where('team_id', $team->id)->where('status', '!=', 2);
                        })->with(['payment', 'allocatable'])->orderBy('id', 'DESC')->get();

                        $directPayments = \App\WashingPayment::where('team_id', $team->id)
                            ->where('wash_number', '!=', 'General')
                            ->where('is_advance', 0)
                            ->where('status', '!=', 2)
                            ->orderBy('date', 'DESC')->get();
                    @endphp
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #01579b 0%, #0277bd 100%);">
                            <h5><i class="fa fa-undo mr-2"></i> تاریخچه تخصیص و تصفیه پیش‌پرداخت‌ها (Reconciliation History)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ تخصیص (Allocation Date)</th>
                                            <th>سند پیش‌پرداخت (Source Advance)</th>
                                            <th>گروپ شست‌وشو مقصد (Target Batch)</th>
                                            <th>مبلغ تخصیص (Allocated Amount)</th>
                                            <th>نرخ ارز (Exchange Rate)</th>
                                            <th>معادل دالر (Base USD Allocated)</th>
                                            <th class="hideOnPrint">عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($washingAllocations as $alloc)
                                        <tr>
                                            <td>{{ $alloc->created_at ? $alloc->created_at->format('Y-m-d') : '---' }}</td>
                                            <td>
                                                <a href="#" class="font-weight-bold">
                                                    WSH-PAY-{{ $alloc->washing_payment_id }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $alloc->payment->description ?? '' }}</small>
                                            </td>
                                            <td>
                                                @if($alloc->allocatable)
                                                    <span class="badge badge-info text-white">گروپ شست‌وشو</span>
                                                    <strong>{{ $alloc->allocatable->reference_number }}</strong>
                                                @else
                                                    <span class="text-danger">سند حذف شده</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success" style="direction: ltr;">
                                                {{ number_format($alloc->allocated_amount, 2) }} {{ $alloc->payment->currency_code ?? 'USD' }}
                                            </td>
                                            <td class="text-muted small" style="direction: ltr;">{{ number_format($alloc->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">
                                                $ {{ number_format($alloc->base_allocated_amount, 2) }}
                                            </td>
                                            <td class="hideOnPrint">
                                                @can('cancel_washing_payment')
                                                <button onclick="removeAllocation({{ $alloc->id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="حذف تخصیص">
                                                    <i class="fa fa-undo"></i> لغو تخصیص
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach

                                        @if(count($directPayments) > 0)
                                        <tr class="bg-light">
                                            <td colspan="7" class="text-center font-weight-bold text-primary py-3">
                                                <i class="fa fa-arrow-down mr-1"></i> پرداخت‌های مستقیم به گروپ‌های شست‌وشو (Direct Payments) <i class="fa fa-arrow-down ml-1"></i>
                                            </td>
                                        </tr>
                                        @endif

                                        @foreach($directPayments as $dp)
                                        <tr>
                                            <td>{{ $dp->date }}</td>
                                            <td>
                                                <span class="badge badge-secondary">پرداخت مستقیم</span>
                                                <br>
                                                <small class="text-muted">{{ $dp->description }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info text-white">گروپ شست‌وشو</span>
                                                <strong>{{ $dp->wash_number }}</strong>
                                            </td>
                                            <td class="font-weight-bold text-success" style="direction: ltr;">
                                                {{ number_format($dp->original_amount, 2) }} {{ $dp->currency_code ?? 'USD' }}
                                            </td>
                                            <td class="text-muted small" style="direction: ltr;">{{ number_format($dp->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">
                                                $ {{ number_format($dp->base_amount, 2) }}
                                            </td>
                                            <td class="hideOnPrint">
                                                @can('cancel_washing_payment')
                                                <button onclick="deletePayment({{ $dp->id }}, {{ $dp->team_id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="ابطال پرداخت مستقیم">
                                                    <i class="fa fa-ban"></i> ابطال
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach

                                        @if(count($washingAllocations) == 0 && count($directPayments) == 0)
                                        <tr>
                                            <td colspan="7" class="text-center py-4">هیچ تصفیه یا پرداخت مستقیمی ثبت نشده است.</td>
                                        </tr>
                                        @endif
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
    @foreach($groupedWashes as $ref => $group)
        unpaidBalances["{{ $ref }}"] = parseFloat("{{ $group['remaining_balance'] }}");
    @endforeach

    $(document).ready(function () {
        $('#wash_number').select2();
        $('#currency_id').select2();
        $('#override_debit_account_id').select2();
        $('#override_credit_account_id').select2();

        @if($paymentEdit && !empty($selectedDebit))
            $('#override_debit_account_id').val("{{ $selectedDebit }}").trigger('change.select2');
        @endif
        @if($paymentEdit && !empty($selectedCredit))
            $('#override_credit_account_id').val("{{ $selectedCredit }}").trigger('change.select2');
        @endif

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
            const ref = $('#wash_number').val();
            const type = $('#payment_type').val();
            
            $('#overpayment-warning').remove();
            $('#submit-payment-btn').prop('disabled', false);

            if (ref !== 'General' && type === 'گرفت' && unpaidBalances[ref] !== undefined) {
                // Determine if we are editing an existing payment to exclude it from the client-side validation logic
                const isEditing = "{{ $paymentEdit ? 'true' : 'false' }}";
                let maxAllowed = unpaidBalances[ref];
                
                if (isEditing === 'true') {
                    const originalEditVal = parseFloat("{{ $paymentEdit ? $paymentEdit->original_amount : 0 }}");
                    const originalEditRef = "{{ $paymentEdit ? $paymentEdit->wash_number : '' }}";
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

        $('#original_amount, #exchange_rate, #wash_number, #payment_type').on('input change', updateUsdPreview);
        updateUsdPreview();

        // Pay Button Click Handler
        $('.pay-wash-btn').on('click', function() {
            const refNumber = $(this).data('ref');
            const remaining = $(this).data('remaining');
            const currency = $(this).data('currency');
            
            // 1. Switch to Payments Tab
            $('#payments-tab').tab('show');
            
            // 2. Pre-fill Form Fields
            $('#original_amount').val(remaining).trigger('input');
            $('#payment_type').val('گرفت').trigger('change');
            
            if ($('#wash_number option[value="' + refNumber + '"]').length > 0) {
                $('#wash_number').val(refNumber).trigger('change');
            } else {
                const newOption = new Option(refNumber, refNumber, true, true);
                $('#wash_number').append(newOption).trigger('change');
            }
            
            $('#currency_id option').each(function() {
                if ($(this).text().indexOf(currency) !== -1) {
                    $('#currency_id').val($(this).val()).trigger('change');
                }
            });

            $('html, body').animate({
                scrollTop: $("#forensicWashingForm").offset().top - 100
            }, 500);
        });

        // Toggle is_advance checkbox visibility
        function toggleAdvanceCheckbox() {
            const isNqd = $('#wash_number').val() === 'General';
            if (isNqd) {
                $('#is_advance_group').show();
            } else {
                $('#is_advance_group').hide();
                $('#is_advance').prop('checked', false);
            }
        }
        $('#wash_number').on('change', toggleAdvanceCheckbox);
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
            $('#modal_display_pay_id').text('WSH-PAY-' + payId);
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
                    select.append(`<option value="${batch.id}" data-remaining="${remaining}">گروپ شست‌وشو ${batch.reference_number} (باقیمانده: $${remaining.toFixed(2)})</option>`);
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
                url: '/dashboard/washing-payments/allocate',
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

    var groupedBatches = [
        @foreach($groupedWashes as $group)
            @if($group['remaining_balance'] > 0.01 && $group['reference'] !== 'General')
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
                    url: '/dashboard/washing-payments/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" });
                            setTimeout(() => window.location = '/dashboard/washing-payments/' + team_id, 1000);
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
            text: "این عمل تخصیص پیش‌پرداخت را لغو کرده و گروپ شست‌وشو را دوباره بدهکار می‌سازد.",
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
                    url: '/dashboard/washing-payments/allocation/' + id,
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

<!-- Allocation Modal -->
<div class="modal fade" id="allocateAdvanceModal" tabindex="-1" role="dialog" aria-labelledby="allocateAdvanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content premium-modal">
            <div class="modal-header bg-premium-dark text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0288d1 0%, #039be5 100%);">
                <h5 class="modal-title font-weight-bold" id="allocateAdvanceModalLabel"><i class="fa fa-share-square-o"></i> تخصیص پیش‌پرداخت به گروپ شست‌وشو</h5>
                <button type="button" class="close text-white m-0 p-0" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="allocateAdvanceForm">
                @csrf
                <input type="hidden" name="washing_payment_id" id="modal_payment_id">
                <div class="modal-body text-right" style="direction: rtl;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light p-3 mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="fa fa-info-circle"></i> معلومات علی‌الحساب</h6>
                                <p class="mb-2"><strong>شماره پیش‌پرداخت:</strong> <span id="modal_display_pay_id" class="badge badge-secondary py-1 px-2 font-weight-bold"></span></p>
                                <p class="mb-2"><strong>مبلغ باقیمانده (Unallocated):</strong> <span id="modal_display_remaining" class="text-success font-weight-bold" style="font-size: 1.1rem;"></span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light p-3 mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                                <h6 class="font-weight-bold text-warning mb-3"><i class="fa fa-file-text-o"></i> انتخاب گروپ جهت تصفیه</h6>
                                
                                <input type="hidden" name="allocatable_type" id="modal_allocatable_type" value="App\ProductionBatch">

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold field-label">گروپ شست‌وشو (Production Batch):</label>
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
                                <small class="field-explanation text-right d-block mt-1">مقداری از پیش‌پرداخت که می‌خواهید به گروپ شست‌وشو انتخاب‌شده تخصیص دهید.</small>
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
