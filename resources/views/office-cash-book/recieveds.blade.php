@extends('dsh.master')
@section('title', 'روزنامچه دخل و مصارف دفتر')
@section('content')
@php($types = ['خوراکه', 'داکتری', 'معاشات', 'شست', 'تیاری', 'دیزاین قالین', 'صادرات', 'متفرقه', 'لباس و لوازم', 'انترنت و کریدیت', 'کرایه', 'تجهیزات', 'خرید قالین', 'خرید مواد خام قالین', 'اداری', 'درس و ورزش'])
<div class="container-fluid px-4 py-4 text-right">
    <!-- Action Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-book text-primary mr-2"></i> روزنامچه نقدینگی</h3>
            <p class="text-muted small mb-0">مدیریت موجودی نقد و ثبت مصارف روزانه دفتر</p>
        </div>
        <div class="col-md-6 text-left">
            <a class="btn btn-primary shadow-sm px-4 font-weight-bold" href="/dashboard/add-office-debit">
                <i class="fa fa-plus-circle mr-1"></i> ثبت مصرف جدید (تفصیلی)
            </a>
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm ml-2" onclick="printPage('expensePrint')">
                <i class="fa fa-print"></i>
            </button>
        </div>
    </div>

    <!-- Info Box (Dari) -->
    <div class="alert bg-soft-warning border-0 rounded-lg p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-start">
            <div class="ml-3">
                <i class="fa fa-info-circle fa-2x text-warning"></i>
            </div>
            <div>
                <h6 class="font-weight-bold text-dark mb-1">رهنمای روزنامچه نقدینگی:</h6>
                <p class="mb-0 text-dark small leading-relaxed">
                    این صفحه به عنوان <strong>دفتر کل نقدینگی</strong> دفتر شما عمل می‌کند. تمام ورودی‌ها و خروجی‌های پول نقد در اینجا ثبت و مدیریت می‌شوند.
                    <br>
                    • <strong>پول فعلی دخل:</strong> مانده واقعی نقد در صندوق شما را نشان می‌دهد که پس از هر مصرف به صورت خودکار کاهش می‌یابد.
                    <br>
                    • <strong>ثبت مصارف:</strong> برای ثبت سریع مصارف کوچک از فورم زیر استفاده کنید. برای مصارف بزرگتر یا با جزئیات حسابی، از دکمه «ثبت مصرف جدید» در بالا استفاده نمایید.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg bg-soft-success p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-success small mb-0 font-weight-bold">مجموع درامد (انتقال از مرکز)</p>
                        <h4 class="mb-0 font-weight-bold text-dark">${{ number_format($credit, 2) }}</h4>
                    </div>
                    <i class="fa fa-arrow-down fa-2x text-success opacity-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg bg-soft-danger p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-danger small mb-0 font-weight-bold">مجموع مصارف ثبت شده</p>
                        <h4 class="mb-0 font-weight-bold text-dark">${{ number_format($debit_sum, 2) }}</h4>
                    </div>
                    <i class="fa fa-arrow-up fa-2x text-danger opacity-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg bg-gradient-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-7 small font-weight-bold">پول نقد موجود (Balance)</p>
                        <h4 class="mb-0 font-weight-bold">
                            @if($cashbook)
                                ${{ number_format($cashbook->balance, 2) }}
                            @else
                                $0.00
                            @endif
                        </h4>
                    </div>
                    <i class="fa fa-bank fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
    </div>

    @if(!($search ?? null))
    <!-- Quick Expense Form -->
    <div class="card border-0 shadow-sm rounded-lg mb-4 overflow-hidden hideOnPrint">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 font-weight-bold text-primary">
                <i class="fa {{ $expenseEdit ? 'fa-edit' : 'fa-bolt' }} mr-2"></i>
                {{ $expenseEdit ? 'ویرایش مصرف' : 'ثبت سریع مصرف روزانه' }}
            </h5>
        </div>
        <div class="card-body bg-soft-light border-top">
            <form action="/dashboard/expenses{{ $expenseEdit ? '/'.$expenseEdit->id : '' }}" method="post">
                @csrf
                @if($expenseEdit) @method('PUT') @endif
                <input type="hidden" value="{{$currency}}" id="currency">
                
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">نام دریافت کننده</label>
                        <input name="name" type="text" value="{{ $expenseEdit->name ?? '' }}" class="form-control form-control-sm border-0 shadow-sm text-right">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">بابت (کجا)</label>
                        <select name="expense_for_where" class="form-control form-control-sm border-0 shadow-sm">
                            <option {{ ($expenseEdit && $expenseEdit->expense_for_where == 'دفتر') ? 'selected' : '' }}>دفتر</option>
                            <option {{ ($expenseEdit && $expenseEdit->expense_for_where == 'خانه') ? 'selected' : '' }}>خانه</option>
                            <option {{ ($expenseEdit && $expenseEdit->expense_for_where == 'ساختمان') ? 'selected' : '' }}>ساختمان</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">نوع مصرف</label>
                        <select name="expense_type" id="expense_type" class="form-control form-control-sm border-0 shadow-sm select2">
                            @foreach($types as $t)
                                <option {{ ($expenseEdit && $expenseEdit->expense_type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">تاریخ</label>
                        <input name="date" type="date" value="{{ $expenseEdit->date ?? date('Y-m-d') }}" class="form-control form-control-sm border-0 shadow-sm text-right">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">مبلغ (افغانی)</label>
                        <input type="number" step="1" name="amount_af" value="{{ $expenseEdit->amount_af ?? '' }}" id="fp" class="form-control form-control-sm border-0 shadow-sm text-right">
                        <input type="hidden" name="amount" id="mainP" value="{{ $expenseEdit->amount ?? '' }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">توضیحات</label>
                        <input name="description" type="text" value="{{ $expenseEdit->description ?? '' }}" class="form-control form-control-sm border-0 shadow-sm text-right" placeholder="...">
                    </div>
                </div>
                <div class="text-right mt-2 border-top pt-3">
                    <span class="text-muted small ml-3 italic">معادل دالری: <strong id="usd_preview">$ {{ number_format($expenseEdit->amount ?? 0, 2) }}</strong></span>
                    @if($expenseEdit)
                        <a href="/dashboard/office-cash-book" class="btn btn-light btn-sm px-4 mr-2 rounded-pill">انصراف</a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold rounded-pill">
                        <i class="fa fa-save mr-1"></i> {{ $expenseEdit ? 'بروزرسانی' : 'ذخیره مصرف' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm rounded-lg mb-4 hideOnPrint overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 font-weight-bold"><i class="fa fa-filter mr-2"></i> جستجو و فیلتر پیشرفته</h6>
        </div>
        <div class="card-body bg-soft-light border-top p-3">
            <div class="row">
                <div class="col-md-6 border-left">
                    <form action="/dashboard/office-cash-book/search-date-range" method="POST">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-3 form-group mb-0">
                                <label class="tiny font-weight-bold">از تاریخ</label>
                                <input type="date" name="from_date" value="{{ $start ?? '' }}" class="form-control form-control-sm border-0 shadow-sm">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label class="tiny font-weight-bold">تا تاریخ</label>
                                <input type="date" name="to_date" value="{{ $end ?? '' }}" class="form-control form-control-sm border-0 shadow-sm">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label class="tiny font-weight-bold">نوعیت</label>
                                <select name="expense_type" class="form-control form-control-sm border-0 shadow-sm select2">
                                    <option>همه مصارف</option>
                                    @foreach($types as $t) <option>{{ $t }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info btn-sm btn-block rounded-pill shadow-sm">جستجو</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="/dashboard/office-cash-book/search" method="POST">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-8 form-group mb-0 text-right">
                                <label class="tiny font-weight-bold">جستجو بر اساس نام یا بابت</label>
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="نام دریافت کننده..." class="form-control form-control-sm border-0 shadow-sm">
                                <input type="hidden" name="from_date" value="{{ $start ?? date('Y-m-d', strtotime('-30 days')) }}">
                                <input type="hidden" name="to_date" value="{{ $end ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-sm btn-block rounded-pill shadow-sm">پیدا کن</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="expensePrint">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold"><i class="fa fa-list-alt text-muted mr-2"></i> لیست تراکنش‌های مالی</h5>
            <div id="exportButton" class="hideOnPrint"></div>
        </div>
        <div class="card-body p-0">
            @if(session("status") || session("error"))
                <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mx-3 mt-3 shadow-sm border-0 rounded-pill text-center py-2 small">
                    {{ session('status') ?: session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right" id="expense_list">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 border-0">دریافت کننده</th>
                            <th class="py-3 border-0 text-center">مبلغ (AFN)</th>
                            <th class="py-3 border-0 text-center font-weight-bold">مبلغ (USD)</th>
                            <th class="py-3 border-0">نوعیت</th>
                            <th class="py-3 border-0">توضیحات</th>
                            <th class="py-3 border-0 text-center">تاریخ</th>
                            <th class="px-4 py-3 border-0 text-left hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debits as $debit)
                        <tr class="border-bottom">
                            <td class="px-4 py-3 font-weight-bold text-dark">{{ $debit->name }}</td>
                            <td class="text-center text-muted small">{{ number_format($debit->amount_af, 0) }} AF</td>
                            <td class="text-center font-weight-bold text-primary">${{ number_format($debit->amount, 2) }}</td>
                            <td><span class="badge badge-soft-info px-3 py-1 rounded-pill">{{ $debit->expense_type }}</span></td>
                            <td class="small">{{ $debit->description }}</td>
                            <td class="text-center text-muted small">{{ $debit->date }}</td>
                            <td class="px-4 py-3 text-left hideOnPrint">
                                <a href="/dashboard/office-cash-book/{{$debit->id}}/edit" class="btn btn-soft-primary btn-sm rounded-pill px-2 shadow-none">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="/dashboard/office-cash-book/{{$debit->id}}" class="btn btn-soft-info btn-sm rounded-pill px-2 shadow-none ml-1">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted italic">هیچ تراکنشی در این بازه زمانی یافت نشد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($debits->count() > 0)
                    <tfoot class="bg-soft-light font-weight-bold">
                        <tr>
                            <td class="px-4 py-3">مجموع صفحه:</td>
                            <td class="text-center text-muted">{{ number_format($debits->sum('amount_af'), 0) }} AF</td>
                            <td class="text-center text-primary font-weight-bold">${{ number_format($debits->sum('amount'), 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 text-left hideOnPrint">
            {{ $debits->links() }}
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
    .badge-soft-info { background-color: rgba(23, 162, 184, 0.15); color: #17a2b8; }
    .leading-relaxed { line-height: 1.6; }
    .tiny { font-size: 11px; }
    .border-left { border-left: 1px solid #dee2e6 !important; }
    @media print { .hideOnPrint { display: none !important; } .card { box-shadow: none !important; border: 1px solid #eee !important; } }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
        $('.status').fadeIn().delay(3000).fadeOut();

        // Currency conversion
        $('#fp').on('input', function() {
            let rate = parseFloat($('#currency').val()) || 0;
            let af = parseFloat($(this).val()) || 0;
            let usd = af / rate;
            $('#mainP').val(usd.toFixed(2));
            $('#usd_preview').text('$ ' + usd.toLocaleString(undefined, {minimumFractionDigits: 2}));
        });

        $("#expense_list").tableExport({
            formats: ["xlsx"],
            bootstrap: true,
            position: "bottom",
            ignoreCols: [6],
            RTL: true,
            sheetname: "Cash Book"
        });
        
        var $buttons = $('#expense_list').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });
</script>
@endsection