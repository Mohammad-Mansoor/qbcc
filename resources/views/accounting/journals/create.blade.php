@extends('dsh.master')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="feather icon-alert-triangle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="feather icon-alert-triangle mr-2"></i>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <br>
    <form action="{{ route('accounting.journals.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Header Section -->
            <div class="col-md-12 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #4099ff, #73b4ff);">
                    <div class="card-body p-4 text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="text-white font-weight-bold mb-1">ثبت سند جدید (Journal Voucher)</h3>
                                <p class="mb-0 opacity-80">لطفاً جزئیات تراکنش را وارد کرده و مطمئن شوید که مجموع دیبت و کریدت برابر است.</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="submit" class="btn btn-white text-primary font-weight-bold px-4 shadow" style="border-radius: 10px;">
                                    <i class="feather icon-check-circle mr-2"></i>ثبت و تایید نهایی
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Info Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 font-weight-bold text-dark">اطلاعات عمومی سند</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">تاریخ سند (Date) <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control bg-light border-0" required>
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> تاریخی که تراکنش در آن رخ داده است.</small>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">شناسه دفتر کل (GL ID)</label>
                            <input type="text" name="journal_id" value="{{ old('journal_id', $nextJournalId) }}" class="form-control bg-light border-0 font-weight-bold text-primary" placeholder="مثلاً: JV-2026-00001" readonly>
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> شناسه دفتر کل خودکار تولید شده توسط سیستم.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">نمبر سند / مرجع (Reference)</label>
                            <input type="text" name="reference" value="{{ old('reference') }}" class="form-control bg-light border-0" placeholder="مثلاً: JV-1001">
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> نمبر فزیکی سند یا مرجع پیگیری (اختیاری).</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">نوعیت سند (Voucher Type) <span class="text-danger">*</span></label>
                            <select name="journal_type" class="form-control bg-light border-0" required>
                                <option value="journal">Journal (روزنامچه عمومی)</option>
                                <option value="payment">Payment (سند تادیاتی)</option>
                                <option value="receipt">Receipt (سند رسید)</option>
                                <option value="adjustment">Adjustment (سند تعدیلی)</option>
                            </select>
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> دسته‌بندی سند برای گزارشات بهتر.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">نوعیت طرف معامله (Party Type)</label>
                            <select id="party_type" name="party_type" class="form-control bg-light border-0" onchange="headerPartyTypeChanged(this)">
                                <option value="">-- بدون طرف معامله --</option>
                                <option value="App\Customer">مشتری (Customer)</option>
                                <option value="App\Agents">نماینده (Agent)</option>
                                <option value="App\OfficeEmployee">کارمند (Employee)</option>
                                <option value="App\StringSeller">فروشنده تار (String Seller)</option>
                                <option value="App\WashingTeam">تیم شستشو (Washing Team)</option>
                                <option value="App\FinishingTeam">تیم پرداخت (Finishing Team)</option>
                                <option value="App\Kachaee">تیم کچه ای (Kachaee)</option>
                                <option value="App\NewDifferentAccount">حساب متفرقه جدید (New Different Account)</option>
                                <option value="App\DifferentAccount">حساب متفرقه (Different Account)</option>
                            </select>
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> در صورت تعلق کل سند به یک شخص/طرف معامله انتخاب کنید.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">طرف معامله (Party)</label>
                            <select id="party_id" name="party_id" class="form-control party-select" disabled>
                                <option value="">-- بدون طرف معامله --</option>
                            </select>
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> شخص یا طرف معامله مشخص سند.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">تفصیلات کلی (Description) <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control bg-light border-0" rows="4" placeholder="شرح مختصری از تراکنش..." required></textarea>
                            <small class="text-info mt-1 d-block"><i class="feather icon-info"></i> توضیح کلی درباره ماهیت این سند حسابداری.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ledger Entries Section -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold text-dark">ردیف‌های حسابداری (Accounting Lines)</h5>
                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="addRow()">
                            <i class="feather icon-plus"></i> افزودن ردیف
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0" id="journal-table">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th style="width: 50%">حساب (Account)</th>
                                        <th style="width: 23%">دیبت (Debit)</th>
                                        <th style="width: 23%">کریدت (Credit)</th>
                                        <th style="width: 4%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be added here -->
                                    <tr class="entry-row">
                                        <td class="p-3">
                                            <select name="entries[0][account_id]" class="form-control select2" required>
                                                <option value="">-- انتخاب حساب --</option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-3">
                                            <input type="number" step="0.01" name="entries[0][debit]" class="form-control bg-light border-0 debit-input" value="0" onchange="calculateTotals()">
                                        </td>
                                        <td class="p-3">
                                            <input type="number" step="0.01" name="entries[0][credit]" class="form-control bg-light border-0 credit-input" value="0" onchange="calculateTotals()">
                                        </td>
                                        <td class="p-3"></td>
                                    </tr>
                                    <tr class="entry-row">
                                        <td class="p-3">
                                            <select name="entries[1][account_id]" class="form-control select2" required>
                                                <option value="">-- انتخاب حساب --</option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-3">
                                            <input type="number" step="0.01" name="entries[1][debit]" class="form-control bg-light border-0 debit-input" value="0" onchange="calculateTotals()">
                                        </td>
                                        <td class="p-3">
                                            <input type="number" step="0.01" name="entries[1][credit]" class="form-control bg-light border-0 credit-input" value="0" onchange="calculateTotals()">
                                        </td>
                                        <td class="p-3"></td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="font-weight-bold">
                                        <td colspan="1" class="text-right p-3">مجموع (Totals):</td>
                                        <td class="p-3 text-primary" id="total-debit">$0.00</td>
                                        <td class="p-3 text-primary" id="total-credit">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-4">
                        <div id="balance-alert" class="alert alert-danger d-none">
                            <i class="feather icon-alert-triangle mr-2"></i> توازن برقرار نیست! مجموع دیبت و کریدت باید برابر باشند.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('footer-plugins')
<script>
    let rowCount = 2;

    function addRow() {
        let tbody = document.querySelector('#journal-table tbody');
        let newRow = document.createElement('tr');
        newRow.className = 'entry-row';
        newRow.innerHTML = `
            <td class="p-3">
                <select name="entries[\${rowCount}][account_id]" class="form-control select2" required>
                    <option value="">-- انتخاب حساب --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-3">
                <input type="number" step="0.01" name="entries[\${rowCount}][debit]" class="form-control bg-light border-0 debit-input" value="0" onchange="calculateTotals()">
            </td>
            <td class="p-3">
                <input type="number" step="0.01" name="entries[\${rowCount}][credit]" class="form-control bg-light border-0 credit-input" value="0" onchange="calculateTotals()">
            </td>
            <td class="p-3 text-center">
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" onclick="this.closest('tr').remove(); calculateTotals();">
                    <i class="feather icon-trash-2"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        $(newRow).find('.select2').select2();
        rowCount++;
    }

    function headerPartyTypeChanged(selectEl) {
        let partySelect = document.querySelector('#party_id');
        let partyType = selectEl.value;

        // Destroy existing select2 if initialized
        if ($(partySelect).hasClass("select2-hidden-accessible")) {
            $(partySelect).select2('destroy');
        }

        if (!partyType) {
            partySelect.innerHTML = '<option value="">-- بدون طرف معامله --</option>';
            partySelect.disabled = true;
            return;
        }

        partySelect.disabled = false;
        partySelect.innerHTML = '<option value=""></option>';

        // Initialize Select2 with AJAX to load parties dynamically
        $(partySelect).select2({
            placeholder: '-- انتخاب طرف معامله --',
            allowClear: true,
            ajax: {
                url: '{{ route("accounting.journals.api.parties") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: partyType,
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
        
        // Trigger opening the dropdown immediately
        $(partySelect).select2('open');
    }

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        
        document.querySelectorAll('.debit-input').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });
        
        document.querySelectorAll('.credit-input').forEach(input => {
            totalCredit += parseFloat(input.value) || 0;
        });

        document.querySelector('#total-debit').innerText = '$' + totalDebit.toFixed(2);
        document.querySelector('#total-credit').innerText = '$' + totalCredit.toFixed(2);

        let alert = document.querySelector('#balance-alert');
        if (totalDebit.toFixed(2) !== totalCredit.toFixed(2)) {
            alert.classList.remove('d-none');
        } else {
            alert.classList.add('d-none');
        }
    }

    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<style>
    .btn-white { background: #fff; border: none; }
    .btn-white:hover { background: #f8f9fa; }
    .select2-container--default .select2-selection--single { background-color: #f8f9fa; border: none; height: 40px; line-height: 40px; }
</style>
@endsection
