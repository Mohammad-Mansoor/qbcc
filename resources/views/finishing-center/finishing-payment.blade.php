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
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card-header-premium {
        background: linear-gradient(135deg, var(--secondary-amber) 0%, var(--accent-gold) 100%);
        padding: 20px 25px;
        border: none;
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
    
    .grand-total-banner {
        background: linear-gradient(135deg, var(--dark-amber) 0%, var(--secondary-amber) 100%);
        border-radius: 15px;
        color: white;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(230, 81, 0, 0.2);
    }
    .badge-soft-amber { background: var(--soft-amber); color: var(--secondary-amber); }
</style>

<div class="container-fluid mt-4" id="finishing-payment-dashboard">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-7 text-right">
            <h3 class="font-weight-bold text-dark">
                <i class="fa fa-magic" style="color:var(--primary-amber)"></i> 
                پرداخت به تیم تیاری: {{ $team->name }}
            </h3>
            <p class="text-muted">مدیریت مالی و تصفیه حسابات نهایی بخش تیاری و پرداخت‌های تیم (Finishing Labor)</p>
        </div>
        <div class="col-md-5 text-left">
            <div class="btn-group">
                <a href="/dashboard/finishing-team" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa fa-arrow-right"></i> بازگشت
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
                <h6 class="font-weight-bold text-dark mb-1">مجموع کل (USD Truth)</h6>
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
                    <div class="d-flex justify-content-between font-weight-bold" style="color:var(--secondary-amber)">
                        <span>تصفیه نهایی:</span>
                        <span>$ {{ number_format($totalBaseReceived - $totalBaseSent, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entry Form Section -->
    @if(!isset($all))
    <div class="premium-card">
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
                        <select name="type" class="form-control custom-input font-weight-bold" required>
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
                        <select name="finish_number" class="form-control custom-input select2">
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
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="small font-weight-bold">حساب بدهکار (Debit Account Override)</label>
                                    <select name="override_debit_account_id" class="form-control custom-input select2">
                                        <option value="">Default: {{ $mapping->debit_account->account_name ?? 'System' }}</option>
                                        @foreach($allowedDebitAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="small font-weight-bold">حساب بستانکار (Credit Account Override)</label>
                                    <select name="override_credit_account_id" class="form-control custom-input select2">
                                        <option value="">Default: {{ $mapping->credit_account->account_name ?? 'System' }}</option>
                                        @foreach($allowedCreditAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 text-left">
                        <button class="btn btn-primary btn-block rounded-pill py-3 font-weight-bold shadow-lg" type="submit" style="background:var(--primary-amber); border:none;">
                            <i class="fa fa-save"></i> {{ $paymentEdit ? 'بروزرسانی تراکنش' : 'ثبت نهایی تراکنش' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Ledger Table -->
    <div class="premium-card">
        <div class="card-header-premium d-flex justify-content-between align-items-center" style="background: var(--dark-amber);">
            <h5><i class="fa fa-list-alt mr-2"></i> لجر محاسباتی تیم تیاری (Forensic Audit Ledger)</h5>
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
                            <th class="px-4">تاریخ</th>
                            <th>نوع</th>
                            <th>نمبر تیاری</th>
                            <th>شرح</th>
                            <th>ارز</th>
                            <th>نرخ</th>
                            <th>مقدار اصلی</th>
                            <th>معادل دالر</th>
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
                            <td class="hideOnPrint text-center">
                                <div class="btn-group">
                                    <a href="/dashboard/finishing-payments/{{$p->id}}/edit" class="btn btn-sm btn-outline-warning border-0">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    
                                    @php
                                        $transaction = \App\LedgerTransaction::where('source_type', 'payment')->where('source_id', $p->id)->first();
                                    @endphp
                                    @if($transaction)
                                        <a href="{{ route('accounting.journals.show', $transaction->id) }}" target="_blank" class="btn btn-sm btn-outline-success border-0" title="View Journal">
                                            <i class="fa fa-book"></i>
                                        </a>
                                    @endif

                                    <button onclick="deletePayment({{$p->id}})" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
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

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });

        function updateUsdPreview() {
            const amount = parseFloat($('#original_amount').val()) || 0;
            const rate = parseFloat($('#exchange_rate').val()) || 0;
            const baseAmount = (amount * rate).toFixed(4);
            
            $('#usd_truth_preview').text('$ ' + parseFloat(baseAmount).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
        }

        $('#currency_id').on('change', function() {
            const rate = $(this).find(':selected').data('rate');
            $('#exchange_rate').val(rate);
            updateUsdPreview();
        });

        $('#original_amount, #exchange_rate').on('input', updateUsdPreview);
        updateUsdPreview();
    });

    function deletePayment(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این سند مالی و تمام آثار حسابداری آن حذف خواهد شد!",
            icon: "warning",
            buttons: ["نخیر", "بلی، حذف شود"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/finishing-payments/' + id,
                    data: { '_token': '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("موفقانه حذف شد!", { icon: "success" }).then(() => location.reload());
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