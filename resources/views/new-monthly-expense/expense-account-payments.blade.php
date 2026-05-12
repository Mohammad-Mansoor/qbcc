@extends('dsh.master')
@section('title', 'مصارف ماهانه - ' . $month_obj->month_name)
@section('content')
<div class="container-fluid px-4 py-4 text-right">
    <!-- Action Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-calendar-check-o text-primary mr-2"></i> 
                مصارف ماه: {{ $month_obj->month_name }} {{ $month_obj->year_name }}
            </h3>
            <p class="text-muted small mb-0">مدیریت جزئیات پرداختی‌ها و تراز مالی ماهانه</p>
        </div>
        <div class="col-md-6 text-left">
            <a href="/dashboard/monthly-expense-accounts" class="btn btn-outline-secondary shadow-sm px-4 rounded-pill transition-all">
                <i class="fa fa-arrow-right mr-1"></i> بازگشت به لست حسابات
            </a>
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm ml-2 transition-all" onclick="printPage('expenseDetailsPrint')">
                <i class="fa fa-print"></i>
            </button>
        </div>
    </div>

    <!-- Stats Section (Grid of 8 Categories) -->
    <div class="row mb-2">
        @php
            $isSP = (auth()->user()->role == 'SP');
            $stats = [
                ['title' => 'خوراکه', 'af' => $isSP ? $khoraka_sp_af : $khoraka_af, 'usd' => $isSP ? $khoraka_sp_usd : $khoraka_usd, 'icon' => 'fa-cutlery', 'color' => 'primary'],
                ['title' => 'کرایه و برق', 'af' => $isSP ? $keraia_sp_af : $keraia_af, 'usd' => $isSP ? $keraia_sp_usd : $keraia_usd, 'icon' => 'fa-bolt', 'color' => 'warning'],
                ['title' => 'معاشات', 'af' => $isSP ? $mashat_sp_af : $mashat_af, 'usd' => $isSP ? $mashat_sp_usd : $mashat_usd, 'icon' => 'fa-users', 'color' => 'success'],
                ['title' => 'ترانسپورت', 'af' => $isSP ? $transport_sp_af : $transport_af, 'usd' => $isSP ? $transport_sp_usd : $transport_usd, 'icon' => 'fa-bus', 'color' => 'info'],
                ['title' => 'متفرقه دفتر', 'af' => $isSP ? $motafrqa_sp_af : $motafrqa_af, 'usd' => $isSP ? $motafrqa_sp_usd : $motafrqa_usd, 'icon' => 'fa-ellipsis-h', 'color' => 'secondary'],
                ['title' => 'برداشت', 'af' => $isSP ? $bardasht_sp_af : $bardasht_af, 'usd' => $isSP ? $bardasht_sp_usd : $bardasht_usd, 'icon' => 'fa-money', 'color' => 'danger'],
                ['title' => 'ترمیمات و تیل', 'af' => $isSP ? $tel_sp_af : $tel_af, 'usd' => $isSP ? $tel_sp_usd : $tel_usd, 'icon' => 'fa-wrench', 'color' => 'dark'],
                ['title' => 'اجوره', 'af' => $isSP ? $ajora_sp_af : $ajora_af, 'usd' => $isSP ? $ajora_sp_usd : $ajora_usd, 'icon' => 'fa-briefcase', 'color' => 'primary'],
            ];
            
            $grand_usd = collect($stats)->sum('usd');
            $grand_af = collect($stats)->sum('af');
        @endphp
        
        @foreach($stats as $s)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-xl mb-4 stat-card overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-soft-{{ $s['color'] }} text-{{ $s['color'] }} rounded-lg d-flex align-items-center justify-content-center mr-0 ml-3" style="width: 40px; height: 40px;">
                            <i class="fa {{ $s['icon'] }}"></i>
                        </div>
                        <h6 class="text-muted font-weight-bold mb-0 small">{{ $s['title'] }}</h6>
                    </div>
                    <div class="row no-gutters">
                        <div class="col-6 border-left pl-2">
                            <p class="text-muted mb-0 x-small font-weight-bold">USD</p>
                            <h6 class="font-weight-bold text-dark mb-0">${{ number_format($s['usd'], 2) }}</h6>
                        </div>
                        <div class="col-6 pr-2">
                            <p class="text-orange mb-0 x-small font-weight-bold">AFN</p>
                            <h6 class="font-weight-bold text-orange mb-0">{{ number_format($s['af'], 0) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Outstanding Grand Total Section -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="grand-total-glass p-1 rounded-xl shadow-lg border-0">
                <div class="row align-items-center no-gutters">
                    <div class="col-md-4 py-4 px-5 text-center text-md-right">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3" style="width: 60px; height: 60px;">
                            <i class="fa fa-university fa-2x text-primary"></i>
                        </div>
                        <h4 class="text-white font-weight-bold mb-0">خلاصه کل مصارف</h4>
                        <p class="text-white-50 small mb-0">مجموع تمام پرداختی‌های این دوره</p>
                    </div>
                    <div class="col-md-4 py-4 px-5 border-right-glass text-center">
                        <span class="badge badge-light px-3 mb-2 rounded-pill font-weight-bold text-primary">TOTAL USD</span>
                        <h2 class="text-white font-weight-bold mb-0">${{ number_format($grand_usd, 2) }}</h2>
                    </div>
                    <div class="col-md-4 py-4 px-5 text-center">
                        <span class="badge badge-light px-3 mb-2 rounded-pill font-weight-bold text-orange">TOTAL AFN</span>
                        <h2 class="text-orange-vibrant font-weight-bold mb-0">{{ number_format($grand_af, 0) }} <small class="text-white-50">AFN</small></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$search)
    <!-- Entry Form -->
    <div class="card border-0 shadow-sm rounded-xl mb-4 overflow-hidden hideOnPrint">
        <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
            <div class="bg-soft-primary p-2 rounded mr-0 ml-3">
                <i class="fa fa-plus-circle text-primary"></i>
            </div>
            <h5 class="mb-0 font-weight-bold text-dark">
                {{ $expenseEdit ? 'ویرایش مصرف' : 'ثبت مصرف جدید' }}
            </h5>
        </div>
        <div class="card-body bg-soft-light border-top">
            <form action="/dashboard/new-monthly-expense-payments{{ $expenseEdit ? '/'.$expenseEdit->id : '' }}" method="post">
                @csrf
                @if($expenseEdit) @method('PUT') @endif
                <input type="hidden" name="month_id" value="{{ $month_obj->me_id }}">
                
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">کتگوری مصرف</label>
                        <select name="category" class="form-control border-0 shadow-sm select2">
                            @php($cats = ['خوراکه', 'متفرقه دفتر', 'کرایه و برق', 'ترانسپورت', 'برداشت', 'ترمیمات و تیل', 'معاشات', 'اجوره'])
                            @foreach($cats as $cat)
                                <option {{ ($expenseEdit && $expenseEdit->category == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">مقدار پول</label>
                        <input type="number" step="0.01" name="amount" value="{{ $expenseEdit->amount ?? '' }}" class="form-control border-0 shadow-sm text-right font-weight-bold text-primary" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">نوع اسعار</label>
                        <select name="currency" class="form-control border-0 shadow-sm">
                            <option value="1" {{ ($expenseEdit && $expenseEdit->currency == 1) ? 'selected' : '' }}>AFN (افغانی)</option>
                            <option value="2" {{ ($expenseEdit && $expenseEdit->currency == 2) ? 'selected' : '' }}>USD (دالر)</option>
                        </select>
                    </div>
                    <input type="hidden" name="dollar_rate" value="1">
                    <div class="col-md-2 form-group">
                        <label class="small font-weight-bold">تاریخ</label>
                        <input name="date" type="date" value="{{ $expenseEdit->date ?? date('Y-m-d') }}" class="form-control border-0 shadow-sm text-right" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">توضیحات</label>
                        <input name="description" type="text" value="{{ $expenseEdit->description ?? '' }}" class="form-control border-0 shadow-sm text-right" placeholder="جزئیات..." required>
                    </div>
                </div>
                <div class="text-right mt-3">
                    @if($expenseEdit)
                        <a href="/dashboard/monthly-expense-accounts/{{ $month_obj->me_id }}" class="btn btn-light px-4 mr-2 rounded-pill shadow-sm">انصراف</a>
                    @endif
                    <button type="submit" class="btn btn-primary px-5 shadow-lg font-weight-bold rounded-pill">
                        <i class="fa fa-save mr-1"></i> {{ $expenseEdit ? 'بروزرسانی' : 'ذخیره مصرف' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-5" id="expenseDetailsPrint">
        <div class="card-header bg-white py-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-list text-muted mr-2"></i> لیست تراکنش‌های ترازشده</h5>
            <div id="exportButton" class="hideOnPrint"></div>
        </div>
        <div class="card-body p-0">
            @if(session("status") || session("error"))
                <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mx-4 mt-2 shadow-none border-0 rounded-lg text-center py-2 small font-weight-bold">
                    {{ session('status') ?: session('error') }}
                </div>
            @endif

            @php($list = $isSP ? $expenses_sp : $expenses)
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right" id="expense_list">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="px-4 py-3 border-0">کتگوری</th>
                            <th class="py-3 border-0 text-center">مبلغ دریافتی</th>
                            <th class="py-3 border-0 text-center">واحد اسعار</th>
                            <th class="py-3 border-0">توضیحات</th>
                            <th class="py-3 border-0 text-center">تاریخ</th>
                            <th class="px-4 py-3 border-0 text-left hideOnPrint">مدیریت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $e)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <span class="badge badge-soft-{{ $e->currency == 2 ? 'primary' : 'warning' }} px-3 py-2 rounded-pill font-weight-bold">
                                    {{ $e->category }}
                                </span>
                            </td>
                            <td class="text-center">
                                <h6 class="mb-0 font-weight-bold {{ $e->currency == 2 ? 'text-primary' : 'text-orange' }}">
                                    {{ $e->currency == 2 ? '$' : '' }}{{ number_format($e->amount, 2) }}{{ $e->currency == 1 ? ' AF' : '' }}
                                </h6>
                            </td>
                            <td class="text-center">
                                <span class="small font-weight-bold {{ $e->currency == 2 ? 'text-muted' : 'text-orange' }}">
                                    {{ $e->currency == 2 ? 'USD (دالر)' : 'AFN (افغانی)' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $e->description }}</td>
                            <td class="text-center text-muted small">{{ $e->date }}</td>
                            <td class="px-4 py-3 text-left hideOnPrint">
                                <a href="/dashboard/new-monthly-expense-payments/{{$e->id}}/edit" class="btn btn-soft-primary btn-icon rounded-circle mr-1" title="ویرایش">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button onclick="deleteExpense({{$e->id}})" class="btn btn-soft-danger btn-icon rounded-circle" title="حذف">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted italic">دیتایی یافت نشد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 text-left hideOnPrint">
            {{ $list->links() }}
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    
    :root {
        --primary: #4361ee;
        --orange: #f77f00;
        --orange-vibrant: #ff9f1c;
    }

    .rounded-xl { border-radius: 1.25rem !important; }
    .text-orange { color: var(--orange) !important; }
    .text-orange-vibrant { color: var(--orange-vibrant) !important; }
    .bg-soft-primary { background-color: rgba(67, 97, 238, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(247, 127, 0, 0.1) !important; }
    .bg-soft-success { background-color: rgba(46, 196, 182, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(230, 57, 70, 0.1) !important; }
    .bg-soft-info { background-color: rgba(76, 201, 240, 0.1) !important; }
    
    .stat-card { transition: transform 0.2s; border: 1px solid rgba(0,0,0,0.02) !important; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    
    .grand-total-glass {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        position: relative;
        overflow: hidden;
    }
    .grand-total-glass::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        pointer-events: none;
    }
    
    .border-right-glass { border-right: 1px solid rgba(255,255,255,0.1); }
    .x-small { font-size: 0.65rem; letter-spacing: 0.5px; }
    .btn-icon { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border: none; }
    .badge-soft-warning { background: rgba(247, 127, 0, 0.1); color: var(--orange); }
    .badge-soft-primary { background: rgba(67, 97, 238, 0.1); color: var(--primary); }
    
    .transition-all { transition: all 0.3s ease; }
    
    @media (max-width: 768px) {
        .border-right-glass { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.1); }
    }
    
    @media print { .hideOnPrint { display: none !important; } .rounded-xl { border-radius: 0 !important; } }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
        $('.status').fadeIn().delay(3000).fadeOut();

        $("#expense_list").tableExport({
            formats: ["xlsx"],
            bootstrap: true,
            position: "bottom",
            ignoreCols: [5],
            RTL: true,
            sheetname: "Monthly Expenses"
        });
        
        var $buttons = $('#expense_list').find('caption').children().detach();
        $buttons.appendTo('#exportButton');
    });

    function deleteExpense(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "بعد از حذف، این تراکنش از سیستم مالی نیز حذف خواهد شد!",
            icon: "warning",
            buttons: ["نخیر", "بلی، حذف شود"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '/dashboard/new-monthly-expense-payments/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        swal("موفقانه حذف شد!", {
                            icon: "success",
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        var msg = "خطا در حذف دیت!";
                        if(err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        swal("عملیات ناموفق!", msg, "error");
                    }
                });
            }
        });
    }
</script>
@endsection
