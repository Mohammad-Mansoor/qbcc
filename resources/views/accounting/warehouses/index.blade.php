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

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="feather icon-alert-triangle mr-2"></i>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; border-right: 5px solid #4caf50;">
                <div class="card-body p-3">
                    <div class="row align-items-center justify-content-between">
                        <!-- Search Input -->
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="input-group bg-light rounded-pill px-3 py-1 border align-items-center transition-all" style="border-color: #e9ecef; transition: all 0.25s ease;">
                                <i class="feather icon-search text-muted mr-2"></i>
                                <input type="text" id="warehouseSearch" class="form-control bg-transparent border-0 text-dark" placeholder="جستجو در نام یا موقعیت گدام..." style="box-shadow: none; font-size: 0.9rem;">
                            </div>
                        </div>
                        <!-- Status Filters -->
                        <div class="col-md-5 d-flex justify-content-md-end justify-content-start align-items-center">
                            <span class="text-muted small ml-3 font-weight-bold">وضعیت گدام:</span>
                            <div class="btn-group btn-group-filter shadow-sm rounded-pill overflow-hidden" style="border: 1px solid #e9ecef;">
                                <button type="button" class="btn btn-light filter-btn active px-4 py-2 small mb-0 font-weight-bold" data-status="active">فعال</button>
                                <button type="button" class="btn btn-light filter-btn px-4 py-2 small mb-0 font-weight-bold" data-status="inactive">غیرفعال</button>
                                <button type="button" class="btn btn-light filter-btn px-4 py-2 small mb-0 font-weight-bold" data-status="all">همه</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouses Grid -->
    <div class="row">
        @foreach($warehouses as $warehouse)
        <div class="col-md-4 mb-4 warehouse-card" data-name="{{ $warehouse->name }}" data-location="{{ $warehouse->location }}" data-active="{{ $warehouse->is_active ? '1' : '0' }}">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: visible; border-top: 5px solid #4caf50 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="feather icon-package text-primary f-20"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link drp-icon dropdown-toggle text-muted p-0 border-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-left shadow-lg border-0" style="border-radius: 10px; min-width: 180px;">
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
                        @if($warehouse->subtype === 'carpet')
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center border-left border-primary border-3">
                                <span class="d-block text-muted x-small">تعداد قالین</span>
                                <span class="font-weight-bold text-dark">{{ number_format($warehouse->carpet_qty) }} تخته</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center border-left border-warning border-3">
                                <span class="d-block text-muted x-small">متراژ کل</span>
                                <span class="font-weight-bold text-dark">{{ number_format($warehouse->carpet_area, 2) }} م²</span>
                            </div>
                        </div>
                        @elseif($warehouse->subtype === 'yarn')
                        <div class="col-12">
                            <div class="p-2 bg-light rounded text-center border-left border-info border-3">
                                <span class="d-block text-muted x-small">موجودی تار (نخ)</span>
                                <span class="font-weight-bold text-dark">{{ number_format($warehouse->material_qty, 2) }} KG</span>
                            </div>
                        </div>
                        @elseif($warehouse->subtype === 'dye')
                        <div class="col-12">
                            <div class="p-2 bg-light rounded text-center border-left border-success border-3">
                                <span class="d-block text-muted x-small">موجودی رنگ (رنگینه)</span>
                                <span class="font-weight-bold text-dark">{{ number_format($warehouse->material_qty, 2) }} KG</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="p-2 bg-success-light rounded mb-3 text-center shadow-sm" style="background: rgba(76, 175, 80, 0.05); border: 1px solid rgba(76, 175, 80, 0.15); border-radius: 12px;">
                        <span class="d-block text-muted small font-weight-bold mb-1">ارزش تخمینی دارایی گدام (Asset Value)</span>
                        <h4 class="mb-0 text-success font-weight-bold">${{ number_format($warehouse->total_asset_value, 2) }} <span class="small" style="font-size: 0.75rem;">USD</span></h4>
                        @foreach($currencies as $c)
                            @if($c->code !== 'USD')
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    معادل: {{ number_format($c->exchange_rate > 0 ? $warehouse->total_asset_value / $c->exchange_rate : 0, 2) }} <span class="font-weight-bold">{{ $c->code }}</span>
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
                            <span class="d-block text-muted small">نوعیت ذخیره</span>
                            <span class="badge badge-info mt-1 d-inline-block font-weight-bold small">{{ $warehouse->subtype_fa }}</span>
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
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted">نوعیت ذخیره (Subtype)</label>
                                <select name="subtype" class="form-control rounded-pill bg-light border-0" required>
                                    <option value="carpet" {{ $warehouse->subtype == 'carpet' ? 'selected' : '' }}>قالین (Carpet)</option>
                                    <option value="yarn" {{ $warehouse->subtype == 'yarn' ? 'selected' : '' }}>تار (Yarn)</option>
                                    <option value="dye" {{ $warehouse->subtype == 'dye' ? 'selected' : '' }}>رنگ (Dye)</option>
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
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted">نوع گدام (Type)</label>
                        <select name="type" class="form-control rounded-pill bg-light border-0">
                            <option value="Main">مرکزی (Main)</option>
                            <option value="WIP">در جریان ساخت (WIP)</option>
                            <option value="Storage" selected>ذخیره (Storage)</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-muted">نوعیت ذخیره (Subtype)</label>
                        <select name="subtype" class="form-control rounded-pill bg-light border-0" required>
                            <option value="carpet" selected>قالین (Carpet)</option>
                            <option value="yarn">تار (Yarn)</option>
                            <option value="dye">رنگ (Dye)</option>
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
    .card { position: relative; }
    .card:hover, .card:focus-within { transform: translateY(-5px); transition: all 0.3s ease; z-index: 99; }
    .dropdown-menu { z-index: 1050 !important; left: 0 !important; right: auto !important; }
    .bg-lightest { background: #fcfcfc; }
    .modal-content { overflow: hidden; }

    /* Search & Filter Styles */
    .btn-group-filter .btn.active {
        background-color: #4caf50 !important;
        color: white !important;
        border-color: #4caf50 !important;
        box-shadow: 0 4px 10px rgba(76, 175, 80, 0.25);
    }
    .btn-group-filter .btn {
        border: none;
        transition: all 0.25s ease;
        background-color: #f8f9fa;
        color: #6c757d;
    }
    .btn-group-filter .btn:hover:not(.active) {
        background-color: #e9ecef;
        color: #495057;
    }
    .warehouse-card {
        transition: opacity 0.25s ease, transform 0.25s ease;
    }
    #warehouseSearch::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('warehouseSearch');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.warehouse-card');
    let selectedStatus = 'active';

    function filterWarehouses() {
        const query = searchInput.value.toLowerCase().trim();

        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const location = (card.getAttribute('data-location') || '').toLowerCase();
            const isActive = card.getAttribute('data-active') === '1';

            const matchesQuery = name.includes(query) || location.includes(query);

            let matchesStatus = false;
            if (selectedStatus === 'all') {
                matchesStatus = true;
            } else if (selectedStatus === 'active') {
                matchesStatus = isActive;
            } else if (selectedStatus === 'inactive') {
                matchesStatus = !isActive;
            }

            if (matchesQuery && matchesStatus) {
                card.style.display = 'block';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }, 10);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                card.style.display = 'none';
            }
        });
    }

    // Input highlight logic
    searchInput.addEventListener('focus', () => {
        searchInput.closest('.input-group').style.borderColor = '#4caf50';
        searchInput.closest('.input-group').classList.add('shadow-sm');
    });
    searchInput.addEventListener('blur', () => {
        searchInput.closest('.input-group').style.borderColor = '#e9ecef';
        searchInput.closest('.input-group').classList.remove('shadow-sm');
    });

    // Attach search/filter listeners
    searchInput.addEventListener('input', filterWarehouses);
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedStatus = this.getAttribute('data-status');
            filterWarehouses();
        });
    });

    // Initial filter run (active by default)
    filterWarehouses();
});
</script>
@endsection
