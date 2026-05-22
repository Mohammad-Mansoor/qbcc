@extends('dsh.master')

@section('title', 'مدیریت مشتریان فرمایشی')

@section('content')
<style>
    /* PREMIUM UI STYLE DESIGN - INDIGO & PURPLE THEME */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .glass-card:hover {
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
    }
    .page-header-premium {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(118, 75, 162, 0.15);
    }
    .custom-input {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 12px 15px;
        transition: all 0.2s;
        font-weight: 500;
    }
    .custom-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
    }
    .field-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        display: block;
        font-size: 0.85rem;
    }
    .btn-premium {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.2);
        transition: all 0.2s;
    }
    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);
        color: white;
    }
    .premium-table thead th {
        background: #f7fafc;
        color: #4a5568;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 15px;
        border-bottom: 2px solid #edf2f7;
    }
    .premium-table tbody td {
        padding: 15px;
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .action-badge {
        font-weight: 700;
        font-size: 0.8rem;
        padding: 6px 12px;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .action-badge:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }
</style>

@php
    // Structured array of major trading & export countries for clean dynamic output
    $countries = [
        'Afghanistan', 'United States', 'Germany', 'United Arab Emirates', 'United Kingdom',
        'Turkey', 'Pakistan', 'Iran', 'China', 'Saudi Arabia', 'Qatar', 'Switzerland',
        'Canada', 'Australia', 'Italy', 'France', 'Netherlands', 'Belgium', 'Sweden',
        'Norway', 'Denmark', 'Japan', 'South Korea', 'India', 'Russia', 'Uzbekistan',
        'Tajikistan', 'Turkmenistan', 'Kuwait', 'Oman', 'Bahrain'
    ];
@endphp

<div class="container-fluid py-4 text-right" id="customer-dashboard">
    <!-- Premium Header -->
    <div class="page-header-premium d-flex justify-content-between align-items-center" style="direction: rtl;">
        <div>
            <h2 class="text-white font-weight-bold mb-1">
                <i class="fa fa-users ml-2"></i> مدیریت مشتریان فرمایشی (Customer Accounts)
            </h2>
            <p class="mb-0 opacity-75">ایجاد، تصحیح و مدیریت قراردادها و حساب‌های تفصیلی خریداران قالین</p>
        </div>
    </div>

    <!-- Dari Elegant Guide Panel -->
    <div class="alert glass-card mb-4 border-0 p-4" style="direction: rtl; background: #eef2ff; border-right: 4px solid #667eea !important;">
        <div class="d-flex align-items-start">
            <div class="ml-3 mt-1">
                <i class="fa fa-info-circle fa-2x" style="color: #667eea"></i>
            </div>
            <div>
                <h6 class="font-weight-bold mb-1" style="color: #4f46e5">راهنمای جامع مدیریت خریداران فرمایشی:</h6>
                <p class="mb-0 text-dark small leading-relaxed">
                    در این بخش خریدارانی که پروژه‌ها یا طرح‌های سفارشی (Custom Orders) دارند را ثبت نمایید. اطلاعات و ارز معاملاتی انتخابی به طور مستقیم بر اسناد مالی و فرآیند تسلیم‌دهی گدام تاثیرگذار است.
                    <br>
                    • <strong>ثبت مشتری:</strong> از کارت سمت راست نام کامل و کشور خریدار را وارد و ثبت کنید.
                    <br>
                    • <strong>مدیریت فرمایشات:</strong> با کلیک روی دکمه طلایی <span class="badge badge-warning">فرمایشات</span> وارد پوشه فنی هر خریدار شوید.
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Column (Add / Edit Customer) -->
        <div class="col-lg-4 mb-4">
            <div class="card glass-card border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa {{ $accountEdit ? 'fa-edit text-info' : 'fa-plus-circle text-primary' }} ml-2"></i>
                        {{ $accountEdit ? 'ویرایش معلومات مشتری' : 'ثبت مشتری جدید' }}
                    </h5>
                </div>
                <div class="card-body bg-light-50 border-top pt-4">
                    @if(session("status") || session("error"))
                        <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mb-4 border-0 rounded-lg py-2 text-center small shadow-none">
                            {{ session('status') ?: session('error') }}
                        </div>
                    @endif

                    @if(!$accountEdit)
                        <form method="post" action="/dashboard/customer-account-for-orders">
                            @csrf
                            <div class="form-group mb-4">
                                <label class="field-label">نام کامل مشتری (Customer Name)</label>
                                <input type="text" name="customer_name" class="form-control custom-input text-right" placeholder="نام مشتری یا شرکت..." required>
                                @error('customer_name') 
                                    <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="field-label">کشور خریدار (Country)</label>
                                <select name="customer_country" id="customer_country" class="form-control custom-input select2" required>
                                    @foreach($countries as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-premium btn-block mt-4" type="submit">
                                <i class="fa fa-save ml-1"></i> ذخیره خریدار
                            </button>
                        </form>
                    @else
                        <form method="post" action="/dashboard/customer-account-for-orders/{{$accountEdit->c_id}}">
                            {{ method_field('patch') }}
                            @csrf
                            <div class="form-group mb-4">
                                <label class="field-label">نام کامل مشتری (Customer Name)</label>
                                <input type="text" name="customer_name" value="{{$accountEdit->customer_name}}" class="form-control custom-input text-right" required>
                                @error('customer_name') 
                                    <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="field-label">کشور خریدار (Country)</label>
                                <select name="customer_country" id="customer_country" class="form-control custom-input select2" required>
                                    @foreach($countries as $c)
                                        <option value="{{ $c }}" {{ $accountEdit->customer_country == $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-premium btn-block mt-4" type="submit">
                                <i class="fa fa-check-circle ml-1"></i> اعمال تغییرات
                            </button>
                            <a href="/dashboard/customer-account-for-orders" class="btn btn-light btn-block mt-2 font-weight-bold" style="border-radius: 10px;">انصراف</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Accounts List Table Column -->
        <div class="col-lg-8 mb-4">
            <div class="card glass-card border-0">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center" style="direction: rtl;">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa fa-list text-muted ml-2"></i> لیست جامع خریداران سفارشی
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="window.print()">
                        <i class="fa fa-print ml-1"></i> چاپ لیست
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table premium-table table-hover align-middle mb-0 text-right">
                            <thead>
                                <tr>
                                    <th class="px-4 text-right"># ID</th>
                                    <th class="text-right">نام کامل مشتری</th>
                                    <th class="text-right">کشور</th>
                                    <th class="hideOnPrint text-center">فرمایشات فنی</th>
                                    <th class="hideOnPrint text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $m)
                                <tr class="ur{{$m->c_id}}">
                                    <td class="px-4 font-weight-bold text-muted">{{ $m->c_id }}</td>
                                    <td class="font-weight-bold text-dark">{{ $m->customer_name }}</td>
                                    <td>
                                        <span class="badge badge-light border px-3 py-2 rounded-pill font-weight-bold text-secondary">
                                            <i class="fa fa-globe ml-1 text-muted"></i> {{ $m->customer_country }}
                                        </span>
                                    </td>
                                    <td class="hideOnPrint text-center">
                                        <a href="/dashboard/customer-account-for-orders/{{$m->c_id}}" class="action-badge bg-warning text-dark font-weight-bold shadow-sm">
                                            <i class="fa fa-folder-open"></i> فرمایشات (Orders)
                                        </a>
                                    </td>
                                    <td class="hideOnPrint text-left pl-4">
                                        <div class="btn-group">
                                            <a href="/dashboard/customer-account-for-orders/{{$m->c_id}}/edit" class="btn btn-sm btn-outline-info border-0" title="Edit">
                                                <i class="fa fa-edit fa-lg"></i>
                                            </a>
                                            @if(auth()->user()->role == 'SP')
                                                <button onclick="deleteCustomer({{$m->c_id}})" class="btn btn-sm btn-outline-danger border-0 ml-1" title="Delete">
                                                    <i class="fa fa-trash fa-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted italic">هیچ خریدار فرمایشی در سیستم ثبت نشده است.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
        $('.status').fadeIn().delay(3000).fadeOut();
    });

    function deleteCustomer(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این عمل تمام تاریخچه فرمایشات و محاسبات مشتری را حذف خواهد کرد!",
            icon: "warning",
            buttons: {
                confirm: { text: 'بلی، حذف شود', className: 'btn-danger' },
                cancel: 'انصراف'
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/customer-account-for-orders/' + id,
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            swal("حذف خریدار با موفقیت انجام شد!", { icon: "success" })
                                .then(() => window.location = '/dashboard/customer-account-for-orders');
                        } else {
                            swal("خطا در برقراری ارتباط با سرور!", { icon: "error" });
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
