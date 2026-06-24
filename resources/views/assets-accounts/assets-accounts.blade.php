@extends('dsh.master')
@section('title', 'مدیریت اجناس و دارایی‌ها')
@section('content')

<!-- Google Fonts & Custom CSS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-blue: #0A192F;
        --accent-green: #10B981;
        --accent-blue: #00acc1;
        --accent-purple: #8B5CF6;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    body { font-family: 'Outfit', 'Inter', 'Segoe UI', sans-serif; background-color: #f4f7fa; direction: rtl; }

    /* Premium Header */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-blue), #1e3c72);
        color: white;
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 25px;
        box-shadow: 0 10px 20px rgba(10, 25, 47, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* KPI Cards */
    .kpi-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--glass-shadow);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }
    .kpi-details h6 { color: #64748b; font-size: 0.95rem; font-weight: 700; margin-bottom: 5px; }
    .kpi-details h3 { color: var(--primary-blue); font-size: 1.6rem; font-weight: 800; margin: 0; }

    /* Table Container */
    .table-container {
        background: var(--glass-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--glass-shadow);
        border: 1px solid var(--glass-border);
    }
    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .premium-table th { color: #64748b; font-weight: 700; padding: 12px 15px; border: none; background: transparent; }
    .premium-table tbody tr { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.2s; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.06); }
    .premium-table td { padding: 15px; border: none; vertical-align: middle; }
    .premium-table td:first-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
    .premium-table td:last-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }

    /* Action Buttons */
    .btn-action-view {
        display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px;
        background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border-radius: 10px; transition: 0.2s;
    }
    .btn-action-view:hover { background-color: #f59e0b; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); }
    
    .btn-action-edit {
        display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px;
        background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9; border-radius: 10px; transition: 0.2s;
    }
    .btn-action-edit:hover { background-color: #0ea5e9; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14,165,233,0.3); }
    
    .btn-action-delete {
        display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px;
        background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 10px; border: none; transition: 0.2s; cursor: pointer;
    }
    .btn-action-delete:hover { background-color: #ef4444; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239,68,68,0.3); }

    .modal-content { border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
    .modal-header { background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-bottom: 1px solid #e2e8f0; }
    .form-control { border-radius: 10px; padding: 10px 15px; border: 1px solid #cbd5e1; }
    .form-control:focus { border-color: #00acc1; box-shadow: 0 0 0 3px rgba(0, 172, 193, 0.1); }
</style>

<div class="container-fluid">
    
    <!-- Premium Header -->
    <div class="dashboard-header">
        <div>
            <h3 class="mb-1 text-white" style="font-weight: 800;"><i class="fa fa-cubes mr-2"></i> مدیریت اجناس و دارایی‌ها</h3>
            <p class="mb-0 text-white-50">لیست تمامی اکونت‌های اجناس ثبت شده در سیستم</p>
        </div>
        <div>
            @if(!$accountEdit)
            @can('create_assets_account')
            <button class="btn btn-light" style="border-radius: 10px; font-weight: 700; color: var(--primary-blue);" data-toggle="modal" data-target="#assetModal">
                <i class="fa fa-plus-circle mr-1"></i> ثبت حساب جدید
            </button>
            @endcan
            @else
            <a href="{{ route('assets-accounts.index') }}" class="btn btn-light" style="border-radius: 10px; font-weight: 700; color: var(--primary-blue);">
                <i class="fa fa-arrow-right mr-1"></i> بازگشت به ثبت جدید
            </a>
            @endif
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="kpi-card" style="border-right: 4px solid #00acc1;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #00acc1, #00838f);"><i class="fa fa-list"></i></div>
                <div class="kpi-details">
                    <h6>تعداد کل اجناس</h6>
                    <h3>{{ $asset_accounts->count() }} <small style="font-size: 1rem;">آیتم</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="border-right: 4px solid #F59E0B;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);"><i class="fa fa-tags"></i></div>
                <div class="kpi-details">
                    <h6>تنوع انواع (نوعیت)</h6>
                    <h3>{{ $asset_accounts->unique('aa_type')->count() }} <small style="font-size: 1rem;">دسته</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="border-right: 4px solid #10B981;">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #10B981, #059669);"><i class="fa fa-calendar-check-o"></i></div>
                <div class="kpi-details">
                    <h6>اخیراً اضافه شده</h6>
                    <h3>{{ $asset_accounts->where('aa_date', '>=', now()->subDays(7)->format('Y-m-d'))->count() }} <small style="font-size: 1rem;">هفته جاری</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
    <div class="alert alert-danger" style="border-radius: 10px;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{session('error')}}
    </div>
    @endif
    <div class="alert alert-success status-alert" style="display:none; border-radius: 10px;" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        عملیات موفقانه انجام شد.
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <h5 class="mb-4 font-weight-bold" style="color: var(--primary-blue);">لیست تمام اجناس ثبتی</h5>
        <div class="table-responsive">
            <table class="premium-table text-right">
                <thead>
                    <tr>
                        <th style="width: 80px;">شماره</th>
                        <th>اسم حساب</th>
                        <th>نوعیت حساب</th>
                        <th>تاریخ ثبت</th>
                        <th class="text-center" style="width: 150px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asset_accounts as $as)
                    <tr class="ur{{$as->aa_id}}">
                        <td class="font-weight-bold" style="color: var(--accent-blue);">#{{$as->aa_id}}</td>
                        <td class="font-weight-bold text-dark">{{$as->aa_name}}</td>
                        <td><span class="badge badge-light" style="font-size: 13px; color: #475569; border: 1px solid #cbd5e1;">{{$as->aa_type}}</span></td>
                        <td><i class="fa fa-calendar text-muted mr-1"></i> {{$as->aa_date}}</td>
                        <td class="text-center" style="white-space: nowrap;">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="/dashboard/assets-accounts/{{$as->aa_id}}" class="btn-action-view" title="جزییات گردش">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>
                                    @can('edit_assets_account')
                                    <a href="/dashboard/assets-accounts/{{$as->aa_id}}/edit" class="btn-action-edit" title="ویرایش جنس">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                    @endcan
                                    @can('delete_assets_account')
                                    <button onclick="deleteAssetAccount({{$as->aa_id}})" class="btn-action-delete" title="حذف جنس">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </button>
                                    @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">هیچ اکونت جنسی در سیستم ثبت نشده است.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="assetModal" tabindex="-1" role="dialog" aria-labelledby="assetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="assetModalLabel" style="color: var(--primary-blue);">
                    @if(!$accountEdit) <i class="fa fa-plus-circle mr-1 text-primary"></i> ثبت اکونت جنس جدید @else <i class="fa fa-edit mr-1 text-primary"></i> ویرایش اکونت جنس @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                @if(!$accountEdit)
                    <form method="post" action="/dashboard/assets-accounts">
                        @csrf
                @else
                    <form method="post" action="/dashboard/assets-accounts/{{$accountEdit->aa_id}}">
                        {{method_field('patch')}}
                        @csrf
                @endif
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-muted small">اسم اکونت جنس</label>
                        <input type="text" name="aa_name" class="form-control" value="{{ $accountEdit ? $accountEdit->aa_name : '' }}" required placeholder="مثال: موتر باربری">
                        @error('aa_name') <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-muted small">نوعیت اکونت جنس</label>
                        <input type="text" name="aa_type" class="form-control" value="{{ $accountEdit ? $accountEdit->aa_type : '' }}" required placeholder="مثال: نقلیه">
                        @error('aa_type') <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">تاریخ ثبت</label>
                        <input type="date" name="aa_date" class="form-control" value="{{ $accountEdit ? $accountEdit->aa_date : date('Y-m-d') }}" required>
                        @error('aa_date') <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p> @enderror
                    </div>

                    <button class="btn btn-block text-white font-weight-bold py-2" style="background-color: var(--primary-blue); border-radius: 10px;" type="submit">
                        @if(!$accountEdit) <i class="fa fa-check mr-1"></i> ثبت و ذخیره @else <i class="fa fa-save mr-1"></i> بروزرسانی @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        @if($accountEdit || $errors->any())
            $('#assetModal').modal('show');
        @endif
    });

    function deleteAssetAccount(id) {
        swal({
            title: "مطمئن هستید؟",
            text: "آیا از حذف این جنس اطمینان دارید؟ این عمل غیرقابل بازگشت است.",
            icon: "warning",
            buttons: ["انصراف", "بله، حذف کن!"],
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: { '_token': '{{csrf_token()}}' },
                    url: '/dashboard/assets-accounts/' + id,
                    success: function (res) {
                        if (res.status == 'success') {
                            $('.status-alert').show();
                            $('.ur' + id).fadeOut(300, function() { $(this).remove(); });
                            setTimeout(() => { $(".status-alert").slideUp(500); }, 5000);
                        } else if (res.status == 'error') {
                            swal("خطا!", res.message, "error");
                        }
                    }
                });
            }
        });
    }
</script>
@endsection

