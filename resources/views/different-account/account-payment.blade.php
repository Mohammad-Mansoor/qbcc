@extends('dsh.master')
@section('title', 'Forensic Ledger - ' . $account->name)

@section('content')
<style>
    /* QBCC PREMIUM DESIGN SYSTEM */
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-header-bg: #ffffff;
        --qbcc-border: #e2e8f0;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        --radius-xl: 20px;
        --radius-lg: 12px;
        --shadow-soft: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .account-hero {
        background: var(--qbcc-gradient); color: white; border-radius: var(--radius-xl);
        padding: 30px; margin-bottom: 25px; box-shadow: var(--shadow-soft);
        display: flex; justify-content: space-between; align-items: center;
    }

    .hero-stats { display: flex; gap: 30px; }
    .hero-stat-item { text-align: left; }
    .hero-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; opacity: 0.8; letter-spacing: 1px; }
    .hero-stat-value { font-size: 24px; font-weight: 800; }

    /* TRANSACTION MODAL STYLE */
    .qbcc-modal-content { border-radius: var(--radius-xl); border: none; }
    .modal-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px 30px; }
    .form-control-modern { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px 15px; font-size: 14px; }

    /* FORENSIC TABLE */
    .glass-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-soft); overflow: hidden; }
    .forensic-table { width: 100%; border-collapse: collapse; }
    .forensic-table thead th { background: #f8fafc; padding: 15px; text-align: right; font-weight: 700; color: #64748b; font-size: 12px; border-bottom: 2px solid #e2e8f0; }
    .forensic-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
    .forensic-table tbody tr:hover { background: #f8fafc; }
    .forensic-table td { padding: 15px; vertical-align: middle; }

    .type-badge { padding: 4px 12px; border-radius: 6px; font-weight: 800; font-size: 11px; }
    .badge-receipt { background: #d1fae5; color: #065f46; }
    .badge-payment { background: #fee2e2; color: #991b1b; }

    .forensic-tag { font-size: 10px; font-weight: 800; background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; margin-right: 5px; }
</style>

<div class="container-fluid">
    <!-- HERO SECTION -->
    <div class="account-hero">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mr-4" style="width: 70px; height: 70px; font-size: 30px; font-weight: 900;">
                {{ mb_substr($account->name, 0, 1) }}
            </div>
            <div>
                <h2 class="mb-1 font-weight-bold">{{ $account->name }}</h2>
                <div class="small opacity-80"><i class="feather icon-phone mr-1"></i> {{ $account->phone }} | <i class="feather icon-map-pin mr-1"></i> {{ $account->address }}</div>
            </div>
        </div>
        <div class="hero-stats hideOnPrint">
            @foreach($totals as $t)
            <div class="hero-stat-item">
                <div class="hero-stat-label">بیلانس ({{ $t->currency_code }})</div>
                <div class="hero-stat-value" dir="ltr">{{ number_format($t->remaining, 2) }}</div>
            </div>
            @endforeach
        </div>
    </div>

    @if(session("status"))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{session('status')}}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 hideOnPrint">
        <div>
            <button class="btn btn-primary rounded-lg px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#transactionModal">
                <i class="feather icon-plus mr-1"></i> ثبت تراکنش جدید
            </button>
            <a href="/dashboard/different-account" class="btn btn-outline-secondary rounded-lg px-4 ml-2">
                <i class="feather icon-arrow-right mr-1"></i> بازگشت
            </a>
        </div>
        <div class="btn-group">
            <button class="btn btn-white shadow-sm border" onclick="window.print()">
                <i class="feather icon-printer mr-1"></i> چاپ صورت حساب
            </button>
            <a href="/dashboard/different-account-payments-all/{{$account->id}}" class="btn btn-white shadow-sm border ml-1">نمایش همه</a>
        </div>
    </div>

    <!-- FORENSIC LEDGER -->
    <div class="card glass-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="forensic-table" id="account_payment">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>نوعیت</th>
                            <th>مبلغ اصلی</th>
                            <th>نرخ تبدیل</th>
                            <th>معادل دالر (GL)</th>
                            <th>شرح و توضیحات</th>
                            <th class="text-center">حالت</th>
                            <th class="text-center hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $pa)
                        <tr>
                            <td class="font-weight-bold">{{ $pa->date }}</td>
                            <td>
                                <span class="type-badge {{ $pa->type == 'رسید' ? 'badge-receipt' : 'badge-payment' }}">
                                    {{ $pa->type }}
                                </span>
                            </td>
                            <td>
                                <div class="font-weight-bold" dir="ltr">
                                    {{ number_format($pa->amount, 2) }} 
                                    <span class="small text-muted">{{ $pa->currency_code }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small text-muted" dir="ltr">1 {{ $pa->currency_code }} = {{ number_format($pa->exchange_rate, 4) }} USD</div>
                            </td>
                            <td>
                                <div class="text-primary font-weight-bold" dir="ltr">
                                    {{ number_format($pa->base_amount, 2) }} <small>USD</small>
                                </div>
                            </td>
                            <td class="small">{{ $pa->description }}</td>
                            <td class="text-center">
                                @if($pa->status == 0)
                                    <span class="badge badge-warning">در انتظار تایید</span>
                                @else
                                    <span class="badge badge-success">ثبت در دفاتر</span>
                                @endif
                            </td>
                            <td class="text-center hideOnPrint">
                                <div class="btn-group">
                                    @if($pa->status == 0 || auth()->user()->role == 'SP')
                                    <a href="/dashboard/different-account-payments/{{$pa->id}}/edit" class="btn btn-sm btn-light-info text-info mr-1">
                                        <i class="feather icon-edit-2"></i>
                                    </a>
                                    @endif
                                    
                                    @if($pa->ledger_transaction_id)
                                        <a href="{{ route('accounting.journals.show', $pa->ledger_transaction_id) }}" target="_blank" class="btn btn-sm btn-light-success text-success" title="مشاهده سند حسابداری">
                                            <i class="feather icon-book"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!isset($all))
            <div class="p-3 border-top hideOnPrint">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL: TRANSACTION -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content qbcc-modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">ثبت تراکنش جدید برای {{ $account->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $paymentEdit ? '/dashboard/different-account-payments/'.$paymentEdit->id : '/dashboard/different-account-payments' }}" method="post">
                @csrf
                @if($paymentEdit) @method('PUT') @endif
                <input type="hidden" name="account_id" value="{{$account->id}}">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">مبلغ تراکنش</label>
                                <input type="number" step="any" name="amount" value="{{ $paymentEdit ? $paymentEdit->amount : '' }}" class="form-control-modern w-100" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">ارز (Currency)</label>
                                <select name="currency_code" id="currency_code" class="form-control-modern w-100" required>
                                    @foreach($currencies as $curr)
                                        <option value="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}" {{ ($paymentEdit && $paymentEdit->currency_code == $curr->code) ? 'selected' : '' }}>{{ $curr->name }} ({{ $curr->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">نرخ تبدیل (به USD)</label>
                                <input type="number" step="0.000001" name="exchange_rate" id="exchange_rate" value="{{ $paymentEdit ? $paymentEdit->exchange_rate : '1.000000' }}" class="form-control-modern w-100" required {{ ($paymentEdit && $paymentEdit->currency_code == 'USD') ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">نوع معامله</label>
                                <select name="type" class="form-control-modern w-100" required>
                                    <option value="رسید" {{ ($paymentEdit && $paymentEdit->type == 'رسید') ? 'selected' : '' }}>رسید (ما گرفتیم)</option>
                                    <option value="گرفت" {{ ($paymentEdit && $paymentEdit->type == 'گرفت') ? 'selected' : '' }}>گرفت (ما دادیم)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">تاریخ معامله</label>
                                <input type="date" name="date" value="{{ $paymentEdit ? $paymentEdit->date : date('Y-m-d') }}" class="form-control-modern w-100" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">معادل دالر (Calculated USD)</label>
                                <input type="text" id="calculated_usd" class="form-control-modern w-100 bg-light font-weight-bold text-primary" readonly value="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">حساب بدهکار (Debit Account)</label>
                                <select name="override_debit_account_id" id="override_debit_account_id" class="form-control-modern w-100" required style="font-family: inherit;">
                                    @foreach($chartOfAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ (($paymentEdit && $paymentEdit->override_debit_account_id == $acc->id) || (!$paymentEdit && $mappingIn && $mappingIn->debit_account_id == $acc->id)) ? 'selected' : '' }} data-cash="{{ $acc->is_cash_account }}">
                                            {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->account_type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">حساب بستانکار (Credit Account)</label>
                                <select name="override_credit_account_id" id="override_credit_account_id" class="form-control-modern w-100" required style="font-family: inherit;">
                                    @foreach($chartOfAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ (($paymentEdit && $paymentEdit->override_credit_account_id == $acc->id) || (!$paymentEdit && $mappingOut && $mappingOut->credit_account_id == $acc->id)) ? 'selected' : '' }} data-cash="{{ $acc->is_cash_account }}">
                                            {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->account_type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="small font-weight-bold text-muted">توضیحات و بابت</label>
                        <textarea name="description" class="form-control-modern w-100" rows="2" placeholder="مثلا: بابت کرایه موتر یا خرید وسایل..." required>{{ $paymentEdit ? $paymentEdit->description : '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 font-weight-bold shadow">
                        <i class="feather icon-save mr-1"></i> تایید و ثبت تراکنش
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    $(document).ready(function () {
        @if($paymentEdit) $('#transactionModal').modal('show'); @endif

        function calculateUSD() {
            let amount = parseFloat($('input[name="amount"]').val()) || 0;
            let rate = parseFloat($('#exchange_rate').val()) || 0;
            let usd = (amount * rate).toFixed(2);
            $('#calculated_usd').val(usd + " USD");
        }

        $("#currency_code").change(function() {
            let selected = $(this).find(':selected');
            let rate = selected.data('rate');
            
            $("#exchange_rate").val(rate);
            
            if($(this).val() === "USD") {
                $("#exchange_rate").attr("readonly", true);
            } else {
                $("#exchange_rate").attr("readonly", false);
            }
            calculateUSD();
        });

        $('input[name="amount"], #exchange_rate').on('input', function() {
            calculateUSD();
        });

        // Initial calculation on load
        calculateUSD();

        // Dynamic select accounts filtering and defaults
        const mappingInDebit = "{{ $mappingIn->debit_account_id ?? '' }}";
        const mappingInCredit = "{{ $mappingIn->credit_account_id ?? '' }}";
        const mappingOutDebit = "{{ $mappingOut->debit_account_id ?? '' }}";
        const mappingOutCredit = "{{ $mappingOut->credit_account_id ?? '' }}";

        function filterAccounts() {
            let type = $('select[name="type"]').val();
            let debitSelect = $('#override_debit_account_id');
            let creditSelect = $('#override_credit_account_id');

            // Enable all options first
            debitSelect.find('option').prop('disabled', false);
            creditSelect.find('option').prop('disabled', false);

            if (type === 'رسید') {
                // Receipt (رسید): Debit side must be Cash. Credit side is arbitrary.
                debitSelect.find('option').each(function() {
                    let isCash = $(this).data('cash') == 1;
                    if (!isCash) {
                        $(this).prop('disabled', true);
                    }
                });

                // Set defaults if currently selected is disabled or if opening a new form
                if (debitSelect.find('option:selected').is(':disabled') || !debitSelect.val()) {
                    debitSelect.val(mappingInDebit);
                }
                if (!creditSelect.val() || creditSelect.val() == mappingOutCredit) {
                    creditSelect.val(mappingInCredit);
                }
            } else {
                // Payment (گرفت): Credit side must be Cash. Debit side is arbitrary.
                creditSelect.find('option').each(function() {
                    let isCash = $(this).data('cash') == 1;
                    if (!isCash) {
                        $(this).prop('disabled', true);
                    }
                });

                // Set defaults if currently selected is disabled or if opening a new form
                if (creditSelect.find('option:selected').is(':disabled') || !creditSelect.val()) {
                    creditSelect.val(mappingOutCredit);
                }
                if (!debitSelect.val() || debitSelect.val() == mappingInDebit) {
                    debitSelect.val(mappingOutDebit);
                }
            }
        }

        $('select[name="type"]').change(filterAccounts);
        filterAccounts(); // run initially
    });
</script>
@endsection