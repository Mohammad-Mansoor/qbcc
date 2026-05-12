@extends('dsh.master')
@section('title', 'درخواست‌های نقدینگی دفاتر')
@section('content')
<div class="container-fluid px-4 py-4 text-right">
    <!-- Action Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-envelope-open-o text-primary mr-2"></i> تایید درخواست‌های پول</h3>
            <p class="text-muted small mb-0">مدیریت و تایید انتقالات داخلی بین دفاتر</p>
        </div>
        <div class="col-md-6 text-left">
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="printPage('MRDetails')">
                <i class="fa fa-print mr-1"></i> چاپ لیست
            </button>
        </div>
    </div>

    <!-- Info Box (Dari) -->
    <div class="alert bg-soft-primary border-0 rounded-lg p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-start">
            <div class="ml-3">
                <i class="fa fa-question-circle fa-2x text-primary"></i>
            </div>
            <div>
                <h6 class="font-weight-bold text-primary mb-1">راهنمای تایید درخواست‌ها:</h6>
                <p class="mb-0 text-dark small leading-relaxed">
                    این لیست شامل تمام درخواست‌های بودجه نقد از طرف دفاتر فرعی (مرکزی و فروشات) است. 
                    <br>
                    • <strong>تایید پرداخت:</strong> با کلیک بر روی این گزینه، مبلغ درخواستی از موجودی <strong>دخل عمومی (SP)</strong> کسر شده و به موجودی <strong>دخل دفتر مربوطه</strong> اضافه می‌گردد. همزمان، سند حسابداری انتقال داخلی در سیستم ثبت می‌شود.
                    <br>
                    • <strong>رد نمودن:</strong> درخواست حذف شده و هیچ تغییری در موجودی دخل‌ها صورت نمی‌گیرد.
                </p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="MRDetails">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 font-weight-bold"><i class="fa fa-clock-o text-warning mr-2"></i> درخواست‌های در انتظار تایید</h5>
        </div>
        <div class="card-body p-0">
            <!-- Alert Section -->
            <div class="px-3 mt-2">
                <div class="alert alert-success approve shadow-none border-0 rounded-pill text-center py-2 small" style="display:none;">
                    <i class="fa fa-check-circle mr-1"></i> انتقال موفقانه تایید و ثبت شد.
                </div>
                <div class="alert alert-danger deleteAlert shadow-none border-0 rounded-pill text-center py-2 small" style="display:none;">
                    <i class="fa fa-times-circle mr-1"></i> درخواست رد شد.
                </div>
                <div class="alert alert-danger errorAlert shadow-none border-0 rounded-pill text-center py-2 small" style="display:none;">
                    <i class="fa fa-warning mr-1"></i> موجودی دخل عمومی کافی نیست!
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 border-0">درخواست کننده</th>
                            <th class="py-3 border-0 text-center">مقدار ($)</th>
                            <th class="py-3 border-0">توضیحات</th>
                            <th class="py-3 border-0 text-center">تاریخ</th>
                            <th class="px-4 py-3 border-0 text-left hideOnPrint">عملیات تایید</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credits as $c)
                        <tr class="ur{{$c->id}} border-bottom">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-soft-info text-info rounded-circle ml-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <h6 class="mb-0 font-weight-bold small text-dark">
                                        {{ $c->user_role == 'CO' || $c->user_role == 'CCO' ? 'دفتر مرکزی' : 'دفتر فروشات' }}
                                    </h6>
                                </div>
                            </td>
                            <td class="text-center font-weight-bold text-primary">${{ number_format($c->amount, 2) }}</td>
                            <td class="small">{{ $c->description }}</td>
                            <td class="text-center small text-muted">{{ $c->date }}</td>
                            <td class="px-4 py-3 text-left hideOnPrint">
                                <button onclick="approveCredit({{$c->id}})" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
                                    <i class="fa fa-check mr-1"></i> تایید
                                </button>
                                <button onclick="deleteCredit({{$c->id}})" class="btn btn-soft-danger btn-sm rounded-pill px-3 shadow-none ml-1">
                                    <i class="fa fa-times mr-1"></i> رد
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted italic">هیچ درخواست جدیدی وجود ندارد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(0, 123, 255, 0.1); }
    .bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
    .btn-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: none; }
    .leading-relaxed { line-height: 1.6; }
    @media print { .hideOnPrint { display: none !important; } .card { border: 1px solid #ddd !important; } }
</style>
@endsection

@section('scripts')
<script>
    function approveCredit(id) {
        swal({
            title: "تایید انتقال پول؟",
            text: "با تایید این درخواست، مبلغ از دخل عمومی کسر و به دفتر مربوطه منتقل می‌گردد.",
            icon: "info",
            buttons: {
                confirm: {text: 'بلی، تایید شود', className: 'btn-success'},
                cancel: 'نخیر'
            },
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    type: 'DELETE',
                    data: {'_token': '{{csrf_token()}}'},
                    url: '/dashboard/approve-request-money/' + id,
                    success: function (res) {
                        if (res.status == 'success') {
                            $('.ur' + id).fadeOut();
                            $('.approve').fadeIn().delay(2000).fadeOut(() => location.reload());
                        } else {
                            $('.errorAlert').fadeIn().delay(3000).fadeOut();
                        }
                    }
                })
            }
        });
    }

    function deleteCredit(id) {
        swal({
            title: "رد درخواست؟",
            text: "آیا از رد نمودن این درخواست نقدینگی اطمینان دارید؟",
            icon: "warning",
            buttons: {
                confirm: {text: 'بلی، رد شود', className: 'btn-danger'},
                cancel: 'نخیر'
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: {'_token': '{{csrf_token()}}'},
                    url: '/dashboard/delete-request-money/' + id,
                    success: function (res) {
                        $('.ur' + id).fadeOut();
                        $('.deleteAlert').fadeIn().delay(2000).fadeOut(() => location.reload());
                    }
                })
            }
        });
    }
</script>
@endsection