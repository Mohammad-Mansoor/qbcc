@extends('dsh.master')

@section('title', 'پرداخت بخش تیاری - ' . $team->name)

@section('content')
<style>
    /* PREMIUM FINISHING UI STYLES - AMBER/GOLD THEME */
    :root {
        --primary-amber: #ff8f00;
        --secondary-amber: #ff6f00;
        --accent-gold: #ffc107;
        --soft-amber: #fff8e1;
        --dark-amber: #e65100;
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
        background: linear-gradient(135deg, var(--secondary-amber) 0%, var(--accent-gold) 100%);
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
        border: 1px solid var(--soft-amber);
        background: #fff;
        padding: 15px;
        height: 100%;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(255, 143, 0, 0.1); }
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
        background: var(--soft-amber);
        border-right: 4px solid var(--primary-amber);
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .truth-label { font-size: 0.8rem; color: var(--secondary-amber); font-weight: 600; }
    .truth-value { font-size: 1.4rem; color: var(--secondary-amber); font-weight: 800; font-family: 'Courier New', monospace; }

    .custom-input {
        border-radius: 10px;
        border: 2px solid var(--soft-amber);
        padding: 12px 15px;
        transition: all 0.2s;
    }
    .custom-input:focus {
        border-color: var(--primary-amber);
        box-shadow: 0 0 0 0.2rem rgba(255, 143, 0, 0.1);
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
        color: var(--secondary-amber);
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
        border-bottom: 2px solid var(--soft-amber);
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
        color: var(--primary-amber);
        border-bottom-color: var(--soft-amber);
    }
    .nav-tabs-premium .nav-link.active {
        color: var(--primary-amber) !important;
        border-bottom-color: var(--primary-amber) !important;
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
        .hideOnPrint, #forensicFinishingForm, .btn, .card-header-premium button,
        .btn-group, #exportButton, #finishingTabs, .container-fluid > .row:first-child,
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

<div class="container-fluid mt-4" id="finishing-payment-dashboard">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-7 text-right">
            <h3 class="font-weight-bold text-dark">
                <i class="fa fa-magic" style="color:var(--primary-amber)"></i> 
                پرداخت به تیم آماده‌سازی (تیاری): {{ $team->name }}
            </h3>
            <p class="text-muted">مدیریت مالی و تصفیه حسابات بخش آماده‌سازی فرشی (Labor Payments)</p>
        </div>
        <div class="col-md-5 text-left">
            <div class="btn-group">
                <a href="/dashboard/finishing-team" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa fa-arrow-right"></i> بازگشت
                </a>
                <a href="/dashboard/finishing-center?team_id={{$team->id}}" class="btn btn-warning btn-sm rounded-pill px-3 ml-2" style="background:var(--primary-amber); border:none; color:white;">
                    <i class="fa fa-scissors"></i> ثبت کار تیاری جدید
                </a>
                <button class="btn btn-outline-warning btn-sm rounded-pill px-3 ml-2" onclick="window.print()" style="border-color:var(--primary-amber); color:var(--primary-amber)">
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
                        <div class="stat-icon" style="background-color: var(--soft-amber); color: var(--primary-amber);">
                            <i class="fa fa-money fa-lg"></i>
                        </div>
                        <span class="badge badge-soft-amber px-2 py-1">{{ $code }}</span>
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
            <div class="stat-card" style="border: 2px dashed var(--accent-gold);">
                <div class="stat-icon" style="background-color: #fff9c4; color: var(--accent-gold);">
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
                    <div class="d-flex justify-content-between font-weight-bold" style="color:var(--secondary-amber)">
                        <span>بیلانس جاری:</span>
                        <span class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">$ {{ number_format(abs($netBalance), 2) }} {{ $netBalance >= 0 ? '(Cr)' : '(Dr)' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entry Form Section -->
    @if(!isset($all))
    <div class="premium-card form-card-parent">
        <div class="card-header-premium">
            <h5><i class="fa fa-plus-circle mr-2"></i> {{ $paymentEdit ? 'ویرایش سند پرداخت (Edit Payment)' : 'ثبت تراکنش جدید (New Entry)' }}</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ $paymentEdit ? '/dashboard/finishing-payments/'.$paymentEdit->id : '/dashboard/finishing-payments' }}" method="post" id="forensicFinishingForm">
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
                        <input type="number" step="0.0001" name="amount" id="original_amount" class="form-control custom-input font-weight-bold text-warning" 
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
                        <label class="field-label">نمبر تیاری (Finish #)</label>
                        <select name="finish_number" id="finish_number" class="form-control custom-input select2">
                            <option value="General">General (نقد)</option>
                            @foreach($finish_numbers as $fn)
                                <option value="{{$fn->finish_number}}" {{ ($paymentEdit && $paymentEdit->finish_number == $fn->finish_number) ? 'selected' : '' }}>
                                    نمبر تیاری: {{$fn->finish_number}}
                                </option>
                            @endforeach
                        </select>
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
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="small font-weight-bold">حساب بدهکار (Debit Account Override)</label>
                                        <select name="override_debit_account_id" id="override_debit_account_id" class="form-control custom-input select2">
                                            <option value="">Default: {{ $mappingOut->debit_account->account_name ?? 'System' }}</option>
                                            @foreach($allowedDebitAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="small font-weight-bold">حساب بستانکار (Credit Account Override)</label>
                                        <select name="override_credit_account_id" id="override_credit_account_id" class="form-control custom-input select2">
                                            <option value="">Default: {{ $mappingOut->credit_account->account_name ?? 'System' }}</option>
                                            @foreach($allowedCreditAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 text-left">
                        <button id="submit-payment-btn" class="btn btn-primary btn-block rounded-pill py-3 font-weight-bold shadow-lg" type="submit" style="background:var(--primary-amber); border:none;">
                            <i class="fa fa-save"></i> {{ $paymentEdit ? 'بروزرسانی تراکنش' : 'ثبت نهایی تراکنش' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- 3-TABBED FORENSIC MODULE -->
    <div class="row profile-card-parent">
        <div class="col-12">
            <ul class="nav nav-tabs nav-tabs-premium border-0" id="finishingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="payments-tab" data-toggle="tab" href="#payments" role="tab"><i class="fa fa-money mr-1"></i> ریز معاملات و دستمزدها (Wage Ledger)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="grouped-batches-tab" data-toggle="tab" href="#grouped_batches" role="tab"><i class="fa fa-folder-open mr-1"></i> بل‌های دستمزد گروپ شده (Grouped Batches)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="statement-tab" data-toggle="tab" href="#statement" role="tab"><i class="fa fa-file-text-o mr-1"></i> صورت حساب تفصیلی (GL Statement)</a>
                </li>
            </ul>

            <div class="tab-content" id="finishingTabContent">
                <!-- Tab 1: Payments List (Wage Ledger) -->
                <div class="tab-pane fade show active" id="payments" role="tabpanel">
                    <div class="premium-card">
                        <div class="card-header-premium d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--secondary-amber) 0%, var(--accent-gold) 100%);">
                            <h5><i class="fa fa-list-alt mr-2"></i> ریز معاملات و دستمزدها (Wage Ledger)</h5>
                            <div id="exportButton">
                                @if(!isset($all))
                                    <a href="/dashboard/finishing-payments-all/{{$team->id}}" class="btn btn-sm btn-light rounded-pill px-3">
                                        <i class="fa fa-eye"></i> نمایش همه
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right" id="finishing_ledger_table">
                                    <thead>
                                        <tr>
                                            <th class="px-4">تاریخ (Date)</th>
                                            <th>نوع (Type)</th>
                                            <th>نمبر تیاری (Finish #)</th>
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
                                            <td class="text-warning font-weight-bold">{{ $p->finish_number ?: 'N/A' }}</td>
                                            <td class="small">{{ $p->description }}</td>
                                            <td class="font-weight-bold text-warning">{{ $p->currency_code ?: 'USD' }}</td>
                                            <td class="small" style="direction: ltr;">{{ number_format($p->exchange_rate, 8) }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">{{ number_format($p->original_amount ?: ($p->amount ?: $p->amount_af), 2) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">$ {{ number_format($p->base_amount ?: ($p->amount ?: $p->amount_af), 2) }}</td>
                                            <td>
                                                @if($p->status == 0)
                                                    <span class="status-badge bg-warning text-dark">انتظار تایید</span>
                                                @else
                                                    <span class="status-badge bg-success text-white">تایید شده</span>
                                                @endif
                                            </td>
                                            <td class="hideOnPrint text-center">
                                                @if($p->status == 0 || auth()->user()->role == 'SP')
                                                    <div class="btn-group">
                                                        <a href="/dashboard/finishing-payments/{{$p->id}}/edit" class="btn btn-sm btn-outline-warning">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <button type="button" onclick="deletePayment({{$p->id}}, {{$p->team_id}})" class="btn btn-sm btn-outline-danger">
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
                                            <td colspan="3" class="text-center font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                بیلانس: {{ number_format(abs($balance), 2) }} {{ $code }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="background: #fff8e1;">
                                            <th colspan="3" class="text-right text-warning"><b>مجموع کل بیلانس لجر (Base USD)</b></th>
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
                                مجموع بل‌ها: {{ count($groupedFinishingWorks) }} عدد
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ اولین ثبت (First Record)</th>
                                            <th>تیاری نمبر (Batch Ref)</th>
                                            <th>تعداد قالین (Carpets)</th>
                                            <th>هزینه کل (Total Cost)</th>
                                            <th>پرداخت شده (Paid)</th>
                                            <th>باقی‌مانده (Remaining)</th>
                                            <th>وضعیت پرداخت (Status)</th>
                                            <th>عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($groupedFinishingWorks as $group)
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
                                                <button type="button" class="btn btn-sm btn-success pay-finish-btn" 
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
                                            <td colspan="8" class="text-center text-muted py-4">هیچ کار آماده‌سازی (تیاری) برای این تیم ثبت نشده است.</td>
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
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #e65100 0%, #ff8f00 100%);">
                            <h5><i class="fa fa-book mr-2"></i> صورت حساب مالی تفصیلی (GL Statement)</h5>
                            <button type="button" class="btn btn-light btn-sm font-weight-bold text-dark" onclick="printStatement()">
                                <i class="fa fa-print"></i> چاپ صورت حساب
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" id="print-area">
                                <!-- Print-only Header (Hidden on Screen) -->
                                <div class="d-none print-header text-center mb-4 mt-3">
                                    <h3 class="font-weight-bold">صورت حساب مالی آماده‌سازی (تیاری): {{ $team->name }}</h3>
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
    @foreach($groupedFinishingWorks as $ref => $group)
        unpaidBalances["{{ $ref }}"] = parseFloat("{{ $group['remaining_balance'] }}");
    @endforeach

    $(document).ready(function () {
        $('#finish_number').select2();
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
            const ref = $('#finish_number').val();
            const type = $('#payment_type').val();
            
            $('#overpayment-warning').remove();
            $('#submit-payment-btn').prop('disabled', false);

            if (ref !== 'General' && type === 'گرفت' && unpaidBalances[ref] !== undefined) {
                // Determine if we are editing an existing payment to exclude it from the client-side validation logic
                const isEditing = "{{ $paymentEdit ? 'true' : 'false' }}";
                let maxAllowed = unpaidBalances[ref];
                
                if (isEditing === 'true') {
                    const originalEditVal = parseFloat("{{ $paymentEdit ? $paymentEdit->original_amount : 0 }}");
                    const originalEditRef = "{{ $paymentEdit ? $paymentEdit->finish_number : '' }}";
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

        $('#original_amount, #exchange_rate, #finish_number, #payment_type').on('input change', updateUsdPreview);
        updateUsdPreview();

        // Pay Button Click Handler
        $('.pay-finish-btn').on('click', function() {
            const refNumber = $(this).data('ref');
            const remaining = $(this).data('remaining');
            const currency = $(this).data('currency');
            
            // 1. Switch to Payments Tab
            $('#payments-tab').tab('show');
            
            // 2. Pre-fill Form Fields
            $('#original_amount').val(remaining).trigger('input');
            $('#payment_type').val('گرفت').trigger('change');
            
            if ($('#finish_number option[value="' + refNumber + '"]').length > 0) {
                $('#finish_number').val(refNumber).trigger('change');
            } else {
                const newOption = new Option(refNumber, refNumber, true, true);
                $('#finish_number').append(newOption).trigger('change');
            }
            
            $('#currency_id option').each(function() {
                if ($(this).text().indexOf(currency) !== -1) {
                    $('#currency_id').val($(this).val()).trigger('change');
                }
            });

            $('html, body').animate({
                scrollTop: $("#forensicFinishingForm").offset().top - 100
            }, 500);
        });
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
                    url: '/dashboard/finishing-payments/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" });
                            setTimeout(() => window.location = '/dashboard/finishing-payments/' + team_id, 1000);
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