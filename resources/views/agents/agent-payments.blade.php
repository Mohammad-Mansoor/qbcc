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
                پرداخت های نماینده (Agent Payments)
            </h3>
            <p class="text-muted">مدیریت معاملات مالی و اسناد پرداخت نمایندگان با سیستم چند ارزی</p>
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
                    <form action="{{ $paymentEdit ? '/dashboard/agent-payments/'.$paymentEdit->id : '/dashboard/agent-payments' }}" method="post" id="forensicPaymentForm">
                        @csrf
                        @if($paymentEdit) @method('PUT') @endif
                        <input type="hidden" name="agent_id" value="{{$agent->agent_id}}">

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
                                    <select name="type" class="form-control custom-input font-weight-bold">
                                        <option value="رسید" class="text-success" {{ ($paymentEdit && $paymentEdit->type == 'رسید') ? 'selected' : '' }}>رسید (Payment Received)</option>
                                        <option value="گرفت" class="text-danger" {{ ($paymentEdit && $paymentEdit->type == 'گرفت') ? 'selected' : '' }}>گرفت (Payment Sent)</option>
                                    </select>
                                    <small class="field-explanation text-right">آیا پول دریافت شده یا پرداخت شده؟</small>
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
                                    <textarea name="description" rows="4" class="form-control custom-input" placeholder="شرح کامل معامله را اینجا بنویسید..." required>{{ $paymentEdit ? $paymentEdit->description : '' }}</textarea>
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

    <!-- Ledger Table Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5><i class="fa fa-history mr-2"></i> سوابق معاملات (Transaction History)</h5>
                    <div id="exportButton"></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table premium-table table-hover mb-0" id="agent_payment_table">
                            <thead>
                                <tr class="text-right">
                                    <th>تاریخ (Date)</th>
                                    <th>شرح (Description)</th>
                                    <th>سند (Ref)</th>
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
                                    <td>{{ $pa->description }}</td>
                                    <td>
                                        <span class="badge badge-light p-2 border">
                                            @if($pa->check_number == 'نقد') نقد @else {{$pa->check_number}} @endif
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold text-primary">{{ $pa->currency_code ?: ($pa->amount > 0 ? 'USD' : 'AFN') }}</td>
                                    <td class="font-weight-bold" style="direction: ltr;">
                                        {{ number_format($pa->original_amount ?: ($pa->amount ?: $pa->amount_af), 2) }}
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
                                        @if(auth()->user()->role != 'AO' && ($pa->status == 0 || auth()->user()->role == 'SP'))
                                            <div class="btn-group">
                                                <a href="/dashboard/agent-payments/{{$pa->id}}/edit" class="btn btn-sm btn-outline-info" title="ویرایش">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button onclick="deletePayment({{$pa->id}} ,{{$pa->agent_id}})" class="btn btn-sm btn-outline-danger" title="حذف">
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
                                        {{ $balance >= 0 ? 'طلبکار' : 'بدهکار' }}: {{ number_format(abs($balance), 2) }} {{ $code }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr style="background: #e3f2fd;">
                                    <th colspan="3" class="text-right"><b>مجموع کل بیلانس (Base USD)</b></th>
                                    <td colspan="2" class="text-success text-right"><b>$ {{ number_format($totalBaseReceived, 2) }}</b></td>
                                    <td colspan="2" class="text-danger text-right"><b>$ {{ number_format($totalBaseSent, 2) }}</b></td>
                                    @php($baseBalance = $totalBaseReceived - $totalBaseSent)
                                    <td colspan="2" class="text-center font-weight-bold {{ $baseBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                                        بیلانس نهایی: $ {{ number_format(abs($baseBalance), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#check_id').select2();
        $('#currency_id').select2();

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
    });

    function deletePayment(id, agent_id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عمل قابل بازگشت نیست!",
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
</script>
@endsection
