@extends('dsh.master')
@section('title' , 'مدیریت نقدینگی و تزریق سرمایه')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Action Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-money text-primary mr-2"></i> مدیریت نقدینگی</h3>
            <p class="text-muted small mb-0">تزریق سرمایه به دخل و درخواست‌های نقدینگی دفاتر</p>
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="printPage('creditPrint')">
                <i class="fa fa-print mr-1"></i> چاپ گزارش
            </button>
        </div>
    </div>

    <!-- Info Box (Dari) -->
    <div class="alert bg-soft-info border-0 rounded-lg p-4 mb-4 shadow-sm text-right">
        <div class="d-flex align-items-start">
            <div class="ml-3">
                <i class="fa fa-info-circle fa-2x text-info"></i>
            </div>
            <div>
                <h6 class="font-weight-bold text-info mb-1">رهنمای بخش نقدینگی:</h6>
                <p class="mb-0 text-dark small leading-relaxed">
                    این بخش برای مدیریت گردش پول نقد در دفاتر استفاده می‌شود. 
                    <br>
                    • <strong>مدیر کل (SP):</strong> مستقیماً پول جدید را به سیستم وارد می‌کند (تزریق سرمایه). این عمل باعث افزایش موجودی دخل عمومی و ثبت سند در روزنامچه می‌گردد.
                    <br>
                    • <strong>دفاتر فرعی (CO/SO):</strong> در صورت نیاز به پول نقد، از این فورم استفاده کرده و درخواست خود را به مدیر کل ارسال می‌کنند. پس از تأیید، مبلغ از دخل عمومی کسر و به دخل دفتر مربوطه اضافه می‌گردد.
                </p>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'SP')
    <!-- Admin Stats Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg bg-gradient-warning text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-7 small font-weight-bold">موجودی دخل مرکز (CO)</p>
                        <h4 class="mb-0 font-weight-bold">${{ number_format($co_cashbook, 2) }}</h4>
                    </div>
                    <i class="fa fa-building fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg bg-gradient-success text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-7 small font-weight-bold">موجودی دخل فروشات (SO)</p>
                        <h4 class="mb-0 font-weight-bold">${{ number_format($so_cashbook, 2) }}</h4>
                    </div>
                    <i class="fa fa-shopping-basket fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg bg-gradient-info text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-7 small font-weight-bold">مجموع تزریق سرمایه عمومی</p>
                        <h4 class="mb-0 font-weight-bold">${{ number_format($sp_total, 2) }}</h4>
                    </div>
                    <i class="fa fa-bank fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        @if(auth()->user()->role != 'SP')
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg border-right-success" style="border-right: 4px solid #28a745 !important;">
                <div class="card-body p-3">
                    <p class="text-muted small mb-1">موجودی فعلی دخل شما</p>
                    <h4 class="font-weight-bold text-success mb-0">${{ number_format($cash ?: 0, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg border-right-danger" style="border-right: 4px solid #dc3545 !important;">
                <div class="card-body p-3">
                    <p class="text-muted small mb-1">مجموع مصارف شما</p>
                    @php($my_debits = (auth()->user()->role == 'CO' || auth()->user()->role == 'CCO') ? $center_debits : $froshat_debits)
                    <h4 class="font-weight-bold text-danger mb-0">${{ number_format($my_debits, 2) }}</h4>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg bg-soft-primary p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-primary small mb-0 font-weight-bold">مجموع پول تخصیص یافته به دفاتر</p>
                        <h4 class="mb-0 font-weight-bold text-dark">${{ number_format($other_user, 2) }}</h4>
                    </div>
                    <i class="fa fa-exchange fa-2x text-primary opacity-2"></i>
                </div>
            </div>
        </div>
        @endif
        
        <div class="{{ auth()->user()->role == 'SP' ? 'col-md-6' : 'col-md-4' }} mb-4">
            <div class="card border-0 shadow-sm rounded-lg bg-soft-warning p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-warning small mb-0 font-weight-bold">موجودی دخل عمومی (SP Vault)</p>
                        <h4 class="mb-0 font-weight-bold text-dark">${{ number_format($cash ?: 0, 2) }}</h4>
                    </div>
                    <i class="fa fa-shield fa-2x text-warning opacity-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card border-0 shadow-sm rounded-lg mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 font-weight-bold">
                <i class="fa {{ $creditEdit ? 'fa-edit' : 'fa-plus-circle' }} text-primary mr-2"></i>
                {{ $creditEdit ? 'ویرایش درخواست/تزریق پول' : (auth()->user()->role == 'SP' ? 'تزریق سرمایه جدید' : 'درخواست بودجه نقد') }}
            </h5>
        </div>
        <div class="card-body bg-soft-light border-top">
            <form action="/dashboard/add-office-credit{{ is_object($creditEdit) ? '/'.$creditEdit->id : '' }}" method="post">
                @csrf
                @if(is_object($creditEdit)) @method('PATCH') @endif
                
                <div class="row">
                    <div class="col-md-4 form-group text-right">
                        <label class="small font-weight-bold">واحد پولی (Currency)</label>
                        <select name="currency_id" id="currency_id" class="form-control form-control-sm border-0 shadow-sm text-right">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}" data-code="{{ $curr->code }}" data-rate="{{ $curr->exchange_rate }}"
                                    {{ (is_object($creditEdit) && $creditEdit->currency_id == $curr->id) ? 'selected' : '' }}>
                                    {{ $curr->code }} - {{ $curr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group text-right">
                        <label class="small font-weight-bold">نرخ تبدیل (FX Rate to USD)</label>
                        <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" value="{{ is_object($creditEdit) ? $creditEdit->exchange_rate : '' }}" 
                               class="form-control form-control-sm border-0 shadow-sm text-right">
                    </div>
                    <div class="col-md-4 form-group text-right">
                        <label class="small font-weight-bold">تاریخ</label>
                        <input type="date" name="date" value="{{ is_object($creditEdit) ? $creditEdit->date : date('Y-m-d') }}" 
                               class="form-control form-control-sm border-0 shadow-sm text-right" required>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 form-group text-right">
                        <label class="small font-weight-bold">مقدار مبلغ (Amount)</label>
                        <input type="number" step="0.01" name="amount" id="amount" value="{{ is_object($creditEdit) ? $creditEdit->amount : '' }}" 
                               class="form-control form-control-sm border-0 shadow-sm text-right" required placeholder="0.00">
                    </div>
                    <div class="col-md-6 form-group text-right">
                        <label class="small font-weight-bold">توضیحات و بابت</label>
                        <textarea name="description" class="form-control form-control-sm border-0 shadow-sm text-right" 
                                  rows="1" required placeholder="علت تزریق یا درخواست پول...">{{ is_object($creditEdit) ? $creditEdit->description : '' }}</textarea>
                    </div>
                </div>

                <!-- LIVE USD TRUTH PREVIEW -->
                <div id="usd-preview-box" class="mt-3 p-3 bg-white shadow-sm d-flex justify-content-between align-items-center" style="border-radius: 10px; display:none !important;">
                    <div>
                        <span class="text-muted small">معادل دالر (USD Truth):</span>
                        <h4 class="mb-0 font-weight-bold text-primary" id="usd-amount-display">0.00 $</h4>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-primary px-3 py-2 rounded-pill">Forensic Normalization Active</span>
                    </div>
                </div>
                <div class="text-right mt-2">
                    @if($creditEdit)
                        <a href="/dashboard/add-office-credit" class="btn btn-light btn-sm px-4 mr-2 shadow-sm rounded-pill">انصراف</a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold rounded-pill">
                        <i class="fa fa-save mr-1"></i> {{ $creditEdit ? 'بروزرسانی' : 'ثبت نهایی' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table Section -->
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="creditPrint">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold"><i class="fa fa-list text-muted mr-2"></i> تاریخچه گردش نقدینگی</h5>
            <div id="exportButton" class="hideOnPrint"></div>
        </div>
        <div class="card-body p-0 text-right">
            @if(session("status") || session("error"))
                <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mx-3 mt-3 shadow-sm border-0 rounded-pill text-center py-2 small">
                    {{ session('status') ?: session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right" id="add_credit">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 border-0">مبلغ و ارز</th>
                            <th class="py-3 border-0">توضیحات</th>
                            <th class="py-3 border-0 text-center">تاریخ</th>
                            @if(auth()->user()->role != 'SP')
                                <th class="py-3 border-0 text-center hideOnPrint">حالت</th>
                            @endif
                            <th class="px-4 py-3 border-0 text-left hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($display_credits = $sp_credits)
                        @forelse ($display_credits as $credit)
                        <tr class="ur{{ $credit->id }} border-bottom">
                            <td class="px-4 py-3 font-weight-bold text-dark text-right">
                                {{ number_format($credit->original_amount ?: $credit->amount, 2) }} 
                                <span class="badge badge-soft-primary px-2 py-1 rounded-pill small">{{ $credit->currency_code ?: 'USD' }}</span>
                                @if($credit->currency_code && $credit->currency_code != 'USD')
                                    <span class="text-muted small d-block mt-1" style="font-size: 0.75rem;">(معادل ${{ number_format($credit->base_amount, 2) }})</span>
                                @endif
                            </td>
                            <td class="small">{{ $credit->description }}</td>
                            <td class="small text-muted text-center">{{ $credit->date }}</td>
                            @if(auth()->user()->role != 'SP')
                            <td class="text-center hideOnPrint">
                                <span class="badge {{ $credit->status == 0 ? 'badge-soft-warning' : 'badge-soft-success' }} px-3 py-1 rounded-pill">
                                    {{ $credit->status == 0 ? 'در انتظار تایید' : 'تایید شده' }}
                                </span>
                            </td>
                            @endif
                            <td class="px-4 py-3 text-left hideOnPrint">
                                @if($credit->status == 0)
                                <a href="/dashboard/add-office-credit/{{$credit->id}}/edit" class="btn btn-soft-primary btn-sm rounded-pill px-3 shadow-none">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <button onclick="RemoveCredit({{$credit->id}})" class="btn btn-soft-danger btn-sm rounded-pill px-3 ml-1 shadow-none">
                                    <i class="fa fa-trash"></i>
                                </button>
                                @else
                                <span class="text-muted small italic"><i class="fa fa-lock mr-1"></i> قفل شده</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted italic">هیچ رکوردی یافت نشد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-warning { background: linear-gradient(135deg, #ff9800 0%, #ed6c02 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #03a9f4 0%, #01579b 100%); }
    .bg-soft-info { background-color: rgba(3, 169, 244, 0.1); }
    .bg-soft-primary { background-color: rgba(0, 123, 255, 0.1); }
    .bg-soft-warning { background-color: rgba(255, 152, 0, 0.1); }
    .bg-soft-light { background-color: #f8f9fa; }
    .leading-relaxed { line-height: 1.6; }
    .badge-soft-warning { background-color: rgba(255, 193, 7, 0.15); color: #856404; }
    .badge-soft-success { background-color: rgba(40, 167, 69, 0.15); color: #155724; }
    .btn-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; border: none; }
    .btn-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: none; }
    @media print { .hideOnPrint { display: none !important; } .card { box-shadow: none !important; border: 1px solid #eee !important; } }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.status').fadeIn().delay(3000).fadeOut();

        function updateForensicPreview() {
            var amount = parseFloat($('#amount').val()) || 0;
            var rate = parseFloat($('#exchange_rate').val()) || 0;
            var usdAmount = amount * rate;

            if (amount > 0) {
                $('#usd-preview-box').attr('style', 'border-radius: 10px; display:flex !important; animation: fadeIn 0.5s;');
                $('#usd-amount-display').text(usdAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 4}) + ' $');
            } else {
                $('#usd-preview-box').attr('style', 'display:none !important;');
            }
        }

        $('#currency_id').change(function() {
            var selected = $(this).find(':selected');
            var rate = selected.data('rate');
            $('#exchange_rate').val(rate);
            updateForensicPreview();
        });

        $('#amount, #exchange_rate').on('keyup change', function() {
            updateForensicPreview();
        });

        if ($('#currency_id').length) {
            $('#currency_id').trigger('change');
        }
        
        $("#add_credit").tableExport({
            formats: ["xlsx"],
            bootstrap: true,
            position: "bottom",
            ignoreCols: [4],
            RTL: true,
            sheetname: "Office Credits"
        });
        
        var $buttons = $('#add_credit').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });

    function RemoveCredit(id) {
        swal({
            title: "حذف رکورد؟",
            text: "آیا از حذف این درخواست اطمینان دارید؟",
            icon: "warning",
            buttons: {
                confirm: {text: 'بلی، حذف شود', className: 'btn-danger'},
                cancel: 'نخیر'
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: 'DELETE',
                    data: {'_token': '{{ csrf_token() }}'},
                    url: '/dashboard/add-office-credit/' + id,
                    success: function (data) {
                        $('.ur' + id).fadeOut();
                    }
                })
            }
        });
    }
</script>
@endsection
