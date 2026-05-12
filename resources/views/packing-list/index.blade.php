@extends('dsh.master')
@section('title' , 'مدیریت پکینگ لیست')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-right">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-cubes text-primary mr-2"></i> مدیریت پکینگ لیست</h3>
            <p class="text-muted small mb-0">ایجاد و مدیریت بسته‌بندی‌های قالین (بایل)</p>
        </div>
    </div>

    <div class="row">
        <!-- Create/Edit Section -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white py-3 text-right">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fa {{ !$packingEdit ? 'fa-plus-circle text-success' : 'fa-edit text-primary' }} mr-2"></i>
                        {{ !$packingEdit ? 'ایجاد پکینگ جدید' : 'ویرایش پکینگ' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ !$packingEdit ? '/dashboard/packing-list' : '/dashboard/packing-list/'.$packingEdit->id }}" method="post">
                        @csrf
                        @if($packingEdit) @method('PUT') @endif
                        
                        <div class="form-group mb-4 text-right">
                            <label class="small font-weight-bold text-muted">نمبر پکینگ (تولید خودکار)</label>
                            <div class="input-group shadow-sm">
                                <input type="text" value="{{ $packingEdit ? $packingEdit->packing_no : $packing_no }}"
                                       name="packing_no" class="form-control border-0 bg-light font-weight-bold text-center" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light border-0"><i class="fa fa-hashtag"></i></span>
                                </div>
                            </div>
                            @error('packing_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button class="btn {{ !$packingEdit ? 'btn-success' : 'btn-primary' }} btn-block shadow-sm py-2 font-weight-bold" type="submit">
                                <i class="fa fa-save mr-2"></i> {{ !$packingEdit ? 'ثبت پکینگ' : 'بروزرسانی' }}
                            </button>
                        </div>
                        @if($packingEdit)
                        <div class="mt-2 text-center">
                            <a href="/dashboard/packing-list" class="btn btn-link btn-sm text-muted">انصراف و لغو ویرایش</a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card border-0 shadow-sm rounded-lg bg-gradient-info text-white p-4 mb-4 text-right" style="background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-0 opacity-8 small">تعداد کل پکینگ‌ها</p>
                        <h2 class="mb-0 font-weight-bold">{{ count($packing_list) }}</h2>
                    </div>
                    <i class="fa fa-archive fa-3x opacity-5"></i>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="packingListTable">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="printPage('packingListTable')">
                        <i class="fa fa-print mr-1"></i> چاپ لیست
                    </button>
                    <h5 class="mb-0 font-weight-bold text-dark">لیست پکینگ‌ها</h5>
                </div>
                <div class="card-body p-0">
                    @if(session("status"))
                        <div class="alert alert-success border-0 rounded-0 mb-0 py-2 text-center small status">
                            <i class="fa fa-check-circle mr-1"></i> {{session('status')}}
                        </div>
                    @endif
                    @if(session("error"))
                        <div class="alert alert-danger border-0 rounded-0 mb-0 py-2 text-center small status">
                            <i class="fa fa-exclamation-triangle mr-1"></i> {{session('error')}}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-right">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="border-0 px-4 py-3 text-right">نمبر پکینگ</th>
                                    <th class="border-0 py-3 text-center">تعداد پکیج‌ها (بایل)</th>
                                    <th class="border-0 py-3 text-center">تاریخ ایجاد</th>
                                    <th class="border-0 px-4 py-3 text-left">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packing_list as $pack)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <span class="badge badge-soft-primary px-3 py-2 rounded-pill font-weight-bold">
                                            <i class="fa fa-tag mr-1"></i> {{ $pack->packing_no }}
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold text-dark">
                                        <span class="badge badge-light px-3 py-2" style="font-size: 0.9rem;">
                                            {{ $pack->package->count() }} <span class="small font-weight-normal text-muted ml-1">بایل</span>
                                        </span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $pack->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-4 py-3 text-left">
                                        <a href="/dashboard/packing-list/{{$pack->id}}" 
                                           class="btn btn-soft-info btn-sm rounded-pill px-3">
                                            <i class="fa fa-eye mr-1"></i> جزییات
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center">
                                        <div class="mb-3">
                                            <i class="fa fa-folder-open-o fa-3x text-muted opacity-3"></i>
                                        </div>
                                        <h6 class="text-muted font-weight-bold">هنوز هیچ پکینگ لیستی ثبت نشده است.</h6>
                                    </td>
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

<style>
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .btn-soft-info { background-color: rgba(23, 162, 184, 0.1); color: #17a2b8; border: none; }
    .btn-soft-info:hover { background-color: #17a2b8 !important; color: white !important; }
    .rounded-lg { border-radius: 0.75rem !important; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
    .opacity-5 { opacity: 0.5; }
    .opacity-8 { opacity: 0.8; }
    .opacity-2 { opacity: 0.2; }
    .opacity-3 { opacity: 0.3; }
    @media print {
        .btn, .card-header button, .col-md-4, .status { display: none !important; }
        .col-md-8 { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        .card { border: none !important; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 3000);
    });
</script>
@endsection
