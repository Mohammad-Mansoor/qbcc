@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #455a64, #37474f);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white font-weight-bold mb-1">مدیریت گدام‌ها و مراکز استقرار</h3>
                            <p class="mb-0 opacity-80">تعریف و مدیریت انبارهای مواد اولیه، محصولات نهایی و کالاهای در جریان ساخت (WIP).</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-primary rounded-pill px-4 shadow" data-toggle="modal" data-target="#addWarehouseModal">
                                <i class="feather icon-plus mr-2"></i> افزودن گدام جدید
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="feather icon-check-circle mr-2"></i> {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="feather icon-alert-triangle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Warehouses Grid -->
    <div class="row">
        @foreach($warehouses as $warehouse)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden; border-top: 5px solid #4caf50 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="feather icon-package text-primary f-20"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link drp-icon dropdown-toggle text-muted p-0 border-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0" style="border-radius: 10px; min-width: 180px;">
                                <a class="dropdown-item py-2" href="#!" data-toggle="modal" data-target="#editWarehouseModal{{ $warehouse->id }}">
                                    <i class="fa fa-edit mr-2 text-info"></i> ویرایش اطلاعات
                                </a>
                                @if($warehouse->id != 1)
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('accounting.warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این گدام اطمینان دارید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fa fa-trash mr-2"></i> حذف گدام
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="font-weight-bold text-dark mb-1">{{ $warehouse->name }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="feather icon-map-pin mr-1"></i> {{ $warehouse->location ?: 'موقیعت ثبت نشده' }}
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center border-left border-primary border-3">
                                <span class="d-block text-muted x-small">موجودی قالین</span>
                                <span class="font-weight-bold text-dark">{{ number_format($warehouse->carpet_qty) }} تخته</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center border-left border-info border-3">
                                <span class="d-block text-muted x-small">موجودی تار</span>
                                <span class="font-weight-bold text-dark">{{ number_format($warehouse->material_qty, 1) }} KG</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-2 bg-success-light rounded mb-3 text-center shadow-sm" style="background: rgba(76, 175, 80, 0.05); border: 1px solid rgba(76, 175, 80, 0.15); border-radius: 12px;">
                        <span class="d-block text-muted small font-weight-bold mb-1">ارزش تخمینی دارایی گدام (Asset Value)</span>
                        <h4 class="mb-0 text-success font-weight-bold">${{ number_format($warehouse->total_asset_value, 2) }} <span class="small" style="font-size: 0.75rem;">USD</span></h4>
                        @foreach($currencies as $c)
                            @if($c->code !== 'USD')
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    معادل: {{ number_format($warehouse->total_asset_value * $c->exchange_rate, 2) }} <span class="font-weight-bold">{{ $c->code }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <hr class="my-3 opacity-5">

                    <div class="row text-center">
                        <div class="col-6 border-right">
                            <span class="d-block text-muted small">وضعیت</span>
                            <span class="badge {{ $warehouse->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill px-3 mt-1 shadow-sm">
                                {{ $warehouse->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="d-block text-muted small">نوع گدام</span>
                            <span class="text-dark mt-1 d-block font-weight-bold small">{{ $warehouse->type ?: 'معمولی' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editWarehouseModal{{ $warehouse->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 p-4">
                        <h5 class="modal-title font-weight-bold"><i class="feather icon-edit mr-2 text-primary"></i> ویرایش گدام: {{ $warehouse->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('accounting.warehouses.update', $warehouse->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted">نام گدام (Warehouse Name)</label>
                                <input type="text" name="name" class="form-control rounded-pill bg-light border-0" value="{{ $warehouse->name }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted">موقعیت (Location)</label>
                                <input type="text" name="location" class="form-control rounded-pill bg-light border-0" value="{{ $warehouse->location }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted">نوع گدام (Type)</label>
                                <select name="type" class="form-control rounded-pill bg-light border-0">
                                    <option value="Main" {{ $warehouse->type == 'Main' ? 'selected' : '' }}>مرکزی (Main)</option>
                                    <option value="WIP" {{ $warehouse->type == 'WIP' ? 'selected' : '' }}>در جریان ساخت (WIP)</option>
                                    <option value="Storage" {{ $warehouse->type == 'Storage' ? 'selected' : '' }}>ذخیره (Storage)</option>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-muted">وضعیت</label>
                                <select name="is_active" class="form-control rounded-pill bg-light border-0">
                                    <option value="1" {{ $warehouse->is_active ? 'selected' : '' }}>فعال</option>
                                    <option value="0" {{ !$warehouse->is_active ? 'selected' : '' }}>غیرفعال</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">انصراف</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">ذخیره تغییرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 bg-primary text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title font-weight-bold text-white"><i class="feather icon-plus mr-2"></i> ایجاد گدام جدید</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('accounting.warehouses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted">نام گدام (Warehouse Name)</label>
                        <input type="text" name="name" class="form-control rounded-pill bg-light border-0" placeholder="مثلاً: گدام مرکزی یا گدام شست" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted">موقعیت (Location)</label>
                        <input type="text" name="location" class="form-control rounded-pill bg-light border-0" placeholder="آدرس یا شماره مرکز">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-muted">نوع گدام (Type)</label>
                        <select name="type" class="form-control rounded-pill bg-light border-0">
                            <option value="Main">مرکزی (Main)</option>
                            <option value="WIP">در جریان ساخت (WIP)</option>
                            <option value="Storage" selected>ذخیره (Storage)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">ایجاد گدام</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    .bg-lightest { background: #fcfcfc; }
    .modal-content { overflow: hidden; }
</style>
@endsection
