@extends('dsh.master')

@section('content')
<div class="container-fluid py-4" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Top Action / Title Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 font-weight-bold text-dark">مدیریت مشتریان</h3>
            <p class="text-muted mb-0">ایجاد، ویرایش و پایش وضعیت مالی مشتریان در سیستم</p>
        </div>
        <div class="hideOnPrint">
            <button class="btn btn-primary btn-sm px-4 shadow-sm" onclick="printPage('customer_list')">
                <i class="fa fa-print"></i> چاپ گزارش
            </button>
        </div>
    </div>

    <!-- Stats & Summary Row -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 font-weight-bold" style="font-size: 0.85rem;">کل طلبات از مشتریان (GL)</h6>
                            <h3 class="font-weight-bold mb-0 text-primary">
                                ${{ number_format($total_receivable ?? 0, 2) }}
                            </h3>
                        </div>
                        <div class="rounded-circle bg-light p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-money text-primary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 font-weight-bold" style="font-size: 0.85rem;">مجموع پرداخت‌ها (USD)</h6>
                            <h3 class="font-weight-bold mb-0 text-success">
                                ${{ number_format($credit_us ?? 0, 2) }}
                            </h3>
                        </div>
                        <div class="rounded-circle bg-light p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-arrow-down text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 font-weight-bold" style="font-size: 0.85rem;">مجموع پرداخت‌ها (AFN)</h6>
                            <h3 class="font-weight-bold mb-0 text-info">
                                {{ number_format($credit_af ?? 0, 0) }} AFN
                            </h3>
                        </div>
                        <div class="rounded-circle bg-light p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-university text-info fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Customer Form Card -->
    <div class="card border-0 shadow-sm mb-4 hideOnPrint" style="background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(15px); border-radius: 12px;">
        <div class="card-header bg-transparent border-0 pt-4 pb-0">
            <h5 class="font-weight-bold mb-0">
                <i class="fa {{ !$customerEdit ? 'fa-plus-circle text-primary' : 'fa-edit text-warning' }} mr-2"></i>
                {{ !$customerEdit ? 'ایجاد مشتری جدید' : 'ویرایش اطلاعات مشتری' }}
            </h5>
        </div>
        <div class="card-body">
            <form method="post" action="{{ !$customerEdit ? '/dashboard/customers' : '/dashboard/customers/'.$customerEdit->id }}">
                @if($customerEdit) @method('PATCH') @endif
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted mb-2" style="font-size: 0.85rem;">کود مشتری</label>
                            <input type="text" value="{{ $customerEdit ? $customerEdit->customer_code : old('customer_code') }}" class="form-control form-control-sm border-light-gray shadow-none" required name="customer_code" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted mb-2" style="font-size: 0.85rem;">نام مشتری</label>
                            <input type="text" class="form-control form-control-sm border-light-gray shadow-none" value="{{ $customerEdit ? $customerEdit->name : old('name') }}" required name="name" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted mb-2" style="font-size: 0.85rem;">نام کمپنی</label>
                            <input type="text" value="{{ $customerEdit ? $customerEdit->company_name : old('company_name') }}" class="form-control form-control-sm border-light-gray shadow-none" required name="company_name" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted mb-2" style="font-size: 0.85rem;">آدرس کمپنی (موقعیت)</label>
                            <input type="text" value="{{ $customerEdit ? $customerEdit->company_address : old('company_address') }}" class="form-control form-control-sm border-light-gray shadow-none" required name="company_address" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted mb-2" style="font-size: 0.85rem;">نمبر تماس</label>
                            <input type="text" value="{{ $customerEdit ? $customerEdit->phone : old('phone') }}" class="form-control form-control-sm border-light-gray shadow-none" required name="phone" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted mb-2" style="font-size: 0.85rem;">نوعیت</label>
                            <select name="type" class="form-control form-control-sm border-light-gray shadow-none" style="border-radius: 8px;">
                                <option value="مشتری قالین" {{ ($customerEdit && $customerEdit->type == 'مشتری قالین') ? 'selected' : '' }}>مشتری قالین</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 text-left mt-2">
                        <button class="btn btn-sm {{ !$customerEdit ? 'btn-primary' : 'btn-warning text-dark' }} px-4 shadow-sm" type="submit" style="border-radius: 8px;">
                            <i class="fa fa-save mr-1"></i> {{ !$customerEdit ? 'ثبت مشتری' : 'ذخیره تغییرات' }}
                        </button>
                        @if($customerEdit)
                            <a href="/dashboard/customers" class="btn btn-sm btn-light ml-2 border px-3" style="border-radius: 8px;">انصراف</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer List Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom py-3">
            <div class="row align-items-center">
                <div class="col-md-6 col-sm-12">
                    <h5 class="mb-0 font-weight-bold text-dark">لیست و گزارش مالی مشتریان</h5>
                </div>
                <div class="col-md-6 col-sm-12 text-left mt-2 mt-md-0 hideOnPrint">
                    <form action="/dashboard/customers/search" method="post" class="d-inline-block w-75">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" placeholder="جستجو بر اساس نام، کود، کمپنی..." class="form-control border-light-gray shadow-none" value="{{ $search ?? '' }}" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            <div class="input-group-append">
                                <button class="btn btn-secondary px-3" type="submit" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="customer_list">
                    <thead class="bg-light text-secondary font-weight-bold" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 px-4">کود</th>
                            <th class="py-3">نام و کمپنی</th>
                            <th class="py-3 text-left">ارزش کل تجارت (USD)</th>
                            <th class="py-3 text-left">بیلانس حسابی (USD)</th>
                            <th class="py-3 text-left">باقیات نقدی (USD)</th>
                            <th class="py-3 text-left">باقیات نقدی (AFN)</th>
                            <th class="py-3">آخرین فعالیت</th>
                            <th class="py-3 hideOnPrint text-center" style="width: 120px;">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $cust)
                            <tr>
                                <td class="px-4">
                                    <span class="badge badge-light border text-dark font-weight-normal px-2 py-1" style="border-radius: 6px;">
                                        {{ $cust->customer_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $cust->name }}</div>
                                    <small class="text-muted">{{ $cust->company_name }}</small>
                                </td>
                                <td class="text-left font-weight-bold text-primary" dir="ltr">
                                    ${{ number_format($cust->lifetime_sales ?? 0, 2) }}
                                </td>
                                <td class="text-left font-weight-bold" dir="ltr">
                                    <span class="{{ ($cust->ledger_balance ?? 0) > 0 ? 'text-success' : (($cust->ledger_balance ?? 0) < 0 ? 'text-danger' : 'text-muted') }}">
                                        ${{ number_format($cust->ledger_balance ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="text-left font-weight-bold" dir="ltr">
                                    <span class="{{ ($cust->total_usd ?? 0) > 0 ? 'text-success' : (($cust->total_usd ?? 0) < 0 ? 'text-danger' : 'text-muted') }}">
                                        ${{ number_format($cust->total_usd ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="text-left font-weight-bold" dir="ltr">
                                    <span class="{{ ($cust->total_af ?? 0) > 0 ? 'text-success' : (($cust->total_af ?? 0) < 0 ? 'text-danger' : 'text-muted') }}">
                                        {{ number_format($cust->total_af ?? 0, 0) }} AFN
                                    </span>
                                </td>
                                <td>
                                    @if($cust->last_activity)
                                        <div class="text-dark" style="font-size: 0.85rem;">{{ $cust->last_activity }}</div>
                                        <small class="{{ ($cust->days_since_active ?? 0) > 60 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                            ({{ $cust->days_since_active }} روز قبل)
                                        </small>
                                    @else
                                        <small class="text-muted">بدون فعالیت</small>
                                    @endif
                                </td>
                                <td class="hideOnPrint text-center">
                                    <div class="btn-group">
                                        <a href="/dashboard/customers/{{$cust->id}}/edit" class="btn btn-sm btn-outline-warning mr-1 p-1 px-2" title="ویرایش" style="border-radius: 6px;">
                                            <i class="feather icon-edit-2"></i>
                                        </a>
                                        <a href="/dashboard/customer-payments/{{$cust->id}}" class="btn btn-sm btn-outline-info p-1 px-2" title="حساب و پرداخت" style="border-radius: 6px;">
                                            <i class="feather icon-credit-card"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    هیچ مشتری با این مشخصات یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center py-3 hideOnPrint">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
