@extends('dsh.master')

@section('title', 'مصارف ماهانه - ' . $month_obj->month_name)

@section('content')
<style>
    /* PREMIUM EXPENSE UI STYLES - PURPLE THEME */
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
        background: linear-gradient(135deg, #4a148c 0%, #7b1fa2 100%);
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
        border: 1px solid #f3e5f5;
        background: #fff;
        padding: 15px;
        height: 100%;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(74, 20, 140, 0.1); }
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
        background: #f3e5f5;
        border-right: 4px solid #7b1fa2;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .truth-label { font-size: 0.8rem; color: #4a148c; font-weight: 600; }
    .truth-value { font-size: 1.4rem; color: #4a148c; font-weight: 800; font-family: 'Courier New', monospace; }

    .custom-input {
        border-radius: 10px;
        border: 2px solid #f3e5f5;
        padding: 12px 15px;
        transition: all 0.2s;
    }
    .custom-input:focus {
        border-color: #7b1fa2;
        box-shadow: 0 0 0 0.2rem rgba(123, 31, 162, 0.1);
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
        color: #4a148c;
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
        background: linear-gradient(135deg, #311b92 0%, #4527a0 100%);
        border-radius: 15px;
        color: white;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(49, 27, 146, 0.2);
    }
</style>

<div class="container-fluid mt-4" id="expense-dashboard">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-7 text-right">
            <h3 class="font-weight-bold text-white">
                <i class="fa fa-credit-card text-white"></i> 
                مصارف ماه: {{ $month_obj->month_name }} {{ $month_obj->year_name }}
            </h3>
            <p class="text-white">مدیریت و تصفیه مصارف اداری و عملیاتی (Admin & OpEx)</p>
        </div>
        <div class="col-md-5 text-left">
            <div class="btn-group">
                <a href="/dashboard/monthly-expense-accounts" class="btn btn-outline-light btn-sm rounded-pill px-3 text-white" style="border-color: white;">
                    <i class="fa fa-arrow-right"></i> لست حسابات
                </a>
                <button class="btn btn-outline-light btn-sm rounded-pill px-3 ml-2 text-white" onclick="window.print()" style="border-color: white;">
                    <i class="fa fa-print"></i> چاپ گزارش
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

    <!-- Category Summaries -->
    <div class="row mb-4">
        @php
            $icons = [
                'خوراکه' => ['icon' => 'fa-cutlery', 'bg' => '#e1f5fe', 'color' => '#01579b'],
                'متفرقه دفتر' => ['icon' => 'fa-ellipsis-h', 'bg' => '#f3e5f5', 'color' => '#4a148c'],
                'کرایه و برق' => ['icon' => 'fa-bolt', 'bg' => '#fff3e0', 'color' => '#e65100'],
                'ترانسپورت' => ['icon' => 'fa-bus', 'bg' => '#e8f5e9', 'color' => '#1b5e20'],
                'برداشت' => ['icon' => 'fa-money', 'bg' => '#ffebee', 'color' => '#b71c1c'],
                'ترمیمات و تیل' => ['icon' => 'fa-wrench', 'bg' => '#eceff1', 'color' => '#263238'],
                'معاشات' => ['icon' => 'fa-users', 'bg' => '#ede7f6', 'color' => '#311b92'],
                'اجوره' => ['icon' => 'fa-briefcase', 'bg' => '#f1f8e9', 'color' => '#33691e'],
            ];
            $grandTotalBase = 0;
        @endphp

        @foreach($categoryTotals as $catName => $totals)
            @php
                $style = $icons[$catName] ?? ['icon' => 'fa-tag', 'bg' => '#f5f5f5', 'color' => '#616161'];
                $catBaseSum = $totals->sum('total_base');
                $grandTotalBase += $catBaseSum;
            @endphp
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="stat-icon" style="background-color: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                            <i class="fa {{ $style['icon'] }} fa-lg"></i>
                        </div>
                        <div class="text-left">
                            <span class="badge badge-light border" style="font-size: 0.7rem;">Exp Detail</span>
                        </div>
                    </div>
                    <h6 class="font-weight-bold text-dark mb-1">{{ $catName }}</h6>
                    <div class="mt-2">
                        @foreach($totals as $t)
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">{{ $t->currency_code }}:</span>
                                <span class="font-weight-bold">{{ number_format($t->total_original, 2) }}</span>
                            </div>
                        @endforeach
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="small font-weight-bold text-purple" style="color:#4a148c">معادل دالر:</span>
                            <span class="font-weight-bold" style="color:#4a148c">$ {{ number_format($catBaseSum, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Grand Total Banner -->
    <div class="grand-total-banner text-center">
        <div class="row align-items-center">
            <div class="col-md-4 border-left border-white-50">
                <h5 class="mb-0 text-white">تعداد تراکنش‌ها</h5>
                <h2 class="font-weight-bold mb-0 text-white">{{ $expenses->total() }}</h2>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 50px; height: 50px;">
                    <i class="fa fa-university text-purple fa-lg" style="color:#4a148c"></i>
                </div>
                <h4 class="font-weight-bold mb-0 text-white">مجموع کل مصارف ماه</h4>
            </div>
            <div class="col-md-4 border-right border-white-50">
                <h5 class="mb-0 text-white">مجموع نهایی (USD Truth)</h5>
                <h2 class="font-weight-bold mb-0 text-white">$ {{ number_format($grandTotalBase, 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Entry Form Section -->
    @if(!$search)
    <div class="premium-card">
        <div class="card-header-premium">
            <h5><i class="fa fa-plus-circle mr-2"></i> {{ $expenseEdit ? 'ویرایش سند مصرف (Edit Expense)' : 'ثبت سند مصرف جدید (New Entry)' }}</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ $expenseEdit ? '/dashboard/new-monthly-expense-payments/'.$expenseEdit->id : '/dashboard/new-monthly-expense-payments' }}" method="post" id="forensicExpenseForm">
                @csrf
                @if($expenseEdit) @method('PUT') @endif
                <input type="hidden" name="month_id" value="{{ $month_obj->me_id }}">

                <div class="row">
                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">کتگوری مصرف (Category)</label>
                        <select name="category" class="form-control custom-input font-weight-bold" required>
                            @php
                                $cats = ['خوراکه', 'متفرقه دفتر', 'کرایه و برق', 'ترانسپورت', 'برداشت', 'ترمیمات و تیل', 'معاشات', 'اجوره'];
                            @endphp
                            @foreach($cats as $cat)
                                <option value="{{ $cat }}" {{ ($expenseEdit && $expenseEdit->category == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">واحد پولی (Currency)</label>
                        <select name="currency_id" id="currency_id" class="form-control custom-input font-weight-bold" required>
                            @foreach($currencies as $curr)
                                <option value="{{$curr->id}}" data-rate="{{$curr->exchange_rate}}" 
                                    {{ ($expenseEdit && ($expenseEdit->currency_code == $curr->code || $expenseEdit->currency == $curr->id)) || (!$expenseEdit && $curr->code == 'USD') ? 'selected' : '' }}>
                                    {{$curr->name}} ({{$curr->code}})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 form-group mb-4 text-right">
                        <label class="field-label">مقدار پول (Amount)</label>
                        <input type="number" step="0.0001" name="amount" id="original_amount" class="form-control custom-input font-weight-bold text-purple" 
                               value="{{ $expenseEdit ? ($expenseEdit->original_amount ?: $expenseEdit->amount) : old('amount') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="truth-preview-box">
                            <span class="truth-label"><i class="fa fa-shield"></i> معادل دالر (USD Normalized):</span><br>
                            <span class="truth-value" id="usd_truth_preview">$ 0.0000</span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 form-group mb-4">
                        <label class="field-label">تاریخ (Date)</label>
                        <input type="date" name="date" class="form-control custom-input" value="{{ $expenseEdit ? $expenseEdit->date : date('Y-m-d') }}" required>
                    </div>

                    <div class="col-lg-9 col-md-12 form-group mb-4">
                        <label class="field-label">توضیحات (Description)</label>
                        <input type="text" name="description" class="form-control custom-input" value="{{ $expenseEdit ? $expenseEdit->description : '' }}" placeholder="شرح مصرف را وارد کنید..." required>
                    </div>

                    <div class="col-lg-4 col-md-6 form-group mb-4">
                        <label class="field-label">حساب بدهکار سفارشی (Manual Debit Override)</label>
                        <select name="override_debit_account_id" id="override_debit_account_id" class="form-control custom-input select2">
                            <option value="">-- پیشفرض سیستم (Default) --</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 form-group mb-4">
                        <label class="field-label">حساب بستانکار سفارشی (Manual Credit Override)</label>
                        <select name="override_credit_account_id" id="override_credit_account_id" class="form-control custom-input select2">
                            <option value="">-- پیشفرض سیستم (Default) --</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-12 text-left mt-4">
                        <button class="btn btn-primary btn-block rounded-pill py-3 font-weight-bold shadow-lg" type="submit" style="background:#4a148c; border:none;">
                            <i class="fa fa-save"></i> {{ $expenseEdit ? 'بروزرسانی سند' : 'ثبت نهایی' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Ledger Table -->
    <div class="premium-card" id="expense-print-area">
        <div class="card-header-premium d-flex justify-content-between align-items-center" style="background: #311b92;">
            <h5><i class="fa fa-list-alt mr-2"></i> جزئیات تراکنش‌های مالی (Forensic Expense Ledger)</h5>
            <div id="exportButton"></div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0 text-right" id="expense_list_table">
                    <thead>
                        <tr>
                            <th class="px-4">تاریخ</th>
                            <th>کتگوری</th>
                            <th>شرح مصرف</th>
                            <th>ارز</th>
                            <th>مقدار اصلی</th>
                            <th>نرخ تبدیل</th>
                            <th>معادل دالر</th>
                            <th class="hideOnPrint text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $e)
                        <tr class="ur{{$e->id}}">
                            <td class="px-4 font-weight-bold text-muted">{{ $e->date }}</td>
                            <td>
                                <span class="badge badge-soft-purple px-3 py-2 rounded-pill font-weight-bold" style="background: #f3e5f5; color: #4a148c;">
                                    {{ $e->category }}
                                </span>
                            </td>
                            <td class="small">
                                {{ $e->description }}
                                @if(isset($e->debit_account_code) || isset($e->credit_account_code))
                                    <div class="mt-1 small" style="color: #7b1fa2;">
                                        @if(isset($e->debit_account_code))
                                            <span class="badge badge-light border" style="color: #7b1fa2;"><i class="fa fa-long-arrow-left"></i> بدهکار: [{{ $e->debit_account_code }}] {{ $e->debit_account_name }}</span>
                                        @endif
                                        @if(isset($e->credit_account_code))
                                            <span class="badge badge-light border" style="color: #7b1fa2;"><i class="fa fa-long-arrow-right"></i> بستانکار: [{{ $e->credit_account_code }}] {{ $e->credit_account_name }}</span>
                                        @endif
                                    </div>
                                @elseif(isset($e->override_debit_account_id) || isset($e->override_credit_account_id))
                                    @php
                                        $dAcc = $e->override_debit_account_id ? \App\ChartOfAccount::find($e->override_debit_account_id) : null;
                                        $cAcc = $e->override_credit_account_id ? \App\ChartOfAccount::find($e->override_credit_account_id) : null;
                                    @endphp
                                    <div class="mt-1 small" style="color: #7b1fa2;">
                                        @if($dAcc)
                                            <span class="badge badge-light border" style="color: #7b1fa2;"><i class="fa fa-long-arrow-left"></i> بدهکار: [{{ $dAcc->account_code }}] {{ $dAcc->account_name }}</span>
                                        @endif
                                        @if($cAcc)
                                            <span class="badge badge-light border" style="color: #7b1fa2;"><i class="fa fa-long-arrow-right"></i> بستانکار: [{{ $cAcc->account_code }}] {{ $cAcc->account_name }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="font-weight-bold text-purple">{{ $e->currency_code ?: ($e->currency == 2 ? 'USD' : 'AFN') }}</td>
                            <td class="font-weight-bold" style="direction: ltr;">{{ number_format($e->original_amount ?: $e->amount, 2) }}</td>
                            <td class="text-muted small" style="direction: ltr;">{{ number_format($e->exchange_rate ?: ($e->dollar_rate ?: 1), 6) }}</td>
                            <td class="font-weight-bold text-dark" style="direction: ltr;">$ {{ number_format($e->base_amount ?: $e->amount, 2) }}</td>
                            <td class="hideOnPrint text-center">
                                <div class="btn-group">
                                    <a href="/dashboard/new-monthly-expense-payments/{{$e->id}}/edit" class="btn btn-sm btn-outline-purple border-0">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    
                                    @php
                                        $transaction = \App\LedgerTransaction::where('source_type', 'NewMonthlyExpenseBalance')->where('source_id', $e->id)->first();
                                    @endphp
                                    @if($transaction)
                                        <a href="{{ route('accounting.journals.show', $transaction->id) }}" target="_blank" class="btn btn-sm btn-outline-success border-0" title="Journal">
                                            <i class="fa fa-book"></i>
                                        </a>
                                    @endif

                                    <button onclick="deleteExpense({{$e->id}})" class="btn btn-sm btn-outline-danger border-0">
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
        <div class="card-footer bg-white border-0 py-3 text-left">
            {{ $expenses->links() }}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });

        const allowedAccounts = @json($allowedAccountsMap ?? []);
        const categoryMappingKeys = {
            'خوراکه': 'EXP_FOOD',
            'متفرقه دفتر': 'EXP_MISC_OFFICE',
            'کرایه و برق': 'EXPENSE_کرایه_و_برق',
            'ترانسپورت': 'EXP_TRANS',
            'برداشت': 'CASH_OUT',
            'ترمیمات و تیل': 'EXP_FUEL',
            'معاشات': 'PAYROLL_ACCRUAL',
            'اجوره': 'EXP_WAGES'
        };

        function populateOverrideAccounts() {
            const category = $('select[name="category"]').val();
            const key = categoryMappingKeys[category];
            const data = allowedAccounts[key] || { debit: [], credit: [] };

            const selectedDebit = "{{ $expenseEdit ? $expenseEdit->override_debit_account_id : '' }}";
            const selectedCredit = "{{ $expenseEdit ? $expenseEdit->override_credit_account_id : '' }}";

            // Debit Select
            const debitSelect = $('#override_debit_account_id');
            debitSelect.empty().append('<option value="">-- پیشفرض سیستم (Default) --</option>');
            data.debit.forEach(acc => {
                const selected = acc.id == selectedDebit ? 'selected' : '';
                debitSelect.append(`<option value="${acc.id}" ${selected}>[${acc.code}] ${acc.name}</option>`);
            });

            // Credit Select
            const creditSelect = $('#override_credit_account_id');
            creditSelect.empty().append('<option value="">-- پیشفرض سیستم (Default) --</option>');
            data.credit.forEach(acc => {
                const selected = acc.id == selectedCredit ? 'selected' : '';
                creditSelect.append(`<option value="${acc.id}" ${selected}>[${acc.code}] ${acc.name}</option>`);
            });

            debitSelect.trigger('change.select2');
            creditSelect.trigger('change.select2');
        }

        $('select[name="category"]').on('change', populateOverrideAccounts);
        populateOverrideAccounts();

        function updateUsdPreview() {
            const amount = parseFloat($('#original_amount').val()) || 0;
            const selectedCurrency = $('#currency_id option:selected');
            const rate = parseFloat(selectedCurrency.data('rate')) || 0;
            const baseAmount = (amount * rate).toFixed(4);
            
            $('#usd_truth_preview').text('$ ' + parseFloat(baseAmount).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
        }

        $('#original_amount, #currency_id').on('input change', updateUsdPreview);
        updateUsdPreview();

        // Export
        $("#expense_list_table").tableExport({
            formats: ["xlsx"],
            filename: "expense_ledger_{{ $month_obj->month_name }}",
            bootstrap: true,
            position: "bottom",
            RTL: true
        });
        var $buttons = $('#expense_list_table').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });

    function deleteExpense(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این سند هزینه و تمام آثار حسابداری آن حذف خواهد شد!",
            icon: "warning",
            buttons: ["نخیر", "بلی، حذف شود"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/new-monthly-expense-payments/' + id,
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
