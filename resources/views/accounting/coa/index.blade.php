@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">لیست حسابات (Chart of Accounts)</h3>
                            <p class="text-muted mb-0">مدیریت و ساختاردهی تمامی حساب‌های مالی شرکت</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('accounting.coa.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 10px;">
                                <i class="feather icon-plus mr-2"></i>ایجاد حساب جدید
                            </a>
                        </div>
                    </div>
                    <hr class="my-4 opacity-10">
                    
                    <!-- Search & Filter Bar -->
                    <form action="{{ route('accounting.coa.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="input-group bg-light rounded-pill px-3 py-1">
                                    <div class="input-group-prepend border-0">
                                        <span class="bg-transparent border-0"><i class="feather icon-search text-muted"></i></span>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو بر اساس نام یا کد حساب..." class="form-control border-0 bg-transparent">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <select name="account_type" class="form-control bg-light border-0" style="border-radius: 20px; height: 45px;">
                                    <option value="">همه نوعیت‌ها (All Types)</option>
                                    @foreach(['Asset' => 'Asset (دارایی)', 'Liability' => 'Liability (بدهی)', 'Equity' => 'Equity (سرمایه)', 'Revenue' => 'Revenue (عاید)', 'Expense' => 'Expense (هزینه)'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ request('account_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-dark btn-block shadow-sm" style="border-radius: 10px; height: 45px;">
                                    فیلتر کردن
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="border-0 py-3 px-4">کد حساب</th>
                            <th class="border-0 py-3">نام حساب</th>
                            <th class="border-0 py-3">نوعیت</th>
                            <th class="border-0 py-3 text-right">بیلانس فعلی (Trial Balance)</th>
                            <th class="border-0 py-3 text-center">ارز</th>
                            <th class="border-0 py-3 text-right px-4">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $acc)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-weight-bold">{{ $acc->account_code }}</span>
                                @if($acc->is_protected)
                                    <i class="feather icon-lock text-danger ml-1" title="حساب سیستمی (محافظت شده)"></i>
                                @endif
                            </td>
                            <td class="py-3">
                                <strong>{{ $acc->account_name }}</strong>
                                <br><small class="text-muted">{{ $acc->normal_balance == 'debit' ? 'دیبت' : 'کریدت' }} نارمل</small>
                            </td>
                            <td class="py-3">
                                @php
                                    $typeColors = ['Asset' => 'badge-light-success', 'Liability' => 'badge-light-danger', 'Equity' => 'badge-light-primary', 'Revenue' => 'badge-light-info', 'Expense' => 'badge-light-warning'];
                                @endphp
                                <span class="badge {{ $typeColors[$acc->account_type] ?? 'badge-light-secondary' }} px-3 py-2" style="font-size: 0.85rem;">
                                    {{ $acc->account_type }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <span class="font-weight-bold {{ $acc->balance > 0 ? 'text-success' : ($acc->balance < 0 ? 'text-danger' : 'text-muted') }}" dir="ltr">
                                    {{ number_format($acc->balance, 2) }} <small class="text-muted">{{ $acc->currency }}</small>
                                </span>
                                @if($acc->currency !== 'USD')
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    Equivalent: ${{ number_format($acc->base_balance, 2) }}
                                </div>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge badge-dark px-2">{{ $acc->currency }}</span>
                            </td>
                            <td class="py-3 text-right px-4">
                                <div class="btn-group">
                                    <a href="{{ route('accounting.reports.account_ledger', ['account_id' => $acc->id]) }}" class="btn btn-sm btn-outline-info mr-2" title="مشاهده صورت حساب (Ledger)">
                                        <i class="feather icon-file-text"></i> صورت حساب
                                    </a>
                                    
                                    <a href="{{ route('accounting.coa.edit', $acc->id) }}" class="btn btn-sm btn-outline-primary" title="ویرایش">
                                        <i class="feather icon-edit-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-light-success { background: #e8f5e9; color: #2e7d32; }
    .badge-light-danger { background: #ffebee; color: #c62828; }
    .badge-light-primary { background: #e3f2fd; color: #1565c0; }
    .badge-light-info { background: #e0f7fa; color: #00838f; }
    .badge-light-warning { background: #fff3e0; color: #ef6c00; }
    .btn-icon { width: 35px; height: 35px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    tr:hover { background-color: #f8f9fa; }
</style>
@endsection
