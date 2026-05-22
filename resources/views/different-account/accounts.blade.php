@extends('dsh.master')
@section('title', 'Miscellaneous Accounts - QBCC Forensic ERP')

@section('content')
<style>
    /* QBCC PREMIUM GLASSMORPHISM SYSTEM */
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-header-bg: #ffffff;
        --qbcc-border: #e2e8f0;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.8);
        --radius-xl: 20px;
        --radius-lg: 12px;
        --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .page-header-modern {
        background: var(--qbcc-header-bg);
        border-bottom: 1px solid var(--qbcc-border);
        margin-bottom: 25px;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
    }

    /* STATS CARDS */
    .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 25px; }
    .stat-card {
        background: white; border-radius: 16px; padding: 20px; border: 1px solid var(--qbcc-border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; align-items: center; gap: 15px;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .stat-info .label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-info .value { font-size: 20px; font-weight: 800; color: #1e293b; margin-top: 2px; }

    /* TABLE SYSTEM */
    .glass-card { background: white; border: 1px solid var(--qbcc-border); border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .table-modern thead th { background: #f8fafc; padding: 15px; font-weight: 700; color: #64748b; font-size: 12px; border: none; }
    .table-modern tbody tr { background: white; transition: all 0.2s; }
    .table-modern tbody tr:hover { background: #f8fafc; }
    .table-modern td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

    .account-badge { font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
    .badge-id { background: #f1f5f9; color: #475569; }
    
    .currency-tag { font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-right: 4px; display: inline-block; }
    .val-negative { color: #ef4444; background: #fee2e2; }
    .val-positive { color: #10b981; background: #d1fae5; }

    /* MODAL DESIGN */
    .qbcc-modal-content { border-radius: 20px; overflow: hidden; border: none; }
    .modal-header { background: var(--qbcc-gradient); color: white; padding: 20px 30px; }
    .modal-title { font-weight: 800; }
    .modal-body { padding: 30px; }
    .form-control-modern { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px 15px; font-size: 13px; }
    .form-control-modern:focus { border-color: var(--qbcc-secondary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
</style>

<div class="row">
    <div class="col-sm-12">
        <!-- HEADER -->
        <div class="page-header-modern d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 text-dark font-weight-bold">حسابات متفرقه و متفرقه</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-briefcase text-primary mr-1"></i> مدیریت اشخاص و شرکت‌های متفرقه (Forensic Audit Ready)</p>
            </div>
            <button class="btn btn-primary rounded-lg px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#accountModal" style="height: 45px;">
                <i class="feather icon-plus mr-1"></i> ایجاد حساب جدید
            </button>
        </div>

        <!-- STATS -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon bg-light-danger text-danger"><i class="feather icon-arrow-up-right"></i></div>
                <div class="stat-info">
                    <span class="label">مجموع باقیات (بدهی ما)</span>
                    <div class="value" dir="ltr">{{ number_format(abs(\App\DifferentAccountTotal::where('remaining', '<', 0)->sum('remaining')), 2) }} <small>USD</small></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-light-success text-success"><i class="feather icon-arrow-down-left"></i></div>
                <div class="stat-info">
                    <span class="label">مجموع طلبات (طلب ما)</span>
                    <div class="value" dir="ltr">{{ number_format(\App\DifferentAccountTotal::where('remaining', '>', 0)->sum('remaining'), 2) }} <small>USD</small></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-light-primary text-primary"><i class="feather icon-users"></i></div>
                <div class="stat-info">
                    <span class="label">تعداد کل حسابات</span>
                    <div class="value">{{ \App\DifferentAccount::count() }}</div>
                </div>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="card glass-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex gap-3">
                        <form action="/dashboard/different-account/search" method="post" class="d-flex">
                            @csrf
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="feather icon-search text-muted"></i></span>
                                </div>
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="جستجو بر اساس نام، شماره یا آدرس..." class="form-control border-left-0" style="width: 300px; border-radius: 0 8px 8px 0;">
                            </div>
                        </form>
                    </div>
                    <div class="hideOnPrint">
                        <button class="btn btn-outline-secondary btn-sm rounded-lg px-3" onclick="printPage('accounts')">
                            <i class="feather icon-printer mr-1"></i> چاپ لیست
                        </button>
                    </div>
                </div>

                <div class="table-responsive" id="accounts">
                    <table class="table-modern" id="account_list">
                        <thead>
                            <tr>
                                <th width="80">کد</th>
                                <th>مشخصات حساب</th>
                                <th>موقعیت و آدرس</th>
                                <th>باقیات (بدهی)</th>
                                <th>طلبات (طلب)</th>
                                <th class="text-center hideOnPrint" width="200">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $display_accounts = [];
                                if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO') $display_accounts = $center_accounts;
                                elseif(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO') $display_accounts = $froshat_accounts;
                                elseif(auth()->user()->role == 'MO') $display_accounts = $mo_accounts;
                                else $display_accounts = $sp_accounts;
                            @endphp

                            @foreach($display_accounts as $account)
                            <tr class="ur{{ $account->id }}">
                                <td><span class="account-badge badge-id">#{{ $account->id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 35px; height: 35px; font-weight: 800;">
                                            {{ mb_substr($account->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $account->name }}</div>
                                            <div class="small text-muted">{{ $account->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="small text-muted"><i class="feather icon-map-pin mr-1"></i> {{ $account->address }}</span></td>
                                <td>
                                    @foreach($account->totals as $t)
                                        @if($t->remaining < 0)
                                            <div class="mb-1">
                                                <span class="currency-tag val-negative">{{ $t->currency_code }}</span>
                                                <span class="font-weight-bold text-danger" dir="ltr">{{ number_format(abs($t->remaining), 2) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($account->totals as $t)
                                        @if($t->remaining > 0)
                                            <div class="mb-1">
                                                <span class="currency-tag val-positive">{{ $t->currency_code }}</span>
                                                <span class="font-weight-bold text-success" dir="ltr">{{ number_format($t->remaining, 2) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center hideOnPrint">
                                    <div class="btn-group">
                                        <a href="/dashboard/different-account/{{$account->id}}/edit" class="btn btn-sm btn-light-info text-info rounded-lg mr-1" title="ویرایش">
                                            <i class="feather icon-edit-2"></i>
                                        </a>
                                        <a href="/dashboard/different-account/{{$account->id}}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" style="font-weight: 700;">
                                            جزییات حساب
                                        </a>
                                        @if(auth()->user()->role == 'SP')
                                        <button onclick="deleteAccount({{$account->id}})" class="btn btn-sm btn-light-danger text-danger rounded-lg ml-1">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                        @endif
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
</div>

<!-- MODAL: CREATE/EDIT ACCOUNT -->
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content qbcc-modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $accountEdit ? 'ویرایش حساب متفرقه' : 'ایجاد حساب متفرقه جدید' }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $accountEdit ? '/dashboard/different-account/'.$accountEdit->id : '/dashboard/different-account' }}" method="post">
                @csrf
                @if($accountEdit) @method('PATCH') @endif
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small uppercase mb-2 d-block">نام کامل شخص یا شرکت</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="feather icon-user"></i></span>
                            </div>
                            <input type="text" name="name" value="{{ $accountEdit ? $accountEdit->name : old('name') }}" class="form-control-modern border-left-0 w-100" placeholder="مثلا: شرکت ساختمانی خورشید" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small uppercase mb-2 d-block">شماره تماس (فعال)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="feather icon-phone"></i></span>
                            </div>
                            <input type="text" name="phone" value="{{ $accountEdit ? $accountEdit->phone : old('phone') }}" class="form-control-modern border-left-0 w-100" placeholder="07XXXXXXXX" required dir="ltr">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-muted small uppercase mb-2 d-block">آدرس یا موقعیت</label>
                        <textarea name="address" class="form-control-modern w-100" rows="3" placeholder="ولایت، شهر، جاده..." required>{{ $accountEdit ? $accountEdit->address : old('address') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light-gray">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 font-weight-bold shadow">
                        <i class="feather icon-save mr-1"></i> تایید و ثبت نهایی
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
        @if($accountEdit) $('#accountModal').modal('show'); @endif

        // Auto-hide alerts
        window.setTimeout(function () {
            $(".alert").fadeTo(500, 0).slideUp(500, function () { $(this).remove(); });
        }, 4000);
    });

    function deleteAccount(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "با حذف این حساب، تمامی تاریخچه تراکنش‌ها نیز تحت تاثیر قرار می‌گیرد.",
            icon: "warning",
            buttons: {
                cancel: "خیر، انصراف",
                confirm: { text: "بله، حذف شود", className: "btn-danger" }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: { '_token': '{{csrf_token()}}' },
                    url: '/dashboard/different-account/' + id,
                    success: function (res) {
                        $('.ur' + id).fadeOut(500, function() { $(this).remove(); });
                        swal("حساب با موفقیت حذف گردید.", { icon: "success" });
                    }
                });
            }
        });
    }
</script>
@endsection
